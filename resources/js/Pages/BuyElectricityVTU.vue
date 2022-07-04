<style>
  .text-primary{
    color: #9124a3 !important;
  }

  .function-card{
    cursor: pointer;
    padding: 0;
    margin-bottom: 15px;
    margin-top: 15px;
  }

  .card.transparent-card{
    background: transparent !important;
  }
</style>

<template>
  <div class="container" style="margin-top:20px;">
    
    <div class="row justify-content-center">
      <h2 class="text-center" style="margin-top:30px; margin-bottom: 60px;">Buy Electricity VTU</h2>
      <div class="col-sm-12 card transparent-card">
        <div class="card-header">
          
          <!-- <inertia-link :href="route('recharge_vtu')" method="get" as="button" type="button" class="btn btn-warning">Go Back</inertia-link> -->
          <button class="btn btn-warning" @click="goBack">Go Back</button>
          <h3 class="text-center">Select Your Disco:</h3>
        </div>
        <div class="card-body">
          
          <div class="row">

            <div class="card col-sm-2 function-card" style="" @click="selectedDisco(disco.ikeja.type)">
              <div class="card-body" style="padding: 0;">
                <img src="/images/ikeja_logo.png" style="width: 100%; height: 160px;" :alt="disco.ikeja.name">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">{{disco.ikeja.name}}</h4>
                </div>
              </div>
            </div>

            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style=""  @click="selectedDisco(disco.eko.type)">
              <div class="card-body" style="padding: 0;">
                <img src="/images/eko_logo.jpg" style="width: 100%; height: 160px;" :alt="disco.eko.name">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">{{disco.eko.name}}</h4>
                </div>
              </div>
            </div>

            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style="" @click="selectedDisco(disco.abuja.type)">
              <div class="card-body" style="padding: 0;">
                <img src="/images/abuja_logo.jpg" style="width: 100%; height: 160px;" :alt="disco.abuja.name">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">{{disco.abuja.name}}</h4>
                </div>
              </div>
            </div>


            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style="">
              <div class="card-body" style="padding: 0;">
                <img src="/images/kano_logo.png"  @click="selectedDisco(disco.kano.type)" style="width: 100%; height: 160px;" :alt="disco.kano.name">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">{{disco.kano.name}}</h4>
                </div>
              </div>
            </div>

            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style="">
              <div class="card-body" style="padding: 0;">
                <img src="/images/jos_logo.png"  @click="selectedDisco(disco.jos.type)" style="width: 100%; height: 160px;" :alt="disco.jos.name">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">{{disco.jos.name}}</h4>
                </div>
              </div>
            </div>


            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style="">
              <div class="card-body" style="padding: 0;">
                <img src="/images/ibadan_logo.png"  @click="selectedDisco(disco.ibadan.type)" style="width: 100%; height: 160px;" :alt="disco.ibadan.name">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">{{disco.ibadan.name}}</h4>
                </div>
              </div>
            </div>

            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style="">
              <div class="card-body" style="padding: 0;">
                <img src="/images/enugu_logo.jpg"  @click="selectedDisco(disco.enugu.type)" style="width: 100%; height: 160px;" :alt="disco.enugu.name">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">{{disco.enugu.name}}</h4>
                </div>
              </div>
            </div>


            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style="">
              <div class="card-body" style="padding: 0;">
                <img src="/images/PHEDC.jpg"  @click="selectedDisco(disco.phc.type)" style="width: 100%; height: 160px;" :alt="disco.phc.name">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">{{disco.phc.name}}</h4>
                </div>
              </div>
            </div>

            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style="">
              <div class="card-body" style="padding: 0;">
                <img src="/images/kaduna-electric.jpg"  @click="selectedDisco(disco.kaduna.type)" style="width: 100%; height: 160px;" :alt="disco.kaduna.name">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">{{disco.kaduna.name}}</h4>
                </div>
              </div>
            </div>
          </div>
          
          
        </div>  
        
      </div>
      
    </div>
    <div class="modal fade" data-backdrop="static" id="enter-amount-electricity-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content" >
          <div class="modal-header">
            <h3 class="modal-title" v-html="electricity_modal_title"></h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body" id="modal-body">
            <p class="text-primary"><em>Note: Maximum Credit Balance Is ₦50,000</em></p>
            
              
              
            <form id="enter-amount-electricity-form" @submit.prevent="submitBuyElectrictyForm">

              <text-input v-model="form.meter_number" :error="form.errors.meter_number" type="number" label="Meter No. To Credit" id="meter_number" required/>
              <text-input max="50000" v-model="form.amount" :error="form.errors.amount" type="number" label="Amount" id="amount" placeholder="In Naira(₦)" required/>
              <text-input v-model="form.phone_number" :error="form.errors.phone_number" type="number" label="Phone Number" id="phone_number" placeholder="e.g 08127027321" required/>
              <text-input v-model="form.email" :error="form.errors.email" type="email" label="Email" id="email" placeholder="" required/>

              <div id="sms-check-div" class="form-check form-check-inline" style="margin-top: 15px">
                <label class="form-check-label">
                  <input class="form-check-input" type="checkbox" name="sms_check" id="sms_check" v-model="form.sms_check" > Send Token As SMS?
                  <span class="form-check-sign">
                      <span class="check"></span>
                  </span>
                </label>
              </div>
              <p><em class="text-primary">Note: SMS Cost ₦5.00</em></p>


              <!-- <input type="submit" class="btn btn-primary col-12" style="margin-top: 20px;"> -->
              <loading-button :loading="form.processing" class="btn-indigo ml-auto btn btn-primary col-12" type="submit">Submit</loading-button>

            </form>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
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
  metaInfo: { title: 'Buy Airtime VTU' },
  components: {
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
  },
  data() {
    return {
      type: "",
      epin_checkbox_checked: false,
      electricity_modal_title: "",
      form: this.$inertia.form({
        amount: "",
        meter_number: "",
        disco: "",
        meter_type: "",
        sms_check: false,
        email: "",
        phone_number: "",
        use_payscribe: false,
      }),
      check_disco_availability_data: this.$inertia.form({
        disco: "",

      }),
      disco: {
        ikeja: {
          name: "Ikeja Electric",
          type: 'ikeja',
          image: '',
        },
        eko: {
          name: "Eko Electric",
          type: 'eko',
          image: '',
        },
        abuja: {
          name: "Abuja Electric",
          type: 'abuja',
          image: '',
        },
        kano: {
          name: "Kano Electric",
          type: 'kano',
          image: '',
        },
        jos: {
          name: "Jos Electric",
          type: 'jos',
          image: '',
        },
        ibadan: {
          name: "Ibadan Electric",
          type: 'ibadan',
          image: '',
        },
        enugu: {
          name: "ENUGU Electric",
          type: 'enugu',
          image: '',
        },
        phc: {
          name: "Port Harcourt Electric",
          type: 'phc',
          image: '',
        },
        kaduna: {
          name: "Kaduna Electric",
          type: 'kaduna',
          image: '',
        },
      }
    }
  },
  
  mounted() {
    
  },
  created() {
    $("body").removeClass("modal-open");
  },
  methods: {
    submitBuyElectrictyForm() {
      var self = this;

      console.log(self.form)
      
    
      // console.log(this.form.type)
      var type = self.form.disco;
      var meter_type = self.form.meter_type;
      var meter_number = self.form.meter_number;
      var amount = self.form.amount;
      var disco_name = self.disco[type].name;

      var amount_to_debit_user = Number(amount);
        

      self.form.post(self.route('verify_electricity_details'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success && response.customer_name !== ""){
            var proceed = false;
            var customer_name = response.customer_name;
            var use_payscribe = response.use_payscribe;
           

            if(use_payscribe){
              proceed = true;
              self.form.use_payscribe = true;
              
            }else{
              self.form.use_payscribe = false;
              if(amount_to_debit_user >= 1000){
                proceed = true;
              }
            }

            if(proceed){

              Swal.fire({
                title: 'Info',
                html: "Is This Your Name ? <br> <em class='text-center text-primary'>"+customer_name+"</em>" ,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes Proceed!',
                cancelButtonText : "No"
              }).then((result) => {
                if (result.isConfirmed) {
                  
                  Swal.fire({
                    title: 'Info',
                    html: "You Are About To Credit Your " + meter_type + " <em class='text-primary'>" + disco_name + "</em> With Meter Number: <em class='text-primary'>"+ meter_number +"</em> Account With ₦" + self.addCommas(amount) +". Are You Sure You Want To Proceed? <p><em>Note You Would Be Debited Of ₦" + self.addCommas(amount_to_debit_user) + "</em></p>" ,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes Proceed!',
                    cancelButtonText : "No"
                  }).then((result) => {
                    if (result.isConfirmed) {
                      self.form.post(self.route('vend_electricity'), {
                      preserveScroll: true,
                      onSuccess: (page) => {
                        
                        var response = JSON.parse(JSON.stringify(self.response_arr))
                        console.log(response)

                        if(response.success && response.order_id !== ""){
                          var order_id = response.order_id;
                          var transaction_pending = response.transaction_pending;

                          var text = "You Have Successfully Credited Your " + meter_type + " <em class='text-primary'>" +disco_name + "</em> With Meter Number: <em class='text-primary'>"+ meter_number +"</em> Account With ₦" + self.addCommas(amount) + ".";
                          if(transaction_pending){
                            text += " Note: This Order Is Currently Pending. You Have Been Debited. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page";

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
                          
                          Swal.fire({
                            title: 'Info',
                            html: text,
                            icon: 'info',
                            confirmButtonColor: '#3085d6'
                          }).then((result) => {
                            if (result.isConfirmed) {
                              this.$inertia.visit(this.route('buy_electricity'));
                            }
                          });
                        }else if(response.invalid_meterno){
                          Swal.fire({
                            title: 'Ooops',
                            text: "An invalid Meter number was entered. Your Money Has Been Refunded",
                            icon: 'error'
                          })
                        }else if(response.meter_type_not_available){
                          Swal.fire({
                            title: 'Ooops',
                            text: "Selected MeterType is not currently available. Your Money Has Been Refunded",
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
                    })
                    }
                  });
                }
              });
            }else{
              Swal.fire({
                title: 'Error!',
                text: "Amount Must Be At Least 1000" ,
                icon: 'error'
              })
            }
          }else if(response.invalid_user){
            Swal.fire({
              title: 'Error!',
              text: "The Details Entered Were Invalid. Please Try Again." ,
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
      })
             
      
    },
    proceedToEnterMeterNoAndAmount(meter_type,type){
      var self = this;
      console.log(meter_type)
      console.log(type)
      self.form.meter_type = meter_type;
      self.form.disco = type;
      
      self.electricity_modal_title = "Disco: <em class='text-primary'>" + self.disco[type].name + "</em><br> Meter Type: <em class='text-primary'>" + meter_type + "</em>";
      
      $("#enter-amount-electricity-modal").modal({
        backdrop: false,
        show: true
      });

    },
    selectedDisco(type) {
      var self = this;
      
      self.check_disco_availability_data.disco = type;
      var name = self.disco[type].name;

      console.log(name)
      
      var meter_type = "";
      var form_data = self.check_disco_availability_data;

      form_data.post(self.route('check_disco_availability'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            Swal.fire({
              title: 'Choose Action: ',
              html: "Please Select Meter Type",
              icon: 'question',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Prepaid',
              cancelButtonText : "Postpaid"
            }).then((result) => {
              if (result.isConfirmed) {
                meter_type = "prepaid";
                self.proceedToEnterMeterNoAndAmount(meter_type,type)
              } else if (result.dismiss === Swal.DismissReason.cancel) {
                meter_type = "postpaid";
                self.proceedToEnterMeterNoAndAmount(meter_type,type);
              }
            });
          }else{
            Swal.fire({
              title: 'Error',
              html: "<em class='text-primary'>" + name + "</em> Disco Is Not Available At The Moment",
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
      })
      
    },
    viewTransactionHistory() {

    },
    epinCheckBoxChanged () {
      if($("#epin_check"). prop("checked") == true){
        this.epin_checkbox_checked = true;
      }else{
        this.epin_checkbox_checked = false;
      }

    },
    goBack() {
      if(this.previous_page != this.route('recharge_vtu')){
        this.$inertia.visit(this.route('recharge_vtu'));
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
