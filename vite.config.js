import {defineConfig, loadEnv} from 'vite';

//import {createVuePlugin} from 'vite-plugin-vue2';
import path from 'path';

// base directory of the theme
const theme = `app/src/`;

// the directory where the frontend theme source files exist
const themeSrc = `app/src/`;

// directory where static assets like images and svg files are located
const themeAssets = `app/public/dist`;

// directory where the generated files must be copied into
const themeAssetsTarget = 'app/dist';

export default ({mode}) => {
  
  const env = loadEnv(mode, process.cwd(), '');
  const isDevMode = env.APP_ENV === 'local';
  const cssModulesHashedNames = isDevMode ? '[local].[hash:base64:2]' : '[hash:base64:2]';
  const vuePath = isDevMode ? 'vue/dist/vue.js' : 'vue/dist/vue.runtime.min.js';
  
  return defineConfig({
    publicDir: themeAssets,
    // see: https://vitejs.dev/config/build-options.html
    build: {
      manifest: false,
      minify: !isDevMode,
      sourcemap: isDevMode,
      outDir: `app/dist`,
      // assetsDir: 'add/assets',
      cssCodeSplit: true,
      target: 'esnext',
      chunkSizeWarningLimit: 2048,
      envDir: './',
      // modulePreload: false,
      rollupOptions: {
        input: {
          app: `${themeSrc}/js/app.js`,
          fawesome: `${themeSrc}/js/fawesome.js`,
        },
        output: [
          {
            entryFileNames: `js/[name].js`,
            dir: `${themeAssetsTarget}`,
            
            // handling for asset files
            assetFileNames: (assetInfo) => {
              if (/\.svg$/.test(assetInfo.name)) {
                return `/svg/[name][extname]`;
              } else {
                return `[ext]/[name].[ext]`;
              }
            },
          },
        ],
      },
    },
    
    css: {
      modules: {
        localsConvention: 'camelCase',
        generateScopedName: cssModulesHashedNames,
      },
      preprocessorOptions: {
        styl: {
          url: {paths: [path.resolve(themeAssetsTarget)]},
        },
      },
    },
    
    resolve: {
      alias: {
        '@svg': path.resolve(theme, 'assets/svg'),
        '@': path.resolve(theme),
        '@asset': path.resolve(themeAssets),
        '@css': path.resolve(themeSrc, 'css'),
        vue: vuePath,
      },
    },
    
    plugins: [
      // createVuePlugin(), // used for vue 2 buildout
    ],
    
    define: {
      // allow vue devtools browser extension only for local environments
      __ALLOW_VUE_DEV_TOOLS__: isDevMode,
    },
    server: {
      watch: {
        usePolling: true,
      },
      hmr: {
        host: 'lachlesegeschichten.de.local',
        clientPort: 443,
      },
    }
  });
};
