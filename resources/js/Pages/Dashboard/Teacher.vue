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
    @openModalContainingListOfClasses="
      shouldOpenModalContainingListOfClasses = true
    "
    :classModel="classModel"
    :studentsInClass="studentsInClass"
    :academicYearName="term.academic_year.name"
  />
  <section
    v-if="!classModel && selectedTab === 1"
    class="flex items-center justify-center"
  >
    <InfoCard
      :mainText="`You have no classes associated with your account for this academic year
        (${term.academic_year.name})`"
      linkText="Add class"
      :linkHref="route('class_teacher.form')"
    />
  </section>
  <hr class="my-2" />
  <section v-if="user.is_this_user_the_auth_user" class="flex gap-6">
    <!-- Notice board -->
    <TimelineCard
      :title="'Notice board'"
      :messages="noticeBoardMessages"
      class="h-96 w-1/2"
    />
    <!-- Notifications -->
    <NotificationsCard class="h-96 w-1/2" />
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

import { changeAcademicYear } from '@/helpers';
import { defaultDashboardProps } from '@/default_dashboard_props.js';
import { teacherActions } from '@/teacher_actions.js';
import ActionButtonAndModal from '@/Components/ActionButtonAndModal.vue';
import TimelineCard from '@/Components/TimelineCard.vue';
import NotificationsCard from '@/Components/NotificationsCard.vue';
import ClassPanel from '@/Components/Classes/Panel.vue';
import InfoCard from '@/Components/InfoCard.vue';

defineProps({
  ...defaultDashboardProps,
  classes: Object,
  classModel: Object,
  studentsInClass: Array,
});

const selectedTab = ref(1);
const shouldOpenModalContainingListOfClasses = ref(false);
</script>
