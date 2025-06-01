const mix = require('laravel-mix');

mix.css('resources/css/app.css', 'public/css');

mix.js('resources/js/app.js', 'public/js')
   .options({
      hmrOptions: {
         host: 'localhost',
         port: '8080'
      }
   });
