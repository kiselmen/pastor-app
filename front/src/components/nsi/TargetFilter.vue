<template>
  <InputSelector
      text ="Группа"
      :id   = viewStore.targetFilterMask
      :data ="nsiStore.targets"
      @selectItem="onTargetSelect"
  />
</template>

<script setup>
  import { useViewStore } from '@/stores/viewStore';
  import { useNsiStore } from '@/stores/nsiStore';
  import InputSelector from '@/components/ui/InputSelector.vue';

  const emits = defineEmits(['changeFilter']);

  const viewStore = useViewStore();
  const nsiStore = useNsiStore();

  const onTargetSelect = (id) => {
    emits('changeFilter', id);
  }

</script>