<template>
  <div class="complete-title">{{ completeTitle }}</div>
  <div class="complete-submit-btns">
    <div
      class="complete-submit secondary-btn"
      :class="`complete-submit--${action}`"
      @click="emits('accept')"
    >
      <UiIconsLoader v-if="loading" color="#292c33" class="loader" />
      <template v-else>
        {{ props.lang === 'ru' ? 'Да' : 'Accept' }}
      </template>
    </div>
    <div
      class="complete-submit close-btn"
      @click="() => emits('toggleModal')"
    >
      {{ props.lang === 'ru' ? 'Отмена' : 'Cancel' }}
    </div>
  </div>
</template>

<script setup lang="ts">
  import { computed } from 'vue'
  import type { ApplicationItem } from '~/src/types/applications/applications'

  // Define props
  const props = defineProps<{
    applications: ApplicationItem[],
    action: string,
    lang: string,
    loading: boolean
  }>()

  const emits = defineEmits(['toggleModal', 'accept'])

  // Define computed property
  const completeTitle = computed(() => {
    // Check if `props.applications` is an array and its length
    const isMultiple = Array.isArray(props.applications) && props.applications.length > 1
    const actionText = getActionText(props.action, isMultiple)
    const applicationText = getApplicationText(isMultiple, props.applications.length)

    // Return the corresponding text
    return `${props.lang === 'ru'
        ? `Вы уверены, что хотите ${actionText} ${props.applications.length} ${applicationText}`
        : `Are you sure you want to ${actionText} ${props.applications.length} application${isMultiple ? 's' : ''}`}`
  })

  // Function to get action text based on conditions
  const getActionText = (action: string, isMultiple: boolean) => {
    switch (action) {
      case 'accept':
        return isMultiple ? 'одобрить' : 'одобрить'
      case 'decline':
        return isMultiple ? 'отклонить' : 'отклонить'
      case 'refresh':
        return isMultiple ? 'отправить повторно' : 'отправить повторно'
      case 'remove':
        return isMultiple ? 'удалить' : 'удалить'
      default:
        return 'выполнить'
    }
  }

  // Function to get application text based on conditions
  const getApplicationText = (isMultiple: boolean, count: number) => {
    // Define your logic for application text here
    if (isMultiple) {
      if (count % 10 === 1 && count % 100 !== 11) {
        return 'заявку'
      } else if ((count % 10 === 2 || count % 10 === 3 || count % 10 === 4) && (count % 100 < 10 || count % 100 >= 20)) {
        return 'заявки'
      } else {
        return 'заявок'
      }
    } else if (count === 1 || count % 10 === 1 && count % 100 !== 11) {
      return 'заявку'
    } else if ((count % 10 === 2 || count % 10 === 3 || count % 10 === 4) && (count % 100 < 10 || count % 100 >= 20)) {
      return 'заявки'
    } else {
      return 'заявок'
    }
  }
</script>

<style scoped lang="scss">
.complete-title {
  font-size: 18px;
  font-weight: 600;
  line-height: 140%;
  margin-bottom: 16px;
}
.complete-submit-btns {
  align-items: center;
  display: flex;
  justify-content: center;
  width: 100%;
  column-gap: 14px;
}
.complete-submit {
  font-size: 14px;
  font-weight: 600;
  height: 48px;
  line-height: 20px;
  padding: 0 8px;
  align-items: center;
  display: flex;
  justify-content: center;
  width: 100%;
  cursor: pointer;
  // transition: $ease;
  border-radius: 12px;

  &--accept{
    background: rgba(34,197,109,.1);
    color: #22c56d;
    &:hover{
      background: rgba(34,197,109,.2)
    }
  }
  &--decline{
    background: rgba(227,37,53,.1);
    color: #e32535;
    &:hover{
      background: rgba(227,37,53,.2)
    }
  }
  &--refresh{
    background: rgba(55,122,200,.1);
    color: #377ac8;
    &:hover{
      background: rgba(55,122,200,.2)
    }
  }
}
.close-btn{
  background: #f4f7f9;
  &:hover{
    background: #eceef1;
  }
}
.loader{
  width: 20px;
  height: 20px;
}
</style>
