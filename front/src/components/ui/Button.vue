<script lang="ts" setup>
  const props = defineProps({
    theme: { type: String, default: 'brand' },
    disabled: { type: Boolean, default: false },
    isLoading: { type: Boolean, default: false },
    outline: { type: Boolean, default: false }
  })
</script>

<template>
  <button
    class="btn"
    :class="[
      `btn--${props.theme}`,
      props.outline ? 'btn--outline' : '',
      props.disabled ? 'btn--disabled' : ''
    ]"
    :disabled="props.disabled"
  >
    <div v-if="props.isLoading" class="loader">
      <UiIconsLoader />
    </div>
    <template v-else>
      <slot name="before" />
      <slot />
      <slot name="after" />
    </template>
  </button>
</template>

<style lang="scss">

.btn {
  // @include df;
  // @include unselectable;
  border-radius: toRem(8px);
  // background: $brand;
  color: var(--bs-white);
  width: fit-content;
  padding: toRem(12px) toRem(12px);
  cursor: pointer;
  column-gap: 6px;
  border: 0;
  position: relative;
  // transition: $transition;
  font-size: toRem(14px);
  font-weight: 600;
  height: toRem(48px);
  span{
    white-space: nowrap;
  }
  .loader {
    display: inline;
    height: 32px;
    left: 50%;
    position: absolute;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 32px;

    svg {
      height: 100%;
      width: 100%;
    }
  }

  &--brand {
    // background: $brand;
    color: var(--bs-white);

    &:hover {
      // background: $brand-dark;
    }
  }
    &--violet{
        // background: $violet;
        color: var(--bs-white);
        &:hover{
            // background: $violet-dark;
        }
    }
  &--dark {
    // background: $dark;
    color: var(--bs-white);

    &:hover {
      // background: $dark-black;
    }
  }
  &--disabled{
    pointer-events: none;
    // background-color: $gray3;
    opacity: .5;
  }
  &--white {
    background: var(--bs-white);
    // color: $dark;
    cursor: pointer;
    // @include buttonS;

    span {
      // transition: $linear;
    }

    &:hover {
      // background: $gray2;

      span {
        color: var(--bs-white);
      }
    }
  }

  &--gray {
    // background-color: $bg4;
    // color: $dark;

    &:hover {
      // background: $bg2;
    }
  }

  &--disabled {
    // color: $dark;
    opacity: .5;
    // background-color: $gray2;
    cursor: default;

    &:hover {
      // background: $gray2;
    }
  }

  &--error {
    // background: $red;
    color: var(--bs-white);
  }

  &--outline{
    background: transparent;
    // border: 1px solid $gray3;
    // color: $black;
    &:hover{
      // background: $gray3;
    }
  }
}
.btn--green {
  // background: $green;
  color: var(--bs-white)
}

.btn--green:hover {
  // background: $green;
  filter: brightness(.9)
}
.btn--light-green{
  background: #ecfff5;
  color: #22c56d;
  &:hover{
    background: #ecfff5;
    filter: brightness(.9);
  }
}
.btn--light-green svg circle {
  stroke: #292c33
}

.btn--light-red {
  background: #fef2f2;
  color: #e32535
}

.btn--light-red:hover {
  background: #fef2f2;
  filter: brightness(.9)
}

.btn--light-red svg circle{
  stroke: #292c33
}

.btn--light-blue {
  background: #edf6ff;
  color: #005fc4
}

.btn--light-blue:hover {
  background: #edf6ff;
  filter: brightness(.9)
}

.btn--light-blue svg circle {
  stroke: #292c33
}

</style>
