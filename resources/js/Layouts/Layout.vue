<template>
  <div
    class="relative flex h-screen bg-gray-100 text-sm text-gray-700 md:text-base"
  >
    <!-- side-bar div -->
    <div
      class="h-screen flex-col items-center gap-4 bg-white px-4 pt-20 shadow transition-all lg:gap-8"
      :class="[isLgBreakpoint ? 'relative flex w-60' : sideBarClasses]"
    >
      <!-- App name and logo... changes shoud also be applied to the one in the nav bar  -->
      <div
        class="flex items-center justify-center gap-0.5 text-lg font-bold tracking-wider text-purple-600 md:ml-8 lg:hidden"
      >
        <LightningBoltIcon class="h-6 w-6" />
        mystify-sms
      </div>
      <!-- school name and logo -->
      <div v-if="school" class="flex flex-col gap-2">
        <div
          class="h-28 w-28 place-self-center rounded-full bg-[url('/images/school-crests/default.png')] bg-contain bg-center bg-no-repeat"
          alt="school crest"
        ></div>
        <div
          class="text-center text-lg font-semibold tracking-wide text-gray-700"
        >
          {{ school.name }}
        </div>
      </div>
      <!-- side bar buttons -->
      <div class="flex w-full flex-col gap-2">
        <Link
          :href="route('dashboard')"
          :class="[
            $page.url.startsWith('/dashboard')
              ? 'bg-purple-200 font-semibold text-purple-600'
              : 'font-medium hover:text-purple-600 hover:underline',
          ]"
          class="flex w-full items-center justify-start gap-2 rounded-lg py-2 pl-5 text-lg tracking-wide"
        >
          <DesktopComputerIcon class="h-5 w-5" />
          Dashboard
        </Link>
        <Link
          v-if="authUser.permissions.viewStudents"
          :href="route('users.index', 'students')"
          :class="[
            $page.url.startsWith('/users/students')
              ? 'bg-purple-600 text-white'
              : 'hover:text-purple-600 hover:underline',
          ]"
          class="flex w-full items-center justify-start gap-2 rounded-full py-2 pl-5 font-medium tracking-wide"
        >
          <AcademicCapIcon class="h-5 w-5" />
          Students
        </Link>
        <Link
          v-if="authUser.permissions.viewClasses"
          :href="route('classes.index')"
          :class="[
            $page.url.startsWith('/class')
              ? 'bg-purple-600 text-white'
              : 'hover:text-purple-600 hover:underline',
          ]"
          class="flex w-full items-center justify-start gap-2 rounded-full py-2 pl-5 font-medium tracking-wide"
        >
          <svg
            class="h-5 w-5"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill="currentColor"
              d="M22 17a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4a1 1 0 011-1h4zm-1 2h-2v2h2v-2zm-7-2a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4a1 1 0 011-1h4zm-1 2h-2v2h2v-2zm-7-2a1 1 0 011 1v4a1 1 0 01-1 1H2a1 1 0 01-1-1v-4a1 1 0 011-1h4zm-1 2H3v2h2v-2zM22 9a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4a1 1 0 011-1h4zm-1 2h-2v2h2v-2zm-7-2a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4a1 1 0 011-1h4zm-1 2h-2v2h2v-2zM6 9a1 1 0 011 1v4a1 1 0 01-1 1H2a1 1 0 01-1-1v-4a1 1 0 011-1h4zm-1 2H3v2h2v-2zM22 1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V2a1 1 0 011-1h4zm-1 2h-2v2h2V3zm-7-2a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V2a1 1 0 011-1h4zm-1 2h-2v2h2V3zM6 1a1 1 0 011 1v4a1 1 0 01-1 1H2a1 1 0 01-1-1V2a1 1 0 011-1h4zM5 3H3v2h2V3z"
            />
          </svg>
          Classes
        </Link>
        <Link
          v-if="authUser.permissions.viewParents"
          :href="route('users.index', 'parents')"
          :class="[
            $page.url.startsWith('/users/parents')
              ? 'bg-purple-600 text-white'
              : 'hover:text-purple-600 hover:underline',
          ]"
          class="flex w-full items-center justify-start gap-2 rounded-full py-2 pl-5 font-medium tracking-wide"
        >
          <UserGroupIcon class="h-5 w-5" />
          Parents
        </Link>
        <Link
          v-if="authUser.permissions.viewTeachers"
          :href="route('users.index', 'teachers')"
          :class="[
            $page.url.startsWith('/users/teachers')
              ? 'bg-purple-600 text-white'
              : 'hover:text-purple-600 hover:underline',
          ]"
          class="flex w-full items-center justify-start gap-2 rounded-full py-2 pl-5 font-medium tracking-wide"
        >
          <UsersIcon class="h-5 w-5" />
          Teachers
        </Link>
        <Link
          v-if="authUser.permissions.viewAdministrators"
          :href="route('users.index', 'administrators')"
          :class="[
            $page.url.startsWith('/users/administrators')
              ? 'bg-purple-600 text-white'
              : 'hover:text-purple-600 hover:underline',
          ]"
          class="flex w-full items-center justify-start gap-2 rounded-full py-2 pl-5 font-medium tracking-wide"
        >
          <ArchiveIcon class="h-5 w-5" />
          Administrators
        </Link>
      </div>
    </div>
    <!-- main content div -->
    <div
      class="relative flex w-full flex-col justify-between gap-6 overflow-y-auto px-2 pt-16 sm:px-6 md:pt-20"
    >
      <!-- Nav bar -->
      <div
        class="fixed top-0 left-0 z-10 flex h-14 w-full items-center justify-between gap-0.5 border-b border-gray-200 bg-white shadow-sm sm:gap-0 md:h-16"
      >
        <!-- button that opens side bar on <lg  -->
        <button
          @click="shouldOpenSideBarOnMobile ? closeSideBar() : openSideBar()"
          class="inline-block p-2 lg:hidden"
        >
          <ChevronRightIcon
            :class="{ 'rotate-180': shouldOpenSideBarOnMobile }"
            class="h-8 w-8 transition-transform"
          />
        </button>
        <!-- App name and logo... changes shoud also be applied to the one in side bar  -->
        <div
          class="hidden items-center justify-center gap-0.5 text-lg font-bold tracking-wider text-purple-600 md:ml-8 lg:flex"
        >
          <LightningBoltIcon class="h-6 w-6" />
          mystify-sms
        </div>
        <!-- Current term -->
        <button
          v-if="term"
          @click="shouldOpenModalContainingListOfAcademicYearsWithTerms = true"
          class="group flex items-center justify-center gap-0.5 rounded-md border border-gray-500 p-1 shadow-sm sm:mx-auto sm:gap-2 sm:py-2 sm:px-3"
        >
          <span
            class="text-xs font-light text-purple-600 group-hover:text-purple-400 sm:font-semibold md:text-sm lg:text-base"
            >{{ term.formatted_name }}</span
          >
          <ViewListIcon class="h-5 w-5 group-hover:text-purple-400" />
        </button>
        <!-- User's name, profile picture and menu -->
        <div
          class="relative flex rounded-3xl border-gray-500 px-2 sm:mr-4 sm:gap-3 sm:border sm:shadow-sm md:mr-12"
        >
          <DotIndicator
            v-if="isThereANewNotification"
            class="-top-1 -right-2 h-6 w-6"
          />
          <ProfilePicture
            :profilePicturePath="authUser.profile_picture_path"
            widthClass="w-10"
            heightClass="h-10"
          />
          <div class="flex gap-1.5">
            <div class="hidden flex-col md:flex">
              <div class="text-base font-semibold text-fuchsia-600 lg:text-lg">
                {{ authUser.name }}
              </div>
              <div class="text-xs lg:text-sm">{{ authUser.user_type }}</div>
            </div>
            <Menu v-slot="{ open }" as="div" class="relative">
              <MenuButton
                class="flex h-full w-full items-center justify-center hover:text-purple-600"
              >
                <ChevronDownIcon
                  class="h-5 w-5 transition-transform"
                  :class="{ 'rotate-180': open }"
                />
              </MenuButton>
              <MenuItemsTransition>
                <MenuItems
                  class="menu-items right-0 z-10 mt-2 w-max origin-top-right"
                >
                  <div class="px-1 py-1">
                    <MenuItem as="div" class="relative" v-slot="{ active }">
                      <DotIndicator
                        v-if="isThereANewNotification"
                        class="-top-1 -right-2 h-6 w-6"
                      />
                      <MenuItemButton
                        @click="$inertia.get(route('notifications.index'))"
                        :active="active"
                      >
                        Notifications
                      </MenuItemButton>
                    </MenuItem>
                    <MenuItem as="div" v-slot="{ active }">
                      <MenuItemButton
                        @click="
                          $inertia.get(
                            route('users.show', { user: authUser.id })
                          )
                        "
                        :active="active"
                      >
                        Profile
                      </MenuItemButton>
                    </MenuItem>
                    <MenuItem
                      v-if="authUser.permissions.changeUserType"
                      as="div"
                      v-slot="{ active }"
                    >
                      <MenuItemButton
                        @click="shouldOpenModalContainingListOfUserTypes = true"
                        :active="active"
                      >
                        Change account type
                      </MenuItemButton>
                    </MenuItem>
                  </div>
                  <div class="px-1 py-1">
                    <MenuItem as="div" v-slot="{ active }">
                      <MenuItemButton
                        @click="$inertia.post(route('logout'))"
                        :active="active"
                      >
                        Log out
                      </MenuItemButton>
                    </MenuItem>
                  </div>
                </MenuItems>
              </MenuItemsTransition>
            </Menu>
          </div>
        </div>
      </div>
      <!-- main content slot -->
      <slot />
      <!-- footer -->
      <footer class="flex w-full flex-col p-5 text-base sm:text-xl">
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
    <!-- MODALS -->
    <!-- Modal containing list of academic years with terms -->
    <Modal
      :show="shouldOpenModalContainingListOfAcademicYearsWithTerms"
      :maxWidthClass="'max-w-xs'"
      @closeModal="
        shouldOpenModalContainingListOfAcademicYearsWithTerms = false
      "
    >
      <div class="flex flex-col gap-3 text-sm text-purple-600 sm:text-base">
        <Menu
          v-for="(
            academicYearItem, academicYearIndex
          ) in academicYearsWithTerms"
          :key="academicYearIndex"
          as="div"
          class="relative inline-block rounded-full odd:border-gray-300 odd:bg-white even:bg-gray-100"
        >
          <MenuButton
            class="align-center inline-flex w-full items-center justify-center rounded-full border p-2 text-purple-600 hover:text-purple-400 hover:underline"
          >
            {{ academicYearItem.name }}
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
                  v-for="(termItem, termIndex) in academicYearItem.terms"
                  :key="termIndex"
                  v-slot="{ active }"
                >
                  <button
                    :class="[
                      active ? 'bg-violet-500 text-white' : 'text-gray-900',
                      'group flex w-full items-center justify-center rounded-md px-2 py-2 text-sm',
                    ]"
                    @click="
                      changeTerm(termItem.id);
                      shouldOpenModalContainingListOfAcademicYearsWithTerms = false;
                    "
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
    <!-- Modal containing list of user types -->
    <Modal
      :show="shouldOpenModalContainingListOfUserTypes"
      :maxWidthClass="'max-w-xs'"
      @closeModal="shouldOpenModalContainingListOfUserTypes = false"
    >
      <div class="flex flex-col gap-3 text-purple-600">
        <button
          v-for="(item, index) in authUser.user_types"
          :key="index"
          @click="
            shouldOpenModalContainingListOfUserTypes = false;
            $inertia.patch(route('change_user_type', { user_type: item }));
          "
          class="list-of-buttons-in-modal relative inline-block p-2 font-semibold tracking-wide hover:text-purple-500 hover:underline"
        >
          {{ item }}
        </button>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch, toRaw } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import {
  DesktopComputerIcon,
  UsersIcon,
  AcademicCapIcon,
  UserGroupIcon,
  ArchiveIcon,
  ChatIcon,
} from '@heroicons/vue/outline';
import { ChevronDownIcon, ChevronRightIcon } from '@heroicons/vue/solid';
import { LightningBoltIcon } from '@heroicons/vue/outline';
import { Inertia } from '@inertiajs/inertia';
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core';
import Swal from 'sweetalert2';

