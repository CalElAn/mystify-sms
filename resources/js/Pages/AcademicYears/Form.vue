<template>
  <section class="flex items-center justify-center">
    <div class="base-card w-full p-4 md:w-11/12">
      <p class="form-title mt-2 text-center">Add academic year</p>
      <div class="mb-2 mt-4 flex justify-end md:mt-0">
        <AddButton @click="add()" :disabled="!shouldAllowAdd" class="mr-4">
          Add
        </AddButton>
      </div>
      <div class="flex flex-col text-sm md:text-base">
        <div
          class="thead grid grid-cols-3 gap-4 p-2 font-bold sm:gap-x-4 xl:gap-x-12"
        >
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

import Subform from '@/Pages/AcademicYears/Subform.vue';
import AddButton from '@/Components/AddButton.vue';

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

<script>
import HeadteacherActions from '@/Layouts/HeadteacherActions.vue';
import Layout from '@/Layouts/Layout.vue';

export default {
  layout: [Layout, HeadteacherActions],
};
</script>

<style scoped>
@import '../../../css/subform_transition.css';
</style>
