<template>
    <div class="dashboard">
        <div class="header">
            <span style="float:left;">Accounts</span>

            <span
                @click="createAccount"
                style="float:right;color:#46A0FC;cursor:pointer;font-weight:400;"
            >
                <i class="el-icon-plus"></i> Create Account
            </span>
        </div>

        <div class="content">
            
            <el-row :gutter="20" v-if="display=='card'">
                <el-col
                    :span="6"
                    v-for="account in accounts"
                    :key="account.id"
                    style="margin-bottom:10px;"
                >
                    <el-card class="box-card">
                        <div slot="header" class="clearfix">
                            <el-button type="text" @click="viewAccount(account)" style="padding:0;">
                                {{ account.name }}
                            </el-button>

                            <el-button type="text" style="float:right; padding:0;">
                                <el-button
                                    type="primary"
                                    size="mini"
                                    icon="el-icon-edit"
                                    @click="editAccount(account)"
                                    style="margin-left: 0px;"
                                />

                                <confirm @yes="deleteAccount(account)">
                                    <el-button
                                        size="mini"
                                        type="danger"
                                        icon="el-icon-delete"
                                        slot="reference"
                                    />
                                </confirm>
                            </el-button>
                        </div>
                        <div>
                            <div style="border-bottom:solid 1px #eee;padding: 2px 0;">
                                Total Ledgers:
                                <span style="float:right;">{{ account.ledgers.length }}</span>
                            </div>

                            <div style="border-bottom:solid 1px #eee;padding: 2px 0;">
                                Total Expense:
                                <span style="float:right;">{{ formatMoney(account.total) }}</span>
                            </div>

                            <div style="border-bottom:solid 1px #eee;padding: 2px 0;">
                                Created At:
                                <span style="float:right;">
                                    {{ longLocalDate(account.created_at) }}
                                </span>
                            </div>
                        </div>
                    </el-card>
                </el-col>
            </el-row>

            <el-table v-else :data="accounts" style="width: 100%">
                <el-table-column label="Account" prop="name" sortable>
                    <template slot-scope="scope">
                        <el-button type="text" @click="viewAccount(scope.row)">
                            <span class="el-icon-link"></span> {{ scope.row.name }}
                        </el-button>
                    </template>
                </el-table-column>

                <el-table-column label="Number of Ledgers" align="center">
                    <template slot-scope="scope">
                        {{ scope.row.ledgers.length }}
                    </template>
                </el-table-column>

                <el-table-column label="Created At">
                    <template slot-scope="scope">
                        {{ longLocalDate(scope.row.created_at) }}
                    </template>
                </el-table-column>

                <el-table-column label="Total Expense" align="right">
                    <template slot-scope="scope">
                        {{ formatMoney(scope.row.total) }}
                    </template>
                </el-table-column>
                
                <el-table-column label="Actions" align="center">
                    <template slot-scope="scope">
                        <el-button
                            type="primary"
                            size="mini"
                            icon="el-icon-edit"
                            @click="editAccount(scope.row)"
                            style="margin-left: 0px;"
                        />

                        <confirm @yes="deleteAccount(scope.row)">
                            <el-button
                                size="mini"
                                type="danger"
                                icon="el-icon-delete"
                                slot="reference"
                            />
                        </confirm>
                    </template>
                </el-table-column>
            </el-table>

            <div style="margin-top:20px;text-align:right;">
                <pagination :pagination="pagination" @fetch="pageChanged" />
            </div>
        </div>

        <el-dialog :title="dialogTitle" :visible.sync="dialogVisible">
            <el-form :model="form" label-position="left">
                <el-form-item label="Account Name" :label-width="formLabelWidth">
                    <el-input v-model="form.name" autocomplete="off"></el-input>
                    <error :error="errors.get('name')" />
                </el-form-item>

                <el-form-item label="Note" :label-width="formLabelWidth">
                    <el-input type="textarea" v-model="form.note" autocomplete="off"></el-input>
                </el-form-item>
            </el-form>

            <span slot="footer" class="dialog-footer">
                <el-button @click="close" size="small">Close</el-button>
                <el-button
                    size="small"
                    type="primary"
                    @click="save"
                    :loading="saving"
                >Save</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
    import Pagination from '@/admin/Pieces/Pagination';
    import Confirm from '@/admin/Pieces/Confirm';
    import Error from '@/admin/Pieces/Error';
    import Errors from '@/admin/Bits/Errors';

    const model = {
        name: '',
        note: ''
    };

    export default {
        name: 'Account',
        components: { Pagination, Confirm, Error },
        data() {
            return {
                display: 'card',
                search: null,
                accounts: [],
                pagination: {
                    total: 0,
                    per_page: 8,
                    current_page: 1
                },
                dialogVisible: false,
                formLabelWidth: '120px',
                form: { ...model },
                saving: false,
                errors: new Errors(),
                dialogTitle: 'Create Account'
            };
        },
        methods: {
            fetch() {
                const data = {
                    search: this.search,
                    page: this.pagination.current_page
                };

                this.$get('accounts', data).then(response => {
                    this.accounts = response.accounts.data;
                    window.alphaAdmin.total = response.total;
                    this.pagination.total = response.accounts.total;
                    window.alphaAdmin.firstEntry = response.first;
                    window.alphaAdmin.lastEntry = response.last;
                });
            },
            save() {
                let url = 'accounts';
                if (this.form.id) {
                    url = `accounts/${this.form.id}`;
                }
                
                this.saving = true;
                this.$post(url, { ...this.form }).then(response => {
                    this.close();
                    this.fetch();
                    this.$success('Account Saved Successfully.');
                }).fail(error => {
                    this.errors.record(error);
                }).always(() => {
                    this.saving = false;
                });
            },
            deleteAccount(account) {
                const url = `accounts/${account.id}`;
                this.$del(url).then(response => {
                    this.fetch();
                    this.$success('Account Deleted Successfully.');
                });
            },
            close() {
                this.dialogVisible = false;
            },
            createAccount() {
                this.errors.clear();
                this.form = { ...model };
                this.dialogVisible = true;
            },
            editAccount(account) {
                this.errors.clear();
                this.dialogTitle = 'Edit Account';
                this.form = { ...account };
                this.dialogVisible = true;  
            },
            viewAccount(account) {
                this.$router.push({
                    name: 'account',
                    query: {
                        id: account.id
                    }
                });
            },
            pageChanged() {
                this.$router.push({
                    name: 'accounts',
                    query: {
                        page: this.pagination.current_page
                    }
                });
            }
        },
        created() {
            const page = Number(this.$route.query.page);
            this.pagination.current_page = page || 1;
            this.search = this.$route.query.search;
            this.fetch();

            this.$bus.$emit('search.sync', this.search);
            this.$bus.$on('search', query => {
                this.search = query;
                this.fetch();
            });
        }
    };
</script>
