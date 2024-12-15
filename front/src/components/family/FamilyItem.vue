<template>
  <div class = "card" ref="cardElem">
    <div class="card-title">
      <span>
        <RouterLink :to="'/family/' + props.family?.id">
          {{ props.family.name }}
        </RouterLink>  
      </span>
      <!-- <EditDuotoneIcon class="change_button" @click="onEditClick"/> -->
      <div class="change_button">
        <ActionMenu 
          :actions = "actions"
          :parentElem = "cardElem"
          @startAction="onStartAction"
        >
          <template #icon >
            <EditDuotoneIcon/>
          </template>
        </ActionMenu>
      </div>
    </div>
    <div class="card-box">
      <div class="card-row">
        <div class="card-img" :style = "{ backgroundImage : 'url(' + getImgPath(props.family.head?.image_url) +')' }"></div>
        <div class="card-column">
          <PrihodMiniItem
            v-if="props.family.head"
            :prihod = "props.family.head.prihod"
          />
          <div class="card-target">Размер: {{ props.family.FamilyComposition }}</div>
        </div>
      </div>
    </div>
    <div class="card-box">
      <div class="card-row">
        <div class="card-target">Состав семьи</div>  
      </div>  
      <div class="card-row flex-wrap">
        <PersoneMiniItem
            v-for="persone in props.family.people"
            :key = "persone.id"
            :persone = "persone"
        />
      </div>
    </div>
    <div class="card-row">
      <DateItem>
        <template #icon>
          <!-- <BirthdayIcon /> -->
        </template>
        <template #heading>
          {{ props.family.discription }}
        </template>
      </DateItem>
    </div>
  </div>
</template>

<script setup>

  import getImgPath from '@/utils/imagePlugin.js';
  import { onBeforeMount, ref, } from 'vue';

  import EditDuotoneIcon from '@/components/icons/IconEditDuotone.vue';
  import DateItem from '@/components/people/DateItem.vue';
  import PrihodMiniItem from '@/components/prihod/PrihodMiniItem.vue';
  import PersoneMiniItem from '@/components/people/PersoneMiniItem.vue';
  import ActionMenu from '@/components/ui/ActionMenu.vue';

  const props = defineProps({
    family: { type: Object, default: new Object() },
  })

  const emits = defineEmits(['editFamily', 'moveFamily']);

  const actions = ref([]);
  const cardElem = ref(null);

  const onStartAction = (action) => {
    const isAction = actions.value.filter(item => item.id === action);
    if (isAction.length) {
      const actionEmit = isAction[0].emit;
      console.log('actionEmit ', actionEmit);
      emits(actionEmit, props.family.id);
    } else {
      console.log('Нет такой операции');
    }
  };

  // const onEditClick = () => {
  //   emits('editFamily', props.family.id)
  // };

  onBeforeMount(() => {
    actions.value = [
      { id: 0, name: 'Изменить', emit: 'editFamily' },
      { id: 1, name: 'Оформить переезд', emit: 'moveFamily' },
    ];
  });

</script>

<style lang="scss" scoped>
  .card-column {
    justify-content: space-between;  
  }

  .flex-wrap {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }
  a {
    text-decoration: none;
    color: var(--bs-white);
  }
</style>