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
  .subhead{
    font-weight: bold;
    font-size: 16px;
    margin-top: 15px;
  }

  .network-card{
    cursor: pointer;
    transition: border 0.05s;
  }

  .network-card, .network-card .card-body, .network-card img{
    padding: 0;
  }

  .network-card.selected{
    padding: 5px;
    border: 2px solid #9c27b0;
  }

  .network-card img{
    height: 100%;
  }

  .amount-card{
    cursor: pointer;
    transition: border 0.01s;
  }

  .amount-card span{
    font-size: 14px;
    font-weight: bold;
  }

  .amount-card.selected{
    /*padding: 5px;*/
    border: 2px solid #9c27b0;
  }

  @media screen and (min-width: 574px) {
    .amount-card{
      margin-right: 10px;
    }
  }
</style>

<template>
    <div class="content">
      <div id="other-overlay" :style="show_other_overlay == true ? 'display: block;' : 'display: none;' "></div>
      <div class="">
        <div class="container-fluid">
          <div class="text-right">
            <inertia-link class="btn btn-primary" :href="route('user_vtu_history_page') + '?length=10&type=bulk&isDirty=true&__rememberable=true'"><i style="font-size:17px;" class="fas fa-history"></i>&nbsp;&nbsp;&nbsp;&nbsp;View Bulk Sms History
            </inertia-link>

            <a href="https://wa.me/2348036302232?text=I Have A Problem With My Bulk SMS Recharge&" class="btn btn-success" target="_blank" ><i style="font-size:17px;" class="fab fa-whatsapp"></i></i>&nbsp;&nbsp;&nbsp;&nbsp;Support</a>
          </div>
          <div class="row justify-content-center" style="">
            <div class="card" id="main-card">
              <div class="card-body">
                <form id="bulk-sms-form" @submit.prevent="submitBulkSmsForm">  
                  <p class="text-primary">Note: Each Message Cost ₦3.00</p>
                  <h4 class="subhead">Recepients Numbers </h4>
                  
                  <textarea-input v-model="bulk_sms_form.recepients" :error="bulk_sms_form.errors.recepients" id="recepients" placeholder="Numbers Must Be Comma Seperated e.g 08127027321,08126372981,09072638291. Maximum 200 Recepients." class="col-12"/>

                  <h4 class="subhead">Enter Message </h4>
                  

                  <textarea-input v-model="bulk_sms_form.message" :error="bulk_sms_form.errors.message" id="message" placeholder="Message Must Not Exceed 140 Characters" class="col-12"/>
                  
                

                  <button :disabled="bulk_sms_form.processing" class="d-flex align-items-center btn btn-primary col-12">
                    Submit
                    <div style="" v-if="bulk_sms_form.processing" class="spinner-border spinner-border-sm ml-auto" />
                    
                  </button>
                </form>
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
import TextareaInput from '../Shared/TextareaInput'
import TextinputGroup from '../Shared/TextinputGroup'
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
    TextinputGroup,
    TextareaInput,
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

    csrf: String,

    

  },
  data() {
    return {
      
      bulk_sms_form: this.$inertia.form({
        recepients: "",
        message: "",
        
      }),

     
      
      
      show_other_overlay: false,
      
    }
  },
  
  mounted() {
    
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    submitBulkSmsForm() {
      var self = this;
      var recepients = self.bulk_sms_form.recepients;
      var message = self.bulk_sms_form.message;
      


      console.log(recepients)
      console.log(message)
      var recepients_arr = recepients.split(",");
      var recepients_num = recepients_arr.length;

      if(recepients_num <= 200){
        var message_cost = 3 * recepients_num;
        var amount_to_debit_user = message_cost;

        swal({
          title: 'Info',
          text: "You Are About To Send This Message To <em class='text-primary'>"+ recepients_num +"</em> Number(s). This Costs ₦" + self.addCommas(message_cost) + " At ₦3 Per SMS. You Will Be Debited <em class='text-primary'>₦"+amount_to_debit_user+"</em>. Do You Want To Proceed?" ,
          type: 'info',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes Proceed!',
          cancelButtonText : "No"
        }).then(function(){

          self.bulk_sms_form.post(self.route('send_bulk_sms'), {
            preserveScroll: true,
            onSuccess: (page) => {
              
              var response = JSON.parse(JSON.stringify(self.response_arr))
              console.log(response)
          
              if(response.success && response.order_id != ""){
                var order_id = response.order_id;


                
                var transaction_pending = response.transaction_pending;

                var text = "You SMS Has Been Sent Successfully. You Have Been Debited <em class='text-primary'>₦"+ self.addCommas(amount_to_debit_user) +"</em>. The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>";

                if(transaction_pending){
                  text += " Note: This Order Is Currently Pending. You Have Been Debited. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page";

                }
                
                swal({
                  title: 'Info',
                  text: text,
                  type: 'info',
                  confirmButtonColor: '#3085d6',
                  allowOutsideClick: false,
                }).then(function(){
                  self.$inertia.visit(self.route('bulk_sms_page'));
                });
              }else if(response.recepients_exceeded){
                swal({
                  title: 'Ooops',
                  text: "You Cannot Send SMS To More Than 200 Recepients",
                  type: 'error'
                })
              }else if(response.insuffecient_funds){
                swal({
                  title: 'Ooops',
                  text: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
                  type: 'error'
                })
              }else{
                swal({
                  title: 'Ooops',
                  text: "Something Went Wrong. Please Try Again",
                  type: 'error'
                })
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

        });

      }else{
        swal({
          title: 'Ooops',
          text: "You Cannot Send SMS To More Than 200 Recepients",
          type: 'error'
        })
      }

          
    },
    selectNetwork(network){
      var self = this;
      
      if(self.bulk_sms_form.network != network){
        self.get_charge_request.network = network;
        self.get_charge_request.post(self.route('get_charge_for_airtime_to_wallet_transfer'), {
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response)

            if(response.success  && response.charge != "" && response.phone_number != "" && response.network != "" && response.transfer_code != "" && response.instruction != ""){
              var charge = response.charge;
              var phone_number = response.phone_number;
              var network_str = response.network;
              var transfer_code = response.transfer_code;
              var instruction = response.instruction;
              
              swal({
                title: 'Info',
                text: "Charge For  " + network_str + " Network" +" Is  <em class='text-primary'>"+ charge + "%</em>. Proceed?" ,
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes Proceed!',
                cancelButtonText : "No"
              }).then(function(){
                self.bulk_sms_form.network = network;
                self.bulk_sms_form.perc_charge = charge;
                self.bulk_sms_form.transfer_code = transfer_code;
                self.bulk_sms_form.instruction = instruction;
              });
            }else{
              swal({
                title: 'Ooops',
                text: "Something Went Wrong. Please Try Again",
                type: 'error'
              })
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
      }
        
    },
    validateBuyAirtimeRequest() {
      var self = this;
      var network = self.bulk_sms_form.network;
      var phone_number = self.bulk_sms_form.phone_number;
      var amount = self.bulk_sms_form.amount;
      var credited_amount = self.bulk_sms_form.credited_amount;
      var perc_charge = self.bulk_sms_form.perc_charge;

      console.log(network)
      console.log(phone_number)
      console.log(amount)
      console.log(credited_amount)
      console.log(perc_charge)
      if(network != null){
        self.bulk_sms_form.post(self.route('validate_airtime_to_wallet_details'), {
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response)

            if(response.success){
            
              credited_amount = (perc_charge / 100) * amount;
              credited_amount = amount - credited_amount;
              self.bulk_sms_form.credited_amount = credited_amount;
              $("#preview-transaction-modal").modal("show");
            }else{
              swal({
                title: 'Ooops',
                text: "Something Went Wrong. Please Try Again",
                type: 'error'
              })
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
          title: 'Error',
          text: "Please Select Network To Proceed",
          type: 'error'
        })
      }
    },
    
    
    cancelTransaction(){
      $("#preview-transaction-modal").modal("hide");
    },
    
    isNumber(value) 
    {
       return typeof value === 'number' && isFinite(value);
    },
    addCommas(nStr){
      nStr += '';
      var x = nStr.split('.');
      var x1 = x[0];
      var x2 = x.length > 1 ? '.' + x[1] : '';
      var rgx = /(\d+)(\d{3})/;
      while (rgx.test(x1)) {
          x1 = x1.replace(rgx, '$1' + ',' + '$2');
      }
      return x1 + x2;
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
