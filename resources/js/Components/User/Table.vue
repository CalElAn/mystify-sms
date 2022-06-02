<template>
  <div class="base-card w-11/12 p-2">
    <p class="mb-1 text-center text-xl font-semibold tracking-wide">
      {{ title }}
    </p>
    <table class="w-full table-auto text-left">
      <thead class="thead">
        <tr>
          <th class="p-2"></th>
          <th class="p-2">Name</th>
          <th class="p-2">Email</th>
          <th class="p-2">Number</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(user, index) in users"
          :key="index"
          class="tbody"
        >
          <td class="flex justify-center p-2">
            <ProfilePicture
              :profilePicturePath="user.profile_picture_path"
              widthClass="w-10"
              heightClass="h-10"
            />
          </td>
          <td class="p-2">
            <Link
              class="tda"
              :href="route('dashboard', { userId: user.id })"
              >{{ user.name }}</Link
            >
          </td>
          <td class="p-2">
            <a
              class="tda"
              :href="'mailto:' + user.email"
            >
              {{ user.email }}
            </a>
          </td>
          <td class="p-2">
            <a
              class="tda"
              :href="'tel:' + user.phone_number"
              >{{ user.phone_number }}</a
            >
          </td>
        </tr>
        <tr v-if="!users || users.length === 0">
            <td class="px-6 py-4 border-t" colspan="4">No {{userType}} found.</td>
          </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  users: Array,
  userType: {
    type: String,
    default: 'students'
  }
});
</script>
