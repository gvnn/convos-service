var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function (mix) {
    mix.less([
        'app.less',
        '../components/bootstrap/less/bootstrap.less'
    ]);

    mix.scripts([
        "components/jquery/dist/jquery.js",
        "components/bootstrap/dist/js/bootstrap.js",
        "components/react/react-with-addons.min.js",
        "components/react/JSXTransformer.js"
    ], 'public/js/components.js', 'resources/assets');

    mix.scripts([
        "js/console.js"
    ], 'public/js/console.js', 'resources/assets');
});