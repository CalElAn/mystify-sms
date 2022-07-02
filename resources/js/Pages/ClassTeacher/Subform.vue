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
      readonly
      class="custom-input w-full"
      type="text"
      v-model="user.name"
    />
    <select
      :disabled="!adding"
      class="custom-select w-full"
      v-model="form.class_id"
    >
      <option v-if="adding" value="" selected disabled>
        - select a class -
      </option>
      <option :value="item.id" v-for="item in classes" :key="item">
        {{ item.name_and_suffix }}
      </option>
    </select>
    <select
      disabled
      class="custom-select w-full"
      v-model="form.academic_year_id"
    >
      <option v-if="adding" value="" selected disabled>
        - select an academic year -
      </option>
      <option :value="item.id" v-for="item in academicYears" :key="item">
        {{ item.name }}
      </option>
    </select>

    <FormValidationErrors :errors="form.errors" />
    <div class="mr-4 flex justify-end gap-3 sm:col-span-3">
      <template v-if="adding">
        <SubformButton @click="store()" :disabled="form.processing">
          {{ form.processing ? 'Adding...' : 'Add' }}
        </SubformButton>
        <SubformButton @click="$emit('cancelAdd')"> Cancel </SubformButton>
      </template>
      <template v-if="!adding">
        <SubformButton @click="destroy()"> Delete </SubformButton>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import Swal from 'sweetalert2';

import FormValidationErrors from '@/Components/FormValidationErrors.vue';
import SubformButton from '@/Components/SubformButton.vue';
import { deleteConfirmationDialog } from '@/Components/swal.js';

const user = computed(() => usePage().props.value.auth.user);

const props = defineProps({
  classTeacherData: Object,
  classes: Array,
  academicYears: Array,
});
const form = useForm({
  class_id: props.classTeacherData.class_id,
  academic_year_id: props.classTeacherData.academic_year_id,
});
const adding = ref(props.classTeacherData.adding ?? false);

const emit = defineEmits(['cancelAdd', 'stored']);

function store() {
  form.post(route('class_teacher.store'), {
    onSuccess: () => {
      adding.value = false;
      //this is because when a record is created, the data of this form is not updated with the id from the server.
      //therefore calls to delete throw an error since id is still empty
      //this is because preserveState is set to true (necessary to properly handle validation errors).
      //reloading the page with false preserve state will update the data with fresh data from the server
      Inertia.get(route('class_teacher.form'), {}, { preserveState: false });
      emit('stored');
      Swal.fire({
        icon: 'success',
        title: '',
        text: 'You have been added as a class teacher for this class',
      });
    },
  });
}

function destroy() {
  deleteConfirmationDialog(
    () =>
      form.delete(route('class_teacher.destroy', props.classTeacherData.id), {
        preserveState: false,
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: '',
            text: 'You are no longer a class teacher for this class',
          });
        },
      }),
    'Delete?',
    'You will no longer be a class teacher for this class'
  );
}
</script>
