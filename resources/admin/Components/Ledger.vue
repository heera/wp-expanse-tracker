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
                Ledger
                <span style="font-size:12px;font-weight:300">
                    (Total: {{ formatMoney(total) }})
                </span>
            </span>

            <span
                @click="createEntry"
                style="float:right;color:#46A0FC;cursor:pointer;font-weight:400;"
            >
                <i class="el-icon-plus"></i> Add Entry
            </span>
        </div>

        <div class="content" v-if="ledger">
            <el-row style="margin-bottom:10px;">
                <el-col :span="3">
                    <span style="font-size:120px;" class="el-icon-document"></span>
                </el-col>

                <el-col :span="21">
                    <el-row>
                        <el-col>
                            <h2>{{ ledger.name }}</h2>
                            <p>Created At: {{ longLocalDate(ledger.created_at) }}</p>
                            <p>Updated At: {{ longLocalDate(ledger.updated_at) }}</p>
                        </el-col>
                    </el-row>
                </el-col>
            </el-row>

            <el-row>
                <el-col v-if="ledger.note">
                    <div class="header">Note</div>
                    <div class="content">
                        {{ ledger.note }}
                    </div>
                </el-col>
            </el-row>

            <el-row>
                <el-col>
                    <div class="header">Entries</div>

                    <div class="content">
                        <el-table :data="ledger.entries.data">
                            <el-table-column label="Title">
                                <template slot-scope="scope">
                                    <el-button type="text" @click="viewEntry(scope.row)">
                                        <span class="el-icon-link"></span> {{ scope.row.title }}
                                    </el-button>
                                </template>
                            </el-table-column>

                            <el-table-column label="Created At">
                                <template slot-scope="scope">
                                    {{ longLocalDate(scope.row.created_at) }}
                                </template>
                            </el-table-column>

                            <el-table-column label="Amount" align="right">
                                <template slot-scope="scope">
                                    {{ scope.row.amount }}
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
                </el-col>
            </el-row>
        </div>

        <el-dialog :title="dialogTitle" :visible.sync="dialogVisible">
            <el-form :model="form" label-position="left">
                <el-form-item label="Date & Time" :label-width="formLabelWidth">
                    <el-date-picker
                        type="date"
                        v-model="form.created_at"
                        placeholder="Select date"
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
        title: '',
        amount: null,
        created_at: null,
        note: ''
    };

    export default {
        name: 'Ledger',
        components: { Pagination, Confirm, Error },
        data() {
            return {
                ledger: null,
                saving: false,
                form: { ...model },
                errors: new Errors(),
                dialogVisible: false,
                dialogTitle: 'Add Entry',
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
                this.$get(`accounts/ledgers/${this.$route.query.id}`, {
                    search: search,
                    page: this.pagination.current_page
                }).then(response => {
                    this.ledger = response.ledger;
                    this.total = response.ledger.total;
                    this.entries = response.ledger.entries.data;
                    this.pagination.total = response.ledger.entries.total;

                    window.alphaAdmin.total = response.total;
                    window.alphaAdmin.firstEntry = response.first;
                    window.alphaAdmin.lastEntry = response.last;
                });
            },
            createEntry() {
                this.errors.clear();
                this.form = { ...model };
                this.dialogVisible = true;
            },
            editEntry(ledger) {
                this.errors.clear();
                this.dialogTitle = 'Edit Entry';
                this.form = { ...ledger };
                this.form = { ...ledger };
                this.dialogVisible = true;  
            },
            save() {
                let url = 'accounts/ledgers/entries';
                if (this.form.id) {
                    url = `${url}/${this.form.id}`;
                }

                this.saving = true;
                this.form.ledger_id = this.ledger.id;
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
            viewEntry(entry) {
                this.$router.push({
                    name: 'entry',
                    query: {
                        id: entry.id
                    }
                });
            },
            deleteEntry(entry) {
                const url = `accounts/ledgers/entries/${entry.id}`;
                this.$del(url).then(response => {
                    this.fetch();
                    this.$success('Entry Deleted Successfully.');
                });
            },
            pageChanged() {
                this.fetch();
            },
            close() {
                this.dialogVisible = false;
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
