<template>
  <section class="flex basis-full items-start justify-center">
    <div class="base-card w-2/5 py-4 px-6">
      <p class="form-title text-center">Change password</p>
      <form @submit.prevent="submit" class="mt-6 flex flex-col gap-4">
        <div>
          <label class="block text-sm font-semibold text-gray-800" for="name"
            >Current password</label
          >
          <input
            type="password"
            class="custom-input mt-1 w-full shadow-sm"
            v-model="form.current_password"
            required
          />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-800" for="name"
            >New password</label
          >
          <input
            type="password"
            class="custom-input mt-1 w-full shadow-sm"
            v-model="form.password"
            required
          />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-800" for="name"
            >Confirm password</label
          >
          <input
            type="password"
            class="custom-input mt-1 w-full shadow-sm"
            v-model="form.password_confirmation"
            required
          />
        </div>
        <FormValidationErrors :errors="form.errors" />
        <div class="flex items-center justify-between">
          <button
            type="submit"
            :disabled="form.processing"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-purple-400 py-2 px-3 text-lg font-semibold tracking-wide text-white shadow-sm hover:bg-purple-500 disabled:opacity-50 disabled:hover:cursor-text"
          >
            <CheckCircleIcon class="h-5 w-5" />
            {{ form.processing ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </form>
    </div>
  </section>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { CheckCircleIcon } from '@heroicons/vue/outline';

import FormValidationErrors from '@/Components/FormValidationErrors.vue';

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

function submit() {
  form.patch(route('change_password'));
}
</script>
