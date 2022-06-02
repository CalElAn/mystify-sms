<template>
  <!-- Navbar -->
  <nav
    class="ml-12 border-b border-gray-300 text-gray-500 dark:border-gray-700 dark:text-gray-400"
  >
    <ul class="flex flex-wrap gap-6">
      <li>
        <button
          @click="selectedTab = 1"
          :class="[
            selectedTab === 1
              ? 'border-blue-600 text-blue-600'
              : 'border-transparent hover:border-gray-400 hover:text-gray-600',
          ]"
          class="border-b-2 p-2 text-lg font-semibold tracking-wide"
        >
          Class
        </button>
      </li>
      <li>
        <button
          @click="selectedTab = 2"
          :class="[
            selectedTab === 2
              ? 'border-blue-600 text-blue-600'
              : 'border-transparent hover:border-gray-400 hover:text-gray-600',
          ]"
          class="border-b-2 p-2 text-lg font-semibold tracking-wide"
        >
          Grades
        </button>
      </li>
    </ul>
  </nav>
  <ClassPanel
    v-if="classModel && selectedTab === 1"
    :classModel="classModel"
    :studentsInClass="studentsInClass"
    :academicYearName="term.academic_year.name"
  />
  <div v-if="!classModel && selectedTab === 1" class="flex justify-center items-center">
    <div
      class="rounded-lg bg-red-300 px-10 py-4 text-lg text-white shadow"
    >
      <p>
        You have no classes associated with your account for this academic year
        ({{ term.academic_year.name }})
      </p>
      <div class="mt-2 flex justify-end">
        <button
          class="rounded-lg border border-fuchsia-600 bg-red-50 p-2 text-base font-semibold tracking-wide text-fuchsia-600 shadow-sm hover:bg-red-100"
        >
          Add class
        </button>
      </div>
    </div>
  </div>
  <section
    v-show="selectedTab === 2"
    class="flex flex-col items-center justify-center gap-4"
  >
    <div v-if="currentSubject" class="flex gap-4">
      <!-- subject, class and term -->
      <div
        class="base-card flex items-center justify-evenly gap-4 px-4 py-3 text-lg font-semibold tracking-wide"
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
        <!-- list icon button -->
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
        <!-- dots icon button -->
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
    <div
      v-else
      class="base-card border border-red-500 px-10 py-4 text-lg text-red-400"
    >
      <p>
        You have no subjects associated with your account for this academic year
        ({{ term.academic_year.name }})
      </p>
      <div class="mt-2 flex justify-end">
        <button
          class="rounded-lg border border-transparent bg-purple-100 p-2 text-base font-semibold tracking-wide text-purple-600 hover:bg-purple-200"
        >
          Add subject
        </button>
      </div>
    </div>
    <div
      v-if="currentSubject"
      class="flex w-full flex-col items-center justify-center gap-2"
    >
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
import {
  CheckCircleIcon,
  MinusCircleIcon,
  PlusCircleIcon,
} from '@heroicons/vue/outline';
import { usePage } from '@inertiajs/inertia-vue3';

import { defaultProps, changeAcademicYear } from '@/helpers';
import TimelineCard from '@/Components/TimelineCard.vue';
import ClassPanel from '@/Components/Class/Panel.vue';

defineProps({
  ...defaultProps,
  classes: Object,
  classModel: Object,
  studentsInClass: Array,
  subjects: Array,
  currentSubject: Object,
  gradesForCurrentSubjectWithStudent: Array,
});

const authUser = computed(() => usePage().props.value.auth.user);
const selectedTab = ref(1);
const shouldOpenModalContainingListOfClasses = ref(false);
const shouldOpenModalContainingListOfSubjects = ref(false);
</script>
