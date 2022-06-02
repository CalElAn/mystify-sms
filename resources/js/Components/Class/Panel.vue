<template>
  <section class="flex items-center justify-center">
    <!-- class name and teacher -->
    <div
      v-if="classModel"
      class="base-card flex items-center justify-evenly gap-2 py-2 px-4"
    >
      <div
        class="flex items-center justify-center gap-2 text-xl font-semibold text-purple-600"
      >
        {{ classModel.name }} {{ classModel.suffix }}
        <button
          @click="shouldOpenModalContainingListOfClasses = true"
          class="text-gray-700 hover:text-purple-600"
        >
          <ViewListIcon class="h-5 w-5" />
        </button>
        <Menu as="div" class="relative">
          <MenuButton class="flex h-full w-full items-center justify-center">
            <DotsVerticalIcon
              class="h-5 w-5 text-gray-700 hover:text-purple-600"
            />
          </MenuButton>
          <MenuItemsTransition>
            <MenuItems
              class="menu-items right-0 z-10 mt-2 w-max origin-top-right"
            >
              <div class="px-1 py-1">
                <MenuItem as="div" v-slot="{ active }">
                  <MenuItemButton :active="active">
                    Add class(es)
                  </MenuItemButton>
                </MenuItem>
                <MenuItem as="div" v-slot="{ active }">
                  <MenuItemButton :active="active">
                    Remove class(es)
                  </MenuItemButton>
                </MenuItem>
              </div>
            </MenuItems>
          </MenuItemsTransition>
        </Menu>
      </div>
      <div
        style="height: 36px; width: 2px; background: #ddd; display: inline"
      ></div>
      <div class="flex items-center gap-2">
        <div class="inline-block">Class teacher:</div>
        <div class="flex items-center justify-center gap-2">
          <ProfilePicture
            :profilePicturePath="classModel.teachers[0]?.profile_picture_path"
            widthClass="w-14"
            heightClass="h-14"
          />
          <Link
            :href="route('users.show', { userId: classModel.teachers[0]?.id })"
            class="inline-block text-lg font-semibold tracking-wide text-purple-600 underline underline-offset-1"
          >
            {{ classModel.teachers[0]?.name }}
          </Link>
        </div>
      </div>
    </div>
  </section>
  <section
    class="flex flex-col items-center justify-center gap-2"
  >
    <div class="flex w-11/12 justify-end pr-6">
      <!-- cog icon button -->
      <Menu as="div" class="base-card relative">
        <MenuButton class="w-full p-2">
          <DotsVerticalIcon
            class="h-6 w-6 text-purple-600 transition-transform hover:scale-110"
          />
        </MenuButton>
        <MenuItemsTransition>
          <MenuItems
            class="menu-items right-0 z-10 mt-2 w-max origin-top-right"
          >
            <div class="px-1 py-1">
              <MenuItem as="div" v-slot="{ active }">
                <MenuItemButton :active="active">
                  Add student(s) to {{ classModel.name }}
                  {{ classModel.suffix }}
                </MenuItemButton>
              </MenuItem>
              <MenuItem as="div" v-slot="{ active }">
                <MenuItemButton :active="active">
                  Remove student(s) from {{ classModel.name }}
                  {{ classModel.suffix }}
                </MenuItemButton>
              </MenuItem>
            </div>
            <div class="px-1 py-1">
              <MenuItem as="div" v-slot="{ active }">
                <MenuItemButton :active="active">
                  Send text message to student(s)
                </MenuItemButton>
              </MenuItem>
              <MenuItem as="div" v-slot="{ active }">
                <MenuItemButton :active="active">
                  Send text message to parent(s)
                </MenuItemButton>
              </MenuItem>
            </div>
          </MenuItems>
        </MenuItemsTransition>
      </Menu>
    </div>
    <!-- list of students in class -->
    <UserTable
      :title="`Students in ${classModel.name} ${classModel.suffix} (${academicYearName})`"
      :users="studentsInClass"
    />
  </section>
</template>

<script setup>
import UserTable from '@/Components/User/Table.vue'

defineProps({
  classModel: Object,
  studentsInClass: Array,
  academicYearName: String
});
</script>

<style lang="scss" scoped></style>
