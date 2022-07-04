<template>
  <div class="container" style="margin-top:20px;">
    <!-- <layout> -->
      <div class="row justify-content-center">
        <div class="col-sm-10" >
          <div class="card">
            <h2 class="text-center">Home Page</h2>
            <inertia-link :href="route('home')" method="get" as="button" type="button" class="btn btn-primary">Go Home</inertia-link>
            <p>{{info}}</p>
          </div>
          
          
        </div>
        
      </div>
    <!-- </layout> -->
  </div>
</template>

<script>

import Layout from '../Shared/Layout'



export default {
  metaInfo: { title: 'Page 2' },
  components: {
    
  },
  layout: Layout,
  props: {
    info: String,
    
  },
  data() {
    return {
      
    }
  },
  mounted() {
    // console.log(this.users)
  },
  created() {
    
  },
  methods: {
    
  },
}
</script>
