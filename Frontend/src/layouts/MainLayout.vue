<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { RouterView, RouterLink, useRoute, useRouter } from "vue-router";
import Badge from "primevue/badge";
import Button from "primevue/button";

import NotificationPanel from "@/components/NotificationPanel.vue";
import UserAvatar from "@/components/UserAvatar.vue";
import { useToastStore } from "@/stores/toastStore";
import RouteMenus from "@/utils/routerMenus.js";
import ApiService, { LOGOUT_ENDPOINT } from "@/services/ApiService.js";
import { clearSession, getStoredUser } from "@/utils/authStorage.js";
import { LOGIN_ROUTER } from "@/utils/constants";

const toastStore = useToastStore();
const notificationPanel = ref();
const openNotifications = (event) => {
  notificationPanel.value.toggle(event);
};

const route = useRoute();
const router = useRouter();
const sidebarCollapsed = ref(false);
const sidebarVisible = ref(false);
const windowWidth = ref(window.innerWidth);
const isMobile = computed(() => windowWidth.value < 1024);

const storedUser = ref(getStoredUser());

const isActiveRoute = (to) => route.name === to;
const updateWidth = () => {
  windowWidth.value = window.innerWidth;

  if (windowWidth.value >= 1024) {
    sidebarVisible.value = true;
  }
};

onMounted(() => {
  updateWidth();
  window.addEventListener("resize", updateWidth);
});

onUnmounted(() => {
  window.removeEventListener("resize", updateWidth);
});

const toggleSidebar = () => {
  if (isMobile.value) {
    sidebarVisible.value = !sidebarVisible.value;
    return;
  }

  sidebarCollapsed.value = !sidebarCollapsed.value;
};

const isDark = ref(localStorage.getItem("theme") === "dark");

const toggleTheme = () => {
  isDark.value = !isDark.value;
  document.documentElement.classList.toggle("dark", isDark.value);
  localStorage.setItem("theme", isDark.value ? "dark" : "light");
};

const closeSidebar = () => {
  if (isMobile.value) sidebarVisible.value = false;
};

const logout = async () => {
  try {
    await ApiService.post(LOGOUT_ENDPOINT);
  } catch {}
  clearSession();
  router.push({ name: LOGIN_ROUTER });
};
</script>

<template>
  <div class="layout-wrapper">
    <header class="layout-topbar">
      <div class="layout-topbar-left">
        <button type="button" class="menu-button" @click="toggleSidebar">
          <i class="pi pi-bars"></i>
        </button>

        <div class="flex align-items-center gap-2">
          <i class="pi pi-box"></i>
          <div class="app-title">ERP Estoque</div>
        </div>
      </div>

      <div class="layout-topbar-actions">
        <Button
          :icon="isDark ? 'pi pi-sun' : 'pi pi-moon'"
          text
          rounded
          v-tooltip.bottom="'Alternar tema'"
          @click="toggleTheme"
        />
        <div class="notification-wrapper">
          <Button
            icon="pi pi-bell"
            text
            rounded
            @click="openNotifications"
            v-tooltip.bottom="'Histórico de notificações'"
          />

          <Badge
            v-if="toastStore.history.length > 0"
            severity="danger"
            class="notification-badge"
          />
        </div>
        <Button
          icon="pi pi-sign-out"
          text
          rounded
          v-tooltip.bottom="'Sair'"
          @click="logout"
        />
        <UserAvatar
          v-if="storedUser?.name || storedUser?.email"
          :name="storedUser?.name || storedUser?.email"
          size="md"
        />
      </div>
    </header>

    <div class="layout-body">
      <aside
        class="layout-sidebar"
        :class="{
          'layout-sidebar-open': sidebarVisible || !isMobile,
          'layout-sidebar-collapsed': !isMobile && sidebarCollapsed,
        }"
      >
        <div class="layout-menu">
          <template v-for="section in RouteMenus" :key="section.label">
            <div class="layout-menu-section">
              <div
                v-if="!sidebarCollapsed || isMobile"
                class="layout-menu-section-label"
              >
                {{ section.label }}
              </div>

              <ul>
                <li v-for="item in section.items" :key="item.label">
                  <RouterLink
                    :to="{ name: item.to }"
                    class="layout-menu-link"
                    :class="{ active: isActiveRoute(item.to) }"
                    v-tooltip.right="
                      sidebarCollapsed && !isMobile ? item.label : null
                    "
                    @click="closeSidebar"
                  >
                    <i
                      :class="[
                        item.icon,
                        { 'text-primary': isActiveRoute(item.to) },
                      ]"
                    ></i>

                    <span
                      v-if="!sidebarCollapsed || isMobile"
                      :class="{ 'text-primary': isActiveRoute(item.to) }"
                    >
                      {{ item.label }}
                    </span>
                  </RouterLink>
                </li>
              </ul>
            </div>
          </template>
        </div>
      </aside>

      <main class="layout-main">
        <div class="layout-content">
          <RouterView />
        </div>
      </main>
    </div>

    <div
      v-if="sidebarVisible && isMobile"
      class="sidebar-mask"
      @click="closeSidebar"
    />

    <NotificationPanel ref="notificationPanel" />
  </div>
</template>
