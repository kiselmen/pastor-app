<script setup lang="ts">
const props = defineProps({
  color: { type: String, default: '' }
})
</script>
<template>
  <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path
      fill-rule="evenodd"
      clip-rule="evenodd"
      d="M16.4226 5.24408C16.748 5.56951 16.748 6.09715 16.4226 6.42259L8.67851 14.1667C8.02764 14.8175 6.97236 14.8175 6.32149 14.1667L2.74408 10.5893C2.41864 10.2638 2.41864 9.73618 2.74408 9.41074C3.06951 9.08531 3.59715 9.08531 3.92259 9.41074L7.5 12.9882L15.2441 5.24408C15.5695 4.91864 16.0972 4.91864 16.4226 5.24408Z"
      :fill="props.color?props.color:'white'"
    />
  </svg>
</template>
