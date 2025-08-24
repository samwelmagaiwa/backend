const { defineConfig } = require('@vue/cli-service')
const path = require('path')
const webpack = require('webpack')

// Try to load compression plugin, fallback if not available
let CompressionPlugin
try {
  CompressionPlugin = require('compression-webpack-plugin')
} catch (error) {
  console.warn('compression-webpack-plugin not found. Compression will be disabled.')
  console.warn('Run: npm install compression-webpack-plugin@^10.0.0 to enable compression.')
  CompressionPlugin = null
}

module.exports = defineConfig({
  transpileDependencies: true,
  publicPath: process.env.NODE_ENV === 'production' ? '/' : '/',
  
  // Production optimizations
  productionSourceMap: false, // Disable source maps in production for smaller bundle
  
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
      }),
      // Add compression plugin for production (if available)
      ...(process.env.NODE_ENV === 'production' && CompressionPlugin ? [
        new CompressionPlugin({
          algorithm: 'gzip',
          test: /\.(js|css|html|svg)$/,
          threshold: 8192,
          minRatio: 0.8
        })
      ] : [])
    ],
    
    // Optimization settings
    optimization: {
      splitChunks: {
        chunks: 'all',
        cacheGroups: {
          vendor: {
            test: /[\\/]node_modules[\\/]/,
            name: 'vendors',
            chunks: 'all',
            priority: 10
          },
          common: {
            name: 'common',
            minChunks: 2,
            chunks: 'all',
            priority: 5,
            reuseExistingChunk: true
          }
        }
      },
      runtimeChunk: {
        name: 'runtime'
      }
    },
    
    // Performance hints
    performance: {
      hints: process.env.NODE_ENV === 'production' ? 'warning' : false,
      maxEntrypointSize: 512000,
      maxAssetSize: 512000
    }
  },
  
  // CSS optimization
  css: {
    extract: process.env.NODE_ENV === 'production' ? {
      ignoreOrder: true
    } : false,
    loaderOptions: {
      postcss: {
        postcssOptions: {
          plugins: [
            require('tailwindcss'),
            require('autoprefixer'),
            ...(process.env.NODE_ENV === 'production' ? [
              require('cssnano')({
                preset: 'default'
              })
            ] : [])
          ]
        }
      }
    }
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
    // Force disable WebSocket at webpack level using new setupMiddlewares API
    setupMiddlewares: (middlewares, devServer) => {
      // Add middleware to disable WebSocket upgrade
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