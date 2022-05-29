<template>
  <div v-if="links.length > 3">
    <div class="flex flex-wrap justify-center gap-2">
      <template v-for="(link, key) in links">
        <div
          v-if="link.url === null"
          :key="key"
          class="base-card mb-1 px-4 py-3 leading-4 text-gray-400"
          v-html="link.label"
        />
        <Link
          v-else
          :key="`link-${key}`"
          class="base-card mb-1 border px-4 py-3 leading-4 hover:border-fuchsia-600 hover:text-fuchsia-600 focus:border-purple-600 focus:text-purple-600"
          :class="{
            'border border-fuchsia-600 font-semibold text-fuchsia-600':
              link.active,
          }"
          :href="link.url"
          v-html="link.label"
        />
      </template>
    </div>
  </div>
</template>

<script setup>
defineProps({
  links: Array,
});
</script>
