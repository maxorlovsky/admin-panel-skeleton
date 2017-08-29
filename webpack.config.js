const path = require('path');
const webpack = require('webpack');

module.exports = {
    entry: {
        bundle: ['../vendor/fe/src/main.js'],
        vendors: [
            'babel-polyfill',
            'promise-polyfill',
            'whatwg-fetch',
            "vue",
            "vue-router",
            "vue-directive-tooltip",
            "@deveodk/vue-tinymce",
			"axios",
            "marked",
            "hammerjs"
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
            vue: 'vue/dist/vue.js'
        }
    },
    plugins: [
		new webpack.EnvironmentPlugin({
			NODE_ENV: 'development', 
		}),
		new webpack.optimize.CommonsChunkPlugin({
			name: 'vendors'
        })
	],
	devtool: "source-map"
};