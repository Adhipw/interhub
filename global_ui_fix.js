const fs = require('fs');
const path = require('path');

function walkDir(dir, callback) {
    fs.readdirSync(dir).forEach(f => {
        let dirPath = path.join(dir, f);
        let isDirectory = fs.statSync(dirPath).isDirectory();
        isDirectory ? walkDir(dirPath, callback) : callback(path.join(dir, f));
    });
}

walkDir('frontend/resources/js', function(filePath) {
    if (!filePath.endsWith('.vue')) return;

    let content = fs.readFileSync(filePath, 'utf8');
    let original = content;

    // 1. Remove massive border radii
    content = content.replace(/rounded-\[2\.5rem\]/g, 'rounded-2xl');
    content = content.replace(/rounded-\[3rem\]/g, 'rounded-2xl');
    content = content.replace(/rounded-\[3\.5rem\]/g, 'rounded-2xl');
    content = content.replace(/rounded-\[4rem\]/g, 'rounded-3xl');
    content = content.replace(/rounded-3xl/g, 'rounded-2xl'); // Downscale further for most items
    content = content.replace(/rounded-full/g, 'rounded-full'); // Leave circular icons/avatars alone

    // 2. Typography: Tone down AI text styles
    // Convert 'font-black uppercase tracking-widest' and variants
    content = content.replace(/font-black uppercase tracking-widest/g, 'font-semibold text-xs tracking-wide');
    content = content.replace(/font-black uppercase tracking-\[0\.2em\]/g, 'font-semibold text-sm tracking-wide');
    content = content.replace(/font-black uppercase/g, 'font-semibold text-sm');
    // For anything still font-black, change to font-bold
    content = content.replace(/font-black/g, 'font-bold');

    // 3. Buttons and Colors
    // Replace primary blue buttons with slate-900.
    // E.g. 'bg-blue-600 text-white' -> 'bg-slate-900 text-white' (Except for links which should remain blue)
    content = content.replace(/bg-blue-600 text-white/g, 'bg-slate-900 text-white');
    content = content.replace(/bg-blue-700/g, 'bg-slate-800');
    // Tone down extreme shadows on these buttons
    content = content.replace(/shadow-xl shadow-blue-600\/20/g, 'shadow-md shadow-slate-900/10');
    content = content.replace(/shadow-lg shadow-blue-600\/15/g, 'shadow-sm hover:shadow-md');
    content = content.replace(/shadow-lg shadow-blue-600\/20/g, 'shadow-sm hover:shadow-md');
    content = content.replace(/shadow-2xl shadow-blue-600\/40/g, 'shadow-md shadow-slate-900/10');

    // 4. Glows and AI Slop specific effects
    content = content.replace(/animate-pulse/g, '');
    content = content.replace(/blur-3xl/g, 'blur-2xl opacity-50');
    content = content.replace(/blur-\[100px\]/g, 'blur-2xl opacity-30');

    if (content !== original) {
        fs.writeFileSync(filePath, content);
        console.log(`Updated: ${filePath}`);
    }
});
console.log('Global UI Fix Complete!');
