import Vue from 'vue';
import Consilio from '@vatsim-uk/consilio';
import App from './App.vue';
import '@vatsim-uk/consilio/dist/consilio.css';

Vue.use(Consilio);
Vue.config.productionTip = false;

new Vue({
  render: (h) => h(App),
}).$mount(document.getElementById('ukcp-web-app'));
