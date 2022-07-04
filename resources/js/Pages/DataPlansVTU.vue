<style>
  .text-primary{
    color: #9124a3 !important;
  }

  .function-card{
    cursor: pointer;
    padding: 0;
  }

  .card.transparent-card{
    background: transparent !important;
  }
  tr{
    cursor: pointer;
  }
</style>

<template>
  <div class="container" style="margin-top:20px;">
    
    <div class="row justify-content-center">
      <h2 class="text-center" style="margin-top:30px; margin-bottom: 60px; text-transform: capitalize;">Data Plans For {{type}}</h2>
      <div class="col-sm-12 card transparent-card">
        <div class="card-header">
          
          <button class="btn btn-warning" @click="goBack">Go Back</button>
          <h3 class="text-center">Choose Data Plan:</h3>
        </div>
        <div class="card-body">
          
          <div class="row">

            <div v-if="data_plans.length > 0" class="table-div material-datatables table-responsive">

              <table class="table table-striped table-bordered wrap hover display table-hover" id="data-bundles-table" cellspacing="0" width="100%" style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Data Amount</th>
                    <th>(₦) Cost</th>
                                              
                  </tr>
                </thead>
                <tbody>

                  <tr v-for="plan in data_plans" :key="plan.index" @click="purchasePlan(plan.index)">
                    <td>{{plan.index}}</td>
                    <td>{{plan.product_name}}</td>
                    <td>{{plan.amount}}</td>
                  </tr>
                                            

                </tbody>
              </table>
            </div>

          </div>
          
          
        </div>  
        
      </div>
      
    </div>
    <div class="modal fade" data-backdrop="static" id="enter-mobile-no-data-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content" >
          <div class="modal-header">
            <h3 class="modal-title">Enter Mobile No.</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="returnPortedToDefault">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body" id="modal-body">
              
            <form id="enter-amount-data-form" @submit.prevent="submitBuyDataForm">

              

              <text-input  minlength="6" maxlength="15" v-model="form.mobile_number" :error="form.errors.mobile_number" type="number" label="Mobile No. To Credit" id="amount" placeholder="e.g 08127027321" required/>


              <div id="generate-epin-check-div" class="form-check form-check-inline" style=" margin-top: 15px">
                <label class="form-check-label">
                  <input class="form-check-input" type="checkbox" name="ported" id="ported" @change="markAsPorted"> Ported Number
                  <span class="form-check-sign">
                      <span class="check"></span>
                  </span>
                </label>
              </div>
             


              <!-- <input type="submit" class="btn btn-primary col-12" style="margin-top: 20px;"> -->
              <loading-button :loading="form.processing" class="btn-indigo ml-auto btn btn-primary col-12" type="submit">Submit</loading-button>

            </form>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close" @click="returnPortedToDefault">Close</button>
          </div>
        </div>
      </div>
     
    </div>

  
  </div>
</template>

<script>

import Layout from '../Shared/Layout'
import Pagination from '../Shared/Pagination'
import SearchFilter from '../Shared/SearchFilter'
import FloatingActionButton from '../Shared/FloatingActionButton'
import mapValues from 'lodash/mapValues'
import throttle from 'lodash/throttle'
import pickBy from 'lodash/pickBy'
import TextInput from '../Shared/TextInput'
import LoadingButton from '../Shared/LoadingButton'


