const HtmlWebpackPlugin = require('html-webpack-plugin')
const Encore = require('@symfony/webpack-encore')
const webpack = require('webpack')
const fs = require('fs')

const isProduction = Encore.isProduction()
const publicPath = isProduction ? '/build' : '/'

Encore
  // the project directory where compiled assets will be stored
  .setOutputPath('public/build/')
  // the public path used by the web server to access the previous directory
  .setPublicPath(publicPath)
  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!isProduction)
  // .enableSourceMaps(true)
  .enableVersioning(isProduction)

  .configureFilenames({js: '[name].[hash].js'})

  // uncomment to define the assets of the project
  // .addEntry('babel-polyfill', 'babel-polyfill')
  .addEntry('js/main', './assets/js/main.js')
  // .addEntry('js/app', './assets/js/app/app.js')
  // .addEntry('js/manager', './assets/js/manager/app.js')
  // .addStyleEntry('css/app', './assets/css/app.scss')

  // uncomment if you use Sass/SCSS files
  .enableSassLoader()

  // uncomment for legacy applications that require $/jQuery as a global variable
  // .autoProvidejQuery()
  .enableVueLoader()
  .addLoader({
    test: /\.(js|vue)$/,
    loader: 'eslint-loader',
    exclude: [/node_modules/],
    enforce: 'pre',
    options: {
      configFile: './.eslintrc',
      emitWarning: true
    }
  })
  .addPlugin(
    new HtmlWebpackPlugin({
      title: 'inFlux Manager',
      filename: 'index.html',
      template: 'assets/index.html',
      appMountId: 'app'
    })
  )
  .addPlugin(new webpack.HotModuleReplacementPlugin())

const webpackData = Encore.getWebpackConfig()

// Config file
const configFilePath = './.dev-server.config.json'
if (fs.existsSync(configFilePath) === false) {
  fs.writeFileSync(configFilePath, JSON.stringify({ env: 'http://localhost:8000' }, null, 2))
}

const ConfigJSON = require(configFilePath)

webpackData.devServer = {
  hot: true,
  contentBase: './public',
  disableHostCheck: true,
  historyApiFallback: true,
  host: '0.0.0.0',
  port: 8081,
  // http2: true,
  proxy: {
    '/uploads/**': {
      target: ConfigJSON.env,
      secure: false,
      changeOrigin: true
    }
  }
}

for (const rule of webpackData.module.rules) {
  if (rule.use) {
    for (let loader of rule.use) {
      if (loader.loader === 'babel-loader') {
        delete rule.exclude
      }
    }
  }
}

module.exports = webpackData
