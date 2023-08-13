<script setup>
import {computed, nextTick, reactive, ref} from 'vue';
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const page = usePage();

const users = ref(page.props.users);
const filteredUsers = computed(() => users.value.filter(user => {
    if (user.id === props.user.id) {
        return false;
    }

    for (const room of chatRooms) {
        if (room.users?.some(u => u.id === user.id)) {
            return false;
        }
    }

    return true;
}));

let currentRoom = ref(null);
let chatRooms = reactive(page.props.chatRooms);
let currentMessage = ref('');
let messages = ref({}); // will be an object of arrays, where the keys are room names and the values are the corresponding room's messages
let isExpanded = ref(false); // changed default state to false
let messagesRef = ref(null);

const sendMessage = async () => {
    const trimmedMessage = currentMessage.value.trim();
    if (trimmedMessage === '') {
        return;
    }

    const roomId = currentRoom.value.id;

    const {data} = (await axios.post('/messages', {
        body: trimmedMessage,
        chat_room_id: roomId,
    })).data;

    currentMessage.value = '';
    // add the message to the messages object
    if (!messages.value[roomId]) {
        messages.value[roomId] = [];
    }

    messages.value[roomId].push(data);
    await nextTick();
    messagesRef.value.scrollTop = messagesRef.value.scrollHeight;
};

const onMessageCreated = async ({message}) => {
    const roomId = message.chat_room_id;

    await checkAndLoad(roomId);
    messages.value[roomId].push(message);

    if (messagesRef.value) {
        await nextTick();
        messagesRef.value.scrollTop = messagesRef.value.scrollHeight;
    }
};

Echo.private(`user.${props.user.id}.chat`)
    .listen('.message.created', onMessageCreated);

let toggleChat = () => isExpanded.value = !isExpanded.value;

const switchRoom = async newRoom => {
    await checkAndLoad(newRoom.id);
    currentRoom.value = newRoom;
};

const loadMessages = async (roomId) => {
    const {data} = (await axios.get(`/chat-rooms/${roomId}`)).data;
    messages.value[roomId] = data.messages || [];
};

const checkAndLoad = async (roomId) => !messages.value[roomId] && await loadMessages(roomId);
</script>


<template>
    <div class="chat-widget">
        <div class="header">
            <div>{{ currentRoom ? currentRoom.name : props.user.name }}</div>
            <button @click="toggleChat" class="toggle-btn">
                {{ isExpanded ? '-' : '+' }}
            </button>
        </div>
        <div class="content" v-show="isExpanded">
            <div v-if="!currentRoom" class="room-selector">
                <div class="room-title">Select a chat room:</div>
                <button
                    class="room-btn"
                    v-for="room in chatRooms"
                    @click="switchRoom(room)"
                >
                    {{ room.name }}
                </button>
                <hr/>
                <div class="room-title">Other users:</div>
                <button
                    class="room-btn"
                    v-for="user in filteredUsers"
                >
                    {{ user.name }}
                </button>
            </div>

            <div v-else class="messages" ref="messagesRef">
                <button class="back-btn" @click="currentRoom = null">Go back</button>
                <div
                    v-for="(message, index) in messages[currentRoom?.id]"
                    :key="index"
                    class="message"
                    :class="{'my-message': message.user_id === props.user.id}"
                >
                    <p>{{ message.body }}</p>
                </div>
            </div>

            <div class="footer" v-if="currentRoom">
                <input
                    type="text"
                    v-model="currentMessage"
                    @keydown.enter="sendMessage"
                    placeholder="Type your message..."
                    class="input-field"
                    maxlength="255"
                />
                <button
                    @click="sendMessage"
                    class="send-btn"
                >
                    Send
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>

.chat-widget {
    position: fixed;
    right: 20px;
    bottom: 20px;
    width: 350px;
    max-height: 500px;
    display: flex;
    flex-direction: column;
    background: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, .15);
    border-radius: 10px;
    overflow: hidden;
    font-size: 16px;
    color: #333;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background: #f5f5f5;
    border-bottom: 1px solid #e0e0e0;
    color: #555;
}

.toggle-btn {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
}

.content {
    transition: max-height 0.5s ease-in-out;
}

.messages {
    padding: 20px;
    flex-grow: 1;
    max-height: 350px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.message {
    align-self: flex-start;
    max-width: 70%;
    margin-bottom: 10px;
    padding: 10px;
    border-radius: 10px;
    background: #f0f0f0;
    line-height: 1.5;
}

.message.my-message {
    align-self: flex-end;
    background: #0084ff;
    color: white;
}

.footer {
    border-top: 1px solid #e0e0e0;
}

.input-field {
    width: 100%;
    padding: 10px;
    border: none;
    border-top: 1px solid #e0e0e0;
}

.footer {
    display: flex;
    border-top: 1px solid #e0e0e0;
}

.input-field {
    flex-grow: 1;
    padding: 10px;
    border: none;
    border-right: 1px solid #e0e0e0;
    outline: none;
}

.input-field:focus {
    outline: none;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
}

.send-btn {
    padding: 10px;
    border: none;
    background: #0084ff;
    color: white;
    cursor: pointer;
    transition: background 0.3s;
}

.send-btn:hover {
    background: #005999;
}

.room-selector {
    flex-grow: 1;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    gap: 10px;
    background-color: #f0f0f0; /* slightly dark */
}

.room-title {
    font-weight: bold;
    color: #272727;
    margin-bottom: 10px;
    font-size: 1.2rem;
}

.room-btn {
    padding: 10px;
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

.back-btn {
    margin: 5px;
    padding: 5px 10px;
    border: none;
    background: none;
    color: #0084ff;
    cursor: pointer;
    text-align: left;
    font-size: 0.9rem;
}

/* add this to handle small screen devices */
@media (max-width: 360px) {
    .chat-widget {
        width: 90%;
        right: 5%;
        bottom: 5%;
    }
}
</style>
