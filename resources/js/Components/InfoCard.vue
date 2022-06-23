<template>
  <div
    class="rounded-lg bg-red-300 px-10 py-4 text-lg tracking-wide text-white shadow"
  >
    <p>{{ mainText }}</p>
    <div class="mt-2 flex justify-end">
      <Link
        :href="linkHref"
        class="rounded-lg border border-fuchsia-600 bg-red-50 p-2 text-base font-semibold tracking-wide text-fuchsia-600 shadow-sm hover:bg-red-100"
      >
        {{ linkText }}
      </Link>
    </div>
  </div>
  <!-- <div class="base-card border tracking-wide border-red-500 px-10 py-4 text-lg text-red-400">
    <p>
      You have no subjects associated with your account for this academic year
      ({{ term.academic_year.name }})
    </p>
    <div class="mt-2 flex justify-end">
      <button
        class="rounded-lg border border-transparent bg-purple-100 p-2 text-base font-semibold tracking-wide text-purple-600 hover:bg-purple-200"
      >
        Add subject
      </button>
    </div>
  </div> -->
</template>

<script setup>
defineProps({
  mainText: String,
  linkText: String,
  linkHref: String,
});
</script>
