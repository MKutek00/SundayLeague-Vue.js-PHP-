const { defineConfig } = require('@vue/cli-service');
module.exports = defineConfig({
  transpileDependencies: ['vuetify'],
  configureWebpack: {
    devServer: {
      headers: { 'Access-Control-Allow-Origin': '*' },
    },
  },
});
