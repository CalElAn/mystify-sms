<script setup>
import BreezeButton from '@/Components/Button.vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';

const form = useForm({
  name: '',
  sex: '',
  email: '',
  phone_number: '',
  default_user_type: '',
  password: '',
  password_confirmation: '',
  terms: false,
});

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <BreezeGuestLayout>
    <Head title="Register" />

    <BreezeValidationErrors class="mb-4" />

    <form @submit.prevent="submit">
      <div>
        <BreezeLabel for="name" value="Name" />
        <BreezeInput
          id="name"
          type="text"
          class="mt-1 block w-full"
          v-model="form.name"
          required
          autofocus
          autocomplete="name"
        />
      </div>
      <div class="mt-4">
        <BreezeLabel for="sex" value="Sex" />
        <select
          id="sex"
          class="custom-select shadow-sm mt-1 block w-full"
          v-model="form.sex"
          required
        >
          <option value="male">male</option>
          <option value="female">female</option>
        </select>
      </div>
      <div class="mt-4">
        <BreezeLabel for="email" value="Email" />
        <BreezeInput
          id="email"
          type="email"
          class="mt-1 block w-full"
          v-model="form.email"
          required
          autocomplete="username"
        />
      </div>
      <div class="mt-4">
        <BreezeLabel for="phone_number" value="Phone number" />
        <BreezeInput
          id="phone_number"
          type="tel"
          class="mt-1 block w-full"
          v-model="form.phone_number"
          required
          autocomplete="phone_number"
        />
      </div>
      <div class="mt-4">
        <BreezeLabel for="default_user_type" value="You are a/an" />
        <select
          id="default_user_type"
          class="custom-select shadow-sm mt-1 block w-full"
          v-model="form.default_user_type"
          required
        >
          <!-- <option value="administrator">school administrator (eg. accountant, assistant headteacher, etc)</option> -->
          <option value="teacher">teacher</option>
          <option value="student">student</option>
          <option value="parent">none of the above</option>
        </select>
      </div>
      <div class="mt-4">
        <BreezeLabel for="password" value="Password" />
        <BreezeInput
          id="password"
          type="password"
          class="mt-1 block w-full"
          v-model="form.password"
          required
          autocomplete="new-password"
        />
      </div>
      <div class="mt-4">
        <BreezeLabel for="password_confirmation" value="Confirm Password" />
        <BreezeInput
          id="password_confirmation"
          type="password"
          class="mt-1 block w-full"
          v-model="form.password_confirmation"
          required
          autocomplete="new-password"
        />
      </div>
      <div class="mt-4 flex items-center justify-end">
        <Link
          :href="route('login')"
          class="text-sm text-gray-600 underline hover:text-gray-900"
        >
          Already registered?
        </Link>
        <BreezeButton
          class="ml-4"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Register
        </BreezeButton>
      </div>
    </form>
  </BreezeGuestLayout>
</template>
