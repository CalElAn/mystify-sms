<template>
  <button
    v-bind="$attrs"
    @click="shouldOpenModalContainingListOfActions = true"
    class="inline-flex items-center justify-center gap-2 rounded-lg border border-purple-800 bg-purple-50 px-2 py-1.5 font-semibold tracking-wide text-purple-800 shadow-sm hover:bg-purple-100"
  >
    <ViewListIcon class="h-5 w-5" />
    Actions
  </button>
  <!-- Modal containing list of actions -->
  <Modal
    :show="shouldOpenModalContainingListOfActions"
    :maxWidthClass="'max-w-xs'"
    @closeModal="shouldOpenModalContainingListOfActions = false"
  >
    <div class="flex text-sm md:text-base flex-col gap-3 text-purple-600">
      <Link
        v-for="(item, index) in actions"
        :key="index"
        :href="item.href"
        @click="shouldOpenModalContainingListOfUserTypes = false"
        class="list-of-buttons-in-modal relative inline-block p-2 text-center font-semibold tracking-wide hover:text-purple-500 hover:underline"
      >
        {{ item.label }}
      </Link>
    </div>
  </Modal>
</template>

<script setup>
import { ref } from 'vue';

const shouldOpenModalContainingListOfActions = ref(false);

defineProps({
  actions: Array,
});
</script>
