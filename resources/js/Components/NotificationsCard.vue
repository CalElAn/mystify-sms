<template>
  <div class="base-card flex flex-col">
    <div
      class="relative border-b py-2 px-4 text-xl font-semibold tracking-wide text-purple-600"
    >
      <Link :href="route('notifications.index')" class="underline"
        >Notifications</Link
      >
      <DotIndicator
        v-if="isThereANewNotification"
        class="-top-1 -right-2 h-6 w-6"
      />
    </div>
    <div class="flex flex-col overflow-y-auto">
      <TransitionGroup name="list">
        <template v-for="item in notifications" :key="item">
          <div
            v-if="item.type.includes('AddAsChildRequest')"
            class="flex flex-col p-4 odd:bg-white even:bg-gray-50"
          >
            <div class="flex items-center gap-2">
              <ProfilePicture
                :profilePicturePath="item.data.parent.profile_picture_path"
                widthClass="w-12"
                heightClass="h-12"
              />
              <div>
                <span class="font-bold">{{ item.data.parent.name }}</span> wants
                to be added as one of your parents
              </div>
            </div>
            <div class="flex items-center justify-end gap-4">
              <button
                @click="declineRequest(item)"
                class="flex items-center justify-center gap-2 rounded-lg border border-red-600 bg-red-50 px-2 py-1 text-sm text-red-600 shadow-sm hover:bg-red-100"
              >
                <XCircleIcon class="h-5 w-5" />
                Decline
              </button>
              <button
                @click="acceptRequest(item)"
                class="flex items-center justify-center gap-2 rounded-lg border border-green-600 bg-green-50 px-2 py-1 text-sm text-green-600 shadow-sm hover:bg-green-100"
              >
                <CheckCircleIcon class="h-5 w-5" />
                Accept
              </button>
            </div>
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
import { computed, onMounted } from 'vue';
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/outline';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/inertia-vue3';

import DotIndicator from '@/Components/DotIndicator.vue';
import { useNotifications } from '@/notifications.js';

function acceptRequest(item) {
  Inertia.visit(
    route('add_as_child_request.accept_request', {
      parentId: item.data.parent.id,
      notificationId: item.id,
    }),
    {
      method: 'post',
      preserveState: false,
      onSuccess: (page) => (isThereANewNotification.value = false),
    }
  );
}

function declineRequest(item) {
  Inertia.visit(
    route('add_as_child_request.decline_request', {
      notificationId: item.id,
    }),
    {
      method: 'post',
      preserveState: false,
      onSuccess: (page) => (isThereANewNotification.value = false),
    }
  );
}

const { isThereANewNotification, notifications } = useNotifications();

const authUser = computed(() => usePage().props.value.auth.user);

onMounted(() => {
  notifications.value = computed(
    () => usePage().props.value.notifications
  ).value;

  if (authUser.value.user_type === 'student') {
    window.Echo.private(`App.Models.User.${authUser.value.id}`).notification(
      (notification) => {
        isThereANewNotification.value = true;
        notifications.value.unshift({
          ...notification,
          data: notification,
        });
        console.log(notification);
      }
    );
  }
});
</script>

<style scoped>
@import '../../css/subform_transition.css';
</style>
