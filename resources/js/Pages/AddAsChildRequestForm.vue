<template>
  <Head title="Add child"></Head>
  <NavBar :actions="parentActions" />
  <section class="flex basis-full items-start justify-center">
    <div class="base-card flex flex-col items-center justify-center gap-6 p-4">
      <p class="form-title mt-2 text-center">Add child</p>
      <div class="text-gray-600">
        <p>
          Enter the email used to register your child's mystify-sms account.
        </p>
        <p>
          A notification will be sent to him/her to verify that you are the
          parent.
        </p>
      </div>
      <input
        class="custom-input w-full text-left shadow-sm"
        type="email"
        placeholder="enter email here"
        v-model="form.email"
      />
      <FormValidationErrors :errors="form.errors" />
      <button
        @click="addChild()"
        :disabled="form.processing"
        class="flex w-full items-center justify-center gap-2 rounded-lg bg-purple-400 p-2 text-lg font-semibold tracking-wide text-white shadow-sm hover:bg-purple-500 disabled:opacity-50 disabled:hover:cursor-text"
      >
        <PlusCircleIcon class="h-6 w-6" />
        {{ form.processing ? 'Adding...' : 'Add' }}
      </button>
    </div>
  </section>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { PlusCircleIcon } from '@heroicons/vue/outline';
import Swal from 'sweetalert2';

import NavBar from '@/Components/ActionsNavBar.vue';
import FormValidationErrors from '@/Components/FormValidationErrors.vue';
import { parentActions } from '@/parent_actions.js';

const form = useForm({
  email: '',
});

function addChild() {
  form.post(route('add_as_child_request.send_request', form.email), {
    onSuccess: () => {
      form.email = '';
      Swal.fire({
        icon: 'success',
        title: 'Request sent successfully.',
        text: 'When your child verifies from that you are the parent, you will have access to his/her account from your dashboard',
      });
    },
  });
}
</script>
