<template>
  <div class="item">
    <div class="icon">
      <slot name="icon"></slot>
    </div>
    <div class="details">
      <h3>
        <slot name="heading"></slot>
      </h3>
      <slot></slot>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.item {
  display: flex;
  position: relative;
  margin-right: 10px;
  align-items: center;
}

.details {
  display: flex;
  justify-content: center;
  align-content: center;
  margin-left: 5px;
}

.icon {
  display: flex;
  place-items: center;
  place-content: center;
  width: 24px;
  height: 24px;
  color: var(--bs-gray-800);
}

h3 {
  font-size: 1rem;
  font-weight: 500;
  margin: auto;
  color: var(--color-heading);
  @media (max-width: 1440px) {
    font-size: 0.8rem;
    font-weight: 400;
  }
}

</style>
