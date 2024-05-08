<template>
  <button class="button__remove">         
  </button>
</template>

<style lang="scss" scoped>
    .button__remove {
      cursor: pointer;
      position: absolute;
      outline: none;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background-color: var(--bs-primary);
      // border: 0;
      // border: 4px solid var(--bs-white);    
      // &:hover{
      //   background: var(--bs-gray-400);
      //   &::before {
      //     background-color: var(--bs-primary);
      //   }
      //   &::after{
      //     background-color: var(--bs-primary);
      //   }
      // }

      &::before {
        content: "";
        position: absolute;
        z-index: 5;
        top: calc(50% - 1px);
        left: calc(50% - 7px);
        width: 15px;
        height: 2px;
        background-color: var(--bs-white);
        rotate: 45deg;
      }
      &::after {
        content: "";
        position: absolute;
        z-index: 5;
        top: calc(50% - 1px);
        left: calc(50% - 7px);
        width: 15px;
        height: 2px;
        background-color: var(--bs-white);
        rotate: -45deg;
      }
    }

</style>