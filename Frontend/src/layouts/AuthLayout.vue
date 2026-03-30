<script setup>
import { ref, onMounted } from "vue";
import { RouterView } from "vue-router";

const isDark = ref(localStorage.getItem("theme") === "dark");

onMounted(() => {
  document.documentElement.classList.toggle("dark", isDark.value);
});

const toggleTheme = () => {
  isDark.value = !isDark.value;
  document.documentElement.classList.toggle("dark", isDark.value);
  localStorage.setItem("theme", isDark.value ? "dark" : "light");
};
</script>

<template>
  <div class="auth-layout">
    <button
      type="button"
      class="auth-theme-toggle"
      @click="toggleTheme"
      v-tooltip.bottom="'Alternar tema'"
    >
      <i :class="isDark ? 'pi pi-sun' : 'pi pi-moon'"></i>
    </button>

    <div class="auth-layout-inner">
      <div class="auth-brand mb-4">
        <i class="pi pi-box text-primary text-4xl"></i>
        <h1 class="auth-title">ERP Estoque</h1>
        <p class="auth-subtitle text-color-secondary">Acesse sua conta ou crie uma!</p>
      </div>

      <div class="auth-card surface-card p-4 border-round shadow-2">
        <RouterView />
      </div>
    </div>
  </div>
</template>
