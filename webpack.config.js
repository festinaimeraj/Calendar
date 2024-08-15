module.exports = {
    resolve: {
      extensions: ['.js', '.json', '.vue'], // Ensure .vue is included here
      alias: {
        'vue$': 'vue/dist/vue.esm-bundler.js'
      }
    },
    module: {
      rules: [
        {
          test: /\.vue$/,
          loader: 'vue-loader'
        },
        {
          test: /\.js$/,
          loader: 'babel-loader',
          exclude: /node_modules/
        }
      ]
    }
  };
  