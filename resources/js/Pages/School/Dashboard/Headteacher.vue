<template>
  <!-- Summary statistics -->
  <section class="grid grid-cols-4 gap-3 font-semibold text-white">
    <div
      class="flex flex-col gap-2 rounded-lg bg-purple-400 py-4 px-6 shadow hover:cursor-pointer hover:shadow-md"
    >
      <p class="">Total students</p>
      <div class="flex items-center justify-between text-3xl font-bold">
        <AcademicCapIcon class="h-12 w-12" />
        {{ numberOfStudents }}
      </div>
    </div>
    <div
      class="flex flex-col gap-2 rounded-lg bg-fuchsia-400 py-4 px-6 shadow hover:cursor-pointer hover:shadow-md"
    >
      <p class="">Total parents</p>
      <div class="flex items-center justify-between text-3xl font-bold">
        <UserGroupIcon class="h-12 w-12" />
        {{ numberOfParents }}
      </div>
    </div>
    <div
      class="flex flex-col gap-2 rounded-lg bg-violet-400 py-4 px-6 shadow hover:cursor-pointer hover:shadow-md"
    >
      <p class="">Total teachers</p>
      <div class="flex items-center justify-between text-3xl font-bold">
        <UsersIcon class="h-12 w-12" />
        {{ numberOfTeachers }}
      </div>
    </div>
    <div
      class="flex flex-col gap-2 rounded-lg bg-pink-400 py-4 px-6 shadow hover:cursor-pointer hover:shadow-md"
    >
      <p class="">Total administrators</p>
      <div class="flex items-center justify-between text-3xl font-bold">
        <ArchiveIcon class="h-12 w-12" />
        {{ numberOfAdministrators }}
      </div>
    </div>
  </section>
  <section class="flex gap-6">
    <!-- Line chart -->
    <div class="w-4/6 base-card p-2">
      <LineChart :chartData="lineChartData" :options="lineChartOptions" />
    </div>
    <!-- Doughnut chart -->
    <div
      class="flex w-2/6 flex-col justify-evenly base-card p-3"
    >
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
            {{ props.totalSchoolFeesCollected.toLocaleString() }}
          </p>
          Total fees collected
        </div>
        <div>
          <button class="text-center block w-full text-3xl font-bold underline text-[#FF6384]">
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
    <TimelineCard :title="'Notice board'" :messages="props.noticeBoardMessages" class="w-2/5"/>
    <!-- Notifications -->
    <TimelineCard :title="'Notifications'" :messages="props.noticeBoardMessages" class="w-2/5"/>
    <!-- Recent activities -->
    <TimelineCard :title="'Recent activities'" :messages="props.noticeBoardMessages" class="w-1/5"/>
  </section>
</template>

<script setup>
import { computed } from 'vue';
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

Chart.register(...registerables);

const props = defineProps({
  school: Object,
  numberOfStudents: Number,
  numberOfParents: Number,
  numberOfTeachers: Number,
  numberOfAdministrators: Number,
  schoolFeesDataForLineChart: Array,
  totalSchoolFees: Number,
  totalSchoolFeesCollected: Number,
  noticeBoardMessages: Object,
});

const user = computed(() => usePage().props.value.auth.user);

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
