import Vue from 'vue'
window.Vue = Vue
require('./bootstrap');
Vue.use(require('vue-resource'));
window.axios = require('axios');
import Welcome from './components/Welcome';
import Properties from './components/Properties';
import Blog from './components/BlogComponent';
import MemberPaymentHistory from "./components/MemberPaymentHistory";
import sanitizeHTML from 'sanitize-html';
import MemberLogActivity from "./components/MemberLogActivity";
import Projects from './components/projects';
import FacilityComponent from './components/FacilityComponent';
import related from './components/related';
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
Vue.component('welcome',Welcome);
Vue.component('properties',Properties);
Vue.component('blog',Blog);
Vue.component('member',MemberPaymentHistory);
Vue.component('pagination', require('laravel-vue-pagination'));
Vue.component('member-log-activity',MemberLogActivity);
Vue.component('projects',Projects);
Vue.component('facility',FacilityComponent);
Vue.component('related',related);
Vue.prototype.__ = (key) => {
    return window.trans[key] !== 'undefined' ? window.trans[key] : key;
};




Vue.prototype.$sanitize = sanitizeHTML;
const app = new Vue({
    el: '#app'
});
