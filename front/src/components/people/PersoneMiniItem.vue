<template>
  <RouterLink :to="'/people/' + props.persone?.id">
    <div v-if="props.persone" class="family-container">
      <div class="family-img" :style = "{ backgroundImage : 'url(' + getImgPath(props.persone.image_url) +')' }"></div>
      <span class="family-title">{{ props.persone?.first_name }}</span>
    </div>
  </RouterLink>  
</template>

<script setup>

  import getImgPath from '@/utils/imagePlugin.js';

  const props = defineProps({
    persone: { type: Object, default: new Object() },
  })


</script>

<style lang="scss" scoped>
  .family {
    &-container {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      align-items: center;
      max-width: 100px;
      margin: auto;    
    }
    &-title {
      margin: 0 auto;
      color: var(--bs-success);
    }
    &-img {
      border-radius: 50%;
      width: 50px;
      height: 50px;
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center center;
    }
  }
</style>