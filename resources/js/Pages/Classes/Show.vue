<template>
  <Head :title="classModel.name_and_suffix"></Head>
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
