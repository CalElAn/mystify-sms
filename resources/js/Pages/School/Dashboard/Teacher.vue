<template>
  <section class="flex items-center justify-center">
    <!-- class name and teacher -->
    <div class="base-card flex w-[32rem] items-center justify-evenly p-2">
      <div
        class="flex items-center justify-center gap-2 text-xl font-semibold text-purple-600"
      >
        {{ classModel.name }} {{ classModel.suffix }}
        <button
          @click="shouldOpenModalContainingListOfClasses = true"
          class="text-gray-700 hover:text-purple-600"
        >
          <ViewListIcon class="h-5 w-5" />
        </button>
        <Menu as="div" class="relative">
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
        </Menu>
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
          <Link
            :href="route('users.show', { userId: classTeacher.id })"
            class="inline-block text-lg font-semibold tracking-wide text-purple-600 underline underline-offset-1"
          >
            {{ classTeacher.name }}
          </Link>
        </div>
      </div>
    </div>
  </section>
  <section class="flex flex-col items-center justify-center gap-2">
    <div class="flex w-11/12 justify-end pr-6">
      <!-- cog icon button -->
      <Menu as="div" class="base-card relative">
        <MenuButton class="w-full p-2">
          <DotsVerticalIcon
            class="h-6 w-6 text-purple-600 transition-transform hover:scale-110"
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
      <p class="mb-1 text-center text-xl font-semibold tracking-wide">
        Students
      </p>
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
  <hr class="my-2" />
  <section class="flex flex-col items-center justify-center gap-4">
    <div class="flex gap-4">
      <!-- subject, class and term -->
      <div
        class="base-card flex w-[30rem] items-center justify-evenly p-2 text-lg font-semibold tracking-wide"
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
            <ViewListIcon
              @click="shouldOpenModalContainingListOfSubjects = true"
              class="h-5 w-5"
            />
          </button>
        </div>
        <!-- cog icon button -->
        <Menu as="div" class="base-card relative">
          <MenuButton class="w-full p-2">
            <DotsVerticalIcon
              class="h-5 w-5 text-purple-600 hover:text-purple-400"
            />
          </MenuButton>
          <MenuItemsTransition>
            <MenuItems
              class="menu-items right-0 z-10 mt-2 w-max origin-top-right"
            >
              <div class="px-1 py-1">
                <MenuItem as="div" v-slot="{ active }">
                  <MenuItemButton :active="active">
                    Add subject(s)
                  </MenuItemButton>
                </MenuItem>
                <MenuItem as="div" v-slot="{ active }">
                  <MenuItemButton :active="active">
                    Remove subject(s)
                  </MenuItemButton>
                </MenuItem>
              </div>
            </MenuItems>
          </MenuItemsTransition>
        </Menu>
      </div>
    </div>
    <div class="flex w-full flex-col items-center justify-center gap-2">
      <div class="flex w-2/3 justify-end pr-6">
        <Menu as="div" class="base-card relative">
          <MenuButton class="w-full p-2">
            <DotsVerticalIcon
              class="h-5 w-5 text-purple-600 transition-transform hover:scale-110"
            />
          </MenuButton>
          <MenuItemsTransition>
            <MenuItems
              class="menu-items right-0 z-10 mt-2 w-max origin-top-right"
            >
              <div class="px-1 py-1">
                <MenuItem as="div" v-slot="{ active }">
                  <MenuItemButton :active="active">
                    Add student(s) grade
                    {{
                      `(${currentSubject.subject_name} | ${currentSubject.class_model.name} ${currentSubject.class_model.suffix} | ${currentSubject.term.formatted_short_name})`
                    }}
                  </MenuItemButton>
                </MenuItem>
                <MenuItem as="div" v-slot="{ active }">
                  <MenuItemButton :active="active">
                    Remove student(s) grade(s)
                    {{
                      `(${currentSubject.subject_name} | ${currentSubject.class_model.name} ${currentSubject.class_model.suffix} | ${currentSubject.term.formatted_short_name})`
                    }}
                  </MenuItemButton>
                </MenuItem>
              </div>
            </MenuItems>
          </MenuItemsTransition>
        </Menu>
        <!-- <button
          class="flex items-center justify-center gap-1 rounded-xl border border-purple-600 p-2 tracking-wide hover:bg-purple-100 text-purple-600 shadow-sm hover:border-purple-800 hover:text-purple-800"
        >
          <PlusCircleIcon class="h-5 w-5" />
          Add student(s)
        </button> -->
      </div>
      <!-- list of students and grades per selected subject -->
      <div class="base-card w-2/3 p-2">
        <p
          class="flex items-center justify-center gap-2 p-1 text-lg font-semibold tracking-wide"
        >
          Grades
          <span class="text-base font-light tracking-normal">
            {{
              `(${currentSubject.subject_name} | ${currentSubject.class_model.name} ${currentSubject.class_model.suffix} | ${currentSubject.term.formatted_short_name})`
            }}
          </span>
        </p>
        <table class="w-full table-auto text-left">
          <thead class="bg-purple-100 text-gray-500">
            <tr>
              <th class="p-2"></th>
              <th class="p-2">Name</th>
              <th class="p-2">
                Class mark ({{ school.class_mark_percentage * 100 }}%)
              </th>
              <th class="p-2">
                Exam mark ({{ school.exam_mark_percentage * 100 }}%)
              </th>
              <th class="p-2"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, index) in gradesForCurrentSubjectWithStudent"
              :key="index"
              class="odd:bg-white even:bg-gray-50"
            >
              <td class="flex justify-center p-2">
                <ProfilePicture
                  :profilePicturePath="item.student.profile_picture_path"
                  widthClass="w-10"
                  heightClass="h-10"
                />
              </td>
              <td class="p-2">
                <Link
                  class="decoration-purple-600 hover:underline"
                  :href="route('users.show', { userId: item.student.id })"
                  >{{ item.student.name }}</Link
                >
              </td>
              <td class="px-3">
                <input
                  class="custom-input w-full text-center"
                  type="number"
                  min="0"
                  :max="school.class_mark_percentage * 100"
                  :value="item.class_mark"
                />
              </td>
              <td class="px-3">
                <input
                  class="custom-input w-full text-center"
                  type="number"
                  min="0"
                  :max="school.exam_mark_percentage * 100"
                  :value="item.exam_mark"
                />
              </td>
              <td class="p-2">
                <MinusCircleIcon class="h-5 w-5" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <button
        class="flex w-2/3 items-center justify-center gap-2 rounded-xl bg-purple-400 p-2 text-lg font-semibold tracking-wide text-white shadow-sm hover:bg-purple-500"
      >
        <CheckCircleIcon class="h-6 w-6" />
        Save
      </button>
    </div>
  </section>
  <hr class="my-2" />
  <section class="flex gap-6">
    <!-- Notice board -->
    <TimelineCard
      :title="'Notice board'"
      :messages="noticeBoardMessages"
      class="w-2/5"
    />
    <!-- Notifications -->
    <TimelineCard
      :title="'Notifications'"
      :messages="noticeBoardMessages"
      class="w-2/5"
    />
    <!-- Recent activities -->
    <TimelineCard
      :title="'Recent activities'"
      :messages="noticeBoardMessages"
      class="w-1/5"
    />
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
        class="list-of-buttons-in-modal flex items-center justify-center p-2 text-purple-600 hover:text-purple-400 hover:underline"
      >
        {{ classItem.name }} {{ classItem.suffix }}
      </button>
    </div>
  </Modal>
  <!-- Modal containing list of subjects -->
  <Modal
    :show="shouldOpenModalContainingListOfSubjects"
    :maxWidthClass="'max-w-2xl'"
    @closeModal="shouldOpenModalContainingListOfSubjects = false"
  >
    <div class="flex flex-col gap-4 text-base">
      <button
        v-for="(subjectItem, subjectIndex) in subjects"
        :key="subjectIndex"
        class="list-of-buttons-in-modal flex items-center p-2 font-medium hover:text-purple-600 hover:shadow-sm"
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
// import {  PlusIcon } from '@heroicons/vue/solid';
import {
  PlusCircleIcon,
  CheckCircleIcon,
  MinusCircleIcon,
} from '@heroicons/vue/outline';
import { usePage } from '@inertiajs/inertia-vue3';
// import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';

import { defaultProps, changeAcademicYear } from '@/helpers';
import ProfilePicture from '@/Components/ProfilePicture.vue';
import TimelineCard from '@/Components/TimelineCard.vue';

const props = defineProps({
  ...defaultProps,
  classes: Object,
  classModel: Object,
  classTeacher: Object,
  studentsInClass: Array,
  subjects: Array,
  currentSubject: Object,
  gradesForCurrentSubjectWithStudent: Array,
});

const authUser = computed(() => usePage().props.value.auth.user);
const shouldOpenModalContainingListOfClasses = ref(false);
const shouldOpenModalContainingListOfSubjects = ref(false);
</script>
