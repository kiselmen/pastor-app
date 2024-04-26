<template>
  <div class = "card">
    <div class="card-title">
      <span>
        {{ props.persone.second_name }} {{ props.persone.first_name }} {{ props.persone.patronymic }}
      </span>
      <IconEditDuotone class="change_button" @click="onEditClick"/>
    </div>
    <!-- <div class="card-row">
      {{ props.persone.first_name }} {{ props.persone.patronymic }}
    </div> -->
    <div class="card-img" :style = "{ backgroundImage : 'url(' + getImgPath(props.persone.image_url) +')' }"></div>
    <div class="card-row">
      ДР: {{ props.persone.birthday_date }} ДК: {{ props.persone.baptism_date }} ДС: {{ props.persone.death_date }}
    </div>
    <div class="card-row">
      Адрес: {{ props.persone.live_addres }}
    </div>
    <div class="card-row">
      ТМ: {{ props.persone.mobile_phone }} ТД: {{ props.persone.home_phone }}
    </div>
    <div class="card-row">
      EMail: {{ props.persone.email }}
    </div>
  </div>
</template>

<script setup>
  import getImgPath from '@/utils/imagePlugin.js';
  import { onBeforeMount } from 'vue';
  import IconEditDuotone from '@/components/icons/IconEditDuotone.vue';

  const props = defineProps({
    persone: { type: Object, default: new Object() },
  })

  const emits = defineEmits(['editPeron'])

  // onBeforeMount(() => {
  //   console.log(props.persone);
  // })

  const onEditClick = () => {
    console.log('Edit persone ', props.persone.id);
    emits('editPeron', props.persone.id)
  }
</script>

<style lang="scss" scoped>
  .change_button {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 32px;
    height: 32px;
    background: var(--bs-blue);
    display: flex;
    align-items: center;
    border-radius: 50%;
    justify-content: center;
    cursor: pointer;
    border: 6px solid var(--bs-white);    
    &:hover{
      background: var(--bs-gray-200);
    }
  }
  .card {
    margin-top: 1rem;
    border-radius: 0;
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 200px;
    min-height: 400px;
    // height: var(--bs-card-height);
    color: var(--bs-gray-600);
    word-wrap: break-word;
    background-color: var(--bs-white);
    background-clip: border-box;
    border: 1px solid var(--bs-gray-400);
    &-title {
      padding: 10px;
      background-color: var(--bs-primary);
      color: var(--bs-white);
      font-size: 14px;
      font-weight: bold;
    }
    &-row {
      padding: 10px;
      // background-color: var(--bs-primary);
      // color: var(--bs-white);
      font-size: 14px;
      font-weight: bold;
    }
    &-img {
      width: 100%;
      height: 200px;
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center center;
    }    
  }
</style>