const fs = require('fs');
let content = fs.readFileSync('frontend/resources/js/Pages/Welcome.vue', 'utf8');

content = content.replace('// Categories static data', `const vReveal = {
    mounted: (el) => {
        el.classList.add('opacity-0', 'translate-y-8', 'transition-all', 'duration-1000', 'ease-out');
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                el.classList.remove('opacity-0', 'translate-y-8');
                observer.unobserve(el);
            }
        }, { threshold: 0.1 });
        setTimeout(() => observer.observe(el), 100);
    }
};

// Categories static data`);

// Replace massive rounded corners
content = content.replace(/rounded-\[2\.5rem\]/g, 'rounded-2xl');
content = content.replace(/rounded-\[3rem\]/g, 'rounded-2xl');
content = content.replace(/rounded-\[3\.5rem\]/g, 'rounded-2xl');
content = content.replace(/rounded-\[4rem\]/g, 'rounded-3xl');

// Tone down extreme typography
content = content.replace(/font-black uppercase tracking-widest/g, 'font-semibold text-xs tracking-wide');
content = content.replace(/font-black uppercase tracking-\[0\.2em\]/g, 'font-semibold text-sm');
content = content.replace(/font-black uppercase/g, 'font-semibold text-sm');
content = content.replace(/font-black/g, 'font-bold');

// Tone down extreme shadows
content = content.replace(/shadow-2xl shadow-blue-600\/20/g, 'shadow-sm hover:shadow-md');
content = content.replace(/shadow-xl shadow-blue-600\/20/g, 'shadow-sm hover:shadow-md');
content = content.replace(/shadow-2xl shadow-blue-600\/40/g, 'shadow-sm hover:shadow-md');
content = content.replace(/shadow-xl shadow-slate-100\/30/g, 'shadow-sm');

// Apply v-reveal to specific elements instead of all sections to be safer
content = content.replace(/<div class="max-w-5xl mx-auto px-6 text-center">/g, '<div v-reveal class="max-w-5xl mx-auto px-6 text-center">');
content = content.replace(/<div class="grid grid-cols-2 lg:grid-cols-4 gap-8">/g, '<div v-reveal class="grid grid-cols-2 lg:grid-cols-4 gap-8">');
content = content.replace(/<div class="max-w-2xl text-center md:text-left">/g, '<div v-reveal class="max-w-2xl text-center md:text-left">');
content = content.replace(/<div v-else-if="featuredInternships\.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">/g, '<div v-reveal class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">');
content = content.replace(/<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">/g, '<div v-reveal class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">');
content = content.replace(/<div class="grid grid-cols-1 md:grid-cols-3 gap-8">/g, '<div v-reveal class="grid grid-cols-1 md:grid-cols-3 gap-8">');
content = content.replace(/<div class="max-w-4xl mx-auto/g, '<div v-reveal class="max-w-4xl mx-auto');
content = content.replace(/<div class="rounded-3xl p-16 lg:p-24/g, '<div v-reveal class="rounded-3xl p-16 lg:p-24');

fs.writeFileSync('frontend/resources/js/Pages/Welcome.vue', content);
console.log('Done!');
