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

  .router_service-card{
    cursor: pointer;
    transition: border 0.05s;
  }

  .router_service-card, .router_service-card .card-body, .router_service-card img{
    padding: 0;
  }

  .router_service-card.selected{
    padding: 5px;
    border: 2px solid #9c27b0;
  }

  .router_service-card img{
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
            <inertia-link class="btn btn-primary" :href="route('user_vtu_history_page') + '?length=10&type=router&isDirty=true&__rememberable=true'"><i style="font-size:17px;" class="fas fa-history"></i>&nbsp;&nbsp;&nbsp;&nbsp;View Router Recharge History
            </inertia-link>

            <a href="https://wa.me/2348036302232?text=I Have A Problem With My Router Recharge" class="btn btn-success" target="_blank" ><i style="font-size:17px;" class="fab fa-whatsapp"></i></i>&nbsp;&nbsp;&nbsp;&nbsp;Support</a>
          </div>
          <div class="row justify-content-center" style="">
            <div class="card" id="main-card" v-if="!show_plans">
              <div class="card-body">
                <form id="router-service-form" @submit.prevent="validateBuyRouterRequest">
                  <h4 class="subhead">Select Router Service</h4>

                  <div class="container">
                    <div class="row">

                      <div :class="buy_router_request.router_service == 'smile' ? 'col-2 col-sm-1 card router_service-card selected' : 'col-2 col-sm-1 card router_service-card'" @click="selectRouterService('smile')">
                        <div class="card-body text-center">
                          <img src="/images/Smile-Logo.jpg" alt="SMILE" class="col-12">
                          <!-- <p>MTN</p> -->
                        </div>

                      </div>

                      
                    </div>
                    <div class="row">
                      <div v-if="buy_router_request.errors.router_service" class="form-error">{{ buy_router_request.errors.router_service }}</div>
                    </div>
                  </div>


                  <h4 class="subhead">Enter Router Number</h4>

                  <text-input v-model="buy_router_request.router_number" :error="buy_router_request.errors.router_number" type="number" id="router_number" placeholder="e.g 123456789" class="col-12"/>

                  <button v-if="router_plans.length == 0" :disabled="buy_router_request.processing" class="d-flex align-items-center btn btn-primary col-12">
                    Submit
                    <div style="" v-if="buy_router_request.processing" class="spinner-border spinner-border-sm ml-auto" />
                    
                  </button>
                </form>


              </div>
            </div>
            
            <div class="card" id="router-plans-card" v-if="show_plans">
              <div class="card-header">
                <button class="btn btn-warning" @click="goBackFromPlansCard"> < < Go Back</button>
                <h3 class="card-title" v-html="router_plans_card_title"></h3>
              </div>
              <div class="card-body">
                <h4 class="subhead" >Select Router Plan Below And Click Floating Action Button To Proceed.</h4>
                

                <div class="container" v-if="router_plans.length > 0">
                  <div @click="selectRouterPlan(plan.index - 1)" :class="buy_router_request.selected_plan_index == (plan.index - 1) ? 'data-plans-card card selected' : 'data-plans-card card' " v-for="plan in router_plans" :key="plan.index" >
                    <div class="card-body row">

                      <div class="col-2 col-sm-1">
                        <span v-html="plan.index + '.'"></span>
                      </div>

                      <div class="col-4 col-sm-4">
                        <span v-html="plan.name"></span>
                      </div>

                      <div class="col-3 col-sm-2">
                        <span v-html="'₦' + addCommas(plan.amount)"></span>
                      </div>

                      <div class="col-3 col-sm-5">
                        <span v-html="plan.validity"></span>
                      </div>

                    </div>
                  </div>
                </div>
                
              </div>
            </div>

          </div>
        </div>
      </div>

     

      <div @click="showDetailsBuyRouterRequest" v-if="!modal_open && router_plans.length > 0 && !buy_router_request.processing">
        <floating-action-button :styles="'background: 9124a3;'" :title="'Proceed'">
          
          <i class="fas fa-arrow-right" style="font-size: 25px; color: #fff;"></i>
        </floating-action-button>
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

            <div class="modal-body" id="modal-body">
              <div class="text-center">
                <p>Kindly confirm that the details you entered are valid before clicking the "Confirm" button.</p>
                
              </div>
              <table class="table table-bordered">
                <tbody v-if="buy_router_request.selected_plan.length != {}">
                  <tr>
                    <td>ROUTER SERVICE</td>
                    <td><em v-html="buy_router_request.router_service" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>ROUTER NUMBER</td>
                    <td><em v-html="buy_router_request.router_number" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>CUSTOMER NAME</td>
                    <td><em v-html="buy_router_request.customer_name" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>SELECTED PLAN</td>
                    <td><em v-html="buy_router_request.selected_plan.name" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>SELECTED PLAN VALIDITY</td>
                    <td><em v-html="buy_router_request.selected_plan.validity" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>SELECTED PLAN COST</td> 
                    <td><em v-html="'₦ ' + addCommas(buy_router_request.selected_plan.amount)" class="text-primary"></em></td>
                  </tr>
                  
                  <tr>
                    <td>PAYABLE</td>
                    <td><em v-html="'₦ ' + addCommas(buy_router_request.selected_plan.amount)" class="text-primary"></em></td>
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
      
      buy_router_request: this.$inertia.form({
        router_service: null,
        selected_plan_index: null,
        router_number: null,
        selected_plan: {},
        productCode: null,
        customer_name: "",
      }),
      
      
      show_other_overlay: false,
      router_plans: [],
      show_plans: false,
      router_plans_card_title: "",
      modal_open: false,
      

    }
  },
  
  mounted() {
    
    var self = this;
    

  },
  created() {
    
  },
  methods: {
    confirmAndProceedWithTransaction() {
      var self = this;
      
      var router_service = self.buy_router_request.router_service;
      var selected_plan_index = self.buy_router_request.selected_plan_index;
      var router_number = self.buy_router_request.router_number;
      var selected_plan = self.buy_router_request.selected_plan;
      var productCode = self.buy_router_request.productCode;
      var customer_name = self.buy_router_request.customer_name;

      console.log(router_service)
      console.log(selected_plan_index)
      console.log(router_number)
      console.log(selected_plan)
      console.log(productCode)
      console.log(customer_name)
      
      

      $("#preview-transaction-modal").modal("hide");
      self.buy_router_request.post(self.route('recharge_router'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            swal({
              title: 'Success',
              text: "You Have Successfully Recharged Your " + router_service + " Router <em class='text-primary'>" + router_number + "</em> With " + self.buy_router_request.selected_plan.name + " Which Costs ₦" + self.addCommas(self.buy_router_request.selected_plan.amount) +" <p><em>You Have Been Debited Of ₦" + self.addCommas(self.buy_router_request.selected_plan.amount) + "</em></p>" ,
              type: 'success',
              allowOutsideClick: false,
            }).then(function(){
              self.$inertia.visit(self.route('router_page'))
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
    cancelTransaction(){
      var self = this;
      self.buy_router_request.selected_plan_index = null;
      self.buy_router_request.selected_plan = {};
      self.modal_open = false;
      $("#preview-transaction-modal").modal("hide");
    },
    selectRouterPlan(index){
      var self = this;
      if(self.buy_router_request.selected_plan_index != index){
        self.buy_router_request.selected_plan_index = index;
        self.buy_router_request.selected_plan = self.router_plans[index];
        console.log(self.buy_router_request.selected_plan);
      }
    },
    goBackFromPlansCard() {
      var self = this;
      self.router_plans_card_title = "";
      self.buy_router_request.selected_plan_index = null;
      self.buy_router_request.selected_plan = {};
      self.buy_router_request.customer_name = "";
      self.buy_router_request.productCode = null;
      self.router_plans = [];
      self.show_plans = false;
    },
    showDetailsBuyRouterRequest() {
      var self = this;
      var router_service = self.buy_router_request.router_service;
      var selected_plan_index = self.buy_router_request.selected_plan_index;
      var router_number = self.buy_router_request.router_number;
      var selected_plan = self.buy_router_request.selected_plan;
      var productCode = self.buy_router_request.productCode;
      var customer_name = self.buy_router_request.customer_name;

      console.log(router_service)
      console.log(selected_plan_index)
      console.log(router_number)
      console.log(selected_plan)
      console.log(productCode)
      console.log(customer_name)
      
      if(selected_plan_index != null){
        self.modal_open = true;
        $("#preview-transaction-modal").modal("show");
        
      }else{
        swal({
          title: 'Error',
          text: "No Router Plan Selected. Please Select One To Proceed",
          type: 'error'
        })
      }
      
    },
    validateBuyRouterRequest() {
      var self = this;
      var router_service = self.buy_router_request.router_service;
      var selected_plan_index = self.buy_router_request.selected_plan_index;
      var router_number = self.buy_router_request.router_number;
      var selected_plan = self.buy_router_request.selected_plan;
      var productCode = self.buy_router_request.productCode;
      var customer_name = self.buy_router_request.customer_name;

      console.log(router_service)
      console.log(selected_plan_index)
      console.log(router_number)
      console.log(selected_plan)
      console.log(productCode)
      console.log(customer_name)
      
      if(router_service != null){
        self.buy_router_request.post(self.route('load_router_bundles_and_verify_number'), {
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response)

            if(response.success && response.router_plans != "" && response.customer_name != ""){
              var router_plans = response.router_plans;
              var customer_name = response.customer_name;
              var productCode = response.productCode;
              swal({
                title: 'Info',
                text: "Is This Your Name ? <br> <em class='text-center text-primary'>"+customer_name+"</em>" ,
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes Proceed!',
                cancelButtonText : "No"
              }).then(function(){
                self.router_plans_card_title = "Router Service: <em class='text-primary'>"+router_service+"</em>";
                self.router_plans_card_title += "<br>Router Number: <em class='text-primary'>"+router_number+"</em>";
                self.router_plans_card_title += "<br>Customer Name: <em class='text-primary'>"+customer_name+"</em>";
                
                self.buy_router_request.customer_name = customer_name;
                self.buy_router_request.productCode = productCode;
                self.router_plans = router_plans;
                self.show_plans = true;
              });
            }else if(response.incorrect_number){
              swal({
                title: 'Ooops',
                text: "Sorry The Router Number You Entered Is Invalid",
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
          title: 'Error',
          text: "No Router Service Selected. Please Select One To Proceed",
          type: 'error'
        })
      }
      
    },
    selectRouterService(router_service){
      var self = this;
      if(self.buy_router_request.router_service != router_service){
        self.buy_router_request.router_service = router_service
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
