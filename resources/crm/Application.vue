<template>
    <div class="ninja-accounting-app">
        <div class="ninja-erp-header">
            <navigation />
        </div>
        <div class="ninja-erp-body">
            <router-view></router-view>
        </div>
    </div>
</template>

<script type="text/babel">
    import Navigation from './Layouts/Navigation';

    export default {
        name: 'Application',
        components: {
            Navigation
        },
        computed: {

        }
    }
</script>
