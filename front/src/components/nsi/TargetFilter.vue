<template>
  <InputSelector
      v-if="!loader"
      :text ="targetFilterMaskText"
      :id   = viewStore.targetFilterMask
      :data ="nsiStore.targets"
      :message ="'Группа'"
      @selectItem="onTargetSelect"
  />
</template>

<script setup>
  
  import { ref, onBeforeMount, computed } from 'vue';
  import { useViewStore } from '@/stores/viewStore';
  import { useNsiStore } from '@/stores/nsiStore';
  import InputSelector from '@/components/ui/InputSelector.vue';

  const emits = defineEmits(['changeFilter']);

  const loader = ref(false);

  const viewStore = useViewStore();
  const nsiStore = useNsiStore();

  const targetFilterMaskText = computed(() => {
    if (viewStore.targetFilterMask === null) {
      return "Группа"
    } else {
      const target = nsiStore.targets.filter(item => item.id == viewStore.targetFilterMask)[0];
      return target?.name;
    }
  });

  const onTargetSelect = (id) => {
    emits('changeFilter', id);
  }

</script>