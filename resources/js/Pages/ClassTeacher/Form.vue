<template>
  <section class="flex items-center justify-center">
    <div class="base-card w-11/12 p-4">
      <p class="form-title mt-2 text-center">Classes</p>
      <div class="mb-2 mt-4 flex justify-end">
        <AddButton @click="add()" :disabled="!shouldAllowAdd" class="mr-4">
          Add
        </AddButton>
      </div>
      <div class="flex flex-col">
        <div
          class="thead grid grid-cols-3 p-2 text-sm font-bold sm:gap-x-4 md:text-base xl:gap-x-12"
        >
          <div>Class teacher</div>
          <div>Class</div>
          <div>Academic year</div>
        </div>
        <TransitionGroup name="list">
          <Subform
            v-for="item in classTeacherData"
            :key="item"
            :classTeacherData="item"
            :academicYears="academicYears"
            :classes="classes"
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

import Subform from '@/Pages/ClassTeacher/Subform.vue';
import AddButton from '@/Components/AddButton.vue';

const props = defineProps({
  classTeacherPivotData: Array,
  classes: Array,
  academicYears: Array,
  defaultAcademicYear: Object,
});
const classTeacherData = props.classTeacherPivotData;
const newClassTeacher = {
  adding: true,
  class_id: '',
  academic_year_id: props.defaultAcademicYear.id,
};
const shouldAllowAdd = ref(true);

function add() {
  classTeacherData.unshift(newClassTeacher);
  shouldAllowAdd.value = false;
}

function onCancelAdd() {
  classTeacherData.shift();
  shouldAllowAdd.value = true;
}
</script>

<script>
import TeacherActions from '@/Layouts/TeacherActions.vue';
import Layout from '@/Layouts/Layout.vue';

export default {
  layout: [Layout, TeacherActions],
};
</script>

<style scoped>
@import '../../../css/subform_transition.css';
</style>
