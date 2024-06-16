<template>
  <div class = "card">
    <div class="card-title">
      <span>
        {{ props.persone.name }} {{ props.persone.first_name }} {{ props.persone.patronymic }}
      </span>
      <div class="change_button">
        <!-- <EditDuotoneIcon  @click="onEditClick"/> -->
        <ActionMenu 
          :actions = "actions"
          @startAction="onStartAction"
        >
          <template #icon>
            <EditDuotoneIcon/>
          </template>
        </ActionMenu>
      </div>

    </div>
    <div class="card-box">
      <div class="card-row">
        <div class="card-img" :style = "{ backgroundImage : 'url(' + getImgPath(props.persone.image_url) +')' }"></div>
        <div class="card-column">
          <PrihodMiniItem
            :prihod = "props.persone.prihod"
          />
          <div class="card-target">{{ props.persone?.TargetName }}</div>
          <FamilyMiniItem
            :family = "props.persone.family"
          />
        </div>
      </div>
    </div>  
    <div class="card-box">
      <div class="card-row">
        <DateItem>
          <template #icon>
            <BirthdayIcon />
          </template>
          <template #heading>
            {{ (new Date(props.persone.birthday_date)).toDateString() }}
          </template>
        </DateItem>
        <DateItem>
          <template #icon>
            <CrossIcon />
          </template>
          <template #heading v-if="props.persone.baptism_date">
            {{ (new Date(props.persone.baptism_date)).toDateString() }}
          </template>
        </DateItem>
      </div>
      <div class="card-row">
        <DateItem>
          <template #icon>
            <RIPIcon />
          </template>
          <template #heading>
            {{ props.persone.death_date ? (new Date(props.persone.death_date)).toDateString(): '-' }}
          </template>
        </DateItem>
      </div>
    </div>
    <div class="card-box">
      <div class="card-row">
        <DateItem>
          <template #icon>
            <AdressIcon />
          </template>
          <template #heading>
            {{ props.persone.live_addres }}
          </template>
        </DateItem>
      </div>
      <div class="card-row">
        <DateItem>
          <template #icon>
            <PhoneIcon />
          </template>
          <template #heading>
            {{ props.persone.mobile_phone }}
          </template>
        </DateItem>
      <!-- </div>
      <div class="card-row"> -->
        <DateItem>
          <template #icon>
            <PhoneIcon />
          </template>
          <template #heading>
            {{ props.persone.home_phone }}
          </template>
        </DateItem>
      </div>
    </div>   
    <div class="card-row">
        <ServiceMiniItem
          :services = "props.persone.pservice"
          @editServices = "onEditPersonServices"
        />

      </div> 
  </div>
</template>

<script setup>
  import getImgPath from '@/utils/imagePlugin.js';
  import EditDuotoneIcon from '@/components/icons/IconEditDuotone.vue';
  import CrossIcon from '@/components/icons/IconCross.vue';
  import BirthdayIcon from '@/components/icons/IconBirthday.vue';
  import RIPIcon from '@/components/icons/IconRIP.vue';
  import AdressIcon from '@/components/icons/IconAdress.vue';
  import PhoneIcon from '@/components/icons/IconPhone.vue';
  import DateItem from '@/components/people/DateItem.vue';
  import PrihodMiniItem from '@/components/prihod/PrihodMiniItem.vue';
  import ServiceMiniItem from '@/components/nsi/ServiceMiniItem.vue';
  import FamilyMiniItem from '@/components/family/FamilyMiniItem.vue';
  import { onBeforeMount, ref } from 'vue';
  import ActionMenu from '@/components/ui/ActionMenu.vue';

  const props = defineProps({
    persone: { type: Object, default: new Object() },
  })

  const emits = defineEmits(['editPerson', 'editPersonServices']);

  const actions = ref([]);

  // const onEditClick = () => {
  //   // console.log('Edit persone ', props.persone.id);
  //   emits('editPerson', props.persone.id)
  // }

  const onStartAction = (action) => {
    // console.log('onStartAction ', action);
    const isAction = actions.value.filter(item => item.id === action);
    if (isAction.length) {
      const actionEmit = isAction[0].emit;
      console.log('action ', actionEmit);
      emits(actionEmit, props.persone.id);
    } else {
      console.log('Нет такой операции');
    }
  };

  const onEditPersonServices = () => {
    emits('editPersonServices', props.persone.id);
  }

  onBeforeMount(async () => {
    actions.value = [
      { id: 0, name: 'Изменить', emit: 'editPerson' },
      { id: 1, name: 'Регистрация', emit: 'registerPerson' },
    ]
  })
</script>

<style lang="scss" scoped>
  .action-container {
    align-items: baseline;
  }
</style>