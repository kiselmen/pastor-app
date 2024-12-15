<template>
      <!-- :text ="serviceFilterMaskText" -->
    <InputSelector
      v-if="nsiStore.availableServices"
      :id   = viewStore.serviceFilterMask
      :data ="nsiStore.availableServices"
      text ="Служение"
      @selectItem="onServiceSelect"
  />
</template>

<script setup>
  
  import { useViewStore } from '@/stores/viewStore';
  import { useNsiStore } from '@/stores/nsiStore';
  import InputSelector from '@/components/ui/InputSelector.vue';

  const emits = defineEmits(['changeFilter']);

  const viewStore = useViewStore();
  const nsiStore = useNsiStore();

  const onServiceSelect = (id) => {
    emits('changeFilter', id);
  }

</script>