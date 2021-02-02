<template>
    <div>
        <div class="header">
            <span style="float:left;">
                <span
                    @click="$router.go(-1)"
                    style="color:#46a0fc;cursor:pointer;"
                >
                    <span
                        class="el-icon-back"
                        style="font-size:large;vertical-align:top;"
                    ></span>
                </span>
                Account
                <span style="font-size:12px;font-weight:300">
                    (Total: {{ formatMoney(total) }})
                </span>
            </span>

            <span
                style="float:right;color:#46A0FC;cursor:pointer;font-weight:400;"
                @click="createLedger"
            >
                <i class="el-icon-plus"></i> Add Ledger
            </span>
        </div>

        <div class="content" v-if="account">
            <el-row style="margin-bottom:10px;">
                <el-col :span="3">
                    <span style="font-size:120px;" class="el-icon-folder-opened"></span>
                </el-col>

                <el-col :span="21">
                    <el-row>
                        <el-col>
                            <h2>{{ account.name }}</h2>
                            <p>Created At: {{ longLocalDate(account.created_at) }}</p>
                            <p>Updated At: {{ longLocalDate(account.updated_at) }}</p>
                        </el-col>
                    </el-row>
                </el-col>
            </el-row>

            <el-row>
                <el-col v-if="account.note">
                    <div class="header">Note</div>
                    <div class="content">
                        {{ account.note }}
                    </div>
                </el-col>
            </el-row>

            <el-row>
                <el-col>
                    <div class="header">Ledgers</div>

                    <div class="content">
                        <el-table :data="accountLedgers">
                            <el-table-column label="Name">
                                <template slot-scope="scope">
                                    <el-button type="text" @click="viewLedger(scope.row)">
                                        <span class="el-icon-link"></span> {{ scope.row.name }}
                                    </el-button>
                                </template>
                            </el-table-column>

                            <el-table-column label="Number of Entries" align="center">
                                <template slot-scope="scope">
                                    {{ scope.row.entries.length }}
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
                </el-col>
            </el-row>
        </div>

        <el-dialog :title="dialogTitle" :visible.sync="dialogVisible">
            <el-form :model="form" label-position="left">
                <el-form-item label="Name" :label-width="formLabelWidth">
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
                account: null,
                saving: false,
                form: { ...model },
                errors: new Errors(),
                dialogVisible: false,
                dialogTitle: 'Add Ledger',
                formLabelWidth: '120px',
                pagination: {
                    total: 0,
                    per_page: 8,
                    current_page: 1
                },
                total: 0
            };
        },
        methods: {
            fetch(search = '') {
                this.$get(`accounts/${this.$route.query.id}`, {
                    search: search,
                    page: this.pagination.current_page
                }).then(response => {
                    this.account = response.account;
                    this.total = response.account.total;
                    this.pagination.total = response.account.ledgers.total;
                    window.alphaAdmin.firstEntry = response.first;
                    window.alphaAdmin.lastEntry = response.last;
                });
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
            save() {
                let url = 'accounts/ledgers';
                if (this.form.id) {
                    url = `${url}/${this.form.id}`;
                }

                this.saving = true;
                this.form.account_id = this.account.id;
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
            viewLedger(ledger) {
                this.$router.push({
                    name: 'ledger',
                    query: {
                        id: ledger.id
                    }
                });
            },
            deleteLedger(ledger) {
                const url = `accounts/ledgers/${ledger.id}`;
                this.$del(url).then(response => {
                    this.fetch();
                    this.$success('Ledger Deleted Successfully.');
                });
            },
            pageChanged() {
                this.fetch();
            },
            close() {
                this.dialogVisible = false;
            },
            search() {

            }
        },
        computed: {
            accountLedgers() {
                return this.account.ledgers ? this.account.ledgers.data : [];
            }
        },
        created() {
            this.fetch();
            this.$bus.$on('search', query => {
                this.fetch(query);
            });
        }
    };
</script>
