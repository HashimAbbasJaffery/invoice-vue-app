<template>
    <router-view v-show="!loading" />
    <div v-show="loading" class="loader-container">
        <div class="loader-item">
             <div class="loader"></div>
        </div>
    </div>
</template>

<script setup>
import { ref, provide } from 'vue';
const loading = ref(true);

provide("isLoading", loading);



</script>

<style scoped>

.loader-container {
      display: grid;
      place-items: center; /* shorthand for aligning items both horizontally and vertically */
      height: 100vh;
    }

    .loader-item {
      width: 200px;
      height: 100px;
      display: flex;
      justify-content: center;
    }
/* HTML: <div class="loader"></div> */
.loader {
  width: 60px;
  aspect-ratio: 1;
  --g: conic-gradient(from -90deg at 10px 10px,#fff 90deg,#0000 0);
  background: var(--g), var(--g), var(--g);
  background-size: 50% 50%;
  animation: l16 1s infinite;
}
@keyframes l16 {
   0%   {background-position:0    0   ,10px 10px,20px 20px}
   50%  {background-position:0    20px,10px 10px,20px 0   }
   100% {background-position:20px 20px,10px 10px,0    0   }
}
</style>
