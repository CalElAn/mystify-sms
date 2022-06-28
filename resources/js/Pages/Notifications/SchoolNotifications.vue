<template>
  <NavBar :actions="headteacherActions" />
  <section class="flex basis-full items-start justify-center">
    <div class="base-card flex w-11/12 flex-col p-4">
      <!-- <p class="form-title mt-2 text-center">Add academic year</p> -->
      <TransitionGroup name="list">
        <template v-for="item in notificationsData" :key="item.id">
          <div
            v-if="item.type === 'App\\Notifications\\JoinSchoolRequest'"
            class="odd:bg-white even:bg-gray-50"
          >
            <!-- //TODO rmr to check fot type so only school notifs show -->
            <RequestNotification :notification="item" />
          </div>
        </template>
      </TransitionGroup>
    </div>
  </section>
</template>

<script setup>
import NavBar from '@/Components/ActionsNavBar.vue';
import RequestNotification from '@/Components/Notifications/RequestNotification.vue';
import { headteacherActions } from '@/headteacher_actions.js';
import { useNotifications } from '@/notifications.js';

const props = defineProps({
  notifications: Array,
});

const { notifications: notificationsData } = useNotifications();

notificationsData.value = props.notifications;
</script>

<style scoped>
@import '@/../css/subform_transition.css';
</style>
