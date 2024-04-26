<template>
  <svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M1 1L7 7M7 1L1 7" :stroke="props.color ? props.color : '#292C33'" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
  </svg>
</template>

<script setup lang="ts">
const props = defineProps({ color: { type: String, default: '' } })
</script>
