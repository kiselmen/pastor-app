<template>
  <div v-click-outside="onCloseDropDown" :class="[selectorOpen ? 'select__open selector' : 'selector']" ref="el">
    <div class="selector-box selector-box__active">
      <div v-if="activeItem && isImgPresent" 
          class = "item-img item-img_selected"
          :style = "{ backgroundImage : 'url(' + getImgPath(activeItem.image_url) +')' }"
      ></div>
      <input 
        v-if="props.data"
        v-model = "selectorInput"
        :disabled="disabled"
        type="text"
        @click="onStartInput()"
        v-on:keyup="onKeyPress"
        v-on:input="onInput"
        tabindex="1"
      />
      <div v-if="(!selectorOpen && !disabled)"
        @click.stop="onStartInput()"
      >
        <ChrvronDownIcon/>
      </div>
      <div v-if="(selectorOpen && !disabled)"
        @click="onCloseDropDown"
        class="chevron_open"
      >
        <ChrvronDownIcon/>
      </div>
      <div v-if="(activeItem !== null || selectedItem !== null) && !disabled"
        @click="onClearChoise"
      >
        <TrashIcon/>
      </div>
    </div>
    <ul v-if="props.data&&selectorOpen&&!disabled"
        :class="{ 'scroll menu-drop menu-down' : selectorOpen&&checkDirection, 'scroll menu-drop menu-up' : selectorOpen&&!checkDirection }"
        :style="{ 'maxHeight': checkHeght}"
    >
      <li class="menu-item"
        v-if="selectorOpen"
        v-for="item in filteredItems"
        :key = "item.id"
        @click="onSelectItem(item)"
        :id = "item.id"
      >
          <div v-if="isImgPresent" 
              class = "item-img"
              :style = "{ backgroundImage : 'url(' + getImgPath(item.image_url) +')' }"
          ></div>
          <div>{{ item.FullName ? item.FullName: item.name }}</div>
      </li>
    </ul>
  </div>
</template>

