<template>
  <div class="mini-container">
    <div class="fields-box">
      <div v-for="fieldItem in markFields">
        <div v-if="fieldItem.field != 'family_id' && fieldItem.field != 'prihod_id' && fieldItem.field != 'source_id'" class="card-box">
          <div v-if="fieldItem.field != 'image_url'">
            {{ fieldItem.field }}: {{ fieldItem.target }}
          </div>
          <div v-if="fieldItem.field == 'image_url'">
            <div class="card-row">
              <div class="card-column">
                <div class="card-img" :style = "{ backgroundImage : 'url(' + getImgPath(fieldItem.target) +')' }"></div>
              </div>  
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="isPresentPersoneLeftFields" class="arrow-right"></div>
    <div class = "card-box" :class="{ marked: isPresentField('id')}" ref="cardElem">
      <div class="card-name">
          <span :class="{ marked: isPresentField('name')}">{{ persone.name + ' '}} </span>
          <span :class="{ marked: isPresentField('first_name')}">{{ persone.first_name + ' '}} </span>
          <span :class="{ marked: isPresentField('patronymic')}">{{ persone.patronymic }} </span>
      </div>

      <div class="card-box">
        <div class="card-row">
          <div class="card-column">
            <div class="card-img" :class="{ marked: isPresentField('image_url')}" :style = "{ backgroundImage : 'url(' + getImgPath(persone.image_url) +')' }"></div>
          </div>  
        </div>
      </div>  
      <div class="card-box">
        <div class="card-row">
          <DateItem :class="{ marked: isPresentField('birthday_date')}">
            <template #icon>
              <BirthdayIcon />
            </template>
            <template #heading>
              {{ (new Date(persone.birthday_date)).toDateString() }}
            </template>
          </DateItem>
          <DateItem :class="{ marked: isPresentField('baptism_date')}">
            <template #icon>
              <CrossIcon />
            </template>
            <template #heading v-if="persone.baptism_date">
              {{ (new Date(persone.baptism_date)).toDateString() }}
            </template>
          </DateItem>
        </div>
        <div class="card-row">
          <DateItem :class="{ marked: isPresentField('death_date')}">
            <template #icon>
              <RIPIcon />
            </template>
            <template #heading>
              {{ persone.death_date ? (new Date(persone.death_date)).toDateString(): '-' }}
            </template>
          </DateItem>
        </div>
      </div>
      <div class="card-box">
        <div class="card-row">
          <div class="info" :class="{ marked: isPresentField('live_index')}">
            {{ persone.live_index }}  &nbsp
          </div>
          <div class="info" :class="{ marked: isPresentField('live_town')}">
            {{ persone.live_town }}  &nbsp
          </div>
          <div class="info" :class="{ marked: isPresentField('live_street')}">
            {{ persone.live_street }}  &nbsp
          </div>
          <div class="info" :class="{ marked: isPresentField('live_house')}">
            {{ persone.live_house }}  &nbsp
          </div>
          <div class="info" :class="{ marked: isPresentField('live_flat')}">
            {{ persone.live_flat }} 
          </div>
        </div>
        <div class="card-row">
          <DateItem :class="{ marked: isPresentField('mobile_phone')}">
            <template #icon>
              <PhoneIcon />
            </template>
            <template #heading>
              {{ persone.mobile_phone }}
            </template>
          </DateItem>
        </div>
        <div class="card-row">
          <DateItem :class="{ marked: isPresentField('home_phone')}">
            <template #icon>
              <PhoneIcon />
            </template>
            <template #heading>
              {{ persone.home_phone }}
            </template>
          </DateItem>
        </div>
      </div>
      <div class="card-box" v-if="props.persone?.ptarget?.length">
        <div v-for="target in props.persone?.ptarget" :key = "target.id">
          <div class="card-row" :class="{ marked: isPresentTargetField(target.target_id)}">
            <div class="card-target">{{ TargetName(target.target_id) }}</div>
          </div>
        </div>
      </div>
      <div class="card-box" v-if="props.persone?.pservice?.length">
        <div  v-for="service in props.persone?.pservice" :key = "service.id">
          <div class="card-row" :class="{ marked: isPresentServiceField(service.service_id)}">
            <div class="card-service">{{ ServiceName(service.service_id) }}</div>
          </div>
        </div>
      </div> 

    </div>
    <div v-if="isPresentPersoneRightFields" class="arrow-right"></div>
    <div class="fields-box">
      <div v-for="fieldItem in markFields">
        <div v-if="fieldItem.field != 'family_id' && fieldItem.field != 'prihod_id' && fieldItem.field != 'target_id' && !fieldItem.target" class="card-box">
          <div v-if="fieldItem.field != 'image_url'" class="card-row">
            <div class="card-service">
              {{ fieldItem.field }}: {{ fieldItem.source }}
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

