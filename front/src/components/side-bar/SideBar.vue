<template>
  <div class="sidebar" :class="menuStore.sideBarClasses">
    <ul>
        <li>
          <RouterLink to="/">
            <div class="sidebar-item">
              <EcosystemIcon class="sidebar-icon"/>
              <span class="sidebar-link">Dashboard</span>
            </div>
          </RouterLink>
        </li>
        <li>
          <RouterLink to="/peoples">
            <div class="sidebar-item">
              <CommunityIcon class="sidebar-icon"/>
              <span class="sidebar-link">Прихожане</span>
            </div>
          </RouterLink>
        </li>
    </ul>
  </div>
</template>

<script setup>
  import { useMenuStore } from '@/stores/menuStore';
  import CommunityIcon from '@/components/icons/IconCommunity.vue'
  import EcosystemIcon from '@/components/icons/IconEcosystem.vue'

  const menuStore = useMenuStore();
</script>

<style lang="scss" scoped>
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
    height: calc(100vh - 64px);
    overflow: auto;
    z-index: 10;
    transition: 0.5s ease-in-out;
    scrollbar-width: thin;
    background-color: var(--bs-gray-900);
    &-off{
      left: -230px;
    }
    &-item {
      display: flex;
      flex-direction: row;
      align-items: center;
    }
    &-icon {
      margin-left: 5px;
      margin-right: 20px;
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
</style>