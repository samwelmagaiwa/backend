const { defineConfig } = require('@vue/cli-service')
const path = require('path')
const webpack = require('webpack')

module.exports = defineConfig({
  transpileDependencies: true,
  runtimeCompiler: true,
  publicPath: process.env.NODE_ENV === 'production' ? '/' : '/',
  configureWebpack: {
    resolve: {
      alias: {
        '@': path.resolve(__dirname, 'src'),
        vue: 'vue/dist/vue.esm-bundler.js'
      }
    },
    plugins: [
      new webpack.DefinePlugin({
        __VUE_OPTIONS_API__: JSON.stringify(true),
        __VUE_PROD_DEVTOOLS__: JSON.stringify(false),
        __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: JSON.stringify(false),
        'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV || 'development')
      })
    ],
    // Disable all caching for debugging
    cache: false,
    optimization: {
      minimize: false,
      concatenateModules: false
    }
  },
  devServer: {
    static: {
      directory: path.join(__dirname, 'public'),
      publicPath: '/'
    },
    // Enable hot reload for debugging
    hot: true,
    liveReload: true,
    host: '127.0.0.1',
    port: 8080,
    allowedHosts: 'all',
    headers: {
      'Access-Control-Allow-Origin': '*',
      'Cache-Control': 'no-cache, no-store, must-revalidate',
      'Pragma': 'no-cache',
      'Expires': '0'
    },
    client: {
      overlay: true,
      progress: true
    },
    devMiddleware: {
      writeToDisk: false
    }
  },
  // Disable all caching
  css: {
    extract: false
  }
})