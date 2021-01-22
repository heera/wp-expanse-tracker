<template>
    <div class="dashboard">
        <div class="header">
            <span style="float:left;">Entries</span>
            
            <el-dropdown
                trigger="click"
                style="cursor:pointer;margin-left:10px;"
                @command="filterByAccount"
            >
                <span class="el-dropdown-link" style="font-weight:500;color:#697386;font-size:13px;">
                    Filter By Account
                    <span>({{ filteredAccount }})</span>
                    <i class="el-icon-arrow-down el-icon--right"></i>
                </span>

                <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item>All</el-dropdown-item>
                    <el-dropdown-item
                        v-for="account in accounts"
                        :command="account.id"
                        :key="account.id"
                    >{{ account.name }}</el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>

            <span
                @click="createEntry"
                style="float:right;color:#46A0FC;cursor:pointer;font-weight:400;"
            >
                <i class="el-icon-plus"></i> Create Entry
            </span>
        </div>

        <div class="content">
            <el-row :gutter="20" v-if="display=='card'">
                <el-col :span="8" v-for="entry in entries" style="margin-bottom:10px;">
                    <el-card class="box-card">
                        <div slot="header" class="clearfix">
                            <el-button type="text" @click="viewEntry(entry)" style="padding:0;">
                                {{ entry.title }}
                            </el-button>

                            <el-button type="text" style="float:right; padding:0;">
                                <el-button
                                    type="primary"
                                    size="mini"
                                    icon="el-icon-edit"
                                    @click="editEntry(entry)"
                                    style="margin-left: 0px;"
                                />

                                <confirm @yes="deleteEntry(entry)">
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
                                Ledger:
                                <span style="float:right;">
                                    <el-button
                                        type="text"
                                        style="padding:0;"
                                        @click="viewLedger(entry)"
                                    >{{ entry.ledger.name }}</el-button>
                                </span>
                            </div>

                            <div style="border-bottom:solid 1px #eee;padding: 2px 0;">
                                Account:
                                <span style="float:right;">
                                    <el-button
                                        type="text"
                                        style="padding:0;"
                                        @click="viewAccount(entry)"
                                    >{{ entry.ledger.account.name }}</el-button>
                                </span>
                            </div>

                            <div style="border-bottom:solid 1px #eee;padding: 2px 0;">
                                Amount:
                                <span style="float:right;">{{ formatMoney(entry.amount) }}</span>
                            </div>

                            <div style="border-bottom:solid 1px #eee;padding: 2px 0;">
                                Created At:
                                <span style="float:right;">
                                    {{ longLocalDate(entry.created_at) }}
                                </span>
                            </div>

                            <div style="border-bottom:solid 1px #eee;padding: 2px 0;">
                                Updated At:
                                <span style="float:right;">
                                    {{ longLocalDate(entry.updated_at) }}
                                </span>
                            </div>
                        </div>
                    </el-card>
                </el-col>
            </el-row>

            <el-table v-else :data="entries" style="width: 100%">
                <el-table-column label="Entry" width="220" prop="title" sortable>
                    <template slot-scope="scope">
                        <el-button type="text" @click="viewEntry(scope.row)">
                            <span class="el-icon-link"></span> {{ scope.row.title }}
                        </el-button>
                    </template>
                </el-table-column>

                <el-table-column label="Ledger" width="200" prop="ledger.name" sortable>
                    <template slot-scope="scope">
                        <el-button type="text" @click="viewLedger(scope.row)">
                            <span class="el-icon-link"></span> {{ scope.row.ledger.name }}
                        </el-button>
                    </template>
                </el-table-column>

                <el-table-column width="200" label="Account" prop="ledger.account.name" sortable>
                    <template slot-scope="scope">
                        <el-button type="text" @click="viewAccount(scope.row)">
                            <span class="el-icon-link"></span>
                            {{ scope.row.ledger.account.name }}
                        </el-button>
                    </template>
                </el-table-column>

                <el-table-column label="Created At">
                    <template slot-scope="scope">
                        {{ longLocalDate(scope.row.created_at) }}
                    </template>
                </el-table-column>

                <el-table-column label="Updated At">
                    <template slot-scope="scope">
                        {{ longLocalDate(scope.row.updated_at) }}
                    </template>
                </el-table-column>

                <el-table-column label="Amount" align="right">
                    <template slot-scope="scope">
                        {{ formatMoney(scope.row.amount) }}
                    </template>
                </el-table-column>
                
                <el-table-column label="Actions" align="center">
                    <template slot-scope="scope">
                        <el-button
                            type="primary"
                            size="mini"
                            icon="el-icon-edit"
                            @click="editEntry(scope.row)"
                            style="margin-left: 0px;"
                        />

                        <confirm @yes="deleteEntry(scope.row)">
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
                <el-form-item label="Account" :label-width="formLabelWidth">
                    <el-select v-model="form.account_id">
                        <el-option
                            label="Create Account"
                            :value="() => $router.push({ name: 'accounts' })"
                        />
                        
                        <el-option
                            v-for="account in accounts"
                            :label="account.name"
                            :value="account.id"
                            :key="account.id"
                        />
                    </el-select>
                    <error :error="errors.get('account_id')" />
                </el-form-item>

                <el-form-item label="Ledger" :label-width="formLabelWidth">
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

                <el-form-item label="Date" :label-width="formLabelWidth">
                    <el-date-picker
                        name="datetime"
                        type="date"
                        v-model="form.created_at"
                        placeholder="Select date and time"
                        value-format="yyyy-MM-dd HH:mm:ss"
                        format="dd-MM-yyyy"
                        style="width: 100%;"
                    ></el-date-picker>
                </el-form-item>

                <el-form-item label="Title" :label-width="formLabelWidth">
                    <el-input v-model="form.title" autocomplete="off"></el-input>
                    <error :error="errors.get('title')" />
                </el-form-item>
                
                <el-form-item label="Amount" :label-width="formLabelWidth">
                    <el-input
                        autocomplete="off"
                        v-model="form.amount"
                        placeholder="00.00"
                        style="direction: rtl;"
                    ></el-input>
                    <error :error="errors.get('amount')" />
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
        created_at: '',
        title: '',
        amount: '',
        note: '',
        account_id: '',
        ledger_id: ''
    };

    export default {
        name: 'Entries',
        components: { Pagination, Confirm, Error },
        data() {
            return {
                display: 'card',
                account: null,
                search: null,
                accounts: [],
                ledgers: [],
                entries: [],
                pagination: {
                    total: 0,
                    per_page: 9,
                    current_page: 1
                },
                dialogVisible: false,
                formLabelWidth: '120px',
                form: { ...model },
                saving: false,
                errors: new Errors(),
                dialogTitle: 'Create Entry'
            };
        },
        methods: {
            fetch() {
                const data = {
                    search: this.search,
                    account: this.account,
                    page: this.pagination.current_page
                };

                this.$get('entries', data).then(response => {
                    this.entries = response.entries.data;
                    window.alphaAdmin.total = response.total;
                    this.pagination.total = response.entries.total;
                    window.alphaAdmin.firstEntry = response.first;
                    window.alphaAdmin.lastEntry = response.last;
                });
            },
            save() {
                let url = 'entries';
                if (this.form.id) {
                    url = `entries/${this.form.id}`;
                }

                this.saving = true;
                this.$post(url, { ...this.form }).then(response => {
                    this.close();
                    this.fetch();
                    this.$success('Entry Saved Successfully.');
                }).fail(error => {
                    this.errors.record(error);
                }).always(() => {
                    this.saving = false;
                });
            },
            deleteEntry(entry) {
                const url = `entries/${entry.id}`;
                this.$del(url).then(response => {
                    this.fetch();
                    this.$success('Entry Deleted Successfully.');
                });
            },
            close() {
                this.dialogVisible = false;
            },
            createEntry() {
                this.errors.clear();
                this.form = { ...model };
                this.dialogVisible = true;
            },
            editEntry(entry) {
                this.errors.clear();
                this.dialogTitle = 'Edit Entry';
                this.form = { ...entry };
                this.form.account_id = entry.ledger.account_id;
                this.dialogVisible = true;  
            },
            viewEntry(entry) {
                this.$router.push({
                    name: 'entry',
                    query: {
                        id: entry.id
                    }
                });
            },
            viewLedger(entry) {
                this.$router.push({
                    name: 'ledger',
                    query: {
                        id: entry.ledger.id
                    }
                });
            },
            viewAccount(entry) {
                this.$router.push({
                    name: 'account',
                    query: {
                        id: entry.ledger.account.id
                    }
                });
            },
            pageChanged() {
                this.$router.push({
                    name: 'entries',
                    query: {
                        search: this.search,
                        account: this.account,
                        page: this.pagination.current_page
                    }
                });
            },
            filterByAccount(accountId) {
                if (this.account === accountId) return;
                this.account = accountId;
                this.pageChanged();
            },
            fetchAccounts() {
                this.$get('accounts', { all: true }).then(response => {
                    this.accounts = response.accounts;
                });
            }
        },
        computed: {
            filteredAccount() {
                const account = this.accounts.find(a => a.id === this.account);
                return account?.name || 'All';
            }
        },
        watch: {
            'form.account_id': function(accountId) {
                if (!this.form.id) {
                    this.form.ledger_id = null;
                }

                this.$get('ledgers', { account_id: accountId }).then(response => {
                    this.ledgers = response;
                });
            }
        },
        created() {
            this.fetchAccounts();

            const page = Number(this.$route.query.page);
            this.pagination.current_page = page || 1;
            this.search = this.$route.query.search;
            this.account = this.$route.query.account;
            this.fetch();

            this.$bus.$emit('search.sync', this.search);
            this.$bus.$on('search', query => {
                this.search = query;
                this.fetch();
            });
        }
    };
</script>
