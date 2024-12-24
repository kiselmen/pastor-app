<template>
  <RouterLink :to="'/family/' + props.family?.id">
    <div v-if="props.family" class="family-container">
      <div class="family-img" :style = "{ backgroundImage : 'url(' + getImgPath(props.family.head?.image_url) +')' }">
        <div class="family-number">{{ props.family?.FamilyComposition }}</div>
      </div>
      <span class="family-title">{{ props.family?.name }}</span>
    </div>
  </RouterLink>  
</template>

<script setup>

  import getImgPath from '@/utils/imagePlugin.js';

  const props = defineProps({
    family: { type: Object, default: new Object() },
  })


</script>

<style lang="scss" scoped>
  .family {
    &-container {
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
      text-align: center;
    }
    &-number {
      display: flex;
      right: -13px;
      top: 0px;
      position: absolute;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      justify-content: center;
      align-items: center;
      // background-color: var(--bs-primary);
      border: 1px solid var(--bs-primary);
      color: var(--bs-primary);
      outline: 4px solid var(--bs-white);
      font-size: 16px;
    }
    &-img {
      position: relative;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center center;
    }
  }
</style>