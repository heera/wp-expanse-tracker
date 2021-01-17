<template>
    <el-menu
        :router="true"
        mode="horizontal"
        class="ninja-erp-navigation"
        :default-active="active"
        style="padding-left:10px;"
    >
        <el-menu-item
            :key="item.route"
            :index="item.route"
            v-html="item.title"
            v-for="item in items"
            :route="{ name: item.route }"
        />
    </el-menu>
</template>

<script>
    export default {
        name: 'Navigation',
        data() {
            return {
                active: null,
                items: []
            }
        },
        watch: {
            '$route'(to, from) {
                if (this.$route.name) {
                    this.setActive();
                }
            }
        },
        methods: {
            defaultRoutes() {
                return [
                    {
                        route: 'crm-dashboard',
                        title: 'Dashboard'
                    },
                    {
                        route: 'crm-contacts',
                        title: 'Contacts'
                    },
                    {
                        route: 'crm-companies',
                        title: 'Companies'
                    },
                    {
                        route: 'crm-contact_groups',
                        title: 'Contact Groups'
                    }
                ]
            },
            setMenus() {
                this.items = this.applyFilters('ninja_erp_top_menus', this.defaultRoutes());
            },
            setActive() {
                this.active = this.$route.meta.parent || this.$route.name;
            }
        },
        mounted() {
            this.setMenus();
        }
    }
</script>
