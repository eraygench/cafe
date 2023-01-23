const path = require("path")

module.exports = {
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
            ziggy: path.resolve('vendor/tightenco/ziggy/dist/vue'), // or 'vendor/tightenco/ziggy/dist/vue' if you're using the Vue plugin
        }
    }
}
