import './bootstrap';
import Alpine from 'alpinejs';

// ============================================================
// DARK MODE STORE
// Persists preference to localStorage, respects system pref
// ============================================================
Alpine.store('theme', {
    dark: false,

    init() {
        // Check saved preference first, then system preference
        const saved = localStorage.getItem('ffinsta_theme');
        if (saved) {
            this.dark = saved === 'dark';
        } else {
            this.dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        this.apply();

        // Listen for OS-level changes (if no manual override)
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('ffinsta_theme')) {
                this.dark = e.matches;
                this.apply();
            }
        });
    },

    toggle() {
        this.dark = !this.dark;
        localStorage.setItem('ffinsta_theme', this.dark ? 'dark' : 'light');
        this.apply();
    },

    apply() {
        if (this.dark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },

    setLight() {
        this.dark = false;
        localStorage.setItem('ffinsta_theme', 'light');
        this.apply();
    },

    setDark() {
        this.dark = true;
        localStorage.setItem('ffinsta_theme', 'dark');
        this.apply();
    },

    setSystem() {
        localStorage.removeItem('ffinsta_theme');
        this.dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        this.apply();
    },
});

// ============================================================
// TOAST NOTIFICATION STORE
// ============================================================
Alpine.store('toast', {
    items: [],
    show(message, type = 'info', duration = 4000) {
        const id = Date.now();
        this.items.push({ id, message, type });
        setTimeout(() => this.dismiss(id), duration);
    },
    dismiss(id) {
        this.items = this.items.filter(t => t.id !== id);
    },
    success(msg) { this.show(msg, 'success'); },
    error(msg)   { this.show(msg, 'error'); },
    warning(msg) { this.show(msg, 'warning'); },
    info(msg)    { this.show(msg, 'info'); },
});

// ============================================================
// SIDEBAR STORE
// ============================================================
Alpine.store('sidebar', {
    open: window.innerWidth >= 1024, // open by default on desktop
    toggle() { this.open = !this.open; },
    close()  { this.open = false; },
});

window.Alpine = Alpine;
Alpine.start();
