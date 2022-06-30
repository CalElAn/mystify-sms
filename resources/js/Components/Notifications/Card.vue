<template>
  <div class="base-card flex flex-col">
    <div
      class="relative border-b py-2 px-4 lg:text-xl text-lg font-semibold tracking-wide text-purple-600"
    >
      <Link :href="route('notifications.index')" class="underline"
        >Notifications</Link
      >
      <DotIndicator
        v-if="isThereANewNotification"
        class="-top-1 -right-2 h-6 w-6"
      />
    </div>
    <div class="flex flex-col text-sm lg:text-base overflow-y-auto">
      <TransitionGroup name="list">
        <template v-for="item in notifications" :key="item.id">
          <div
            v-if="item.type.includes('Request')"
            class="odd:bg-white even:bg-gray-50"
          >
            <RequestNotification :notification="item" />
          </div>
        </template>
      </TransitionGroup>
    </div>
    <p class="p-4" v-if="!notifications || notifications.length === 0">
      No notifications found...
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';

import RequestNotification from '@/Components/Notifications/RequestNotification.vue';
import DotIndicator from '@/Components/DotIndicator.vue';
import { useNotifications } from '@/notifications.js';

const { isThereANewNotification, notifications } = useNotifications();

notifications.value = computed(() => usePage().props.value.notifications).value;
</script>

<style scoped>
@import '../../../css/subform_transition.css';
</style>
