import { ref } from 'vue';

// global state, created in module scope
const isThereANewNotification = ref(false);
const notifications = ref([]);

export function useNotifications() {
  return { isThereANewNotification, notifications };
}
