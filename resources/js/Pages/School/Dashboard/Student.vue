<template>
  <section class="flex justify-evenly">
    <!-- Position -->
    <div class="base-card flex w-72 flex-col p-2">
      <div class="flex w-full gap-2">
        <div class="flex w-3/4 items-center justify-end">
          Position in class:
        </div>
        <div
          class="flex w-1/4 items-center justify-start gap-1 p-1 text-xl font-semibold text-purple-600"
        >
          {{ props.positionInClass }}
          <button
            @click="shouldOpenPositionsOfAllStudentsModal = true"
            class="p-1 text-gray-700 hover:text-purple-600"
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
    <div class="base-card flex w-60 gap-2 p-2">
      <div class="flex w-3/4 items-center justify-end">Average mark:</div>
      <div
        class="flex items-center justify-start p-2 text-xl font-semibold text-purple-600"
      >
        {{ props.averageMark }}%
      </div>
    </div>
    <!-- Average grade -->
    <div class="base-card flex w-60 gap-2 p-2">
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
      <LineChart :chartData="lineChartDataForGrades" :options="lineChartOptions" />
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
                @click="openModalContainingGradesPerSubjectLineChart(item.subject_name)"
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
  <!-- positions and stats of all students modal -->
  <Modal
    :show="shouldOpenPositionsOfAllStudentsModal"
    @closeModal="shouldOpenPositionsOfAllStudentsModal = false"
  >
    <div class="mt-2 min-w-max overflow-auto">
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
    <LineChart :chartData="lineChartDataForGradesPerSubject" :options="lineChartOptionsForGradesPerSuject" />
  </Modal>
</template>

<script setup>
import { ref } from 'vue';
import { DoughnutChart, LineChart } from 'vue-chart-3';
import { Chart, registerables } from 'chart.js';
import { MenuIcon, TrendingUpIcon } from '@heroicons/vue/solid';

import TimelineCard from '@/Components/TimelineCard.vue';
import Modal from '@/Components/Modal.vue';

Chart.register(...registerables);

const props = defineProps({
  school: Object,
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
  setlineChartDataAndOptionsForGradesPerSubject(subjectName)
  shouldOpenModalContainingGradesPerSubjectLineChart.value = true
}
//TODO google how to get first item in object, maybe Lodash has a method
const lineChartDataForGradesPerSubject = {
    datasets: [
    {
      label: "Student's performance over time ",
      data: props.gradesDataPerSubjectForLineChart[Object.keys(props.gradesDataPerSubjectForLineChart)[0]].gradesDataForStudent,
      borderColor: 'rgb(255, 99, 132)',
      backgroundColor: 'rgb(255, 99, 132, 0.5)',
      pointStyle: 'circle',
      pointRadius: 8,
      pointHoverRadius: 13,
    },
    {
      label: "Class' performance over time ",
      data: props.gradesDataPerSubjectForLineChart[Object.keys(props.gradesDataPerSubjectForLineChart)[0]].gradesDataForOtherStudents,
      borderColor: 'rgb(54, 162, 235)',
      backgroundColor: 'rgb(54, 162, 235, 0.5)',
      pointStyle: 'circle',
      pointRadius: 8,
      pointHoverRadius: 13,
    },
  ]}

function setlineChartDataAndOptionsForGradesPerSubject(subjectName) {
  lineChartDataForGradesPerSubject.datasets[0].data = props.gradesDataPerSubjectForLineChart[subjectName].gradesDataForStudent
  lineChartOptionsForGradesPerSuject.plugins.title.text = subjectName 
};

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
      text: '' //TODO increase size of text
    }
  }
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