export default {
  

  metaInfo() {
    return {
      title: `Buy Data For ${this.type}` 
    }
  },components: {
    Pagination,
    SearchFilter,
    FloatingActionButton,
    TextInput,
    LoadingButton,

  },
  layout: Layout,
  props: {
    response_arr: Object,
    previous_page: String,
    type: String,
    data_plans: Array,
  },
  data() {
    return {
      
      epin_checkbox_checked: false,
      selected_index: 0,
      form: this.$inertia.form({
        mobile_number: "",
        ported: false,
        plan: [],

      }),
    }
  },
  
  mounted() {
    console.log(this.data_plans)
  },
  created() {
    $("body").removeClass("modal-open");
  },
  methods: {
    returnPortedToDefault(){
      
      $('#ported').prop('checked', false)
        
      this.form.ported = false;
      
    },
    markAsPorted(){
      if($("#ported"). prop("checked") == true){
        this.form.ported = true;
      }else{
        this.form.ported = false;
      }
    },
    purchasePlan(index){
      index = index - 1;
      this.selected_index = index;
      
      $("#enter-mobile-no-data-modal").modal({
        backdrop: false,
        show: true
      });
    },
    submitBuyDataForm() {
      var self = this;
      // console.log(this.form.type)
      var mobile_number = self.form.mobile_number;
      
      self.form.plan = self.data_plans[self.selected_index];
      var amount = self.form.plan.amount;
      var type = self.form.plan.type;
      var product_name = self.form.plan.product_name;
      var combo = self.form.plan.combo;
      console.log(self.form);
      if(combo){
        
        var text_html = "You Are About To Credit <em class='text-primary'>" + mobile_number + "</em> With SME DATA Woth " + product_name +" On <span style='text-transform: capitalize;'>" + type + "</span> Network. Are You Sure You Want To Proceed? <p><em>Note You Would Be Debited Of ₦" + amount + "</em></p>"
      }else{
        if(type == "glo" || type == "airtel"){
          var text_html = "You Are About To Credit <em class='text-primary'>" + mobile_number + "</em> With " + product_name +" Worth Of Data On <span style='text-transform: capitalize;'>" + type + "</span> Network. Are You Sure You Want To Proceed? <p></p>";
        }else{
          var text_html = "You Are About To Credit <em class='text-primary'>" + mobile_number + "</em> With " + product_name +" Worth Of Data On <span style='text-transform: capitalize;'>" + type + "</span> Network. Are You Sure You Want To Proceed? <p><em>Note You Would Be Debited Of ₦" + amount + "</em></p>";
        }
        
      }

      Swal.fire({
        title: 'Info',
        html: text_html ,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes Proceed!',
        cancelButtonText : "No"
      }).then((result) => {
        if (result.isConfirmed) {

          self.form.post(self.route('process_buy_data',type), {
            preserveScroll: true,
            onSuccess: (page) => {
              
              var response = JSON.parse(JSON.stringify(self.response_arr))
              console.log(response)

              if(response.success){
                var order_id = response.order_id;
                var transaction_pending = response.transaction_pending;

                if(combo){
                  var text_html = "Your SME DATA Recharge Request To Credit <em class='text-primary'>" + mobile_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + type + "</span> Network Has Been Sent To The Admin. You Would Be Credited Shortly . Note You Have Been Debited Of ₦" + amount  + "."
                }else{

                  if(type == "glo" || type == "airtel"){
                    var text_html = "Your Request To Top Up <em class='text-primary'>" + mobile_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + type + "</span> Network Is Successful. The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>.";
                  }else{
                    var text_html = "Your Request To Top Up <em class='text-primary'>" + mobile_number + "</em> With Data Worth " + product_name + " On <span style='text-transform: capitalize;'>" + type + "</span> Network Is Successful. Note You Have Been Debited Of ₦" + amount  + ". The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>.";
                  }

                 
                  if(transaction_pending){
                    text_html += " Note: This Order Is Currently Pending. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page";
                    
                  }
                }
                Swal.fire({
                  title: 'Info',
                  html: "You Have Successfully Credited <em class='text-primary'>" + mobile_number + "</em> With Airtime Worth ₦" + self.addCommas(amount) + " On <span style='text-transform: capitalize;'>" + type + "</span> Network. Note You Have Been Debited Of ₦" + self.addCommas(amount)  + ". The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>",
                  icon: 'info',
                  confirmButtonColor: '#3085d6'
                }).then((result) => {
                  if (result.isConfirmed) {
                    // document.location.reload();
                    $("#enter-mobile-no-data-modal").modal("hide");
                    self.$inertia.visit(self.route('data_plans_list',type));
                  }
                });
              }else if(response.invalid_data_plan){
                Swal.fire({
                  title: 'Ooops',
                  text: "Invalid Data Plan Was Entered. Your Money Has Been Refunded",
                  icon: 'error'
                })
              }else if(response.invalid_recipient){
                Swal.fire({
                  title: 'Ooops',
                  text: "Invalid Mobile Number Was Entered. Your Money Has Been Refunded",
                  icon: 'error'
                })
              }else if(response.insuffecient_funds){
                Swal.fire({
                  title: 'Ooops',
                  text: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
                  icon: 'error'
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
        }
      });
        
        
      
    },
    goBack() {
      if(this.previous_page != this.route('buy_data')){
        this.$inertia.visit(this.route('buy_data'));
      }else{
        window.history.back()
      }
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
    printRechargeEpins(){

    }
  },
}
</script>