import DotIndicator from '@/Components/DotIndicator.vue';
import { changeTerm } from '@/helpers';
import { defaultDashboardProps } from '@/default_dashboard_props.js';
import { useNotifications } from '@/notifications.js';

const breakpoints = useBreakpoints(breakpointsTailwind);

const isLgBreakpoint = computed(() => breakpoints.lg.value);

const sideBarClasses = ref('opacity-0 absolute invisible w-0');

const shouldOpenSideBarOnMobile = ref(false);

function openSideBar() {
  shouldOpenSideBarOnMobile.value = true;
  sideBarClasses.value = 'flex absolute top-0 left-0 z-10 w-60 opacity-100';
}

function closeSideBar() {
  shouldOpenSideBarOnMobile.value = false;
  sideBarClasses.value = 'opacity-0 absolute invisible w-0 z-10';
}

const { isThereANewNotification, notifications } = useNotifications();

defineProps({ ...defaultDashboardProps });

const shouldOpenModalContainingListOfAcademicYearsWithTerms = ref(false);
const shouldOpenModalContainingListOfUserTypes = ref(false);

const authUser = computed(() => usePage().props.value.auth.user);
// in the tempalate you can access inertia page props directly by $page.props.auth.user

function handleNewNotifications(notification) {
  isThereANewNotification.value = true;
  notifications.value.unshift({
    ...notification,
    data: notification,
  });
  // console.log(notification);
}

watch(
  () => usePage().props.value.flash.warning,
  (newFlashMsg) => {
    if (!toRaw(newFlashMsg)) return;
    Swal.fire({
      icon: 'warning',
      title: "Cannot view student's dashboard",
      text: toRaw(newFlashMsg),
    });
  }
);

onMounted(() => {
  // if (['student', 'parent'].includes(authUser.value.user_type)) {
  window.Echo.private(`App.Models.User.${authUser.value.id}`).notification(
    (notification) => {
      handleNewNotifications(notification);
    }
  );
  // }

  if (['headteacher', 'administrator'].includes(authUser.value.user_type)) {
    window.Echo.private(
      `App.Models.School.${authUser.value.school_id}`
    ).notification((notification) => {
      handleNewNotifications(notification);
    });
  }
});
</script>
