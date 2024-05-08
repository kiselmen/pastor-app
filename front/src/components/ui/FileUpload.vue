<template>
  <div class="photo-uploader">
    <div
      class="photo-uploader__wrapper"
      :class="{ 'photo-uploader__wrapper--drag': isDragStarted }"
    >
      <input
        ref="fileRef"
        class="photo-uploader__input"
        type="file"
        accept="image/*"
        :disabled = "loader"
        @change="handleFileUpload"
        @dragenter="setDragStartedOn"
        @dragleave="setDragStartedOff"
      >
      <img v-if = "!showPreview&&!loader&&!props.prevImage"
        src="@/assets/img/upload.svg"
        class="photo-uploader__icon"
        alt="Загрузите фото"
      >
      <div v-if = "!showPreview&&!loader&&props.prevImage"
        class="photo-uploader__photo"
        :style = "{ backgroundImage : 'url(' + getImgPath(props.prevImage) +')' }"
      >
      </div> 
      <span v-if = "!showPreview&&loader"
        class="photo-uploader__icon"
      >
        Loading...
      </span>
      <div v-if = "showPreview"
        class="photo-uploader__photo"
        :style = "{backgroundImage : 'url(' + imagePreview + ')'}"
      >
      </div>
      <XButton 
        v-if = "showPreview"
        @click="removePhoto"
        class="photo-uploader__remove" 
      />
    </div>
  </div>

</template>

<script setup>
import { ref } from 'vue';
import getImgPath from '@/utils/imagePlugin.js';
import XButton from '@/components/ui/XButton.vue';

const props = defineProps({
  prevImage: { type: String, default: null },
})
const emit = defineEmits(['uploadImage', 'clearImage'])
const fileRef = ref(null);
const file = ref(null);
const showPreview = ref(false);
const imagePreview =ref(null);
const isDragStarted = ref(false);
const loader = ref(false);

const handleFileUpload = () => {
 
  console.log(fileRef.value.files);
  file.value = fileRef.value.files[0];
  let reader  = new FileReader();
  loader.value = true;
  reader.addEventListener("load", function () {
    showPreview.value = true;
    isDragStarted.value = false;
    imagePreview.value = reader.result;
    loader.value = false;
    emit('uploadImage', file.value);
  }.bind(this), false);

  if( file.value ){
    if ( /\.(jpe?g|png|gif)$/i.test(file.value.name) ) {
      reader.readAsDataURL(file.value);
    }
  }
}

const setDragStartedOn = () => {
  showPreview.value = false;
  isDragStarted.value = true;
}

const setDragStartedOff = () => {
  showPreview.value = imagePreview ? true: false;
  isDragStarted.value = false;
}

const removePhoto = () => {
  showPreview.value = false;
  isDragStarted.value = false;
  file.value = null;
  imagePreview.value = null;
  emit('clearImage');
  loader.value = false;
}

</script>

<style lang="scss" scoped>

  .photo-uploader {
    &__wrapper {
      margin-top: 30px  ;
      position: relative;
      text-align: center;
      height: 150px;
      display: flex;
      justify-content: center;
      align-items: center;
      border: 1px solid var(--bs-gray-500);
      width: 200px;
      height: 200px;

      &--drag {
        color: rgba(var(--bs-light), 0);
        border-color: var(--bs-primary);
      }
    }

    &__input {
      cursor: pointer;
      position: absolute;
      z-index: 2;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      width: 100%;
      opacity: 0;
    }

    &__photo {
      position: relative;
      border-radius: 10px;
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center center;
      width: 100%;
      height: 100%;
    }

    &__remove {
      top: -15px;
      left: calc(100% - 15px);
    }

    &__icon {
      opacity: 0.3;
      width: 50px;
    }
  }
</style>