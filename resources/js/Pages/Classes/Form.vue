<template>
  <section class="flex items-center justify-center">
    <div class="base-card w-11/12 p-4">
      <p class="form-title mt-2 text-center">Add class</p>
      <div class="mb-2 mt-4 flex justify-end">
        <AddButton @click="add()" :disabled="!shouldAllowAdd" class="mr-4"
          >Add</AddButton
        >
      </div>

      <div class="flex flex-col">
        <!-- TODO title should be sticky -->
        <div class="thead grid grid-cols-2 gap-12 p-2 font-bold">
          <div>Name</div>
          <div>Group</div>
        </div>
        <TransitionGroup name="list">
          <Subform
            v-for="item in classesData"
            :key="item"
            :classModel="item"
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

import Subform from '@/Pages/Classes/Subform.vue';
import AddButton from '@/Components/AddButton.vue';

const props = defineProps({
  classes: Array,
});
const classesData = props.classes;
const newClass = {
  adding: true,
  name: '',
  suffix: '',
};
const shouldAllowAdd = ref(true);

function add() {
  classesData.unshift(newClass);
  shouldAllowAdd.value = false;
}

function onCancelAdd() {
  classesData.shift();
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
