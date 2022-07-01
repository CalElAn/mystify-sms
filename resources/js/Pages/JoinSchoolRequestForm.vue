<template>
  <Head title="Join school" />

  <!-- <BreezeAuthenticatedLayout> -->
  <!-- <template #header>
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
      Join school
    </h2>
  </template> -->

  <section class="mt-12 flex items-center justify-center">
    <div class="base-card flex flex-col items-center justify-center gap-6 p-4">
      <p class="form-title mt-2 text-center">Join school</p>
      <div class="text-gray-600">
        <p>Enter the school's name.</p>
        <p>
          A notification will be sent to the administrators of the school to
          verify that you are a/an
          {{ $page.props.auth.user.default_user_type }}.
        </p>
      </div>
      <input
        class="custom-input w-full text-left shadow-sm"
        type="text"
        placeholder="enter name here"
        v-model="form.name"
      />
      <FormValidationErrors :errors="form.errors" />
      <button
        @click="sendRequest()"
        :disabled="form.processing"
        class="flex w-full items-center justify-center gap-2 rounded-lg bg-purple-400 p-2 text-lg font-semibold tracking-wide text-white shadow-sm hover:bg-purple-500 disabled:opacity-50 disabled:hover:cursor-text"
      >
        <CheckCircleIcon class="h-6 w-6" />
        {{ form.processing ? 'Sending...' : 'Send' }}
      </button>
    </div>
  </section>
  <!-- </BreezeAuthenticatedLayout> -->
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';

export default {
  layout: BreezeAuthenticatedLayout,
};
</script>

<script setup>
import { onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import { CheckCircleIcon } from '@heroicons/vue/outline';
import { useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';

import FormValidationErrors from '@/Components/FormValidationErrors.vue';

const form = useForm({
  name: '',
});

function sendRequest() {
  form.post(route('join_school_request.send_request', form.name), {
    onSuccess: () => {
      form.name = '';
      //TODO notify with:
      //request sent successfully. When your child verifies from that you are the parent,
      //you will have access to his/her account from your dashboard
    },
  });
}

const authUser = computed(() => usePage().props.value.auth.user);

onMounted(() => {
  window.Echo.private(`App.Models.User.${authUser.value.id}`).notification(
    (notification) => {
      // console.log(notification);
      if (notification.type === 'App\\Notifications\\AcceptedJoinSchoolRequest') {
        Inertia.reload();
      }
    }
  );
});
</script>
