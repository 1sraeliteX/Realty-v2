#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const postcss = require('postcss');
const tailwindcss = require('@tailwindcss/postcss');
const autoprefixer = require('autoprefixer');

console.log('🚀 Building CSS assets...');

// Ensure directories exist
const cssDir = path.join(__dirname, '../public/assets/css');
const jsDir = path.join(__dirname, '../public/assets/js');

if (!fs.existsSync(cssDir)) {
    fs.mkdirSync(cssDir, { recursive: true });
}

if (!fs.existsSync(jsDir)) {
    fs.mkdirSync(jsDir, { recursive: true });
}

// Process CSS with PostCSS
const inputCssPath = path.join(__dirname, '../src/css/input_simple.css');
const outputCssPath = path.join(__dirname, '../public/assets/css/style.css');

if (fs.existsSync(inputCssPath)) {
    const css = fs.readFileSync(inputCssPath, 'utf8');

    postcss([
        tailwindcss({
            config: path.join(__dirname, 'tailwind.config.js')
        }),
        autoprefixer()
    ])
        .process(css, { from: inputCssPath })
        .then(result => {
            fs.writeFileSync(outputCssPath, result.css);
            console.log('✅ CSS compiled successfully with PostCSS');

            if (result.map) {
                fs.writeFileSync(outputCssPath + '.map', result.map.toString());
                console.log('✅ Source map generated');
            }
        })
        .catch(error => {
            console.error('❌ CSS compilation failed:', error);
            process.exit(1);
        });
} else {
    console.log('❌ Input CSS file not found:', inputCssPath);
}

// Verify Font Awesome
const fontAwesomePath = path.join(__dirname, '../public/assets/css/fontawesome.css');
if (fs.existsSync(fontAwesomePath)) {
    console.log('✅ Font Awesome CSS available');
} else {
    console.log('❌ Font Awesome CSS missing');
}

// Verify Chart.js
const chartJsPath = path.join(__dirname, '../public/assets/js/chart.js');
if (fs.existsSync(chartJsPath)) {
    console.log('✅ Chart.js available');
} else {
    console.log('❌ Chart.js missing');
}

console.log('\n🎉 Build completed!');
console.log('📁 CSS files are ready in public/assets/css/');
console.log('📁 JS files are ready in public/assets/js/');
console.log('\n🔧 To rebuild CSS after changes, run: npm run build');
