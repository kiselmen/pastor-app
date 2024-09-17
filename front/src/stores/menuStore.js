import { ref } from 'vue';
import { defineStore } from 'pinia';

export const useMenuStore = defineStore('menuStore', () => {

  const burgerClasses = ref('burger');
  const logoClasses = ref('');
  const sideBarClasses =ref('');
  const topBarClasses = ref('');
  const contentClasses = ref('content');
  const isMobileView = ref(false);

  const onBurgerClick = () => {
    const newStateBurger = burgerClasses.value.includes('open') ? 'burger close': 'burger open';
    burgerClasses.value = newStateBurger;

    const newSateLogo = logoClasses.value ? '': 'logo-off';
    logoClasses.value = newSateLogo;

    const newSateSideBar = sideBarClasses.value ? '': 'sidebar-off';
    sideBarClasses.value = newSateSideBar;

    const newSateTopBar = topBarClasses.value ? '': 'topbar-off';
    topBarClasses.value = newSateTopBar;

    const newSateContent = contentClasses.value.includes('content-off') ? 'content': 'content content-off';
    contentClasses.value = newSateContent;
  };

  // const removePaddingAndMargin = () => {
  //   contentClasses.value = 'content content-off';
  // }

  const setContentClasses = (val) => {
    contentClasses.value = val;
  };

  const setIsMobileView = (type) => {
    isMobileView.value = type;
  };

  return {
    burgerClasses,
    logoClasses,
    sideBarClasses,
    topBarClasses,
    contentClasses,
    isMobileView,
    onBurgerClick,
    // removePaddingAndMargin,
    setContentClasses,
    setIsMobileView,
  };
});
