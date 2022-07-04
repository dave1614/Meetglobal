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
            <inertia-link as="button" class="btn btn-primary" :href="route('view_investment_history')">View Investment History</inertia-link>
          </div>
          
          <div class="row justify-content-center" style="">
            
            <div class="card col-sm-12" id="main-card">
              <div class="card-header">
              </div>
              <div class="card-body">
                <p>Investing partners reap 5% of their investment monthly. <br>
                Partners invest in Tens"(N10k, N20k, N30k, 40k, N60k etc)" and decides investment period, eg 1, 3, 6, or 12 month, at 5% No Excuse Monthly interest to be paid directly to their Wallet.</p>

                <form style="margin-top: 40px;" id="make-investment-form" @submit.prevent="submitMakeInvestmentForm">
                  <text-input v-model="make_investment_form.amount" :error="make_investment_form.errors.amount" type="number" label="Amount" id="amount" placeholder="" required/>

                  <h4 style="font-weight: bold; margin-bottom: 20px;">Select Investment Period</h4>

                  <div v-for="duration in durations" :key="duration.unit" class="form-check form-check-radio form-check-inline">
                    <label class="form-check-label">
                        <input v-model="make_investment_form.duration" class="form-check-input" type="radio" name="duration" :id="slugify(duration.unit)" :value="duration.unit" >
                        {{ duration.unit }}
                        <span class="circle">
                            <span class="check"></span>
                        </span>
                    </label>
                  </div>


                  <span class="text-danger" :error="make_investment_form.errors.duration"></span>

                  <!-- <select v-model="make_investment_form.duration" :error="make_investment_form.errors.duration" id="type"  class="form-control" style="margin-top: -10px;">
                      
                    <option v-for="duration in durations" :key="duration.unit" value="duration.unit">{{duration.unit}}</option>
                    
                  </select> -->

                  <loading-button :loading="make_investment_form.processing" class="btn btn-primary col-12" type="submit">Submit</loading-button>
                </form>
                
               
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


    allow_investments: [Boolean],

    

  },
  data() {
    return {
      durations: [
        {
          unit: '1 month'
        },
        {
          unit: '3 months'
        },
        {
          unit: '6 months'
        },
        {
          unit: '12 months'
        },
      ],
      
      show_other_overlay: false,

      make_investment_form: this.$inertia.form({
        amount: null,
        duration: '1 month',
        duration_num: 1
      }),

    }
  },
 
  mounted() {
    if(!this.allow_investments){
      swal({
        title: 'Investments Not Available',
        text: "Sorry the admin has temporarily disabled investments currently. Please try again later",
        type: 'warning',
      })
    }
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    submitMakeInvestmentForm(){
      var self = this;
      console.log(self.make_investment_form.duration)
      console.log(self.make_investment_form.amount % 10000);
      var amount = Number(self.make_investment_form.amount);
      var duration = self.make_investment_form.duration;
      var duration_mnths = duration.charAt(0);
      var credited_amount = (0.05 * amount) * duration_mnths;
      credited_amount = credited_amount + amount;
      self.make_investment_form.duration_num = duration_mnths;

      if(self.make_investment_form.amount % 10000 == 0){
        swal({
          title: 'Proceed?',
          text: "You are about to make an investment of <em class='text-primary'>₦" + addCommas(amount) + "</em> for a period of " + duration + ". You will be credited <em class='text-primary'>₦" + addCommas(credited_amount) + " </em> automatically at the end of this period. Are you sure you want to proceed with this investment?",
          type: 'info',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#FF0000',
          confirmButtonText: 'Yes Proceed',
          cancelButtonText : "Cancel",
          
        }).then(function(){
          self.make_investment_form.post(self.route('process_make_coop_investment'), {
            preserveScroll: true,
            onSuccess: (page) => {
              
              var response = JSON.parse(JSON.stringify(self.response_arr))
              console.log(response)

              if(response.success){
                
                swal({
                  title: 'Investment Made Successfully',
                  text: "You have successfully made an investment of <em class='text-primary'>₦" + addCommas(amount) + "</em> for a period of " + duration + ". You will be credited <em class='text-primary'>₦" + addCommas(credited_amount) + " </em> automatically at the end of this period. ",
                  type: 'success',
                  allowOutsideClick: false,
                }).then(function(){
                  self.$inertia.visit(self.route('manage_investments'));
                });
              }else if(response.invalid_amount){
                swal({
                  title: 'Invalid Amount Entered',
                  text: "Amount entered must be divisible by 10,000. e.g 10k,20k,30k,40k etc. Amount must also be at least 10,000.",
                  type: 'warning',
                })
              }else if(response.invalid_duration){
                swal({
                  title: 'Invalid investment period',
                  text: "Investment period must be 1, 3, 6, or 12 months",
                  type: 'warning',
                })
              }else if(response.inv_not_allow){
                swal({
                  title: 'Investments Not Available',
                  text: "Sorry the admin has temporarily disabled investments currently. Please try again later",
                  type: 'warning',
                })
              }else if(response.not_bouyant){
                swal({
                  title: 'Ooops',
                  text: "You do not have enough funds for this. Please credit your account or adjust the investment amount.",
                  type: 'error'
                })
              }else if(response.not_registered){
                swal({
                  title: 'Ooops',
                  text: "Seems You're Not Registered In The Cooperative yet. Please Register.",
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
        });
      }else{
        swal({
          title: 'Invalid Amount Entered',
          text: "Amount entered must be divisible by 10,000. e.g 10k,20k,30k,40k etc. Amount must also be at least 10,000.",
          type: 'warning',
        })
      }

      
    },

    slugify(str){
      return str.replace(/\s+/g, '-').toLowerCase();
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
