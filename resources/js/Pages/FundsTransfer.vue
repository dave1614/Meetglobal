<style>
  tbody tr{
    /*cursor: pointer;*/
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
          <div class="text-right">
            <inertia-link as="button" class="btn btn-primary" :href="route('funds_transfer_history')"><i style="font-size: 13px;" class="fas fa-history"></i>&nbsp;&nbsp;Funds Transfer History</inertia-link>
            
          </div>
          <div class="row justify-content-center" style="">
            
            <div class="col-sm-12">

             
              <div class="card" id="main-card">
                <div class="card-header">
                  
                  <h3 class="card-title">Wallet Balance: <em class="text-primary" v-html="'₦' + balance_str"></em></h3>
                </div>
                <div class="card-body">
                  <h4>Transfer To Other Users</h4>

                  <button class="btn btn-primary" style="margin-top: 40px;" @click="transferFunds">Transfer Funds</button>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

     
      <div class="modal fade" data-backdrop="static" id="transfer-funds-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">Transfer Funds</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body" id="modal-body">
              
              <form id="transfer-funds-form" @submit.prevent="submitTransferFundsForm">
                
                <text-input v-model="transfer_funds_form.amount" :error="transfer_funds_form.errors.amount" step="any" type="number" id="amount" placeholder="Enter Amount To Transfer" min="200"/>
                
                <text-input v-model="transfer_funds_form.recepient_username" :error="transfer_funds_form.errors.recepient_username" type="text" id="recepient_username" placeholder="Recepient Username"/>

                <loading-button :loading="transfer_funds_form.processing" class="btn btn-primary col-12" type="submit">Submit</loading-button>

                
              </form>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="reloadPage(this)">Close</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" data-backdrop="static" id="enter-transfer-otp-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" v-html="enter_transfer_otp_modal_title"></h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body" id="modal-body">
              
              <form id="enter-transfer-otp-form" @submit.prevent="submitEnterTransferOtpForm">

                <text-input v-model="enter_transfer_otp_form.transaction_password" :error="enter_transfer_otp_form.errors.transaction_password" type="text" id="transaction_password" placeholder="Enter Transaction Password"/>

                <loading-button :loading="enter_transfer_otp_form.processing" class="btn btn-primary col-12" type="submit">Submit</loading-button>
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
import FileInput from '../Shared/FileInput'
import SelectInput from '../Shared/SelectInput'
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
    SelectInput,
    LoadingButton,
    FileInput,

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

    total_income: [String,Number],
    withdrawn: [String,Number],
    balance: [String,Number],
    balance_str: [String,Number],
    providus_account_number: [String,Number],
    providus_account_name: [String,Number],
    transaction_password_inputed: Boolean,

  },
  data() {
    return {
      transfer_funds_form: this.$inertia.form({
        amount: "",
        recepient_username: "",
        
      }),
      get_users_email_request: this.$inertia.form({
        show_records: true,
        
        
      }),
      send_transfer_otp_request: this.$inertia.form({
        show_records: true,
        
      }),
      enter_transfer_otp_form: this.$inertia.form({
        show_records: true,
        transaction_password: "",
        amount: "",
        recepient_id: ""
      }),
      show_other_overlay: false,

      recepient_id: '',
      enter_transfer_otp_modal_title: '',
      transfer_amount: '',
      transfer_phone_code: '',
      transfer_phone_number: '',

    }
  },
 
  mounted() {
    var self = this;
    if(!this.transaction_password_inputed){
      swal({
        title: 'Info',
        text: "We Have Noticed That You Do Not Have A Transaction Password Currently Set. Click Ok To Enter One.",
        type: 'info',
        allowOutsideClick: false
      }).then(function(){
        self.$inertia.visit(self.route('change_transaction_password'));
      });
    }
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    submitEnterTransferOtpForm(){
      var self = this;
      self.enter_transfer_otp_form.amount = self.transfer_amount
      self.enter_transfer_otp_form.recepient_id = self.recepient_id

         
      self.enter_transfer_otp_form.post(self.route('verify_transfer_otp'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            self.show_other_overlay = true;
            $.notify({
              message:"You Have Successfully Transfered " + addCommas(self.transfer_amount) + "."
              },{
              type : "success",
              z_index: 20000,  
            });
            $("#enter-transfer-otp-modal").modal("hide");
            setTimeout(function () {
              self.show_other_overlay = false;
              self.$inertia.visit(self.route('funds_transfer'))
            }, 1500);
          }else if(response.incomplete_detais){
            
            swal({
              title: 'Error',
              text: "Some Details Were Not Received For Processing. Please Try Again.",
              type: 'error',                              
            })
          }else if(response.amount_not_numeric){
            
            swal({
              title: 'Error',
              text: "Amount Entered Must Be A Number.",
              type: 'error',                              
            })
          }else if(response.amount_too_small){
            
            swal({
              title: 'Error',
              text: "Amount Must Be At Least 200.",
              type: 'error',                              
            })
          }else if(response.not_bouyant){
            
            swal({
              title: 'Error',
              text: "You Currently Do Not Have Sufficient Funds To Complete This Transfer.",
              type: 'error',                              
            })
          }else if(response.invalid_recipient){
           
            swal({
              title: 'Error',
              text: "Invalid Recipient Selected.",
              type: 'error',                              
            })
          }else if(response.incorrect_otp){
            swal({
              title: 'Error',
              text: "This Transaction Password Entered Is Incorrect. Please Enter The Valid One.",
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
      });

    },
    submitTransferFundsForm() {
      var self = this;
      
      if(self.transfer_funds_form.amount != ""){

        self.transfer_funds_form.post(self.route('transfer_funds_to_user'), {
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response)

            if(response.success == true && response.phone_number != "" && response.code != "" && response.recepient_fullname != "" && response.users_id != ""){
              var messages = response.messages;
              var recepient_fullname = response.recepient_fullname;
              var users_id = response.users_id;

              self.transfer_amount = self.transfer_funds_form.amount;
              self.transfer_phone_code = "+" + response.code;
              self.transfer_phone_number = response.phone_number;

              swal({
                title: 'Warning',
                text: "Is This Your Recepient's Full Name <br><em class='text-primary'>" + recepient_fullname + "</em> ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes Proceed!',
                cancelButtonText : "No Cancel"
              }).then(function(){

                swal({
                  title: 'Proceed With Transfer?',
                  text: "Are You Sure You Want To Transfer <em class='text-primary'>₦"+addCommas(self.transfer_amount)+"</em> To <em class='text-primary'>" + recepient_fullname + "</em> ?",
                  type: 'success',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes',
                  cancelButtonText : "No"
                }).then(function(){
                  
                  self.recepient_id = users_id;
                  self.show_other_overlay = true;
                  self.get_users_email_request.post(self.route('get_users_email'), {
                    preserveScroll: true,
                    onSuccess: (page) => {
                      
                      var response = JSON.parse(JSON.stringify(self.response_arr))
                      console.log(response)
                      self.show_other_overlay = false;
                      if(response.success && response.email != ""){
                        var email = response.email;
                        self.show_other_overlay = true;
                        self.send_transfer_otp_request.post(self.route('send_transfer_otp'), {
                          preserveScroll: true,
                          onSuccess: (page) => {
                            self.show_other_overlay = false;
                            var response = JSON.parse(JSON.stringify(self.response_arr))
                            console.log(response)

                            if(response.success && response.email != ""){
                              var email = response.email;
                              self.show_other_overlay = true;
                              $("#transfer-funds-modal").modal("hide");
                              setTimeout(function () {
                                
                                self.show_other_overlay = false;
                                
                                self.enter_transfer_otp_modal_title = "Enter Your Transaction Password";
                                $("#enter-transfer-otp-modal").modal("show");
                              }, 2000);
                              
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
              });

              
            }else if(response.not_bouyant){
              swal({
                title: 'Error!',
                text: "Sorry You Do Not Have Enough Funds For The Amount You Want To Transfer. Credit Your Account And Try Again",
                type: 'error'
              });
            }else if(response.too_small){
              swal({
                title: 'Error!',
                text: "Minimum Withdrawable Amount Is ₦200",
                type: 'error'
              });
            }else if(response.recepient_does_not_exist){
              swal({
                title: 'Ooops!',
                text: "Sorry This User Does Not Exist",
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
        });
      }else{
         swal({
            title: 'Error!',
            text: "Minimum Withdrawable Amount Is ₦200",
            type: 'error'
          });
      }
    },
    transferFunds() {
      $("#transfer-funds-modal").modal("show");
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
