<template>
    <div class="nerp_section_wrapper" v-loading="loading">
        <div class="nerp_section_header">
            <h3 class="nerp_section_title">
                CRM Dashboard
            </h3>
        </div>
        <div style="min-height: 400px;" class="nerp_section_body">
            <h5>Will show activity in future</h5>
        </div>
    </div>
</template>

<script type="text/babel">
    export default {
        name: 'Dashboard',
        data() {
            return {
                loading: true
            }
        },
        methods: {

        },
        mounted() {
            this.loading = false;
        }
    }
</script>
