const { defineConfig } = require('@vue/cli-service')
const path = require('path')
const webpack = require('webpack')

module.exports = defineConfig({
  transpileDependencies: true,
  publicPath: process.env.NODE_ENV === 'production' ? '/' : '/',
  configureWebpack: {
    resolve: {
      alias: {
        '@': path.resolve(__dirname, 'src')
      }
    },
    plugins: [
      new webpack.DefinePlugin({
        __VUE_OPTIONS_API__: JSON.stringify(true),
        __VUE_PROD_DEVTOOLS__: JSON.stringify(false),
        __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: JSON.stringify(false)
      })
    ]
  },
  devServer: {
    static: {
      directory: path.join(__dirname, 'public'),
      publicPath: '/'
    },
    // Completely disable all WebSocket functionality
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
      webSocketURL: false,  // Completely disable WebSocket URL
      overlay: false,
      progress: false,
      reconnect: false,
      logging: 'none',
      webSocketTransport: 'sockjs'
    },
    // Additional WebSocket disabling
    devMiddleware: {
      writeToDisk: false
    },
    // Force disable WebSocket at webpack level
    onBeforeSetupMiddleware: function(devServer) {
      // Disable WebSocket upgrade
      devServer.app.use((req, res, next) => {
        if (req.headers.upgrade === 'websocket') {
          res.status(404).end()
          return
        }
        next()
      })
    }
  }
})