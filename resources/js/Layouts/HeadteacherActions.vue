<template>
  <Head :title="headteacherActions.find((item) => item.component === $page.component)?.label"></Head>
  <NavBar :actions="headteacherActions" />
  <slot/>
</template>

<script setup>
import NavBar from '@/Components/ActionsNavBar.vue';
import { headteacherActions } from '@/headteacher_actions.js';
</script>
