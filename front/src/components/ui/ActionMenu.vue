<template>
    <div class="action-container" ref="el">
      <div class="action-icon">
        <slot name="icon"></slot>
      </div>

      <div class="action-menu--wrapper" v-if="actions" :class="[checkDirection ? 'down' : 'up']">
        <div class="action-menu">
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
  const el = ref(null);

  const emits = defineEmits(['startAction']);

  const onAction = (actionID) => {
    emits('startAction', actionID, props.boxID);
  }

  const checkDirection = computed (() => {
    // console.log('el ', el.value);
    // console.log('Parent ', props.parentElem);
    if (!props.parentElem || !el.value) {
      return true
    } else {
      const totalBottom = props.parentElem.getBoundingClientRect().bottom;
      const totalTop = props.parentElem.getBoundingClientRect().top;
      const input = el.value.querySelector(".action-icon").getBoundingClientRect().bottom
      let limited = props.actions.length;
      if (props.actions) {
        limited = props.actions.length;
      } else {
        limited = 0;
      }
      // console.log('Limit ', limited);
      if ((totalBottom - input) < limited * 32 && (input - totalTop - 40) < (limited + 1) * 32) {
        // console.log('Not enouth space');
        // console.log('top: ', totalTop, 'input: ', input, 'bottom: ', totalBottom);
        let limitBottom = Math.trunc((totalBottom - input) / 32);
        let limitTop = Math.trunc((input - totalTop - 40) / 32) + 1;
        if (limitBottom > limitTop) {
          limit.value = Math.min(limitBottom, limit.value);
          return true
        } else {
          limit.value = Math.min(limitTop, limit.value);
          return false
        }
      }
      return (totalBottom - input) > limited * 32 ? true : false;
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
            left: calc(100% - 50px);
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