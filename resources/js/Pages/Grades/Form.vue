<template>
  <NavBar :actions="teacherActions" />
  <section class="flex basis-full items-start justify-center">
    <div class="base-card w-full p-4 px-8">
      <p class="form-title mt-2 text-center">Grades</p>
      <div class="mt-6 grid w-1/2 grid-cols-5 gap-y-4">
        <label class="label col-span-2">Subject:</label>
        <select
          @change="getGrades()"
          class="custom-select col-span-3"
          v-model="subjectName"
        >
          <option value="" selected disabled>- select a subject -</option>
          <option :value="item.name" v-for="item in subjects" :key="item">
            {{ item.name }}
          </option>
        </select>
        <label class="label col-span-2">Class:</label>
        <select
          @change="getGrades()"
          class="custom-select col-span-3"
          v-model="classId"
        >
          <option value="" selected disabled>- select a class -</option>
          <option :value="item.id" v-for="item in classes" :key="item">
            {{ item.name_and_suffix }}
          </option>
        </select>
        <label class="label col-span-2">Academic year:</label>
        <select
          @change="getTerms()"
          class="custom-select col-span-3"
          v-model="academicYearId"
        >
          <option value="" selected disabled>
            - select an academic year -
          </option>
          <option :value="item.id" v-for="item in academicYears" :key="item">
            {{ item.name }}
          </option>
        </select>
        <label class="label col-span-2">Terms:</label>
        <select
          @change="getGrades()"
          class="custom-select col-span-3"
          v-model="termId"
        >
          <option value="" selected disabled>- select a term -</option>
          <option :value="item.id" v-for="item in terms" :key="item">
            {{ item.name }}
          </option>
        </select>
      </div>
      <section class="mt-4 flex flex-col items-center justify-center gap-4">
        <div class="w-full py-2">
          <p
            class="flex items-center justify-center gap-2 p-1 text-xl font-semibold tracking-wide text-gray-600"
          >
            Grades
            <span class="text-base">
              ({{ selected_subject_class_and_academicYear }})
            </span>
          </p>
          <table class="w-full table-auto text-left">
            <thead class="thead">
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
                v-for="(item, index) in studentsWithGrades"
                :key="item"
                class="tbody"
              >
                <td class="flex justify-center p-2">
                  <ProfilePicture
                    :profilePicturePath="item.profile_picture_path"
                    widthClass="w-10"
                    heightClass="h-10"
                  />
                </td>
                <td class="p-2">
                  <Link
                    class="decoration-purple-600 hover:underline"
                    :href="route('users.show', { userId: item.id })"
                    >{{ item.name }}</Link
                  >
                </td>
                <td class="px-3">
                  <input
                    class="custom-input w-full text-center shadow-sm"
                    type="number"
                    min="0"
                    :max="school.class_mark_percentage * 100"
                    v-model="item.nonEmptyGrades.class_mark"
                  />
                </td>
                <td class="px-3">
                  <input
                    class="custom-input w-full text-center shadow-sm"
                    type="number"
                    min="0"
                    :max="school.exam_mark_percentage * 100"
                    v-model="item.nonEmptyGrades.exam_mark"
                  />
                </td>
                <td class="p-2">
                  <button  v-if="academicYearId === defaultAcademicYear.id" class="p-2" @click="clearMarks(item)">
                    <TrashIcon class="h-5 w-5 text-red-500" />
                  </button>
                </td>
              </tr>
              <tr v-if="!studentsWithGrades || studentsWithGrades.length === 0">
                <td class="border-t px-6 py-4" colspan="4">
                  No students found in
                  {{
                    classes.find((item) => item.id === classId)?.name_and_suffix
                  }}
                  ({{
                    terms.find((item) => item.id === termId)
                      ?.formatted_short_name
                  }}).
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <button
          v-if="academicYearId === defaultAcademicYear.id"
          @click="save()"
          class="flex w-5/6 items-center justify-center gap-2 rounded-lg bg-purple-400 p-2 text-lg font-semibold tracking-wide text-white shadow-sm hover:bg-purple-500"
        >
          <CheckCircleIcon class="h-6 w-6" />
          Save
        </button>
      </section>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue';
import { TrashIcon, CheckCircleIcon } from '@heroicons/vue/outline';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/inertia-vue3';

import NavBar from '@/Components/ActionsNavBar.vue';
import { teacherActions } from '@/teacher_actions.js';

const authUser = computed(() => usePage().props.value.auth.user);
const school = computed(() => usePage().props.value.school);

const props = defineProps({
  subjects: Array,
  classes: Array,
  academicYears: Array,
  defaultAcademicYear: Object,
});

const subjectName = ref('');
const classId = ref('');
const academicYearId = ref('');
const termId = ref('');

const terms = ref([]);
const studentsWithGrades = ref([]);

const selected_subject_class_and_academicYear = computed(() => {
  return `${subjectName.value} | ${
    props.classes.find((item) => item.id === classId.value)?.name_and_suffix ??
    ''
  } | ${
    terms.value.find((item) => item.id === termId.value)
      ?.formatted_short_name ?? ''
  }`;
});

function getTerms() {
  axios
    .get(route('academic_years.terms', academicYearId.value))
    .then((response) => {
      terms.value = response.data.terms;
      termId.value = terms.value[0]?.id;
      getGrades();
    });
}

function getGrades() {
  if (!classId.value) classId.value = _.first(props.classes)?.id;

  if (!academicYearId.value) {
    academicYearId.value = _.first(props.academicYears)?.id;
    getTerms();
  }

  if (!subjectName.value || !classId.value || !termId.value) return;
  axios
    .get(
      route('grades.students_with_grades', {
        subjectName: subjectName.value,
        classModel: classId.value,
        term: termId.value,
      })
    )
    .then((response) => {
      studentsWithGrades.value = response.data.studentsWithGrades;
    });
}

function clearMarks(item) {
  //TODO notify
  item.nonEmptyGrades.class_mark = null;
  item.nonEmptyGrades.exam_mark = null;
}

function save() {
  //TODO notify
  const grades = studentsWithGrades.value.map((item) => ({
    teacher_id: authUser.value.id,
    class_mark: item.nonEmptyGrades.class_mark,
    exam_mark: item.nonEmptyGrades.exam_mark,
    student_id: item.nonEmptyGrades.student_id,
    school_id: item.nonEmptyGrades.school_id,
    subject_name: item.nonEmptyGrades.subject_name,
    class_name: item.nonEmptyGrades.class_name,
    class_suffix: item.nonEmptyGrades.class_suffix,
    term_id: item.nonEmptyGrades.term_id,
  }));
  //TODO on start disable save button and change text to 'saving', enable on finish
  Inertia.patch(route('grades.upsert'), { grades: grades });
}
</script>
