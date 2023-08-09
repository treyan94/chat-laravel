<script setup>
import { ref, nextTick } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

let currentMessage = ref('');
let messages = ref(['Hello there, ' + props.user.name + '! How can I assist you today?']);
let isExpanded = ref(true);
let messagesRef = ref(null);

const sendMessage = async () => {
    const trimmedMessage = currentMessage.value.trim();
    if (trimmedMessage === '') {
        return;
    }

    messages.value.push(trimmedMessage);
    currentMessage.value = '';
    await nextTick();
    messagesRef.value.scrollTop = messagesRef.value.scrollHeight;
};

let toggleChat = () => { isExpanded.value = !isExpanded.value }
</script>

<template>
    <div class="chat-widget">
        <div class="header">
            <div>{{ props.user.name }}</div>
            <button @click="toggleChat" class="toggle-btn">
                {{ isExpanded ? '-' : '+' }}
            </button>
        </div>
        <div class="content" v-show="isExpanded">
            <div class="messages" ref="messagesRef">
                <div
                    class="message"
                    v-for="(message, index) in messages"
                    :key="index"
                >
                    <p>{{ message }}</p>
                </div>
            </div>

            <div class="footer">
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
    max-width: 70%;
    margin-bottom: 10px;
    padding: 10px;
    border-radius: 10px;
    background: #f0f0f0;
    line-height: 1.5;
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
    box-shadow: 0 0 5px rgba(0,0,0,0.3);
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
</style>
