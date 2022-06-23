<template>
  <h1 class="h1-title">All {{ userType }}</h1>
  <section
    class="mt-2 flex basis-full flex-col items-center justify-center gap-4"
  >
    <div class="flex w-11/12 items-center justify-start gap-2">
      <SearchIcon class="h-6 w-6 text-gray-500" />
      <input
        v-model="name"
        class="custom-input w-1/3 shadow-sm"
        placeholder="Search..."
        type="text"
      />
      <!-- <Menu as="div" class="base-card relative ml-4 text-base">
        <MenuButton class="flex w-full items-center justify-center p-2">
          <DotsVerticalIcon class="h-5 w-5 text-purple-600" />
        </MenuButton>
        <MenuItemsTransition>
          <MenuItems class="menu-items left-0 z-10 mt-2 w-max origin-top-left">
            <div class="px-1 py-1">
              <MenuItem as="div" v-slot="{ active }">
                <MenuItemButton :active="active">
                  Add {{ userType }}
                </MenuItemButton>
              </MenuItem>
              <MenuItem as="div" v-slot="{ active }">
                <MenuItemButton :active="active">
                  Remove {{ userType }}
                </MenuItemButton>
              </MenuItem>
            </div>
          </MenuItems>
        </MenuItemsTransition>
      </Menu> -->
    </div>
    <div
      class="flex w-full basis-full flex-col items-center justify-start gap-4"
    >
      <UserTable
        class="base-card w-11/12"
        :title="''"
        :users="users.data"
        :userType="userType"
      />
      <Pagination class="flex w-11/12 justify-start" :links="users.links" />
    </div>
  </section>
</template>

<script setup>
import { ref, watch } from 'vue';
import { SearchIcon } from '@heroicons/vue/solid';
import { Inertia } from '@inertiajs/inertia';

import UserTable from '@/Components/Users/Table.vue';
import Pagination from '@/Components/Pagination.vue';

const name = ref(props.nameFilter);

watch(
  name,
  _.throttle((newName) => {
    Inertia.get(
      route('users.index', props.userType),
      { nameFilter: newName },
      { preserveState: true, replace: true }
    );
  }, 150)
);

const props = defineProps({
  users: Object,
  nameFilter: String,
  userType: String,
});
</script>

<style lang="scss" scoped></style>
