<template>
  <div
    class="place-self-center rounded-full bg-contain bg-center bg-no-repeat"
    :class="[heightClass, widthClass]"
    :style="{
      'background-image': 'url(' + getProfilePictureUrl(profilePicturePath) + ')',
    }"
    alt="profile picture"
  ></div>
</template>

<script setup>
import { getProfilePictureUrl } from '@/helpers';

defineProps({
  profilePicturePath: String,
  heightClass: String,
  widthClass: String,
});
</script>

<style lang="scss" scoped></style>
