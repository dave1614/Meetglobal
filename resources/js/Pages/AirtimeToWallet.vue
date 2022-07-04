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
            <inertia-link class="btn btn-primary" :href="route('user_vtu_history_page') + '?length=10&type=airtime_to_wallet&isDirty=true&__rememberable=true'"><i style="font-size:17px;" class="fas fa-history"></i>&nbsp;&nbsp;&nbsp;&nbsp;View Airtime To Wallet History
            </inertia-link>

            <a href="https://wa.me/2348036302232?text=I Have A Problem With My Airtime To Wallet Recharge&" class="btn btn-success" target="_blank" ><i style="font-size:17px;" class="fab fa-whatsapp"></i></i>&nbsp;&nbsp;&nbsp;&nbsp;Support</a>
          </div>
          <div class="row justify-content-center" style="">
            <div class="card" id="main-card">
              <div class="card-body">
                <h4 class="subhead">Select Network</h4>

                <div class="container">
                  <div class="row">

                    <div :class="airtime_to_wallet_request.network == 'mtn' ? 'col-2 col-sm-1 card network-card selected' : 'col-2 col-sm-1 card network-card'" @click="selectNetwork('mtn')">
                      <div class="card-body text-center">
                        <img src="/images/mtn_logo.png" alt="MTN" class="col-12">
                        <!-- <p>MTN</p> -->
                      </div>

                    </div>

                    <div class="col-1">
                      
                    </div>

                    <div :class="airtime_to_wallet_request.network == 'glo' ? 'col-2 col-sm-1 card network-card selected' : 'col-2 col-sm-1 card network-card'" @click="selectNetwork('glo')">
                      <div class="card-body text-center">
                        <img src="/images/glo_logo.jpg" alt="GLO" class="col-12">
                        <!-- <p>MTN</p> -->
                      </div>
                    </div>

                    <div class="col-1">
                      
                    </div>

                    <div :class="airtime_to_wallet_request.network == 'airtel' ? 'col-2 col-sm-1 card network-card selected' : 'col-2 col-sm-1 card network-card'" @click="selectNetwork('airtel')">
                      <div class="card-body text-center">
                        <img src="/images/airtel_logo.png" alt="Airtel" class="col-12">
                        <!-- <p>MTN</p> -->
                      </div>
                    </div>



                    <div class="col-1">
                      
                    </div>

                    <div :class="airtime_to_wallet_request.network == '9mobile' ? 'col-2 col-sm-1 card network-card selected' : 'col-2 col-sm-1 card network-card'" @click="selectNetwork('9mobile')">
                      <div class="card-body text-center">
                        <img src="/images/9mobile-1.png" alt="9mobile" class="col-12">
                        <!-- <p>MTN</p> -->
                      </div>
                    </div>

                  </div>
                  <div class="row">
                    <div v-if="airtime_to_wallet_request.errors.network" class="form-error">{{ airtime_to_wallet_request.errors.network }}</div>
                  </div>
                </div>

                

                <h4 class="subhead">Enter Amount </h4>
                

                <textinput-group v-model="airtime_to_wallet_request.amount" :error="airtime_to_wallet_request.errors.amount" type="number" icon="₦" id="amount" placeholder="1,000-20,000" class="col-12"/>

                <h4 class="subhead">Enter Phone Number</h4>

                <text-input  v-model="airtime_to_wallet_request.phone_number" :error="airtime_to_wallet_request.errors.phone_number" type="number" id="phone_number" placeholder="e.g 08127027321" class="col-12"/>

                
              

                <button @click="validateBuyAirtimeRequest" :disabled="airtime_to_wallet_request.processing" class="d-flex align-items-center btn btn-primary col-12">
                  Continue
                  <div style="" v-if="airtime_to_wallet_request.processing" class="spinner-border spinner-border-sm ml-auto" />
                  
                </button>
              </div>
            </div>
           

          </div>
        </div>
      </div>

     
      <div class="modal fade" data-backdrop="static" id="preview-transaction-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content" >
            <div class="modal-header text-center">
              <h3 class="modal-title">Preview Transaction</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body" id="modal-body">
              <div class="text-center">
                <p>Kindly confirm that the details you entered are valid before clicking the "Confirm" button.</p>
                
              </div>
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <td>NETWORK</td>
                    <td><em v-html="airtime_to_wallet_request.network" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>PHONE NUMBER</td>
                    <td><em v-html="airtime_to_wallet_request.phone_number" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>AMOUNT</td>
                    <td><em v-html="'₦ ' +addCommas(airtime_to_wallet_request.amount)" class="text-primary"></em></td>
                  </tr>
                  
                  <tr>
                    <td>CHARGE</td>
                    <td><em v-html="airtime_to_wallet_request.perc_charge + '%'" class="text-primary"></em></td>
                  </tr>
                  
                  <tr>
                    <td>CREDITED AMOUNT</td>
                    <td><em v-html="'₦ ' +addCommas(airtime_to_wallet_request.credited_amount)" class="text-primary"></em></td>
                  </tr>

                  <tr>
                    <td>TRANSFER CODE</td>
                    <td><em v-html="airtime_to_wallet_request.transfer_code" class="text-primary"></em></td>
                  </tr>

                  <tr>
                    <td>INSTRUCTIONS</td>
                    <td><em v-html="airtime_to_wallet_request.instruction" class="text-primary"></em></td>
                  </tr>
                </tbody>
              </table>

              <div class="justify-content-center text-center">
                <button class="btn btn-primary" @click="confirmAndProceedWithTransaction">Confirm</button>
                <br>
                <p class="text-danger" style="cursor: pointer;" @click="cancelTransaction">Cancel and return</p>
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
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
      
      airtime_to_wallet_request: this.$inertia.form({
        network: null,
        phone_number: null,
        amount: null,
        credited_amount: null,
        perc_charge: null,
        transfer_code: null,
        instruction: "",
        from: null,
        
      }),

      get_charge_request: this.$inertia.form({
        network: null,
        
        
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
    confirmAndProceedWithTransaction() {
      var self = this;
      var network = self.airtime_to_wallet_request.network;
      var phone_number = self.airtime_to_wallet_request.phone_number;
      var amount = self.airtime_to_wallet_request.amount;
      var credited_amount = self.airtime_to_wallet_request.credited_amount;
      var perc_charge = self.airtime_to_wallet_request.perc_charge;
      var transfer_code = self.airtime_to_wallet_request.transfer_code;
      var instruction = self.airtime_to_wallet_request.instruction;


      console.log(network)
      console.log(phone_number)
      console.log(amount)
      console.log(credited_amount)
      console.log(perc_charge)
      console.log(transfer_code)
      console.log(instruction)

      $("#preview-transaction-modal").modal("hide");
      self.get_charge_request.post(self.route('get_charge_for_airtime_to_wallet_transfer'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success  && response.charge != "" && response.phone_number != "" && response.network != "" && response.transfer_code != "" && response.instruction != ""){
            var charge = response.charge;
            var phone_number = response.phone_number;
            

            self.airtime_to_wallet_request.from = phone_number;
            self.show_other_overlay = true
            self.airtime_to_wallet_request.post(self.route('process_airtime_to_wallet_transfer'), {
              preserveScroll: true,
              onSuccess: (page) => {
                self.show_other_overlay = false
                var response = JSON.parse(JSON.stringify(self.response_arr))
                console.log(response)

                if(response.success && response.order_id !== ""){
                  var order_id = response.order_id;
                  swal({
                    title: 'Info',
                    text: "You Request Has Been Sent Successfully. For <em class='text-primary'>" + phone_number + "</em> And Credit Worth ₦" + self.addCommas(amount) + " On <span style='text-transform: capitalize;'>" + network + "</span> Network. You Would Be Automatically Credited When You Send This Code In This Format From The Requested Number <em class='text-primary'>" + transfer_code  + "</em>. Note: <em class='text-primary'>"+instruction+". Amount Sent Must Match Account Requested.</em> The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>",
                    type: 'info',
                    confirmButtonColor: '#3085d6',
                    allowOutsideClick: false,
                  }).then(function(){
                    self.$inertia.visit(self.route('airtime_to_wallet_page'));
                  });
                }else if(response.invalid_amount){
                  swal({
                    title: 'Ooops',
                    text: "Invalid Amount Was Entered. Your Money Has Been Refunded",
                    type: 'error'
                  })
                }else if(response.invalid_recipient){
                  swal({
                    title: 'Ooops',
                    text: "Invalid Mobile Number Was Entered. Your Money Has Been Refunded",
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
                self.show_other_overlay = false
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
    },
    selectNetwork(network){
      var self = this;
      
      if(self.airtime_to_wallet_request.network != network){
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
                self.airtime_to_wallet_request.network = network;
                self.airtime_to_wallet_request.perc_charge = charge;
                self.airtime_to_wallet_request.transfer_code = transfer_code;
                self.airtime_to_wallet_request.instruction = instruction;
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
      var network = self.airtime_to_wallet_request.network;
      var phone_number = self.airtime_to_wallet_request.phone_number;
      var amount = self.airtime_to_wallet_request.amount;
      var credited_amount = self.airtime_to_wallet_request.credited_amount;
      var perc_charge = self.airtime_to_wallet_request.perc_charge;

      console.log(network)
      console.log(phone_number)
      console.log(amount)
      console.log(credited_amount)
      console.log(perc_charge)
      if(network != null){
        self.airtime_to_wallet_request.post(self.route('validate_airtime_to_wallet_details'), {
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response)

            if(response.success){
            
              credited_amount = (perc_charge / 100) * amount;
              credited_amount = amount - credited_amount;
              self.airtime_to_wallet_request.credited_amount = credited_amount;
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
