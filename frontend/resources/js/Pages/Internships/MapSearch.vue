<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useLangStore } from '@/Stores/lang';
import { ArrowLeft, MapPin, Building2, ExternalLink, Compass } from 'lucide-vue-next';
import { Link, router as inertiaRouter } from '@inertiajs/vue3';
import type { Internship } from '@/Types/internship';

import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Fix leaflet default icon issue in Vue
delete (L.Icon.Default.prototype as any)._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
});

const props = defineProps<{
    internships: Internship[];
}>();

const langStore = useLangStore();
const t = (key: string) => langStore.t(key);

const mapContainer = ref<HTMLElement | null>(null);
let map: L.Map | null = null;
const isLoadingLocation = ref(false);

const initMap = () => {
    if (!mapContainer.value) return;

    // Default center to Jakarta
    map = L.map(mapContainer.value).setView([-6.2088, 106.8456], 12);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    const markers = L.featureGroup();

    const groupedInternships: Record<string, Internship[]> = {};

    props.internships.forEach(internship => {
        if (internship.latitude && internship.longitude) {
            const key = `${internship.latitude},${internship.longitude}`;
            if (!groupedInternships[key]) {
                groupedInternships[key] = [];
            }
            groupedInternships[key].push(internship);
        }
    });

    Object.values(groupedInternships).forEach(group => {
        const first = group[0];
        
        let popupContent = `<div class="p-2 min-w-[200px] max-w-[280px] max-h-[300px] overflow-y-auto pr-2">`;
        
        if (group.length > 1) {
            popupContent += `
                <div class="text-[10px] font-black text-primary-600 bg-primary-50 px-2 py-1 rounded-md mb-3 border border-primary-100 inline-block uppercase tracking-wider">
                    ${group.length} Lowongan di lokasi ini
                </div>
            `;
        }
        
        group.forEach((internship, index) => {
            popupContent += `
                <div class="${index > 0 ? 'border-t border-slate-100 pt-3 mt-3' : ''}">
                    <div class="text-[10px] font-bold text-slate-500 mb-0.5 uppercase tracking-wider">${internship.type}</div>
                    <h3 class="font-black text-sm text-slate-900 mb-1 leading-tight">${internship.title}</h3>
                    <div class="flex items-start gap-1.5 text-slate-500 text-xs mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building-2 shrink-0 mt-0.5"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>
                        <span class="leading-tight">${internship.company?.name || 'Perusahaan'}</span>
                    </div>
                    <a href="/internships/${internship.slug}" class="block w-full text-center bg-slate-900 text-white rounded-lg py-2 text-xs font-bold hover:bg-primary-600 transition-colors shadow-sm">
                        Lihat Detail
                    </a>
                </div>
            `;
        });
        
        popupContent += `</div>`;

        const iconHtml = `
            <div style="position: relative; display: inline-block;">
                <img src="https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png" style="width: 25px; height: 41px;" />
                ${group.length > 1 ? `<div style="position: absolute; top: -8px; right: -8px; background-color: #ef4444; color: white; border-radius: 9999px; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); z-index: 50;">${group.length}</div>` : ''}
            </div>
        `;

        const customIcon = L.divIcon({
            html: iconHtml,
            className: 'bg-transparent border-0',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        const marker = L.marker([Number(first.latitude), Number(first.longitude)], { icon: customIcon })
            .bindPopup(popupContent, {
                className: 'custom-popup'
            });
        
        markers.addLayer(marker);
    });

    if (map && markers.getLayers().length > 0) {
        map.addLayer(markers);
        map.fitBounds(markers.getBounds(), { padding: [50, 50] });
    }
};

const locateMe = () => {
    if (!map) return;
    isLoadingLocation.value = true;
    
    map.locate({ setView: true, maxZoom: 14 });
    
    map.on('locationfound', (e) => {
        isLoadingLocation.value = false;
        L.circle(e.latlng, e.accuracy / 2, {
            color: '#0ea5e9',
            fillColor: '#0ea5e9',
            fillOpacity: 0.15
        }).addTo(map!);
    });

    map.on('locationerror', (e) => {
        isLoadingLocation.value = false;
        alert("Gagal mendeteksi lokasi. Pastikan GPS aktif dan Anda memberikan izin lokasi pada browser.");
    });
};

onMounted(() => {
    initMap();
    document.title = 'Peta Magang - InternHub';
});

onUnmounted(() => {
    if (map) {
        map.remove();
    }
});
</script>

<template>
    <PublicLayout>
        <div class="bg-neutral-50 dark:bg-neutral-950 min-h-screen pt-24 flex flex-col h-screen">
            <!-- Header Bar -->
            <div class="bg-white dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-800 p-4 px-6 flex items-center justify-between z-10 shadow-sm relative">
                <div class="flex items-center gap-4">
                    <Link href="/internships" class="p-2 bg-neutral-100 dark:bg-neutral-800 rounded-xl hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 transition-colors">
                        <ArrowLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <h1 class="text-xl font-black text-slate-900 dark:text-white leading-none">Peta Magang</h1>
                        <p class="text-xs text-slate-500 font-bold mt-1">Temukan peluang terbaik di sekitar Anda</p>
                    </div>
                </div>
                
                <button 
                    @click="locateMe"
                    class="flex items-center gap-2 bg-primary-600 text-white px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-primary-700 transition-colors shadow-lg shadow-primary-600/20 active:scale-95"
                >
                    <Compass class="w-4 h-4" :class="{ 'animate-spin': isLoadingLocation }" />
                    <span class="hidden md:inline">{{ isLoadingLocation ? 'Mencari...' : 'Lokasi Saya' }}</span>
                </button>
            </div>

            <!-- Map Container -->
            <div class="flex-1 relative z-0">
                <div ref="mapContainer" class="w-full h-full"></div>
                
                <!-- Floating Legend/Info -->
                <div class="absolute bottom-6 left-6 z-[1000] bg-white/90 dark:bg-neutral-900/90 backdrop-blur-md p-4 rounded-2xl shadow-xl border border-white/20 dark:border-white/10 hidden md:block">
                    <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest mb-3 border-b border-slate-200 dark:border-slate-800 pb-2">Informasi Peta</p>
                    <ul class="space-y-3 text-sm text-slate-600 dark:text-slate-400">
                        <li class="flex items-center gap-3">
                            <img src="https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png" class="h-5" />
                            <span>Titik Lokasi Perusahaan</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-5 h-5 rounded-full bg-primary-500/20 border border-primary-500 flex items-center justify-center">
                                <div class="w-1.5 h-1.5 rounded-full bg-primary-500"></div>
                            </div>
                            <span>Lokasi Anda Saat Ini</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<style>
/* Override leaflet styles for dark mode and modern look */
.leaflet-container {
    font-family: inherit;
}
.custom-popup .leaflet-popup-content-wrapper {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 1rem;
    box-shadow: 0 10px 40px -10px rgba(0,0,0,0.2);
    border: 1px solid rgba(0,0,0,0.05);
}
.dark .custom-popup .leaflet-popup-content-wrapper {
    background: rgba(15, 23, 42, 0.95);
    border: 1px solid rgba(255,255,255,0.1);
}
.custom-popup .leaflet-popup-tip {
    background: rgba(255, 255, 255, 0.95);
}
.dark .custom-popup .leaflet-popup-tip {
    background: rgba(15, 23, 42, 0.95);
}
.custom-popup .leaflet-popup-close-button {
    color: #64748b !important;
    padding: 0.5rem !important;
}
</style>
