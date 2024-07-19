<template>
  <InputSelector
      v-if="!loader"
      :text ="prihodFilerMaskText"
      :id   = viewStore.prihodFilerMask
      :data ="prihodStore.prihods"
      @selectItem="onPrihodSelect"
  />
</template>

<script setup>
  
  import { ref, onBeforeMount, computed } from 'vue';
  import { useViewStore } from "@/stores/viewStore";
  import { usePrihodStore } from '@/stores/prihodStore';
  import InputSelector from '@/components/ui/InputSelector.vue';

  const emits = defineEmits(['changeFilter']);

  const loader = ref(false);

  const viewStore = useViewStore();
  const prihodStore = usePrihodStore();

  const prihodFilerMaskText = computed(() => {
    if (viewStore.prihodFilerMask === null) {
      return "Участок"
    } else {
      const prihod = prihodStore.prihods.filter(item => item.id == viewStore.prihodFilerMask)[0];
      return prihod?.name + ' ' + prihod?.number;
    }
  });

  const onPrihodSelect = (id) => {
    // viewStore.setPrihodFilerMask(id);
    emits('changeFilter', id);
  }

  onBeforeMount(async () => {
    loader.value = true;
    await prihodStore.getPrihods();
    loader.value = false;
  })

</script>