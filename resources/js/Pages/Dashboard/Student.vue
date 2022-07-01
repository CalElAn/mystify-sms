<template>
  <Head title="Dashboard"></Head>
  <!-- dashboard heading component -->
  <section v-if="!user.is_this_user_the_auth_user" class="flex items-center">
    <div
      class="flex flex-col gap-2 text-lg font-semibold text-gray-500 md:flex-row md:items-center md:text-2xl"
    >
      Student dashboard:
      <div
        class="flex items-center gap-1 text-lg tracking-wide text-fuchsia-600 md:text-xl"
      >
        <ProfilePicture
          :profilePicturePath="user.profile_picture_path"
          widthClass="w-10"
          heightClass="h-10"
        />
        {{ user.name }}
        <a class="ml-3" :href="'tel:' + user.phone_number">
          <PhoneIcon class="h-4 w-4 text-blue-700" />
        </a>
        <a class="ml-1" :href="'mailto:' + user.email">
          <MailIcon class="h-4 w-4 text-blue-700" />
        </a>
      </div>
    </div>
  </section>
  <section class="flex items-center gap-4">
    <ActionButtonAndModal
      v-if="user.is_this_user_the_auth_user"
      :actions="studentActions"
    />
    <button
      @click="shouldOpenModalContainingListOfParents = true"
      class="inline-flex items-center justify-center gap-2 rounded-lg border border-fuchsia-800 bg-fuchsia-50 px-2 py-1.5 font-semibold text-fuchsia-800 shadow-sm hover:bg-fuchsia-100"
    >
      <ViewListIcon class="h-5 w-5" />
      Parents
    </button>
  </section>
  <section class="flex items-center justify-evenly">
    <!-- class name and teacher -->
    <div
      class="base-card flex flex-col items-center justify-evenly gap-2 py-2 px-4 sm:w-max sm:flex-row sm:gap-0"
    >
      <div
        class="flex items-center justify-center gap-1.5 text-lg font-semibold tracking-tight text-purple-600 sm:text-xl"
      >
        {{ classModel.name_and_suffix }}
        <button
          @click="shouldOpenModalContainingListOfClasses = true"
          class="text-gray-700 hover:text-purple-600"
        >
          <ViewListIcon class="h-5 w-5" />
        </button>
      </div>
      <div
        class="mx-4 hidden sm:inline"
        style="height: 36px; width: 2px; background: #ddd"
      ></div>
      <div class="flex items-center gap-2">
        <div class="inline-block text-sm sm:text-base">Class teacher:</div>
        <div class="flex items-center justify-center gap-2">
          <ProfilePicture
            :profilePicturePath="classTeacher?.profile_picture_path"
            widthClass="w-14"
            heightClass="h-14"
          />
          <button
            @click="shouldOpenTeacherCardModal = true"
            class="inline-block font-semibold text-purple-600 underline underline-offset-1 sm:text-lg"
          >
            {{ classTeacher?.name }}
          </button>
        </div>
      </div>
    </div>
  </section>
  <section
    class="flex flex-col items-center gap-3 text-sm md:flex-row md:justify-evenly md:gap-4 lg:text-base xl:gap-0"
  >
    <!-- Position -->
    <div class="base-card flex w-64 flex-col p-2 lg:w-80">
      <div class="flex w-full gap-2">
        <div class="flex w-3/4 items-center justify-end">
          Position in class:
        </div>
        <div
          class="flex w-1/4 items-center justify-start gap-1 text-lg font-semibold text-purple-600 lg:text-xl"
        >
          {{ positionInClass }}
          <button
            @click="shouldOpenPositionsOfAllStudentsModal = true"
            class="text-gray-700 hover:text-purple-600"
          >
            <ViewListIcon class="h-5 w-5" />
          </button>
        </div>
      </div>
      <div class="flex w-full gap-2">
        <div class="flex w-3/4 items-center justify-end">
          Total number of students:
        </div>
        <div class="w-1/4 p-1 text-left text-xl font-semibold text-purple-600">
          {{ numberOfStudentsInClass }}
        </div>
      </div>
    </div>
    <!-- Average mark -->
    <div class="base-card flex w-64 gap-2 p-2 md:w-56">
      <div class="flex w-3/4 items-center justify-end">Average mark:</div>
      <div
        class="flex items-center justify-start p-2 text-xl font-semibold text-purple-600"
      >
        {{ averageMark }}%
      </div>
    </div>
    <!-- Average grade -->
    <div class="base-card flex w-64 gap-2 p-2 md:w-56">
      <div class="flex w-3/4 items-center justify-end">Average grade:</div>
      <div
        class="flex items-center justify-start p-2 text-xl font-semibold text-purple-600"
      >
        {{ gradeForAverageMark }}
      </div>
    </div>
  </section>
  <section class="sitems-center flex flex-col gap-6 lg:flex-row">
    <!-- Line chart -->
    <div class="base-card w-full p-2 lg:w-4/6">
      <LineChart
        :chartData="lineChartDataForGrades"
        :options="lineChartOptions"
      />
    </div>
    <!-- Doughnut chart -->
    <div
      class="base-card mx-auto flex flex-col justify-evenly p-3 md:w-3/5 lg:w-2/6"
    >
      <p class="p-2 text-center font-medium md:text-lg">
        Total school fees:
        <span class="text-2xl font-bold text-purple-600">
          {{ totalSchoolFees.toLocaleString() }}
        </span>
      </p>
      <DoughnutChart
        class="h-3/5"
        :chartData="doughnutChartData"
        :options="doughnutChartOptions"
      />
      <div class="flex items-center justify-between font-medium md:text-lg">
        <div class="text-center">
          <p class="text-center text-2xl font-bold text-[#36A2EB]">
            {{ totalSchoolFeesPaid.toLocaleString() }}
          </p>
          Total fees paid
        </div>
        <div class="text-center">
          <p class="text-center text-2xl font-bold text-[#FF6384]">
            {{ (totalSchoolFees - totalSchoolFeesPaid).toLocaleString() }}
          </p>
          Total fees remaining
        </div>
      </div>
    </div>
  </section>
  <section class="flex">
    <!-- Subjects and grades -->
    <div class="base-card w-full overflow-x-auto p-2">
      <table class="w-full table-auto text-center">
        <thead class="bg-purple-100 text-gray-500">
          <tr class="text-sm md:text-base">
            <th class="p-2 text-left md:pl-6">Subject</th>
            <th class="p-2">
              Class marks ({{ school.class_mark_percentage * 100 }}%)
            </th>
            <th class="p-2">
              Exam marks ({{ school.exam_mark_percentage * 100 }}%)
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
            class="text-sm odd:bg-white even:bg-gray-50 md:text-base"
          >
            <td class="p-2 text-left md:pl-6">
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
  <hr class="my-2" />
  <section class="flex flex-col gap-6 md:flex-row">
    <!-- Notifications -->
    <NotificationsCard
      v-if="user.is_this_user_the_auth_user"
      class="h-96 w-full md:w-1/2"
    />
    <!-- Notice board -->
    <TimelineCard
      :title="'Notice board'"
      :messages="noticeBoardMessages"
      class="h-96"
      :class="[user.is_this_user_the_auth_user ? 'md:w-1/2' : 'w-full']"
    />
  </section>
  <!-- MODALS -->
  <!-- Modal containing list of parents -->
  <Modal
    :show="shouldOpenModalContainingListOfParents"
    :maxWidthClass="'max-w-md'"
    @closeModal="shouldOpenModalContainingListOfParents = false"
  >
    <div class="flex flex-col gap-4">
      <UserCard
        v-for="(parent, parentIndex) in parents"
        :as="'div'"
        :key="parentIndex"
        :user="parent"
        class="border-purple-600"
      />
    </div>
  </Modal>
  <!-- Modal with teacher card -->
  <TeacherCardModal
    :teacher="classTeacher"
    :show="shouldOpenTeacherCardModal"
    @closeModal="shouldOpenTeacherCardModal = false"
  />
  <!-- Modal containing list of classes -->
  <Modal
    :show="shouldOpenModalContainingListOfClasses"
    :maxWidthClass="'max-w-xs'"
    @closeModal="shouldOpenModalContainingListOfClasses = false"
  >
    <div class="flex flex-col gap-3 text-purple-600">
      <Menu
        v-for="(classItem, classIndex) in classesWithTerms"
        :key="classIndex"
        as="div"
        class="relative inline-block rounded-full odd:border-gray-300 odd:bg-white even:bg-gray-100"
      >
        <MenuButton
          class="align-center inline-flex w-full items-center justify-center rounded-full border p-2 text-purple-600 hover:text-purple-400 hover:underline"
        >
          {{ classItem.class_model.name_and_suffix }}
          <ChevronDownIcon
            class="ml-1 -mr-1 h-5 w-5 text-purple-600 hover:text-purple-400"
            aria-hidden="true"
          />
        </MenuButton>
        <MenuItemsTransition>
          <MenuItems class="menu-items left-0 z-10 mt-2 w-full origin-top-left">
            <div class="px-1 py-1">
              <MenuItem
                as="div"
                v-for="(termItem, termIndex) in classItem.academic_year?.terms"
                :key="termIndex"
                v-slot="{ active }"
              >
                <MenuItemButton
                  @click="changeTerm(termItem.id)"
                  :active="active"
                >
                  {{ termItem.name }}
                </MenuItemButton>
              </MenuItem>
            </div>
          </MenuItems>
        </MenuItemsTransition>
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
    :maxWidthClass="'md:max-w-3xl'"
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
import { TrendingUpIcon, ChevronDownIcon } from '@heroicons/vue/solid';

