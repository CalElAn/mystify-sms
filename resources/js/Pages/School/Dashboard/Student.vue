<template>
  <section class="flex items-center justify-evenly">
    <!-- class name and teacher -->
    <div class="base-card flex w-[30rem] items-center justify-evenly p-2">
      <div
        class="flex items-center justify-center gap-1.5 text-xl font-semibold tracking-tight text-purple-600"
      >
        {{ props.classObject.name }} {{ props.classObject.suffix }}
        <button
          @click="shouldOpenModalContainingListOfClasses = true"
          class="text-gray-700 hover:text-purple-600"
        >
          <MenuIcon class="h-5 w-5" />
        </button>
      </div>
      <div
        style="height: 36px; width: 2px; background: #ddd; display: inline"
      ></div>
      <div class="flex items-center gap-2">
        <div class="inline-block">Class teacher:</div>
        <div class="flex items-center justify-center gap-2">
          <div
            class="h-14 w-14 place-self-center rounded-full bg-contain bg-center bg-no-repeat"
            :style="{
              'background-image':
                'url(' + getProfilePictureUrl(props.classTeacher) + ')',
            }"
            alt="profile picture"
          ></div>
          <!-- //TODO href should lead to profile -->
          <a
            href="#"
            class="inline-block text-xl font-semibold text-purple-600 underline underline-offset-1"
          >
            {{ props.classTeacher?.name }}
          </a>
        </div>
      </div>
    </div>
  </section>
  <section class="flex justify-evenly">
    <!-- Position -->
    <div class="base-card flex w-80 flex-col p-2">
      <div class="flex w-full gap-2">
        <div class="flex w-3/4 items-center justify-end">
          Position in class:
        </div>
        <div
          class="flex w-1/4 items-center justify-start gap-1 text-xl font-semibold text-purple-600"
        >
          {{ props.positionInClass }}
          <button
            @click="shouldOpenPositionsOfAllStudentsModal = true"
            class="text-gray-700 hover:text-purple-600"
          >
            <MenuIcon class="h-5 w-5" />
          </button>
        </div>
      </div>
      <div class="flex w-full gap-2">
        <div class="flex w-3/4 items-center justify-end">
          Total number of students:
        </div>
        <div class="w-1/4 p-1 text-left text-xl font-semibold text-purple-600">
          {{ props.numberOfStudentsInClass }}
        </div>
      </div>
    </div>
    <!-- Average mark -->
    <div class="base-card flex w-56 gap-2 p-2">
      <div class="flex w-3/4 items-center justify-end">Average mark:</div>
      <div
        class="flex items-center justify-start p-2 text-xl font-semibold text-purple-600"
      >
        {{ props.averageMark }}%
      </div>
    </div>
    <!-- Average grade -->
    <div class="base-card flex w-56 gap-2 p-2">
      <div class="flex w-3/4 items-center justify-end">Average grade:</div>
      <div
        class="flex items-center justify-start p-2 text-xl font-semibold text-purple-600"
      >
        {{ props.gradeForAverageMark }}
      </div>
    </div>
  </section>
  <section class="flex gap-6">
    <!-- Line chart -->
    <div class="base-card w-4/6 p-2">
      <LineChart
        :chartData="lineChartDataForGrades"
        :options="lineChartOptions"
      />
    </div>
    <!-- Doughnut chart -->
    <div class="base-card flex w-2/6 flex-col justify-evenly p-3">
      <p class="p-2 text-center text-lg font-medium">
        Total school fees:
        <span class="text-2xl font-bold text-purple-600">
          {{ props.totalSchoolFees.toLocaleString() }}
        </span>
      </p>
      <DoughnutChart
        class="h-3/5"
        :chartData="doughnutChartData"
        :options="doughnutChartOptions"
      />
      <div class="flex items-center justify-between text-lg font-medium">
        <div>
          <p class="text-center text-3xl font-bold text-[#36A2EB]">
            {{ props.totalSchoolFeesPaid.toLocaleString() }}
          </p>
          Total fees paid
        </div>
        <div>
          <p class="text-center text-3xl font-bold text-[#FF6384]">
            {{
              (
                props.totalSchoolFees - props.totalSchoolFeesPaid
              ).toLocaleString()
            }}
          </p>
          Total fees remaining
        </div>
      </div>
    </div>
  </section>
  <section class="flex gap-6">
    <!-- Subjects and grades -->
    <div class="base-card w-full p-2">
      <table class="w-full table-auto text-center">
        <thead class="bg-purple-100 text-gray-500">
          <tr>
            <th class="p-2 pl-6 text-left">Subject</th>
            <th class="p-2">
              Class marks ({{ props.school.class_mark_percentage * 100 }}%)
            </th>
            <th class="p-2">
              Exam marks ({{ props.school.exam_mark_percentage * 100 }}%)
            </th>
            <th class="p-2">Overall (100%)</th>
            <th class="p-2">Grade</th>
            <th class="p-2">Position</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(item, index) in subjectsAndGrades"
            :key="index"
            class="odd:bg-white even:bg-gray-50"
          >
            <td class="p-2 pl-6 text-left">
              {{ item.subject_name }}
              <button
                @click="
                  openModalContainingGradesPerSubjectLineChart(
                    item.subject_name
                  )
                "
                class="ml-3 rounded-md border border-purple-600 p-0.5 text-purple-600 hover:bg-purple-600 hover:text-white"
              >
                <TrendingUpIcon class="h-3 w-3" />
              </button>
            </td>
            <td class="p-2">{{ item.class_mark }}</td>
            <td class="p-2">{{ item.exam_mark }}</td>
            <td class="p-2">{{ item.overall_mark }}</td>
            <td class="p-2">{{ item.overall_grade }}</td>
            <td class="p-2">{{ item.position }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
  <section class="flex gap-6">
    <!-- Notice board -->
    <TimelineCard
      :title="'Notice board'"
      :messages="props.noticeBoardMessages"
      class="w-2/5"
    />
    <!-- Notifications -->
    <TimelineCard
      :title="'Notifications'"
      :messages="props.noticeBoardMessages"
      class="w-2/5"
    />
    <!-- Recent activities -->
    <TimelineCard
      :title="'Recent activities'"
      :messages="props.noticeBoardMessages"
      class="w-1/5"
    />
  </section>
  <!-- Modal containing list of classes -->
  <Modal
    :show="shouldOpenModalContainingListOfClasses"
    :maxWidthClass="'max-w-xs'"
    @closeModal="shouldOpenModalContainingListOfClasses = false"
  >
    <div class="flex flex-col gap-3 text-purple-600">
      <Menu v-for="(classItem, classIndex) in props.listOfClasses" :key="classIndex" as="div" class="relative inline-block rounded-full odd:border-gray-300 odd:bg-white even:bg-gray-100">
        <MenuButton
          class="inline-flex w-full items-center justify-center rounded-full align-center p-2 hover:underline border text-purple-600 hover:text-purple-400"
        >
          {{ classItem.class_model.name }}
          <ChevronDownIcon
            class="ml-1 -mr-1 h-5 w-5 text-purple-600 hover:text-purple-400"
            aria-hidden="true"
          />
        </MenuButton>
        <transition
          enter-active-class="transition duration-100 ease-out"
          enter-from-class="transform scale-95 opacity-0"
          enter-to-class="transform scale-100 opacity-100"
          leave-active-class="transition duration-75 ease-in"
          leave-from-class="transform scale-100 opacity-100"
          leave-to-class="transform scale-95 opacity-0"
        >
          <MenuItems
            class="absolute left-0 z-10 mt-2 w-full origin-top-left divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
          >
            <div class="px-1 py-1">
              <MenuItem
                v-for="(termItem, termIndex) in classItem.terms"
                :key="termIndex"
                v-slot="{ active }"
              >
                <button
                  :class="[
                    active ? 'bg-violet-500 text-white' : 'text-gray-900',
                    'group flex w-full items-center justify-center rounded-md px-2 py-2 text-sm',
                  ]"
                  @click="changeTerm(termItem.term_id)"
                >
                  {{ termItem.name }}
                </button>
              </MenuItem>
            </div>
          </MenuItems>
        </transition>
      </Menu>
    </div>
  </Modal>
  <!-- positions and stats of all students modal -->
  <Modal
    :show="shouldOpenPositionsOfAllStudentsModal"
    @closeModal="shouldOpenPositionsOfAllStudentsModal = false"
  >
    <div class="mt-2 min-w-max overflow-y-auto">
      <table class="table-auto text-center text-sm">
        <thead class="bg-purple-100 text-gray-500">
          <tr>
            <th class="p-2 text-left">Position</th>
            <th class="p-2 pl-6 text-left">Name</th>
            <th class="p-2">Average mark</th>
            <th class="p-2">Average grade</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(item, index) in positionStatisticsOfAllStudentsInClass"
            :key="index"
            class="odd:bg-white even:bg-gray-50"
          >
            <td class="p-2 pl-4 text-left">{{ item.position }}</td>
            <td class="p-2 pl-6 text-left">{{ item.name }}</td>
            <td class="p-2">{{ item.averageMark }}%</td>
            <td class="p-2">{{ item.averageGrade }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </Modal>
  <!-- line chart modal for grades per subject -->
  <Modal
    :show="shouldOpenModalContainingGradesPerSubjectLineChart"
    :maxWidthClass="'max-w-3xl'"
    @closeModal="shouldOpenModalContainingGradesPerSubjectLineChart = false"
  >
    <LineChart
      :chartData="lineChartDataForGradesPerSubject"
      :options="lineChartOptionsForGradesPerSuject"
    />
  </Modal>
</template>

<script setup>
import { ref } from 'vue';
import { DoughnutChart, LineChart } from 'vue-chart-3';
import { Chart, registerables } from 'chart.js';
import { MenuIcon, TrendingUpIcon, ChevronDownIcon } from '@heroicons/vue/solid';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';

import TimelineCard from '@/Components/TimelineCard.vue';
import Modal from '@/Components/Modal.vue';

import { getProfilePictureUrl, changeTerm } from '@/helpers';

Chart.register(...registerables);

const props = defineProps({
  school: Object,
  classObject: Object,
  listOfClasses: Array,
  classTeacher: Object,
  gradesDataForLineChart: Object,
  gradesDataPerSubjectForLineChart: Object,
  totalSchoolFees: Number,
  totalSchoolFeesPaid: Number,
  positionInClass: String,
  positionStatisticsOfAllStudentsInClass: Array,
  numberOfStudentsInClass: Number,
  averageMark: Number,
  gradeForAverageMark: String,
  subjectsAndGrades: Array,
  noticeBoardMessages: Object,
});

const shouldOpenModalContainingListOfClasses = ref(false);

const shouldOpenPositionsOfAllStudentsModal = ref(false);
const shouldOpenModalContainingGradesPerSubjectLineChart = ref(false);

const lineChartDataForGrades = {
  datasets: [
    {
      label: "Student's performance over time ",
      data: props.gradesDataForLineChart.gradesDataForStudent,
      borderColor: 'rgb(255, 99, 132)',
      backgroundColor: 'rgb(255, 99, 132, 0.5)',
      pointStyle: 'circle',
      pointRadius: 8,
      pointHoverRadius: 13,
    },
    {
      label: "Class' performance over time ",
      data: props.gradesDataForLineChart.gradesDataForOtherStudents,
      borderColor: 'rgb(54, 162, 235)',
      backgroundColor: 'rgb(54, 162, 235, 0.5)',
      pointStyle: 'circle',
      pointRadius: 8,
      pointHoverRadius: 13,
    },
  ],
};

function openModalContainingGradesPerSubjectLineChart(subjectName) {
  setlineChartDataAndOptionsForGradesPerSubject(subjectName);
  shouldOpenModalContainingGradesPerSubjectLineChart.value = true;
}

const lineChartDataForGradesPerSubject = {
  datasets: [
    {
      label: "Student's performance over time ",
      data: props.gradesDataPerSubjectForLineChart[
        Object.keys(props.gradesDataPerSubjectForLineChart)[0]
      ].gradesDataForStudent,
      borderColor: 'rgb(255, 99, 132)',
      backgroundColor: 'rgb(255, 99, 132, 0.5)',
      pointStyle: 'circle',
      pointRadius: 8,
      pointHoverRadius: 13,
    },
    {
      label: "Class' performance over time ",
      data: props.gradesDataPerSubjectForLineChart[
        Object.keys(props.gradesDataPerSubjectForLineChart)[0]
      ].gradesDataForOtherStudents,
      borderColor: 'rgb(54, 162, 235)',
      backgroundColor: 'rgb(54, 162, 235, 0.5)',
      pointStyle: 'circle',
      pointRadius: 8,
      pointHoverRadius: 13,
    },
  ],
};

function setlineChartDataAndOptionsForGradesPerSubject(subjectName) {
  lineChartDataForGradesPerSubject.datasets[0].data =
    props.gradesDataPerSubjectForLineChart[subjectName].gradesDataForStudent;
  lineChartOptionsForGradesPerSuject.plugins.title.text = subjectName;
}

const lineChartOptions = {
  parsing: {
    xAxisKey: 'class_name_and_suffix',
    yAxisKey: 'average_mark',
  },
};

const lineChartOptionsForGradesPerSuject = {
  parsing: {
    xAxisKey: 'class_name_and_suffix',
    yAxisKey: 'average_mark',
  },
  plugins: {
    title: {
      display: true,
      font: {weight: 'bold', size: 14},
      text: '',
    },
  },
};

const doughnutChartData = {
  labels: ['Total school fees remaining', 'Total school fees paid'],
  datasets: [
    {
      data: [
        props.totalSchoolFees - props.totalSchoolFeesPaid,
        props.totalSchoolFeesPaid,
      ],
      backgroundColor: [
        'rgb(255, 99, 132)', //red
        'rgb(54, 162, 235)', //blue
      ],
      hoverOffset: 5,
    },
  ],
};
const doughnutChartOptions = {
  plugins: {
    legend: {
      display: false,
      // position: 'bottom',
      // align: 'start',
      // reverse: true,
    },
  },
};
</script>
