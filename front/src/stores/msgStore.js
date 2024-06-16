import { ref } from 'vue';
import { defineStore } from 'pinia';

export const useMsgStore = defineStore('msgStore', () => {

  const messages = ref([]);

  const deleteMessage = () => {
    messages.value.splice(messages.value.length - 1, 1)
  };

  const addMessage = (message) => {
    messages.value.unshift(message)
  }

  return {
    messages,
    deleteMessage,
    addMessage,
  }
})