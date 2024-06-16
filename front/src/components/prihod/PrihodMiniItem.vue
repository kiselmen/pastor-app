<template>
  <div class="church-container">
    <RouterLink :to="'/prihod/' + props.prihod?.id">
      <ChurchIcon class="church-item" />
    </RouterLink>  
    <span class="church-title">{{ props.prihod?.name }}</span>
    <div class="church-number">{{ props.prihod?.number }}</div>
  </div>
</template>

<script setup>
  import ChurchIcon from '@/components/icons/IconChurch.vue';

  const props = defineProps({
    prihod: { type: Object, default: new Object() },
  })


</script>

<style lang="scss" scoped>
  .church {
    &-container {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      max-width: 100px;    
    }
    &-item {
      width: 100px;
      height: 80px    
    }
    &-title {
      margin: 0 auto;
    }
    &-number {
      display: flex;
      right: 0px;
      top: 0px;
      position: absolute;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      justify-content: center;
      align-items: center;
      background-color: var(--bs-success);
      color: var(--bs-white);
      font-size: 14px;
    }
  }
</style>