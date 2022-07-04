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
</style>

<template>
  <div class="container" style="margin-top:20px;">
    
    <div class="row justify-content-center">
      <h2 class="text-center" style="margin-top:30px; margin-bottom: 60px;">Buy Airtime VTU</h2>
      <div class="col-sm-12 card transparent-card">
        <div class="card-header">
          
          <!-- <inertia-link :href="route('recharge_vtu')" method="get" as="button" type="button" class="btn btn-warning">Go Back</inertia-link> -->
          <button class="btn btn-warning" @click="goBack">Go Back</button>
          <h3 class="text-center">Select Operator For Airtime:</h3>
        </div>
        <div class="card-body">
          
          <div class="row">

            <div class="card col-sm-2 function-card" style="" @click="selectedAirtimeOperator('mtn')">
              <div class="card-body" style="padding: 0;">
                <img src="/images/mtn_logo.png" style="width: 100%; height: 160px;" alt="MTN">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">MTN</h4>
                </div>
              </div>
            </div>

            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style="" id="glo-airtime" @click="selectedAirtimeOperator('glo')">
              <div class="card-body" style="padding: 0;">
                <img src="/images/glo_logo.jpg" style="width: 100%; height: 160px;" alt="GLO">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">GLO</h4>
                </div>
              </div>
            </div>

            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style="" id="airtel-airtime" @click="selectedAirtimeOperator('airtel')">
              <div class="card-body" style="padding: 0;">
                <img src="/images/airtel_logo.png" style="width: 100%; height: 160px;" alt="AIRTEL">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">AIRTEL</h4>
                </div>
              </div>
            </div>


            <div class="offset-sm-1">
  
            </div>
            

            <div class="card col-sm-2 function-card" style="">
              <div class="card-body" style="padding: 0;">
                <img src="/images/9mobile-1.png" id="9mobile-airtime" @click="selectedAirtimeOperator('9mobile')" style="width: 100%; height: 160px;" alt="9 MOBILE">
                <div class="" style="margin-top: 10px;">
                  <h4 class="text-center" style="font-size: 20px; font-weight: bold;">9 MOBILE</h4>
                </div>
              </div>
            </div>


          </div>
          
          
        </div>  
        
      </div>
      
    </div>
    <div class="modal fade" data-backdrop="static" id="enter-amount-airtime-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content" >
        <div class="modal-header">
          <h3 class="modal-title">Enter Amount</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" id="modal-body">
          <p class="text-primary"><em>Note: Minimum Credit Balance Is ₦100 And Maximum Is ₦50,000</em></p>
          
            
            
          <form id="enter-amount-airtime-form" @submit.prevent="submitBuyAirtimeForm">

            <text-input min="100" max="50000" v-model="form.amount" :error="form.errors.amount" type="number" label="Amount" id="amount" placeholder="In Naira(₦)"/>

            <text-input v-if="epin_checkbox_checked" min="1" max="20" v-model="form.quantity" :error="form.errors.quantity" type="number" label="Quantity (units)" id="amount" placeholder="e.g 1"/>

            <text-input v-if="!epin_checkbox_checked" minlength="6" maxlength="15" v-model="form.mobile_number" :error="form.errors.mobile_number" type="number" label="Mobile No. To Credit" id="amount" placeholder="e.g 08127027321"/>


            <div id="generate-epin-check-div" class="form-check form-check-inline" style=" margin-top: 15px">
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="epin_check" id="epin_check"  @change="epinCheckBoxChanged"> Generate E-PIN
                <span class="form-check-sign">
                    <span class="check"></span>
                </span>
              </label>
            </div>

            <p v-if="epin_checkbox_checked" id="epin-cashback" style=""><em class="text-primary">Note: 2% Cashback On All Transactions</em></p>
           


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
      epins_amount: "",
      epins_json: "",
      form: this.$inertia.form({
        amount: "",
        quantity: "",
        mobile_number: "",
        type: "",

      }),
    }
  },
  
  mounted() {
    
  },
  created() {
    $("body").removeClass("modal-open");
  },
  methods: {
    submitBuyAirtimeForm() {
      var self = this;
      // console.log(this.form.type)
      var mobile_number = self.form.mobile_number;
      var amount = self.form.amount;
      var type_str = self.form.type
      // console.log(self.addCommas(amount))
      if(!self.epin_checkbox_checked){

        Swal.fire({
          title: 'Info',
          html: "You Are About To Credit <em class='text-primary'>" + mobile_number + "</em> With Airtime Worth ₦" + self.addCommas(amount) + " On <span style='text-transform: capitalize;'>" + type_str + "</span> Network. Are You Sure You Want To Proceed? <p><em>Note You Would Be Debited Of ₦" + self.addCommas(amount) + "</em></p>" ,
          icon: 'info',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes Proceed!',
          cancelButtonText : "No"
        }).then((result) => {
          if (result.isConfirmed) {
            if(type_str != "9mobile"){

              self.form.post(self.route('process_buy_airtime'), {
                preserveScroll: true,
                onSuccess: (page) => {
                  
                  var response = JSON.parse(JSON.stringify(self.response_arr))
                  console.log(response)

                  if(response.success && response.order_id !== ""){
                    var order_id = response.order_id;
                    Swal.fire({
                      title: 'Info',
                      html: "You Have Successfully Credited <em class='text-primary'>" + mobile_number + "</em> With Airtime Worth ₦" + self.addCommas(amount) + " On <span style='text-transform: capitalize;'>" + type_str + "</span> Network. Note You Have Been Debited Of ₦" + self.addCommas(amount)  + ". The Order Id For This Transaction Is <em class='text-primary'>" +order_id + "</em>",
                      icon: 'info',
                      confirmButtonColor: '#3085d6'
                    }).then((result) => {
                      if (result.isConfirmed) {
                        // document.location.reload();
                        self.$inertia.visit(self.route('buy_airtime'));
                      }
                    });
                  }else if(response.invalid_amount){
                    Swal.fire({
                      title: 'Ooops',
                      text: "Invalid Amount Was Entered. Your Money Has Been Refunded",
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
              })
             
              
                
            }else{
              showNormalAndComboEtisalat(url,type,form_data,type_str,amount,mobile_number,amount_to_debit_user);
            }
          }
        });
      }else{
        var type = self.form.type;
        var quantity = self.form.quantity;
        if(type == "mtn" || type == "airtel" ){
          quantity = Number(quantity); 
          if(quantity >= 1){
            if(quantity <= 20){
              if(amount >= 100){
                if(amount <= 20000){
                  var amount_to_debit_user = amount - (0.02 * amount);
                  amount_to_debit_user = amount_to_debit_user * quantity;
                  
                  Swal.fire({
                    title: 'Info',
                   
                    html: "You Are About To Generate E-pin Of <span style='text-transform: capitalize;'>" + quantity + "</span> Unit(s)  Worth ₦" + self.addCommas(amount) + " Each. Are You Sure You Want To Proceed? <p><em>Note You Would Be Debited Of ₦" + self.addCommas(amount_to_debit_user) + "</em></p>",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes Proceed!',
                    cancelButtonText : "No"
                  }).then((result) => {
                    if (result.isConfirmed) {
                    
                      self.form.post(self.route('generate_vtu_epin'), {
                        preserveScroll: true,
                        onSuccess: (page) => {
                          
                          var response = JSON.parse(JSON.stringify(self.response_arr))
                          console.log(response)

                          
                          if(response.success && Array.isArray(response.epins) && response.amount != "" && response.epins_json != "") {
                            var epins = response.epins;
                            var epins_json = response.epins_json;
                            
                            self.epins_amount = response.amount;
                            self.epins_json = epins_json;
                            var text = "<p>Recharge card pin generated successfully <br> <h5>Details: </h5>Amount: ₦<em class='text-primary'>"+amount+"</em> <br>Quantity: <em class='text-primary'>"+quantity+"</em> Unit(s)</p>";
                            
                            var j = 0;
                            text += "<div class='container'>";
                            text += "<button @click='printRechargeEpins' style='margin-bottom: 30px;' class='btn btn-success' >Print E-Pins</button>";
                            for(var i = 0; i < epins.length; i++){
                              j++;
                              var pin = epins[i].pin;
                              var code = epins[i].code;
                              text += "<div class='row' style='margin-bottom: 5px;'>";
                              text += "<p class='col-1'>"+j+"</p>";
                              text += "<p class='col-5'>"+pin+"</p>";
                              text += "<p class='col-6'>"+code+"</p>";
                              text += "</div>";
                            }

                            text += "</div>";

                            Swal.fire({
                              title: 'Success',
                              html: text,
                              icon: 'info',
                              confirmButtonColor: '#3085d6'
                            }).then((result) => {
                              if (result.isConfirmed) {
                                self.$inertia.visit(self.route('buy_airtime'))
                              }
                            });
                          }else if(response.no_available_epin){
                            Swal.fire({
                              title: 'Ooops',
                              text: "No Available E-PIN For The Parameters You Selected Currently. Please Try Again Later.",
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
                }else{
                  Swal.fire({
                    title: 'Error',
                    text: "Amount Cannot Exceed ₦20000.",
                    icon: 'error'
                  })
                }
              }else{
                Swal.fire({
                  title: 'Error',
                  text: "Amount Must Be At Least ₦100",
                  icon: 'error'
                })
              }
            }else{
              Swal.fire({
                title: 'Error',
                text: "Quantity Cannot Exceed 20.",
                icon: 'error'
              })
            }
          }else{
            Swal.fire({
              title: 'Error',
              text: "Quantity Must Be At Least 1.",
              icon: 'error'
            })
          }
        }else{
          Swal.fire({
            title: 'Ooops',
            text: "Sorry E-PIN Recharge Is Not Available For The Selected Network.",
            icon: 'error'
          })
        }
      }
      
    },
    selectedAirtimeOperator(type) {
      this.form.type = type;
      $("#enter-amount-airtime-modal").modal({
        backdrop: false,
        show: true
      });
      console.log(type)

      
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
