<script setup>
import { inject } from "vue";
import { RouterView } from "vue-router";
import Toast from "primevue/toast";
import LoadingOverlay from "@/components/LoadingOverlay.vue";

const isLoading = inject("isLoading");
</script>

<template>
  <RouterView />
  <Toast />
  <LoadingOverlay :is-loading="isLoading" />
</template>
