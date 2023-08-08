<script setup>
defineProps({
    user: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <div class="flex items-center justify-center h-screen text-center bg-gradient-to-r from-gray-300 via-gray-400 to-gray-500 text-white text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl">
        <p class="font-serif">Greetings, {{ user.name }}!</p>
    </div>
</template>

<style scoped>

</style>
