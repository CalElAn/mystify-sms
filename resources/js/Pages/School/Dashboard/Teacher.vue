<template>
  <section class="flex items-center justify-center">
    <!-- class name and teacher -->
    <div class="base-card flex w-[32rem] items-center justify-evenly p-2">
      <div
        class="flex items-center justify-center gap-2 text-xl font-semibold tracking-tight text-purple-600"
      >
        {{ classModel.name }} {{ classModel.suffix }}
        <button
          @click="shouldOpenModalContainingListOfClasses = true"
          class="text-gray-700 hover:text-purple-600"
        >
          <MenuIcon class="h-5 w-5" />
        </button>
        <button class="text-gray-700 hover:text-purple-600">
          <CogIcon class="h-5 w-5" />
        </button>
      </div>
      <div
        style="height: 36px; width: 2px; background: #ddd; display: inline"
      ></div>
      <div class="flex items-center gap-2">
        <div class="inline-block">Class teacher:</div>
        <div class="flex items-center justify-center gap-2">
          <ProfilePicture
            :profilePicturePath="classTeacher.profile_picture_path"
            widthClass="w-14"
            heightClass="h-14"
          />
          <!-- //TODO href should lead to profile -->
          <a
            href="#"
            class="inline-block text-lg font-semibold text-purple-600 underline underline-offset-1"
          >
            {{ classTeacher.name }}
          </a>
        </div>
      </div>
    </div>
  </section>
  <section class="flex flex-col items-center justify-center gap-2">
    <div class="flex w-11/12 justify-end pr-6">
      <!-- cog icon button -->
      <Menu as="div" class="base-card relative">
        <MenuButton class="w-full p-2">
          <CogIcon
            class="h-7 w-7 text-purple-600 transition-transform hover:scale-110"
          />
        </MenuButton>
        <MenuItemsTransition>
          <MenuItems
            class="menu-items right-0 z-10 mt-2 w-max origin-top-right"
          >
            <div class="px-1 py-1">
              <MenuItem as="div" v-slot="{ active }">
                <MenuItemButton :active="active">
                  Add student(s) to {{ classModel.name }}
                  {{ classModel.suffix }}
                </MenuItemButton>
              </MenuItem>
              <MenuItem as="div" v-slot="{ active }">
                <MenuItemButton :active="active">
                  Remove student(s) from {{ classModel.name }}
                  {{ classModel.suffix }}
                </MenuItemButton>
              </MenuItem>
            </div>
            <div class="px-1 py-1">
              <MenuItem as="div" v-slot="{ active }">
                <MenuItemButton :active="active">
                  Send text message to student(s)
                </MenuItemButton>
              </MenuItem>
              <MenuItem as="div" v-slot="{ active }">
                <MenuItemButton :active="active">
                  Send text message to parent(s)
                </MenuItemButton>
              </MenuItem>
            </div>
          </MenuItems>
        </MenuItemsTransition>
      </Menu>
    </div>
    <!-- list of students in class -->
    <div class="base-card w-11/12 p-2">
      <table class="w-full table-auto text-left">
        <thead class="bg-purple-100 text-gray-500">
          <tr>
            <th class="p-2"></th>
            <th class="p-2">Name</th>
            <th class="p-2">Email</th>
            <th class="p-2">Number</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(student, index) in studentsInClass"
            :key="index"
            class="odd:bg-white even:bg-gray-50"
          >
            <td class="flex justify-center p-2">
              <ProfilePicture
                :profilePicturePath="student.profile_picture_path"
                widthClass="w-10"
                heightClass="h-10"
              />
            </td>
            <td class="p-2">
              <Link
                class="decoration-purple-600 hover:underline"
                :href="route('users.show', { userId: student.id })"
                >{{ student.name }}</Link
              >
            </td>
            <td class="p-2">
              <a
                class="decoration-purple-600 hover:underline"
                :href="'mailto:' + student.email"
              >
                {{ student.email }}
              </a>
            </td>
            <td class="p-2">
              <a
                class="decoration-purple-600 hover:underline"
                :href="'tel:' + student.phone_number"
                >{{ student.phone_number }}</a
              >
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
  <section class="flex items-center justify-center">
    <div class="flex gap-4">
      <!-- subject, class and term -->
      <div
        class="base-card flex w-[30rem] items-center justify-evenly p-2 text-lg font-semibold"
      >
        <div>
          {{ currentSubject.subject_name }}
        </div>
        <div
          style="height: 36px; width: 2px; background: #ddd; display: inline"
        ></div>
        <div>
          {{ currentSubject.class_model.name }}
          {{ currentSubject.class_model.suffix }}
        </div>
        <div
          style="height: 36px; width: 2px; background: #ddd; display: inline"
        ></div>
        <div>
          {{ currentSubject.term.formatted_short_name }}
        </div>
      </div>
      <div class="flex items-center justify-center gap-2 text-purple-600">
        <div class="base-card p-2">
          <button
            class="flex items-center justify-center hover:text-purple-400"
          >
            <MenuIcon
              @click="shouldOpenModalContainingListOfSubjects = true"
              class="h-5 w-5"
            />
          </button>
        </div>
        <div class="base-card p-2">
          <button
            class="flex items-center justify-center hover:text-purple-400"
          >
            <CogIcon class="h-5 w-5" />
          </button>
        </div>
      </div>
    </div>
  </section>
  <!-- MODALS -->
  <!-- Modal containing list of classes -->
  <Modal
    :show="shouldOpenModalContainingListOfClasses"
    :maxWidthClass="'max-w-xs'"
    @closeModal="shouldOpenModalContainingListOfClasses = false"
  >
    <div class="flex flex-col gap-3 text-purple-600">
      <button
        v-for="(classItem, classIndex) in classes"
        @click="changeAcademicYear(classItem.pivot.academic_year_id)"
        :key="classIndex"
        class="list-of-buttons-in-modal text-purple-600 hover:text-purple-400 flex items-center justify-center p-2"
      >
        {{ classItem.name }} {{ classItem.suffix }}
      </button>
    </div>
  </Modal>
  <!-- Modal containing list of subjects -->
  <Modal
    :show="shouldOpenModalContainingListOfSubjects"
    :maxWidthClass="'max-w-lg'"
    @closeModal="shouldOpenModalContainingListOfSubjects = false"
  >
    <div class="flex flex-col gap-4 text-sm">
      <button
        v-for="(subjectItem, subjectIndex) in subjects"
        :key="subjectIndex"
        class="list-of-buttons-in-modal flex items-center justify-between hover:text-purple-600 p-1"
      >
        <div class="w-1/3">
          {{ subjectItem.subject_name }}
        </div>
        <div
          style="height: 30px; width: 2px; background: #ddd; display: inline"
        ></div>
        <div class="w-1/3">
          {{ subjectItem.class_model.name }}
          {{ subjectItem.class_model.suffix }}
        </div>
        <div
          style="height: 30px; width: 2px; background: #ddd; display: inline"
        ></div>
        <div class="w-1/3">
          {{ subjectItem.term.formatted_short_name }}
        </div>
      </button>
    </div>
  </Modal>
</template>

<script setup>
import { ref, computed } from 'vue';
// import {  } from '@heroicons/vue/solid';
// import {  } from '@heroicons/vue/outline';
import { usePage } from '@inertiajs/inertia-vue3';
// import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';

import { defaultProps, changeAcademicYear } from '@/helpers';
import ProfilePicture from '@/Components/ProfilePicture.vue';

const props = defineProps({
  ...defaultProps,
  classes: Object,
  classModel: Object,
  classTeacher: Object,
  studentsInClass: Array,
  subjects: Array,
  currentSubject: Object,
});

const authUser = computed(() => usePage().props.value.auth.user);
const shouldOpenModalContainingListOfClasses = ref(false);
const shouldOpenModalContainingListOfSubjects = ref(false);
</script>
