import { createRouter, createWebHistory } from "vue-router";

import MainLayout from "@/layouts/MainLayout.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";

import ProductsPage from "@/pages/ProductsPage.vue";
import PurchasesPage from "@/pages/PurchasesPage.vue";
import SalesPage from "@/pages/SalesPage.vue";
import LoginPage from "@/pages/auth/login.vue";
import RegisterPage from "@/pages/auth/register.vue";

import { getToken } from "@/utils/authStorage.js";

import {
  PRODUCTS_ROUTER,
  PURCHASES_ROUTER,
  SALES_ROUTER,
  LOGIN_ROUTER,
  REGISTER_ROUTER,
} from "@/utils/constants.js";

const routes = [
  {
    path: "/",
    component: MainLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: "produtos",
        name: PRODUCTS_ROUTER,
        component: ProductsPage,
      },
      {
        path: "compras",
        name: PURCHASES_ROUTER,
        component: PurchasesPage,
      },
      {
        path: "vendas",
        name: SALES_ROUTER,
        component: SalesPage,
      },
    ],
    redirect: { name: PRODUCTS_ROUTER },
  },
  {
    path: "/auth",
    component: AuthLayout,
    meta: { guestOnly: true },
    children: [
      {
        path: "login",
        name: LOGIN_ROUTER,
        component: LoginPage,
      },
      {
        path: "register",
        name: REGISTER_ROUTER,
        component: RegisterPage,
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, _from, next) => {
  const token = getToken();
  const needsAuth = to.matched.some((r) => r.meta.requiresAuth);
  const guestOnly = to.matched.some((r) => r.meta.guestOnly);

  if (needsAuth && !token) {
    next({
      name: LOGIN_ROUTER,
      query: { redirect: to.fullPath },
    });
    return;
  }

  if (guestOnly && token) {
    next({ name: PRODUCTS_ROUTER });
    return;
  }

  next();
});

export default router;
