---
name: UI and Frontend Guidelines
description: Comprehensive design standards, visual identity, typography, colors, and frontend styling approach for FFInsta to ensure UI consistency.
---

# UI and Frontend Guidelines

This document outlines the exact visual identity, colors, fonts, and design standards for building and extending the frontend of the FFInsta application. When creating new interfaces or components, adhere strictly to these guidelines to ensure a cohesive and consistent user experience.

## 1. Core Technologies
- **Styling**: Tailwind CSS 3
- **CSS Preprocessor**: PostCSS (with `resources/css/app.css` acting as the central stylesheet)
- **Frameworks**: Livewire 3 & Alpine.js (Avoid raw JavaScript DOM manipulation; use Alpine.js for interactivity)

## 2. Typography & Fonts
- **Primary Font (LTR)**: `Inter` (Weights: 300, 400, 500, 600, 700, 800)
- **Primary Font (Arabic / RTL)**: `Cairo` (Weights: 400, 500, 600, 700, 800)
- The application automatically applies the correct font based on the `[dir="rtl"]` attribute on the `<body>` or `<html>` tag.

## 3. Theming & Color Palette

### 3.1 CSS Variables (Dynamic Light/Dark Modes)
Instead of hardcoding Tailwind color classes (e.g., `bg-white`, `text-gray-900`), use the predefined CSS variables mapped in `app.css`. This ensures flawless switching between Light and Dark modes.

**Surfaces & Backgrounds:**
- `--bg-primary` (Class: `bg-surface`) - Main background (Light: `#ffffff`, Dark: `#0f172a`).
- `--bg-secondary` (Class: `bg-surface-2`) - Slightly darker background (Light: `#f8fafc`, Dark: `#1e293b`).
- `--bg-tertiary` (Class: `bg-surface-3`) - Hover states or disabled backgrounds (Light: `#f1f5f9`, Dark: `#273548`).
- `--bg-card` (Class: `bg-card`) - Card backgrounds (Light: `#ffffff`, Dark: `#1e293b`).
- `--bg-sidebar` - Sidebar background (Light: `#ffffff`, Dark: `#0a1020`).

**Text Colors:**
- `--text-primary` (Class: `text-primary`) - Headings/primary content (Light: `#0f172a`, Dark: `#f1f5f9`).
- `--text-secondary` (Class: `text-secondary`) - Subtitles/standard text (Light: `#475569`, Dark: `#94a3b8`).
- `--text-muted` (Class: `text-muted`) - Placeholders/less important (Light: `#94a3b8`, Dark: `#64748b`).

**Borders:**
- `--border-color` - Standard border (Light: `#e2e8f0`, Dark: `#334155`).
- `--border-subtle` (Class: `border-subtle`) - Subtle border (Light: `#f1f5f9`, Dark: `#1e293b`).

**Brand Shared:**
- `--brand` - Main primary color (`#2055f5` in Light, `#4d80ff` in Dark).
- `--brand-hover` - Hover brand color (`#1140e8` in Light, `#2055f5` in Dark).
- `--brand-light` - Light brand surface (`#dce8ff` in Light, `#1a2a5e` in Dark).

**Status Colors (Fixed):**
- Success: `#10b981`
- Warning: `#f59e0b`
- Danger: `#ef4444`
- Info: `#3b82f6`

### 3.2 Tailwind Config Colors (`tailwind.config.js`)
If you must use utility classes directly for specific designs, the following colors are registered:
- **Brand**:
  - `brand-50`: `#f0f4ff`
  - `brand-100`: `#dce8ff`
  - `brand-200`: `#b9d0ff`
  - `brand-300`: `#85adff`
  - `brand-400`: `#4d80ff`
  - `brand-500`: `#2055f5` (Primary Core Color)
  - `brand-600`: `#1140e8`
  - `brand-700`: `#0e30c4`
  - `brand-800`: `#0f2a9e`
  - `brand-900`: `#132980`
  - `brand-950`: `#0d1a52`
- **Dark Surfaces**:
  - `dark-50`: `#f8fafc`
  - `dark-100`: `#f1f5f9`
  - `dark-700`: `#1e293b`
  - `dark-800`: `#0f172a`
  - `dark-850`: `#0a1020`
  - `dark-900`: `#060d1a`

