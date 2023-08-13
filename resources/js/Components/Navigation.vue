<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const links = ref([
    {
        url: '/',
        name: 'Home',
    },
    {
        url: '/page1',
        name: 'Page 1',
    },
    {
        url: '/page2',
        name: 'Page 2',
    },
]);
</script>

<template>
    <div class="p-6 bg-white shadow">
        <nav>
            <ul class="flex">
                <li class="mr-6" v-for="(link, index) in links" :key="index">
                    <Link
                        :href="link.url"
                        class="text-blue-500 hover:text-blue-800"
                    >
                        {{ link.name }}
                    </Link>
                </li>
            </ul>
        </nav>
    </div>
</template>

<style scoped>

</style>
