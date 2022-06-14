<template>
  <NavBar />
  <section class="flex items-center justify-center">
    <div class="base-card w-11/12 p-4">
      <p
        class="mt-2 text-center text-xl font-semibold tracking-wide text-gray-600"
      >
        Add academic year
      </p>
      <div class="mb-2 flex justify-end">
        <button
          @click="add()"
          :disabled="!shouldAllowAdd"
          class="mr-4 inline-flex items-center justify-center gap-1 rounded-lg border border-purple-600 px-2 py-1 font-semibold tracking-wide text-purple-600 shadow-sm hover:bg-purple-100 disabled:opacity-50"
        >
          <PlusCircleIcon class="h-5 w-5" />
          Add
        </button>
      </div>

      <div class="flex flex-col">
        <!-- TODO title should be sticky -->
        <div class="thead grid grid-cols-3 gap-12 p-2 font-bold">
          <div>Name</div>
          <div>Start date</div>
          <div>End date</div>
        </div>
        <TransitionGroup name="list">
          <Subform
            v-for="item in academicYearsData"
            :key="item"
            :academicYear="item"
            @cancelAdd="onCancelAdd()"
            @stored="shouldAllowAdd = true"
          />
        </TransitionGroup>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';

import { PlusCircleIcon } from '@heroicons/vue/outline';

import NavBar from '@/Components/HeadteaherActionsNavBar.vue';
import Subform from '@/Pages/AcademicYears/Subform.vue';

const props = defineProps({
  academicYears: Array,
});
const academicYearsData = props.academicYears;
const newAcademicYear = {
  adding: true,
  name: '',
  start_date: '',
  end_date: '',
};
const shouldAllowAdd = ref(true);

function add() {
  academicYearsData.unshift(newAcademicYear);
  shouldAllowAdd.value = false;
}

function onCancelAdd() {
  academicYearsData.shift();
  shouldAllowAdd.value = true;
}
</script>

<style scoped>
@import '../../../css/subform_transition.css';
</style>
