<template>
  <InputSelector
      text ="Участок"
      :id   = viewStore.prihodFilterMask
      :data ="prihodStore.prihods"
      @selectItem="onPrihodSelect"
  />
</template>

<script setup>
  
  import { useViewStore } from '@/stores/viewStore';
  import { usePrihodStore } from '@/stores/prihodStore';
  import InputSelector from '@/components/ui/InputSelector.vue';

  const emits = defineEmits(['changeFilter']);

  const viewStore = useViewStore();
  const prihodStore = usePrihodStore();

  const onPrihodSelect = (id) => {
    emits('changeFilter', id);
  }

</script>