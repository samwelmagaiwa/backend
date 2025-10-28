const { defineConfig } = require('@vue/cli-service')
const path = require('path')
const webpack = require('webpack')
const CompressionWebpackPlugin = require('compression-webpack-plugin')

module.exports = defineConfig({
  transpileDependencies: true,
  runtimeCompiler: true,
  publicPath: process.env.NODE_ENV === 'production' ? '/' : '/',
  configureWebpack: () => {
    const base = {
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
          // Define NODE_ENV specifically for the browser
          'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV || 'development')
        })
      ]
    }

    // Production-only optimizations
    if (process.env.NODE_ENV === 'production') {
      base.plugins.push(
        new CompressionWebpackPlugin({
          filename: '[path][base].gz',
          algorithm: 'gzip',
          test: /\.(js|css|html|svg)$/i,
          threshold: 10240,
          minRatio: 0.8
        })
      )
    }

    // Return object to be merged with Vue CLI's config
    return base
  },
  devServer: {
    static: {
      directory: path.join(__dirname, 'public'),
      publicPath: '/'
    },
    // Disable WebSocket functionality
    webSocketServer: false,
    hot: false,
    liveReload: false,
    host: '127.0.0.1',
    port: 8080,
    allowedHosts: 'all',
    setupExitSignals: false,
    headers: {
      'Access-Control-Allow-Origin': '*'
    },
    client: {
      // Remove webSocketURL entirely when disabling WebSocket
      overlay: false,
      progress: false,
      reconnect: false,
      logging: 'none'
    },
    // Additional WebSocket disabling
    devMiddleware: {
      writeToDisk: false
    },
    // Force disable WebSocket at webpack level
    setupMiddlewares: (middlewares, devServer) => {
      // Disable WebSocket upgrade
      devServer.app.use((req, res, next) => {
        if (req.headers.upgrade === 'websocket') {
          res.status(404).end()
          return
        }
        next()
      })
      return middlewares
    }
  }
})
