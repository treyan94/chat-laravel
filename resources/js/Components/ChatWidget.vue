<script setup>
import {computed, nextTick, reactive, ref, onBeforeUnmount} from 'vue';
import {useToast} from "vue-toastification";
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const toast = useToast();
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
let messages = ref({});
let isExpanded = ref(false);

let messagesRef = ref(null);
const scrollToBottom = async () => {
    await nextTick();
    if (messagesRef.value) {
        messagesRef.value.scrollTop = messagesRef.value.scrollHeight;
    }
};

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
    await scrollToBottom();
};

const onMessageCreated = async ({message}) => {
    const { chat_room_id: roomId } = message;

    await checkAndLoad(roomId);
    messages.value[roomId].push(message);

    const room = chatRooms.find(room => room.id === roomId);
    if (room && room.id !== currentRoom.value.id) {
        newMessageToast(message, room);
    }

    await scrollToBottom();
};

const newMessageToast = (message, room) => {
    toast.info(`New message in ${room.name}`, {
        onClick: async () => {
            await switchRoom(room);
            isExpanded.value = true;
            await scrollToBottom();
        },
    });
};

Echo.private(`user.${props.user.id}.chat`)
    .listen('.message.created', onMessageCreated);

onBeforeUnmount(() => {
    Echo.leave(`user.${props.user.id}.chat`);
});

let toggleChat = () => isExpanded.value = !isExpanded.value;

const switchRoom = async newRoom => {
    await checkAndLoad(newRoom.id);
    currentRoom.value = newRoom;
    currentMessage.value = '';
    await scrollToBottom();
};

const loadMessages = async (roomId) => {
    const {data} = (await axios.get(`/chat-rooms/${roomId}`)).data;
    messages.value[roomId] = data.messages || [];
};

const checkAndLoad = async (roomId) => !messages.value[roomId] && await loadMessages(roomId);

const createRoom = async (user) => {
    const {data} = (await axios.post('/chat-rooms', {
        user_id: user.id,
        name: `${user.name} & ${props.user.name}`,
    })).data;

    page.props.chatRooms.push(data);
    await switchRoom(data);
};
</script>


<template>
    <div class="chat-widget">
        <div class="header">
            <div v-if="!currentRoom">{{ props.user.name }}</div>
            <div v-else class="room-info">
                <button class="back-btn" @click="currentRoom = null">Back</button>
                <span class="text-sm">
                    {{ currentRoom.name }}
                </span>
            </div>
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
                <template v-if="filteredUsers.length">
                    <hr/>
                    <div class="room-title">Other users:</div>
                    <div class="users-container">
                        <button
                            class="room-btn"
                            v-for="user in filteredUsers"
                            @click="createRoom(user)"
                        >
                            {{ user.name }}
                        </button>
                    </div>
                </template>
            </div>

            <div v-else class="messages" ref="messagesRef">
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
    text-align: left;
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
    padding: 20px;
    flex-direction: column;
    justify-content: flex-start;
    gap: 10px;
    background-color: #f0f0f0; /* slightly dark */
    max-height: 350px;
    overflow-y: auto;
}

.users-container {
    display: block;
    overflow-y: auto;
    max-height: 200px;
    margin-bottom: 10px;
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

.room-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.back-btn {
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
