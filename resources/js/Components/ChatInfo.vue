<script setup>
import {computed} from "vue";

const props = defineProps({
    room: {
        type: Object,
        required: true,
    },
})

const users = computed(() => props.room.users);
</script>

<template>
    <div>
        <div class="flex flex-col-reverse mt-4 px-4">
            <div
                class="flex items-center mt-1.5 mb-1.5 px-4 py-2 rounded-lg bg-gray-100"
                v-for="user in users"
                :key="user.id"
            >
                <span class="text-sm font-medium">{{ user.name }}</span>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
