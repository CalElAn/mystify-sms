<template>
  <!-- dashboard heading component -->
  <section v-if="!user.is_this_user_the_auth_user">
    <div
      class="flex flex-col gap-3 text-lg font-semibold text-gray-500 md:flex-row md:items-center md:text-2xl"
    >
      Parent dashboard:
      <div
        class="flex items-center gap-1 text-lg tracking-wide text-fuchsia-600 md:text-xl"
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
  <section
    class="h1-title mt-4 flex items-center justify-start text-gray-600 sm:mt-6"
  >
    Children
  </section>
  <section class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
    <UserCard v-for="child in children" :user="child"></UserCard>
  </section>
  <section
    v-if="!children || Object.keys(children).length === 0"
    class="flex items-center justify-center"
  >
    <InfoCard
      mainText="You have no children associated with your account"
      linkText="Add child"
      :linkHref="route('add_as_child_request.form')"
    />
  </section>
  <hr class="my-2" />
  <section v-if="user.is_this_user_the_auth_user" class="flex gap-6">
    <!-- Notifications -->
    <NotificationsCard class="h-96 w-full" />
  </section>
</template>

<script setup>
import { defaultDashboardProps } from '@/default_dashboard_props.js';
import { parentActions } from '@/parent_actions.js';
import ActionButtonAndModal from '@/Components/ActionButtonAndModal.vue';
import NotificationsCard from '@/Components/Notifications/Card.vue';
import InfoCard from '@/Components/InfoCard.vue';

defineProps({
  ...defaultDashboardProps,
  children: Object,
});
</script>
