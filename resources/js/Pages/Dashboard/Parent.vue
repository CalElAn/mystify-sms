<template>
  <!-- dashboard heading component -->
  <section v-if="shouldShowDashboardHeading">
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
  <section class="h1-title flex items-center justify-start gap-4">
    Children
    <Menu as="div" class="base-card relative text-base">
      <MenuButton class="flex w-full items-center justify-center p-1.5">
        <DotsVerticalIcon class="h-5 w-5 text-purple-600" />
      </MenuButton>
      <MenuItemsTransition>
        <MenuItems class="menu-items left-0 z-10 mt-2 w-max origin-top-left">
          <div class="px-1 py-1">
            <MenuItem as="div" v-slot="{ active }">
              <MenuItemButton :active="active"> Add child </MenuItemButton>
            </MenuItem>
            <MenuItem as="div" v-slot="{ active }">
              <MenuItemButton :active="active"> Remove child </MenuItemButton>
            </MenuItem>
          </div>
        </MenuItems>
      </MenuItemsTransition>
    </Menu>
  </section>
  <section class="mt-6 mb-auto grid grid-cols-3 gap-6">
    <UserCard v-for="child in children" :user="child"></UserCard>
  </section>
</template>

<script setup>
import { defaultDashboardProps } from '@/default_dashboard_props.js';

defineProps({
  ...defaultDashboardProps,
  children: Object,
});
</script>
