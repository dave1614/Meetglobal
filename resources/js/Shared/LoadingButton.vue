<template>
  <button :disabled="loading" class="d-flex align-items-center">
    <slot />
    <div style="" v-if="loading" class="spinner-border spinner-border-sm ml-auto" />
    
  </button>
</template>

<script>
export default {
  props: {
    loading: Boolean,
  },
}
</script>
