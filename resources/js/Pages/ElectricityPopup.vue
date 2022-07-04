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

  .disco-card{
    cursor: pointer;
    transition: border 0.05s;
  }

  .disco-card, .disco-card .card-body, .disco-card img{
    padding: 0;
  }

  .disco-card.selected{
    padding: 5px;
    border: 2px solid #9c27b0;
  }

  .disco-card img{
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

  .disco-card span{
    font-size: 13px;
    font-weight: bold;
  }

  
</style>

<template>
    <div class="content">
      <div id="other-overlay" :style="show_other_overlay == true ? 'display: block;' : 'display: none;' "></div>
      <div class="">
        <div class="container-fluid">
          <div class="text-right">
            <inertia-link class="btn btn-primary" :href="route('user_vtu_history_page') + '?length=10&type=electricity&isDirty=true&__rememberable=true'"><i style="font-size:17px;" class="fas fa-history"></i>&nbsp;&nbsp;&nbsp;&nbsp;View Electricity History
            </inertia-link>

            <a href="https://wa.me/2348036302232?text=I Have A Problem With My Electricity Recharge&" class="btn btn-success" target="_blank" ><i style="font-size:17px;" class="fab fa-whatsapp"></i></i>&nbsp;&nbsp;&nbsp;&nbsp;Support</a>
          </div>
          <div class="row justify-content-center" style="">
            <div class="card" id="main-card" >
              <div class="card-body">
                <h4 class="subhead">Select Disco</h4>

                <div class="container">
                  <div class="row justify-content-center">

                    <div v-for="disco in discos" :key="disco.index" :class="outputDiscoRowClasses(disco.index)" @click="selectedDisco(disco.index)" >
                      <div class="card-body text-center">
                        <img :src="'/images/'+ disco.image" :alt="disco.name" class="col-12">
                        <span v-html="disco.name"></span>
                      </div>

                    </div>

                    

                  </div>
                  <div class="row">
                    <div v-if="buy_electricity_request.errors.disco" class="form-error">{{ buy_electricity_request.errors.disco }}</div>
                  </div>
                </div>


                <h4 class="subhead" style="margin-top:40px;">Meter Type</h4>

                <div class="form-check form-check-radio form-check-inline">
                  <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="meter_type" id="inlineRadio1" v-model="buy_electricity_request.meter_type" value="prepaid"> Prepaid
                    <span class="circle">
                        <span class="check"></span>
                    </span>
                  </label>
                </div>
                <div class="form-check form-check-radio form-check-inline">
                  <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="meter_type" id="inlineRadio2" v-model="buy_electricity_request.meter_type" value="postpaid"> Postpaid
                    <span class="circle">
                        <span class="check"></span>
                    </span>
                  </label>
                </div>
                <div v-if="buy_electricity_request.errors.meter_type" class="form-error">{{ buy_electricity_request.errors.meter_type }}</div>
                
                <h4 class="subhead">Meter Number</h4>

                <text-input v-model="buy_electricity_request.meter_number" :error="buy_electricity_request.errors.meter_number" type="number" id="meter_number" class="col-12" placeholder="e.g 45062872259"/>

                <h4 class="subhead">Amount</h4>

                <text-input v-model="buy_electricity_request.amount" :error="buy_electricity_request.errors.amount" type="number" id="amount" class="col-12" placeholder="In Naira(₦)"/>

                <h4 class="subhead">Mobile Number</h4>

                <text-input v-model="buy_electricity_request.mobile_number" :error="buy_electricity_request.errors.mobile_number" type="number" id="mobile_number" class="col-12" placeholder="e.g 08127027321"/>

                <h4 class="subhead">Email</h4>

                <text-input v-model="buy_electricity_request.email" :error="buy_electricity_request.errors.email" type="email" id="email" class="col-12" placeholder="e.g ikechukwunwogo@gmail.com"/>

                <div class="form-group">
                  <div id="sms-check-div" class="form-check form-check-inline" style="margin-top: 15px">
                    <label class="form-check-label">
                      <input class="form-check-input" type="checkbox" v-model="buy_electricity_request.sms_check"> Send Token As SMS?
                      <span class="form-check-sign">
                          <span class="check"></span>
                      </span>
                    </label>
                  </div>
                </div>
                <p><em class="text-primary">Note: SMS Cost ₦5.00</em></p>

                
                <button @click="verifyMeterDetails" :disabled="buy_electricity_request.processing" class="d-flex align-items-center btn btn-primary col-12">
                  PROCEED
                  <div style="" v-if="buy_electricity_request.processing" class="spinner-border spinner-border-sm ml-auto" />
                  
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
              <button @click="cancelTransaction" type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body" id="modal-body" >
              <div class="text-center">
                <p>Kindly confirm that the details you entered are valid before clicking the "Confirm" button.</p>
                
              </div>
              <table class="table table-bordered">
                <tbody v-if="buy_electricity_request.disco != null">
                  <tr>
                    <td>DISCO</td>
                    <td style="text-transform"><em v-html="buy_electricity_request.disco" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>METER TYPE</td>
                    <td style="text-transform"><em v-html="buy_electricity_request.meter_type" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>METER NUMBER</td>
                    <td style="text-transform"><em v-html="buy_electricity_request.meter_number" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>CUSTOMER NAME</td>
                    <td style="text-transform"><em v-html="buy_electricity_request.customer_name" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>AMOUNT</td> 
                    <td><em v-html="'₦ ' + addCommas(buy_electricity_request.amount)" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>MOBILE NUMBER</td>
                    <td><em v-html="buy_electricity_request.mobile_number" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>EMAIL</td>
                    <td><em v-html="buy_electricity_request.email" class="text-primary"></em></td>
                  </tr>
                  
                  
                  <tr>
                    <td>PAYABLE</td>
                    <td><em v-html="'₦ ' + addCommas(buy_electricity_request.payable)" class="text-primary"></em></td>
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
              <button @click="cancelTransaction" type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
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
  },
  data() {
    return {
      
      buy_electricity_request: this.$inertia.form({
        selected_disco_index: null,
        disco: null,
        meter_type: "prepaid",
        meter_number: null,
        amount: "",
        mobile_number: "",
        email: "",
        sms_check: false,
        customer_name: '',
        productCode: "",
        productToken: "",
        use_payscribe: false,
        payable: null,
        
      }),
      check_disco_availability_request: this.$inertia.form({
        disco: "",
        
      }),
      
      
      show_other_overlay: false,
      discos: [
        {
          name: "Ikeja Electric",
          type: 'ikeja',
          image: 'ikeja_logo.png',
        },
        {
          name: "Eko Electric",
          type: 'eko',
          image: 'eko_logo.jpg',
        },
        {
          name: "Abuja Electric",
          type: 'abuja',
          image: 'abuja_logo.jpg',
        },
        {
          name: "Kano Electric",
          type: 'kano',
          image: 'kano_logo.png',
        },
        {
          name: "Jos Electric",
          type: 'jos',
          image: 'jos_logo.png',
        },
        {
          name: "Ibadan Electric",
          type: 'ibadan',
          image: 'ibadan_logo.png',
        },
        {
          name: "ENUGU Electric",
          type: 'enugu',
          image: 'enugu_logo.jpg',
        },
        {
          name: "Port Harcourt Electric",
          type: 'phc',
          image: 'PHEDC.jpg',
        },
        {
          name: "Kaduna Electric",
          type: 'kaduna',
          image: 'kaduna-electric.jpg',
        },
      ],
      

    }
  },
  
  mounted() {
    
    var self = this;
    $("body").removeClass("modal-open");
    

  },
  created() {
    var self = this;
    
    for(var i = 0; i < self.discos.length; i++){
      self.discos[i].index = i;
    }
  },
  methods: {
    proceedWithBuyPower() {
      var self = this;
      var selected_disco_index = self.buy_electricity_request.selected_disco_index;
      var disco = self.buy_electricity_request.disco;
      var meter_type = self.buy_electricity_request.meter_type;
      var meter_number = self.buy_electricity_request.meter_number;
      var amount = self.buy_electricity_request.amount;
      var mobile_number = self.buy_electricity_request.mobile_number;

      var email = self.buy_electricity_request.email;
      var sms_check = self.buy_electricity_request.sms_check;
      var customer_name = self.buy_electricity_request.customer_name;
      var productCode = self.buy_electricity_request.productCode;
      var productToken = self.buy_electricity_request.productToken;
      var use_payscribe = self.buy_electricity_request.use_payscribe;
      

      console.log(selected_disco_index)
      console.log(disco)
      console.log(meter_type)
      console.log(meter_number)
      console.log(amount)
      console.log(mobile_number)

      console.log(email)
      console.log(sms_check)
      console.log(customer_name)
      console.log(productCode)
      console.log(productToken)
      console.log(use_payscribe)

      self.buy_electricity_request.post(self.route('purchase_electricity_with_buypower'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success && response.order_id !== ""){
            var order_id = response.order_id;
            var transaction_pending = response.transaction_pending;

            var text = "You Have Successfully Credited Your " + meter_type + " <em class='text-primary'>" + self.discos[selected_disco_index].name+ "</em> With Meter Number: <em class='text-primary'>"+ meter_number +"</em> Account With ₦" + self.addCommas(amount) + ".";
            if(transaction_pending){
              

              if(meter_type == "prepaid"){
                text += " Note: This Order Is Currently Pending. You Have Been Debited. To See The Status Of Your Transaction And Your Meter Token, Track This Transaction From The Recharge Vtu Transaction History Page"; 
              }else{
                text += " Note: This Order Is Currently Pending. You Have Been Debited. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page"; 
              }

            }else{

              if(meter_type == "prepaid"){
                if(response.metertoken != ""){
                  var metertoken = response.metertoken;
                  text += " Your Meter Token Is: <em class='text-primary'>"+metertoken+"</em>";
                }else{
                  text += " Your Meter Token Will be Sent To Your Email And Notification Panel Soon.";
                }
              }
            }

            
            
            swal({
              title: 'Info',
              text: text,
              type: 'info',
              confirmButtonColor: '#3085d6',
              allowOutsideClick: false,
            }).then(function(){
              self.$inertia.visit(self.route('electricity_page'));
            });
          }else if(response.invalid_meterno){
            swal({
              title: 'Ooops',
              text: "An invalid Meter number was entered. Your Money Has Been Refunded",
              type: 'error'
            })
          }else if(response.meter_type_not_available){
            swal({
              title: 'Ooops',
              text: "Selected MeterType is not currently available. Your Money Has Been Refunded",
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
    proceedWithGsubz() {
      var self = this;
      var selected_disco_index = self.buy_electricity_request.selected_disco_index;
      var disco = self.buy_electricity_request.disco;
      var meter_type = self.buy_electricity_request.meter_type;
      var meter_number = self.buy_electricity_request.meter_number;
      var amount = self.buy_electricity_request.amount;
      var mobile_number = self.buy_electricity_request.mobile_number;

      var email = self.buy_electricity_request.email;
      var sms_check = self.buy_electricity_request.sms_check;
      var customer_name = self.buy_electricity_request.customer_name;
      var productCode = self.buy_electricity_request.productCode;
      var productToken = self.buy_electricity_request.productToken;
      var use_payscribe = self.buy_electricity_request.use_payscribe;
      

      console.log(selected_disco_index)
      console.log(disco)
      console.log(meter_type)
      console.log(meter_number)
      console.log(amount)
      console.log(mobile_number)

      console.log(email)
      console.log(sms_check)
      console.log(customer_name)
      console.log(productCode)
      console.log(productToken)
      console.log(use_payscribe)

      self.buy_electricity_request.post(self.route('purchase_electricity_with_gsubz'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success && response.order_id !== ""){
            var order_id = response.order_id;
            var transaction_pending = response.transaction_pending;

            var text = "You Have Successfully Credited Your " + meter_type + " <em class='text-primary'>" + self.discos[selected_disco_index].name+ "</em> With Meter Number: <em class='text-primary'>"+ meter_number +"</em> Account With ₦" + self.addCommas(amount) + ".";
            if(transaction_pending){
              

              if(meter_type == "prepaid"){
                text += " Note: This Order Is Currently Pending. You Have Been Debited. To See The Status Of Your Transaction And Your Meter Token, Track This Transaction From The Recharge Vtu Transaction History Page"; 
              }else{
                text += " Note: This Order Is Currently Pending. You Have Been Debited. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page"; 
              }

            }else{

              if(meter_type == "prepaid"){
                if(response.metertoken != ""){
                  var metertoken = response.metertoken;
                  text += " Your Meter Token Is: <em class='text-primary'>"+metertoken+"</em>";
                }else{
                  text += " Your Meter Token Will be Sent To Your Email And Notification Panel Soon.";
                }
              }
            }

            
            
            swal({
              title: 'Info',
              text: text,
              type: 'info',
              confirmButtonColor: '#3085d6',
              allowOutsideClick: false,
            }).then(function(){
              self.$inertia.visit(self.route('electricity_page'));
            });
          }else if(response.invalid_meterno){
            swal({
              title: 'Ooops',
              text: "An invalid Meter number was entered. Your Money Has Been Refunded",
              type: 'error'
            })
          }else if(response.meter_type_not_available){
            swal({
              title: 'Ooops',
              text: "Selected MeterType is not currently available. Your Money Has Been Refunded",
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
    proceedWithPayscribe() {
      var self = this;
      var selected_disco_index = self.buy_electricity_request.selected_disco_index;
      var disco = self.buy_electricity_request.disco;
      var meter_type = self.buy_electricity_request.meter_type;
      var meter_number = self.buy_electricity_request.meter_number;
      var amount = self.buy_electricity_request.amount;
      var mobile_number = self.buy_electricity_request.mobile_number;

      var email = self.buy_electricity_request.email;
      var sms_check = self.buy_electricity_request.sms_check;
      var customer_name = self.buy_electricity_request.customer_name;
      var productCode = self.buy_electricity_request.productCode;
      var productToken = self.buy_electricity_request.productToken;
      var use_payscribe = self.buy_electricity_request.use_payscribe;
      

      console.log(selected_disco_index)
      console.log(disco)
      console.log(meter_type)
      console.log(meter_number)
      console.log(amount)
      console.log(mobile_number)

      console.log(email)
      console.log(sms_check)
      console.log(customer_name)
      console.log(productCode)
      console.log(productToken)
      console.log(use_payscribe)

      self.buy_electricity_request.post(self.route('purchase_electricity_with_payscribe'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success && response.order_id !== ""){
            var order_id = response.order_id;
            var transaction_pending = response.transaction_pending;

            var text = "You Have Successfully Credited Your " + meter_type + " <em class='text-primary'>" + self.discos[selected_disco_index].name+ "</em> With Meter Number: <em class='text-primary'>"+ meter_number +"</em> Account With ₦" + self.addCommas(amount) + ".";
            if(transaction_pending){
              

              if(meter_type == "prepaid"){
                text += " Note: This Order Is Currently Pending. You Have Been Debited. To See The Status Of Your Transaction And Your Meter Token, Track This Transaction From The Recharge Vtu Transaction History Page"; 
              }else{
                text += " Note: This Order Is Currently Pending. You Have Been Debited. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page"; 
              }

            }else{

              if(meter_type == "prepaid"){
                if(response.metertoken != ""){
                  var metertoken = response.metertoken;
                  text += " Your Meter Token Is: <em class='text-primary'>"+metertoken+"</em>";
                }else{
                  text += " Your Meter Token Will be Sent To Your Email And Notification Panel Soon.";
                }
              }
            }

            
            
            swal({
              title: 'Info',
              text: text,
              type: 'info',
              confirmButtonColor: '#3085d6',
              allowOutsideClick: false,
            }).then(function(){
              self.$inertia.visit(self.route('electricity_page'));
            });
          }else if(response.invalid_meterno){
            swal({
              title: 'Ooops',
              text: "An invalid Meter number was entered. Your Money Has Been Refunded",
              type: 'error'
            })
          }else if(response.meter_type_not_available){
            swal({
              title: 'Ooops',
              text: "Selected MeterType is not currently available. Your Money Has Been Refunded",
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
    confirmAndProceedWithTransaction() {
      var self = this;
      var selected_disco_index = self.buy_electricity_request.selected_disco_index;
      var disco = self.buy_electricity_request.disco;
      var meter_type = self.buy_electricity_request.meter_type;
      var meter_number = self.buy_electricity_request.meter_number;
      var amount = self.buy_electricity_request.amount;
      var mobile_number = self.buy_electricity_request.mobile_number;

      var email = self.buy_electricity_request.email;
      var sms_check = self.buy_electricity_request.sms_check;
      var customer_name = self.buy_electricity_request.customer_name;
      var productCode = self.buy_electricity_request.productCode;
      var productToken = self.buy_electricity_request.productToken;
      var use_payscribe = self.buy_electricity_request.use_payscribe;
      

      console.log(selected_disco_index)
      console.log(disco)
      console.log(meter_type)
      console.log(meter_number)
      console.log(amount)
      console.log(mobile_number)

      console.log(email)
      console.log(sms_check)
      console.log(customer_name)
      console.log(productCode)
      console.log(productToken)
      console.log(use_payscribe)

      if(disco != null){


        $("#preview-transaction-modal").modal("hide");
        // if(disco == "eko"){
        //   self.proceedWithGsubz();
        // }else{
          if(use_payscribe){
            self.proceedWithPayscribe();
          }else{
            self.proceedWithBuyPower();
          }
        // }
        
      }else{
        swal({
          title: 'Error!',
          text: "No Disco Was Selected. Please Select A Disco To Proceed.",
          type: 'error'
        })
      }
    },
    cancelTransaction(){
      var self = this;
      $("#preview-transaction-modal").modal("hide");
    },
     verifyMeterDetails() {
      var self = this;
      var selected_disco_index = self.buy_electricity_request.selected_disco_index;
      var disco = self.buy_electricity_request.disco;
      var meter_type = self.buy_electricity_request.meter_type;
      var meter_number = self.buy_electricity_request.meter_number;
      var amount = self.buy_electricity_request.amount;
      var mobile_number = self.buy_electricity_request.mobile_number;

      var email = self.buy_electricity_request.email;
      var sms_check = self.buy_electricity_request.sms_check;
      var customer_name = self.buy_electricity_request.customer_name;
      var productCode = self.buy_electricity_request.productCode;
      var productToken = self.buy_electricity_request.productToken;
      var use_payscribe = self.buy_electricity_request.use_payscribe;
      

      console.log(selected_disco_index)
      console.log(disco)
      console.log(meter_type)
      console.log(meter_number)
      console.log(amount)
      console.log(mobile_number)

      console.log(email)
      console.log(sms_check)
      console.log(customer_name)
      console.log(productCode)
      console.log(productToken)
      console.log(use_payscribe)

      if(disco != null){

        self.buy_electricity_request.post(self.route('validate_meter_number_disco'), {
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response)

            if(response.success && response.customer_name !== ""){
              var proceed = false;
              self.buy_electricity_request.customer_name = response.customer_name;
              self.buy_electricity_request.use_payscribe = response.use_payscribe;
              self.buy_electricity_request.productCode = response.productCode;
              self.buy_electricity_request.productToken = response.productToken;

              if(self.buy_electricity_request.use_payscribe){
                proceed = true;
                
              }else{
                if(amount >= 1000){
                  proceed = true;
                }
              }

              if(proceed){

                swal({
                  title: 'Info',
                  text: "Is This Your Name ? <br> <em class='text-center text-primary'>"+self.buy_electricity_request.customer_name+"</em>" ,
                  type: 'info',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes Proceed!',
                  cancelButtonText : "No"
                }).then(function(){
                  if(self.buy_electricity_request.sms_check){
                    self.buy_electricity_request.payable = Number(amount) + 5;
                  }else{
                    self.buy_electricity_request.payable = amount;
                  }
                  $("#preview-transaction-modal").modal("show");
                });
              }else{
                swal({
                  title: 'Error!',
                  text: "Amount Must Be At Least 1000" ,
                  type: 'error'
                })
              }
            }else if(response.invalid_user){
              swal({
                title: 'Error!',
                text: "The Details Entered Were Invalid. Please Try Again." ,
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
      }else{
        swal({
          title: 'Error!',
          text: "No Disco Was Selected. Please Select A Disco To Proceed.",
          type: 'error'
        })
      }
    },
    selectedDisco(index){
      var self = this;
      if(self.buy_electricity_request.selected_disco_index != index){
        self.check_disco_availability_request.disco = self.discos[index].type;
        self.check_disco_availability_request.post(self.route('check_if_disco_is_available'), {
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response)

            if(response.success){
              self.buy_electricity_request.selected_disco_index = index;
              self.buy_electricity_request.disco = self.discos[index].type;
            }else{
              swal({
                title: 'Error',
                text: "<em class='text-primary'>" + self.discos[index].name + "</em> Disco Is Not Available At The Moment",
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
    outputDiscoRowClasses(index){
      var self = this;
      var str = "";
      if(self.buy_electricity_request.selected_disco_index == index){
        str += 'col-3 col-sm-1 card disco-card selected' ;
      }else{
        str += 'col-3 col-sm-1 card disco-card'; 
      }

      if(index != 0){
        str += ' offset-1';
      }
      return str;
    },
    
    
    validateBuyCableRequest() {
      var self = this;
      var selected_plan = JSON.parse(JSON.stringify(self.buy_electricity_request.selected_plan));

      console.log(Object.keys(selected_plan).length)
      if(Object.keys(selected_plan).length > 0){
        $("#preview-transaction-modal").modal("show");
      }else{
        swal({
          title: 'Error!',
          text: "You Must Select A Plan To Proceed" ,
          type: 'error'
        })
      }
      
    },
    purchasePlan(index,cycle = ""){
      var self = this;
      if(cycle == ""){
        self.buy_electricity_request.selected_plan_index = index;
        self.buy_electricity_request.selected_plan = self.cable_plans[index];
      }else{
        self.buy_electricity_request.selected_plan = {};
        self.buy_electricity_request.selected_plan.package_name = self.cable_plans[index].name;
        if(cycle == "daily"){
         self.buy_electricity_request.selected_plan.amount = self.cable_plans[index].cycles.daily;
         self.buy_electricity_request.selected_plan.cycle = "daily";
        }else if(cycle == "weekly"){
         self.buy_electricity_request.selected_plan.amount = self.cable_plans[index].cycles.weekly;
         self.buy_electricity_request.selected_plan.cycle = "weekly";
        }else if(cycle == "monthly"){
         self.buy_electricity_request.selected_plan.amount = self.cable_plans[index].cycles.monthly;
         self.buy_electricity_request.selected_plan.cycle = "monthly";
        }
      }
      console.log(self.buy_electricity_request.selected_plan);
      // console.log(self.buy_electricity_request.selected_plan.cycle)
      // console.log(self.buy_electricity_request.selected_plan.amount)

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
