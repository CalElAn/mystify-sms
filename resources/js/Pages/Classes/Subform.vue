<template>
  <div
    class="grid grid-cols-1 gap-y-3 p-2 odd:bg-white even:bg-gray-100 sm:grid-cols-2 sm:gap-y-4 sm:gap-x-12"
    :class="[
      {
        'my-4 border-purple-400 ring-4 ring-purple-300 ring-opacity-50': adding,
      },
    ]"
  >
    <input
      :readonly="!adding"
      placeholder="name, example 'Class 1'"
      class="custom-input w-full read-only:bg-slate-100"
      type="text"
      v-model="form.name"
    />
    <input
      :readonly="!editing && !adding"
      placeholder="group, example 'A'"
      class="custom-input w-full read-only:bg-slate-100"
      type="text"
      v-model="form.suffix"
    />
    <FormValidationErrors :errors="form.errors" />
    <div class="mr-4 flex justify-end gap-3 sm:col-span-2">
      <template v-if="adding">
        <SubformButton @click="store()" :disabled="form.processing">
          {{ form.processing ? 'Adding...' : 'Add' }}
        </SubformButton>
        <SubformButton @click="$emit('cancelAdd')"> Cancel </SubformButton>
      </template>
      <template v-if="editing">
        <SubformButton @click="update()"> Save </SubformButton>
        <!-- <SubformButton @click="destroy()"> Delete </SubformButton> -->
        <SubformButton @click="editing = false"> Cancel </SubformButton>
      </template>
      <SubformButton v-if="!editing && !adding" @click="editing = true">
        Edit
      </SubformButton>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';

import FormValidationErrors from '@/Components/FormValidationErrors.vue';
import SubformButton from '@/Components/SubformButton.vue';
import { toast, deleteConfirmationDialog } from '@/Components/swal.js';

const props = defineProps({
  classModel: Object,
});
const form = useForm({
  name: props.classModel.name,
  suffix: props.classModel.suffix,
});
const adding = ref(props.classModel.adding ?? false);
const editing = ref(false);

const emit = defineEmits(['cancelAdd', 'stored']);

function store() {
  form.post(route('classes.store'), {
    onSuccess: () => {
      adding.value = false;
      //this is because when a class is created, the data of this form is not updated with the id from the server.
      //therefore calls to delete throw an error since id is still empty
      //this is because preserveState is set to true (necessary to properly handle validation errors).
      //reloading the page with false preserve state will update the data with fresh data from the server
      Inertia.get(route('classes.form'), {}, { preserveState: false });
      emit('stored');
      toast.fire({ title: `Added!` });
    },
  });
}

function update() {
  form.patch(route('classes.update', props.classModel.id), {
    onSuccess: () => {
      toast.fire({ title: `Saved!` });
    },
  });
}

function destroy() {
  deleteConfirmationDialog(() =>
    form.delete(route('classes.destroy', props.classModel.id), {
      preserveState: false,
      onSuccess: () => {
        toast.fire({ title: `Deleted!` });
      },
    })
  );
}
</script>
