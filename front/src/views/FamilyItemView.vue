<template>
  <div class="profile">
    <div v-if="loader" class="card-text">Loading...</div>
    <div v-if="!loader&&!familyStore.oneFamily" class="card-text">
        <button 
          @click="router.push('/families')" 
          type="button" 
          class="btn btn-blue" 
        >
            К списку
        </button>
    </div>
    <div v-if="!loader&&familyStore.oneFamily" class="card-name">
      <div>
        {{ familyStore.oneFamily.name }}
      </div> 
    </div>
    <div v-if="!loader&&familyStore.oneFamily" class="card-container">
      <div class="square2x">
        <div class="card-box" ref="mainElem">
          <div class="card-name">
            Глава
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
            <div class="card-img" :style = "{ backgroundImage : 'url(' + getImgPath(familyStore.oneFamily.head?.image_url) +')' }"></div>
            <div class="card-column">
              <PrihodMiniItem
                v-if="familyStore.oneFamily.head"
                :prihod = "familyStore.oneFamily.head.prihod"
              />
              <div class="card-target">Размер: {{ familyStore.oneFamily.FamilyComposition }}</div>
            </div>
          </div>
        </div>

        <div class="card-box">
          <div class="card-name">
            Состав семьи
          </div>
          <div class="card-row flex-wrap">
            <PersoneMiniItem
                v-for="persone in familyStore.oneFamily.people"
                :key = "persone.id"
                :persone = "persone"
            />
          </div>
        </div>
      </div>
      <div class="card-row">
        <DateItem>
          <template #icon>
            <!-- <BirthdayIcon /> -->
          </template>
          <template #heading>
            Описание: {{ familyStore.oneFamily.discription }}
          </template>
        </DateItem>
      </div>
    </div>
  </div>
  <ModalWrapper
    :is-modal-active="isModalAction"
    @close-modal="isModalAction = false"
  >
    <EditFamily v-if="actionName === 'editFamily'"
        @toggle-modal="isModalAction = false"
        :id="activeFamily"
    />
  </ModalWrapper>
</template>

<script setup>
  import getImgPath from '@/utils/imagePlugin.js';
  import { ref, computed, onBeforeMount } from 'vue';
  import { useFamilyStore } from '@/stores/familyStore';
  import { useUserStore } from '@/stores/userStore';
  import DateItem from '@/components/people/DateItem.vue';
  import PrihodMiniItem from '@/components/prihod/PrihodMiniItem.vue';
  import PersoneMiniItem from '@/components/people/PersoneMiniItem.vue';
  import ActionMenu from '@/components/ui/ActionMenu.vue';
  import EditDuotoneIcon from '@/components/icons/IconEditDuotone.vue';
  import ModalWrapper from '@/components/ui/ModalWrapper.vue';
  import EditFamily  from '@/components/family/EditFamily.vue';

  const props = defineProps({
    id: { type: String, default: null },
  })

  const familyStore = useFamilyStore();
  const userStore = useUserStore();
  
  const loader =ref(false);
  const commonActions = ref([]);
  const actionName = ref('');
  const isModalAction = ref(false);
  const activeFamily = ref(null);

  const mainElem = ref(null);

  const onStartCommonAction = (action) => {
    const isAction = commonActions.value.filter(item => item.id === action);
    if (isAction.length) {
      actionName.value = isAction[0].emit;
      activeFamily.value = null;
      isModalAction.value = true;
    } else {
      console.log('Нет такой операции');
    }
  }

  onBeforeMount( async () => {
    loader.value = true;
    await familyStore.getOneFamily(props.id);
    let isAdmin = false;
    const prihodIDs = [];
    userStore.user.permition.forEach(permition => {
      if (permition.type == 0) isAdmin = true;
      if (permition.type == 1) prihodIDs.push(permition.source_id);
    });
    if (isAdmin) {
      commonActions.value = [
          { id: 0, name: 'Изменить', emit: 'editFamily' },
        ];
    } else {
      const isPrihodAdmin = prihodIDs.filter(item => item == familyStore.oneFamily.head?.prihod_id).length;
      if (isPrihodAdmin) {
        commonActions.value = [
          { id: 0, name: 'Изменить', emit: 'editFamily' },
        ];
      } else {
        commonActions.value = [];
      }
    }

    loader.value = false;
  });
</script>

<style lang="scss">
  .profile {
    padding: 10px;
  }

  .square2x {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    align-items: stretch;
  }

  .square2x .card-box {
    flex-basis: 48%;
    flex-grow: 3;
    // margin: 0;
    // padding: 5px;
    // border: none;
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

</style>