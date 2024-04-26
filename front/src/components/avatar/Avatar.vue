<template>
  <div class="avatar" v-click-outside="onCloseMenu">
    <div class="avatar-img" :style = "{ backgroundImage : 'url(' + getImgPath(userStore.user.image_url) +')' }" @click="onToggleMenu"></div>
    <div class="avatar-dropdown" v-if="isOpen">
      <ul>
        <li>
          <RouterLink to="/home" @click="onCloseMenu">
            <div class="avatar-menuitem">
              <span class="avatar-link">Профиль</span>
            </div>
          </RouterLink>
        </li>
        <li>
          <a to="#" @click.prevent="onLogoutUser">
            <div class="avatar-menuitem">
              <span class="avatar-link">Выход</span>
            </div>
          </a>
        </li>
      </ul>
    </div>
  </div>

</template>

<script setup>
  import { ref, onBeforeMount } from 'vue';
  import getImgPath from '@/utils/imagePlugin.js';
  import { useUserStore } from '@/stores/userStore';

  const userStore = useUserStore();

  const isOpen = ref(false);

  const onCloseMenu = () => {
    isOpen.value = false;
  }

  const onToggleMenu = () => {
    isOpen.value = !isOpen.value;
  }

  const onLogoutUser = async () => {
    await userStore.logoutUser()
  }

  onBeforeMount(() => {
    // console.log('QETWFGH ', getImgPath('peoples/e1bbc9a42f4ebf34f45a99a87246cee6.jpg'));
  })
</script>

<style lang="scss" scoped>
  .avatar {
    position: relative;
    margin-left: auto;
    &-img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: none;
      background-position: center center;
      background-repeat: no-repeat;
      background-size: cover;
    }
    &-img:hover {
      cursor: pointer;
    }
    &-dropdown {
      position: absolute;
      min-width: 160px;
      left: -10px;
      transform: translate3d(-105px, 40px, 0px);
      will-change: transform;
      display: block;
      top: 11px;
      border-radius: 0;
      z-index: 100;
      font-size: 16px;
      font-weight: 400;
      color: var(--bs-gray-700);
      text-align: left;
      list-style: none;
      background-color: var(--bs-white);
      background-clip: padding-box;
      border: 1px solid var(--bs-gray-600);
      padding: 10px 0;
    }
  }

  .avatar li {
    margin: 0;
    padding: 0;
    list-style: none;
    text-decoration: none;    
  }

  .avatar ul li a {
    display: block;
    width: 100%;
    padding: 4px 16px;
    clear: both;
    font-weight: 400;
    color: var(--bs-gray-600);
    text-align: inherit;
    text-decoration: none;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
    border-radius: 0;
    cursor: pointer;
  }

  .avatar ul li a:hover {
    color: var(--bs-gray-200);
    background-color: var(--bs-primary);
  }

  // .router-link-active {
  //   color: var(--bs-light);
  //   background-color: var(--bs-gray-800);
  // }

</style>