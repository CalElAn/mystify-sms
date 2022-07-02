<template>
  <section class="flex basis-full items-start justify-center">
    <div class="base-card flex w-11/12 flex-col gap-2 p-4">
      <!-- <p class="form-title mt-2 text-center">Add academic year</p> -->
      <TransitionGroup name="list">
        <template v-for="item in notificationsData" :key="item.id">
          <div
            v-if="item.type === 'App\\Notifications\\JoinSchoolRequest'"
            class="odd:bg-white even:bg-gray-50"
          >
            <RequestNotification :notification="item" />
          </div>
        </template>
      </TransitionGroup>
    </div>
  </section>
</template>

<script setup>
import RequestNotification from '@/Components/Notifications/RequestNotification.vue';
import { useNotifications } from '@/notifications.js';

const props = defineProps({
  notifications: Array,
});

const { notifications: notificationsData } = useNotifications();

notificationsData.value = props.notifications;
</script>

<script>
import HeadteacherActions from '@/Layouts/HeadteacherActions.vue';
import Layout from '@/Layouts/Layout.vue';

export default {
  layout: [Layout, HeadteacherActions],
};
</script>

<style scoped>
@import '@/../css/subform_transition.css';
</style>
