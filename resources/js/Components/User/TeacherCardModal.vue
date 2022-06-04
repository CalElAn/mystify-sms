<template>
  <Modal :show="show" @closeModal="$emit('closeModal')">
    <div class="flex gap-4">
      <ProfilePicture
        :profilePicturePath="teacher.profile_picture_path"
        widthClass="w-1/3"
        heightClass="h-44"
        maxWidthClass="max-w-xl"
      />
      <div class="flex flex-col gap-2">
        <div class="flex flex-col gap-2 text-left">
          <div class="text-2xl font-semibold tracking-wide text-fuchsia-600">
            {{ teacher.name }}
          </div>
          <div class="flex items-center justify-start gap-2.5">
            <PhoneIcon class="h-4 w-4 text-blue-700" />
            <a
              class="decoration-purple-600 hover:underline"
              :href="'tel:' + teacher.phone_number"
              >{{ teacher.phone_number }}</a
            >
          </div>
          <div class="flex items-center justify-start gap-2.5">
            <MailIcon class="h-4 w-4 text-blue-700" />
            <a
              class="decoration-purple-600 hover:underline"
              :href="'mailto:' + teacher.email"
            >
              {{ teacher.email }}
            </a>
          </div>
        </div>
        <div>
          <p class="text-gray-600">Subjects:</p>
          <ul
            class="grid h-24 list-inside list-disc grid-cols-2 overflow-y-auto rounded-md border p-2 text-sm"
          >
            <li
              class="text-purple-600"
              v-for="(subjectItem, subjectIndex) in teacher.unique_subjects"
              :key="subjectIndex"
            >
              <span class="text-gray-800">{{ subjectItem }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { PhoneIcon, MailIcon } from '@heroicons/vue/solid';

defineProps({ show: Boolean, teacher: Object });

defineEmits(['closeModal']);
</script>
