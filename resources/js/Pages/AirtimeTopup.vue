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
            <inertia-link class="btn btn-primary" :href="route('user_vtu_history_page') + '?length=10&type=airtime&isDirty=true&__rememberable=true'"><i style="font-size:17px;" class="fas fa-history"></i>&nbsp;&nbsp;&nbsp;&nbsp;View Airtime History
            </inertia-link>

            <a href="https://wa.me/2348036302232?text=I Have A Problem With My Airtime Recharge&" class="btn btn-success" target="_blank" ><i style="font-size:17px;" class="fab fa-whatsapp"></i></i>&nbsp;&nbsp;&nbsp;&nbsp;Support</a>
          </div>
          
          <div class="row justify-content-center" style="">
            <div class="card" id="main-card" v-if="!epins_received">
              <div class="card-body">
                <h4 class="subhead">Select Network</h4>

                <div class="container">
                  <div class="row">

                    <div :class="buy_airtime_request.network == 'mtn' ? 'col-2 col-sm-1 card network-card selected' : 'col-2 col-sm-1 card network-card'" @click="selectNetwork('mtn')">
                      <div class="card-body text-center">
                        <img src="/images/mtn_logo.png" alt="MTN" class="col-12">
                        <!-- <p>MTN</p> -->
                      </div>

                    </div>

                    <div class="col-1">
                      
                    </div>

                    <div :class="buy_airtime_request.network == 'glo' ? 'col-2 col-sm-1 card network-card selected' : 'col-2 col-sm-1 card network-card'" @click="selectNetwork('glo')">
                      <div class="card-body text-center">
                        <img src="/images/glo_logo.jpg" alt="GLO" class="col-12">
                        <!-- <p>MTN</p> -->
                      </div>
                    </div>

                    <div class="col-1">
                      
                    </div>

                    <div :class="buy_airtime_request.network == 'airtel' ? 'col-2 col-sm-1 card network-card selected' : 'col-2 col-sm-1 card network-card'" @click="selectNetwork('airtel')">
                      <div class="card-body text-center">
                        <img src="/images/airtel_logo.png" alt="Airtel" class="col-12">
                        <!-- <p>MTN</p> -->
                      </div>
                    </div>



                    <div class="col-1">
                      
                    </div>

                    <div :class="buy_airtime_request.network == '9mobile' ? 'col-2 col-sm-1 card network-card selected' : 'col-2 col-sm-1 card network-card'" @click="selectNetwork('9mobile')">
                      <div class="card-body text-center">
                        <img src="/images/9mobile-1.png" alt="9mobile" class="col-12">
                        <!-- <p>MTN</p> -->
                      </div>
                    </div>

                  </div>
                  <div class="row">
                    <div v-if="buy_airtime_request.errors.network" class="form-error">{{ buy_airtime_request.errors.network }}</div>
                  </div>
                </div>

                <h4 class="subhead">Choose An Amount</h4>

                <div class="container-fluid">
                  <div class="row">

                    <div v-for="amount in amounts" :class="buy_airtime_request.selected_amount == amount ? 'col-6 col-sm-4 col-md-2 card amount-card selected' : 'col-6 col-sm-4 col-md-2 card amount-card'" @click="selectAmount(amount)">
                      <div class="card-body text-center">
                        <span v-html="'NGN ' + amount"></span>
                      </div>
                    </div>

                    
                    
                  </div>
                </div>

                <h4 class="subhead" v-if="!epin_checkbox_checked">Or Enter Amount Directly</h4>
                

                <textinput-group :hide="epin_checkbox_checked" v-model="buy_airtime_request.entered_amount" :error="buy_airtime_request.errors.amount" type="number" icon="₦" id="entered_amount" placeholder="100-50,000" class="col-12"/>

                <h4 class="subhead" v-if="!epin_checkbox_checked">Enter Phone Number</h4>

                <text-input v-if="!epin_checkbox_checked" v-model="buy_airtime_request.phone_number" :error="buy_airtime_request.errors.phone_number" type="number" id="phone_number" placeholder="e.g 08127027321" class="col-12"/>

                
                <div v-if="!epin_checkbox_checked && buy_airtime_request.network == 'mtn'" id="mtn-airtime-bonus-check-div" class="form-check form-check-inline" style="margin-top: 15px">
                  <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" v-model="buy_airtime_request.airtime_bonus" > MTN Awuf (400% airtime bonus)
                    <span class="form-check-sign">
                        <span class="check"></span>
                    </span>
                  </label>
                </div>

                <div v-if="!epin_checkbox_checked && buy_airtime_request.network == 'glo'" id="glo-airtime-bonus-check-div" class="form-check form-check-inline" style="margin-top: 15px">
                  <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" v-model="buy_airtime_request.airtime_bonus" > Glo 5X (500% airtime bonus)
                    <span class="form-check-sign">
                        <span class="check"></span>
                    </span>
                  </label>
                </div>

                <!-- <h4 class="subhead" v-if="epin_checkbox_checked">Enter Quantity(Units)</h4>

                <text-input v-if="epin_checkbox_checked" v-model="buy_airtime_request.quantity" :error="buy_airtime_request.errors.quantity" type="number" id="quantity" min="1" max="20" placeholder="e.g 1" class="col-12"/> -->

                <div id="generate-epin-check-div" class="form-check form-check-inline" style="margin-top: 15px">
                  <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" v-model="epin_checkbox_checked" @change="epinCheckBoxChanged"> Generate E-PIN
                    <span class="form-check-sign">
                        <span class="check"></span>
                    </span>
                  </label>
                </div>
                <p id="epin-cashback" v-if="epin_checkbox_checked"><em class="text-primary">{{cashback_text}}</em></p>
              

                <button @click="validateBuyAirtimeRequest" :disabled="buy_airtime_request.processing" class="d-flex align-items-center btn btn-primary col-12">
                  Continue
                  <div style="" v-if="buy_airtime_request.processing" class="spinner-border spinner-border-sm ml-auto" />
                  
                </button>
              </div>
            </div>
            <div class="card" id="epin-card" v-else>
              <div class="card-header">
                <h3 class="card-title" v-html="epin_card_title" style="text-transform: capitalize;"></h3>
              </div>
              <div class="card-body">
                <h4>Details: </h4>
                <h4>Amount: <em class='text-primary' v-html="'₦' + addCommas(epins_amount)"></em> <br>Quantity: <em class='text-primary' v-html='epins_quantity'></em> Unit(s)</h4>
            
                
                <div class='container'>
                  <!-- <button @click="printRechargePins"  style='margin-bottom: 30px;'  class='btn btn-success' >Print E-Pins</button> -->
                  <div class='row' style='margin-bottom: 5px;'>
                    
                    <h4 class='col-12' v-html="pin"></h4>
                    
                  </div>
                </div>

              </div>
            </div>
            <!-- <div class="card" id="epin-card" v-else>
              <div class="card-header">
                <h3 class="card-title" v-html="epin_card_title" style="text-transform: capitalize;"></h3>
              </div>
              <div class="card-body">
                <h4>Details: </h4>
                <h4>Amount: <em class='text-primary' v-html="'₦' + addCommas(epins_amount)"></em> <br>Quantity: <em class='text-primary' v-html='epins_quantity'></em> Unit(s)</h4>
            
                
                <div class='container'>
                  <button @click="printRechargePins"  style='margin-bottom: 30px;'  class='btn btn-success' >Print E-Pins</button>
                  <div class='row' style='margin-bottom: 5px;' v-for="epin in epins" :key="epin.index">
                    <p class='col-1' v-html="epin.index + '.'"></p>
                    <p class='col-5' v-html="epin.pin"></p>
                    <p class='col-6' v-html="epin.code"></p>
                  </div>
                </div>

              </div>
            </div> -->

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
                  <tr v-if="buy_airtime_request.recharge_type == 'epin'">
                    <td>QUANTITY</td>
                    <td><em v-html="buy_airtime_request.quantity + ' Unit(s)'" class="text-primary">{buy_airtime_request.quantity}</em></td>
                  </tr>
                  <tr>
                    <td>AMOUNT</td>
                    <td><em v-html="'₦ ' +buy_airtime_request.amount" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>PHONE</td>
                    <td><em v-html="buy_airtime_request.phone_number" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>NETWORK</td>
                    <td><em  class="text-primary">{{buy_airtime_request.network}}</em></td>
                  </tr>
                  <tr>
                    <td>RECHARGE TYPE</td>
                    <td><em class="text-primary">{{buy_airtime_request.recharge_type}}</em></td>
                  </tr>
                  <tr>
                    <td>PAYABLE</td>
                    <td><em v-html="'₦ ' +buy_airtime_request.payable" class="text-primary"></em></td>
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

      <form action="/print_recharge_pins" method="post" target="_blank" id="print-recharge-pins-form">
          <input type="hidden" name="_token" :value="csrf">
         <input type="hidden" id="epins" name="epins" value="">
         <input type="hidden" id="amount" name="amount" value="">
      </form>
      
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
      
      buy_airtime_request: this.$inertia.form({
        network: "mtn",
        selected_amount: 100,
        entered_amount: "100",
        phone_number: null,
        amount: null,
        recharge_type: "normal",
        quantity: 1,
        payable: null,
        epin_check: false,
        airtime_bonus: false,
      }),

      amounts: [100,200,500,1000,2000,5000,10000],
      
      show_other_overlay: false,
      epin_checkbox_checked: false,
      epins_received: false,
      epin_card_title: '',
      epins: [],
      epins_json: '',
      epins_amount: '',
      epins_quantity: '',
      pin: '',
      cashback_text: 'Note: 2% Cashback On All Transactions',
    }
  },
  
  mounted() {
    console.log(this.amounts)
    console.log(this.$page.props)
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    printRechargePins(){
      var self = this;
      var epins = self.epins_json;
      var amount = self.epins_amount;

      if(epins != "" && amount != ""){

        $("#print-recharge-pins-form #epins").val(epins);
        $("#print-recharge-pins-form #amount").val(amount);
        $("#print-recharge-pins-form").submit();
        
      }
      
    },
    confirmAndProceedWithTransaction() {
      var self = this;
      var network = self.buy_airtime_request.network;
      var selected_amount = self.buy_airtime_request.selected_amount;
      var entered_amount = self.buy_airtime_request.entered_amount;
      var phone_number = self.buy_airtime_request.phone_number;
      var amount = self.buy_airtime_request.amount;
      var recharge_type = self.buy_airtime_request.recharge_type;

      console.log(amount)
      console.log(network)
      console.log(selected_amount)
      console.log(entered_amount)
      console.log(phone_number)
      console.log(recharge_type)

      $("#preview-transaction-modal").modal("hide");
      if(network == "9mobile" && recharge_type == "combo"){
        self.proceedWithComboRequest();
      }else if(recharge_type == "normal"){
        self.proceedWithNormalAirtimeRequest();
      }else if(recharge_type == "epin" && (network == "mtn" || network == "airtel" || network == "glo" || network == "9mobile")){
        self.proceedWithEpinAirtimeRequest();
      }
    },
    proceedWithEpinAirtimeRequest(){
      var self = this;
      var network = self.buy_airtime_request.network;
      var selected_amount = self.buy_airtime_request.selected_amount;
      var entered_amount = self.buy_airtime_request.entered_amount;
      var phone_number = self.buy_airtime_request.phone_number;
      var amount = self.buy_airtime_request.amount;
      var recharge_type = self.buy_airtime_request.recharge_type;
      var quantity = self.buy_airtime_request.quantity;

      
      self.buy_airtime_request.post(self.route('generate_vtu_epin'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

         // if(response.success && Array.isArray(response.epins) && response.amount != "" && response.epins_json != "") {
          if(response.success && response.amount != "" && response.pin != "") {
            
            
            self.epins_amount = response.amount;
            self.epins_quantity = quantity;
            self.pin = response.pin;
            
            self.epins_received = true;
            self.epin_card_title = "E-pin For " + network + " Network";
          }else if(response.no_available_epin){
            swal({
              title: 'Ooops',
              text: "No Available E-PIN For The Parameters You Selected Currently. Please Try Again Later.",
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
    },
    proceedWithNormalAirtimeRequest() {
      var self = this;
      var network = self.buy_airtime_request.network;
      var selected_amount = self.buy_airtime_request.selected_amount;
      var entered_amount = self.buy_airtime_request.entered_amount;
      var phone_number = self.buy_airtime_request.phone_number;
      var amount = self.buy_airtime_request.amount;
      var recharge_type = self.buy_airtime_request.recharge_type;

      
      self.buy_airtime_request.post(self.route('normal_airtime_recharge_request'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success && response.order_id !== ""){
            var order_id = response.order_id;
            swal({
              title: 'Info',
              text: "You Have Successfully Credited <em class='text-primary'>" + phone_number + "</em> With Airtime Worth ₦" + self.addCommas(amount) + " On <span style='text-transform: capitalize;'>" + network + "</span> Network. Note You Have Been Debited Of ₦" + self.addCommas(amount)  + ". The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>",
              type: 'info',
              confirmButtonColor: '#3085d6',
              allowOutsideClick: false,
            }).then(function(){
              self.$inertia.visit(self.route('airtime_page'));
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
    proceedWithComboRequest() {
      var self = this;
      var network = self.buy_airtime_request.network;
      var selected_amount = self.buy_airtime_request.selected_amount;
      var entered_amount = self.buy_airtime_request.entered_amount;
      var phone_number = self.buy_airtime_request.phone_number;
      var amount = self.buy_airtime_request.amount;
      var recharge_type = self.buy_airtime_request.recharge_type;

      self.buy_airtime_request.post(self.route('request_9mobile_combo_recharge'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            swal({
              title: 'Info',
              text: "You Have Successfully Credited <em class='text-primary'>" + phone_number + "</em> With Airtime Worth ₦" + self.addCommas(amount) + " On <span style='text-transform: capitalize;'>" + network + "</span> Network. Note You Have Been Debited Of ₦" + self.addCommas(amount)  + ".",
              type: 'info',
              confirmButtonColor: '#3085d6',
              allowOutsideClick: false,
            }).then(function(){
              self.$inertia.visit(self.route('airtime_page'));
            });
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
    },
    epinCheckBoxChanged () {
      var self = this;
      
      console.log(self.epin_checkbox_checked)
      var network = self.buy_airtime_request.network;
      if(self.epin_checkbox_checked){
        self.amounts = [100,200,500];
        self.buy_airtime_request = this.$inertia.form({
          network: self.buy_airtime_request.network,
          selected_amount: 100,
          entered_amount: "100",
          phone_number: null,
          amount: null,
          recharge_type: "normal",
          quantity: 1,
          payable: null,
          epin_check: false,
          airtime_bonus: false,
        })
        // if(network == "glo" || network == "9mobile"){
        //   self.show_other_overlay = true;
        //   setTimeout(function () {
        //     self.show_other_overlay = false;
        //     self.epin_checkbox_checked = false;
        //     swal({
        //       title: 'Error',
        //       text: "Sorry E-PIN Recharge Is Not Available For The Selected Network. Only For MTN And Airtel",
        //       type: 'error'
        //     })
        //   },100);
          
        // }
        if(network == "mtn"){
          self.cashback_text = "Note: 1% Cashback On All Transactions"
        }else{
          self.cashback_text = "Note: 2% Cashback On All Transactions"
        }
      }else{
        self.amounts = [100,200,500,1000,2000,5000,10000];
      }
    },
    cancelTransaction(){
      $("#preview-transaction-modal").modal("hide");
    },
    proceedToSubmitBuyAirtimeRequest() {
      var self = this;
      var network = self.buy_airtime_request.network;
      var selected_amount = self.buy_airtime_request.selected_amount;
      var entered_amount = self.buy_airtime_request.entered_amount;
      var phone_number = self.buy_airtime_request.phone_number;
      var amount = self.buy_airtime_request.amount;

      
      if(network == "9mobile" && amount >= 1000){
        swal({
          title: 'Choose Option',
          text: "Choose Recharge Option: ",
          type: 'info',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#4caf50',
          confirmButtonText: 'Normal Recharge',
          cancelButtonText : "Combo Recharge"
        }).then(function(){
          
          $("#preview-transaction-modal").modal("show")
        },function(dismiss){
          if(dismiss == 'cancel'){
            self.buy_airtime_request.recharge_type = "combo";
            $("#preview-transaction-modal").modal("show")
          }
        });
      }else{
        $("#preview-transaction-modal").modal("show")
      }
    },
    validateBuyAirtimeRequest() {
      var self = this;
      var network = self.buy_airtime_request.network;
      var selected_amount = self.buy_airtime_request.selected_amount;
      var entered_amount = self.buy_airtime_request.entered_amount;
      var phone_number = self.buy_airtime_request.phone_number;
      var epin_checkbox_checked = self.epin_checkbox_checked;
      var quantity = self.buy_airtime_request.quantity;

      
      if(!epin_checkbox_checked){
        self.buy_airtime_request.recharge_type = "normal";
        if(network == "mtn" || network == "glo" || network == "airtel" || network == "9mobile"){
          if(self.amounts.includes(selected_amount)){

            if(phone_number != null && phone_number != 0){
              if(entered_amount == null || entered_amount == ""){
                self.buy_airtime_request.amount = selected_amount;
                self.buy_airtime_request.payable = selected_amount;
                self.proceedToSubmitBuyAirtimeRequest();
              }else{
                if(entered_amount >= 100 && entered_amount <= 50000){
                  self.buy_airtime_request.amount = entered_amount;
                  self.buy_airtime_request.payable = entered_amount;
                  self.proceedToSubmitBuyAirtimeRequest();
                }else{
                  swal({
                    title: 'Error',
                    text: "Amount Entered Is Not Valid. Amount Must Be Between ₦100 And ₦50,000",
                    type: 'error'
                  })
                }
              }
            }else{
              swal({
                title: 'Error',
                text: "Phone Number Entered Is Not Valid. Please Enter A Valid One",
                type: 'error'
              })
            }
          }else{
            swal({
              title: 'Error',
              text: "Invalid Amount Selected. Please Select A Valid One",
              type: 'error'
            })
          }
        }else{
          swal({
            title: 'Error',
            text: "Invalid Network Selected. Please Select A Valid One",
            type: 'error'
          })
        }
      }else{
        self.buy_airtime_request.recharge_type = "epin";
        if(network == "mtn" || network == "airtel"  || network == "glo" || network == "9mobile"){
          if(self.amounts.includes(selected_amount)){

            if(quantity != null){
              if(quantity >= 1 && quantity <= 20){
                
                self.buy_airtime_request.amount = selected_amount;
                var discount = 0.00;

                if(network == "mtn"){
                    
                    discount = 0.01;
                }else if(network == "glo"){
                    
                    discount = 0.02;
                }else if(network == "9mobile"){
                    
                    discount = 0.02;
                }else if(network == "airtel"){
                    
                    discount = 0.02;
                } 
                
                var payable = selected_amount - (discount * selected_amount);
                payable = payable * quantity;
                self.buy_airtime_request.payable = payable;
                self.proceedToSubmitBuyAirtimeRequest();
                
              }else{
                swal({
                  title: 'Error',
                  text: "Quantity Entered Must Be Between 1 And 20.",
                  type: 'error'
                })
              }
            }else{
              swal({
                title: 'Error',
                text: "Quantity Entered Is Not Valid. Please Enter A Valid One",
                type: 'error'
              })
            }
          }else{
            swal({
              title: 'Error',
              text: "Invalid Amount Selected. Please Select A Valid One",
              type: 'error'
            })
          }
        }else{
          swal({
            title: 'Error',
            text: "Invalid Network Selected. Please Select A Valid One",
            type: 'error'
          })
        }
      }
    },
    selectAmount(amount){
      var self = this;
      self.buy_airtime_request.selected_amount = amount;
      self.buy_airtime_request.entered_amount =  amount.toString();
    },
    selectNetwork(network){
      var self = this;
      if(self.epin_checkbox_checked){
        // if(network == "glo" || network == "9mobile"){
        //   swal({
        //     title: 'Error',
        //     text: "Sorry E-PIN Recharge Is Not Available For The Selected Network. Only For MTN And Airtel",
        //     type: 'error'
        //   })
        // }else{
          if(network == "mtn"){
            self.cashback_text = "Note: 1% Cashback On All Transactions"
          }else{
            self.cashback_text = "Note: 2% Cashback On All Transactions"
          }
          self.buy_airtime_request.network = network;
        // }
      }else{
        self.buy_airtime_request.network = network;
      }
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
