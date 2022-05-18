<template>
  <button
    :class="[
      active ? 'bg-violet-500 text-white' : 'text-gray-900',
      'group flex w-full items-center justify-start rounded-md px-2 py-2 text-sm',
    ]"
  >
    <slot/>
  </button>
</template>

<script setup>
defineProps({
  active: Boolean
})
</script>
