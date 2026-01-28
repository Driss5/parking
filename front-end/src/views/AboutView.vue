<script setup>

import LogoutAuth from '@/components/Auth/LogoutAuth.vue';

</script>


<template>
  <div class="about">
    <h1>This is an about page</h1>
    <LogoutAuth />
  </div>
</template>

<style>
@media (min-width: 1024px) {
  .about {
    min-height: 100vh;
    display: flex;
    align-items: center;
  }
}
</style>
