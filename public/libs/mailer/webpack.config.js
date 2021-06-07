const MiniCssExtractPlugin = require('mini-css-extract-plugin')
module.exports = {
  mode: 'development',
  entry: {
    'contact': './src/contact',
  },
  output: {
    path: __dirname + '/assets',
    filename: '[name].js'
  },
  watch: true,
  devServer: {
    port: 8080
  },
	module: {
        rules: [{
                test: /\.(sa|sc|c)ss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    { loader: "css-loader", options: {} },
                    {
                        loader: "postcss-loader",
                        options: {
postcssOptions: {
        // postcss plugins, can be exported to postcss.config.js
        plugins: function () {
          return [
            require('autoprefixer')
          ];
        }
      }
                        },
                    },
                    { loader: "sass-loader", options: {} },
                ],
            },
            {
                test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
                use: [{
                    loader: "file-loader",
                    options: {
                        name: "[name].[ext]",
                        outputPath: "../assets/",
                    },
                }, ],
            },
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: "[name].css",
            chunkFilename: "[id].css",
        }),
    ],
};
