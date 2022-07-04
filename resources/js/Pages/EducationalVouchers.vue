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

  .voucher_type-card{
    cursor: pointer;
    transition: border 0.05s;
  }

  .voucher_type-card, .voucher_type-card .card-body, .voucher_type-card img{
    padding: 0;
  }

  .voucher_type-card.selected{
    padding: 5px;
    border: 2px solid #9c27b0;
  }

  .voucher_type-card img{
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
            <inertia-link class="btn btn-primary" :href="route('user_vtu_history_page') + '?length=10&type=E-Pin&isDirty=true&__rememberable=true'"><i style="font-size:17px;" class="fas fa-history"></i>&nbsp;&nbsp;&nbsp;&nbsp;View History
            </inertia-link>

            <a href="https://wa.me/2348036302232?text=I Have A Problem With My Educational Voucher Recharge" class="btn btn-success" target="_blank" ><i style="font-size:17px;" class="fab fa-whatsapp"></i></i>&nbsp;&nbsp;&nbsp;&nbsp;Support</a>
          </div>
          <div class="row justify-content-center" style="">
            <div class="card" id="main-card" v-if="epins_text == ''">
              <div class="card-body">
                <form id="router-service-form" @submit.prevent="validateBuyRouterRequest">
                  <h4 class="subhead">Select Voucher Type</h4>

                  <div class="container">
                    <div class="row" v-if="voucher_types.length > 0">

                      <!-- <div > -->
                        <div v-for="row in voucher_types" :key="row.index" :class="buy_voucher_request.voucher_type == row.name ? 'col-2 col-sm-1 offset-sm-1 card voucher_type-card selected' : 'col-2 col-sm-1 offset-sm-1 card voucher_type-card'" @click="selectVoucherType(row.name,row.index)">
                          <div class="card-body text-center">
                            <img :src="row.image" alt="WAEC" class="col-12">
                            <p v-html="'₦'+addCommas(row.price)" class="text-bold"></p>
                          </div>

                        </div>

                        <!-- <div class="col-1">
                          
                        </div> -->
                      <!-- </div> -->

                    </div>
                    <div class="row">
                      <div v-if="buy_voucher_request.errors.voucher_type" class="form-error">{{ buy_voucher_request.errors.voucher_type }}</div>
                    </div>
                  </div>


                  <!-- <p class="text-primary" v-if="buy_voucher_request.voucher_type == 'waec'">Note: One WAEC Voucher Costs ₦2,000. But You Get ₦100 Cashback.</p>

                  <p class="text-primary" v-if="buy_voucher_request.voucher_type == 'neco'">Note: One NECO Voucher Costs ₦1,000. But You Get ₦150 Cashback.</p> -->

                  <button :disabled="buy_voucher_request.processing" class="d-flex align-items-center btn btn-primary col-12">
                    Submit
                    <div style="" v-if="buy_voucher_request.processing" class="spinner-border spinner-border-sm ml-auto" />
                    
                  </button>
                </form>


              </div>
            </div>
            
            <div class="card" id="epins-card" v-if="epins_text != ''">
              <div class="card-body" v-html="epins_text">
                
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

            <div class="modal-body" id="modal-body">
              <div class="text-center">
                <p>Kindly confirm that the details you entered are valid before clicking the "Confirm" button.</p>
                
              </div>
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <td>VOUCHER TYPE</td>
                    <td><em v-html="buy_voucher_request.voucher_type" class="text-primary"></em></td>
                  </tr>
                  <tr>
                    <td>QUANTITY (UNITS)</td>
                    <td><em v-html="buy_voucher_request.quantity" class="text-primary"></em></td>
                  </tr>
                  
                  <tr>
                    <td>COST PER UNIT</td> 
                    <td><em v-html="'₦ ' + addCommas(buy_voucher_request.amount)" class="text-primary"></em></td>
                  </tr>
                  
                  <tr>
                    <td>PAYABLE</td>
                    <td><em v-html="'₦ ' + addCommas(buy_voucher_request.payable)" class="text-primary"></em></td>
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

    voucher_types: Array,

    

  },
  data() {
    return {
      
      buy_voucher_request: this.$inertia.form({
        voucher_type: null,
        quantity: 1,
        amount: null,
        payable: null,
        index: null,
      }),
      check_voucher_availability: this.$inertia.form({
        voucher_type: null,
        
      }),
      
      
      show_other_overlay: false,
      epins_text: "",
      
    }
  },
  
  mounted() {
    
    var self = this;
    

  },
  created() {
    console.log(this.voucher_types)
  },
  methods: {
    confirmAndProceedWithTransaction() {
      var self = this;
      // self.buy_voucher_request.voucher_type = "testy";
      var voucher_type = self.buy_voucher_request.voucher_type;
      var quantity = self.buy_voucher_request.quantity;
      var amount = self.buy_voucher_request.amount;
      var payable = self.buy_voucher_request.payable;
      

      console.log(voucher_type)
      console.log(quantity)
      console.log(amount)
      console.log(payable)
      
      

      $("#preview-transaction-modal").modal("hide");
      self.buy_voucher_request.post(self.route('buy_eminence_educational_voucher_vtu'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

           if(response.success && response.epins != "" && response.amount != "") {
            var epins = response.epins;
            
            var amount = response.amount;
            var text = "<p>"+voucher_type+" Result Checker ePin generated successfully<br> <h5>Details: </h5>Quantity: <em class='text-primary'>"+quantity+"</em> Unit(s)</p>";
            
            var j = 0;
            text += "<div class='container'>";
            // for(var i = 0; i < epins.length; i++){
              j++;
              // var pin = epins[i].pin;
              // var serial = epins[i].serial;
              
              text += "<div class='row' style='margin-bottom: 5px;'>";
              text += "<p class='col-1'>"+j+"</p>";
              text += "<p class='col-11'>"+epins+"</p>";
              // text += "<p class='col-5'>"+serial+"</p>";
              text += "</div>";
            // }

            text += "</div>";

            self.epins_text = text;

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
    // confirmAndProceedWithTransaction() {
    //   var self = this;
      
    //   var voucher_type = self.buy_voucher_request.voucher_type;
    //   var quantity = self.buy_voucher_request.quantity;
    //   var amount = self.buy_voucher_request.amount;
    //   var payable = self.buy_voucher_request.payable;
      

    //   console.log(voucher_type)
    //   console.log(quantity)
    //   console.log(amount)
    //   console.log(payable)
      
      

    //   $("#preview-transaction-modal").modal("hide");
    //   self.buy_voucher_request.post(self.route('buy_payscribe_educational_voucher_vtu'), {
    //     preserveScroll: true,
    //     onSuccess: (page) => {
          
    //       var response = JSON.parse(JSON.stringify(self.response_arr))
    //       console.log(response)

    //        if(response.success && Array.isArray(response.epins) && response.amount != "") {
    //         var epins = response.epins;
            
    //         var amount = response.amount;
    //         var text = "<p>"+voucher_type+" Result Checker ePin generated successfully<br> <h5>Details: </h5>Quantity: <em class='text-primary'>"+quantity+"</em> Unit(s)</p>";
            
    //         var j = 0;
    //         text += "<div class='container'>";
    //         for(var i = 0; i < epins.length; i++){
    //           j++;
    //           var pin = epins[i].pin;
    //           var serial = epins[i].serial;
              
    //           text += "<div class='row' style='margin-bottom: 5px;'>";
    //           text += "<p class='col-1'>"+j+"</p>";
    //           text += "<p class='col-6'>"+pin+"</p>";
    //           text += "<p class='col-5'>"+serial+"</p>";
    //           text += "</div>";
    //         }

    //         text += "</div>";

    //         self.epins_text = text;

    //       }else if(response.no_available_epin){
    //         swal({
    //           title: 'Ooops',
    //           text: "No Available E-PIN For The Parameters You Selected Currently. Please Try Again Later.",
    //           type: 'error'
    //         })
    //       }else if(response.insuffecient_funds){
    //         swal({
    //           title: 'Ooops',
    //           text: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
    //           type: 'error'
    //         })
    //       }else{
    //         swal({
    //           title: 'Ooops',
    //           text: "Something Went Wrong. Please Try Again",
    //           type: 'error'
    //         })
    //       }
    //     },onError: (errors) => {
          
    //       var errors = JSON.parse(JSON.stringify(errors))
    //       var errors_num = Object.keys(errors).length;
          
    //       if(errors_num > 0){
    //         $.notify({
    //           message: errors_num + " Field(s) Have Error(s). Please Correct Them."
    //         },{
    //           type : "warning",
    //           z_index: 20000,
    //         });
    //       }
    //     },
    //   });
    // },
    cancelTransaction(){
      var self = this;
      $("#preview-transaction-modal").modal("hide");
    },
    validateBuyRouterRequest() {
      var self = this;
      var voucher_type = self.buy_voucher_request.voucher_type;
      var quantity = self.buy_voucher_request.quantity;
      var index = self.buy_voucher_request.index;
      console.log(this.voucher_types[index]['price'])

      self.buy_voucher_request.amount = this.voucher_types[index].price;
      self.buy_voucher_request.payable = this.voucher_types[index].price;
      $("#preview-transaction-modal").modal("show");
      

      // console.log(voucher_type)
      // console.log(quantity)
     
      
      // if(voucher_type != null){
      //   self.buy_voucher_request.post(self.route('validate_educational_voucher_info'), {
      //     preserveScroll: true,
      //     onSuccess: (page) => {
            
      //       var response = JSON.parse(JSON.stringify(self.response_arr))
      //       console.log(response)

      //       if(response.success && response.amount != "" && response.payable != ""){
      //         self.buy_voucher_request.amount = response.amount;
      //         self.buy_voucher_request.payable = response.payable;
      //         $("#preview-transaction-modal").modal("show");
      //       }else{
      //         swal({
      //           title: 'Ooops',
      //           text: "Something Went Wrong. Please Try Again",
      //           type: 'error'
      //         })
      //       }
      //     },onError: (errors) => {
            
      //       var errors = JSON.parse(JSON.stringify(errors))
      //       var errors_num = Object.keys(errors).length;
            
      //       if(errors_num > 0){
      //         $.notify({
      //           message: errors_num + " Field(s) Have Error(s). Please Correct Them."
      //         },{
      //           type : "warning",
      //           z_index: 20000,
      //         });
      //       }
      //     },
      //   });
        
      // }else{
      //   swal({
      //     title: 'Error',
      //     text: "No Router Service Selected. Please Select One To Proceed",
      //     type: 'error'
      //   })
      // }
      
    },
    selectVoucherType(voucher_type,index){
      var self = this;
      if(self.buy_voucher_request.voucher_type != voucher_type){
        self.buy_voucher_request.voucher_type = voucher_type
        self.buy_voucher_request.index = index - 1;
        // self.check_voucher_availability.voucher_type = voucher_type;
        // self.check_voucher_availability.post(self.route('check_if_educational_voucher_is_available'), {
        //   preserveScroll: true,
        //   onSuccess: (page) => {
            
        //     var response = JSON.parse(JSON.stringify(self.response_arr))
        //     console.log(response)

        //     if(response.success){
              
        //       self.buy_voucher_request.voucher_type = voucher_type
        //     }else{
        //       swal({
        //         title: 'Ooops',
        //         text: voucher_type + " Is Not Available At The Moment. Please Try Again Later.",
        //         type: 'error'
        //       })
        //     }
        //   },onError: (errors) => {
            
        //     var errors = JSON.parse(JSON.stringify(errors))
        //     var errors_num = Object.keys(errors).length;
            
        //     if(errors_num > 0){
        //       $.notify({
        //         message: errors_num + " Field(s) Have Error(s). Please Correct Them."
        //       },{
        //         type : "warning",
        //         z_index: 20000,
        //       });
        //     }
        //   },
        // });
        
      }
      
    },
    
    
    
    selectRouterPlan(index){
      var self = this;
      if(self.buy_voucher_request.selected_plan_index != index){
        self.buy_voucher_request.selected_plan_index = index;
        self.buy_voucher_request.selected_plan = self.router_plans[index];
        console.log(self.buy_voucher_request.selected_plan);
      }
    },
    goBackFromPlansCard() {
      var self = this;
      self.router_plans_card_title = "";
      self.buy_voucher_request.selected_plan_index = null;
      self.buy_voucher_request.selected_plan = {};
      self.buy_voucher_request.customer_name = "";
      self.buy_voucher_request.productCode = null;
      self.router_plans = [];
      self.show_plans = false;
    },
    showDetailsBuyRouterRequest() {
      var self = this;
      var voucher_type = self.buy_voucher_request.voucher_type;
      var selected_plan_index = self.buy_voucher_request.selected_plan_index;
      var router_number = self.buy_voucher_request.router_number;
      var selected_plan = self.buy_voucher_request.selected_plan;
      var productCode = self.buy_voucher_request.productCode;
      var customer_name = self.buy_voucher_request.customer_name;

      console.log(voucher_type)
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
