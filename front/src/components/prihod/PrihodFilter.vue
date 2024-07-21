<template>
  <InputSelector
      v-if="!loader"
      :text ="prihodFilterMaskText"
      :id   = viewStore.prihodFilterMask
      :data ="prihodStore.prihods"
      :message ="'Участок'"
      @selectItem="onPrihodSelect"
  />
</template>

<script setup>
  
  import { ref, onBeforeMount, computed } from 'vue';
  import { useViewStore } from '@/stores/viewStore';
  import { usePrihodStore } from '@/stores/prihodStore';
  import InputSelector from '@/components/ui/InputSelector.vue';

  const emits = defineEmits(['changeFilter']);

  const loader = ref(false);

  const viewStore = useViewStore();
  const prihodStore = usePrihodStore();

  const prihodFilterMaskText = computed(() => {
    if (viewStore.prihodFilterMask === null) {
      return "Участок"
    } else {
      const prihod = prihodStore.prihods.filter(item => item.id == viewStore.prihodFilterMask)[0];
      return prihod?.name + ' ' + prihod?.number;
    }
  });

  const onPrihodSelect = (id) => {
    emits('changeFilter', id);
  }

</script>