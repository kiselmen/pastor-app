<template>
  <div class = "card">
    <div class="card-title">
      <span>
        {{ props.family.name }}
      </span>
      <EditDuotoneIcon class="change_button" @click="onEditClick"/>
    </div>
    <div class="card-box">
      <div class="card-row">
        <div class="card-img" :style = "{ backgroundImage : 'url(' + getImgPath(props.family.head?.image_url) +')' }"></div>
        <div class="card-column">
          <PrihodMiniItem
            v-if="props.family.head"
            :prihod = "props.family.head.prihod"
          />
          <div class="card-target">Размер: {{ props.family.FamilyComposition }}</div>
        </div>
      </div>
    </div>
    <div class="card-box">
      <div class="card-row">
        <div class="card-target">Состав семьи</div>  
      </div>  
      <div class="card-row flex-wrap">
        <PersoneMiniItem
            v-for="persone in props.family.people"
            :persone = "persone"
        />
      </div>
    </div>
    <div class="card-row">
      <DateItem>
        <template #icon>
          <!-- <BirthdayIcon /> -->
        </template>
        <template #heading>
          {{ props.family.discription }}
        </template>
      </DateItem>
    </div>
  </div>
</template>

<script setup>

  import getImgPath from '@/utils/imagePlugin.js';
  import EditDuotoneIcon from '@/components/icons/IconEditDuotone.vue';
  import DateItem from '@/components/people/DateItem.vue';
  import PrihodMiniItem from '@/components/prihod/PrihodMiniItem.vue';
  import PersoneMiniItem from '@/components/people/PersoneMiniItem.vue';

  const props = defineProps({
    family: { type: Object, default: new Object() },
  })

  const emits = defineEmits(['editFamily']);

  const onEditClick = () => {
    emits('editFamily', props.family.id)
  }

</script>

<style lang="scss" scoped>
  .card-column {
    justify-content: space-between;  
  }

  .flex-wrap {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }
</style>