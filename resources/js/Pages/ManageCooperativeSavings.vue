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
            <inertia-link as="button" class="btn btn-primary" :href="route('view_savings_history')">View Savings History</inertia-link>
          </div>
          
          <div class="row justify-content-center" style="">
            
            <div class="card col-sm-12" id="main-card">
              <div class="card-header">
              </div>
              <div class="card-body">
                <!-- <p>Investing partners reap 5% of their savings monthly. <br>
                Partners invest in Tens"(N10k, N20k, N30k, 40k, N60k etc)" and decides savings period, eg 1, 3, 6, or 12 month, at 5% No Excuse Monthly interest to be paid directly to their Wallet.</p>
 -->
                <p>You can enter any amount you want to save at a particular time. It works by moving your funds from your mgr wallet to your cooperative account. You can decide a set time frame which will be the minimum time you can withdraw your savings or you can decide to select none meaning you can withdraw that saving anytime.</p>

                <form style="margin-top: 40px;" id="make-savings-form" @submit.prevent="submitMakesavingsForm">
                  <text-input v-model="make_savings_form.amount" :error="make_savings_form.errors.amount" type="number" label="Amount" id="amount" placeholder="" required/>

                  <h4 style="font-weight: bold; margin-bottom: 20px;">Select Time Frame</h4>

                  <div v-for="time_frame in time_frames" :key="time_frame.unit" class="form-check form-check-radio form-check-inline">
                    <label class="form-check-label">
                        <input v-model="make_savings_form.time_frame" class="form-check-input" type="radio" name="time_frame" :id="slugify(time_frame.unit)" :value="time_frame.unit" >
                        {{ time_frame.unit }}
                        <span class="circle">
                            <span class="check"></span>
                        </span>
                    </label>
                  </div>


                  <span class="text-danger" :error="make_savings_form.errors.time_frame"></span>

                  <!-- <select v-model="make_savings_form.time_frame" :error="make_savings_form.errors.time_frame" id="type"  class="form-control" style="margin-top: -10px;">
                      
                    <option v-for="time_frame in time_frames" :key="time_frame.unit" value="time_frame.unit">{{time_frame.unit}}</option>
                    
                  </select> -->

                  <loading-button :loading="make_savings_form.processing" class="btn btn-primary col-12" type="submit">Submit</loading-button>
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


    allow_savingss: [Boolean],

    

  },
  data() {
    return {
      time_frames: [
        {
          unit: 'None'
        },
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

      make_savings_form: this.$inertia.form({
        amount: null,
        time_frame: 'None',
        time_frame_num: 1
      }),

    }
  },
 
  mounted() {
    
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    submitMakesavingsForm(){
      var self = this;
      console.log(self.make_savings_form.time_frame)
      console.log(self.make_savings_form.amount % 10000);
      var amount = Number(self.make_savings_form.amount);
      var time_frame = self.make_savings_form.time_frame;
      var time_frame_first_char = time_frame.charAt(0);
      self.make_savings_form.time_frame_num = time_frame_first_char;

      var text = "";
      if(time_frame_first_char == "N"){
        text = "You are about to make a savings of <em class='text-primary'>₦" + addCommas(amount) + "</em>, withdrawable anytime. Are you sure you want to proceed with this savings?";
      }else{
        text = "You are about to make a savings of <em class='text-primary'>₦" + addCommas(amount) + "</em>, withdrawable after " + time_frame + ". Are you sure you want to proceed with this savings?";
      }

      // if(self.make_savings_form.amount % 10000 == 0){
        swal({
          title: 'Proceed?',
          text: text,
          type: 'info',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#FF0000',
          confirmButtonText: 'Yes Proceed',
          cancelButtonText : "Cancel",
          
        }).then(function(){
          self.make_savings_form.post(self.route('process_make_coop_savings'), {
            preserveScroll: true,
            onSuccess: (page) => {
              
              var response = JSON.parse(JSON.stringify(self.response_arr))
              console.log(response)

              if(response.success){
                text = "";
                if(time_frame.charAt(0) == "N"){
                  text = "You have successfully made a savings of <em class='text-primary'>₦" + addCommas(amount) + "</em>, withdrawable anytime.";
                }else{
                  text = "You have successfully made a savings of <em class='text-primary'>₦" + addCommas(amount) + "</em>, withdrawable after " + time_frame + ".";
                }

                swal({
                  title: 'Savings Made Successfully',
                  text: text,
                  type: 'success',
                  allowOutsideClick: false,
                }).then(function(){
                  self.$inertia.visit(self.route('manage_cooperative_savings'));
                });
              }else if(response.invalid_time_frame){
                swal({
                  title: 'Invalid Time Frame',
                  text: "Savings Time Frame be 1, 3, 6, or 12 months Or None",
                  type: 'warning',
                })
              }else if(response.inv_not_allow){
                swal({
                  title: 'Savings Not Available',
                  text: "Sorry the admin has temporarily disabled savingss currently. Please try again later",
                  type: 'warning',
                })
              }else if(response.not_bouyant){
                swal({
                  title: 'Ooops',
                  text: "You do not have enough funds for this. Please credit your account or adjust the savings amount.",
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
      // }else{
      //   swal({
      //     title: 'Invalid Amount Entered',
      //     text: "Amount entered must be divisible by 10,000. e.g 10k,20k,30k,40k etc. Amount must also be at least 10,000.",
      //     type: 'warning',
      //   })
      // }

      
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
