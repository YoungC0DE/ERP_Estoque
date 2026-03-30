<script setup>
import { computed } from "vue";

const props = defineProps({
  name: {
    type: String,
    required: true,
  },
  size: {
    type: String,
    default: "md", // 'sm', 'md', 'lg'
  },
});

const firstLetter = computed(() => {
  return props.name?.charAt(0).toUpperCase() || "?";
});

const sizeClasses = computed(() => {
  const sizes = {
    sm: "text-xs",
    md: "text-sm",
    lg: "text-lg",
  };
  return sizes[props.size] || sizes.md;
});

// Gera cor consistente baseada no nome
const backgroundColor = computed(() => {
  const colors = [
    "bg-blue-500",
    "bg-purple-500",
    "bg-pink-500",
    "bg-red-500",
    "bg-orange-500",
    "bg-yellow-500",
    "bg-green-500",
    "bg-teal-500",
    "bg-indigo-500",
    "bg-cyan-500",
  ];
  
  let hash = 0;
  for (let i = 0; i < props.name.length; i++) {
    hash = props.name.charCodeAt(i) + ((hash << 5) - hash);
  }
  
  const index = Math.abs(hash) % colors.length;
  return colors[index];
});
</script>

<template>
  <div
    :class="[
      'user-avatar',
      sizeClasses,
      backgroundColor,
      'rounded-full flex items-center justify-center font-semibold text-white shadow-md cursor-pointer transition-transform',
    ]"
    :title="name"
  >
    {{ firstLetter }}
  </div>
</template>

<style scoped>
.user-avatar {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  user-select: none;
  border-radius: 100%;
  width: 35px;
  height: 35px;
}
</style>
