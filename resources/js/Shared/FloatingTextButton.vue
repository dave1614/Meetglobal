<template>
  <div class="ftb"  :title="title" :style="styles">
    <div class="" style="">
      
      <slot />
    </div>
  </div>
</template>

<script>


export default {
  components: {
    
  },
  props: {
    styles: String,
    title: String,
  },
}
</script>