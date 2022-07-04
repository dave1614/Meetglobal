<template>
  

  <div class="form-group">
          
    <input  :id="id" :ref="id" v-bind="$attrs" class="inputfile inputfile-1" :class="{ error: error }" :type="type"  @input="$emit('input', $event.target.files[0]); if($event.target.files[0]){new_label = $event.target.files[0].name} else{ new_label = 'Choose File' }" />
    <label v-if="new_label" class="form-label col-12" :for="id"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/></svg> <span v-html="new_label + '&hellip;'"></span></label>
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
        return `file-input-${this._uid}`
      },
    },
    type: {
      type: String,
      default: 'file',
    },
    value: File,
    label: String,
    error: String,
  },
  data() {
    return {
      new_label: this.label,
      

    }
  },
  methods: {
    input(){

    },
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
