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
  #bank-details-form,#enter-amount-to-withdraw-form, #withdrawal-otp-form{
    transition: display 1s;
  }
</style>

<template>
    <div class="content">
      <div id="other-overlay" :style="show_other_overlay == true ? 'display: block;' : 'display: none;' "></div>
      <div class="">
        <div class="container-fluid">
          <div class="text-right">
            <inertia-link as="button" class="btn btn-primary" :href="route('funds_withdrawal_history')"><i style="font-size: 13px;" class="fas fa-history"></i>&nbsp;&nbsp;Funds Withdrawal History</inertia-link>
            
          </div>
          <div class="row justify-content-center" style="">
            
            <div class="col-sm-12">

             
              <div class="card" id="main-card">
                <div class="card-header">
                  <h3 class="card-title">Wallet Balance: <em class="text-primary" v-html="'₦' + balance_str"></em></h3>
                  <span class="text-primary">Note: Withdrawal Comes With Charge Of ₦100 </span>
                </div>
                <div class="card-body">
                  <h4>Click Button Below To Withdraw Funds</h4>

                  <button class="btn btn-primary" style="margin-top: 40px;" @click="withdrawFunds">Withdraw Funds</button>

                </div>
              </div>

            </div>
          </div>
        </div>
      </div>


      <div class="modal fade" data-backdrop="static" id="withdraw-funds-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title" v-html="withdraw_funds_modal_title">Withdraw From Your Wallet</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body" id="modal-body" v-if="banks_arr.status && banks_arr.message == 'Banks retrieved'">
              <form id="bank-details-form" @submit.prevent="submitBankDetailsForm" v-if="banks_arr.data.length > 0" :style="show_bank_details_form ? '' : 'display:none;'">

                <select-input id="bank_name" v-model="bank_details_form.bank_name" :error="bank_details_form.errors.bank_name" class="col-sm-12" label="Select Bank Name">
                  <option v-for="row in banks_arr.data" :key="row.id" :value="row.code" v-html="row.name"></option>
                </select-input>


                <text-input v-model="bank_details_form.account_number" :error="bank_details_form.errors.amount" type="number" label="Enter Account Number"  id="account_number" placeholder="" class="col-sm-12"/>

                <loading-button :loading="bank_details_form.processing" class="btn btn-primary col-12" type="submit">Submit</loading-button>

              </form>

                
              <form id="enter-amount-to-withdraw-form" @submit.prevent="submitAmountToWithdrawForm" :style="show_enter_amount_to_withdraw_form ? '' : 'display:none;'">
                

                <text-input v-model="enter_amount_to_withdraw_form.amount" :error="enter_amount_to_withdraw_form.errors.amount" type="number" step="any" label="Enter Amount" id="amount" placeholder="" class="col-sm-12"/>
                <loading-button :loading="enter_amount_to_withdraw_form.processing" class="btn btn-primary col-12" type="submit">Submit</loading-button>
              </form>

              

              <form id="withdrawal-otp-form" @submit.prevent="submitWithdrawalOtpForm" :style="show_withdrawal_otp_form ? '' : 'display:none;'">

                <text-input v-model="withdrawal_otp_form.otp" :error="withdrawal_otp_form.errors.otp" type="text" id="otp" placeholder="Enter Transaction Password....."/>
                <loading-button :loading="withdrawal_otp_form.processing" class="btn btn-primary col-12" type="submit">Submit</loading-button>
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
    banks_arr: Object,

  },
  data() {
    return {
      get_forms_for_funds_withdrawal_request: this.$inertia.form({
        show_records: true,
        
      }),

      bank_details_form: this.$inertia.form({
        bank_name: this.user_info.bank_name,
        account_number: this.user_info.account_number,
        
      }),

      enter_amount_to_withdraw_form: this.$inertia.form({
        amount: "",
        
        
      }),

      withdrawal_otp_form: this.$inertia.form({
        otp: "",        
        amount: ""
      }),
      
      show_other_overlay: false,

      show_bank_details_form: true,
      show_enter_amount_to_withdraw_form: false,
      show_withdrawal_otp_form: false,
      withdraw_funds_modal_title: "",
      account_name: ""

    }},

   
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
    console.log(this.banks_arr.data)
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    submitWithdrawalOtpForm (elem,evt) {
      var self = this;
      
      self.withdrawal_otp_form.amount = self.enter_amount_to_withdraw_form.amount;
      var amount_to_withdraw = self.withdrawal_otp_form.amount;


      self.withdrawal_otp_form.post(self.route('validate_withdrawal_otp'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            $("#withdraw-funds-modal").modal("hide");
            swal({
              title: 'Success!',
              // text: "You Have Successfully Transfered <em class='text-primary'>₦" + addCommas(amount_to_withdraw) + "</em> From Your Meetglobal Account To Bank Account With Name <em class='text-primary'>" + account_name + "</em>.",
              text: "Your Request To Transfer <em class='text-primary'>₦" + self.addCommas(amount_to_withdraw) + "</em> From Your Meetglobal Account To Bank Account With Name <em class='text-primary'>" + self.account_name + "</em> Has Been Sent To The Admin For Approval.",
              type: 'success'
            }).then(function(){
              
              self.$inertia.visit(self.route('funds_withdrawal'))
            });

           
          }else if(response.incomplete_detais){
            swal({
              title: 'Error',
              text: "Some Details Were Not Received By The Server. Please Try Again.",
              type: 'error',                              
            })
          }else if(response.incorrect_otp){
            swal({
              title: 'Error',
              text: "This Transaction Password Entered Is Incorrect. Please Enter The Valid One.",
              type: 'error',                              
            })
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
    
    submitAmountToWithdrawForm() {
      var self = this;
      var amount = self.enter_amount_to_withdraw_form.amount;
    

      self.enter_amount_to_withdraw_form.post(self.route('enter_amount_withdraw_funds'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success == true && response.code != "" && response.phone_number != ""){
            var code = "+" + response.code;
            var phone_number = response.phone_number;
            swal({
              title: 'Proceed With Withdrawal?',
              text: "Are You Sure You Want To Transfer <em class='text-primary'>₦ " + self.addCommas(amount) + "</em> From Your Meetglobal Account To Bank Account With Account Name <em class='text-primary'>" + self.account_name + "</em>?",
              type: 'success',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes',
              cancelButtonText : "No"
            }).then(function(){
              var amount_to_withdraw = amount;
              
              // self.show_other_overlay = true
              self.enter_amount_to_withdraw_form.post(self.route('send_withdrwal_otp'), {
                preserveScroll: true,
                onSuccess: (page) => {
                  // self.show_other_overlay = false
                  var response = JSON.parse(JSON.stringify(self.response_arr))
                  console.log(response)

                  if(response.success == true && response.email != ""){
                    
                    var email = response.email;
                    self.withdraw_funds_modal_title = "Enter Your Transaction Password";
                    self.show_bank_details_form = false;
                    self.show_enter_amount_to_withdraw_form = false;
                    self.show_withdrawal_otp_form = true;

                    
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
            }, function(dismiss){
               if(dismiss == 'cancel'){
                  // function when cancel button is clicked
                  console.log('cancelled');
               }
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
    submitBankDetailsForm () {
      
      var self = this;
      self.bank_details_form.post(self.route('withdraw_funds_cont'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success == true && response.account_name !== ""){
            self.account_name = response.account_name;
            swal({
              title: 'Proceed With Withdrawal?',
              text: "Is This Your Account Name <span class='text-primary' style='font-style: italic;'>" + response.account_name + "</span>?",
              type: 'success',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes',
              cancelButtonText : "No"
            }).then(function(){
              
              
              self.show_bank_details_form = false;
              self.show_enter_amount_to_withdraw_form = true;
              self.show_withdrawal_otp_form = false;
              self.withdraw_funds_modal_title = "Enter Withdrawal Amount To Proceed"
            }, function(dismiss){
               if(dismiss == 'cancel'){
                   // function when cancel button is clicked
                   console.log('cancelled');
               }
            });    
          }else if(response.invalid_account == true){
            swal({
              title: 'Invalid Account Details',
              text: "Sorry These Account Details Are Not Linked To Any Account",
              type: 'error',
              confirmButtonColor: '#3085d6',                    
              confirmButtonText: 'Ok'                   
            });
          }else if(response.bouyant == false){
             swal({
              title: 'Insuffecient Balance',
              text: "Sorry You Do Not Have Enough Funds In Your Account To Complete This Transaction",
              type: 'error',
              confirmButtonColor: '#3085d6',                    
              confirmButtonText: 'Ok'                   
            });
          }else if(response.no_refer == true){
             swal({
              title: 'No Referrals',
              text: "You Need To Sponsor At Least One Ambassador Or One Great Ambassador To Be Eligible For Your First Withdrawal. Each Sponsorship Earns You ₦400 Commission",
              type: 'error',
              confirmButtonColor: '#3085d6',                    
              confirmButtonText: 'Ok'                   
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
    },
    withdrawFunds(elem) {
      
      var self = this;
      self.show_bank_details_form = true;
      self.show_enter_amount_to_withdraw_form = false;
      self.show_withdrawal_otp_form = false;
      self.get_forms_for_funds_withdrawal_request.post(self.route('get_forms_for_funds_withdrawal'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            self.bank_details_form.bank_name = response.code;
            self.bank_details_form.account_number = response.account_number;
            self.withdraw_funds_modal_title = "Enter Account Details";
            $("#withdraw-funds-modal").modal("show");
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
    },
    addCommas(nStr)
    {
        nStr += '';
        var x = nStr.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }
  },
}
</script>
