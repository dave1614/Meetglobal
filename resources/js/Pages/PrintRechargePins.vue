<style>
  body{
    background: #fff;
  }
</style>

<template>
   <div class="">
     
   </div>
  
 
</template>

<script>

import Layout from '../Shared/Layout'
import AdminLayout from '../Shared/AdminLayout'
import Pagination from '../Shared/Pagination'
import SearchFilter from '../Shared/SearchFilter'
import FloatingActionButton from '../Shared/FloatingActionButton'
import mapValues from 'lodash/mapValues'
import throttle from 'lodash/throttle'
import pickBy from 'lodash/pickBy'
import TextInput from '../Shared/TextInput'
import TextinputGroup from '../Shared/TextinputGroup'
import SelectInput from '../Shared/SelectInput'
import LoadingButton from '../Shared/LoadingButton'


export default {
  
  metaInfo() {
    return {
      title: `${this.page_title}` 
    }
  },components: {
    Pagination,
    SearchFilter,
    FloatingActionButton,
    TextInput,
    SelectInput,
    LoadingButton,
    TextinputGroup,
  },
  // ...(this.user_info.is_admin == 0) && {layout: Layout},
  // layout: Layout,
  props: {
    page_title: String,

  },
  data() {
    return {
     

    }
  },
  
  mounted() {
    var self = this;
    setTimeout(function () {
      self.processPrintFunction()
    }, 3500);
    
  },
  created() {
    
  },
  methods: {
    processPrintFunction () {
      window.print()
    },
  },
}
</script>
