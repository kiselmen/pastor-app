<template>
  <svg width="16" height="16" viewBox="0 0 16 16" fill="var(--bs-gray-600)" xmlns="http://www.w3.org/2000/svg">
    <path
      d="M12.6693 6.0013L8.0026 10.668L3.33594 6.0013"
      :stroke="color"
      stroke-width="1.6"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
  </svg>
</template>

<script>
export default {
  name: 'IconChevronDown',
  props: {
    color: {
      type: String,
      default: '#FFFFFF'
    }
  }
}
</script>

<style lang="scss" scoped>
svg {
  cursor: pointer;
}
</style>
