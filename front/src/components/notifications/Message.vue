<template>
  <div class='v-notification'>
    <div class="v-notification__list">
      <transition-group
        name="v-transition-animate"
        
      >
        <div
            class="v-notification__content"
            v-for=" (message, index) in msgStore.messages"
            :key=index
            :class="message.icon"
        >
          <div class="content__text">
            <div v-if="message.icon == 'error'" class="v-notification-icon">
               <ErrorIcon/> 
            </div>
            <div v-if="message.icon == 'warning'" class="v-notification-icon">
              <WarningIcon />
            </div>
            <div v-if="message.icon == 'done'" class="v-notification-icon">
              <OkIcon /> 
            </div>
            <div class="content-message">{{message.name}}</div>
          </div>
          <div class="content_buttons" v-if="index === msgStore.messages.length - 1">
            <div class="close-popup btn-close-popup" @click="closeNotification()"></div>
          </div>
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script setup>

  import { useMsgStore } from '@/stores/msgStore';
  import { onMounted, watch } from 'vue';
  import ErrorIcon from '@/components/icons/IconError.vue';
  import WarningIcon from '@/components/icons/IconWarning.vue';
  import OkIcon from '@/components/icons/IconOk.vue';

  const props = defineProps({
      timeout:      { type: Number,  default: 3000}
  });

  const msgStore = useMsgStore();

  const hideNotification = () => {
    if (msgStore.messages.length) {
      setTimeout(function () {
        msgStore.deleteMessage();
      }, props.timeout)
    }
  };

  const closeNotification = () => {
      msgStore.deleteMessage();
  };

  watch( () => msgStore.messages, () => {
    hideNotification();
  }, { deep: true });


  onMounted(() => {
    hideNotification();
  })

</script>

<style lang="scss" scoped>
  .v-notification {
    position: fixed;
    top: 80px;
    right: 16px;
    width: 30%;
    z-index: 100;
    @media (max-width: 768px) {
      max-width: 60%;
      width: 100%;
    }
    &__list {
      display: flex;
      flex-direction: column-reverse;
      gap: 10px;
    }
    &__content {
      padding: 16px;
      border-radius: 4px;
      position: relative;
      // color: #757575;
      display: flex;
      justify-content: space-between;
      align-items: center;
      height: 75px;
      // background: green;
      @media (max-width: 768px) {
        padding: 5px 5px;
        height: 40px;
    }
      &.error {
        background: #EED3D3;
        border:2px solid #E2B6B6;
      }
      &.warning {
        background: #F3EAD1;
        border:2px solid #EBDCB3;
      }
      &.done {
        background: #D3DFCE;
        border: 2px solid #B6CAAE;
      }
    }
    .content {
      &__text {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
      }
    }
  }
  .v-transition-animate {
    &-enter {
      transform: translateX(120px);
      opacity: 0;
    }
    &-enter-active {
      transition: all .6s ease;
    }
    &-enter-to {
      opacity: 1;
    }
    &-leave {
      height: 50px;
      opacity: 1;
    }
    &-leave-active {
      transition: transform .6s ease, opacity .6s, height .6s .2s;
    }
    &-leave-to {
      height: 0;
      transform: translateX(120px);
      opacity: 0;
    }
    &-move {
      transition: transform .6s ease;
    }
  }

  /* Кнопка закрытия */
  .btn-close-popup{
    position: absolute;
    top: 10px;
    right: 10px;
    width: 30px;
    height: 30px;
    cursor: pointer;
    z-index: 1000;
    @media (max-width: 450px) {
      top: -3px;
      right: 15px;
      width: 20px;
      height: 20px;
    }
  }

  .btn-close-popup:before,
  .btn-close-popup:after {
    content: "";
    position: absolute;
      top: 14px;
      left: 9px;
      width: 20px;
      height: 2px;
    background: #32323B;
  }

  .btn-close-popup:before {
    transform: rotate(45deg);
  }

  .btn-close-popup:after {
    transform: rotate(-45deg);
  }

</style>
