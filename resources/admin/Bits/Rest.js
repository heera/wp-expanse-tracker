export default {
    get(route, data = {}) {
        return window.Alpha.request('GET', route, data);
    },
    post(route, data = {}) {
        return window.Alpha.request('POST', route, data);
    },
    delete(route, data = {}) {
        return window.Alpha.request('DELETE', route, data);
    },
    put(route, data = {}) {
        return window.Alpha.request('PUT', route, data);
    },
    patch(route, data = {}) {
        return window.Alpha.request('PATCH', route, data);
    }
};

jQuery(document).ajaxSuccess((event, xhr, settings) => {
    const nonce = xhr.getResponseHeader('X-WP-Nonce');
    if (nonce) {
        window.alphaAdmin.rest.nonce = nonce;
    }
});
