<template>
  <div class="base-card flex flex-col">
    <div
      class="flex items-center justify-between border-b py-2 px-4 text-lg font-semibold tracking-wide text-purple-600 lg:text-xl"
    >
      <Link :href="route('notice_board.index')" class="underline">{{
        props.title
      }}</Link>
    </div>
    <div class="flex flex-col gap-8 overflow-y-auto p-4 text-sm lg:text-base">
      <div
        v-for="(firstItem, firstIndex) in messages"
        :key="firstIndex"
        class="flex flex-col gap-2"
      >
        <p class="font-semibold">{{ firstIndex }}</p>
        <ol class="relative ml-3 flex flex-col gap-8 border-l border-gray-200">
          <li
            v-for="(secondItem, secondIndex) in firstItem"
            :key="secondIndex"
            class="ml-4 flex flex-col"
          >
            <div
              class="absolute -left-1.5 h-3 w-3 rounded-full border-4 border-fuchsia-600 bg-white"
            ></div>
            <div class="flex flex-col gap-2">
              <time
                class="mb-1 text-sm font-semibold leading-none text-gray-500"
              >
                {{ secondItem.time_string }}
              </time>
              <button
                @click="openTimelineMessageModal(secondItem.message)"
                class="rounded-lg border p-2 text-left"
              >
                {{ secondItem.message.substring(0, 200) + ' ....' }}
              </button>
            </div>
          </li>
        </ol>
      </div>
      <p class="p-4" v-if="!messages || messages.length === 0">
        No notice board messages found...
      </p>
    </div>
    <!-- Timeline message modal -->
    <Modal
      :show="shouldOpenTimelineMessageModal"
      @closeModal="shouldOpenTimelineMessageModal = false"
    >
      <div class="mt-2">
        <div class="overflow-auto whitespace-pre-wrap">
          {{ messageToDisplayInModal }}
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  title: String,
  messages: Object,
});

const messageToDisplayInModal = ref('');
const shouldOpenTimelineMessageModal = ref(false);

function openTimelineMessageModal(message) {
  messageToDisplayInModal.value = message;
  shouldOpenTimelineMessageModal.value = true;
}
</script>
