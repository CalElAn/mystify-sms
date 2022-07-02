<template>
  <div
    class="grid grid-cols-1 gap-y-3 p-2 odd:bg-white even:bg-gray-100 sm:grid-cols-3 sm:gap-y-4 sm:gap-x-4 xl:gap-x-12"
    :class="[
      {
        'my-4 border-purple-400 ring-4 ring-purple-300 ring-opacity-50': adding,
      },
    ]"
  >
    <input
      :readonly="!editing && !adding"
      placeholder="name, example 'First term'"
      class="custom-input w-full"
      type="text"
      v-model="form.name"
    />
    <input
      :readonly="!editing && !adding"
      class="custom-input w-full"
      type="date"
      v-model="form.start_date"
    />
    <input
      :readonly="!editing && !adding"
      class="custom-input w-full"
      type="date"
      v-model="form.end_date"
    />
    <FormValidationErrors :errors="form.errors" />
    <div class="mr-4 flex justify-end gap-3 sm:col-span-3">
      <template v-if="adding">
        <SubformButton @click="store()" :disabled="form.processing">
          {{ form.processing ? 'Adding...' : 'Add' }}
        </SubformButton>
        <SubformButton @click="$emit('cancelAdd')"> Cancel </SubformButton>
      </template>
      <template v-if="editing">
        <SubformButton @click="update()"> Save </SubformButton>
        <SubformButton @click="destroy()"> Delete </SubformButton>
        <SubformButton @click="editing = false"> Cancel </SubformButton>
      </template>
      <!-- <SubformButton v-if="!editing && !adding" @click="editing = true">
        Edit
      </SubformButton> -->
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';

import FormValidationErrors from '@/Components/FormValidationErrors.vue';
import SubformButton from '@/Components/SubformButton.vue';
import { toast, deleteConfirmationDialog } from '@/Components/swal.js';

const props = defineProps({
  term: Object,
});
const form = useForm({
  name: props.term.name,
  start_date: props.term.start_date,
  end_date: props.term.end_date,
});
const adding = ref(props.term.adding ?? false);
const editing = ref(false);

const emit = defineEmits(['cancelAdd', 'stored', 'destroyed']);

function store() {
  form.post(route('academic_years.terms.store', props.term.academic_year_id), {
    onSuccess: () => {
      adding.value = false;
      emit('stored');
      toast.fire({ title: `Added!` });
    },
  });
}

function update() {
  form.patch(route('terms.update', props.term.id), {
    onSuccess: () => {
      toast.fire({ title: `Saved!` });
    },
  });
}

function destroy() {
  deleteConfirmationDialog(() =>
    form.delete(route('terms.destroy', props.term.id), {
      onSuccess: () => {
        emit('destroyed');
        toast.fire({ title: `Deleted!` });
      },
    })
  );
}
</script>
