<template>
  <div
    class="grid grid-cols-3 gap-y-4 gap-x-12 p-2 odd:bg-white even:bg-gray-100"
    :class="[
      {
        'my-4 border-purple-400 ring-4 ring-purple-300 ring-opacity-50': adding,
      },
    ]"
  >
    <input
      :readonly="!editing && !adding"
      placeholder="name, example '2021/2022'"
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
    <div class="col-span-3 mr-4 flex justify-end gap-3">
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
import { Inertia } from '@inertiajs/inertia';

import FormValidationErrors from '@/Components/FormValidationErrors.vue';
import SubformButton from '@/Components/SubformButton.vue';

const props = defineProps({
  academicYear: Object,
});
const form = useForm({
  name: props.academicYear.name,
  start_date: props.academicYear.start_date,
  end_date: props.academicYear.end_date,
});
const adding = ref(props.academicYear.adding ?? false);
const editing = ref(false);

const emit = defineEmits(['cancelAdd', 'stored']);

function store() {
  form.post(route('academic_years.store'), {
    onSuccess: () => {
      adding.value = false;
      //this is because when an academic year is created, the data of this form is not updated with the id from the server.
      //therefore calls to delete throw an error since id is still empty
      //this is because preserveState is set to true (necessary to properly handle validation errors).
      //reloading the page with false preserve state will update the data with fresh data from the server
      Inertia.get(route('academic_years.form'), {}, { preserveState: false });
      emit('stored');
      //TODO notify added
    },
  });
}

function update() {
  form.patch(route('academic_years.update', props.academicYear.id), {
    onSuccess: () => {
      //TODO notify saved
    },
  });
}

function destroy() {
  //TODO notify confirmation
  form.delete(route('academic_years.destroy', props.academicYear.id), {
    preserveState: false,
    onSuccess: () => {
      // emit('destroyed')
      //TODO notify deleted
    },
  });
}
</script>
