const path = require('path');
const webpack = require('webpack');

module.exports = {
    mode: 'development',
    entry: {
        bundle: ['./fe/src/main.js']
    },
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, '/dist/js')
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules)/,
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
		})
    ],
    optimization: {
        splitChunks: {
            cacheGroups: {
                commons: {
                    test: /[\\/]node_modules[\\/]/,
                    name: "vendors",
                    chunks: "all"
                }
            }
        }
    },
	devtool: "source-map"
};