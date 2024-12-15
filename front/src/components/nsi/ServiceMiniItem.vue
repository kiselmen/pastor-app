<template>
  <div class="service-box">
    <div class="service-title">
      <!-- <div class="service-name">Служения</div> -->
      <DateItem>
        <template #icon>
          <ServiceIcon class="service-icon"/>
        </template>
        <template #heading>
          Служения
        </template>
      </DateItem>

      <div>
        <EditDuotoneIcon v-if="isAvailableEdit" class="change_button" @click="emits('editServices')"/>
      </div>
    </div>
    <div class="service-items">
      <div class="service-item" v-for="service in props.services" :key = "service.id">
        {{ service.ServiceName }}
      </div>
    </div>
  </div>
</template>

<script setup>
  import { computed } from 'vue';
  import { useUserStore } from '@/stores/userStore';
  import EditDuotoneIcon from '@/components/icons/IconEditDuotone.vue';
  import DateItem from '@/components/people/DateItem.vue';
  import ServiceIcon from '@/components/icons/IconService.vue';

  const props = defineProps({
    services: { type: Object, default: new Object() },
    isActionPossible: { type: Boolean, default: false },
  });

  const emits = defineEmits(['editServices']);

  const userStore = useUserStore();

  const isAvailableEdit = computed(() => {
    let isAdmin = false;
    userStore.user?.permition?.forEach(permition => {
      if (permition.type == 0 || permition.type == 1) isAdmin = true;
    });
    isAdmin = isAdmin&&props.isActionPossible
    return isAdmin;
  })

</script>

<style lang="scss" scoped>
  .service {
    &-box {
      // border-radius: 5px;
      padding: 5px;
      // border: 1px solid var(--bs-gray-300);
      width: 100%;
      min-height: 100px;      
      position: relative;
    }
    &-title {
      display: flex;
    }
    &-items {
      min-height: 80px;
      margin: 10px;
    }
    &-item {
      display: block;
      font-size: 14px;
    }
    &-icon {
       fill: var(--bs-primary);
    }
  }
</style>