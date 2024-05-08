<template>
  <div class="checkbox-container">
    <div class="checkbox" :class="[{ active: props.modelValue, disabled: props.disabled },props.theme]" @click="onToggleCheckbox">
      <input type="checkbox" :disabled="props.disabled" class="hidden-input">
      <IconCheck class="checkbox-icon" :class="{ active: props.modelValue, disabled: props.disabled }" />
    </div>
    <span v-if="props.title != ''">{{ props.title }}</span>
  </div>
</template>

<script setup>
  import IconCheck from '@/components/icons/IconCheck.vue';
  import { ref, onBeforeMount } from 'vue';

  const props = defineProps({
    modelValue: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    title: { type: String, default: '' },
    theme: { type: String, default: '' },
    boxID: { type: Number, default: null },
  });

  const emits = defineEmits(['toggleCheckBox']);

  const onToggleCheckbox = () => {
    if (!props.disabled) {
      const newValue = !props.modelValue;
      // console.log('id ', props.modelValue);
      emits('toggleCheckBox', newValue, props.boxID);
    }
  }
</script>

<style scoped lang="scss">
.checkbox {
  position: relative;
  height: 20px;
  width: 20px;
  background-color: var(--bs-white);
  cursor: pointer;
  border-radius: toRem(4px);
  transition: background-color ease .1s;
  flex-shrink: 0;

  &-container {
    display: flex;
    align-items: center;
    column-gap: toRem(12px);
    border: none;

    span {
      // @include text4;
      // color: $dark;
      font-weight: 500;
    }
  }

  &.active {
    border: 1px solid transparent;

    &.disabled {
      background-color: var(--bs-gray-200);
      cursor: default;
    }

    &.green{
      background-color: var(--bs-primary);
    }
  }

  &.disabled {
    background-color: var(--bs-gray-200);
    opacity: .5;
    cursor: default;
  }

  &-icon {
    position: absolute;
    transform: translate(-50%, -50%);
    top: 60%;
    left: 53%;
    opacity: 0;
    transition: opacity ease .1s;
    height: 20px;
    width: 20px;

    &.active {
      opacity: 1;
      background-color: var(--bs-primary);
    }
  }
}
</style>
