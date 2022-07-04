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
      <div id="other-overlay" :style="show_other_overlay == true ? 'display: block;' : 'display: none;' "></div>
      <div class="">
        <div class="container-fluid">
          
          <div class="row justify-content-center">
            <div class="col-sm-6">
              
              <div class="card" id="main-card" v-if="user_info.transaction_password != ''">
                <div class="card-header">
                  <h3 class="card-title">Change Your Transaction Password</h3>
                </div>
                <div class="card-body">
                  
                  

                  <form id="change-transaction-password" @submit.prevent="submitChangeTransactionPasswordForm">  


                    <text-input v-model="change_transaction_password_form.old_password" :error="change_transaction_password_form.errors.old_password" type="text" label="Enter Old Transaction Password" id="old_password" placeholder=""/>

                    <text-input v-model="change_transaction_password_form.new_password" :error="change_transaction_password_form.errors.new_password" type="password" label="Enter New Transaction Password" id="new_password" placeholder=""/>


                    <text-input v-model="change_transaction_password_form.new_password_confirm" :error="change_transaction_password_form.errors.new_password_confirm" type="password" label="Confirm New Transaction Password" id="new_password_confirm" placeholder=""/>

                    <a href="#" @click="forgotTransactionPassword">Forgot Transaction Password?</a>       

                    <loading-button :loading="change_transaction_password_form.processing" class="btn btn-primary" type="submit">Submit</loading-button>
                  </form> 
                </div>
              </div>  
              

              <div class="card" id="main-card" v-else>
                <div class="card-header">
                  <h4 class="card-title">Click Button Below To Set Your Transaction Password</h4>
                </div>
                <div class="card-body">
                  <button style="margin-top: 40px;" class="btn btn-primary" @click="setTransactionPassword">Set Transaction Password</button>     
                </div>
              </div>
              
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" data-backdrop="static" id="set-transaction-password-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">Enter Your New Transaction Password</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body" id="modal-body">

              <form id="set-transaction-password-form" @submit.prevent="submitSetTransactionPasswordForm">  

                <text-input v-model="set_transaction_password_form.transaction_password" :error="set_transaction_password_form.errors.transaction_password" type="text" label="" id="transaction_password" placeholder=""/>

                <loading-button :loading="set_transaction_password_form.processing" class="btn btn-primary" type="submit">Submit</loading-button>
              </form> 
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" data-backdrop="static" id="enter-otp-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" v-html="enter_otp_modal_title"></h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body" id="modal-body">
              <form id="enter-otp-form" @submit.prevent="submitEnterOtpForm">  


                <text-input v-model="enter_otp_form.transfer_otp" :error="enter_otp_form.errors.transfer_otp" type="number" label="" id="old_password" placeholder="Enter OTP"/>


                <loading-button :loading="enter_otp_form.processing" class="btn btn-primary" type="submit">Submit</loading-button>
              </form> 
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" data-backdrop="static" id="forgot-transaction-password-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">Enter A New Transaction Password</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body" id="modal-body">
              

              <form id="forgot-transaction-password-form" @submit.prevent="submitForgotTransactionPasswordForm">  

                <text-input v-model="forgot_transaction_password_form.transaction_password" :error="forgot_transaction_password_form.errors.transaction_password" type="text" label="" id="transaction_password" placeholder=""/>

                <loading-button :loading="forgot_transaction_password_form.processing" class="btn btn-primary" type="submit">Submit</loading-button>
              </form> 
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" data-backdrop="static" id="enter-otp-modal-forgot-password" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">Enter OTP</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body" id="modal-body">
              

              <form id="enter-otp-form-forgot-password" @submit.prevent="submitEnterOtpFormForgotPasswordForm">  


                <text-input v-model="enter_otp_forgot_password_form.transfer_otp" :error="enter_otp_forgot_password_form.errors.transfer_otp" type="number" label="" id="transfer_otp" placeholder="Enter OTP"/>


                <loading-button :loading="change_transaction_password_form.processing" class="btn btn-primary" type="submit">Submit</loading-button>
              </form> 
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
      change_transaction_password_form: this.$inertia.form({
        old_password: "",
        new_password: "",
        new_password_confirm: ""

      }),
      get_users_email_request: this.$inertia.form({
        show_records: true

      }),
      set_transaction_password_form: this.$inertia.form({
        transaction_password: "",
        show_records: true
      }),
      enter_otp_form: this.$inertia.form({
        transfer_otp: "",
        transaction_password: ""
      }),
      forgot_transaction_password_form: this.$inertia.form({
        transaction_password: "",
        show_records: true
      }),
      enter_otp_forgot_password_form: this.$inertia.form({
        transfer_otp: "",
        transaction_password: ""
      }),
      enter_otp_modal_title: "",
      enter_otp_modal_forgot_password: "",
      show_other_overlay: false,
    }
  },
  
  mounted() {
    console.log(this.user_info.is_admin)
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    submitEnterOtpFormForgotPasswordForm() {

      var self = this;

      self.enter_otp_forgot_password_form.transaction_password = self.global_transaction_password;
      self.enter_otp_forgot_password_form.post(self.route('verify_forgot_transaction_password_otp'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)
          if(response.success){
            $.notify({
              message:"Transaction Password Changed Successfully. Secure It."
              },{
              type : "success",
              z_index: 20000, 
            });
            $("#enter-otp-modal-forgot-password").modal("hide");
            setTimeout(function () {
              self.$inertia.visit(self.route('change_transaction_password'));
            }, 2000);
          }else if(response.transaction_password_not_set){
            swal({
              title: 'Error!',
              text: "Transaction Password Is Already Set.",
              type: 'error',
              allowOutsideClick: false,
            });
            setTimeout(function () {
              self.$inertia.visit(self.route('change_transaction_password'));
            }, 3000)
          }else if(response.expired){
            swal({
              title: 'Ooops',
              text: "This OTP Has Expired. Please Request Another One.",
              type: 'error'
            });
            
          }else if(response.incomplete_detais){

            swal({
              title: 'Ooops',
              text: "Some Details Were Not Received For Processing. Please Try Again",
              type: 'error'
            });
            
          }else if(response.incorrect_otp){
            swal({
              title: 'Ooops',
              text: "This OTP Entered Is Incorrect. Please Enter The Valid One",
              type: 'error'
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
    submitForgotTransactionPasswordForm() {
      var self = this;
        

      self.global_transaction_password = self.forgot_transaction_password_form.transaction_password
      self.get_users_email_request.post(self.route('get_users_email_forgot_transaction_password'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)
          if(response.success && response.email != ""){
            var email = response.email;
            swal({
              title: 'Proceed?',
              text: "We Will Be Verifying Your Email <em class='text-primary'>" + email + "</em>. Are You Sure You Want To Proceed?",
              type: 'question',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes',
              cancelButtonText : "No"
            }).then(function(){
              self.forgot_transaction_password_form.post(self.route('send_forgot_transaction_password_otp'), {
                preserveScroll: true,
                onSuccess: (page) => {
                  
                  var response = JSON.parse(JSON.stringify(self.response_arr))
                  console.log(response)
                  if(response.success && response.email != ""){
                    var email = response.email;
                    self.show_other_overlay = true;
                    $("#forgot-transaction-password-modal").modal("hide");
                    setTimeout(function () {
                      self.show_other_overlay = false;
                      
                      self.enter_otp_modal_forgot_password = "An OTP Has Been Sent To <em class='text-primary'>"+email+"</em> <br> <small>Enter OTP Below</small>";
                      $("#enter-otp-modal-forgot-password").modal("show");
                    }, 2000);
                    
                  }else if(response.transaction_password_not_set){
                    swal({
                      title: 'Error!',
                      text: "Transaction Password Is Already Set.",
                      type: 'error',
                      allowOutsideClick: false,
                    });
                    setTimeout(function () {
                      self.$inertia.visit(self.route('change_transaction_password'));
                    }, 3000)
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
            });
          }else if(response.transaction_password_not_set){
            swal({
              title: 'Error!',
              text: "Transaction Password Is Already Set.",
              type: 'error',
              allowOutsideClick: false,
            });
            setTimeout(function () {
              self.$inertia.visit(self.route('change_transaction_password'));
            }, 3000)
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
    forgotTransactionPassword() {
      var self = this;

      self.get_users_email_request.post(self.route('get_users_email_forgot_transaction_password'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)
          if(response.success && response.email != ""){
            var email = response.email;
            $("#forgot-transaction-password-modal").modal("show");
          }else if(response.transaction_password_not_set){
            swal({
              title: 'Error!',
              text: "Transaction Password Is Already Set.",
              type: 'error',
              allowOutsideClick: false,
            });
            setTimeout(function () {
              self.$inertia.visit(self.route('change_transaction_password'));
            }, 3000)
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
    submitEnterOtpForm(){
      var self = this;
 
      self.enter_otp_form.transaction_password = self.global_transaction_password;
      self.enter_otp_form.post(self.route('verify_set_transaction_password_otp'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)
          if(response.success){
            $("#enter-otp-modal").modal("hide");
            $.notify({
              message:"Transaction Password Successful. Secure It."
              },{
              type : "success",
              z_index: 20000,   
            });
            setTimeout(function () {
              self.$inertia.visit(self.route('change_transaction_password'));
            }, 2000);
          }else if(response.transaction_password_already_set){
            swal({
              title: 'Ooops',
              text: "Transaction Password Is Already Set.",
              type: 'error'
            });
          }else if(response.expired){
            swal({
              title: 'Ooops',
              text: "This OTP Has Expired. Please Request Another One.",
              type: 'error'
            });
            
          }else if(response.incomplete_detais){

            swal({
              title: 'Ooops',
              text: "Some Details Were Not Received For Processing. Please Try Again",
              type: 'error'
            });
            
          }else if(response.incorrect_otp){
            swal({
              title: 'Ooops',
              text: "This OTP Entered Is Incorrect. Please Enter The Valid One",
              type: 'error'
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
    submitSetTransactionPasswordForm(){
      var self = this;
      self.global_transaction_password = self.set_transaction_password_form.transaction_password;
      self.get_users_email_request.post(self.route('get_users_email_set_transaction_password'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)
          if(response.success && response.email != ""){
            var email = response.email;
            swal({
              title: 'Proceed?',
              text: "We Will Be Verifying Your Email <em class='text-primary'>" + email + "</em>. Are You Sure You Want To Proceed?",
              type: 'question',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes',
              cancelButtonText : "No"
            }).then(function(){

              self.set_transaction_password_form.post(self.route('send_set_transaction_password_otp'), {
                preserveScroll: true,
                onSuccess: (page) => {
                  
                  var response = JSON.parse(JSON.stringify(self.response_arr))
                  console.log(response)
                  if(response.success && response.email != ""){
                    var email = response.email;
                    
                    self.show_other_overlay = true;
                    $("#set-transaction-password-modal").modal("hide");
                    setTimeout(function () {
                      self.show_other_overlay = false;
                      self.enter_otp_modal_title = "An OTP Has Been Sent To <em class='text-primary'>"+email+"</em> <br> <small>Enter OTP Below</small>";
                      $("#enter-otp-modal").modal("show");
                    }, 2000);
                    
                  }else if(response.transaction_password_already_set){
                    swal({
                      title: 'Error!',
                      text: "Transaction Password Is Already Set.",
                      type: 'error'
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
              
              
            });
          }else if(response.transaction_password_already_set){
            swal({
              title: 'Error!',
              text: "Transaction Password Is Already Set.",
              type: 'error'
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
    setTransactionPassword(){

      var self = this;

      self.get_users_email_request.post(self.route('get_users_email_set_transaction_password'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)
          if(response.success && response.email != ""){
            var email = response.email;
            $("#set-transaction-password-modal").modal("show");
          }else if(response.transaction_password_already_set){
            swal({
              title: 'Error!',
              text: "Transaction Password Is Already Set.",
              type: 'error',
              allowOutsideClick: false,
            });
            setTimeout(function () {
              self.$inertia.visit(self.route('change_transaction_password'));
            }, 3000)
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
    
    submitChangeTransactionPasswordForm() {
      var self = this;

      self.change_transaction_password_form.post(self.route('process_change_transaction_password'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            
            swal({
              title: 'Success',
              text: "Transaction Password Changed Successfully.",
              type: 'success',
              allowOutsideClick: false,
            }).then(function(){
              self.$inertia.visit(self.route('change_transaction_password'));
            });
          }else if(response.new_passwords_mismatch){
            swal({
              title: 'Ooops',
              text: "New Passwords Do Not Match.",
              type: 'error'
            });
          }else if(response.wrong_password == true){
            
            swal({
              title: 'Ooops',
              text: "Wrong Old Transaction Password Inputed. Please Try Again",
              type: 'error'
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
