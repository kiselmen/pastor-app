<template>
  <div v-if="!loader" class="card-container" v-for="event in eventsList" :key = event.days>
    <h2 class="card-name danger" v-if="event.days < 0">{{ event.birthday.toLocaleDateString("ru", {day: '2-digit', month: '2-digit', year: 'numeric'}) }} До события: {{ event.days }} дней</h2>
    <h2 class="card-name" v-if="event.days >= 0">{{ event.birthday.toLocaleDateString("ru", {day: '2-digit', month: '2-digit', year: 'numeric'}) }} До события: {{ event.days }} дней</h2>
    <!-- <div class="card-container" > -->
      <div class="card-items">
        <PersoneItem 
          v-for="persone in event.peoples"
          class="card-item"
          :key = persone.Id
          :persone = persone
          :isActionPossible = false
        />
      </div>
    <!-- </div>   -->
  </div>
</template>

<script setup>
  import { ref, onBeforeMount, computed } from 'vue';
  import { usePeopleStore } from '@/stores/peopleStore';
  import PersoneItem from '@/components/people/PersoneItem.vue';

  const peopleStore = usePeopleStore();
  
  const loader = ref(false);

  const eventsList = computed(() => {
    const result = [];
    if (peopleStore.peoplesWithBirthday) {
      peopleStore.peoplesWithBirthday.forEach(persone => {

        const now = new Date()
        const now_day = now.getDate();
        const now_month = now.getMonth();
        const now_year = now.getFullYear();

        const birthday = new Date(persone.birthday_date);
        const birthday_day = birthday.getDate();
        const birthday_month = birthday.getMonth();
        let birthday_year = now_year;

        if (birthday_month === 11 &&  now_month === 0) {
          birthday_year = now_year - 1;
        }
        if (birthday_month === 0 &&  now_month === 11) {
          birthday_year = now_year + 1;
        }

        const thisYearBirthday = new Date(birthday_year, birthday_month, birthday_day);
        const nowYearDay = new Date(now_year, now_month, now_day);

        let diffInSecs = thisYearBirthday.getTime() - nowYearDay.getTime();
        let diffInDays = Math.round(diffInSecs / (1000 * 3600 * 24));

        const isPresent = result.filter(item => item.days === diffInDays);

        if (isPresent.length) {
          isPresent.peoples = [...isPresent.peoples, persone];
        } else {
          const newItem = {};
          newItem.days = diffInDays;
          newItem.birthday = thisYearBirthday;
          newItem.peoples = [persone];
          result.push(newItem);
        }
        
      })
      return result.sort((a,b) => a.days - b.days);
    } else return []
  });

  onBeforeMount(async () => {
    loader.value = true;
    await peopleStore.getPeoplesWithBirthayOverWeek();
    loader.value = false;
  })

</script>
<style lang="scss" scoped>
  .danger {
    color: var(--bs-danger);
  }
  .card-name {
    margin: 0;
    margin-top: 5px;
  }
</style>