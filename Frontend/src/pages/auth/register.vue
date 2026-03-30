<script setup>
import { ref } from "vue";
import { RouterLink, useRouter } from "vue-router";
import InputText from "primevue/inputtext";
import Password from "primevue/password";
import Button from "primevue/button";

import ValidationError from "@/components/ValidationError.vue";
import ApiService, { REGISTER_ENDPOINT } from "@/services/ApiService.js";
import ToastService from "@/services/ToastService.js";
import { validateRegister } from "@/services/ValidationService.js";
import { LOGIN_ROUTER } from "@/utils/constants.js";

const Toast = ToastService();
const router = useRouter();

const name = ref("");
const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const loading = ref(false);
const formErrors = ref({});

const submit = async () => {
  const errors = validateRegister({
    name: name.value,
    email: email.value,
    password: password.value,
    password_confirmation: confirmPassword.value,
  });

  if (Object.keys(errors).length > 0) {
    formErrors.value = errors;
    return;
  }

  formErrors.value = {};

  loading.value = true;
  try {
    await ApiService.post(REGISTER_ENDPOINT, {
      name: name.value.trim(),
      email: email.value.trim(),
      password: password.value,
      password_confirmation: confirmPassword.value,
    });

    Toast.success("Conta criada com sucesso! Redirecionando para login...");
    setTimeout(() => {
      router.push({ name: LOGIN_ROUTER });
    }, 1500);
  } catch (error) {
    const msg =
      error.response?.data?.message ??
      "Não foi possível criar a conta. Tente novamente.";
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
    <h2 class="text-xl font-semibold mt-0 mb-4 w-full text-center">Cadastrar</h2>

    <form class="flex flex-column gap-3" @submit.prevent="submit">
      <div class="flex flex-column gap-2">
        <label for="register-name" class="text-sm font-medium">Nome</label>
        <InputText
          id="register-name"
          v-model="name"
          type="text"
          autocomplete="name"
          class="w-full"
          :disabled="loading"
          placeholder="Seu nome completo"
          @input="clearFieldError('name')"
        />
        <ValidationError :error="formErrors.name" />
      </div>

      <div class="flex flex-column gap-2">
        <label for="register-email" class="text-sm font-medium">E-mail</label>
        <InputText
          id="register-email"
          v-model="email"
          type="email"
          autocomplete="username"
          class="w-full"
          :disabled="loading"
          placeholder="seu@email.com"
          @input="clearFieldError('email')"
        />
        <ValidationError :error="formErrors.email" />
      </div>

      <div class="flex flex-column gap-2">
        <label for="register-password" class="text-sm font-medium"
          >Senha</label
        >
        <Password
          input-id="register-password"
          v-model="password"
          :feedback="false"
          toggle-mask
          fluid
          input-class="w-full"
          :input-props="{ autocomplete: 'new-password' }"
          :disabled="loading"
          placeholder="Mínimo 6 caracteres"
          @input="clearFieldError('password')"
        />
        <ValidationError :error="formErrors.password" />
      </div>

      <div class="flex flex-column gap-2">
        <label for="register-confirm-password" class="text-sm font-medium"
          >Confirmar Senha</label
        >
        <Password
          input-id="register-confirm-password"
          v-model="confirmPassword"
          :feedback="false"
          toggle-mask
          fluid
          input-class="w-full"
          :input-props="{ autocomplete: 'new-password' }"
          :disabled="loading"
          placeholder="Confirme sua senha"
          @input="clearFieldError('password_confirmation')"
        />
        <ValidationError :error="formErrors.password_confirmation" />
      </div>

      <Button
        type="submit"
        label="Cadastrar"
        class="w-full"
        :loading="loading"
      />
    </form>

    <p class="text-center text-sm text-color-secondary mt-4 mb-0">
      Já tem conta?
      <RouterLink
        :to="{ name: LOGIN_ROUTER }"
        class="text-primary no-underline font-medium"
      >
        Entrar
      </RouterLink>
    </p>
  </div>
</template>
