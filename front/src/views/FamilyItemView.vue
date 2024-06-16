<template>
  <div>
    Карточка семьи {{ props.id }}
  </div>
</template>

<script setup>
  const props = defineProps({
    id: { type: String, default: null },
  })
</script>

<style lang="scss">
</style>