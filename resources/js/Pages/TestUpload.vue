<style>
  
</style>

<template>
    <div class="container">
      <form @submit.prevent="submit">
        <!-- <input type="text" v-model="form.name" /> -->
        <!-- <input type="file" @input="form.image = $event.target.files[0]" /> -->
        <!-- <text-input v-model="form.image" :error="form.errors.image" type="file" label="Summary" id="summary" placeholder="" class="col-sm-4"/> -->
        <div class="form-group">
          <!-- <label class="form-label" for="image">Select Image:</label>
          <input id="image"  class="form-input form-control" type="file" @input="form.image = $event.target.files[0]" />
          <div v-if="form.errors.image" class="form-error">{{ form.errors.image }}</div> -->

          <input @input="form.image = $event.target.files[0]" type="file" name="image" id="image" class="inputfile inputfile-1" accept="image/*" ref="image"/>
          <label for="image"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/></svg> <span>Select Image&hellip;</span></label>
          <div v-if="form.errors.image" class="form-error">{{ form.errors.image }}</div>
        </div>


        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
          {{ form.progress.percentage }}%
        </progress>
        <loading-button :loading="form.processing" class="btn btn-primary" type="submit">Submit</loading-button>
      </form>
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
import SelectInput from '../Shared/SelectInput'
import LoadingButton from '../Shared/LoadingButton'


export default {
  
  metaInfo() {
    return {
      title: `Test Image Upload` 
    }
  },components: {
    Pagination,
    SearchFilter,
    FloatingActionButton,
    TextInput,
    SelectInput,
    LoadingButton,

  },
  
  props: {
    response_arr: Object,
  },
  data() {
    return {
      form: this.$inertia.form({
        
        image: null,
      }),

    }
  },
  
  mounted() {
    
  },
  created() {
    
    
  },
  methods: {
    submit() {
      var self = this;
      
      self.form.post(self.route('process_test_upload'), {
        preserveScroll: true,
        onSuccess: (page) => {
          console.log(self.response_arr)
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            $.notify({
              message:"Image Uploaded Successfully. <br>Image Name " + response.image_name
            },{
              type : "success",
              z_index: 20000,    
            });
          }else if(response.image_empty){
            swal({
              title: 'Ooops',
              text: "You Must Select An Image To Upload As Oppurtunity Image",
              type: 'error',                              
            })
          }else if(!response.image_only_one_image){
            swal({
              title: 'Ooops',
              text: "You Can Only Select One Image To Upload As Oppurtunity Image",
              type: 'error',                              
            })
          }else if(response.image_errors != ""){
            
            swal({
              title: 'Oppurtunity Image Error',
              html: response.image_errors,
              type: 'error',
              
            })
          }else{

            $.notify({
            message:"Something Went Wrong. Please Try Again"
            },{
              type : "warning",
              z_index: 20000,  
            });
          }
        },onError: (errors) => {
          
          var errors = JSON.parse(JSON.stringify(errors))
          var errors_num = Object.keys(errors).length;
          
          if(errors_num > 0){
            $.notify({
              message: errors_num + " Field(s) Have Error(s). Please Correct Them."
            },{
              type : "warning",
              z_index: 20000,
            });
          }
        },
      })
    },
    

    copyText(text) {
      /* Get the text field */
      var elem = document.createElement("textarea");
      elem.value = text;
      document.body.append(elem);

      /* Select the text field */
      elem.select();
      /* Copy the text inside the text field */
      if(document.execCommand("copy")){
        $.notify({
          message:"Copied To Clipboard"
        },{
          type : "success",
          z_index: 20000,
        });
      }

      document.body.removeChild(elem);

      /* Alert the copied text */
    }
  },
}
</script>
