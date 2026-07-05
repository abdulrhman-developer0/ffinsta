import React from 'react';
import { createRoot } from 'react-dom/client';
import PostEditor from './PostEditor';

const mountEditor = () => {
    const mountPoint = document.getElementById('react-editor-mount');
    if (mountPoint && !mountPoint.dataset.mounted) {
        mountPoint.dataset.mounted = 'true';
        let initialDataEn = '';
        let initialDataAr = '';
        
        const dataEnRaw = mountPoint.getAttribute('data-en');
        const dataArRaw = mountPoint.getAttribute('data-ar');

        try {
            initialDataEn = dataEnRaw ? JSON.parse(dataEnRaw) : {};
        } catch(e) {
            initialDataEn = dataEnRaw || ''; // Fallback to raw HTML
        }
        
        try {
            initialDataAr = dataArRaw ? JSON.parse(dataArRaw) : {};
        } catch(e) {
            initialDataAr = dataArRaw || ''; // Fallback to raw HTML
        }
        
        const root = createRoot(mountPoint);
        root.render(
            <PostEditor 
                initialDataEn={initialDataEn} 
                initialDataAr={initialDataAr} 
            />
        );
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountEditor);
} else {
    mountEditor();
}
