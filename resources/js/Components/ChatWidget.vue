<script setup>
import {nextTick, reactive, ref, onBeforeUnmount} from 'vue';
import {useToast} from "vue-toastification";
import {usePage} from "@inertiajs/vue3";
import AddUserToRoom from "./AddUserToRoom.vue";
import ChatInfo from "./ChatInfo.vue";
import RoomSelector from "./RoomSelector.vue";

// Initializations
const toast = useToast();
const page = usePage();

// Prop Section
const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

// Variable Definitions
const users = ref(page.props.users);
let chatRooms = reactive(page.props.chatRooms);
let messages = ref({});
let messagesRef = ref(null);

const chatState = reactive({
    currentRoom: null,
    currentMessage: '',
    isExpanded: false,
    infoMode: false,
});

// Functions
const scrollToBottom = async () => {
    await nextTick();
    if (messagesRef.value) {
        messagesRef.value.scrollTop = messagesRef.value.scrollHeight;
    }
};

const sendMessage = async () => {
    const trimmedMessage = chatState.currentMessage.trim();
    if (trimmedMessage === '') {
        return;
    }

    const roomId = chatState.currentRoom.id;

    const {data} = (await axios.post('/messages', {
        body: trimmedMessage,
        chat_room_id: roomId,
    })).data;

    chatState.currentMessage = '';
    // add the message to the messages object
    if (!messages.value[roomId]) {
        messages.value[roomId] = [];
    }

    messages.value[roomId].push(data);
    await scrollToBottom();
};

const onMessageCreated = async ({message}) => {
    const {chat_room_id: roomId} = message;

    await checkAndLoad(roomId);
    messages.value[roomId].push(message);

    const room = chatRooms.find(room => room.id === roomId);
    if (room && room.id !== chatState.currentRoom?.id) {
        newMessageToast(message, room);
    }

    await scrollToBottom();
};

const newMessageToast = (message, room) => {
    toast.info(`New message in ${room.name}`, {
        onClick: async () => {
            await switchRoom(room);
            chatState.isExpanded = true;
            await scrollToBottom();
        },
    });
};

const toggleChat = () => chatState.isExpanded = !chatState.isExpanded;
const checkAndLoad = async (roomId) => !messages.value[roomId] && await loadMessages(roomId);

const switchRoom = async newRoom => {
    await checkAndLoad(newRoom.id);
    chatState.currentRoom = newRoom;
    chatState.currentMessage = '';
    await scrollToBottom();
};

const loadMessages = async (roomId) => {
    const {data} = (await axios.get(`/chat-rooms/${roomId}`)).data;
    messages.value[roomId] = data.messages || [];
};

const createRoom = async (user) => {
    const {data} = (await axios.post('/chat-rooms', {
        user_id: user.id,
        name: `${user.name} & ${props.user.name}`,
    })).data;

    page.props.chatRooms.push(data);
    await switchRoom(data);
};

const getUserName = (message) => {
    const room = chatRooms.find(room => room.id === message.chat_room_id);
    if (!room) {
        return '';
    }

    const user = room.users.find(u => u.id === message.user_id);
    return user ? user.name : '';
};

const onBackClick = () => {
    if (chatState.infoMode) {
        chatState.infoMode = false;
        return;
    }

    chatState.currentRoom = null;
};


// Event Listener Section
Echo.private(`user.${props.user.id}.chat`).listen('.message.created', onMessageCreated);

// Lifecycle Hooks
onBeforeUnmount(() => Echo.leave(`user.${props.user.id}.chat`));
</script>


<template>
    <div class="chat-widget">
        <div class="header">
            <div v-if="!chatState.currentRoom">{{ props.user.name }}</div>
            <div v-else class="room-info">
                <button class="back-btn mr-1" @click="onBackClick">Back</button>
                <span class="text-xs cursor-pointer" @click="chatState.infoMode = true">
                    {{ chatState.currentRoom.name }}
                </span>
            </div>
            <div class="flex">
                <AddUserToRoom
                    v-if="chatState.currentRoom"
                    class="ml-1 flex-shrink-0"
                    :users="users"
                    :room="chatState.currentRoom"
                    @add-user="chatState.currentRoom?.users.push($event)"
                />
                <button
                    @click="toggleChat"
                    class="toggle-btn ml-1"
                >
                    {{ chatState.isExpanded ? '-' : '+' }}
                </button>
            </div>
        </div>
        <div class="content" v-show="chatState.isExpanded">
            <RoomSelector
                v-if="!chatState.currentRoom"
                :chat-rooms="chatRooms"
                :users="users"
                @switch-room="switchRoom"
                @create-room="createRoom"
            />

            <template v-else>
                <ChatInfo
                    v-if="chatState.infoMode"
                    :room="chatState.currentRoom"
                />
                <div
                    v-else
                    class="messages"
                    ref="messagesRef"
                >
                    <p
                        v-if="!messages[chatState.currentRoom?.id].length"
                        class="text-center"
                    >
                        No messages yet
                    </p>
                    <template v-else>
                        <div
                            v-for="(message, index) in messages[chatState.currentRoom?.id]"
                            :key="index"
                            class="message"
                            :class="{'my-message': message.user_id === props.user.id}"
                        >
                            <div class="user-name">
                                {{ getUserName(message) }}
                            </div>
                            <p>{{ message.body }}</p>
                        </div>
                    </template>
                </div>
            </template>

            <div class="footer" v-if="chatState.currentRoom">
                <input
                    type="text"
                    v-model="chatState.currentMessage"
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
    height: 350px;
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


.user-name {
    font-size: 0.8rem;
    color: #888;
}

.my-message .user-name {
    color: #add8e6; /* light blue color */
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

.room-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.back-btn {
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
