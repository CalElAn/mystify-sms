<template>
  <section class="flex items-center justify-center">
    <!-- class name and teacher -->
    <div
      v-if="classModel"
      class="base-card flex flex-col items-center justify-center gap-2 py-2 px-4 sm:flex-row sm:gap-4"
    >
      <div
        class="flex items-center justify-center gap-2 text-lg font-semibold text-purple-600 sm:text-xl"
      >
        {{ classModel.name_and_suffix }}
        <button
          v-if="$page.url.startsWith('/dashboard')"
          @click="$emit('openModalContainingListOfClasses')"
          class="text-gray-700 hover:text-purple-600"
        >
          <ViewListIcon class="h-5 w-5" />
        </button>
        <!-- <Menu as="div" class="relative">
          <MenuButton class="flex h-full w-full items-center justify-center">
            <DotsVerticalIcon
              class="h-5 w-5 text-gray-700 hover:text-purple-600"
            />
          </MenuButton>
          <MenuItemsTransition>
            <MenuItems
              class="menu-items right-0 z-10 mt-2 w-max origin-top-right"
            >
              <div class="px-1 py-1">
                <MenuItem as="div" v-slot="{ active }">
                  <MenuItemButton :active="active">
                    Add class(es)
                  </MenuItemButton>
                </MenuItem>
                <MenuItem as="div" v-slot="{ active }">
                  <MenuItemButton :active="active">
                    Remove class(es)
                  </MenuItemButton>
                </MenuItem>
              </div>
            </MenuItems>
          </MenuItemsTransition>
        </Menu> -->
      </div>
      <div
        class="hidden sm:inline"
        style="height: 36px; width: 2px; background: #ddd"
      ></div>
      <div class="flex items-center gap-2">
        <div class="inline-block text-sm sm:text-base">Class teacher:</div>
        <div class="flex items-center justify-center gap-2">
          <ProfilePicture
            :profilePicturePath="classModel.teachers[0]?.profile_picture_path"
            widthClass="w-14"
            heightClass="h-14"
          />
          <button
            @click="shouldOpenTeacherCardModal = true"
            class="inline-block font-semibold tracking-wide text-purple-600 underline underline-offset-1 sm:text-lg"
          >
            {{ classModel.teachers[0]?.name }}
          </button>
        </div>
      </div>
    </div>
  </section>
  <section class="flex items-center justify-center gap-2">
    <!-- list of students in class -->
    <UserTable
      :title="`Students in ${classModel.name_and_suffix} (${academicYearName})`"
      :users="studentsInClass"
      class="base-card w-11/12"
    />
  </section>
  <!-- modal with teacher's information  -->
  <TeacherCardModal
    :teacher="classModel.teachers[0]"
    :show="shouldOpenTeacherCardModal"
    @closeModal="shouldOpenTeacherCardModal = false"
  />
</template>

<script setup>
import UserTable from '@/Components/Users/Table.vue';
import TeacherCardModal from '@/Components/Users/TeacherCardModal.vue';
import { useTeacherCardModal } from '@/Components/Users/teacher_card_modal.js';

const { shouldOpenTeacherCardModal } = useTeacherCardModal();

defineProps({
  classModel: Object,
  studentsInClass: Array,
  academicYearName: String,
});

defineEmits(['openModalContainingListOfClasses']);
</script>

<style lang="scss" scoped></style>
