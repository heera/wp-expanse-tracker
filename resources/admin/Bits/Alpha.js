import Vue from '@/admin/Bits/elements';
import Rest from '@/admin/Bits/Rest';
import Router from 'vue-router';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

import {
    applyFilters,
    addFilter,
    addAction,
    doAction,
    removeAllActions
} from '@wordpress/hooks';

const moment = require('moment');
require('moment/locale/en-gb');
moment.locale('en-gb');
Vue.use(ElementUI);

export default class Alpha {
    constructor() {
        this.Router = Router;
        this.doAction = doAction;
        this.addFilter = addFilter;
        this.addAction = addAction;
        this.applyFilters = applyFilters;
        this.removeAllActions = removeAllActions;

        this.$rest = Rest;
        this.appVars = window.alphaAdmin;
        this.Vue = this.extendVueConstructor();
    }

    extendVueConstructor() {
        const self = this;

        Vue.mixin({
            data() {
                return {
                    appVars: self.appVars
                }
            },
            methods: {
                addFilter,
                applyFilters,
                doAction,
                addAction,
                removeAllActions,
                longLocalDate: self.longLocalDate,
                longLocalDateTime: self.longLocalDateTime,
                dateTimeFormat: self.dateTimeFormat,
                localDate: self.localDate,
                formatMoney: self.formatMoney,
                ucFirst: self.ucFirst,
                ucWords: self.ucWords,
                slugify: self.slugify,
                moment: moment
            },
            computed: {
                totalExpense() {
                    return window.alphaAdmin.total;
                },
                firstEntry() {
                    return window.alphaAdmin.firstEntry;
                },
                lastEntry() {
                    return window.alphaAdmin.lastEntry;
                }
            }
        });

        Vue.filter('dateTimeFormat', self.dateTimeFormat);
        Vue.filter('ucFirst', self.ucFirst);
        Vue.filter('ucWords', self.ucWords);
        Vue.filter('money', (val) => {
            return val;
        });

        Vue.use(this.Router);

        return Vue;
    }

    registerBlock(blockLocation, blockName, block) {
        this.addFilter(blockLocation, this.appVars.slug, function (components) {
            components[blockName] = block;
            return components;
        });
    }

    registerTopMenu(title, route) {
        if (!title || !route.name || !route.path || !route.component) {
            return;
        }

        this.addFilter('ninja_erp_top_menus', this.appVars.slug, function (menus) {
            menus = menus.filter(m => m.route !== route.name);
            menus.push({
                route: route.name,
                title: title
            });
            return menus;
        });

        this.addFilter('ninja_erp_global_routes', this.appVars.slug, function (routes) {
            routes = routes.filter(r => r.name !== route.name);
            routes.push(route);
            return routes;
        });
    }

    $get(url, options = {}) {
        return window.Alpha.$rest.get(url, options);
    }

    $post(url, options = {}) {
        return window.Alpha.$rest.post(url, options);
    }

    $del(url, options = {}) {
        return window.Alpha.$rest.delete(url, options);
    }

    $put(url, options = {}) {
        return window.Alpha.$rest.put(url, options);
    }

    $patch(url, options = {}) {
        return window.Alpha.$rest.patch(url, options);
    }

    dateTimeFormat(date, format) {
        const dateString = (date === undefined) ? null : date;
        const dateObj = moment(dateString);
        return dateObj.isValid() ? dateObj.format(format) : null;
    }

    localDate(date) {
        return moment.utc(date).local();
    }

    longLocalDate(date) {
        return this.dateTimeFormat(
            date, 'ddd, DD MMM, YYYY'
        );
    }

    longLocalDateTime(date) {
        return this.dateTimeFormat(
            date, 'ddd, DD MMM, YYYY hh:mm:ssa'
        );
    }

    formatMoney(amount) {
        return (new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'BDT'
        })).format(amount);
    }

    ucFirst(text) {
        return text[0].toUpperCase() + text.slice(1).toLowerCase();
    }

    ucWords(text) {
        return (text + '').replace(/^(.)|\s+(.)/g, function ($1) {
            return $1.toUpperCase();
        })
    }

    slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-') // Replace spaces with -
            .replace(/[^\w\\-]+/g, '') // Remove all non-word chars
            .replace(/\\-\\-+/g, '-') // Replace multiple - with single -
            .replace(/^-+/, '') // Trim - from start of text
            .replace(/-+$/, ''); // Trim - from end of text
    }
}