import TimelineCard from '@/Components/TimelineCard.vue';
import NotificationsCard from '@/Components/Notifications/Card.vue';
import TeacherCardModal from '@/Components/Users/TeacherCardModal.vue';
import UserCard from '@/Components/Users/Card.vue';
import ActionButtonAndModal from '@/Components/ActionButtonAndModal.vue';

import { changeTerm } from '@/helpers';
import { defaultDashboardProps } from '@/default_dashboard_props.js';
import { useTeacherCardModal } from '@/Components/Users/teacher_card_modal.js';
import { studentActions } from '@/student_actions.js';

const { shouldOpenTeacherCardModal } = useTeacherCardModal();

Chart.register(...registerables);

const props = defineProps({
  ...defaultDashboardProps,
  parents: Array,
  classesWithTerms: Array,
  classModel: Object,
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
  subjectsAndGrades: [Array, Object],
});

const shouldOpenModalContainingListOfParents = ref(false);

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
      label: "Average class' performance over time ",
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
      //select the first subject in the "gradesDataPerSubjectForLineChart" object
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
      label: "Average class' performance over time ",
      //select the first subject in the "gradesDataPerSubjectForLineChart" object
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
  lineChartDataForGradesPerSubject.datasets[1].data =
    props.gradesDataPerSubjectForLineChart[
      subjectName
    ].gradesDataForOtherStudents;
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
      font: { weight: 'bold', size: 14 },
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
