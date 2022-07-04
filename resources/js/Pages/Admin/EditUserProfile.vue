<style>
  tbody tr{
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
      <div id="other-overlay" :style="show_other_overlay == true ? 'display: block;' : 'display: none;' "></div>
      <div class="">
        <div class="container-fluid">
          
          <div class="row justify-content-center" style="">
            <div class="card" id="edit-user-profile-card">
                <div class="card-header">
                  
                  <h3 class="card-title" v-html="card_title"></h3>
                </div>
                <div class="card-body">
                  
                  <form id="edit-user-profile-form" @submit.prevent="submitEditUsersProfileForm">  
                    
                    
                    <text-input v-model="edit_user_profile_form.full_name" :error="edit_user_profile_form.errors.full_name" type="text" label="Full Name" id="full_name" placeholder=""/>

                    <text-input v-model="edit_user_profile_form.phone_number" :error="edit_user_profile_form.errors.phone_number" type="number" label="Phone Number" id="phone_number" placeholder=""/>

                    <text-input v-model="edit_user_profile_form.email_address" :error="edit_user_profile_form.errors.email_address" type="email" label="Email" id="email_address" placeholder=""/>
                    
                    <!-- <div class="form-group">
                      <label for="address">Address: </label>
                      <textarea name="address" id="address" class="form-control" cols="30" rows="10"></textarea>
                      <span class="form-error"></span>
                    </div> -->

                    <textarea-input v-model="edit_user_profile_form.address" :error="edit_user_profile_form.errors.address"  label="Address" id="address" placeholder=""/>

                    
                    <loading-button :loading="edit_user_profile_form.processing" class="btn btn-primary col-12" type="submit">Submit</loading-button>
                  </form>
                </div>
              </div>

          </div>
        </div>
      </div>


      <div @click="goBack">
        <floating-action-button :styles="'background: 9124a3;'" :title="'Go Back'">
          
          <i class="fas fa-arrow-left" style="font-size: 25px; color: #fff;"></i>
        </floating-action-button>
      </div>
     
      
      <footer class="footer">
        <div class="container-fluid">
          
        </div>
      </footer>
    </div>
  
  <!--   Core JS Files   -->
 
</template>

<script>

import Layout from '../../Shared/Layout'
import AdminLayout from '../../Shared/AdminLayout'
import Pagination from '../../Shared/Pagination'
import SearchFilter from '../../Shared/SearchFilter'
import FloatingActionButton from '../../Shared/FloatingActionButton'
import mapValues from 'lodash/mapValues'
import throttle from 'lodash/throttle'
import pickBy from 'lodash/pickBy'
import TextInput from '../../Shared/TextInput'
import TextareaInput from '../../Shared/TextareaInput'
import SelectInput from '../../Shared/SelectInput'
import LoadingButton from '../../Shared/LoadingButton'


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
    TextareaInput,
    SelectInput,
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


    users_arr: Object,
    
    

    

  },
  data() {
    return {
      

      edit_user_profile_form: this.$inertia.form({
        full_name: this.users_arr.full_name,
        phone_number: this.users_arr.phone,
        email_address: this.users_arr.email,
        address: this.users_arr.address,
        user_id: this.users_arr.id
      }),

      card_title: "Edit <em class='text-primary'>" + this.users_arr.user_name + "'s</em> Profile",
      
      show_other_overlay: false,

    }
  },
  
  mounted() {
    console.log(this.users_arr)
    console.log(this.$page.props)
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    submitEditUsersProfileForm() {
      var self = this;
      self.edit_user_profile_form.post(self.route('process_edit_users_profile'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            $.notify({
              message:"Profile Edited Successfully"
            },{
              type : "success",
              z_index: 20000,    
            });
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
    goBack(){
      // this.$inertia.visit(this.route('view_members_list'));
      window.history.back();
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
