<template>
  <InputSelector
      v-if="nsiStore.availableServices"
      :text ="serviceFilterMaskText"
      :id   = viewStore.serviceFilterMask
      :data ="nsiStore.availableServices"
      :message ="'Служение'"
      @selectItem="onServiceSelect"
  />
</template>

<script setup>
  
  import { ref, onBeforeMount, computed } from 'vue';
  import { useViewStore } from '@/stores/viewStore';
  import { useNsiStore } from '@/stores/nsiStore';
  // import { useUserStore } from '@/stores/userStore';
  import InputSelector from '@/components/ui/InputSelector.vue';

  const emits = defineEmits(['changeFilter']);

  const loader = ref(false);

  const viewStore = useViewStore();
  const nsiStore = useNsiStore();
  // const userStore = useUserStore();

  const serviceFilterMaskText = computed(() => {
    if (viewStore.serviceFilterMask === null) {
      return "Группа"
    } else {
      const service = nsiStore.services.filter(item => item.id == viewStore.serviceFilterMask)[0];
      return service?.name;
    }
  });

  const onServiceSelect = (id) => {
    emits('changeFilter', id);
  }

</script>