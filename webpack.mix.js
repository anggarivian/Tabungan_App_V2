const mix = require('laravel-mix');

mix.css('resources/css/app.css', 'public/css');

// mix.js('resources/js/app.js', 'public/js');

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .options({
      hmrOptions: {
         host: 'localhost',
         port: '8080'
      }
   });
