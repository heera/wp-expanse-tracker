<template>
    <div class="reports">
        <div class="header">Reports</div>

        <div class="content">
            <el-form :model="form" label-position="left">
                <el-form-item label="Date Range" :label-width="formLabelWidth">
                    <el-date-picker
                        type="daterange"
                        v-model="form.date_range"
                        placeholder="Select date"
                        value-format="yyyy-MM-dd"
                        format="dd-MM-yyyy"
                        range-separator="To"
                        start-placeholder="Start date"
                        end-placeholder="End date"
                        style="width: 100%;"
                        :picker-options="pickerOptions"
                        popper-class="reports-daterange"
                    ></el-date-picker>
                </el-form-item>

                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="Select Account" :label-width="formLabelWidth">
                            <el-select v-model="form.account_id">
                                <el-option
                                    v-for="account in accounts"
                                    :label="account.name"
                                    :value="account.id"
                                    :key="account.id"
                                />
                            </el-select>
                            <error :error="errors.get('account_id')" />
                        </el-form-item>
                    </el-col>

                    <el-col :span="12">
                        <el-form-item label="Select Ledger" :label-width="formLabelWidth">
                            <el-select v-model="form.ledger_id">
                                <el-option
                                    v-for="ledger in ledgers"
                                    :label="ledger.name"
                                    :value="ledger.id"
                                    :key="ledger.id"
                                />
                            </el-select>
                            <error :error="errors.get('ledger_id')" />
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-form-item align="right">
                    <el-button
                        :loading="fetching"
                        type="primary"
                        size="small"
                        @click="fetch"
                    >Fetch</el-button>
                </el-form-item>
            </el-form>

            <div class="result" v-if="result.length">
                <hr>
                <el-table
                    :data="result"
                    :summary-method="getSummaries"
                    :show-summary="pagination.total > pagination.per_page"
                >
                    <el-table-column prop="title" label="Expense" width="300" />

                    <el-table-column prop="ledger.name" label="Ledger" />

                    <el-table-column prop="ledger.account.name" label="Account" />
                    
                    <el-table-column prop="created_at" label="Created At">
                        <template slot-scope="scope">
                            {{ longLocalDate(scope.row.created_at) }}
                        </template>
                    </el-table-column>

                    <el-table-column prop="updated_at" label="Updated At">
                        <template slot-scope="scope">
                            {{ longLocalDate(scope.row.updated_at) }}
                        </template>
                    </el-table-column>

                    <el-table-column prop="amount" label="Amount" width="150" align="right">
                        <template slot-scope="scope">
                            {{ formatMoney(scope.row.amount) }}
                        </template>
                    </el-table-column>
                </el-table>

                <el-row
                    v-if="total"
                    style="padding: 15px 10px;background-color:#F5F7FA;color:#606266;line-height: 1.4em;font-size: 14px;font-weight:700;"
                >
                    <el-col :span="4">{{ totalTitle }}</el-col>
                    <el-col :span="20" style="text-align: right;">
                        {{ formatMoney(total) }}
                    </el-col>
                </el-row>

                <div style="margin-top:20px;text-align:right;">
                    <pagination :pagination="pagination" @fetch="pageChanged" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Pagination from '@/admin/Pieces/Pagination';
    import Error from '@/admin/Pieces/Error';
    import Errors from '@/admin/Bits/Errors';

    export default {
        name: 'Reports',
        components: { Pagination, Error },
        data() {
            return {
                accounts: [],
                ledgers: [],
                formLabelWidth: '120px',
                form: {
                    date_range: null,
                    account_id: null,
                    ledger_id: null
                },
                pagination: {
                    total: 0,
                    per_page: 8,
                    current_page: 1
                },
                errors: new Errors(),
                fetching: false,
                result: [],
                pickerOptions: {
                    shortcuts: [
                        {
                            text: 'Last week',
                            onClick(picker) {
                                const end = new Date();
                                const start = new Date();
                                start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                                picker.$emit('pick', [start, end]);
                            }
                        },
                        {
                            text: 'Last month',
                            onClick(picker) {
                                const end = new Date();
                                const start = new Date();
                                start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                                picker.$emit('pick', [start, end]);
                            }
                        },
                        {
                            text: 'Last 3 months',
                            onClick(picker) {
                                const end = new Date();
                                const start = new Date();
                                start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                                picker.$emit('pick', [start, end]);
                            }
                        }
                    ]
                }
            };
        },
        methods: {
            fetch() {
                this.fetching = true;
                const data = {
                    ...this.form,
                    page: this.pagination.current_page
                };

                this.$get('reports', data).then(response => {
                    this.total = [response.total];
                    this.result = response.entries.data;
                    this.pagination.total = response.entries.total;
                }).catch(error => {
                    console.log(error);
                }).always(() => {
                    this.fetching = false;
                })
            },
            pageChanged() {
                this.fetch();
            },
            getSummaries(param) {
                const sums = [];
                const { columns, data } = param;

                columns.forEach((column, index) => {
                    if (index === 0) {
                        sums[index] = 'Page Total';
                        return;
                    }
                    
                    const values = data.map(item => {
                        return Number(item[column.property]);
                    });

                    if (!values.every(value => isNaN(value))) {
                        sums[index] = values.reduce((prev, curr) => {
                            const value = Number(curr);
                            if (!isNaN(value)) {
                                return prev + curr;
                            } else {
                                return prev;
                            }
                        }, 0);
                    }
                });
                
                sums[5] = this.formatMoney(sums[5]);

                return sums;
            }
        },
        computed: {
            totalTitle() {
                let title = 'Total';

                if (this.pagination.total > this.pagination.per_page) {
                    title = 'Grand Total';
                }

                return title;
            }
        },
        watch: {
            'form.account_id': function(accountId) {
                if (!this.form.id) {
                    this.form.ledger_id = null;
                }

                this.$get('accounts/ledgers', { account_id: accountId }).then(response => {
                    this.ledgers = response;
                });
            }
        },
        created() {
            this.$get('accounts', { all: true }).then(response => {
                this.accounts = response.accounts;
                this.firstEntry = response.first;
                this.lastEntry = response.last;
            });

            let date = this.$route.query.date;
            
            if (date) {
                date = date.split('-').reverse().join('-');
                this.form.date_range = [date, date];
                this.fetch();
            } else {
                this.form.date_range = [
                    this.moment().startOf('month').format('YYYY-MM-DD'),
                    this.moment(new Date()).format('YYYY-MM-DD')
                ];
            }
        }
    };
</script>

<style>
    .reports .el-table__footer-wrapper {
        font-weight: 600;
    }

    .reports-daterange .el-picker-panel__body-wrapper .el-picker-panel__sidebar {
        width: 115px;
    }
</style>
