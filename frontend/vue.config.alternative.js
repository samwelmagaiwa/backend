const { defineConfig } = require('@vue/cli-service')
const path = require('path')
const webpack = require('webpack')

// Alternative configuration with WebSocket enabled
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
    // Enable WebSocket functionality (alternative approach)
    webSocketServer: 'ws', // or 'sockjs'
    hot: true,
    liveReload: true,
    host: '127.0.0.1',
    port: 8080,
    allowedHosts: 'all',
    headers: {
      'Access-Control-Allow-Origin': '*'
    },
    client: {
      // Valid webSocketURL configurations:
      // Option 1: String URL
      webSocketURL: 'ws://127.0.0.1:8080/ws',
      // Option 2: Object configuration
      // webSocketURL: {
      //   hostname: '127.0.0.1',
      //   pathname: '/ws',
      //   port: 8080,
      //   protocol: 'ws'
      // },
      overlay: {
        warnings: false,
        errors: true
      },
      progress: true,
      reconnect: true,
      logging: 'info'
    }
  }
})