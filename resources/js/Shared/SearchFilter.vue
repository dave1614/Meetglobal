<template>
  <div class="flex items-center">
    <div class="flex w-full  form-group col-sm-4">
      
      <input class="relative w-full px-6 py-3 rounded-r focus:ring form-control" autocomplete="off" type="text" name="search" placeholder="Search…" :value="value" @input="$emit('input', $event.target.value)" />
    </div>
    <button class="btn btn-success ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500" type="button" @click="$emit('reset')">Reset</button>
  </div>
</template>

<script>


export default {
  components: {
    
  },
  props: {
    value: String,
    maxWidth: {
      type: Number,
      default: 300,
    },
  },
}
</script>
