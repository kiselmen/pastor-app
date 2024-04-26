<template>
  <div
    class="nax-card"
    :style="{
      flexBasis: props.styles.width,
      height: props.styles.height,
      borderRadius: props.radius + 'px',
    }"
  >
    <div class="img-card" />
  </div>
</template>

<script setup>

const props = defineProps({
  radius: {
    type: String,
    default: '20'
  },
  styles: {
    type: Object,
    default: () => ({
      width: '100%',
      height: '100%'
    })
  }
})

</script>

<style>
.nax-card {
  color: #000;
  background-color: #fff;
  overflow: hidden;
}

.nax-card .img-card {
  width: 100%;
  max-width: 100%;
  height: 100%;
  background: linear-gradient(120deg, rgb(238, 238, 238) 30%, #ffffff 38%, #ffffff 40%, rgb(238, 238, 238) 48%);
  background-size: 200% 100%;
  background-position: 100% 0;
  animation: SkeletonLoading 2s infinite;
}

.nax-card .img-card img {
  background-color: #eee;
  object-fit: unset;
}

.nax-card .text-card p {
  font-size: 24px;
  height: 28px;
  overflow: hidden;
  letter-spacing: 0.5px;
}

.nax-card .text-banner h2 {
  font-size: 30px;
}

.nax-card .text-banner p {
  margin-top: 5px;
  font-size: 19px;
}

@keyframes SkeletonLoading {
  100% {
    background-position: -100% 0;
  }
}
</style>
