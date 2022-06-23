<template>
  <component
    :is="as"
    :href="route('dashboard', {userId: user.id})"
    class="base-card flex gap-6 border p-4 hover:border-purple-600"
  >
    <ProfilePicture
      :profilePicturePath="user.profile_picture_path"
      widthClass="w-1/4"
      heightClass="h-full"
    />
    <div class="flex flex-col gap-2 text-left">
      <div class="text-xl font-semibold tracking-wide text-fuchsia-600">
        {{ user.name }}
      </div>
      <div class="flex gap-2.5 items-center justify-start">
        <PhoneIcon class="h-4 w-4 text-blue-700"/>
        <a
          class="decoration-purple-600 hover:underline"
          :href="'tel:' + user.phone_number"
          >{{ user.phone_number }}</a
        >
      </div>
      <div class="flex gap-2.5 items-center justify-start">
        <MailIcon class="h-4 w-4 text-blue-700"/>
        <a
          class="decoration-purple-600 hover:underline"
          :href="'mailto:' + user.email"
        >
          {{ user.email }}
        </a>
      </div>
    </div>
  </component>
</template>

<script setup>
defineProps({
  user: Object,
  as: {
    type: String,
    default: 'Link',
  }
});
</script>

<style lang="scss" scoped></style>
