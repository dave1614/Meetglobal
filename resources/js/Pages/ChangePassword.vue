<style>
  tr{
    cursor: pointer;
  }
  .frame-area {
     display: block;
    width: 100%;
    /* max-width: 400px; */
    height: 1000px;
    /* overflow: auto; */
    border: #999999 1px solid;
    margin: 0px;
    padding: 0px;
  }
</style>

<template>
    <div class="content">
      <div class="">
        <div class="container-fluid">
          
          <div class="row">
            <div class="col-sm-8">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Change Your Password</h3>
                </div>
                <div class="card-body">
                  <form id="change-password-from" @submit.prevent="submitChangePasswordForm">  

                    
                    <text-input v-model="change_password_form.old_password" :error="change_password_form.errors.old_password" type="password" label="Enter Old Password" id="old_password" placeholder=""/>

                    <text-input v-model="change_password_form.new_password" :error="change_password_form.errors.new_password" type="password" label="Enter New Password" id="new_password" placeholder=""/>

                    <loading-button :loading="change_password_form.processing" class="btn btn-primary" type="submit">Submit</loading-button>
                  </form>        
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <footer class="footer">
        <div class="container-fluid">
          
        </div>
      </footer>
    </div>
  
  <!--   Core JS Files   -->
 
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
import LoadingButton from '../Shared/LoadingButton'


export default {
  layout(h, page) {
    
    if(page.data.props.user_info.is_admin == 0){
      return h(Layout, [
        page,
        ]
      )
    }else{
      return h(AdminLayout, [
        page,
        ]
      )
    }
  },  
  metaInfo() {
    return {
      title: `${this.page_title}` 
    }
  },components: {
    Pagination,
    SearchFilter,
    FloatingActionButton,
    TextInput,
    LoadingButton,

  },
  // ...(this.user_info.is_admin == 0) && {layout: Layout},
  // layout: Layout,
  props: {
    response_arr: Object,
    previous_page: String,
    user_info: Object,
    upgrade_to_business: false,
    active_page: String,
    new_message_count: String,
    new_notifs_count: String,
    page_title: String,
    global_search_val: String,
    all_messages: Array,
    conversations_num: Number,
    noti_count: String,
    all_notifs: Array,

    


  },
  data() {
    return {
      change_password_form: this.$inertia.form({
        old_password: "",
        new_password: ""

      }),
      
      
    }
  },
  
  mounted() {
    console.log(this.user_info.is_admin)
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    submitChangePasswordForm() {
      var self = this;

      self.change_password_form.post(self.route('process_change_password'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            self.change_password_form.old_password = "";
            self.change_password_form.new_password = "";
            swal({
              title: 'Success',
              text: "Password Changed Successfully.",
              type: 'success'
            })
          }else if(response.wrong_password == true){
            swal({
              title: 'Ooops!',
              text: "Wrong Password Inputed. Please Try Again.",
              type: 'error'
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
