<template>
    <div class="dashboard">
        <div class="header">
            <span style="float:left;">Ledgers</span>

            <span
                @click="createLedger"
                style="float:right;color:#46A0FC;cursor:pointer;font-weight:400;"
            >
                <i class="el-icon-plus"></i> Create Ledger
            </span>
        </div>

        <div class="content">
            <el-row :gutter="20" v-if="display=='card'">
                <el-col
                    :span="6"
                    v-for="ledger in ledgers"
                    :key="ledger.id"
                    style="margin-bottom:10px;"
                >
                    <el-card class="box-card">
                        <div slot="header" class="clearfix">
                            <el-button type="text" @click="viewLedger(ledger)" style="padding:0;">
                                {{ ledger.name }}
                            </el-button>

                            <el-button type="text" style="float:right; padding:0;">
                                <el-button
                                    type="primary"
                                    size="mini"
                                    icon="el-icon-edit"
                                    @click="editLedger(ledger)"
                                    style="margin-left: 0px;"
                                />

                                <confirm @yes="deleteLedger(ledger)">
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
                            <!-- <div style="border-bottom:solid 1px #eee;padding: 2px 0;">
                                Total Entries:
                                <span style="float:right;">{{ ledger.entries.length }}</span>
                            </div> -->

                            <div style="border-bottom:solid 1px #eee;padding: 2px 0;">
                                Account:
                                <span style="float:right;">
                                    <el-button
                                        type="text"
                                        style="padding:0;"
                                        @click="viewAccount(ledger.account)"
                                    >{{ ledger.account.name }}</el-button>
                                </span>
                            </div>

                            <div style="border-bottom:solid 1px #eee;padding: 2px 0;">
                                Total Expense:
                                <span style="float:right;">{{ formatMoney(ledger.total) }}</span>
                            </div>

                            <div style="border-bottom:solid 1px #eee;padding: 2px 0;">
                                Created At:
                                <span style="float:right;">
                                    {{ longLocalDate(ledger.created_at) }}
                                </span>
                            </div>
                        </div>
                    </el-card>
                </el-col>
            </el-row>

            <el-table
                v-else
                :data="ledgers"
                style="width: 100%"
            >
                <el-table-column label="Ledger Name" prop="name" sortable>
                    <template slot-scope="scope">
                        <el-button type="text" @click="viewLedger(scope.row)">
                            <span class="el-icon-link"></span> {{ scope.row.name }}
                        </el-button>
                    </template>
                </el-table-column>

                <el-table-column label="Account Name" prop="account.name" sortable>
                    <template slot-scope="scope">
                        <el-button type="text" @click="viewAccount(scope.row.account)">
                            <span class="el-icon-link"></span> {{ scope.row.account.name }}
                        </el-button>
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
                            @click="editLedger(scope.row)"
                            style="margin-left: 0px;"
                        />

                        <confirm @yes="deleteLedger(scope.row)">
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
                            v-for="account in accounts"
                            :label="account.name"
                            :value="account.id"
                            :key="account.id"
                        />
                    </el-select>
                    <error :error="errors.get('account')" />
                </el-form-item>

                <el-form-item label="Ledger Name" :label-width="formLabelWidth">
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
        note: '',
        account_id: ''
    };

    export default {
        name: 'Ledger',
        components: { Pagination, Confirm, Error },
        data() {
            return {
                display: 'card',
                search: null,
                accounts: [],
                ledgers: [],
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
                dialogTitle: 'Create Ledger'
            };
        },
        methods: {
            fetch() {
                const data = {
                    search: this.search,
                    page: this.pagination.current_page
                };

                this.$get('accounts/ledgers', data).then(response => {
                    this.accounts = response.accounts;
                    this.ledgers = response.ledgers.data;
                    window.alphaAdmin.total = response.total;
                    this.pagination.total = response.ledgers.total;
                    window.alphaAdmin.firstEntry = response.first;
                    window.alphaAdmin.lastEntry = response.last;
                });
            },
            save() {
                let url = 'accounts/ledgers';
                if (this.form.id) {
                    url = `${url}/${this.form.id}`;
                }

                this.saving = true;
                this.$post(url, { ...this.form }).then(response => {
                    this.close();
                    this.fetch();
                    this.$success('Ledger Saved Successfully.');
                }).fail(error => {
                    this.errors.record(error);
                }).always(() => {
                    this.saving = false;
                });
            },
            deleteLedger(ledger) {
                const url = `accounts/ledgers/${ledger.id}`;
                this.$del(url).then(response => {
                    this.fetch();
                    this.$success('Ledger Deleted Successfully.');
                });
            },
            close() {
                this.dialogVisible = false;
            },
            createLedger() {
                this.errors.clear();
                this.form = { ...model };
                this.dialogVisible = true;
            },
            editLedger(ledger) {
                this.errors.clear();
                this.dialogTitle = 'Edit Ledger';
                this.form = { ...ledger };
                this.dialogVisible = true;  
            },
            viewLedger(ledger) {
                this.$router.push({
                    name: 'ledger',
                    query: {
                        id: ledger.id
                    }
                });
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
                    name: 'ledgers',
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
