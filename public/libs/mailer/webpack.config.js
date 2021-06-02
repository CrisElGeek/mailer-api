//const MiniCssExtractPlugin = require('mini-css-extract-plugin')
module.exports = {
  mode: 'development',
  entry: {
    'mailer': './src/mailer',
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
