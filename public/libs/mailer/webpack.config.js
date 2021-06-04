//const MiniCssExtractPlugin = require('mini-css-extract-plugin')
module.exports = {
  mode: 'development',
  entry: {
    'contact': './src/contact',
  },
  output: {
    path: __dirname + '/assets/',
    filename: '[name].js'
  },
  watch: true,
  devServer: {
    port: 8080
  },
  module: {
    rules: []
  },
  plugins: []
}
