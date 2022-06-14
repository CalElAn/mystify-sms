<template>
  <div
    class="grid grid-cols-2 gap-y-4 gap-x-12 p-2 odd:bg-white even:bg-gray-100"
    :class="[
      {
        'my-4 border-purple-400 ring-4 ring-purple-300 ring-opacity-50': adding,
      },
    ]"
  >
    <input
      :readonly="!editing && !adding"
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
    <div
      v-if="Object.keys(form.errors).length"
      class="col-span-2 my-2 rounded-md border border-red-500 p-2 text-sm text-red-500"
    >
      <ul class="list-inside list-disc">
        <li class="" v-for="(item, index) in form.errors" :key="index">
          {{ item }}
        </li>
      </ul>
    </div>
    <div class="col-span-2 mr-4 flex justify-end gap-3">
      <template v-if="adding">
        <button
          @click="store()"
          :disabled="form.processing"
          class="rounded-lg border border-fuchsia-600 text-sm px-2 py-1 font-medium tracking-wide text-fuchsia-600 shadow-sm hover:border-transparent hover:bg-fuchsia-400 hover:text-white disabled:opacity-50 disabled:hover:cursor-text"
        >
          {{ form.processing ? 'Adding...' : 'Add' }}
        </button>
        <button
          @click="$emit('cancelAdd')"
          class="rounded-lg border border-fuchsia-600 text-sm px-2 py-1 font-medium tracking-wide text-fuchsia-600 shadow-sm hover:border-transparent hover:bg-fuchsia-400 hover:text-white"
        >
          Cancel
        </button>
      </template>
      <template v-if="editing">
        <button
          @click="update()"
          class="rounded-lg border border-fuchsia-600 text-sm px-2 py-1 font-medium tracking-wide text-fuchsia-600 shadow-sm hover:border-transparent hover:bg-fuchsia-400 hover:text-white"
        >
          Save
        </button>
        <button
          @click="destroy()"
          class="rounded-lg border border-fuchsia-600 text-sm px-2 py-1 font-medium tracking-wide text-fuchsia-600 shadow-sm hover:border-transparent hover:bg-fuchsia-400 hover:text-white"
        >
          Delete
        </button>
        <button
          @click="editing = false"
          class="rounded-lg border border-fuchsia-600 text-sm px-2 py-1 font-medium tracking-wide text-fuchsia-600 shadow-sm hover:border-transparent hover:bg-fuchsia-400 hover:text-white"
        >
          Cancel
        </button>
      </template>
      <button
        v-if="!editing && !adding"
        @click="editing = true"
        class="rounded-lg border border-fuchsia-600 text-sm px-2 py-1 font-medium tracking-wide text-fuchsia-600 shadow-sm hover:border-transparent hover:bg-fuchsia-400 hover:text-white"
      >
        Edit
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';

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
      //this is because when an academic year is created, the data of this form is not updated with the id from the server.
      //therefore calls to delete throw an error since id is still empty
      //this is because preserveState is set to true (necessary to properly handle validation errors).
      //reloading the page with false preserve state will update the data with fresh data from the server
      Inertia.get(route('classes.form'), {}, { preserveState: false });
      emit('stored');
      //TODO notify added
    },
  });
}

function update() {
  form.patch(route('classes.update', props.classModel.id), {
    onSuccess: () => {
      //TODO notify saved
    },
  });
}

function destroy() {
  //TODO notify confirmation
  form.delete(route('classes.destroy', props.classModel.id), {
    preserveState: false,
    onSuccess: () => {
      // emit('destroyed')
      //TODO notify deleted
    },
  });
}
</script>
