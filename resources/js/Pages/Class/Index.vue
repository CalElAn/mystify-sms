<template>
  <section class="flex items-center justify-center">
    <div class="base-card w-2/5 p-2">
      <p class="mb-1 text-center text-lg font-semibold tracking-wide">
        Classes and class teachers ({{ term.academic_year.name }})
      </p>
      <table class="w-full table-auto text-center">
        <thead class="thead">
          <tr>
            <th class="p-2">Class</th>
            <!-- <th class="p-2"></th> -->
            <th class="p-2 pl-6 text-left">Class teacher</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(classItem, index) in classes" :key="index" class="tbody">
            <td class="p-2">
              <Link class="tda" :href="route('classes.show', classItem.id)"
                >{{ classItem.name }} {{ classItem.suffix }}</Link
              >
            </td>
            <td class="ml-2 flex items-center justify-start gap-2 p-2">
              <ProfilePicture
                :profilePicturePath="
                  classItem.teachers[0]?.profile_picture_path
                "
                widthClass="w-10"
                heightClass="h-10"
              />
              <a
                class="tda"
                :href="
                  route('dashboard', { userId: classItem.teachers[0]?.id })
                "
              >
                {{ classItem.teachers[0]?.name }}
              </a>
            </td>
          </tr>
          <tr v-if="classes.length === 0">
            <td class="border-t px-6 py-4" colspan="2">No classes found.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<script setup>
defineProps({
  term: Object,
  classes: Array,
});
</script>

<style lang="scss" scoped></style>