<script setup>

  import getImgPath from '@/utils/imagePlugin.js';
  import ChrvronDownIcon from '@/components/icons/IconChevronDown.vue';
  import TrashIcon from '@/components/icons/IconTrash.vue';
  import { ref, computed, onBeforeMount, watch } from 'vue';

  const props = defineProps({
    text        : "",
    id          : "",
    data        : "",
    limit       : {type: Number, default : 10},
    parentElem  : null,
    message     : {type: String, default : ''},
    disabled    : {type: Boolean, default: false},
  });

  const emits = defineEmits(['selectItem']);

  const selectorOpen = ref(false);
  const selectorInput = ref('');
  const selectedItem = ref(null);
  const selectorActive = ref(false);
  const limit = ref(10);
  const prevValue = ref("");
  const el = ref(null);
  const isImgPresent = ref(false);

  const filteredItems = computed(() =>{
    let InputText = selectorInput.value;
    let NotLimited = props.data.filter(elem =>
    {
        if(InputText==='') return true;
        else return elem.name.toLowerCase().indexOf(InputText.toLowerCase()) > -1;
    });

    NotLimited.sort((a, b) => {
      let x = a.name.toLowerCase();
      let y = b.name.toLowerCase();
      return x < y ? -1 : x > y ? 1 : 0;
    });

    return NotLimited;

    // let Iteration = limit.value;
    // let Limited = [];
    // if (NotLimited.length < Iteration) Iteration = NotLimited.length;
    // for (let i = 0; i < Iteration; i++){
    //     Limited[i] = NotLimited[i];
    // }
    // return Limited;
  });

  const activeItem = computed(() => {
    if (!props.id) return null
    return props.data.filter(item => item.id == props.id)[0];
  })

  const checkDirection = computed(() => {
    // console.log(props.parentElem);
    if (!props.parentElem) {
      return true
    } 

    const totalBottom = props.parentElem.getBoundingClientRect().bottom;
    const totalTop = props.parentElem.getBoundingClientRect().top;
    const input = el.value.querySelector(".selector-box").getBoundingClientRect().bottom
    let limited = 0;
    if (props.data) {
      limited = Math.min(limit.value, props.data.length)
    } else {
      limited = 0;
    }
    // console.log('Limit ', limited);
    if ((totalBottom - input) < limited * 30 && (input - totalTop - 40) < (limited + 1) * 30) {
      // console.log('Not enouth space');
      // console.log('top: ', totalTop, 'input: ', input, 'bottom: ', totalBottom);
      let limitBottom = Math.trunc((totalBottom - input) / 30);
      let limitTop = Math.trunc((input - totalTop - 40) / 30) + 1;
      if (limitBottom > limitTop) {
        limit.value = Math.min(limitBottom, limit.value);
        return true
      } else {
        limit.value = Math.min(limitTop, limit.value);
        return false
      }
    }
    return (totalBottom - input) > limited * 30 ? true : false;
  });

  const checkHeght = computed(() => {
    if (!props.parentElem) {
      return '120px';
    } 
    const totalBottom = props.parentElem.getBoundingClientRect().bottom;
    const totalTop = props.parentElem.getBoundingClientRect().top;
    const input = el.value.querySelector(".selector-box").getBoundingClientRect().bottom
    const limitItem = Math.min(filteredItems.value.length, props.limit);
    const limitHeight = 30 * limitItem + 5;
    // console.log('totalBottom ', totalBottom, ' totalTop ', totalTop, ' input ', input);
    
    if (checkDirection.value) {
      // console.log('Down');
      const height = totalBottom - input;
      const resultHeight = Math.min(limitHeight, height);
      return resultHeight + 'px';
    } else {
      // console.log('Up');
      const height = input - totalTop - 50;
      const resultHeight = Math.min(limitHeight, height);
      return resultHeight + 'px';
    }
  })

  const onSelectItem = (elem) => {
    selectedItem.value = elem.id;
    selectorInput.value = elem.FullName ? elem.FullName: elem.name;
    selectorOpen.value = false;
    selectorActive.value = false;
    emits('selectItem', selectedItem.value);
  };

  const onClearChoise = () => {
    selectedItem.value = null;
    selectorInput.value = props.text;
    selectorOpen.value = false;
    selectorActive.value = false;
    emits('selectItem', null);
  }

  const onStartInput = () => {
    selectorOpen.value = true;
    prevValue.value = selectorInput.value;
    selectorInput.value  = "";
    selectorActive.value = true;
  };

  const onInput = () => {
    if (!selectorActive.value) {
      selectorOpen.value = true;
      prevValue.value = selectorInput.value;
      selectorActive.value = true;
    }
  };

  const onCloseDropDown = () => {
    if (selectorActive.value){
      selectorOpen.value = false;
      selectorActive.value = false;
      selectorInput.value = prevValue.value;
    }
  };

  const onKeyPress = (event) => {
    // console.log('InputSelector onKewyPress ', filteredItems.value);
    // event.stopPropogation();
    if (event.keyCode === 13) {
      // console.log(' filteredItems: ', filteredItems);
      selectedItem.value = filteredItems.value[0].id;
      selectorInput.value = filteredItems.value[0].FullName ? filteredItems.value[0].FullName: filteredItems.value[0].name;
      selectorOpen.value = false;
      selectorActive.value = false;
      emits('selectItem', selectedItem.value);
    }
  };

  onBeforeMount (() => {
    
    limit.value = props.limit;
    if (props.id !== null) {
      // console.log('mount ', props.id, '  ', props.data?.filter(item => item.id == props.id)[0].name);
      const isPresent = selectorInput.value = props.data.filter(item => item.id == props.id);
      if (isPresent.length) {
        selectorInput.value = isPresent[0].FullName ? 
          props.data.filter(item => item.id == props.id)[0].FullName: 
          props.data.filter(item => item.id == props.id)[0].name;
      } else {
        selectorInput.value = props.text;
      }
    } else {
      selectorInput.value = props.text;
    }
    const dataObj = props.data[0];
    if (dataObj) {
      if (Object.keys(dataObj).includes('image_url')) isImgPresent.value = true;
    }
  })

</script>

<style lang="scss" scoped>
  .chevron_open {
    transform: rotate(0.5turn);
  }
  .selector {
    position: relative;
    &-box {
      display: flex;
      justify-content: space-between;
      position: relative;
      align-items: center;
      width: 100%;
      border: 1px solid var(--bs-gray-500);
      &:disabled{
        cursor: default;
      }
      input {
        width: 100%;
        color: var(--bs-gray-600);
        line-height: 1.5;
        appearance: none;
        background-color: var(--bs-white);
        background-clip: padding-box;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        width: 100%;
        font-size: 16px;
        padding: 10px 10px;
        text-align: left;
        outline: none;
      }
      &__active{
        cursor: pointer;
      }
    }
    &-text {
      min-width: 184px;
    }
  }

  .menu {
    &-drop {
      position: absolute;
      z-index: 20;
      left: 0;
      background: var(--bs-white);
      width: 100%;
      border: 1px solid var(--bs-gray-500);
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    &-up {
      bottom: 46px;
      border-bottom: none;
    }
    &-down{
      top: 46px;
      border-top: none;
    }

    &-item {
      display: flex;
      flex-direction: row;
      align-items: center;
      // gap: 10px;
      width: 100%;
      padding: .2rem 1.6rem;
      clear: both;
      color: var(--bs-black);
      text-align: inherit;
      background-color: transparent;
      border: 0;
      &:hover{
        color:var(--bs-primary);
      }
    }
  }

  .item {
    &-img {
      width: 30px;
      min-width: 30px;
      height: 30px;
      margin-right: 10px;
      border-radius: 50%;
      background-size: cover;
      background-repeat: no-repeat;
      z-index: 10;
      &_selected {
        margin-right: 0;
        margin-left: 10px;
      }
    }
  }

  ul {
    position: relative;
    list-style: none;
    padding: 0;
  }
    li {
      cursor: pointer;
    }

</style>
