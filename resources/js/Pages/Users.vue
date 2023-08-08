<script setup>
import {onMounted, ref} from "vue";
import {router} from "@inertiajs/vue3";

const users = ref([])
const loading = ref(false)
const loadUsers = async () => {
    loading.value = true
    axios.get('/api/users')
        .then(({data}) => users.value = data)
        .finally(() => loading.value = false)
}

const loginUsers = async (user) => {
    loading.value = true
    axios.post('/login', {
        email: user.email,
        password: 'password',
    }).then(() => router.visit('/home'))
}

onMounted(() => {
    loadUsers()
})
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
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24">
                        <circle class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"></circle>
                        <path class="opacity-75"
                              fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962
                              0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>
