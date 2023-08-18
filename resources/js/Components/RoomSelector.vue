<script setup>
defineProps({
    chatRooms: {
        type: Array,
        required: true,
    },
    users: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['switchRoom', 'createRoom']);
</script>

<template>
    <div class="room-selector">
        <div class="room-title">Select a chat room:</div>
        <button
            v-for="room in chatRooms"
            :key="room.id"
            class="room-btn"
            @click="emit('switchRoom', room)"
        >
            {{ room.name }}
        </button>
        <template v-if="users.length">
            <hr/>
            <div class="room-title">Other users:</div>
            <div class="users-container">
                <button
                    class="room-btn"
                    v-for="user in users"
                    :key="user.id"
                    @click="emit('createRoom', user)"
                >
                    {{ user.name }}
                </button>
            </div>
        </template>
    </div>
</template>

<style scoped>
.room-selector {
    padding: 20px;
    flex-direction: column;
    justify-content: flex-start;
    gap: 10px;
    background-color: #f0f0f0; /* slightly dark */
    max-height: 350px;
    overflow-y: auto;
}

.room-title {
    font-weight: bold;
    color: #272727;
    margin-bottom: 10px;
    font-size: 1.2rem;
}

.room-btn {
    padding: 10px;
    margin-bottom: 5px;
    border-radius: 5px; /* rounded edges */
    border: none;
    background: #0084ff;
    color: white;
    cursor: pointer;
    transition: background 0.3s;
    text-align: left;
    width: 100%;
    box-sizing: border-box;
    text-overflow: ellipsis;
    overflow: hidden;
}

.room-btn:hover {
    background: #005999;
}

.room-btn:focus {
    outline: none;
    box-shadow: 0 0 1px 2px rgba(0, 0, 0, 0.1);
}

.users-container {
    display: block;
    overflow-y: auto;
    max-height: 200px;
    margin-bottom: 10px;
}

</style>
