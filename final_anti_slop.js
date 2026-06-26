const fs = require('fs');
const path = require('path');

function processFile(filePath) {
    if (!filePath.endsWith('.vue')) return;

    let content = fs.readFileSync(filePath, 'utf8');
    let original = content;

    // Remove uppercase tracking-widest universally
    content = content.replace(/uppercase tracking-widest/g, 'font-medium');
    content = content.replace(/uppercase tracking-wider/g, 'font-medium');
    content = content.replace(/tracking-\[0\.2em\]/g, 'tracking-normal');
    
    // Convert text-[10px] uppercase to text-xs
    content = content.replace(/text-\[10px\] font-bold uppercase/g, 'text-xs font-semibold');
    
    // Nuke blur blobs
    content = content.replace(/<div class="absolute[^>]*blur-2xl[^>]*><\/div>/g, '');
    content = content.replace(/<div class="absolute[^>]*blur-3xl[^>]*><\/div>/g, '');

    if (content !== original) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log(`Cleaned AI Slop in: ${filePath}`);
    }
}

function traverseDir(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            traverseDir(fullPath);
        } else {
            processFile(fullPath);
        }
    }
}

console.log('Running Final Anti-Slop Cleanup...');
traverseDir(path.join(__dirname, 'frontend/resources/js'));
console.log('Done!');
