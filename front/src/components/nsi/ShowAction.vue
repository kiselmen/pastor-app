<template>
  <div class="form" ref="formElem" >
    <div class="form-header">Подробности операции</div>
      <div v-if="type == 'persone'" class="container">
        <div v-if="isPresentPersons" class="line-container">
          <div class="persone-container">
              <PersoneMiddleItem
                v-for="peopleObj in peoples"
                :key = "peopleObj.id"
                :persone = "peopleObj.persone"
                :markFields = "peopleObj.fields"
                :targets = "nsiStore.targets"
                :services = "nsiStore.services"
              />
          </div>
        </div>  
      <div v-if="isPresentFamily" class="arrow-top"></div>
      <div v-if="isPresentFamily" class="line-container">
        <div class="persone-container">
            <FamilyMiddleItem
              v-for="familyObj in families" :key = familyObj.id
              :family = "familyData(familyObj.id)"
              :markFields = "familyObj"
            />
        </div>
      </div>  
    </div>
    <div v-if="type == 'family'" class="container">
      <div v-for = "row in rows" :key = "row" lass="container_rows">
        <div class="line-container">
          <div class="prihod-container" v-if="props.item.prihodaction.length">
            <PrihodMiniItem
              :prihod = "props.item.prihodaction[0].SourceItem"
            />
          </div>
          <div class="arrow-right" v-if="props.item.prihodaction.length">

          </div>
          <div class="prihod-container">
            <div class="card-box">
              <div class="card-name">
                {{ props.item.familyaction[0].FamilyName }}
              </div>
              <PersoneMiniItem
                  v-for="persone in props.item.familyaction[0].family.people"
                  :persone = "persone"
                  :key = "persone.id"
              />
            </div>
          </div>

          <div class="arrow-right" v-if="props.item.prihodaction.length">

          </div>
          <div class="prihod-container" v-if="props.item.prihodaction.length">
            <PrihodMiniItem
              :prihod = "props.item.prihodaction[0].TargetItem"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
  import { computed, onBeforeMount } from 'vue';
  import { useFamilyStore } from '@/stores/familyStore.js';
  import { useNsiStore } from '@/stores/nsiStore.js';

  import PrihodMiniItem from '@/components/prihod/PrihodMiniItem.vue';
  import PersoneMiniItem from '@/components/people/PersoneMiniItem.vue';
  import PersoneMiddleItem  from '@/components/people/PersoneMiddleItem.vue';
  import FamilyMiddleItem from '@/components/family/FamilyMiddleItem.vue';
  // import FamilyMiniItem from '@/components/family/FamilyMiniItem.vue';

  const props = defineProps({
    item: { type: Object, default: new Object() },
  });

  const emits = defineEmits(['toggleModal']);

  const familyStore = useFamilyStore();
  const nsiStore = useNsiStore();

  const type = computed(() => {
    return props.item.action_id == 9 || props.item.action_id == 14 || props.item.action_id == 17 ? 'family': 'persone';
  })

  const rows = computed(() => {
    if (type.value == 'persone') {
      const isfamilyOperations = props.item.familyaction.length ? 1: 0;
      const isprihodOperations = props.item.prihodaction.length ? 1: 0;
      return 1 + isfamilyOperations * 2 + isprihodOperations * 2;
    } else {
      return 1;
    }
  });

  const isPresentPersons = computed(() => {
    let peopleCount = props.item.peopleaction.length;
    peopleCount = peopleCount + props.item.targetaction.length;
    peopleCount = peopleCount + props.item.serviceaction.length;
    return peopleCount;
  })

  const isPresentFamily = computed(() => {
    let familyRecords = props.item.familyaction.length;
    familyRecords = familyRecords + props.item.peopleaction.filter(item => item.field === 'family_id').length
    return familyRecords;
  })

  const peoples = computed(() => {
    const allPeoples = [];

    const allPeopleRecords = props.item.peopleaction;
    allPeopleRecords.forEach(oneRecord => {
        const personeID = oneRecord.people_id;
        const isPresent = allPeoples.filter(item => item.id === personeID);
        if (isPresent.length) {
          const fieldData = {};
          fieldData.field = oneRecord.field;
          fieldData.source = oneRecord.source;
          fieldData.target = oneRecord.target;
          isPresent[0].fields.push(fieldData);
        } else {
          const newPersone = {
            id: personeID,
            persone: {...oneRecord.Persone},
            fields: [],
          };
          const fieldData = {};
          fieldData.field = oneRecord.field;
          fieldData.source = oneRecord.source;
          fieldData.target = oneRecord.target;
          newPersone.fields.push(fieldData);
          allPeoples.push(newPersone);
        }
    });

    const allTargetRecords = props.item.targetaction;
    allTargetRecords.forEach(oneRecord => {
        const personeID = oneRecord.people_id;
        const isPresent = allPeoples.filter(item => item.id === personeID);
        
        if (isPresent.length) {
          const fieldData = {};
          if (oneRecord.target_id) {
            fieldData.field = 'target_id';
          } else {
            fieldData.field = 'source_id';
          }
          fieldData.source = oneRecord.source?.name;
          fieldData.target = oneRecord.target?.name;
          isPresent[0].fields.push(fieldData);
        } else {
          const newPersone = {
            id: personeID,
            persone: {...oneRecord.Persone},
            fields: [],
          };
          const fieldData = {};
          if (oneRecord.target_id) {
            fieldData.field = 'target_id';
          } else {
            fieldData.field = 'source_id';
          }
          fieldData.source = oneRecord.source?.name;
          fieldData.target = oneRecord.target?.name;
          newPersone.fields.push(fieldData);
          allPeoples.push(newPersone);
        }
    });

    const allServiceRecords = props.item.serviceaction;
    allServiceRecords.forEach(oneRecord => {
        const personeID = oneRecord.people_id;
        const isPresent = allPeoples.filter(item => item.id === personeID);
        
        if (isPresent.length) {
          const fieldData = {};
          if (oneRecord.target_id) {
            fieldData.field = 'target_id';
          } else {
            fieldData.field = 'source_id';
          }
          fieldData.source = oneRecord.source?.name;
          fieldData.target = oneRecord.target?.name;
          isPresent[0].fields.push(fieldData);
        } else {
          const newPersone = {
            id: personeID,
            persone: {...oneRecord.Persone},
            fields: [],
          };
          const fieldData = {};
          if (oneRecord.target_id) {
            fieldData.field = 'target_id';
          } else {
            fieldData.field = 'source_id';
          }
          fieldData.source = oneRecord.source?.name;
          fieldData.target = oneRecord.target?.name;
          newPersone.fields.push(fieldData);
          allPeoples.push(newPersone);
        }
    });

    return allPeoples;
  });

  const families = computed(() => {
    const families = [];

    const allRecords = props.item.peopleaction;
    allRecords.forEach(oneRecord => {
      if (oneRecord.field == 'family_id') {
        const isPresent = families.filter(item => item.id === oneRecord.target);
        if (!isPresent.length) {
          families.push({
            id: oneRecord.target,
            field: oneRecord.field,
            source: oneRecord.source,
            target: oneRecord.target,
          });
        } 
      }  
    });

    const familyRecords = props.item.familyaction;
    familyRecords.forEach(oneRecord => {
      if (oneRecord.field == 'id') {
        families.push({
            id: oneRecord.target,
            field: oneRecord.field,
            source: oneRecord.source,
            target: oneRecord.target,
        });        
      }
    })
    return families;
  });

  const familyData = (id) => {
    return familyStore.families.filter(item => item.id == id)[0];
  };

  onBeforeMount(() => {
    // console.log('onMount ', props.item);
  });

</script>

<style lang="scss" scoped>
  .container {
    // width: 500px;
    // height: 500px;
    margin: auto 0;
    // background-color: aqua;
    display: flex;
    flex-direction: column;
    flex-wrap: nowrap;
    padding-top: 10px;
    &_rows {
      flex: 1;
      margin: 0 10px;
    }
  }

  .line-container {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    width: 100%;
  }

  .prihod-container {
    // background-color: antiquewhite;
    margin: auto;
    flex: 1;
    padding: 10px;
  }

  .persone-container {
    margin: auto;
    flex: 1;
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    gap: 10px;
    justify-content: center;
  }

</style>