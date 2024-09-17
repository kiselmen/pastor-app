<template>
  <div class = "card">
    <div class="card-title">
      <span>
        <RouterLink :to="'/peoples?_prihod=' + props.prihod?.id">
          {{ props.prihod.name }}
        </RouterLink>  
      </span>
      <IconEditDuotone class="change_button" @click="onEditClick"/>
    </div>
    <div class="card-row">
      Номер: {{ props.prihod.number }}
    </div>
    <div class="card-row">
      Описание: {{ props.prihod.discription }} 
    </div>
  </div>
</template>

<script setup>
  import IconEditDuotone from '@/components/icons/IconEditDuotone.vue';

  const props = defineProps({
    prihod: { type: Object, default: new Object() },
  })

  const emits = defineEmits(['editPrihod'])

  const onEditClick = () => {
    console.log('Edit prihod ', props.prihod.id);
    emits('editPrihod', props.prihod.id)
  }
</script>

<style lang="scss" scoped>
</style>