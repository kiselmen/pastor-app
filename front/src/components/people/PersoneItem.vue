<template>
  <div class = "card">
    <div class="card-title">
        <div :class="personeStatusStyle" :style = "{background: personeStatusColor }">
          {{ personeStatusLength ? personeStatusLength: ''}}
        </div> 
        <div :class = "personeTitleStyle">
          <RouterLink :to="'/people/' + props.persone?.id">
            {{ props.persone.name }} {{ props.persone.first_name }} {{ props.persone.patronymic }}
          </RouterLink>  
        </div>
        <div class="change_button">
          <!-- <EditDuotoneIcon  @click="onEditClick"/> -->
          <ActionMenu 
            :actions = "actions"
            @startAction="onStartAction"
          >
            <template #icon >
              <EditDuotoneIcon v-if="isAvailableActions"/>
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
          @editServices = "onEditPersoneServices"
        />

      </div> 
  </div>
</template>

<script setup>
  import getImgPath from '@/utils/imagePlugin.js';
  import { onBeforeMount, ref, computed } from 'vue';
  import { useUserStore } from '@/stores/userStore';
  import { usePeopleStore } from '@/stores/peopleStore' ;
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
  import ActionMenu from '@/components/ui/ActionMenu.vue';

  const props = defineProps({
    persone: { type: Object, default: new Object() },
  });

  const userStore = useUserStore();
  const peopleStore = usePeopleStore();

  const emits = defineEmits(['editPersone', 'editPersoneLevels', 'editPersoneServices', 'registerNewUser', 'changeUserPass', 'changeUserPermitions']);

  const actions = ref([]);

  const isAvailableActions = computed(() => {
    return actions.value.length;
  });

  const personeStatusColor = computed(() => {
    const isStatus = props.persone.plevel.length;
    const totalRecords = props.persone.plevel.length;
    let levelSyle = '';
    if (isStatus) {
      const lastStatus = props.persone.plevel[totalRecords-1];
      levelSyle = lastStatus.level.color;
    }  
    return levelSyle;
  })

  const personeStatusStyle = computed(() => {
    const isStatus = props.persone.plevel.length;
    return isStatus ? 'colored': '';
  });

  const personeTitleStyle = computed(() => {
    return personeStatusStyle.value ? 'space': '';
  })

  const personeStatusLength = computed(() => {
    return props.persone.plevel.length;
  });

  const onStartAction = (action) => {
    const isAction = actions.value.filter(item => item.id === action);
    if (isAction.length) {
      const actionEmit = isAction[0].emit;
      // console.log('actionEmit ', actionEmit);
      emits(actionEmit, props.persone.id);
    } else {
      console.log('Нет такой операции');
    }
  };

  const onEditPersoneServices = () => {
    emits('editPersoneServices', props.persone.id);
  }

  onBeforeMount(async () => {
    let isAdmin = false;
    const prihodIDs = [];
    const serviceIDs = [];
    userStore.user.permition.forEach(permition => {
      if (permition.type == 0) isAdmin = true;
      if (permition.type == 1) prihodIDs.push(permition.source_id);
      if (permition.type == 2) serviceIDs.push(permition.source_id);
    });
    if (isAdmin) {
      const isUserPresent = peopleStore.peoples.filter(item => item.id == props.persone.id)[0].user_id;
      if (isUserPresent) {
        actions.value = [
          { id: 0, name: 'Изменить', emit: 'editPersone' },
          { id: 1, name: 'Дисциплина', emit: 'editPersoneLevels' },
          { id: 3, name: 'Сменить пароль', emit: 'changeUserPass' },
          { id: 4, name: 'Права доступа', emit: 'changeUserPermitions' },
        ]
      } else {
        actions.value = [
          { id: 0, name: 'Изменить', emit: 'editPersone' },
          { id: 1, name: 'Дисциплина', emit: 'editPersoneLevels' },
          { id: 2, name: 'Регистрация', emit: 'registerNewUser' },
        ]
      }
    } else {
      const isPrihodAdmin = prihodIDs.filter(item => item == props.persone.prihod_id).length;
      if (isPrihodAdmin) {
        actions.value = [
          { id: 0, name: 'Изменить', emit: 'editPersone' },
          { id: 1, name: 'Дисциплина', emit: 'editPersoneLevels' },
        ]
      } else {
        const isServiceAdmin = serviceIDs.filter(item => props.persone.pservice.filter(service => service.service_id == item).length).length;
        if (isServiceAdmin) {
          actions.value = [
            { id: 1, name: 'Дисциплина', emit: 'editPersoneLevels' },
          ]
        } else {
          actions.value = [];
        }
      }
    }
  })
</script>

<style lang="scss" scoped>
  .action-container {
    align-items: baseline;
  }
  .colored {
    display: flex;
    left: 0px;
    top: 0px;
    position: absolute;
    width: 40px;
    height: 40px;
    justify-content: center;
    align-items: center;
    color: var(--bs-black);
    font-size: 14px;    
  }
  .space {
    margin-left: 40px;
  }
  a {
    text-decoration: none;
    color: var(--bs-white);
  }
</style>