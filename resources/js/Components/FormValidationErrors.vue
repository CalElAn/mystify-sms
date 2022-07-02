<template>
  <div
    v-if="Object.keys(errors).length"
    class="sm:col-span-3 my-2 rounded-md border border-red-500 p-2 text-sm text-red-500"
  >
    <ul class="list-inside list-disc">
      <li class="" v-for="(item, index) in errors" :key="index">
        {{ item }}
      </li>
    </ul>
  </div>
</template>

<script setup>
defineProps({ errors: Object });
</script>
