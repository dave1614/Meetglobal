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
      <h2 class="text-center" style="margin-top:30px; margin-bottom: 60px; text-transform: capitalize;">Bouquet Plans For {{type}}</h2>
      <div class="col-sm-12 card transparent-card">
        <div class="card-header">
          
          <button class="btn btn-warning" @click="goBack">Go Back</button>
          <h3 class="text-center" style="text-transform: capitalize;">
            <p v-if="customer_name != ''">Customer Name: <em class="text-primary">{{customer_name}}</em></p>
            <p v-if="main_type == 'multichoice' && decoder_number != ''">Iuc Number: <em class="text-primary">{{decoder_number}}</em></p>
            <p v-if="main_type == 'startimes' && decoder_number != ''">Smart Card Number: <em class="text-primary">{{decoder_number}}</em></p>
          </h3>
        </div>
        <div class="card-body">
          
          <div class="row">

            <div v-if="main_type == 'multichoice' && cable_plans.length > 0" class="table-div material-datatables table-responsive">

              <table class="table table-striped table-bordered wrap hover display table-hover" id="data-bundles-table" cellspacing="0" width="100%" style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Data Amount</th>
                    <th>(₦) Cost</th>
                                              
                  </tr>
                </thead>
                <tbody>

                  <tr v-for="plan in cable_plans" :key="plan.index" @click="purchasePlan(plan.index)">
                    <td>{{plan.index}}</td>
                    <td>{{plan.name}}</td>
                    <td>{{plan.amount}}</td>
                  </tr>
                                            

                </tbody>
              </table>
            </div>

            <div v-else-if="main_type == 'startimes' && cable_plans.length > 0" class="table-div material-datatables table-responsive">

              <table class="table table-striped table-bordered wrap hover display table-hover" id="data-bundles-table" cellspacing="0" width="100%" style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Data Amount</th>
                    <th>(₦) Cost</th>
                                              
                  </tr>
                </thead>
                <tbody v-for="plan in cable_plans" :key="plan.index">

                  <tr>
                    <td style="font-weight: bold;">{{plan.index}}.</td>
                    <td style="font-weight: bold;">{{plan.name}}</td>
                    <td></td>
                  </tr>

                  <tr @click="purchasePlan(plan.index,'daily')">
                    <td>i.</td>
                    <td>Daily</td>
                    <td>{{plan.cycles.daily}}</td>
                  </tr>

                  <tr @click="purchasePlan(plan.index,'weekly')">
                    <td>ii.</td>
                    <td>Weekly</td>
                    <td>{{plan.cycles.weekly}}</td>
                  </tr>

                  <tr @click="purchasePlan(plan.index,'monthly')">
                    <td>iii.</td>
                    <td>Monthly</td>
                    <td>{{plan.cycles.monthly}}</td>
                  </tr>

                </tbody>
              </table>
            </div>

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
      title: `Choose Cable Tv Plan For ${this.type}` 
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
    cable_plans: Array,
    no_decoder_number: Boolean,
    invalid_decoder_number: Boolean,
    customer_name: String,
    productCode: String,
    productToken: String,
    decoder_number: String,
    main_type: String,
  },
  data() {
    return {
      
      epin_checkbox_checked: false,
      selected_index: 0,
      form: this.$inertia.form({
        "multichoice_type" : "",
        "smart_card_no" : "",
        "amount" : "",
        "plan" : "",
        "package_name" : "",
        "productCode": "",
        "productToken": "",
        "cycle" : "",
      }),
    }
  },
  
  mounted() {
    console.log(this.cable_plans)
  },
  created() {
    $("body").removeClass("modal-open");
    var self = this;
    if(this.no_decoder_number){
      if(self.main_type == "multichoice"){
        var text_html = 'No Iuc Number Was Found In URL. Click Ok To Enter Your Iuc Number';
      }else {
        var text_html = 'No Smart Card Number Was Found In URL. Click Ok To Enter Your Smart Card Number';
      }
      Swal.fire({
        title: 'Ooops!',
        html: text_html ,
        icon: 'error',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false
      }).then((result) => {
        if (result.isConfirmed) {
          self.$inertia.visit(self.route('buy_cable'));
        }
      })
    }else if(this.invalid_decoder_number){
      if(self.main_type == "multichoice"){
        var text_html = 'The Iuc Number You Entered Was Invalid. Click Ok To Enter A Valid One';
      }else {
        var text_html = 'The Smart Card Number You Entered Was Invalid. Click Ok To Enter A Valid One';
      }
      Swal.fire({
        title: 'Ooops!',
        html: text_html ,
        icon: 'error',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false
      }).then((result) => {
        if (result.isConfirmed) {
          self.$inertia.visit(self.route('buy_cable'));
        }
      })
    }
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
    purchasePlan(index,cycle = ""){
      index = index - 1;
      this.selected_index = index;
      var self = this;
      
      var form_data = {
        "multichoice_type" : self.type,
        "smart_card_no" : self.decoder_number,
        "amount" : self.cable_plans[index].amount,
        "plan" : self.cable_plans[index].package_id,
        "package_name" : self.cable_plans[index].name,
        "productCode": self.productCode,
        "productToken": self.productToken
      }

      if(self.main_type == "startimes"){
        if(cycle == "daily"){
         form_data.amount = self.cable_plans[index].cycles.daily;
         form_data.cycle = "daily";
        }else if(cycle == "weekly"){
         form_data.amount = self.cable_plans[index].cycles.weekly;
         form_data.cycle = "weekly";
        }else if(cycle == "monthly"){
         form_data.amount = self.cable_plans[index].cycles.monthly;
         form_data.cycle = "monthly";
        }
      }

      self.form.multichoice_type = form_data.multichoice_type;
      self.form.smart_card_no = form_data.smart_card_no;
      self.form.amount = form_data.amount;
      self.form.plan = form_data.plan;
      self.form.package_name = form_data.package_name;
      self.form.productCode = form_data.productCode;
      self.form.productToken = form_data.productToken;
      self.form.cycle = form_data.cycle;
      console.log(self.form)
      
                      
      Swal.fire({
        title: 'Info',
        html: "You Are About To Credit "+ self.form.multichoice_type +" Decoder With Number: <em class='text-primary'>" + self.form.smart_card_no + "</em> With " + self.form.package_name +" Package On <span style='text-transform: capitalize;'>" + self.form.multichoice_type + "</span>. Are You Sure You Want To Proceed? <p><em>Note You Would Be Debited Of ₦" + self.form.amount + "</em></p>" ,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes Proceed!',
        cancelButtonText : "No"
      }).then((result) => {
        if (result.isConfirmed) {
          
          self.form.post(self.route('process_buy_cable',self.form.multichoice_type), {
            preserveScroll: true,
            onSuccess: (page) => {
              
              var response = JSON.parse(JSON.stringify(self.response_arr))
              console.log(response)

              if(response.success && response.order_id !== ""){
                var order_id = response.order_id;
                var transaction_pending = response.transaction_pending;
                if(!transaction_pending){
                  Swal.fire({
                    title: 'Info',
                    html: "You Have Successfully Recharged Decoder With Number: <em class='text-primary'>" + self.form.smart_card_no + ".</em> The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>",
                    icon: 'info',
                    confirmButtonColor: '#3085d6'
                  }).then((result) => {
                    if (result.isConfirmed) {
                      document.location.reload();
                      self.$inertia.visit(self.route('cable_tv_plans',self.form.multichoice_type) + '?dn='+self.form.smart_card_no);
                    }
                  });
                }else{
                  Swal.fire({
                    title: 'Info',
                    html: "You Have Successfully Recharged Decoder With Number: <em class='text-primary'>" + self.form.smart_card_no + ".</em> The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>. Note: This Order Is Currently Pending. You Have Been Debited. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page.",
                    icon: 'info',
                    confirmButtonColor: '#3085d6'
                  }).then((result) => {
                    if (result.isConfirmed) {
                      document.location.reload();
                      self.$inertia.visit(self.route('cable_tv_plans',self.form.multichoice_type) + '?dn='+self.form.smart_card_no);
                    }
                  });
                }
              }else if(response.invalid_no){
                Swal.fire({
                  title: 'Ooops',
                  text: "Invalid Smart Card No. Was Entered. Your Money Has Been Refunded",
                  icon: 'error'
                })
              }else if(response.insuffecient_funds){
                Swal.fire({
                  title: 'Ooops',
                  text: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
                  icon: 'error'
                })
              }else if(response.error_message != ""){
                Swal.fire({
                  title: 'Ooops',
                  text: response.error_message,
                  icon: 'error'
                })
              }else{
                Swal.fire({
                  title: 'Ooops',
                  text: "Something Went Wrong",
                  icon: 'error'
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
      });
    },
    
    goBack() {
      if(this.previous_page != this.route('buy_cable')){
        this.$inertia.visit(this.route('buy_cable'));
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
