<script setup>
import {ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import AnimatedSpinner from "../Components/AnimatedSpinner.vue";

const users = usePage().props.users
const loading = ref(false)

const loginUsers = async (user) => {
    loading.value = true
    axios.post('/login', {
        email: user.email,
        password: 'password',
    }).then(() => router.visit('/home'))
}
</script>

<template>
    <div class="mx-auto max-w-screen-lg px-4 py-8 sm:px-8">
        <div class="overflow-y-hidden rounded-lg border">
            <div class="overflow-x-auto">
                <table class="w-full" v-if="!loading">
                    <thead>
                    <tr class="bg-blue-600 text-left text-xs font-semibold uppercase tracking-widest text-white">
                        <th class="px-5 py-3">ID</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Full Name</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-500">
                    <tr
                        v-for="user in users"
                        :key="user.id"
                        class="cursor-pointer hover:bg-gray-100 border-gray-200"
                        @click="loginUsers(user)"
                    >
                        <td class="border-b border-gray-200 px-5 py-5 text-sm">
                            <p class="whitespace-no-wrap">{{ user.id }}</p>
                        </td>
                        <td class="border-b border-gray-200 px-5 py-5 text-sm">
                            <div class="flex items-center">
                                <p class="whitespace-no-wrap">{{ user.email }}</p>
                            </div>
                        </td>
                        <td class="border-b border-gray-200 px-5 py-5 text-sm">
                            <div class="flex items-center">
                                <p class="whitespace-no-wrap">{{ user.name }}</p>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div v-else class="flex items-center justify-center p-10">
                    <AnimatedSpinner/>
                </div>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>
