<template>
  <div class="profile">
    <div v-if="loader" class="card-text">Loading...</div>
    <div v-if="!loader&&!peopleStore.onePersone" class="card-text">
        <button 
          @click="router.push('/peoples')" 
          type="button" 
          class="btn btn-blue" 
        >
            К списку
        </button>
    </div>
    <div v-if="!loader&&peopleStore.onePersone" class="card-name">
      <div :class="personeStatusStyle" :style = "{background: personeStatusColor }">
        {{ personeStatusLength ? personeStatusLength: ''}}
      </div> 
      <div :class = "personeTitleStyle">
        {{ peopleStore.onePersone.name }} {{ peopleStore.onePersone.first_name }} {{ peopleStore.onePersone.patronymic }}
      </div> 
    </div>
    <div v-if="!loader&&peopleStore.onePersone" class="card-container">
      <div class="square4x">
        <div class="card-box" ref="mainElem">
          <div class="card-name">
            Ощие данные
            <div class="change_button">
              <ActionMenu 
                :actions = "commonActions"
                :parentElem = "mainElem"
                @startAction="onStartCommonAction"
              >
                <template #icon >
                  <EditDuotoneIcon v-if="commonActions.length"/>
                </template>
              </ActionMenu>
            </div>
          </div>
          <div class="card-row">
            <div class="card-column">
              <div class="card-img" :style = "{ backgroundImage : 'url(' + getImgPath(peopleStore.onePersone.image_url) +')' }"></div>

              <div class="sex-men" v-if="peopleStore.onePersone.sex_id === 1">{{ peopleStore.onePersone.SexName }}</div>
              <div class="sex-women" v-if="peopleStore.onePersone.sex_id === 2">{{ peopleStore.onePersone.SexName }}</div>
            </div>
            <div class="card-column">
              <DateItem>
                <template #icon>
                  <BirthdayIcon />
                </template>
                <template #heading v-if="peopleStore.onePersone.birthday_date">
                  {{ (new Date(peopleStore.onePersone.birthday_date)).toDateString() }}
                </template>
              </DateItem>
              <DateItem>
                <template #icon>
                  <CrossIcon />
                </template>
                <template #heading v-if="peopleStore.onePersone.baptism_date">
                  {{ (new Date(peopleStore.onePersone.baptism_date)).toDateString() }}
                </template>
              </DateItem>
              <DateItem v-if="peopleStore.onePersone.death_date">
                <template #icon>
                  <RIPIcon />
                </template>
                <template #heading >
                  {{ (new Date(peopleStore.onePersone.death_date)).toDateString() }}
                </template>
              </DateItem>
              <DateItem>
                <template #icon>
                  <AdressIcon />
                </template>
                <template #heading>
                  {{ peopleStore.onePersone.live_addres }}
                </template>
              </DateItem>
              <DateItem>
                <template #icon>
                  <PhoneIcon />
                </template>
                <template #heading>
                  {{ peopleStore.onePersone.mobile_phone }}
                </template>
              </DateItem>
              <DateItem>
                <template #icon>
                  <PhoneIcon />
                </template>
                <template #heading>
                  {{ peopleStore.onePersone.home_phone }}
                </template>
              </DateItem>

              <div class="card-text"> {{ peopleStore.onePersone.email}}</div>

              <hr class="custom-hr">

              <div class="card-target" v-for="target in peopleStore.onePersone.ptarget">{{target.TargetName }}</div>
            </div>
          </div>
        </div>
        <div class="card-box" ref="levelElem">
          <div class="card-name">
            Дисциплина
            <div class="change_button">
              <ActionMenu 
                :actions = "levelActions"
                :parentElem = "levelElem"
                @startAction="onStartLevelAction"
              >
                <template #icon >
                  <EditDuotoneIcon v-if="levelActions.length"/>
                </template>
              </ActionMenu>
            </div>
          </div>
          <div v-if="peopleStore.onePersone.plevel.length" class="card-table">
            <Table
              :tableHeadNames = "levelTableHeadNames"
              :tableHeadID = "levelTableHeadID"
              :tableData = "peopleStore.onePersone.plevel"
              :hasActiveBtn = "false"
              :isAllRowSelected = "isAllRowSelected"
              :actions = "levelActions"
            />
          </div>
          <div v-if="!peopleStore.onePersone.plevel.length" class="card-table">Нарушений нет</div>
        </div>
        <div class="card-box">
          <div class="card-name">Семья</div>
          <div v-if="peopleStore.onePersone.family?.people?.length" class="card-table">
            <Table
              :tableHeadNames = "familyTableHeadNames"
              :tableHeadID = "familyTableHeadID"
              :tableData = "peopleStore.onePersone.family?.people"
              :hasActiveBtn = "false"
              :isAllRowSelected = "isAllRowSelected"
              :actions = "levelActions"
              :activeRows = "[props.id]"
            />
          </div>
        </div>
        <div class="card-box" ref="serviceElem">
          <div class="card-name">
            Участок и занятия
            <div class="change_button">
              <ActionMenu 
                :actions = "serviceActions"
                :parentElem = "serviceElem"
                @startAction="onStartPersoneAction"
              >
                <template #icon >
                  <EditDuotoneIcon v-if="serviceActions.length"/>
                </template>
              </ActionMenu>
            </div>
          </div>
            <div class="square2x">
              <div class="card-box">
                <div class="card-column">
                  <PrihodMiniItem
                    :prihod = "peopleStore.onePersone.prihod"
                  />
                </div>
              </div>
              <div class="card-box" v-if="peopleStore.onePersone.pservice.length">
                <div class="card-column">
                  <div class="card-text" v-for="service in peopleStore.onePersone.pservice">{{ service.ServiceName }}</div>
                </div>  
              </div>
            </div>
            <!-- <div v-if="persone.pservice.length" class="card-table">
              <Table
                :tableHeadNames = "serviceTableHeadNames"
                :tableHeadID = "serviceTableHeadID"
                :tableData = "persone.pservice"
                :hasActiveBtn = "false"
                :isAllRowSelected = "isAllRowSelected"
                :actions = "serviceActions"
              />
            </div> -->
        </div>
      </div>
    </div>
  </div>
  <ModalWrapper
    :is-modal-active="isModalAction"
    @close-modal="isModalAction = false"
  >
    <EditPersone v-if="actionName === 'editPersone'"
        @toggle-modal="isModalAction = false"
        :id="activePersone"
    />
    <EditPersoneLevels v-if="actionName === 'editPersoneLevels'"
        @toggle-modal="isModalAction = false"
        :id="activePersone"
    />
    <EditPersoneServices v-if="actionName === 'editPersoneServices'"
        @toggle-modal="isModalAction = false"
        :id="activePersone"
    />
    <EditPersoneTargets v-if="actionName === 'editPersoneTargets'"
        @toggle-modal="isModalAction = false"
        :id="activePersone"
    />
    <RegisterNewUser v-if="actionName === 'registerNewUser'"
        @toggle-modal="isModalAction = false"
        :id="activePersone"
    />
    <ChangeUserPass v-if="actionName === 'changeUserPass'"
        @toggle-modal="isModalAction = false"
        :id="activeUser"
    />
    <ChangeUserPermitions v-if="actionName === 'changeUserPermitions'"
        @toggle-modal="isModalAction = false"
        :id="activeUser"
    />
  </ModalWrapper>
