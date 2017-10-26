const path = require('path');
const webpack = require('webpack');

module.exports = {
    entry: {
        bundle: ['./fe/src/main.js'],
        vendors: [
            'babel-polyfill',
            'promise-polyfill',
            'whatwg-fetch',
            "vue",
            "vue-router",
            "vue-directive-tooltip",
            "vuedraggable",
            "@deveodk/vue-tinymce",
			"axios",
            "marked",
            "hammerjs",
            "tinymce"
		]
    },
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, '/dist/js')
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['env']
                    }
                }
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    loaders: {
    
                    }
                }
            }
        ]
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.min.js'
        }
    },
    plugins: [
		new webpack.EnvironmentPlugin({
			NODE_ENV: 'production', 
        }),
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: '"production"'
            }
        }),
		new webpack.optimize.CommonsChunkPlugin({
			name: 'vendors'
        }),
        new webpack.optimize.UglifyJsPlugin()
	]
};