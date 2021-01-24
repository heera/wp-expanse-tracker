<template>
    <div class="dashboard">
        <div class="header">Dashboard</div>

        <div class="content">
            <el-row
                :gutter="20"
                v-for="(accounts, key) in sliceOfAccounts"
                :key="key"
                style="margin-bottom:20px;"
            >
                <el-col :span="2" :style="{
                    opacity: pagination.current_page == 1 ? .3 : 1
                }">
                    <div
                        style="margin-top:40px;height:50px;width:50px;border-radius:50%;float:left;border:solid 1px #409ef2;box-shadow: 0 0 0 2px #eee;"
                        :style="{cursor: pagination.current_page == 1 ? 'no-drop' : 'pointer'}"
                    >
                        <span
                            @click="prev"
                            class="el-icon-d-arrow-left"
                            style="font-size:15px;padding:18px;color:#409ef2;"
                        ></span>
                    </div>
                </el-col>

                <el-col
                    :span="6"
                    v-for="(account, aKey) in accounts"
                    :key="account.id"
                    :offset="aKey == 0 ? 1 : 0"
                >
                    <el-card shadow="always">
                        <div>
                            <strong style="font-size:15px;font-weight:400;">
                                <span
                                    @click="goto(account)"
                                    style="cursor:pointer;color:#409EFF;"
                                >
                                    <span class="el-icon-folder-opened"></span>
                                    {{ account.name }}
                                </span>

                                <confirm @yes="deleteAccount(account)">
                                    <span
                                        class="el-icon-delete"
                                        slot="reference"
                                        style="float:right;cursor:pointer;color:#F56C6C;"
                                    ></span>
                                </confirm>
                                <hr>
                            </strong>
                        </div>

                        <div>
                            Total Ledgers:
                            <span style="display:inline-block;float:right;">
                                <strong>{{ account.ledgers.length }}</strong>
                            </span>
                        </div>

                        <div>
                            Total Entries:
                            <span style="display:inline-block;float:right;">
                                <strong>{{ getTotalEntriesFrom(account) }}</strong>
                            </span>
                        </div>

                        <div>
                            Total Expense:
                            <span style="display:inline-block;float:right;">
                                <strong>{{ formatMoney(getTotalExpenseFrom(account)) }}</strong>
                            </span>
                        </div>
                    </el-card>
                </el-col>

                <el-col :span="2" style="float:right;" :style="{
                    opacity: pagination.current_page == pagination.last_page ? .3 : 1
                }">
                    <div
                        style="margin-top:40px;height:50px;width:50px;border-radius:50%;float:right;border:solid 1px #409ef2;box-shadow: 0 0 0 2px #eee;"
                        :style="{cursor: pagination.current_page == pagination.last_page ? 'no-drop' : 'pointer'}"
                    >
                        <span
                            @click="next"
                            class="el-icon-d-arrow-right"
                            style="font-size:15px;padding:18px;color:#409ef2;"
                        ></span>
                    </div>
                </el-col>
            </el-row>

            <el-row>
                <el-col>
                    <div class="header">
                        <span style="margin-top:7px;display:inline-block;">
                            Expense Chart
                        </span>

                        <el-button
                            plain
                            size="small"
                            type="primary"
                            style="float:right;margin-left:10px;"
                            @click="apply"
                        >Apply</el-button>

                        <el-date-picker
                            size="small"
                            v-model="dateRange"
                            type="daterange"
                            range-separator="To"
                            start-placeholder="Start date"
                            end-placeholder="End date"
                            format="dd-MM-yyyy"
                            style="float:right;"
                        />
                    </div>

                    <div class="content">
                        <chart
                            :height="300"
                            :chartData="chartData.datasets"
                            :chartLabels="chartData.labels"
                        />
                    </div>
                </el-col>
            </el-row>
        </div>
    </div>
</template>

<script>
    import Confirm from '@/admin/Pieces/Confirm';
    import Chart from './Chart';

    export default {
        name: 'Dashboard',
        components: { Confirm, Chart },
        data() {
            return {
                accounts: [],
                selected_account: null,
                chartData: {
                    labels: [],
                    datasets: []
                },
                pagination: {
                    page: 1
                },
                dateRange: []
            };
        },
        methods: {
            fetch() {
                this.$get('dashboard', {
                    page: this.pagination.page
                }).then(response => {
                    window.alphaAdmin.total = response.total;
                    this.accounts = response.accounts.data;
                    this.pagination.total = response.accounts.total;
                    this.pagination.from = response.accounts.from;
                    this.pagination.to = response.accounts.to;
                    this.pagination.last_page = response.accounts.last_page;
                    this.pagination.per_page = response.accounts.per_page;
                    this.pagination.current_page = response.accounts.current_page;
                    window.alphaAdmin.firstEntry = response.first;
                    window.alphaAdmin.lastEntry = response.last;
                });
            },
            getTotalEntriesFrom(account) {
                return account.ledgers
                    .map(l => l.entries.length)
                    .reduce((total, num) => total + num, 0);
            },
            getTotalExpenseFrom(account) {
                const totalExpensePerAccount = account.ledgers.map(
                    l => l.entries.reduce(
                        (total, entry) => total + Number(entry.amount), 0
                    )
                ).reduce((total, num) => total + num, 0);

                return totalExpensePerAccount;
            },
            goto(account) {
                this.$router.push({
                    name: 'account',
                    query: {
                        id: account.id
                    }
                });
            },
            deleteAccount(account) {
                const url = `accounts/${account.id}`;
                this.$del(url).then(response => {
                    this.fetch();
                    this.$success('Account Deleted Successfully.');
                });
            },
            prev() {
                if (this.pagination.current_page > 1) {
                    this.pagination.page = this.pagination.current_page - 1;
                    this.fetch();
                }
            },
            next() {
                if (this.pagination.current_page < this.pagination.last_page) {
                    this.pagination.page = this.pagination.current_page + 1;
                    this.fetch();
                }
            },
            setDate() {
                this.dateRange[0] = this.moment(this.dateRange[0]).format('YYYY-MM-DD');
                this.dateRange[1] = this.moment(this.dateRange[1]).format('YYYY-MM-DD');
            },
            getChartData() {
                this.$get('chart/data', {
                    from: this.dateRange[0],
                    to: this.dateRange[1]
                }).then(response => {
                    this.chartData.labels = response.stats.labels;
                    this.chartData.datasets = response.stats.values;
                });
            },
            apply() {
                this.setDate();
                this.getChartData();
            }
        },
        computed: {
            sliceOfAccounts() {
                let i, j;
                const temparray = [];
                const chunk = 3;

                if (this.accounts.length) {
                    for (i = 0, j = this.accounts.length; i < j; i += chunk) {
                        temparray[i] = this.accounts.slice(i, i + chunk);
                    }
                }

                return temparray;
            }
        },
        created() {
            this.dateRange = [
                this.moment().subtract(30, 'days').format('YYYY-MM-DD'),
                this.moment().format('YYYY-MM-DD')
            ];

            this.fetch();
            this.apply();
        }
    };
</script>

<style>
    .dashboard .el-icon-d-arrow-left,
    .dashboard .el-icon-d-arrow-right {
        vertical-align:top;
    }
</style>
