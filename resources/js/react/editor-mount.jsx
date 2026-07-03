import React from 'react';
import { createRoot } from 'react-dom/client';
import PostEditor from './PostEditor';

document.addEventListener('DOMContentLoaded', () => {
    const mountPoint = document.getElementById('react-editor-mount');
    if (mountPoint) {
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
});
