<script setup>

import ChatWidget from "../Components/ChatWidget.vue";
import Navigation from "../Components/Navigation.vue";

defineProps({
    user: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <div class="h-screen">
        <Navigation/>

        <div class="content">
            <ChatWidget :user="user"/>
            <slot/>
        </div>
    </div>
</template>

<style scoped>
.content {
    @apply flex items-center justify-center h-[calc(100vh-74px)] text-center bg-gradient-to-r from-blue-300 via-blue-400 to-blue-500 text-white text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl;
}
</style>
