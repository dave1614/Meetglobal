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

  .operator-card{
    cursor: pointer;
    transition: border 0.05s;
  }

  .operator-card, .operator-card .card-body, .operator-card img{
    padding: 0;
  }

  .operator-card.selected{
    padding: 5px;
    border: 2px solid #9c27b0;
  }

  .operator-card img{
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

  .cable-plans-card{
    box-shadow: 0 1px 16px 0 rgb(0 0 0 / 14%);
    cursor: pointer;
    /*transition: border 0.05s;*/

    margin-bottom: 0;
  }

  .cable-plans-card span{
    font-weight: bold;
    font-size: 13px;
  }

  

  .cable-plans-card.selected{
    /*padding: 5px;*/
    border: 2px solid #9c27b0;
  }

  .cable-plans-card.display{
    cursor: unset;
  }
</style>

<template>
    <div class="content">
      <div id="other-overlay" :style="show_other_overlay == true ? 'display: block;' : 'display: none;' "></div>
      <div class="">
        <div class="container-fluid">
          <div class="text-right">
            <inertia-link class="btn btn-primary" :href="route('user_vtu_history_page') + '?length=10&type=cable&isDirty=true&__rememberable=true'"><i style="font-size:17px;" class="fas fa-history"></i>&nbsp;&nbsp;&nbsp;&nbsp;View Cable Tv History
            </inertia-link>

            <a href="https://wa.me/2348036302232?text=I Have A Problem With My Cable Tv Recharge&" class="btn btn-success" target="_blank" ><i style="font-size:17px;" class="fab fa-whatsapp"></i></i>&nbsp;&nbsp;&nbsp;&nbsp;Support</a>
          </div>
          <div class="row justify-content-center" style="">
            <div class="card" id="main-card" v-if="!show_cable_plans">
              <div class="card-body">
                <h4 class="subhead">Select Operator For Cable Tv</h4>

                <div class="container">
                  <div class="row">

                    <div :class="buy_cable_request.operator == 'dstv' ? 'col-2 col-sm-1 card operator-card selected' : 'col-2 col-sm-1 card operator-card'" @click="selectedTvOperator('dstv')">
                      <div class="card-body text-center">
                        <img src="/images/dstv_logo.jpg" alt="DSTV" class="col-12">
                        <!-- <p>MTN</p> -->
                      </div>

                    </div>

                    <div class="col-1">
                      
                    </div>

                    <div :class="buy_cable_request.operator == 'gotv' ? 'col-2 col-sm-1 card operator-card selected' : 'col-2 col-sm-1 card operator-card'" @click="selectedTvOperator('gotv')">
                      <div class="card-body text-center">
                        <img src="/images/gotv_logo.jpg" alt="GOTV" class="col-12">
                        <!-- <p>MTN</p> -->
                      </div>
                    </div>

                    <div class="col-1">
                      
                    </div>

                    <div :class="buy_cable_request.operator == 'startimes' ? 'col-2 col-sm-1 card operator-card selected' : 'col-2 col-sm-1 card operator-card'" @click="selectedTvOperator('startimes')">
                      <div class="card-body text-center">
                        <img src="/images/startimes_logo.jpg" alt="STARTIMES" class="col-12">
                        <!-- <p>MTN</p> -->
                      </div>
                    </div>



                  </div>
                  <div class="row">
                    <div v-if="buy_cable_request.errors.operator" class="form-error">{{ buy_cable_request.errors.operator }}</div>
                  </div>
                </div>


                <h4 class="subhead" v-html="decoder_number_title"></h4>

                <text-input v-model="buy_cable_request.decoder_number" :error="buy_cable_request.errors.decoder_number" type="number" id="decoder_number" class="col-12"/>

                
                <button @click="verifyDecoderNumber" :disabled="buy_cable_request.processing" class="d-flex align-items-center btn btn-primary col-12">
                  PROCEED
                  <div style="" v-if="buy_cable_request.processing" class="spinner-border spinner-border-sm ml-auto" />
                  
                </button>
              </div>
            </div>

            <div class="card" id="cable-plans-card" v-if="show_cable_plans">
              <div class="card-header">
                <button class="btn btn-warning" @click="reEnterCableDetails"> < < Re-Enter Details</button>
                <h3 class="card-title" v-html="cable_plans_card_title">Choose Bouquet</h3>
              </div>
              <div class="card-body">
                <div class="" v-if="(buy_cable_request.operator == 'dstv' || buy_cable_request.operator == 'gotv' || (buy_cable_request.operator == 'startimes' && buy_cable_request.platform == 'club')) && cable_plans.length > 0">
                  <div class="container" v-if="cable_plans.length > 0">
                    <div @click="purchasePlan(plan.index - 1)" :class="buy_cable_request.selected_plan_index == (plan.index - 1) ? 'cable-plans-card card selected' : 'cable-plans-card card' " v-for="plan in cable_plans" :key="plan.index" >
                      <div class="card-body row">

                        <div class="col-3 col-sm-1">
                          <span v-html="plan.index + '.'"></span>
                        </div>

                        <div class="col-4 col-sm-6">
                          <span v-html="plan.name"></span>
                        </div>

                        <div class="col-5 col-sm-5">
                          <span v-html="'₦' + addCommas(plan.amount)"></span>
                        </div>

                        <!-- <div class="col-1">

                          <div class="form-check form-check-radio">
                            <label class="form-check-label">
                              <input v-if="buy_cable_request.selected_plan_index == (plan.index - 1)" class="form-check-input" type="radio" checked>

                              <input v-else class="form-check-input" type="radio">
                              <span class="circle">
                                  <span class="check"></span>
                              </span>
                            </label>
                          </div>

                        </div> -->
                      </div>
                    </div>
                  </div>
                </div>

                <div v-else-if="buy_cable_request.operator == 'startimes' && buy_cable_request.platform == 'payscribe' && cable_plans.length > 0">

                  
                  <div class="container" v-for="plan in cable_plans" :key="plan.index">

                    <div class="cable-plans-card card display"  >
                      <div class="card-body row">

                        <div class="col-3 col-sm-1">
                          <span v-html="plan.index + '.'" style="font-size:15px;"></span>
                        </div>

                        <div class="col-4 col-sm-6">
                          <span v-html="plan.name" style="font-size:15px;"></span>
                        </div>

                        <div class="col-5 col-sm-5">
                          
                        </div>
                        
                      </div>
                    </div>

                      <div @click="purchasePlan(plan.index - 1,'daily')" :class="buy_cable_request.selected_plan.cycle == 'daily' && buy_cable_request.selected_plan.amount == plan.cycles.daily ? 'cable-plans-card card selected' : 'cable-plans-card card'">
                      <div class="card-body row">

                        <div class="col-3 col-sm-1">
                          <span>i.</span>
                        </div>

                        <div class="col-4 col-sm-6">
                          <span>Daily</span>
                        </div>

                        <div class="col-5 col-sm-5">
                          <span v-html="'₦' + addCommas(plan.cycles.daily)"></span>
                        </div>
                        
                      </div>
                    </div>

                    <div @click="purchasePlan(plan.index - 1,'weekly')" :class="buy_cable_request.selected_plan.cycle == 'weekly' && buy_cable_request.selected_plan.amount == plan.cycles.weekly ? 'cable-plans-card card selected' : 'cable-plans-card card'">
                      <div class="card-body row">

                        <div class="col-3 col-sm-1">
                          <span>ii</span>
                        </div>

                        <div class="col-4 col-sm-6">
                          <span>Weekly</span>
                        </div>

                        <div class="col-5 col-sm-5">
                          <span v-html="'₦' + addCommas(plan.cycles.weekly)"></span>
                        </div>

                      </div>
                    </div>

                    <div @click="purchasePlan(plan.index - 1,'monthly')" :class="buy_cable_request.selected_plan.cycle == 'monthly' && buy_cable_request.selected_plan.amount == plan.cycles.monthly ? 'cable-plans-card card selected' : 'cable-plans-card card'">
                      <div class="card-body row">

                        <div class="col-3 col-sm-1">
                          <span>iii</span>
                        </div>

                        <div class="col-4 col-sm-6">
                          <span>Monthly</span>
                        </div>

                        <div class="col-5 col-sm-5">
                          <span v-html="'₦' + addCommas(plan.cycles.monthly)"></span>
                        </div>
                      </div>
                    </div>

                  </div>

                    
                </div>
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
                <tbody v-if="buy_cable_request.operator == 'dstv' || buy_cable_request.operator == 'gotv' || (buy_cable_request.operator == 'startimes' && buy_cable_request.platform == 'club')">
                  <tr>
                    <td>CABLE OPERATOR</td>
                    <td style="text-transform"><em v-html="buy_cable_request.operator" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td v-if="buy_cable_request.operator == 'dstv' || buy_cable_request.operator == 'startimes'">Smart Card Number</td>
                    <td v-else>IUC Number</td>
                    <td style="text-transform"><em v-html="buy_cable_request.decoder_number" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>CUSTOMER NAME</td>
                    <td style="text-transform"><em v-html="buy_cable_request.customer_name" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>BOUQUET</td>
                    <td><em v-html="buy_cable_request.selected_plan.name" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>COST</td> 
                    <td><em v-html="'₦ ' + addCommas(buy_cable_request.selected_plan.amount)" class="text-primary"></em></td>
                  </tr>
                  
                  <tr>
                    <td>PAYABLE</td>
                    <td><em v-html="'₦ ' + addCommas(buy_cable_request.selected_plan.amount)" class="text-primary"></em></td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr>
                    <td>CABLE OPERATOR</td>
                    <td style="text-transform"><em v-html="buy_cable_request.operator" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>Smart Card Number</td>
                    <td style="text-transform"><em v-html="buy_cable_request.decoder_number" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>CUSTOMER NAME</td>
                    <td style="text-transform"><em v-html="buy_cable_request.customer_name" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>PACKAGE NAME</td>
                    <td><em v-html="buy_cable_request.selected_plan.package_name" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>CYCLE</td>
                    <td><em v-html="buy_cable_request.selected_plan.cycle" class="text-primary"></em></td>
                  </tr>

                  <tr>
                    <td>COST</td> 
                    <td><em v-html="'₦ ' + addCommas(buy_cable_request.selected_plan.amount)" class="text-primary"></em></td>
                  </tr>
                  
                  <tr>
                    <td>PAYABLE</td>
                    <td><em v-html="'₦ ' + addCommas(buy_cable_request.selected_plan.amount)" class="text-primary"></em></td>
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
        
      <div @click="validateBuyCableRequest" v-if="!buy_cable_request.processing && show_cable_plans">
        <floating-action-button :styles="'background: 9124a3;'" :title="'Proceed'">
          
          <i class="fas fa-arrow-right" style="font-size: 25px; color: #fff;"></i>
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
      
      buy_cable_request: this.$inertia.form({
        operator: "dstv",
        selected_plan_index: null,

        decoder_number: null,
        selected_plan: {},
        productCode: '',
        productToken: '',
        customer_name: '',
        platform: '',
        
      }),
      get_cable_plans_request: this.$inertia.form({
        operator: "",
        combo: false,
      }),
      
      
      show_other_overlay: false,
      cable_plans: [],
      show_cable_plans: false,
      decoder_number_title: "Enter Smart Card Number",
      cable_plans_card_title: "",
      

    }
  },
  
  mounted() {
    
    var self = this;
    $("body").removeClass("modal-open");
    

  },
  created() {
    
  },
  methods: {

    confirmAndProceedWithTransaction() {
      var self = this;
      
      var selected_plan = self.buy_cable_request.selected_plan;
      var operator = self.buy_cable_request.operator;
      
      var decoder_number = self.buy_cable_request.decoder_number;
      var productCode = self.buy_cable_request.productCode;
      var productToken = self.buy_cable_request.productToken;

      console.log(selected_plan)
      console.log(operator)
      console.log(decoder_number)
      console.log(productCode)
      console.log(productToken)
      
      if(Object.keys(selected_plan).length > 0){

        $("#preview-transaction-modal").modal("hide");
        self.buy_cable_request.post(self.route('purchase_cable_tv_plan'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success && response.order_id !== ""){
            self.buy_cable_request.selected_plan_index = null;
            self.buy_cable_request.selected_plan = {};
            var order_id = response.order_id;
            var transaction_pending = response.transaction_pending;
            if(!transaction_pending){
              swal({
                title: 'Info',
                text: "You Have Successfully Recharged Decoder With Number: <em class='text-primary'>" + decoder_number + ".</em> The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>",
                type: 'info',
                confirmButtonColor: '#3085d6',
                allowOutsideClick: false,
              }).then(function(){
                self.$inertia.visit(self.route('cable_tv_page'));
              });
            }else{
              swal({
                title: 'Info',
                text: "You Have Successfully Recharged Decoder With Number: <em class='text-primary'>" + decoder_number + ".</em> The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>. Note: This Order Is Currently Pending. You Have Been Debited. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page.",
                type: 'info',
                confirmButtonColor: '#3085d6',
                allowOutsideClick: false,
              }).then(function(){
                self.$inertia.visit(self.route('cable_tv_page'));
              });
            }
          }else if(response.invalid_no){
            swal({
              title: 'Ooops',
              text: "Invalid Smart Card No. Was Entered. Your Money Has Been Refunded",
              type: 'error'
            })
          }else if(response.insuffecient_funds){
            swal({
              title: 'Ooops',
              text: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
              type: 'error'
            })
          }else if(response.error_message != ""){
            swal({
              title: 'Ooops',
              text: response.error_message,
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
      }
    },
    cancelTransaction(){
      var self = this;
      self.buy_cable_request.selected_plan_index = null;
      self.buy_cable_request.selected_plan = {};
      $("#preview-transaction-modal").modal("hide");
    },
    validateBuyCableRequest() {
      var self = this;
      var selected_plan = JSON.parse(JSON.stringify(self.buy_cable_request.selected_plan));

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
        self.buy_cable_request.selected_plan_index = index;
        self.buy_cable_request.selected_plan = self.cable_plans[index];
      }else{
        self.buy_cable_request.selected_plan = {};
        self.buy_cable_request.selected_plan.package_name = self.cable_plans[index].name;
        if(cycle == "daily"){
         self.buy_cable_request.selected_plan.amount = self.cable_plans[index].cycles.daily;
         self.buy_cable_request.selected_plan.cycle = "daily";
        }else if(cycle == "weekly"){
         self.buy_cable_request.selected_plan.amount = self.cable_plans[index].cycles.weekly;
         self.buy_cable_request.selected_plan.cycle = "weekly";
        }else if(cycle == "monthly"){
         self.buy_cable_request.selected_plan.amount = self.cable_plans[index].cycles.monthly;
         self.buy_cable_request.selected_plan.cycle = "monthly";
        }
      }
      console.log(self.buy_cable_request.selected_plan);
      // console.log(self.buy_cable_request.selected_plan.cycle)
      // console.log(self.buy_cable_request.selected_plan.amount)

    },
    reEnterCableDetails() {
      var self = this;
      self.buy_cable_request.customer_name = "";
      self.cable_plans = [];
      self.buy_cable_request.productCode = "";
      self.buy_cable_request.productToken = "";
      self.show_cable_plans = false;
      self.cable_plans_card_title = "";
    },
    verifyDecoderNumber() {
      var self = this;
      var selected_plan = self.buy_cable_request.selected_plan;
      var decoder_number = self.buy_cable_request.decoder_number;
      var operator = self.buy_cable_request.operator;
      

      console.log(selected_plan)
      console.log(decoder_number)
      console.log(operator)

      self.buy_cable_request.post(self.route('validate_decoder_number_cable_plans'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success && response.customer_name !== "" && response.cable_plans != "" && response.productCode != "" && response.productToken != "" && response.platform != ""){
            
            self.buy_cable_request.platform = response.platform;
            swal({
              title: 'Info',
              text: "Is This Your Name ? <br> <em class='text-center text-primary'>"+response.customer_name+"</em>" ,
              type: 'info',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes Proceed!',
              cancelButtonText : "No"
            }).then(function(){
              self.buy_cable_request.customer_name = response.customer_name;
              self.cable_plans = response.cable_plans;
              self.buy_cable_request.productCode = response.productCode;
              self.buy_cable_request.productToken = response.productToken;
              self.show_cable_plans = true;
              self.cable_plans_card_title = "Cable Operator: <em class='text-primary'>"+operator+"</em><br>";
              if(operator == "dstv" || operator == "startimes"){
                self.cable_plans_card_title += "SmartCard Number: <em class='text-primary'>"+decoder_number+"</em><br>";

              }else{
                self.cable_plans_card_title += "IUC Number: <em class='text-primary'>"+decoder_number+"</em><br>";
              }

              self.cable_plans_card_title += "Customer Name: <em class='text-primary'>"+self.buy_cable_request.customer_name+"</em><br>";
              self.cable_plans_card_title += "<p style='font-size:17px; margin-top: 20px;'>Choose Plan Below</p>";
            });  
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
    },
    selectedTvOperator(operator){
      var self = this;
      if(self.buy_cable_request.operator != operator){
        self.buy_cable_request.operator = operator;
        if(operator == "dstv" || operator == "startimes"){
          self.decoder_number_title = "Enter Smart Card Number";
        }else{
          self.decoder_number_title = "Enter IUC Number";
        }
      }
      
        
    },
    proceedWithClubDataRequest() {
      var self = this;
      
      var selected_plan = self.buy_cable_request.selected_plan;
      var decoder_number = self.buy_cable_request.decoder_number;
      var sub_type = self.buy_cable_request.selected_plan.sub_type;
      var operator = self.buy_cable_request.selected_plan.operator;
      var ported = self.buy_cable_request.ported;
      var product_name = self.buy_cable_request.selected_plan.product_name;
      var amount = self.buy_cable_request.selected_plan.amount;

      console.log(selected_plan)
      console.log(decoder_number)
      console.log(sub_type)
      console.log(operator)
      console.log(ported)

      self.buy_cable_request.post(self.route('purchase_clubkonnect_data'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            var order_id = response.order_id;
            var transaction_pending = response.transaction_pending;
            
            if(operator == "glo" || operator == "airtel"){
              var text_html = "Your Request To Top Up <em class='text-primary'>" + decoder_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + operator + "</span> operator Is Successful. The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>.";
            }else{
              var text_html = "Your Request To Top Up <em class='text-primary'>" + decoder_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + operator + "</span> operator Is Successful. Note You Have Been Debited Of ₦" + addCommas(amount)  + ". The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>.";
            }

           
            if(transaction_pending){
              text_html += " Note: This Order Is Currently Pending. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page";
              
            }
            
            swal({
              title: 'Info',
              text: text_html,
              type: 'info',
              confirmButtonColor: '#3085d6',
              allowOutsideClick: false,
            }).then(function(){
              self.$inertia.visit(self.route('data_page'));
            });
          }else if(response.invalid_data_plan){
            swal({
              title: 'Ooops',
              text: "Invalid Data Plan Was Entered. Your Money Has Been Refunded",
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
    proceedWithPayscribeDataRequest() {
      var self = this;
      
      var selected_plan = self.buy_cable_request.selected_plan;
      var decoder_number = self.buy_cable_request.decoder_number;
      var sub_type = self.buy_cable_request.selected_plan.sub_type;
      var operator = self.buy_cable_request.selected_plan.operator;
      var ported = self.buy_cable_request.ported;
      var product_name = self.buy_cable_request.selected_plan.product_name;
      var amount = self.buy_cable_request.selected_plan.amount;

      console.log(selected_plan)
      console.log(decoder_number)
      console.log(sub_type)
      console.log(operator)
      console.log(ported)

      self.buy_cable_request.post(self.route('purchase_payscribe_data'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            var order_id = response.order_id;
            var transaction_pending = response.transaction_pending;
            
            if(operator == "glo" || operator == "airtel"){
              var text_html = "Your Request To Top Up <em class='text-primary'>" + decoder_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + operator + "</span> operator Is Successful. The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>.";
            }else{
              var text_html = "Your Request To Top Up <em class='text-primary'>" + decoder_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + operator + "</span> operator Is Successful. Note You Have Been Debited Of ₦" + addCommas(amount)  + ". The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>.";
            }

           
            if(transaction_pending){
              text_html += " Note: This Order Is Currently Pending. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page";
              
            }
            
            swal({
              title: 'Info',
              text: text_html,
              type: 'info',
              confirmButtonColor: '#3085d6',
              allowOutsideClick: false,
            }).then(function(){
              self.$inertia.visit(self.route('data_page'));
            });
          }else if(response.invalid_data_plan){
            swal({
              title: 'Ooops',
              text: "Invalid Data Plan Was Entered. Your Money Has Been Refunded",
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
      
      var selected_plan = self.buy_cable_request.selected_plan;
      var decoder_number = self.buy_cable_request.decoder_number;
      var sub_type = self.buy_cable_request.selected_plan.sub_type;
      var operator = self.buy_cable_request.selected_plan.operator;
      var ported = self.buy_cable_request.ported;
      var product_name = self.buy_cable_request.selected_plan.product_name;
      var amount = self.buy_cable_request.selected_plan.amount;

      console.log(selected_plan)
      console.log(decoder_number)
      console.log(sub_type)
      console.log(operator)
      console.log(ported)

      self.buy_cable_request.post(self.route('purchase_9mobile_combo_data'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){

            
            var text_html = "Your SME DATA Recharge Request To Credit <em class='text-primary'>" + decoder_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + operator + "</span> operator Has Been Sent To The Admin. You Would Be Credited Shortly . Note You Have Been Debited Of ₦" + self.addCommas(amount)  + "."
            
            swal({
              title: 'Info',
              text: text_html,
              type: 'info',
              confirmButtonColor: '#3085d6',
              allowOutsideClick: false,
            }).then(function(){
              self.$inertia.visit(self.route('data_page'));
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
