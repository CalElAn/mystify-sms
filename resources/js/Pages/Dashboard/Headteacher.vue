<template>
  <!-- Summary statistics -->
  <section class="grid grid-cols-4 gap-3 pt-2 font-semibold text-white">
    <Link
      :href="route('users.index', 'students')"
      class="flex flex-col gap-2 rounded-lg bg-purple-400 py-4 px-6 shadow hover:cursor-pointer hover:shadow-md"
    >
      <p class="">Total students</p>
      <div class="flex items-center justify-between text-3xl font-bold">
        <AcademicCapIcon class="h-12 w-12" />
        {{ numberOfStudents }}
      </div>
    </Link>
    <Link
      :href="route('users.index', 'parents')"
      class="flex flex-col gap-2 rounded-lg bg-fuchsia-400 py-4 px-6 shadow hover:cursor-pointer hover:shadow-md"
    >
      <p class="">Total parents</p>
      <div class="flex items-center justify-between text-3xl font-bold">
        <UserGroupIcon class="h-12 w-12" />
        {{ numberOfParents }}
      </div>
    </Link>
    <Link
      :href="route('users.index', 'teachers')"
      class="flex flex-col gap-2 rounded-lg bg-violet-400 py-4 px-6 shadow hover:cursor-pointer hover:shadow-md"
    >
      <p class="">Total teachers</p>
      <div class="flex items-center justify-between text-3xl font-bold">
        <UsersIcon class="h-12 w-12" />
        {{ numberOfTeachers }}
      </div>
    </Link>
    <Link
      :href="route('users.index', 'administrators')"
      class="flex flex-col gap-2 rounded-lg bg-pink-400 py-4 px-6 shadow hover:cursor-pointer hover:shadow-md"
    >
      <p class="">Total administrators</p>
      <div class="flex items-center justify-between text-3xl font-bold">
        <ArchiveIcon class="h-12 w-12" />
        {{ numberOfAdministrators }}
      </div>
    </Link>
  </section>
  <section class="flex gap-6">
    <!-- Line chart -->
    <div class="base-card w-4/6 p-2">
      <LineChart :chartData="lineChartData" :options="lineChartOptions" />
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
          <p class="text-center text-2xl font-bold text-[#36A2EB]">
            {{ props.totalSchoolFeesCollected.toLocaleString() }}
          </p>
          Total fees collected
        </div>
        <div>
          <button
            @click="
              shouldOpenModalContainingListOfStudentsWhoOweSchoolFees = true
            "
            class="flex w-full items-center gap-2 text-center text-2xl font-bold text-[#FF6384] underline hover:opacity-75"
          >
            <ViewListIcon class="h-5 w-5" />
            {{
              (
                props.totalSchoolFees - props.totalSchoolFeesCollected
              ).toLocaleString()
            }}
          </button>
          Total fees remaining
        </div>
      </div>
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
  <!-- MODALS -->
  <!-- Modal containing list of students who owe school fees -->
  <Modal
    :show="shouldOpenModalContainingListOfStudentsWhoOweSchoolFees"
    :maxWidthClass="'max-w-md'"
    @closeModal="
      shouldOpenModalContainingListOfStudentsWhoOweSchoolFees = false
    "
  >
    <table class="w-full text-sm table-auto text-center">
      <thead class="thead">
        <tr>
          <th class="p-2"></th>
          <th class="p-2">Name</th>
          <th class="p-2">Amount owed</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(student, index) in studentsWhoOweSchoolFees"
          :key="index"
          class="tbody"
        >
          <td class="flex justify-center p-1.5">
            <ProfilePicture
              :profilePicturePath="student.profile_picture_path"
              widthClass="w-9"
              heightClass="h-9"
            />
          </td>
          <td class="p-2 text-left">
            <Link
              class="tda"
              :href="route('dashboard', { userId: student.id })"
            >
              {{ student.name }}
            </Link>
          </td>
          <td class="p-2 text-[#FF6384] font-semibold">
            {{ student.amountOwed }}
          </td>
        </tr>
        <tr v-if="studentsWhoOweSchoolFees.length === 0">
          <td class="border-t px-6 py-4" colspan="3">
            No students found owing school fees.
          </td>
        </tr>
      </tbody>
    </table>
  </Modal>
</template>

<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import { DoughnutChart, LineChart } from 'vue-chart-3';
import { Chart, registerables } from 'chart.js';
import {
  UsersIcon,
  AcademicCapIcon,
  UserGroupIcon,
  ArchiveIcon,
} from '@heroicons/vue/outline';
import TimelineCard from '@/Components/TimelineCard.vue';
import { defaultProps } from '@/helpers';

Chart.register(...registerables);

const props = defineProps({
  ...defaultProps,
  numberOfStudents: Number,
  numberOfParents: Number,
  numberOfTeachers: Number,
  numberOfAdministrators: Number,
  schoolFeesDataForLineChart: Array,
  totalSchoolFees: Number,
  totalSchoolFeesCollected: Number,
  studentsWhoOweSchoolFees: Array,
});

const user = computed(() => usePage().props.value.auth.user);

const shouldOpenModalContainingListOfStudentsWhoOweSchoolFees = ref(false);

const lineChartData = {
  datasets: [
    {
      label: 'Fees collected',
      data: props.schoolFeesDataForLineChart,
      borderColor: 'rgb(255, 99, 132)',
      backgroundColor: 'rgb(255, 99, 132, 0.5)',
      pointStyle: 'circle',
      pointRadius: 8,
      pointHoverRadius: 13,
    },
  ],
};
const lineChartOptions = {
  parsing: {
    xAxisKey: 'weekName',
    yAxisKey: 'sumOfAmount',
  },
  plugins: {
    tooltip: {
      callbacks: {
        label: function (context) {
          return context.raw.startOfWeekFormat;
        },
      },
    },
  },
};

const doughnutChartData = {
  labels: ['Total school fees remaining', 'Total school fees collected'],
  datasets: [
    {
      data: [
        props.totalSchoolFees - props.totalSchoolFeesCollected,
        props.totalSchoolFeesCollected,
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
