<template>
  <div v-if="curPersone.user_id" class="form" ref="formElem">
    <div class="form-header"> {{ curPersone.name }} {{ curPersone.first_name }} {{ curPersone.patronymic }}</div>
    <div class="card-name">Права доступа</div>
    <div v-if="loader" class="form-container section-container form-middle">
      <div class="form-text">Loading...</div>
    </div>
    <div v-if="!loader&&!confirmWindow" class="form-container section-container form-middle">
      <div class = "table2x">
        <div class="form-group">
          <label class="input-label">Список служений</label>
          <InputSelector
            :text = "''"
            :id   ="null"
            :data ="types"
            :parentElem = "formElem"
            @selectItem="onTypeSelect"
          />
        </div>
        <div class="form-group" v-if="typeID == 1">
          <label class="input-label">Список участков</label>
          <InputSelector
            :text = "''"
            :id   ="null"
            :data ="prihodStore.prihods"
            :parentElem = "formElem"
            @selectItem="onSourceSelect"
          />
        </div>
        <div class="form-group" v-if="typeID == 2">
          <label class="input-label">Список служений</label>
          <InputSelector
            :text = "''"
            :id   ="null"
            :data ="nsiStore.services"
            :parentElem = "formElem"
            @selectItem="onSourceSelect"
          />
        </div>
        <div class="form-group">
          <button @click.prevent="onPermitionAdd" class="btn btn-blue">Добавить</button>
        </div>  
      </div>
      <div class="table2x">
        <div v-if ="!loader" class="group-items">
          <div class="group-item" v-if = "permitions.length" v-for ="permition in permitions" >
            <div class="group-name">{{ permition.name + ' ' + permition.source_name }}</div>
              <div class="group-delete" @click="onDeletePermition(permition.id, permition.source_id)">
                <DismissIcon/>
              </div>
            </div> 
        </div>
      </div>  
    </div>
    <div v-if="!loader&&confirmWindow" class="form-container section-container form-middle">
      <div class = "table1x">
        <div class="form-text">Сохранить изменения?</div>
      </div>
    </div>
    <div v-if="!confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onSavePermitions" class="btn btn-blue" :disabled="loader">{{'Сохранить'}}</button>
      <button @click.prevent="emits('toggleModal')" class="btn btn-gray" :disabled="loader">Отмена</button>
    </div>
    <div v-if="confirmWindow" class="form-buttons form-bottom">
      <button @click.prevent="onConfirmAction" class="btn btn-blue" :disabled="loader">{{ loader ? 'Обработка...': 'Да'}}</button>
      <button @click.prevent="onCancelAction" class="btn btn-gray">Отмена</button>
    </div>
  </div>  
</template>

<script setup>
  import { reactive, ref, computed, onBeforeMount } from 'vue';
  import { useUserStore } from '@/stores/userStore';
  import { useNsiStore } from '@/stores/nsiStore';
  import { useMsgStore } from '@/stores/msgStore';
  import { usePrihodStore } from '@/stores/prihodStore';
  import { usePeopleStore } from '@/stores/peopleStore';
  import InputSelector from '@/components/ui/InputSelector.vue';
  import DismissIcon from '@/components/icons/IconDismiss.vue';

  const props = defineProps({
    id: null,
  });

  const emits = defineEmits(['toggleModal']);

  const userStore = useUserStore();
  const msgStore = useMsgStore();
  const nsiStore = useNsiStore();
  const prihodStore = usePrihodStore();
  const peopleStore = usePeopleStore();

  const form = reactive({
    persone_id: props.id ? props.id: peopleStore.onePersone.user_id,
    name: '',
    password: '',
    password_confirmation: '',
  });

  const loader = ref(false);
  const confirmWindow = ref(false);
  const formElem = ref(null);
  const permitions = ref([]);
  const typeID = ref(null);
  const sourceID = ref(null);
  const types = [
    {id: 0, name: 'Пастор'},
    {id: 1, name: 'Дьякон'},
    {id: 2, name: 'Руководитель служения'},
  ];

  const curPersone = computed(() => {
    if (props.id) {
      const filtered = peopleStore.peoples.filter(item => item.user_id == props.id);
      return filtered[0];
    } else {
      return peopleStore.onePersone;
    }
  });

  const onTypeSelect = (id) => {
    typeID.value = id;
  };

  const onSourceSelect = (id) => {
    sourceID.value = id;
  };

  const onPermitionAdd = () => {
    const newPermition = JSON.parse(JSON.stringify(types.filter(item => item.id === typeID.value)))[0];
    newPermition.user_id = curPersone.value.user_id;
    newPermition.type = typeID.value;
    if (typeID.value === 0 ) {
      newPermition.source_id = null;
      newPermition.source_name = '';
    } else if (typeID.value === 1 ) {
      const curPrihod = prihodStore.prihods.filter(item => item.id === sourceID.value)[0];
      newPermition.source_id = curPrihod.id;
      newPermition.source_name = '(' + curPrihod.name + ' ' + curPrihod.number +')';
    } else if (typeID.value === 2 ) {
      const curService = nsiStore.services.filter(item => item.id === sourceID.value)[0];
      newPermition.source_id = curService.id;
      newPermition.source_name = '(' + curService.name +')';
    }
    const isAlreadeyPresent = permitions.value.filter(item => item.id === newPermition.id && item.source_id === newPermition.source_id);
    if (!isAlreadeyPresent.length) permitions.value.push(newPermition);
  };

  const onDeletePermition = (id, source_id) => {
    permitions.value = permitions.value.filter(item => {
      return !(item.id == id && item.source_id == source_id)
    })
  };
  
  const onConfirmAction = async () => {
    loader.value = true;
    await userStore.updatePersonePermitions(curPersone.value.user_id, permitions.value);
    loader.value = false;
    confirmWindow.value = false;
    emits('toggleModal');
  }

  const onSavePermitions = () => {
    confirmWindow.value = true;
  };

  const onCancelAction = () => {
    confirmWindow.value = false;
  };
  
  onBeforeMount(async () => {
    loader.value = true;
    form.name = curPersone.value.name + ' ' + curPersone.value.first_name + ' ' + curPersone.value.patronymic;    
    const isPermition = userStore.user.permition;
    userStore.clearErrorsState();
      if (isPermition.length) {
        await nsiStore.getServices();
        await prihodStore.getPrihods();
        console.log('curPersone.value.user_id ', curPersone.value.user_id);
        
        await peopleStore.getPersonePermitions(curPersone.value.user_id);
        permitions.value = JSON.parse(JSON.stringify(peopleStore.personePermitions));
        permitions.value.forEach(item => {
          const type = item.type;
          if (type === 0 ) {
            item.source_name = '';
            item.name = 'Пастор';
          } else if (type === 1 ) {
            const curPrihod = prihodStore.prihods.filter(prihod => prihod.id == item.source_id)[0];
            item.source_name = '(' + curPrihod.name + ' ' + curPrihod.number +')';
            item.name = 'Дьякон';
          } else if (type === 2 ) {
            const curService = nsiStore.services.filter(service => service.id == item.source_id)[0];
            item.source_name = '(' + curService.name +')';
            item.name = 'Руководитель служения';
          }
        });
      } else {
        setTimeout(() => {
          msgStore.addMessage({name: 'Не достаточно прав доступа', icon: 'warning'});
          emits('toggleModal');     
        }, 0);
      }
    loader.value = false;
  });

</script>