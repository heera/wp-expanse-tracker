import routes from './routes';
const vueRouter = new window.Alpha.Router({
    routes: window.Alpha.applyFilters('alpha_global_routes', routes)
});

window.Alpha.Vue.prototype.$rest = window.Alpha.$rest;
window.Alpha.Vue.prototype.$get = window.Alpha.$get;
window.Alpha.Vue.prototype.$post = window.Alpha.$post;
window.Alpha.Vue.prototype.$del = window.Alpha.$del;
window.Alpha.Vue.prototype.$put = window.Alpha.$put;
window.Alpha.Vue.prototype.$patch = window.Alpha.$patch;
window.Alpha.Vue.prototype.$bus = new window.Alpha.Vue();

window.Alpha.Vue.prototype.$success = function(msg) {
    return window.Alpha.Vue.prototype.$notify.success({
        title: 'Great!',
        message: msg,
        offset: 19
    });
};

window.Alpha.Vue.prototype.$error = function(msg) {
    return window.Alpha.Vue.prototype.$notify.error({
        title: 'Oops!',
        message: msg,
        offset: 19
    });
};

window.Alpha.request = function(method, route, data = {}) {
    const url = `${window.alphaAdmin.rest.url}/${route}`;
    
    return window.jQuery.ajax({
        url: url,
        type: method,
        data: data,
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-WP-Nonce', window.alphaAdmin.rest.nonce);
        }
    });
};

new window.Alpha.Vue({
    el: '#alpha_app',
    render: h => h(require('./Application').default),
    router: vueRouter
});
