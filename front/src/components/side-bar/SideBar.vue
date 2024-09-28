<template>
  <div class="sidebar" :class="menuStore.sideBarClasses">
    <ul>
        <li>
          <RouterLink to="/" @click="onMenuClick">
            <div class="sidebar-item">
              <EcosystemIcon class="sidebar-icon"/>
              <span class="sidebar-link">Dashboard</span>
            </div>
          </RouterLink>
        </li>
        <li>
          <RouterLink to="/peoples" @click="onMenuClick">
            <div class="sidebar-item">
              <CommunityIcon class="sidebar-icon"/>
              <span class="sidebar-link">Прихожане</span>
            </div>
          </RouterLink>
        </li>
        <li v-if="userStore.isPermition(1)">
          <RouterLink to="/families" @click="onMenuClick">
            <div class="sidebar-item">
              <SupportIcon class="sidebar-icon"/>
              <span class="sidebar-link">Семьи</span>
            </div>
          </RouterLink>
        </li>
        <li v-if="userStore.isPermition(1)">
          <RouterLink to="/prihods" @click="onMenuClick">
            <div class="sidebar-item">
              <DocumentationIcon class="sidebar-icon"/>
              <span class="sidebar-link">Участки</span>
            </div>
          </RouterLink>
        </li>
        <li v-if="userStore.isPermition(0)">
          <a @click.prevent="onToggleMenuItem">
            <div class="sidebar-item">
              <ToolingIcon class="sidebar-icon"/>
              <span class="sidebar-link">Отчеты</span>
              <ChevronDownIcon class="sidebar-chevron"/>
            </div>
          </a>
          <ul class="children">
            <li>
              <RouterLink to="/report-borns" @click="onMenuClick">
                <div class="sidebar-subitem">
                  <span class="sidebar-link">Родилось/Умерли</span>
                </div>
              </RouterLink>
            </li>
          </ul>
        </li>
        <li v-if="userStore.isPermition(0)">
          <a @click.prevent="onToggleMenuItem">
            <div class="sidebar-item">
              <ToolingIcon class="sidebar-icon"/>
              <span class="sidebar-link">НСИ</span>
              <ChevronDownIcon class="sidebar-chevron"/>
            </div>
          </a>
          <ul class="children">
            <li>
              <RouterLink to="/target-groups" @click="onMenuClick">
                <div class="sidebar-subitem">
                  <span class="sidebar-link">Целевые группы</span>
                </div>
              </RouterLink>
            </li>
            <li>
              <RouterLink to="/services" @click="onMenuClick">
                <div class="sidebar-subitem">
                  <span class="sidebar-link">Виды служения</span>
                </div>
              </RouterLink>
            </li>
            <li>
              <RouterLink to="/levels" @click="onMenuClick">
                <div class="sidebar-subitem">
                  <span class="sidebar-link">Уровни дисциплины</span>
                </div>
              </RouterLink>
            </li>
          </ul>
        </li>
    </ul>
  </div>
</template>

<script setup>
  import { useMenuStore } from '@/stores/menuStore';
  import { useUserStore } from '@/stores/userStore';
  import CommunityIcon from '@/components/icons/IconCommunity.vue';
  import EcosystemIcon from '@/components/icons/IconEcosystem.vue';
  import SupportIcon from '@/components/icons/IconSupport.vue';
  import DocumentationIcon from '@/components/icons/IconDocumentation.vue';
  import ToolingIcon from '@/components/icons/IconTooling.vue';
  import ChevronDownIcon from '@/components/icons/IconChevronDown.vue';

  const menuStore = useMenuStore();
  const userStore = useUserStore();

  const onToggleMenuItem = (e) => {
    // console.log(e.currentTarget.parentNode); 
    e.currentTarget.parentNode.classList.toggle('open');
  }

  const onMenuClick = () => {
    if (menuStore.isMobileView) {
      menuStore.onBurgerClick();
    }
  }

</script>

<style lang="scss" scoped>
  a {
    cursor: pointer;
  }

  ul {
    list-style: none;
    text-decoration: none;
  }

  li {
    display: list-item;
    text-align: -webkit-match-parent;
    unicode-bidi: isolate;
    width: 100%
  }

  .sidebar {
    padding-top: 50px;
    position: fixed;
    top: 64px;
    left: 0;
    width: 230px;
    @media (max-width: 768px) {
      width: 100%;
    }
    height: calc(100vh - 64px);
    overflow: auto;
    z-index: 10;
    transition: 0.5s ease-in-out;
    scrollbar-width: thin;
    background-color: var(--bs-gray-900);
    &-off{
      left: -230px;
      @media (max-width: 768px) {
        left: -100%;
      }
    }
    &-item {
      display: flex;
      flex-direction: row;
      align-items: center;
    }
    &-subitem {
      padding-left: 30px;
    }
    &-icon {
      margin-left: 5px;
      margin-right: 20px;
    }
    &-chevron{
      margin-left: auto;
    }
    &-link {
      display: inline-block;

    }
  }

  .sidebar > ul > li {
    border-bottom: 1px solid var(--bs-gray-800);
  }

  .sidebar ul li a {
    display: block;
    padding: 10px 15px;
    color: var(--bs-gray-100);
    font-size: 15px;
    font-weight: 400;
    line-height: 22.5px;
    letter-spacing: 0.9px;
    text-decoration-color: var(--bs-gray-100);
    position: relative;
  }

  .sidebar ul li a:hover {
    color: var(--bs-light);
    background-color: var(--bs-gray-700);
  }

  .router-link-active {
    color: var(--bs-light);
    background-color: var(--bs-gray-800);
  }

  .children {
    display: none;
  }

  .open >.children {
    display: block;
  }

  .open >a >.sidebar-item >.sidebar-chevron {
    transform: rotate(180deg);
  }

</style>