<script setup>
import {computed, ref} from "vue";
import {useToast} from "vue-toastification";
import {onClickOutside} from "@vueuse/core";

const toast = useToast();

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
    room: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['addUser']);

const dropdown = ref(null);
onClickOutside(dropdown, () => openUserDropdown.value = false);
const openUserDropdown = ref(false);

// filter out the users that are already in the room
const filteredUsers = computed(() => props.users.filter(user => {
    return !props.room.users.some(u => u.id === user.id);
}));

const addUserToRoom = async (user) => {
    await axios.post(`/chat-rooms/${props.room.id}/users`, {
        users: [user.id],
    }).then(() => {
        // handle success
        emit('addUser', user);
        openUserDropdown.value = false;
    }).catch(error => {
        toast.error(error.response.data.message);
    });
};
</script>

<template>
    <div class="relative">
        <button
            class="add-user-btn text-blue-500 rounded text-sm font-medium"
            @click="openUserDropdown = !openUserDropdown"
        >
            +
        </button>
        <ul
            ref="dropdown"
            v-if="openUserDropdown"
            class="user-dropdown absolute right-0 mt-2 w-48 bg-white border rounded shadow-xl z-10 overflow-y-auto max-h-48"
        >
            <li
                v-for="user in filteredUsers"
                :key="user.id"
                class="border-b border-gray-100"
            >
                <button
                    class="user-option block px-4 py-2 text-gray-800 hover:bg-blue-500 hover:text-white w-full text-left"
                    @click="addUserToRoom(user)"
                >
                    {{ user.name }}
                </button>
            </li>
        </ul>
    </div>
</template>

<style scoped>

</style>