</template>

<script setup>
  import getImgPath from '@/utils/imagePlugin.js';
  import { ref, computed, onBeforeMount } from 'vue';
  import { useUserStore } from '@/stores/userStore';
  import { usePeopleStore } from '@/stores/peopleStore';
  import router from '@/router';
  import CrossIcon from '@/components/icons/IconCross.vue';
  import BirthdayIcon from '@/components/icons/IconBirthday.vue';
  import RIPIcon from '@/components/icons/IconRIP.vue';
  import AdressIcon from '@/components/icons/IconAdress.vue';
  import PhoneIcon from '@/components/icons/IconPhone.vue';
  import DateItem from '@/components/people/DateItem.vue';
  import EditDuotoneIcon from '@/components/icons/IconEditDuotone.vue';
  import PrihodMiniItem from '@/components/prihod/PrihodMiniItem.vue';
  import Table from '@/components/ui/Table.vue';
  import ActionMenu from '@/components/ui/ActionMenu.vue';
  import ModalWrapper from '@/components/ui/ModalWrapper.vue';
  import EditPersone  from '@/components/people/EditPersone.vue';
  import EditPersoneServices from '@/components/nsi/EditPersoneServices.vue';
  import EditPersoneTargets from '@/components/nsi/EditPersoneTargets.vue';
  import EditPersoneLevels from '@/components/nsi/EditPersoneLevels.vue';
  import RegisterNewUser from '@/components/nsi/RegisterNewUser.vue';
  import ChangeUserPass from '@/components/nsi/ChangeUserPass.vue';
  import ChangeUserPermitions from '@/components/nsi/ChangeUserPermitions.vue';

  const props = defineProps({
    id: { type: String, default: null },
  });

  const userStore = useUserStore();
  const peopleStore = usePeopleStore();

  const loader =ref(false);
  const commonActions = ref([]);
  const levelActions = ref([]);
  const serviceActions = ref([]);

  const actionName = ref('');
  const isModalAction = ref(false);
  const activePersone = ref(null);
  const activeUser = ref(null);
  const mainElem = ref(null);
  const levelElem = ref(null);
  const serviceElem = ref(null);

  const levelTableHeadNames = ref(['Дата', 'Уровень', 'Описание', 'Автор']);
  const levelTableHeadID = ref(['date', 'LevelName', 'discription', 'UserName']);

  const familyTableHeadNames = ref(['Фамилия', 'Имя', 'Отчество', 'Статус', 'Приход']);
  const familyTableHeadID = ref(['name', 'first_name', 'patronymic', 'TargetName', 'PrihodName']);

  const isAllRowSelected = ref(false);

  const personeStatusColor = computed(() => {
    const isStatus = peopleStore.onePersone.plevel.length;
    const totalRecords = peopleStore.onePersone.plevel.length;
    let levelSyle = '';
    if (isStatus) {
      const lastStatus = peopleStore.onePersone.plevel[totalRecords-1];
      levelSyle = lastStatus.level.color;
    }  
    return levelSyle;
  })

  const personeStatusStyle = computed(() => {
    const isStatus = peopleStore.onePersone.plevel.length;
    return isStatus ? 'colored': '';
  });

  const personeTitleStyle = computed(() => {
    return personeStatusStyle.value ? 'space': '';
  })

  const personeStatusLength = computed(() => {
    return peopleStore.onePersone.plevel.length;
  });

  const onStartCommonAction = (action) => {
    const isAction = commonActions.value.filter(item => item.id === action);
    if (isAction.length) {
      actionName.value = isAction[0].emit;
      activePersone.value = null;
      activeUser.value = null;
      isModalAction.value = true;
    } else {
      console.log('Нет такой операции');
    }
  }

  const onStartLevelAction = (action) => {
    const isAction = levelActions.value.filter(item => item.id === action);
    if (isAction.length) {
      actionName.value = isAction[0].emit;
      activePersone.value = null;
      // activeUser.value = null;
      isModalAction.value = true;
    } else {
      console.log('Нет такой операции');
    }
  }

  const onStartPersoneAction = (action) => {
    const isAction = serviceActions.value.filter(item => item.id === action);
    if (isAction.length) {
      actionName.value = isAction[0].emit;
      activePersone.value = null;
      // activeUser.value = null;
      isModalAction.value = true;
    } else {
      console.log('Нет такой операции');
    }
  }

  onBeforeMount( async () => {
    loader.value = true;
    await peopleStore.getOnePersone(props.id);
    let isAdmin = false;
    const prihodIDs = [];
    const serviceIDs = [];
    userStore.user.permition.forEach(permition => {
      if (permition.type == 0) isAdmin = true;
      if (permition.type == 1) prihodIDs.push(permition.source_id);
      if (permition.type == 2) serviceIDs.push(permition.source_id);
    });
    if (isAdmin) {
      const isUserPresent = peopleStore.onePersone.user_id;
      if (isUserPresent) {
        commonActions.value = [
          { id: 0, name: 'Изменить', emit: 'editPersone' },
          // { id: 1, name: 'Дисциплина', emit: 'editPersoneLevels' },
          { id: 1, name: 'Сменить пароль', emit: 'changeUserPass' },
          { id: 2, name: 'Права доступа', emit: 'changeUserPermitions' },
        ];
        levelActions.value = [
          { id: 0, name: 'Изменить', emit: 'editPersoneLevels' },
        ];
        serviceActions.value = [
          { id: 0, name: 'Изменить', emit: 'editPersoneServices' },
        ];
      } else {
        commonActions.value = [
          { id: 0, name: 'Изменить', emit: 'editPersone' },
          // { id: 1, name: 'Дисциплина', emit: 'editPersoneLevels' },
          { id: 1, name: 'Регистрация', emit: 'registerNewUser' },
        ];
        levelActions.value = [
          { id: 0, name: 'Изменить', emit: 'editPersoneLevels' },
        ];
        serviceActions.value = [
          { id: 0, name: 'Изменить', emit: 'editPersoneServices' },
        ];
      }
    } else {
      const isPrihodAdmin = prihodIDs.filter(item => item == peopleStore.onePersone.prihod_id).length;
      if (isPrihodAdmin) {
        commonActions.value = [
          { id: 0, name: 'Изменить', emit: 'editPersone' },
          { id: 1, name: 'Целевые группы', emit: 'editPersoneTargets' },
        ];
        levelActions.value = [
          { id: 0, name: 'Изменить', emit: 'editPersoneLevels' },
        ];
        serviceActions.value = [
          { id: 0, name: 'Изменить', emit: 'editPersoneServices' },
        ];
      } else {
        const isServiceAdmin = serviceIDs.filter(item => peopleStore.onePersone.pservice.filter(service => service.service_id == item).length).length;
        if (isServiceAdmin) {
          commonActions.value = [];
          levelActions.value = [];
          serviceActions.value = [
            { id: 0, name: 'Изменить', emit: 'editPersoneServices' },
          ];
        } else {
          commonActions.value = [];
          levelActions.value = [];
          serviceActions.value = [];
        }
      }
    }
    loader.value = false;
  });


