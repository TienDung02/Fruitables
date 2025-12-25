<template>
  <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded shadow text-center">
      <h1 class="text-3xl font-bold text-red-600 mb-4">Lỗi {{ status }}</h1>
      <p class="text-lg text-gray-700 mb-4">{{ message }}</p>
      <button @click="reload" class="px-4 py-2 bg-blue-600 text-white rounded">Tải lại trang</button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  status: {
    type: Number,
    default: 500
  },
  message: {
    type: String,
    default: 'Đã xảy ra lỗi không xác định.'
  }
});

function reload() {
  window.location.reload();
}
</script>

