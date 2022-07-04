<template>
  <div class="container" style="margin-top:20px;">
    <!-- <layout> -->
      <div class="row justify-content-center">
        <div class="col-sm-7" >
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Edit {{user_info.name}}'s Profile</h3>
            </div>
            <!-- <trashed-message v-if="user_info.deleted_at" class="mb-6" @restore="restore">
              This contact has been deleted.
            </trashed-message> -->

            <div class="card-body">
              <form @submit.prevent="update">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                  <text-input v-model="form.name" :error="form.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Full name" />
                  
                  <text-input v-model="form.email" :error="form.errors.email" class="pr-6 pb-8 w-full lg:w-1/2" label="Email" />
                  
                </div>
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
                  <button v-if="!user_info.deleted_at" class="text-red-600 btn btn-danger hover:underline" tabindex="-1" type="button" @click="destroy" >Delete Contact</button>
                  <loading-button :loading="form.processing" class="btn-indigo ml-auto btn btn-primary" type="submit">Update Contact</loading-button>
                </div>
              </form>
            </div>
          </div>
          
          
        </div>
        
      </div>
    <!-- </layout> -->
  </div>
</template>

<script>

import Layout from '../Shared/Layout'
import TextInput from '../Shared/TextInput'
import LoadingButton from '../Shared/LoadingButton'



export default {
  
   metaInfo() {
    return {
      title: `Edit User: ${this.form.name}` 
    }
  },
  components: {
    TextInput,
    LoadingButton,
  },
  layout: Layout,
  props: {
    user_info: Object,
    response_arr: Object
  },
  data() {
    return {
      form: this.$inertia.form({
        name: this.user_info.name,
        email: this.user_info.email,
        id: this.user_info.email,
       
      }),
    }
  },
  mounted() {
    console.log(this.user_info.name)
  },
  created() {
    
  },
  methods: {
    update() {
      this.form.put(this.route('update_user', this.user_info.id), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response_arr = JSON.parse(JSON.stringify(this.response_arr))
          console.log(response_arr)
          
          if(response_arr.success){
            $.notify({
              message:"User Edited Successfully"
            },{
              type : "success"  
            });
          }else{
             $.notify({
              message:"Something Went Wrong"
            },{
              type : "warning"  
            });
          }
        },onError: (errors) => {
          
          var errors = JSON.parse(JSON.stringify(errors))
          var errors_num = Object.keys(errors).length;
          
          if(errors_num > 0){
            $.notify({
              message: errors_num + " Field(s) Have Errors. Please Correct Them."
            },{
              type : "warning"  
            });
          }
        },
      })
    },
    destroy() {
      var self = this;
      swal({
        title: 'Warning',
        text: "Are you sure you want to delete this contact?" ,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes Proceed!',
        cancelButtonText : "No"
      }).then(function(){
        self.$inertia.delete(self.route('delete_user', self.user_info.id),{
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response_arr = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response_arr)
            
            if(response_arr.success){
              $.notify({
                message:"User Deleted Successfully"
              },{
                type : "success"  
              });
              

              
              self.$inertia.visit(self.route('home'))
            }else if(response_arr.invalid_id){
              swal({
                title: 'Ooops',
                text: "This User Is Invalid" ,
                type: 'error',
              });
            }else{
               $.notify({
                message:"Something Went Wrong"
              },{
                type : "warning"  
              });
            }
          },onError: (errors) => {
            
            var errors = JSON.parse(JSON.stringify(errors))
            var errors_num = Object.keys(errors).length;
            
            if(errors_num > 0){
              $.notify({
                message: errors_num + " Field(s) Have Error(s). Please Correct Them."
              },{
                type : "warning"  
              });
            }
          },
        })
      });
      
    },
  },
}
</script>
