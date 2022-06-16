<template>
  <button
    class="inline-flex items-center justify-center gap-1 rounded-lg border border-purple-600 px-2 py-1 font-semibold tracking-wide text-purple-600 shadow-sm hover:bg-purple-100 disabled:opacity-50"
  >
    <PlusCircleIcon class="h-5 w-5" />
    <slot />
  </button>
</template>

<script setup>
import { PlusCircleIcon } from '@heroicons/vue/outline';
</script>
