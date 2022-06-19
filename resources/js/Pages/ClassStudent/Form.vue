<template>
  <NavBar :actions="teacherActions" />
  <section class="flex basis-full items-start justify-center">
    <div class="base-card w-full p-4 px-8">
      <p class="form-title mt-2 text-center">Students</p>
      <div class="mt-6 grid w-1/2 grid-cols-5 gap-y-4">
        <label class="label col-span-2">Class:</label>
        <select
          @change="getStudents"
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
          @change="getStudents"
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
      </div>
      <nav>
        <ul
          class="mt-12 flex flex-row gap-4 border-b border-gray-200 font-medium text-gray-600"
        >
          <li>
            <button
              @click="selectedTab = 1"
              :class="[
                selectedTab === 1 ? activeTabClasses : 'hover:bg-gray-50',
              ]"
              class="rounded-t-xl py-2 px-4 tracking-wide"
            >
              Students in class
            </button>
          </li>
          <li>
            <button
              @click="selectedTab = 2"
              :class="[
                selectedTab === 2 ? activeTabClasses : 'hover:bg-gray-50',
              ]"
              class="rounded-t-xl py-2 px-4 tracking-wide"
            >
              Requests to join class
            </button>
          </li>
          <li>
            <button
              @click="selectedTab = 3"
              :class="[
                selectedTab === 3 ? activeTabClasses : 'hover:bg-gray-50',
              ]"
              class="rounded-t-xl py-2 px-4 tracking-wide"
            >
              Remove students from class
            </button>
          </li>
        </ul>
      </nav>
      <section
        v-if="selectedTab === 1 && classId && academicYearId"
        class="flex justify-center"
      >
        <UserTable
          class="mt-4 w-full"
          :title="`Students in ${selectedClassNameAndSuffix} (${selectedAcademicYearName})`"
          :users="students"
        />
      </section>
      <section
        class="mt-4 flex flex-col items-center justify-center gap-1"
        v-if="selectedTab === 3 && classId && academicYearId"
      >
        <div
          v-for="student in students"
          :key="student"
          class="flex w-80 justify-between p-2 odd:bg-white even:bg-gray-50"
        >
          <div class="flex items-center gap-2">
            <ProfilePicture
              :profilePicturePath="student.profile_picture_path"
              widthClass="w-10"
              heightClass="h-10"
            />
            <Link
              class="tda"
              :href="route('dashboard', { userId: student.id })"
            >
              {{ student.name }}
            </Link>
          </div>
          <button @click="destroy(student.pivot.id)" class="p-2">
            <TrashIcon class="h-5 w-5 text-red-500" />
          </button>
        </div>
      </section>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue';
import { TrashIcon } from '@heroicons/vue/outline';

import NavBar from '@/Components/ActionsNavBar.vue';
import UserTable from '@/Components/Users/Table.vue';
import { teacherActions } from '@/teacher_actions.js';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
  classStudentData: Array,
  classes: Array,
  academicYears: Array,
});

const activeTabClasses = ref('text-blue-600 bg-gray-100 font-semibold');
const selectedTab = ref(1);
const classId = ref('');
const academicYearId = ref('');
const students = ref([]);

const selectedClassNameAndSuffix = computed(() => {
  return (
    props.classes.find((item) => item.id === classId.value)?.name_and_suffix ??
    ''
  );
});

const selectedAcademicYearName = computed(() => {
  return (
    props.academicYears.find((item) => item.id === academicYearId.value)
      ?.name ?? ''
  );
});

function getStudents() {
  if (!academicYearId.value) {
    academicYearId.value = _.first(props.academicYears)?.id;
  }

  if (!classId.value || !academicYearId.value) return;

  axios
    .get(
      route('class_student.students', {
        classModel: classId.value,
        academicYear: academicYearId.value,
      })
    )
    .then((response) => {
      students.value = response.data.students;
    });
}

function destroy(class_student_id) {
  //TODO notify should delete
  Inertia.delete(route('class_student.destroy', class_student_id), {
    onSuccess: () => {
      //TODO notify deleted
      _.remove(students.value, function (item) {
        return item.pivot.id === class_student_id;
      });
    },
  });
}
</script>
