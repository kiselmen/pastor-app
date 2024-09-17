<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="props.isModalActive" class="modal__background" />
    </Transition>
    <Transition
      :name="props.isSticky?.value && props.isSticky?.position === 'top' ? 'move' : 'fade'"
    >
      <div v-if="props.isModalActive" class="modal" ref="modalEl" @click="onCloseModal">
        <div
          class="modal__content"
          :class="{ 'sticky-top' : props.isSticky?.value && props.isSticky?.position === 'top' }"
        >
        <!-- @click.stop -->
          <slot />
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
  import scrollbar from '@/utils/scrollbar'
  import { watch, onMounted, onUnmounted, ref } from 'vue';

  const emits = defineEmits(['closeModal']);

  const props = defineProps({
    isModalActive: {type: Boolean, default: false},
    isSticky: () => {
      return {
        value: false,
        position: null
      }
    }
  });

  const modalEl = ref(null);

  const closeByKey = (event) => {
    if (props.isModalActive && event.key === 'Escape') {
      emits('closeModal')
    }
  };

  const onCloseModal = (event) => {
    // console.log(event.target, modalEl.value);
    if (modalEl.value === event.target) {
      emits('closeModal')  
    } 
  };

  watch(() => props.isModalActive, () => {
    if (props.isModalActive) {
      scrollbar.removeScrollBar()
    } else {
      scrollbar.addScrollBar()
    }
  });

  onMounted(() => {
    document.addEventListener('keydown', closeByKey)
    if (props.isModalActive) {
      scrollbar.removeScrollBar()
    } else {
      scrollbar.addScrollBar()
    }
  });

  onUnmounted(() => {
    document.removeEventListener('keydown', closeByKey)
  });
</script>

<style scoped lang="scss">
.modal {
    z-index: 99;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow-y: auto;

    &__background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: var(--bs-gray-700);
        opacity: 0.6;
        z-index: 99;
    }

    &__content {
        position: absolute;
        z-index: 1;

        background: var(--bs-white);
        left: 50%;
        max-width: 952px;
        min-width: 454px;
        @media (max-width: 454px) {
          min-width: calc(100vw - 10px);
        }
        @media (max-width: 952px) {
          max-width: calc(100vw - 10px);
        }
        max-height: 99vh; /* Установить максимальную высоту для контента */
        overflow-y: auto; /* Если контента много, появится скролл */
        padding: 1.5rem;
        top: 50%;
        transform: translate(-50%, -50%);
        transition: opacity .3s ease, transform .3s;

        &.sticky-top {
            top: 0;
        }
    }

    &__close-btn {
        position: absolute;
        right: -12px;
        top: 0;
        transform: translate(100%, 0);
        // background-color: $gray3;
        padding: 8px;
        // border-radius: toRem(8px);
        height: 40px;
        width: 40px;
        cursor: pointer;
    }
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.move-enter-active,
.move-leave-active {
    transition: transform 0.3s;
}

.move-enter-from,
.move-leave-to {
    transform: translateY(-100%);
}

</style>
