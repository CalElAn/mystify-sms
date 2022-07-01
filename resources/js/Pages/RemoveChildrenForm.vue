<template>
  <Head title="Remove child"></Head>
  <NavBar :actions="parentActions" />
  <section class="flex basis-full items-start justify-center">
    <div class="base-card flex flex-col p-4">
      <p class="form-title my-2 text-center">Children</p>
      <div
        v-for="child in children"
        :key="child"
        class="flex w-60 sm:w-80 justify-between p-2 odd:bg-white even:bg-gray-50"
      >
        <div class="flex items-center gap-2">
          <ProfilePicture
            :profilePicturePath="child.profile_picture_path"
            widthClass="w-10"
            heightClass="h-10"
          />
          <Link class="tda" :href="route('dashboard', { userId: child.id })">
            {{ child.name }}
          </Link>
        </div>
        <button @click="removeChild(child.pivot.id)" class="p-2">
          <TrashIcon class="h-5 w-5 text-red-500" />
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';
import { TrashIcon } from '@heroicons/vue/outline';

import NavBar from '@/Components/ActionsNavBar.vue';
import { parentActions } from '@/parent_actions.js';

defineProps({
  children: Array,
});

function removeChild(id) {
  //TODO confirm delete
  Inertia.visit(route('parent_student.destroy', id), {
    method: 'delete',
    preserveState: false,
  });
}
</script>
