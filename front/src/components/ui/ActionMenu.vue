<template>
    <div class="action-container" ref="actionElem">
      <div class="action-icon" ref="iconElem">
        <slot name="icon"></slot>
      </div>

      <div class="action-menu--wrapper" v-if="actions" :class="[checkVDirection]" :style = "{left : checkHDirection}">
        <div class="action-menu" ref= "menuElem">
          <span class="action-item" v-for="action in actions" @click="onAction(action.id)">
            {{ action.name }}
          </span>
        </div>
      </div>
    </div>
</template>

<script setup>
  import { ref, computed } from 'vue';

  const props = defineProps({
    actions: { type: Array, default: new Array() },
    boxID: { type: Number, default: null },
    parentElem: null,
  });

  const limit = ref(null);
  const actionElem = ref(null);
  const iconElem = ref(null);
  const menuElem = ref(null);

  const emits = defineEmits(['startAction']);

  const onAction = (actionID) => {
    emits('startAction', actionID, props.boxID);
  }

  const checkHDirection = computed(() => {
    if (iconElem.value && menuElem.value) {
      const iconLeft = iconElem.value.getBoundingClientRect().left
      const menuSize = menuElem.value.getBoundingClientRect().right - menuElem.value.getBoundingClientRect().left;
      // console.log('iconLeft ', iconLeft, ' menuSize ', menuSize);
      let direction = 'right';
      if (iconLeft < menuSize) { 
        // direction = 'right';
        direction = iconLeft;
      } else {
        // direction = 'left';
        direction = 'calc(100% - ' + menuSize + 'px)';
      }
      return direction
    }  
  });


  const checkVDirection = computed (() => {
    if (!props.parentElem || !actionElem.value) {
      return 'down';
    } else {
      const parentBottom = props.parentElem.getBoundingClientRect().bottom;
      const parentTop = props.parentElem.getBoundingClientRect().top;
      const parentLeft = props.parentElem.getBoundingClientRect().left;
      const parentRight = props.parentElem.getBoundingClientRect().right;
      const iconBottom = iconElem.value.getBoundingClientRect().bottom
      // console.log('parentLeft ', parentLeft, ' parentRight ', parentRight);
      
      let limited = props.actions.length;
      if (props.actions) {
        limited = props.actions.length;
      } else {
        limited = 0;
      }
      // console.log('Limit ', limited);
      if ((parentBottom - iconBottom) < limited * 32 && (iconBottom - parentTop - 40) < (limited + 1) * 32) {
        // console.log('Not enouth space');
        // console.log('top: ', parentTop, 'iconBottom: ', iconBottom, 'bottom: ', parentBottom);
        let limitBottom = Math.trunc((parentBottom - iconBottom) / 32);
        let limitTop = Math.trunc((iconBottom - parentTop - 40) / 32) + 1;
        if (limitBottom > limitTop) {
          limit.value = Math.min(limitBottom, limit.value);
          return 'down';
        } else {
          limit.value = Math.min(limitTop, limit.value);
          return 'up';
        }
      }
      return (parentBottom - iconBottom) > limited * 32 ? 'down': 'up';
    }
  })


</script>

<style lang="scss" scoped>
  .action {
    &-container {
        align-items: center;
        display: flex;
        justify-content: center;
        padding: 0 5px;
        width: 20px;
        height: 15px;
        position: relative;
    }
    &-menu {
        background: var(--bs-gray-200);
        border-radius: 8px;
        box-shadow: 0 5px 20px 0 rgba(111,117,135,.15);
        cursor: pointer;
        display: flex;
        flex-direction: column;
        min-width: 140px;
        padding: 10px 8px;
        white-space: nowrap;
        color: var(--bs-black);
        svg {
            width: 17px;
            height: 17px;
        }
        &:hover {
            background-color: var(--bs-gray-200);
        }
        &--wrapper {
            // left: calc(100% - 140px);
            opacity: 0;
            padding-bottom: 8px;
            position: absolute;
            transform: translate(0%, 100%);
            transition: all 0.2s ease-in-out;
            visibility: hidden;
            z-index: 3;
            width: 100%;
        }
    }
    &-item:hover {
        color: var(--bs-primary);
    }
    &-column {
        align-items: center;
        display: flex;
        gap: 10px;
        .btn{
            height: auto;
        }
    }
  }
  .down {
    bottom: 0px;
  }
  .up {
    bottom: 100px;
  }
  .action-container:hover .action-menu--wrapper {
      opacity: 1;
      visibility: visible;
  }

</style>