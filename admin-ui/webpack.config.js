const path = require('path');
const fs = require('fs');
const packageFile = fs.readFileSync('package.json');
const version = JSON.parse(packageFile).version;
const CopyWebpackPlugin = require('copy-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const HtmlWebpackHarddiskPlugin = require('html-webpack-harddisk-plugin');
const ReplaceInFileWebpackPlugin = require('replace-in-file-webpack-plugin');
const globImporter = require('node-sass-glob-importer');
const MinifyPlugin = require('babel-minify-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

const config = {
    mode: 'development',
    entry: {
        bundle: ['@babel/polyfill', './src/main.js'],
        styles: ['./styles/global.scss']
    },
    output: {
        filename: '[name].js',
        path: path.resolve('./public/dist')
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/env']
                    }
                }
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    {
                        loader: 'postcss-loader',
                        options: {
                            config: {
                                path: './postcss.config.js'
                            }
                        }
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            importer: globImporter()
                        }
                    }
                ]
            }
        ]
    },
    resolve: {
        extensions: ['.vue', '.js', '.html'],
        modules: ['node_modules'],
        alias: {
            vue: 'vue/dist/vue.js'
        }
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: '[name].css'
        }),
        new HtmlWebpackPlugin({
            template: './index.html',
            filename: '../index.html',
            alwaysWriteToDisk: true,
            inject: false
        }),
        new HtmlWebpackHarddiskPlugin(),
        new VueLoaderPlugin()
    ],
    optimization: {
        splitChunks: {
            cacheGroups: {
                commons: {
                    test: /[\\/]node_modules[\\/]/,
                    name: 'vendors',
                    chunks: 'all'
                },
                styles: {
                    name: 'styles',
                    test: /\.css$/,
                    chunks: 'all',
                    enforce: true
                }
            }
        },
        minimizer: []
    },
    devtool: 'source-map',
    devServer: {
        compress: true,
        https: false,
        port: 8071,
        historyApiFallback: true,
        contentBase: './public/',
        publicPath: '/dist/',
        watchOptions: {
            ignored: /node_modules/
        },
        inline: true,
        quiet: false,
        proxy: {
            '/api': {
                target: 'http://localhost:3000',
                secure: false,
                changeOrigin: true,
                pathRewrite: {
                    '^/api': ''
                }
            }
        }
    },
    performance: {
        hints: false
    }
};

module.exports = function (env = {}) {
    if (env.dashboard) {
        const Dashboard = require('webpack-dashboard');
        const DashboardPlugin = require('webpack-dashboard/plugin');
        const dashboard = new Dashboard({ port: 9000 });

        config.plugins.push(new DashboardPlugin(dashboard.setData));
    }

    const copyFiles = [
        {
            from: './assets/',
            to: 'assets/'
        },
        {
            from: './node_modules/tinymce/skins/',
            to: 'assets/tinymce-skins'
        }
    ];

    config.plugins.push(new CopyWebpackPlugin(copyFiles));

    const replaceInFileRules = [
        {
            search: /%version%/g,
            replace: version
        },
        {
            search: /(<!-- dev -->)([\s\S]*?)(<!-- !dev -->)/g,
            replace: ''
        }
    ];

    // Replace API URL depending on the environment
    // Add minification and remove devtool for staging and production
    if (env.production) {
        config.mode = 'production';
        config.devtool = false;

        config.resolve.alias.vue = 'vue/dist/vue.min';

        config.plugins.push(new MinifyPlugin());
    }

    config.plugins.push(new ReplaceInFileWebpackPlugin([
        {
            dir: path.resolve('./public/'),
            files: ['index.html'],
            rules: replaceInFileRules
        }
    ]));

    return config;
};