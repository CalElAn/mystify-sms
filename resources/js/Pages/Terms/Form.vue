<template>
  <NavBar />

  <section class="flex items-center justify-center">
    <div class="base-card w-11/12 p-4">
      <p
        class="mt-2 text-center text-xl font-semibold tracking-wide text-gray-600"
      >
        Add term
      </p>
      <label class="ml-4 font-semibold tracking-wide text-gray-600"
        >Academic year:</label
      >
      <select
        v-model="academicYearId"
        @change="getTerms"
        class="custom-select m-4 w-1/2 shadow-sm"
      >
        <option selected disabled value="">- Select academic year -</option>
        <option v-for="item in academicYears" :value="item.id" :key="item">
          {{ item.formatted_name }}
        </option>
      </select>
      <div class="mb-2 flex justify-end">
        <button
          @click="add()"
          :disabled="!(shouldAllowAdd && academicYearId)"
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
            v-for="item in terms"
            :key="item"
            :term="item"
            @cancelAdd="onCancelAdd()"
            @stored="onStored()"
            @destroyed="getTerms()"
          />
        </TransitionGroup>
        <p v-if="academicYearId && (!terms || terms.length === 0)" class="ml-2">
          No terms found
        </p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue';

import { PlusCircleIcon } from '@heroicons/vue/outline';

import NavBar from '@/Components/HeadteaherActionsNavBar.vue';
import Subform from '@/Pages/Terms/Subform.vue';

defineProps({
  academicYears: Object,
});
const academicYearId = ref('');
const terms = ref([]);
const shouldAllowAdd = ref(true);

function getTerms() {
  axios
    .get(route('academic_years.terms', academicYearId.value))
    .then((response) => {
      terms.value = response.data;
    });
}

const newTerm = computed(() => {
  return {
    academic_year_id: academicYearId.value,
    adding: true,
    name: '',
    start_date: '',
    end_date: '',
  };
});

function add() {
  terms.value.unshift(newTerm.value);
  shouldAllowAdd.value = false;
}

function onStored() {
  shouldAllowAdd.value = true;
  getTerms();
}

function onCancelAdd() {
  terms.value.shift();
  shouldAllowAdd.value = true;
}
</script>

<style scoped>
@import '../../../css/subform_transition.css';
</style>
