<template>
  <div>
    Карточка прихожанина {{ props.id }}
  </div>
</template>

<script setup>
  const props = defineProps({
    id: { type: String, default: null },
  })
</script>

<style lang="scss">
</style>