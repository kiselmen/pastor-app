<template>
  <div class = "card">
    <div class="card-title" 
        :class="{ 'is-global': props.prihod?.is_global }"
    >
      <div>
        <RouterLink :to="'/peoples?_prihod=' + props.prihod?.id">
          {{ props.prihod.name }}
        </RouterLink>  
      </div>
      <IconEditDuotone class="change_button" @click="onEditClick"/>
    </div>
    <div class="card-row">
      Номер: {{ props.prihod.number }}
    </div>
    <div class="card-row">
      Описание: {{ props.prihod.discription }} 
    </div>
    <div class="card-row">
      Тип: {{ createType(props.prihod?.is_global) }} 
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

  const createType = (type) => {
    return !type ? 'Локальная церковь': 'Другие церкви';
  }
</script>

<style lang="scss" scoped>
  .is-global {
    background-color: var(--bs-danger);
  }
  
  a {
    text-decoration: none;
    color: var(--bs-white);
  }

</style>