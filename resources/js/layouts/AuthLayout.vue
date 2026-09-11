<script setup lang="ts">
defineProps<{
  title?: string
  description?: string
}>()
</script>

<template>
  <div class="min-h-screen bg-[#f6f3ee] text-[#181716]">
    <slot />
  </div>
</template>