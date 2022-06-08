<template>
  <ClassPanel
    :classModel="classModel"
    :studentsInClass="classModel.students"
    :academicYearName="term.academic_year.name"
  />
</template>

<script setup>
import ClassPanel from '@/Components/Classes/Panel.vue';

defineProps({
  classModel: Object,
  studentsInClass: Array,
  term: Object,
});
</script>
