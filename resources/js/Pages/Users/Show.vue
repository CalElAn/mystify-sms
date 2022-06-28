<template>
  <div class="mx-auto flex w-2/5 items-center justify-end">
    <button
      v-if="user.is_this_user_the_auth_user"
      @click="editing = !editing"
      class="inline-flex items-center justify-center gap-2 rounded-lg border border-purple-800 bg-purple-50 px-2 py-1.5 font-semibold tracking-wide text-purple-800 shadow-sm hover:bg-purple-100"
    >
      <PencilAltIcon class="h-5 w-5" />
      {{ editing ? 'Cancel' : 'Edit' }}
    </button>
  </div>
  <section class="flex basis-full items-start justify-center">
    <div class="base-card w-2/5 py-4 px-6">
      <p class="form-title text-center">Profile</p>
      <form @submit.prevent="submit" class="mt-6 flex flex-col gap-4">
        <file-pond
          name="filepond"
          ref="filepond"
          label-idle="Drag & Drop your picture here or <span class='filepond--label-action'>Browse</span>"
          accepted-file-types="image/png, image/jpeg"
          :allow-multiple="false"
          :max-files="1"
          :required="false"
          :captureMethod="null"
          :server="{
            url: '/filepond',
            process: '/process',
            revert: '/revert',
            restore: '/restore',
            load: '/load/User/',
            fetch: '/fetch',
            remove: handleFilePondRemove,
            headers: {
              'X-CSRF-TOKEN': $page.props.csrf_token,
            },
          }"
          :files="filepondInitialMedia"
          :imagePreviewHeight="170"
          imageCropAspectRatio="1:1"
          :imageResizeTargetWidth="200"
          :imageResizeTargetHeight="200"
          :stylePanelLayout="'compact circle'"
          styleLoadIndicatorPosition="center bottom"
          styleProgressIndicatorPosition="right bottom"
          styleButtonRemoveItemPosition="left bottom"
          styleButtonProcessItemPosition="right bottom"
        />
        <div>
          <label class="block text-sm font-semibold text-gray-800" for="name"
            >Name</label
          >
          <input
            type="text"
            class="custom-input mt-1 w-full shadow-sm"
            v-model="form.name"
            required
            :readonly="!editing"
            autocomplete="name"
          />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-800" for="email"
            >Email</label
          >
          <input
            id="email"
            type="email"
            class="custom-input mt-1 w-full shadow-sm"
            v-model="form.email"
            required
            :readonly="!editing"
            autocomplete="email"
          />
        </div>
        <div>
          <label
            class="block text-sm font-semibold text-gray-800"
            for="phone_number"
            >Phone number</label
          >
          <input
            id="phone_number"
            type="tel"
            class="custom-input mt-1 w-full shadow-sm"
            v-model="form.phone_number"
            required
            :readonly="!editing"
            autocomplete="phone_number"
          />
        </div>
        <div>
          <label
            class="block text-sm font-semibold text-gray-800"
            for="user_type"
            >Account type</label
          >
          <input
            id="user_type"
            type="tel"
            class="custom-input mt-1 w-full shadow-sm"
            v-model="user.user_type"
            readonly
          />
        </div>
        <FormValidationErrors :errors="form.errors" />
        <div v-show="editing" class="flex items-center justify-between">
          <button
            type="submit"
            :disabled="form.processing"
            class="flex items-center justify-center gap-2 rounded-lg bg-purple-400 py-2 px-3 text-lg font-semibold tracking-wide text-white shadow-sm hover:bg-purple-500 disabled:opacity-50 disabled:hover:cursor-text"
          >
            <CheckCircleIcon class="h-5 w-5" />
            {{ form.processing ? 'Saving...' : 'Save' }}
          </button>
        </div>
        <Link
          :href="route('change_password_form')"
          class="text-gray-600 underline"
        >
          Change password?
        </Link>
      </form>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { CheckCircleIcon, PencilAltIcon } from '@heroicons/vue/outline';
import { Inertia } from '@inertiajs/inertia';

import FormValidationErrors from '@/Components/FormValidationErrors.vue';

import vueFilePond from 'vue-filepond';

import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';

import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileEncode from 'filepond-plugin-file-encode';
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation';
import FilePondPluginImageCrop from 'filepond-plugin-image-crop';
import FilePondPluginImageResize from 'filepond-plugin-image-resize';
import FilePondPluginImageTransform from 'filepond-plugin-image-transform';

const FilePond = vueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginImagePreview,
  FilePondPluginFileEncode,
  FilePondPluginImageExifOrientation,
  FilePondPluginImageCrop,
  FilePondPluginImageResize,
  FilePondPluginImageTransform
);

const filepondInitialMedia = ref([]);
const filepond = ref(null);

const editing = ref(false);

const props = defineProps({
  user: Object,
});

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  phone_number: props.user.phone_number,
});

function handleFilePondRemove(source, load, error) {
  axios
    .delete(`/filepond/remove/User/${source}`)
    .then((response) => {
      if (response.status === 200 && response.data === 1) load();
    })
    .catch((axiosError) => {
      error('Server error, could not delete');
      console.log(axiosError);
    });
}

function submit() {
  form
    .transform((data) => ({
      ...data,
      filepond: filepond.value.getFile()?.serverId,
    }))
    .patch(route('users.update', { user: props.user.id }), {
      onSuccess: () => {
        //this is needed to make sure profile pic in navbar is updated properly
        //this is because preserveState is set to true (necessary to properly handle validation errors).
        //reloading the page with false preserve state will update the profile pic with fresh data from the server
        Inertia.get(
          route('users.show', { user: props.user.id }),
          {},
          { preserveState: false }
        );
        //TODO notify added
      },
    });
}

onMounted(() => {
  if (!props.user.profile_picture_path.includes('https://')) {
    filepondInitialMedia.value = [
      {
        source: `${props.user.id}`,
        options: { type: 'local' },
      },
    ];
  }
});
</script>

<style>
.filepond--root {
  width: 170px;
  margin: 0 auto;
}
</style>
