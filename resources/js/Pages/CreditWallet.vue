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
</style>

<template>
    <div class="content">
      <div id="other-overlay" :style="show_other_overlay == true ? 'display: block;' : 'display: none;' "></div>
      <div class="">
        <div class="container-fluid">
          <div class="text-right">
            <inertia-link as="button" class="btn btn-primary" :href="route('wallet_credit_history')"><i style="font-size: 13px;" class="fas fa-history"></i>&nbsp;&nbsp;Wallet Credit History</inertia-link>
          </div>
          <div class="row justify-content-center" style="">
            
            <div class="col-sm-7">

             
              <div class="card" id="main-card">
                <div class="card-header">                  
                  <h3 class="card-title">Wallet Balance: <em class="text-primary" v-html="'₦' + balance_str"></em></h3>
                </div>
                <div class="card-body">
                  <div class="" v-if="providus_account_number != null && providus_account_name != null">
                    <h4 class="text-center">Personalized Account Funding</h4>
                    <p class="text-center">From ₦1 to ₦9,999 attracts ₦20 charge</p>
                    <p class="text-center">₦10,000 or greater attracts ₦70 charge</p>

                    <table class="table">
                      <tbody>
                        <tr>
                          <td>Bank Name</td>
                          <td>Providus Bank</td>
                        </tr>
                        <tr>
                          <td>A/c Name</td>
                          <td><em class="text-primary">{{providus_account_name}}</em></td>
                        </tr>
                        <tr>
                          <td>A/c No</td>
                          <td><em class="text-primary">{{providus_account_number}}</em></td>
                        </tr>
                        
                      </tbody>
                    </table>
                  </div>

                  <h4 class="text-center" style="margin-top: 40px; margin-bottom: 20px;">OR</h4>


                  <h4 class="text-center" style="margin-top: 30px; margin-bottom: 30px; text-transform: uppercase;">ADMIN WALLET FUNDING</h4>
                  <p class="text-center">Deposit To De Meet Global Resources: </p>

                  <table class="table">
                    <tbody>
                      <tr>
                        <td>Bank Name</td>
                        <td>GTBank</td>
                      </tr>
                       <tr>
                        <td>A/c No</td>
                        <td><em class="text-primary">0597824729</em></td>
                      </tr>
                      <tr>
                        <td>Bank Name</td>
                        <td>Access Bank</td>
                      </tr>
                       <tr>
                        <td>A/c No</td>
                        <td><em class="text-primary">0103304419</em></td>
                      </tr>
                    </tbody>
                  </table>

                  <h4 class="text-center" style="margin-top: 40px; margin-bottom: 20px;">Submit Proof Of Payment</h4>

                  <form id="proof-of-payment-form" @submit.prevent="submitProofOfPaymentForm">

                    <text-input v-model="proof_of_payment_form.amount" :error="proof_of_payment_form.errors.amount" step="any" type="number" label="" id="amount" placeholder="Enter Amount"/>

                    <text-input v-model="proof_of_payment_form.depositors_name" :error="proof_of_payment_form.errors.depositors_name" type="text" label="" id="depositors_name" placeholder="Enter Depositors Name"/>

                    <text-input v-model="proof_of_payment_form.date_of_payment" :error="proof_of_payment_form.errors.date_of_payment" type="date" label="" id="date_of_payment" placeholder="Select Date Of Payment"/>

                    <file-input v-model="proof_of_payment_form.image" :error="proof_of_payment_form.errors.image" label="Choose Image" id="image"/>


                    <!-- <progress v-if="proof_of_payment_form.progress" :value="proof_of_payment_form.progress.percentage" max="100">
                      {{ proof_of_payment_form.progress.percentage }}%
                    </progress> -->
                    <div class="progress">
                      <div v-if="proof_of_payment_form.progress" class="progress-bar progress-bar-primary" role="progressbar" :aria-valuenow="proof_of_payment_form.progress.percentage"
                      aria-valuemin="0" aria-valuemax="100" :style="'width:'+ proof_of_payment_form.progress.percentage +'%'">
                        <span class="sr-only" v-html="proof_of_payment_form.progress.percentage + '% Complete'"></span>
                      </div>
                    </div>
                    <loading-button :loading="proof_of_payment_form.processing" class="btn btn-primary col-12" type="submit">Submit</loading-button>
                  </form>


                </div>
              </div>
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
import FileInput from '../Shared/FileInput'
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
    FileInput,

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

    total_income: [String,Number],
    withdrawn: [String,Number],
    balance: [String,Number],
    balance_str: [String,Number],
    providus_account_number: [String,Number],
    providus_account_name: [String,Number],

  },
  data() {
    return {
      proof_of_payment_form: this.$inertia.form({
        amount: "",
        depositors_name: "",
        date_of_payment: "",
        image: null

      }),
      show_other_overlay: false,

      

    }
  },
 
  mounted() {
    
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    submitProofOfPaymentForm(id) {
      var self = this;
      

      self.proof_of_payment_form.post(self.route('submit_proof_of_payment_to_admin_inside_app'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
                  
            $.notify({
            message:"Your Request Has Been Sent To The Admin. You Will Soon Be Credited."
            },{
              type : "success",
              z_index: 20000,   
            });
            setTimeout(function () {
              document.location.reload();
            }, 1500);
          }else if(response.empty){
            swal({
              title: 'Ooops',
              text: "You Must Select An Image To Upload For Payment Proof",
              type: 'error',                              
            })
          }else if(!response.only_one_image){
            swal({
              title: 'Ooops',
              text: "You Can Only Select One Image To Upload As Payment Proof",
              type: 'error',                              
            })
          }else if(response.errors != ""){
            
            swal({
              title: 'Error',
              html: response.errors,
              type: 'error',
              
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
