import { nextTick, watch } from 'vue';
import { staticTextCatalog } from '@/i18n/staticTextCatalog';

type Locale = 'id' | 'en';
type LangStore = {
    locale: Locale | string;
    translations: Record<string, string>;
    catalogs: Record<Locale, Record<string, string>>;
    ensureCatalogs: () => Promise<void>;
};

const textOriginals = new WeakMap<Text, string>();
const attributeOriginals = new WeakMap<Element, Record<string, string>>();
const translatedAttributes = ['placeholder', 'title', 'aria-label', 'alt'];
const ignoredTags = new Set(['SCRIPT', 'STYLE', 'NOSCRIPT', 'CODE', 'PRE', 'TEXTAREA']);

let observer: MutationObserver | null = null;
let scheduled = false;

const normalize = (value: string) => value.replace(/\s+/g, ' ').trim();

const buildValueMap = (store: LangStore, targetLocale: Locale) => {
    const idCatalog = {
        ...(store.catalogs.id || {}),
        ...staticTextCatalog.id,
    };
    const enCatalog = {
        ...(store.catalogs.en || {}),
        ...staticTextCatalog.en,
    };
    const targetCatalog = targetLocale === 'en' ? enCatalog : idCatalog;
    const map = new Map<string, string>();

    const addPair = (source?: string, target?: string) => {
        if (!source || !target) return;
        const normalizedSource = normalize(source);
        const normalizedTarget = normalize(target);
        if (!normalizedSource || !normalizedTarget || normalizedSource === normalizedTarget) return;
        map.set(normalizedSource, normalizedTarget);
    };

    for (const key of new Set([...Object.keys(idCatalog), ...Object.keys(enCatalog)])) {
        addPair(idCatalog[key], targetCatalog[key]);
        addPair(enCatalog[key], targetCatalog[key]);
    }

    return map;
};

const shouldSkip = (node: Node) => {
    const parent = node.parentElement;
    if (!parent) return true;
    if (ignoredTags.has(parent.tagName)) return true;
    if (parent.closest('[data-no-dom-i18n]')) return true;
    return false;
};

const translateTextNode = (node: Text, map: Map<string, string>) => {
    if (shouldSkip(node)) return;

    const current = node.nodeValue || '';
    const original = textOriginals.get(node) || current;
    textOriginals.set(node, original);

    const leading = original.match(/^\s*/)?.[0] || '';
    const trailing = original.match(/\s*$/)?.[0] || '';
    const translated = map.get(normalize(original));

    node.nodeValue = translated ? `${leading}${translated}${trailing}` : original;
};

const translateAttributes = (element: Element, map: Map<string, string>) => {
    if (ignoredTags.has(element.tagName) || element.closest('[data-no-dom-i18n]')) return;

    const originals = attributeOriginals.get(element) || {};

    for (const attribute of translatedAttributes) {
        if (!element.hasAttribute(attribute)) continue;

        if (!originals[attribute]) {
            originals[attribute] = element.getAttribute(attribute) || '';
        }

        const original = originals[attribute];
        const translated = map.get(normalize(original));
        const nextValue = translated || original;
        if (element.getAttribute(attribute) !== nextValue) {
            element.setAttribute(attribute, nextValue);
        }
    }

    if (Object.keys(originals).length > 0) {
        attributeOriginals.set(element, originals);
    }
};

const walkAndTranslate = (root: ParentNode, map: Map<string, string>) => {
    const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
    let node = walker.nextNode();

    while (node) {
        translateTextNode(node as Text, map);
        node = walker.nextNode();
    }

    if (root instanceof Element) {
        translateAttributes(root, map);
    }

    root.querySelectorAll?.('*').forEach((element) => translateAttributes(element, map));
};

const scheduleTranslate = (store: LangStore) => {
    if (scheduled) return;
    scheduled = true;

    window.requestAnimationFrame(() => {
        scheduled = false;
        const locale = store.locale === 'en' ? 'en' : 'id';
        const map = buildValueMap(store, locale);
        walkAndTranslate(document.body, map);
    });
};

export const startDomI18nBridge = async (store: LangStore) => {
    await store.ensureCatalogs();
    await nextTick();
    scheduleTranslate(store);

    watch(
        () => [store.locale, store.translations],
        async () => {
            await nextTick();
            scheduleTranslate(store);
        },
        { deep: true }
    );

    observer?.disconnect();
    observer = new MutationObserver((mutations) => {
        if (mutations.some((mutation) => mutation.addedNodes.length > 0 || mutation.type === 'attributes')) {
            scheduleTranslate(store);
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: translatedAttributes,
    });
};
