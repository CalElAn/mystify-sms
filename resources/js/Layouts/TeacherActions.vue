<template>
  <NavBar :actions="teacherActions" />
  <slot/>
</template>

<script setup>
import NavBar from '@/Components/ActionsNavBar.vue';
import { teacherActions } from '@/teacher_actions.js';
</script>
