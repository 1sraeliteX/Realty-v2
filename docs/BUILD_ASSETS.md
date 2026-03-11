# Asset Build Guide

This document explains how to build and manage CSS/JS assets for the Cornerstone Realty platform.

## 🎯 Problem Solved

The application was previously using CDN links for:
- Tailwind CSS (production warning)
- Font Awesome (CSP violations)
- Chart.js (CSP violations)

These have been replaced with local assets to eliminate:
- Content Security Policy violations
- Production warnings
- External dependencies
- Network latency issues

## 📁 Asset Structure

```
public/assets/
├── css/
│   ├── fontawesome.css    # Font Awesome icons
│   ├── style.css          # Main application styles
│   └── tailwind.css       # Tailwind CSS framework
└── js/
    └── chart.js           # Chart.js library
```

## 🚀 Quick Start

### Installation
```bash
# Install dependencies
npm install

# Build assets
npm run build
```

### Development
```bash
# Build for development
npm run dev

# Or just run build
npm run build
```

## 🛠️ Build Process

The build script (`build.js`) performs these actions:

1. **Copies Tailwind CSS** from node_modules to public assets
2. **Processes custom CSS** and combines with Tailwind
3. **Verifies Font Awesome** availability
4. **Verifies Chart.js** availability
5. **Creates directories** if they don't exist

## 📝 Making Changes

### CSS Changes
1. Edit files in `src/css/`
2. Run `npm run build` to rebuild
3. Changes are reflected in `public/assets/css/style.css`

### Adding New Dependencies
1. Install via npm: `npm install package-name`
2. Download/copy to appropriate `public/assets/` directory
3. Update view files to reference local paths
4. Update build script if needed

## 🔧 Configuration Files

### `tailwind.config.js`
- Configures Tailwind CSS
- Defines content paths for CSS generation
- Sets up custom colors and plugins

### `postcss.config.js`
- Configures PostCSS processing
- Enables autoprefixer
- Integrates with Tailwind

### `package.json`
- Contains build scripts
- Lists development dependencies
- Defines project metadata

## 🚨 Troubleshooting

### CSS Not Updating
```bash
# Rebuild assets
npm run build

# Clear browser cache
# Hard refresh: Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)
```

### Missing Assets
```bash
# Check if files exist
ls public/assets/css/
ls public/assets/js/

# Rebuild if missing
npm run build
```

### Build Errors
```bash
# Check node modules
npm install

# Verify build script
node build.js
```

## 🎨 Custom CSS

Custom styles are defined in `src/css/input.css`:

```css
/* Custom Components */
.btn-primary {
    @apply bg-primary-600 text-white px-4 py-2 rounded-lg;
}

/* Custom Utilities */
.stats-card {
    @apply bg-white dark:bg-gray-800 rounded-lg shadow;
}
```

## 📦 Production Deployment

For production deployment:

1. Run build: `npm run build`
2. Ensure `public/assets/` is deployed
3. No CDN dependencies required
4. All assets are self-contained

## 🔒 Security Benefits

- **No external CDN dependencies**
- **Content Security Policy compliant**
- **Reduced attack surface**
- **Offline capability**
- **Faster loading (cached locally)**

## 📊 Performance

- **Reduced DNS lookups** (no CDN requests)
- **Faster page loads** (local assets)
- **Better caching** (controlled by your server)
- **No network latency** for CSS/JS

## 🔄 Maintenance

### Regular Tasks
- Run `npm run build` after CSS changes
- Keep dependencies updated: `npm update`
- Monitor asset sizes in `public/assets/`

### Updates
- Update Tailwind: `npm install tailwindcss@latest`
- Update other packages: `npm update`
- Rebuild after updates: `npm run build`

---

**Note**: The @apply warnings in the IDE are normal - they're processed correctly by the Tailwind CSS engine when served by the browser.
