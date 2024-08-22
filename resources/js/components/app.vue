<template>
    <router-view v-if="!loading" />
</template>

<script setup>
import { ref, provide } from 'vue';
const loading = ref(true);

provide("isLoading", loading)

</script>
