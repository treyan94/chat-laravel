 <script>
import Layout from "./Layout.vue";

export default {
    layout: Layout,
}
</script>

<script setup>
defineProps({
    user: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <p class="font-serif">Welcome to Page1, {{ user.name }}!</p>
</template>

<style scoped>

</style>
