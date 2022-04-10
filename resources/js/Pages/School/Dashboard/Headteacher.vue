<template>
  <div class="flex min-h-screen bg-gray-100 text-gray-700">
    <div
      class="relative flex min-h-screen w-1/6 flex-col items-center gap-6 bg-white px-4 pt-20 shadow"
    >
      <!-- App logo -->
      <div
        class="absolute top-0 flex h-14 w-full items-center justify-center bg-fuchsia-600 text-lg font-medium tracking-wide text-white"
      >
        mystify-sms
      </div>
      <!-- Side bar -->
      <div class="flex flex-col gap-2">
        <div
          class="h-32 w-32 place-self-center rounded-full bg-contain bg-center bg-no-repeat"
          :style="{
            'background-image': 'url(' + getProfilePictureUrl(user) + ')',
          }"
          alt="profile picture"
        ></div>
        <p class="text-center font-semibold">
          {{ user.name }}
        </p>
        <div class="flex flex-row items-center gap-2">
          <span class="text-sm">Logged in as:</span>
          <div class="w-auto">
            <Menu as="div" class="relative inline-block">
              <div>
                <MenuButton
                  class="inline-flex w-full justify-center rounded-md bg-purple-600 bg-opacity-70 px-4 py-2 text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
                >
                  {{ user.user_type }}
                  <ChevronDownIcon
                    class="ml-1 -mr-1 h-5 w-5 text-white hover:text-violet-100"
                    aria-hidden="true"
                  />
                </MenuButton>
              </div>
              <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
              >
                <MenuItems
                  class="absolute left-0 mt-2 w-36 origin-top-left divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                  <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                      <button
                        :class="[
                          active ? 'bg-violet-500 text-white' : 'text-gray-900',
                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]"
                      >
                        parent
                      </button>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                      <button
                        :class="[
                          active ? 'bg-violet-500 text-white' : 'text-gray-900',
                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]"
                      >
                        teacher
                      </button>
                    </MenuItem>
                  </div>
                </MenuItems>
              </transition>
            </Menu>
          </div>
        </div>
      </div>
      <button
        class="flex w-full items-center justify-center gap-2 rounded-full bg-purple-600 py-2 tracking-wide text-white"
      >
        <DesktopComputerIcon class="h-5 w-5" />
        Dashboard
      </button>
    </div>
    <div
      class="relative flex min-h-screen w-5/6 flex-col gap-6 px-6 pt-20 pb-28"
    >
      <!-- Nav bar -->
      <div
        class="absolute top-0 left-0 flex h-14 w-full items-center justify-evenly border-b border-gray-100 shadow-sm"
      >
        <div>
          <!-- <select class="custom-select w-full font-semibold text-gray-600">
            <option value="1">2020, First Term (8 January to 12 May)</option>
          </select> -->
          <Menu as="div" class="relative inline-block">
            <div>
              <MenuButton
                class="inline-flex w-full justify-center rounded-md border border-purple-600 bg-opacity-70 px-4 py-1.5 font-semibold text-purple-600 hover:text-purple-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
              >
                {{ '2020, First Term (8 January to 12 May)' }}
                <ChevronDownIcon
                  class="ml-1 -mr-1 h-5 w-5 text-purple-600 hover:text-purple-400"
                  aria-hidden="true"
                />
              </MenuButton>
            </div>
            <transition
              enter-active-class="transition duration-100 ease-out"
              enter-from-class="transform scale-95 opacity-0"
              enter-to-class="transform scale-100 opacity-100"
              leave-active-class="transition duration-75 ease-in"
              leave-from-class="transform scale-100 opacity-100"
              leave-to-class="transform scale-95 opacity-0"
            >
              <MenuItems
                class="absolute left-0 mt-2 w-auto origin-top-left divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
              >
                <div class="px-1 py-1">
                  <MenuItem v-slot="{ active }">
                    <button
                      :class="[
                        active ? 'bg-violet-500 text-white' : 'text-gray-900',
                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                      ]"
                    >
                      2019, First Term (1 February to 12 April)
                    </button>
                  </MenuItem>
                  <MenuItem v-slot="{ active }">
                    <button
                      :class="[
                        active ? 'bg-violet-500 text-white' : 'text-gray-900',
                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                      ]"
                    >
                      2019, Second Term (8 July to 12 October)
                    </button>
                  </MenuItem>
                </div>
              </MenuItems>
            </transition>
          </Menu>
        </div>
        <div class="text-2xl font-semibold tracking-wide text-gray-800">
          {{ school.name }}
        </div>
      </div>
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
        <div class="w-4/6 rounded-lg bg-white p-2 shadow">
          <LineChart :chartData="lineChartData" :options="lineChartOptions" />
        </div>
        <!-- Doughnut chart -->
        <div
          class="flex w-2/6 flex-col justify-evenly rounded-lg bg-white p-3 shadow"
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
              <p class="text-center text-3xl font-bold text-[#FF6384]">
                {{
                  (
                    props.totalSchoolFees - props.totalSchoolFeesCollected
                  ).toLocaleString()
                }}
              </p>
              Total fees remaining
            </div>
          </div>
        </div>
      </section>
      <section class="flex gap-6">
        <!-- Notice board -->
        <div class="flex h-96 w-2/5 flex-col rounded-lg bg-white shadow">
          <div
            class="flex items-center justify-between border-b py-2 px-4 text-xl font-semibold tracking-wide text-purple-600"
          >
            Notice board
          </div>
          <div class="flex flex-col gap-8 overflow-y-auto p-4">
            <div
              v-for="(firstItem, firstIndex) in noticeBoardMessages"
              :key="firstIndex"
              class="flex flex-col gap-2"
            >
              <p class="font-semibold">{{ firstIndex }}</p>
              <ol
                class="relative ml-3 flex flex-col gap-8 border-l border-gray-200"
              >
                <li
                  v-for="(secondItem, secondIndex) in firstItem"
                  :key="secondIndex"
                  class="ml-4 flex flex-col"
                >
                  <div
                    class="absolute -left-1.5 h-3 w-3 rounded-full border-4 border-fuchsia-600 bg-white"
                  ></div>
                  <div class="flex flex-col gap-2">
                    <time
                      class="mb-1 text-sm font-semibold leading-none text-gray-500"
                    >
                      {{ secondItem.time_string }}
                    </time>
                    <p
                      @click="openNoticeBoardMessageModal(secondItem.message)"
                      class="rounded-lg border p-2 hover:cursor-pointer"
                    >
                      {{ secondItem.message.substring(0, 200) + ' ....' }}
                    </p>
                  </div>
                </li>
              </ol>
            </div>
          </div>
        </div>
        <!-- Notifications -->
        <div class="flex h-96 w-2/5 flex-col rounded-lg bg-white shadow">
          <div
            class="flex items-center justify-between border-b py-2 px-4 text-xl font-semibold tracking-wide text-purple-600"
          >
            Notifications
          </div>
          <div class="flex flex-col gap-8 overflow-y-auto p-4">
            <div
              v-for="(firstItem, firstIndex) in noticeBoardMessages"
              :key="firstIndex"
              class="flex flex-col gap-2"
            >
              <p class="font-semibold">{{ firstIndex }}</p>
              <ol
                class="relative ml-3 flex flex-col gap-8 border-l border-gray-200"
              >
                <li
                  v-for="(secondItem, secondIndex) in firstItem"
                  :key="secondIndex"
                  class="ml-4 flex flex-col"
                >
                  <div
                    class="absolute -left-1.5 h-3 w-3 rounded-full border-4 border-fuchsia-600 bg-white"
                  ></div>
                  <div class="flex flex-col gap-2">
                    <time
                      class="mb-1 text-sm font-semibold leading-none text-gray-500"
                    >
                      {{ secondItem.time_string }}
                    </time>
                    <p
                      @click="openNoticeBoardMessageModal(secondItem.message)"
                      class="rounded-lg border p-2 hover:cursor-pointer"
                    >
                      {{ secondItem.message.substring(0, 200) + ' ....' }}
                    </p>
                  </div>
                </li>
              </ol>
            </div>
          </div>
        </div>
        <!-- Recent activities -->
        <div class="flex h-96 w-1/5 flex-col rounded-lg bg-white shadow">
          <div
            class="flex items-center justify-between border-b py-2 px-4 text-xl font-semibold tracking-wide text-purple-600"
          >
            Recent activities
          </div>
          <div class="flex flex-col gap-8 overflow-y-auto p-4">
            <div
              v-for="(firstItem, firstIndex) in noticeBoardMessages"
              :key="firstIndex"
              class="flex flex-col gap-2"
            >
              <p class="font-semibold">{{ firstIndex }}</p>
              <ol
                class="relative ml-3 flex flex-col gap-8 border-l border-gray-200"
              >
                <li
                  v-for="(secondItem, secondIndex) in firstItem"
                  :key="secondIndex"
                  class="ml-4 flex flex-col"
                >
                  <div
                    class="absolute -left-1.5 h-3 w-3 rounded-full border-4 border-fuchsia-600 bg-white"
                  ></div>
                  <div class="flex flex-col gap-2">
                    <time
                      class="mb-1 text-sm font-semibold leading-none text-gray-500"
                    >
                      {{ secondItem.time_string }}
                    </time>
                    <p
                      @click="openNoticeBoardMessageModal(secondItem.message)"
                      class="rounded-lg border p-2 hover:cursor-pointer"
                    >
                      {{ secondItem.message.substring(0, 200) + ' ....' }}
                    </p>
                  </div>
                </li>
              </ol>
            </div>
          </div>
        </div>
      </section>
      <footer class="absolute bottom-0 left-0 flex flex-col w-full p-5 text-base sm:text-xl">
        <hr class="my-3" />
        <div class="flex items-center justify-center gap-5 sm:gap-12">
          <span class="flex flex-shrink-0">&copy; mystify-sms</span>
          <div class="flex items-center justify-center gap-3">
            <a href="#" class="text-gray-500 hover:text-gray-900">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"
                />
              </svg>
            </a>
            <button>
              <ChatIcon class="h-6 w-6 text-gray-500 hover:text-gray-900" />
            </button>
            <a
              href="mailto: info@efiehub.com"
              class="text-gray-500 hover:text-gray-900"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
                />
                <path
                  d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"
                />
              </svg>
            </a>
            <a href="#" class="text-gray-500 hover:text-gray-900">
              <svg
                class="h-5 w-5"
                fill="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                  clip-rule="evenodd"
                />
              </svg>
            </a>
            <a href="#" class="text-gray-500 hover:text-gray-900">
              <svg
                class="h-5 w-5"
                fill="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                  clip-rule="evenodd"
                />
              </svg>
            </a>
            <a
              href="https://twitter.com/efiehub?ref_src=twsrc%5Etfw"
              class="text-gray-500 hover:text-gray-900"
            >
              <svg
                class="h-6 w-6"
                fill="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"
                />
              </svg>
            </a>
          </div>
        </div>
      </footer>
    </div>
    <!-- Notice board message modal -->
    <TransitionRoot appear :show="isNoticeBoardMessageModalOpen" as="template">
      <Dialog as="div" @close="setIsNoticeBoardMessageModalOpen(false)">
        <div class="fixed inset-0 z-10 overflow-y-auto">
          <div class="min-h-screen px-4 text-center">
            <TransitionChild
              as="template"
              enter="duration-300 ease-out"
              enter-from="opacity-0"
              enter-to="opacity-100"
              leave="duration-200 ease-in"
              leave-from="opacity-100"
              leave-to="opacity-0"
            >
              <DialogOverlay class="fixed inset-0 bg-black opacity-70" />
            </TransitionChild>
            <span class="inline-block h-screen align-middle" aria-hidden="true">
              &#8203;
            </span>
            <TransitionChild
              as="template"
              enter="duration-300 ease-out"
              enter-from="opacity-0 scale-95"
              enter-to="opacity-100 scale-100"
              leave="duration-200 ease-in"
              leave-from="opacity-100 scale-100"
              leave-to="opacity-0 scale-95"
            >
              <div
                class="my-8 inline-block w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
              >
                <!-- <DialogTitle
                  as="h3"
                  class="text-lg font-medium leading-6 text-gray-900"
                >
                  Payment successful
                </DialogTitle> -->
                <div class="mt-2">
                  <div class="overflow-auto whitespace-pre-wrap">
                    {{ noticeBoardMessageToDisplayInModal }}
                  </div>
                </div>
                <div class="mt-4 flex justify-end">
                  <button
                    type="button"
                    class="justify-center rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                    @click="setIsNoticeBoardMessageModalOpen(false)"
                  >
                    Close
                  </button>
                </div>
              </div>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import { DoughnutChart, LineChart } from 'vue-chart-3';
import { Chart, registerables } from 'chart.js';
import {
  DesktopComputerIcon,
  UsersIcon,
  AcademicCapIcon,
  UserGroupIcon,
  ArchiveIcon,
  ChatIcon,
} from '@heroicons/vue/outline';
import {
  Menu,
  MenuButton,
  MenuItems,
  MenuItem,
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogOverlay,
  DialogTitle,
} from '@headlessui/vue';
import { ChevronDownIcon } from '@heroicons/vue/solid';

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
const noticeBoardMessageToDisplayInModal = ref('');
const isNoticeBoardMessageModalOpen = ref(false);

function setIsNoticeBoardMessageModalOpen(value) {
  isNoticeBoardMessageModalOpen.value = value;
}

function openNoticeBoardMessageModal(message) {
  noticeBoardMessageToDisplayInModal.value = message;
  isNoticeBoardMessageModalOpen.value = true;
}

Chart.register(...registerables);

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

function getProfilePictureUrl(user) {
  if (user.profile_picture_path.includes('https://'))
    return user.profile_picture_path;

  return '/storage/' + user.profile_picture_path;
}
</script>
