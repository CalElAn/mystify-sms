<template>
  <Head title="Classes"></Head>
  <section class="flex items-center justify-center">
    <div class="base-card overflow-x-auto p-2 qxl:w-3/5">
      <p class="mb-1 text-center font-semibold tracking-wide md:text-lg">
        Classes and class teachers ({{ term.academic_year.name }})
      </p>
      <table class="mt-2 w-full table-auto text-center text-sm sm:text-base">
        <thead class="thead">
          <tr>
            <th class="p-2">Class</th>
            <!-- <th class="p-2"></th> -->
            <th class="p-2 pl-6 text-left">Class teacher</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(classItem, index) in classes"
            :key="index"
            class="tbody text-sm xl:text-base"
          >
            <td class="p-2">
              <Link
                class="tda"
                :href="
                  route('classes.show', {
                    classModel: classItem.id,
                    termId: term.id,
                  })
                "
                >{{ classItem.name_and_suffix }}</Link
              >
            </td>
            <td class="ml-2 flex items-center justify-start gap-2 p-2">
              <ProfilePicture
                :profilePicturePath="
                  classItem.teachers[0]?.profile_picture_path
                "
                widthClass="w-10"
                heightClass="h-10"
              />
              <button
                @click="showTeacherCardModal(classItem.teachers[0])"
                class="tda"
              >
                {{ classItem.teachers[0]?.name }}
              </button>
            </td>
          </tr>
          <tr v-if="classes.length === 0">
            <td class="border-t px-6 py-4" colspan="2">No classes found.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <TeacherCardModal
      :teacher="teacherRef"
      :show="shouldOpenTeacherCardModal"
      @closeModal="shouldOpenTeacherCardModal = false"
    />
  </section>
</template>

<script setup>
import TeacherCardModal from '@/Components/Users/TeacherCardModal.vue';

import { useTeacherCardModal } from '@/Components/Users/teacher_card_modal.js';

const { shouldOpenTeacherCardModal, teacherRef, showTeacherCardModal } =
  useTeacherCardModal();

defineProps({
  term: Object,
  classes: Array,
});
</script>

<style lang="scss" scoped></style>
