<template>
  <div class="form-group">
    <label v-if="label" class="form-label" :for="id">{{ label }}:</label>
    <textarea :id="id" ref="input" v-bind="$attrs" class="form-input form-control" :class="{ error: error }" :type="type" :value="value" @input="$emit('input', $event.target.value)" cols="30" rows="10"></textarea>
    <div v-if="error" class="form-error">{{ error }}</div>
  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `text-input-${this._uid}`
      },
    },
    type: {
      type: String,
      default: 'text',
    },
    value: String,
    label: String,
    error: String,
  },
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
    setSelectionRange(start, end) {
      this.$refs.input.setSelectionRange(start, end)
    },
  },
}
</script>
