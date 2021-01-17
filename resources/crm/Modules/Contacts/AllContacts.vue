<template>
    <div class="nerp_section_wrapper">
        <div class="nerp_section_header">
            <h3 class="nerp_section_title">
                All Contacts
            </h3>
        </div>
        <div v-loading="loading" style="min-height: 400px;" class="nerp_section_body">
        </div>
    </div>
</template>

<script type="text/babel">
    export default {
        name: 'crm_contacts',
        data() {
            return {
                loading: true
            }
        }
    }
</script>
