export default {
  beforeMount(el, binding) {
      el.clickOutsideEvent = function(event) {
          if (!(el === event.target || el.contains(event.target))) {
            // console.log(el);
              binding.value(event);
          }
      };
      document.addEventListener('click', el.clickOutsideEvent);
  },
  unmounted(el) {
      document.removeEventListener('click', el.clickOutsideEvent);
  },
};