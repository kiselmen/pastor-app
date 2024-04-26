<template>
  <div class="checkbox-container">
    <div class="checkbox" :class="[{ active: props.modelValue, disabled: props.disabled },props.theme]" @click="toggleCheckbox">
      <input type="checkbox" :disabled="props.disabled" class="hidden-input">
      <IconCheck class="checkbox-icon" :class="{ active: props.modelValue, disabled: props.disabled }" />
    </div>
    <span v-if="props.title != ''">{{ props.title }}</span>
  </div>
</template>

<script setup>
  import IconCheck from '@/components/icons/IconCheck.vue';

  const props = defineProps({
    modelValue: false,
    disabled: false,
    title: '' ,
    theme: ''
  })
  const emits = defineEmits(['update:modelValue'])
  const toggleCheckbox = () => {
    if (!props.disabled) {
      const newValue = !props.modelValue
      emits('update:modelValue', newValue)
    }
  }
</script>

<style scoped lang="scss">
.checkbox {
  position: relative;
  height: toRem(20px);
  width: toRem(20px);
  border: 1px solid #9BA3B2;
  background-color: #ffffff;
  cursor: pointer;
  border-radius: toRem(4px);
  transition: background-color ease .1s;
  flex-shrink: 0;

  &-container {
    display: flex;
    align-items: center;
    column-gap: toRem(12px);

    span {
      // @include text4;
      // color: $dark;
      font-weight: 500;
    }
  }

  &.active {
    // background-color: $brand-dark;
    border: 1px solid transparent;

    &.disabled {
      // background-color: $bg4;
      cursor: default;
    }

    &.green{
      // background-color: $green;
    }
  }

  &.disabled {
    // background-color: $bg4;
    opacity: .5;
    cursor: default;
  }

  &-icon {
    position: absolute;
    transform: translate(-50%, -50%);
    top: 50%;
    left: 53%;
    opacity: 0;
    transition: opacity ease .1s;
    height: toRem(16px);
    width: toRem(16px);

    &.active {
      opacity: 1;
    }
  }
}
</style>
