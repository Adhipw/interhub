import { usePage } from '@inertiajs/vue3';

/**
 * Composable to handle initial data hydration from Inertia/Server.
 * Page-level data should come from the active Inertia page props first.
 */
export function useInitialData() {
    const page = usePage();

    const getInitialProps = (componentName: string): Record<string, any> | null => {
        if (page.component === componentName && page.props) {
            return { ...(page.props as Record<string, any>) };
        }

        return null;
    };

    return {
        getInitialProps,
    };
}