## 4. Reusable Component Classes (`app.css` @layer components)
To maintain DRY CSS, use these base classes instead of repeating Tailwind utility strings:

- **Cards**:
  - `.card`: Standard card (rounded-2xl, border, custom shadow).
  - `.stat-card`: For statistics (padding 5, flex items).
  - `.card-premium-glow`: Premium cards with glow effects on hover.
- **Buttons**:
  - `.btn`: Base button class.
  - `.btn-primary`: Solid brand color with shadow.
  - `.btn-secondary`: Outline/Surface color.
  - `.btn-danger`, `.btn-success`.
  - Sizes: `.btn-sm`, `.btn-lg`, `.btn-icon`.
- **Forms**:
  - `.form-input`: Standard text inputs (rounded-xl, focus rings).
  - `.form-label`: Labels above inputs.
  - `.form-error`: Validation error messages.
  - `.form-group`: Wrapper for spacing.
- **Badges**:
  - Base: `.badge` (rounded-full).
  - Variants: `.badge-pending`, `.badge-active`, `.badge-completed`, `.badge-cancelled`, `.badge-high`, `.badge-normal`, `.badge-admin`, `.badge-user`.
- **Tables**:
  - `.data-table`: Pre-styled table with hover effects and borders.
- **Alerts / Toasts**:
  - Base: `.alert` (rounded-xl, padded).
  - Variants: `.alert-success`, `.alert-error`, `.alert-warning`, `.alert-info`.
- **Sidebar**:
  - `.sidebar-link`: Pre-styled link with hover states. Add `.active` for current page.
- **Misc**:
  - `.page-title`: Large heading (text-2xl, bold).
  - `.section-title`: Subheading (text-base, font-semibold).
  - `.progress-bar` & `.progress-fill`: For progress indicators.
  - `.skeleton`: Animated skeleton loader.

## 5. Shadows & Animations

**Shadows (`boxShadow` in tailwind config):**
- `shadow-glow`: `0 0 20px rgba(32, 85, 245, 0.3)`
- `shadow-card`: `0 4px 24px rgba(0, 0, 0, 0.08)`
- `shadow-card-dark`: `0 4px 24px rgba(0, 0, 0, 0.4)`

**Animations (`animation` in tailwind config):**
- `animate-fade-in`: `fadeIn 0.2s ease-out`
- `animate-slide-up`: `slideUp 0.3s ease-out`
- `animate-pulse-slow`: `pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite`

**Premium/Landing Animations (Custom in `app.css`):**
- `animate-float`: Floating animation (6s).
- `animate-float-delayed`: Floating animation with delay (8s).
- `animate-marquee-ltr` & `animate-marquee-rtl`: Infinite scrolling marquees.
- `animate-glow-pulse`: Glowing pulse effect for backgrounds.

## 6. Layout, Dark Mode & RTL Support

### Dark Mode
- Dark mode uses the `class` strategy (toggling `.dark` on the `<html>` tag).
- Rely on the CSS variables (`var(--bg-card)`, etc.) which automatically flip values when `.dark` is active.

### RTL (Right-to-Left) Arabic Support
- The application natively flips layout for Arabic.
- Use logical properties in Tailwind:
  - `ms-*` instead of `ml-*` (Margin Start).
  - `pe-*` instead of `pr-*` (Padding End).
  - `text-start` and `text-end` instead of `text-left` and `text-right`.
  - `rounded-s-*` and `rounded-e-*` instead of left/right radius.
- Custom CSS flipping is handled via `[dir="rtl"]` selectors in `app.css` (e.g., flipping icons with `.rtl-flip`).

### General Layout Principles
- **Spacing**: Use standard Tailwind spacing (`gap-4`, `p-6`). Maintain consistent whitespace.
- **Border Radius**: Favor highly rounded corners (`rounded-xl`, `rounded-2xl`, `rounded-full`). Avoid sharp corners (`rounded-none`).
- **Glassmorphism**: Use the `.glass` utility class for transparent, blurred backgrounds.
- **Gradients**: Use `.bg-mesh-gradient-light` or `.bg-mesh-gradient-dark` for premium backgrounds.
