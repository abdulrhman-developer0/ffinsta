import React from 'react';
import { createRoot } from 'react-dom/client';
import PostEditor from './PostEditor';

const mountEditor = () => {
    const mountPoint = document.getElementById('react-editor-mount');
    if (mountPoint && !mountPoint.dataset.mounted) {
        mountPoint.dataset.mounted = 'true';
        let initialDataEn = {};
        let initialDataAr = {};
        
        try {
            initialDataEn = JSON.parse(mountPoint.getAttribute('data-en') || '{}');
        } catch(e) {}
        
        try {
            initialDataAr = JSON.parse(mountPoint.getAttribute('data-ar') || '{}');
        } catch(e) {}
        
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
