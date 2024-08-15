const mix = require('laravel-mix');
const path = require('path');

mix.js('resources/js/app.js', 'public/js')
   .js('resources/js/calendar.js', 'public/js')
   .js('resources/js/main.js', 'public/js')
   .postCss('resources/css/custom.css', 'public/css', [
      require('postcss-import'),
      require('tailwindcss'),
      require('autoprefixer'),
   ])
   .vue()
   .sass('resources/sass/app.scss', 'public/css')
   .sass('resources/sass/styles.scss', 'public/css')
   .webpackConfig({
      resolve: {
         alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            'vue$': 'vue/dist/vue.esm-bundler.js',
         },
         extensions: ['.js', '.vue', '.json']
      },
   });

mix.disableNotifications();
