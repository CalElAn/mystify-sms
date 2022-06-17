<template>
  <nav>
    <ul
      class="flex max-w-fit divide-x divide-gray-300 rounded-lg bg-white text-center font-semibold tracking-wide text-gray-500 shadow"
    >
      <li
        v-for="(item, index) in actions"
        :class="[
          { 'rounded-l-lg pl-1.5': first(actions) === item },
          { 'rounded-r-lg pr-1.5': last(actions) === item },
        ]"
      >
        <a
          :key="index"
          :href="item.href"
          :class="[
            {
              'border-b-2 border-blue-600 text-blue-600':
                route().current() === item.name,
            },
          ]"
          class="inline-block w-full py-4 px-6 hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
        >
          {{ item.label }}
        </a>
      </li>
    </ul>
  </nav>
</template>

<script setup>
import { first, last } from 'lodash';

defineProps({
  actions: Array
})
</script>
