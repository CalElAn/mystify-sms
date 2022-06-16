<template>
  <NavBar />
  <section class="flex items-center justify-center">
    <div class="base-card w-11/12 p-4">
      <p class="form-title mt-2 text-center">Add academic year</p>
      <div class="mb-2 flex justify-end">
        <AddButton @click="add()" :disabled="!shouldAllowAdd" class="mr-4"
          >Add</AddButton
        >
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

import NavBar from '@/Components/HeadteaherActionsNavBar.vue';
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

<style scoped>
@import '../../../css/subform_transition.css';
</style>
