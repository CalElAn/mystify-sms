<template>
  <NavBar :actions="teacherActions" />
  <section class="flex items-center justify-center">
    <div class="base-card w-11/12 p-4">
      <p class="form-title mt-2 text-center">Classes</p>
      <div class="mb-2 flex justify-end">
        <AddButton @click="add()" :disabled="!shouldAllowAdd" class="mr-4">
          Add
        </AddButton>
      </div>
      <div class="flex flex-col">
        <div class="thead grid grid-cols-3 gap-12 p-2 font-bold">
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

import NavBar from '@/Components/ActionsNavBar.vue';
import Subform from '@/Pages/ClassTeacher/Subform.vue';
import AddButton from '@/Components/AddButton.vue';
import { teacherActions } from '@/teacher_actions.js';

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

<style scoped>
@import '../../../css/subform_transition.css';
</style>