</template>

<script setup>
  import getImgPath from '@/utils/imagePlugin.js';
  import { ref, computed } from 'vue';

  import CrossIcon from '@/components/icons/IconCross.vue';
  import BirthdayIcon from '@/components/icons/IconBirthday.vue';
  import RIPIcon from '@/components/icons/IconRIP.vue';
  import AdressIcon from '@/components/icons/IconAdress.vue';
  import PhoneIcon from '@/components/icons/IconPhone.vue';
  import DateItem from '@/components/people/DateItem.vue';

  const props = defineProps({
    persone: { type: Object, default: new Object() },
    markFields: { type: Object, default: new Object() },
    targets: { type: Object, default: new Object() },
    services: { type: Object, default: new Object() },
  });

  const cardElem = ref(null);

  const persone = computed(() => {
    const rebuldedPersone = {};
    for (let key in props.persone) {
      const isPresent = props.markFields.filter(markItem => markItem.field == key);
      if (isPresent.length) {
        rebuldedPersone[key] = isPresent[0].source;
      } else {
        rebuldedPersone[key] = props.persone[key];
      }
    }
    return rebuldedPersone;
  });

  const isPresentPersoneLeftFields = computed(() => {
    return props.markFields.filter(item => item.field != 'family_id' && item.field != 'prihod_id' && item.field != 'source_id').length;
  });

  const isPresentPersoneRightFields = computed(() => {
    return props.markFields.filter(item => item.field != 'family_id' && item.field != 'prihod_id' && item.field != 'target_id' && !item.target).length;
  });
  
  const isPresentField = (field) => {
    const isPresent = props.markFields.filter(item => item.field == field);
    return isPresent.length;
  };

  const isPresentTargetField = (targetID) => {
    const name = TargetName(targetID);
    const isPresent = props.markFields.filter(item => item.target === name); 
    return isPresent.length;
  };

  const isPresentServiceField = (serviceID) => {
    const name = ServiceName(serviceID);
    const isPresent = props.markFields.filter(item => item.target === name); 
    return isPresent.length;
  }

  const TargetName = (id) => {
    console.log('id ', id);
    const res = props.targets.filter(item => item.id === id)[0].name;
    return res;
  };

  const ServiceName = (id) => {
    console.log('id ', id);
    const res = props.services.filter(item => item.id === id)[0].name;
    return res;
  };

</script>

<style lang="scss" scoped>
  .card {
    &-img {
      min-width: 200px;
      width: auto;
    }
    &-row {
      padding: 3px;
    }
  }
  .mini-container {
    display: flex;
    flex-direction: row;
  }

  .action-container {
    align-items: baseline;
  }
  
  a {
    text-decoration: none;
    color: var(--bs-white);
  }

  .marked {
    border: 1px solid var(--bs-danger);
    padding: 5px; 
  }

  .info {
    padding: 5px; 
  }

  .fields-box {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 20px;    
  }
</style>