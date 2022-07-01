<template>
  <Head title="Notice board"></Head>
  <TimelineCard
    :title="'Notice board'"
    :messages="noticeBoardMessages"
    class="w-full h-full"
  />
</template>

<script setup>
import TimelineCard from '@/Components/TimelineCard.vue';

defineProps({
  noticeBoardMessages: Object,
});
</script>
