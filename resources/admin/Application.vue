<template>
    <div class="alpha-app">
        <div class="alpha-main-menu-items">
            <el-menu
                :router="true"
                mode="horizontal"
                class="alpha-navigation"
                :default-active="active"
            >
                <el-menu-item
                    :key="item.route"
                    :index="item.route"
                    v-html="item.title"
                    v-for="item in items"
                    :route="{ name: item.route }"
                />

                <li>
                    <div
                        v-if="isSearchable"
                        style="display:inline-block;margin:15px 0px 0;float:left;"
                    >
                        <el-input
                            size="small" 
                            v-model="search"
                            style="width:300px;"
                            :placeholder="getPlaceHolder"
                            @input="searchRecords"
                        >
                            <i
                                slot="prefix"
                                class="el-icon-search el-input__icon"
                                style="cursor:pointer;"
                                @click="searchRecords"
                            ></i>
                        </el-input>
                    </div>
                </li>

                <div style="display:inline-block;margin:3px 20px 0;float:right;font-weight:500;color:#909399;">
                    <div style="border-bottom:solid 1px #b3d8ff;padding:0 0 2px 0;">
                        Total Expanse: {{ formatMoney(totalExpense) }}
                    </div>
                    <div v-if="firstEntry" style="text-align:center;padding:2px 0 0 0;">
                        {{ firstEntry }} &nbsp; to &nbsp; {{ lastEntry }}
                    </div>
                </div>
            </el-menu>
        </div>

        <div class="alpha-body">
            <router-view :key="$route.fullPath"></router-view>
        </div>
    </div>
</template>

<script type="text/babel">
    export default {
        name: 'AlphaExpanseTracker',
        data() {
            return {
                logo: '',
                items: [],
                active: null,
                search: ''
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
            syncSearch(s) {
                console.log(s);
            },
            getRoutes() {
                return this.$router.options.routes
                    .filter(r => r.meta && r.meta.title)
                    .map(r => ({ route: r.name, title: r.meta.title }));
            },
            setMenus() {
                this.items = this.applyFilters('alpha_top_menus', this.getRoutes());
                this.setActive();
            },
            setActive() {
                this.active = this.$route.meta.parent?.toLowerCase() || this.$route.name;
            },
            searchRecords() {
                this.$bus.$emit('search', this.search);
            }
        },
        computed: {
            isSearchable() {
                const items = ['dashboard', 'reports', 'entry'];
                return items.indexOf(this.$route.name) === -1 && this.$route.meta.title;
            },
            getPlaceHolder() {
                return `Search ${this.$route.meta?.title}...`;
            }
        },
        created() {
            this.$bus.$on('search.sync', s => {
                this.search = s;
            });

            jQuery('.notice').remove();
            this.setMenus();
        }
    };
</script>
