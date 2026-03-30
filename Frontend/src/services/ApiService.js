import axios from "axios";
import { startLoading, stopLoading } from '@/utils/loading.js';
import { getToken, clearSession } from "@/utils/authStorage.js";

// PRODUTOS
export const PRODUCTS_ENDPOINT = "produtos";
export const PURCHASES_ENDPOINT = "compras";
export const SALES_ENDPOINT = "vendas";
// AUTH
export const LOGIN_ENDPOINT = "login";
export const REGISTER_ENDPOINT = "register";
export const LOGOUT_ENDPOINT = "logout";

const ApiService = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || "http://localhost:3000/api",
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

// Interceptor para mostrar loading antes da requisição
ApiService.interceptors.request.use(
  (config) => {
    const token = getToken();
    const isLogin = String(config.url ?? "").includes("v1/login");
    if (token && !isLogin) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    startLoading();
    return config;
  },
  (error) => {
    stopLoading();
    return Promise.reject(error);
  }
);

// Interceptor para esconder loading após a resposta
ApiService.interceptors.response.use(
  (response) => {
    stopLoading();
    return response;
  },
  (error) => {
    stopLoading();
    if (error.response?.status === 401) {
      const url = String(error.config?.url ?? "");
      if (!url.includes("v1/login")) {
        clearSession();
        const path = window.location.pathname;
        if (!path.startsWith("/auth")) {
          window.location.href = "/auth/login";
        }
      }
    }
    return Promise.reject(error);
  }
);

export default ApiService;
