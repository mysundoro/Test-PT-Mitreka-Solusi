<template>
    <v-footer class="bg-red-lighten-1 text-center d-flex flex-column">
        <div>
            &copy; {{ new Date().getFullYear() }} - <strong>{{ $page.props.configuration.application_name.value }}</strong>
        </div>
    </v-footer>
</template>

<script setup>

</script>