<template>
  <NavBar :actions="headteacherActions" />
  <section class="flex items-center justify-center">
    <div class="base-card w-11/12 py-4 px-6">
      <p
        class="mt-2 text-center text-xl font-semibold tracking-wide text-gray-600"
      >
        Add message to notice board
      </p>
      <div class="mt-12">
        <label class="ml-4 font-semibold tracking-wide text-gray-600"
          >Current term:</label
        >
        <input
          readonly
          class="custom-input ml-4 w-1/3 bg-slate-100"
          :value="currentTerm.formatted_name"
          type="text"
        />
      </div>
      <div class="mt-6 px-4">
        <label class="block font-semibold tracking-wide text-gray-600">
          Message:
        </label>
        <textarea
          @input="autoGrowTextarea"
          v-model="form.message"
          rows="2"
          class="sm mt-1 block w-11/12 rounded-md border border-gray-300 text-xs shadow-sm focus:border-indigo-400 focus:ring-indigo-400 sm:text-sm"
        ></textarea>
        <FormValidationErrors :errors="form.errors" />
        <div class="mt-4 flex justify-center">
          <button
            @click="store()"
            class="flex w-5/6 items-center justify-center gap-2 rounded-md bg-purple-400 p-1 text-lg font-semibold tracking-wide text-white shadow-sm hover:bg-purple-500"
          >
            <PlusCircleIcon class="h-6 w-6" />
            Add
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';

import { PlusCircleIcon } from '@heroicons/vue/outline';

import { autoGrowTextarea } from '@/helpers';
import NavBar from '@/Components/ActionsNavBar.vue';
import FormValidationErrors from '@/Components/FormValidationErrors.vue';
import { headteacherActions } from '@/headteacher_actions.js';

const props = defineProps({
  currentTerm: Object,
});

const form = useForm({
  message: '',
  term_id: props.currentTerm.id,
});

function store() {
  form.post(route('notice_board.store'), {
    onSuccess: () => {
      //TODO notify added
    },
  });
}
</script>
