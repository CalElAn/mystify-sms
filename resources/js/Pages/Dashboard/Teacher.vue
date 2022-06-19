<template>
  <!-- action button -->
  <section>
    <ActionButtonAndModal class="ml-14" :actions="teacherActions" />
  </section>
  <!-- Navbar -->
  <!-- <nav class="ml-12 border-b border-gray-300 text-gray-500">
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
  </nav> -->
  <ClassPanel
    v-if="classModel && selectedTab === 1"
    @openModalContainingListOfClasses="shouldOpenModalContainingListOfClasses = true"
    :classModel="classModel"
    :studentsInClass="studentsInClass"
    :academicYearName="term.academic_year.name"
  />
  <div
    v-if="!classModel && selectedTab === 1"
    class="flex items-center justify-center"
  >
    <div class="rounded-lg tracking-wide bg-red-300 px-10 py-4 text-lg text-white shadow">
      <p>
        You have no classes associated with your account for this academic year
        ({{ term.academic_year.name }})
      </p>
      <div class="mt-2 flex justify-end">
        <Link
          :href="route('class_teacher.form')"
          class="rounded-lg border border-fuchsia-600 bg-red-50 p-2 text-base font-semibold tracking-wide text-fuchsia-600 shadow-sm hover:bg-red-100"
        >
          Add class
        </Link>
      </div>
    </div>
  </div>
  <!-- <div class="base-card border tracking-wide border-red-500 px-10 py-4 text-lg text-red-400">
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
  </div> -->
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
        {{ classItem.name_and_suffix }}
      </button>
    </div>
  </Modal>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';

import { changeAcademicYear } from '@/helpers';
import { defaultDashboardProps } from '@/default_dashboard_props.js';
import { teacherActions } from '@/teacher_actions.js';
import ActionButtonAndModal from '@/Components/ActionButtonAndModal.vue';
import TimelineCard from '@/Components/TimelineCard.vue';
import ClassPanel from '@/Components/Classes/Panel.vue';

defineProps({
  ...defaultDashboardProps,
  classes: Object,
  classModel: Object,
  studentsInClass: Array,
  // subjects: Array,
  // currentSubject: Object,
  // gradesForCurrentSubjectWithStudent: Array,
});

const selectedTab = ref(1);
const shouldOpenModalContainingListOfClasses = ref(false);
</script>
