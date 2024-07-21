<template>
  <main v-if="!userStore.user?.email_verified_at">
        К сожелению ваша учетная запись все еще не активирована
  </main>
  <main v-if="userStore.user?.email_verified_at">
      <TheWelcome />
  </main>
</template>

<script setup>
  import TheWelcome from '@/components/TheWelcome.vue'
  import { useUserStore } from '@/stores/userStore';

  const userStore = useUserStore();
  
</script>