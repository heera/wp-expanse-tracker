let mix = require('./resources/mix');

if (!mix.inProduction()) {
    mix.webpackConfig({
        devtool: 'source-map'
    }).sourceMaps(false);
}

mix
    .js('resources/admin/boot.js', 'assets/admin/js/boot.js')
    .js('resources/admin/start.js', 'assets/admin/js/start.js')
    .sass('resources/scss/alpha-admin.scss', 'assets/admin/css/alpha-admin.css')
    .copy('resources/images', 'assets/images')
    .copy('node_modules/element-ui/lib/theme-chalk/fonts', 'assets/admin/css/fonts');
