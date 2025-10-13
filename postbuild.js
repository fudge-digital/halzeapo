import { copyFileSync } from 'fs';

try {
    copyFileSync(
        'public/build/.vite/manifest.json',
        'public/build/manifest.json'
    );
    console.log('✅ Manifest.json successfully moved to public/build/');
} catch (e) {
    console.warn('⚠️ Could not move manifest.json:', e.message);
}
