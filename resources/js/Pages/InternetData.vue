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

  .data-plans-card{
    box-shadow: 0 1px 16px 0 rgb(0 0 0 / 14%);
    cursor: pointer;
    /*transition: border 0.05s;*/
    margin-bottom: 0;
  }

  .data-plans-card span{
    font-weight: bold;
    font-size: 13px;
  }

  

  .data-plans-card.selected{
    /*padding: 5px;*/
    border: 2px solid #9c27b0;
  }
</style>

<template>
    <div class="content">
      <div id="other-overlay" :style="show_other_overlay == true ? 'display: block;' : 'display: none;' "></div>
      <div class="">
        <div class="container-fluid">
          <div class="text-right">
            <inertia-link class="btn btn-primary" :href="route('user_vtu_history_page') + '?length=10&type=data&isDirty=true&__rememberable=true'"><i style="font-size:17px;" class="fas fa-history"></i>&nbsp;&nbsp;&nbsp;&nbsp;View Data History
            </inertia-link>

            <a href="https://wa.me/2348036302232?text=I Have A Problem With My Internet Data Recharge&" class="btn btn-success" target="_blank" ><i style="font-size:17px;" class="fab fa-whatsapp"></i></i>&nbsp;&nbsp;&nbsp;&nbsp;Support</a>
          </div>
          <div class="row justify-content-center" style="">
            <div class="card" id="main-card">
              <div class="card-body">
                <h4 class="subhead">Select Network</h4>

                <div class="container">
                  <div class="row">

                    <div :class="buy_data_request.network == 'mtn' ? 'col-2 col-sm-1 card network-card selected' : 'col-2 col-sm-1 card network-card'" @click="selectNetwork('mtn')">
                      <div class="card-body text-center">
                        <img src="/images/mtn_logo.png" alt="MTN" class="col-12">
                        <!-- <p>MTN</p> -->
                      </div>

                    </div>

                    <div class="col-1">
                      
                    </div>

                    <div :class="buy_data_request.network == 'glo' ? 'col-2 col-sm-1 card network-card selected' : 'col-2 col-sm-1 card network-card'" @click="selectNetwork('glo')">
                      <div class="card-body text-center">
                        <img src="/images/glo_logo.jpg" alt="GLO" class="col-12">
                        <!-- <p>MTN</p> -->
                      </div>
                    </div>

                    <div class="col-1">
                      
                    </div>

                    <div :class="buy_data_request.network == 'airtel' ? 'col-2 col-sm-1 card network-card selected' : 'col-2 col-sm-1 card network-card'" @click="selectNetwork('airtel')">
                      <div class="card-body text-center">
                        <img src="/images/airtel_logo.png" alt="Airtel" class="col-12">
                        <!-- <p>MTN</p> -->
                      </div>
                    </div>



                    <div class="col-1">
                      
                    </div>

                    <div :class="buy_data_request.network == '9mobile' ? 'col-2 col-sm-1 card network-card selected' : 'col-2 col-sm-1 card network-card'" @click="selectNetwork('9mobile')">
                      <div class="card-body text-center">
                        <img src="/images/9mobile-1.png" alt="9mobile" class="col-12">
                        <!-- <p>MTN</p> -->
                      </div>
                    </div>

                  </div>
                  <div class="row">
                    <div v-if="buy_data_request.errors.network" class="form-error">{{ buy_data_request.errors.network }}</div>
                  </div>
                </div>


                <h4 class="subhead">Enter Phone Number</h4>

                <text-input v-model="buy_data_request.phone_number" :error="buy_data_request.errors.phone_number" type="number" id="phone_number" placeholder="e.g 08127027321" class="col-12"/>

                <div class="form-group">
                  <div id="ported-number-check-div" class="form-check form-check-inline" style="margin-top: 15px">
                    <label class="form-check-label">
                      <input class="form-check-input" type="checkbox" v-model="buy_data_request.ported"> Ported Number
                      <span class="form-check-sign">
                          <span class="check"></span>
                      </span>
                    </label>
                  </div>
                </div>

                <h4 class="subhead" v-if="data_plans.length > 0">Select Data Plan</h4>
                <!-- <h4 class="subhead text-warning" v-else>Data Plans Could Not Be Loaded.</h4> -->

                <div class="container" v-if="data_plans.length > 0">
                  <div @click="selectDataPlan(plan.index - 1)" :class="buy_data_request.selected_plan_index == (plan.index - 1) ? 'data-plans-card card selected' : 'data-plans-card card' " v-for="plan in data_plans" :key="plan.index" >
                    <div class="card-body row">

                      <div class="col-3 col-sm-1">
                        <span v-html="plan.index + '.'"></span>
                      </div>

                      <div class="col-4 col-sm-6">
                        <span v-html="plan.product_name"></span>
                      </div>

                      <div class="col-5 col-sm-5">
                        <span v-html="'₦' + addCommas(plan.amount)"></span>
                      </div>

                      <!-- <div class="col-1">

                        <div class="form-check form-check-radio">
                          <label class="form-check-label">
                            <input v-if="buy_data_request.selected_plan_index == (plan.index - 1)" class="form-check-input" type="radio" checked>

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
                

                <!-- <button @click="validateBuyDataRequest" :disabled="buy_data_request.processing" class="d-flex align-items-center btn btn-primary col-12">
                  Continue
                  <div style="" v-if="buy_data_request.processing" class="spinner-border spinner-border-sm ml-auto" />
                  
                </button> -->
              </div>
            </div>
            

          </div>
        </div>
      </div>

      <!-- <div @click="validateBuyDataRequest" v-if="!buy_data_request.processing">
        <floating-action-button :styles="'background: 9124a3;'" :title="'Proceed'">
          
          <i class="fas fa-arrow-right" style="font-size: 25px; color: #fff;"></i>
        </floating-action-button>
      </div> -->

      <div @click="validateBuyDataRequest" v-if="!buy_data_request.processing">
        <floating-text-button :styles="'background: 9124a3;'" :title="'Proceed'">
          
          <!-- <i class="fas fa-arrow-right" style="font-size: 25px; color: #fff;"></i> -->
          <span style="font-size: 18px; font-weight: bold; color: #fff;">Submit</span>
        </floating-text-button>
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
                <tbody v-if="buy_data_request.selected_plan.length != {}">
                  <tr>
                    <td>NETWORK</td>
                    <td><em v-html="buy_data_request.selected_plan.network" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>DATA PLAN</td>
                    <td><em v-html="buy_data_request.selected_plan.product_name" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>AMOUNT</td> 
                    <td><em v-html="'₦ ' + addCommas(buy_data_request.selected_plan.amount)" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>PHONE</td>
                    <td><em v-html="buy_data_request.phone_number" class="text-primary"></em></td>
                  </tr>
              
                  <tr v-if="buy_data_request.selected_plan.sub_type == 'combo'">
                    <td>RECHARGE TYPE</td>
                    <td><em class="text-primary">Combo Recharge</em></td>
                  </tr>
                  <tr v-else>
                    <td>RECHARGE TYPE</td>
                    <td><em class="text-primary">Normal Recharge</em></td>
                  </tr>
                  <tr>
                    <td>PAYABLE</td>
                    <td><em v-html="'₦ ' + addCommas(buy_data_request.selected_plan.amount)" class="text-primary"></em></td>
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
import FloatingTextButton from '../Shared/FloatingTextButton'
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
    FloatingTextButton,
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

    // plans: Array,

    

  },
  data() {
    return {
      
      buy_data_request: this.$inertia.form({
        network: false,
        selected_plan_index: null,
        phone_number: null,
        selected_plan: {},
        ported: false,
      }),
      get_data_plans_request: this.$inertia.form({
        network: "",
        combo: false,
      }),
      
      
      show_other_overlay: false,
      data_plans: [],
      
      

    }
  },
  
  mounted() {
    
    var self = this;
    $("body").removeClass("modal-open");
    console.log((JSON.stringify(self.data_plans)))
    if(self.data_plans.length > 0){
      self.buy_data_request.selected_plan_index = 0;
      self.buy_data_request.selected_plan = self.data_plans[self.buy_data_request.selected_plan_index];
    }

  },
  created() {
    
  },
  methods: {
    proceedWithEminenceDataRequest() {
      var self = this;

      console.log('test')
      
      var selected_plan = self.buy_data_request.selected_plan;
      var phone_number = self.buy_data_request.phone_number;
      var sub_type = self.buy_data_request.selected_plan.sub_type;
      var network = self.buy_data_request.selected_plan.network;
      var ported = self.buy_data_request.ported;
      var product_name = self.buy_data_request.selected_plan.product_name;
      var amount = self.buy_data_request.selected_plan.amount;

      console.log(selected_plan)
      console.log(phone_number)
      console.log(sub_type)
      console.log(network)
      console.log(ported)

      self.buy_data_request.post(self.route('purchase_eminence_data'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            var order_id = response.order_id;
            var transaction_pending = response.transaction_pending;
            
            if(network == "glo" || network == "airtel"){
              var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + network + "</span> Network Is Successful. The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>.";
            }else{
              var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + network + "</span> Network Is Successful. Note You Have Been Debited Of ₦" + addCommas(amount)  + ". The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>.";
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
    proceedWithGsubzDataRequest() {
      var self = this;

      console.log('test')
      
      var selected_plan = self.buy_data_request.selected_plan;
      var phone_number = self.buy_data_request.phone_number;
      var sub_type = self.buy_data_request.selected_plan.sub_type;
      var network = self.buy_data_request.selected_plan.network;
      var ported = self.buy_data_request.ported;
      var product_name = self.buy_data_request.selected_plan.product_name;
      var amount = self.buy_data_request.selected_plan.amount;

      console.log(selected_plan)
      console.log(phone_number)
      console.log(sub_type)
      console.log(network)
      console.log(ported)

      self.buy_data_request.post(self.route('purchase_gsubz_data'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            var order_id = response.order_id;
            var transaction_pending = response.transaction_pending;
            
            if(network == "glo" || network == "airtel"){
              var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + network + "</span> Network Is Successful. The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>.";
            }else{
              var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + network + "</span> Network Is Successful. Note You Have Been Debited Of ₦" + addCommas(amount)  + ". The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>.";
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
    proceedWithClubDataRequest() {
      var self = this;
      
      var selected_plan = self.buy_data_request.selected_plan;
      var phone_number = self.buy_data_request.phone_number;
      var sub_type = self.buy_data_request.selected_plan.sub_type;
      var network = self.buy_data_request.selected_plan.network;
      var ported = self.buy_data_request.ported;
      var product_name = self.buy_data_request.selected_plan.product_name;
      var amount = self.buy_data_request.selected_plan.amount;

      console.log(selected_plan)
      console.log(phone_number)
      console.log(sub_type)
      console.log(network)
      console.log(ported)

      self.buy_data_request.post(self.route('purchase_clubkonnect_data'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            var order_id = response.order_id;
            var transaction_pending = response.transaction_pending;
            
            if(network == "glo" || network == "airtel"){
              var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + network + "</span> Network Is Successful. The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>.";
            }else{
              var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + network + "</span> Network Is Successful. Note You Have Been Debited Of ₦" + addCommas(amount)  + ". The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>.";
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
      
      var selected_plan = self.buy_data_request.selected_plan;
      var phone_number = self.buy_data_request.phone_number;
      var sub_type = self.buy_data_request.selected_plan.sub_type;
      var network = self.buy_data_request.selected_plan.network;
      var ported = self.buy_data_request.ported;
      var product_name = self.buy_data_request.selected_plan.product_name;
      var amount = self.buy_data_request.selected_plan.amount;

      console.log(selected_plan)
      console.log(phone_number)
      console.log(sub_type)
      console.log(network)
      console.log(ported)

      self.buy_data_request.post(self.route('purchase_payscribe_data'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            var order_id = response.order_id;
            var transaction_pending = response.transaction_pending;
            
            if(network == "glo" || network == "airtel"){
              var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + network + "</span> Network Is Successful. The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>.";
            }else{
              var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + network + "</span> Network Is Successful. Note You Have Been Debited Of ₦" + addCommas(amount)  + ". The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>.";
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
      
      var selected_plan = self.buy_data_request.selected_plan;
      var phone_number = self.buy_data_request.phone_number;
      var sub_type = self.buy_data_request.selected_plan.sub_type;
      var network = self.buy_data_request.selected_plan.network;
      var ported = self.buy_data_request.ported;
      var product_name = self.buy_data_request.selected_plan.product_name;
      var amount = self.buy_data_request.selected_plan.amount;

      console.log(selected_plan)
      console.log(phone_number)
      console.log(sub_type)
      console.log(network)
      console.log(ported)

      self.buy_data_request.post(self.route('purchase_9mobile_combo_data'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){

            
            var text_html = "Your SME DATA Recharge Request To Credit <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + network + "</span> Network Has Been Sent To The Admin. You Would Be Credited Shortly . Note You Have Been Debited Of ₦" + self.addCommas(amount)  + "."
            
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

    confirmAndProceedWithTransaction() {
      var self = this;
      
      var selected_plan = self.buy_data_request.selected_plan;
      var phone_number = self.buy_data_request.phone_number;
      var sub_type = self.buy_data_request.selected_plan.sub_type;
      var network = self.buy_data_request.selected_plan.network;

      console.log(selected_plan)
      console.log(phone_number)
      console.log(sub_type)
      console.log(network)
      
      

      $("#preview-transaction-modal").modal("hide");
      if(sub_type == "combo" && network == "9mobile"){
        self.proceedWithComboRequest();
      }else if(sub_type == "payscribe"){
        self.proceedWithPayscribeDataRequest();
      }else if(sub_type == "clubkonnect"){
        self.proceedWithClubDataRequest();
      }else if(sub_type == "gsubz"){
        self.proceedWithGsubzDataRequest();
      }else if(sub_type == "eminence"){
        self.proceedWithEminenceDataRequest();
      }
    },
    proceedToSubmitBuyDataRequest() {
      var self = this;
      var network = self.buy_data_request.network;
      var selected_plan = self.buy_data_request.selected_plan;
      var phone_number = self.buy_data_request.phone_number;

      
      $("#preview-transaction-modal").modal("show")
        
    },
    validateBuyDataRequest() {
      var self = this;
      var network = self.buy_data_request.network;
      var selected_plan = self.buy_data_request.selected_plan;
      var phone_number = self.buy_data_request.phone_number;
      
      if(network == "mtn" || network == "glo" || network == "airtel" || network == "9mobile"){
        if(phone_number != null && phone_number != 0){          
          self.proceedToSubmitBuyDataRequest();
            
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
          text: "Invalid Network Selected. Please Select A Valid One",
          type: 'error'
        })
      }
      
    },
    getDataPlans(network){
      var self = this;
      self.get_data_plans_request.network = network;
      self.get_data_plans_request.post(self.route('get_data_plans_by_network'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            if(response.data_plans.length > 0){
              self.buy_data_request.selected_plan_index = 0;
              self.buy_data_request.network = network;
              self.data_plans = response.data_plans;
              self.buy_data_request.selected_plan = self.data_plans[self.buy_data_request.selected_plan_index];
            }else{
              swal({
                title: 'Ooops',
                text: "Data Plans Could Not Be Loaded. Please Try Again",
                type: 'error'
              })
            }
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
      if(self.buy_data_request.network != network){
        if(network == "9mobile"){
          swal({
            title: 'Choose Option',
            text: "Choose Recharge Option: ",
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#4caf50',
            confirmButtonText: 'Normal Recharge',
            cancelButtonText : "SME DATA Recharge"
          }).then(function(){
            self.get_data_plans_request.combo = false;
            self.getDataPlans(network);
          },function(dismiss){
            if(dismiss == 'cancel'){
              self.get_data_plans_request.combo = true;
              self.getDataPlans(network);
            }
          });
        }else{
          self.get_data_plans_request.combo = false;
          self.getDataPlans(network);
        }
      }
      
        
    },
    selectDataPlan(index){
      var self = this;
      self.buy_data_request.selected_plan_index = index;
      self.buy_data_request.selected_plan = self.data_plans[index];
      console.log(self.buy_data_request.selected_plan);
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
