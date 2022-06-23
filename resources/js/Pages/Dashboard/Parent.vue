<template>
  <!-- dashboard heading component -->
  <section v-if="!user.is_this_user_the_auth_user">
    <div class="flex items-center gap-3 text-2xl font-semibold text-gray-500">
      Parent dashboard:
      <div
        class="flex items-center gap-1 text-xl tracking-wide text-fuchsia-600"
      >
        <ProfilePicture
          :profilePicturePath="user.profile_picture_path"
          widthClass="w-10"
          heightClass="h-10"
        />
        {{ user.name }}
        <a class="ml-3" :href="'tel:' + user.phone_number">
          <PhoneIcon class="h-4 w-4 text-blue-700" />
        </a>
        <a class="ml-1" :href="'mailto:' + user.email">
          <MailIcon class="h-4 w-4 text-blue-700" />
        </a>
      </div>
    </div>
  </section>
  <section v-if="user.is_this_user_the_auth_user">
    <ActionButtonAndModal class="ml-4" :actions="parentActions" />
  </section>
  <section class="h1-title flex items-center justify-start gap-4 text-gray-600">
    Children
  </section>
  <section class="mt-6 mb-auto grid grid-cols-3 gap-6">
    <UserCard v-for="child in children" :user="child"></UserCard>
  </section>
</template>

<script setup>
import { defaultDashboardProps } from '@/default_dashboard_props.js';
import { parentActions } from '@/parent_actions.js';
import ActionButtonAndModal from '@/Components/ActionButtonAndModal.vue';

defineProps({
  ...defaultDashboardProps,
  children: Object,
});
</script>