</script>

<style lang="scss">
  .profile {
    padding: 10px;
  }

  .square4x {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
  }

  .square2x {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    align-items: center;
  }

  .square4x .card-box {
    flex-basis: 48%;
    flex-grow: 3;
    margin: 0;
    padding: 5px;
    border: 1px solid var(--bs-gray-300);
    border-radius: 5px;
  }

  .square2x .card-box {
    flex-basis: 48%;
    // flex-grow: 3;
    margin: 0;
    padding: 5px;
    border: none;
  }

  .card {
    &-img {
      margin-right: 10px;
    }
    &-name {
      position: relative;
      // background-color: var(--bs-primary);
      // color: var(--bs-white);
    }
  }

  .action-container {
    align-items: baseline;
  }
  .colored {
    display: flex;
    left: 0px;
    top: 0px;
    position: absolute;
    width: 40px;
    height: 100%;
    justify-content: center;
    align-items: center;
    color: var(--bs-black);
    font-size: 14px;    
  }
  .space {
    margin-left: 40px;
  }
  .custom-hr {
    border: none; /* Убираем стандартное обрамление */
    height: 2px; /* Высота полоски */
    background-color: var(--bs-gray-400); /* Цвет полоски */
    width: 80%; /* Ширина полоски (100% - на всю ширину родителя) */
    margin: 20px 0; /* Отступы сверху и снизу */
  }
  .card-column {
    justify-content: space-between;    
  }
  .sex{
    &-men {
      padding-left: 10px;
      color: var(--bs-primary);
    }
    &-women {
      padding-left: 10px;
      color: var(--bs-pink);
    }
  }

</style>