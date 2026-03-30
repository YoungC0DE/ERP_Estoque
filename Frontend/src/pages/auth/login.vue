<script setup>
import { ref } from "vue";
import { RouterLink, useRoute, useRouter } from "vue-router";
import InputText from "primevue/inputtext";
import Password from "primevue/password";
import Button from "primevue/button";

import ValidationError from "@/components/ValidationError.vue";
import ApiService, { LOGIN_ENDPOINT } from "@/services/ApiService.js";
import ToastService from "@/services/ToastService.js";
import { validateLogin } from "@/services/ValidationService.js";
import { setSession } from "@/utils/authStorage.js";
import { LOGIN_ROUTER, REGISTER_ROUTER } from "@/utils/constants.js";

const Toast = ToastService();
const route = useRoute();
const router = useRouter();

const email = ref("");
const password = ref("");
const loading = ref(false);
const formErrors = ref({});

const submit = async () => {
  const errors = validateLogin({
    email: email.value,
    password: password.value,
  });

  if (Object.keys(errors).length > 0) {
    formErrors.value = errors;
    return;
  }

  formErrors.value = {};

  loading.value = true;
  try {
    const { data } = await ApiService.post(LOGIN_ENDPOINT, {
      email: email.value.trim(),
      password: password.value,
    });
    setSession(data.token, data.user);
    const redirect =
      typeof route.query.redirect === "string" ? route.query.redirect : "/";

    await router.replace(redirect);
  } catch (error) {
    const msg =
      error.response?.data?.message ??
      "Não foi possível entrar. Tente novamente.";
    Toast.error(msg);
  } finally {
    loading.value = false;
  }
};

const clearFieldError = (field) => {
  if (formErrors.value[field]) {
    delete formErrors.value[field];
  }
};
</script>

<template>
  <div>
    <h2 class="text-xl font-semibold mt-0 mb-4 w-full text-center">Login</h2>

    <form class="flex flex-column gap-3" @submit.prevent="submit">
      <div class="flex flex-column gap-2">
        <label for="login-email" class="text-sm font-medium">E-mail</label>
        <InputText
          id="login-email"
          v-model="email"
          type="email"
          autocomplete="username"
          placeholder="ex: jose@email.com"
          class="w-full"
          :disabled="loading"
          @input="clearFieldError('email')"
        />
        <ValidationError :error="formErrors.email" />
      </div>

      <div class="flex flex-column gap-2">
        <label for="login-password" class="text-sm font-medium">Senha</label>
        <Password
          input-id="login-password"
          v-model="password"
          placeholder="******"
          :feedback="false"
          toggle-mask
          fluid
          input-class="w-full"
          :input-props="{ autocomplete: 'current-password' }"
          :disabled="loading"
          @input="clearFieldError('password')"
        />
        <ValidationError :error="formErrors.password" />
      </div>

      <Button type="submit" label="Entrar" class="w-full" :loading="loading" />
    </form>

    <p class="text-center text-sm text-color-secondary mt-4 mb-0">
      Não tem conta?
      <RouterLink
        :to="{ name: REGISTER_ROUTER }"
        class="text-primary no-underline font-medium"
      >
        Cadastro
      </RouterLink>
    </p>
  </div>
</template>
