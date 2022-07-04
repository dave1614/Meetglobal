<style>
  tr{
    cursor: pointer;
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
          
          <div class="row justify-content-center" style="">

            <div class="card col-sm-7" id="main-card">
              <div class="card-header">
              </div>
              <div class="card-body">
                
                <form id="make-complaint-form" @submit.prevent="submitMakeComplaintForm">  
                  <div class="" style="">
                    <h4 style="">Choose Complain Type: </h4>
                    <select v-model="make_complaint_form.complaint_type" :error="make_complaint_form.errors.complaint_type" id="type" @change="complaintTypeChange" class="form-control" style="margin-top: -10px;">
                      <!-- <option :value="null" /> -->
                      <option value="registration">Registration</option>
                      <option value="commission">Commission</option>
                      <option value="airtime">Airtime</option>
                      <option value="data">Data Bundle</option>
                      <option value="cable">Cable TV</option>
                      <option value="electricity">Electricity</option>
                      <option value="pos">POS</option>
                      <option value="mini_importation">Mini Importation</option>
                      <option value="smart_business_loan">Smart Business Loan</option>
                      <option value="withdrawal">Withdrawal</option>
                      <option value="seminar_invite">Invite For Seminar</option>
                      <option value="flyers_and_tools">Flyers And Other Tools</option>
                    </select>
                    <div v-if="make_complaint_form.errors.complaint_type" class="form-error">{{ make_complaint_form.errors.complaint_type }}</div>
                  </div>

                  <div id="airtime-div" :class="airtime_div_open ? '' : 'hide' ">
                    <text-input v-model="make_complaint_form.network" :error="make_complaint_form.errors.network" type="text" label="Network" id="network" placeholder=""/>
                    <text-input v-model="make_complaint_form.subscribed_number" :error="make_complaint_form.errors.subscribed_number" type="number" label="Subscribed Number" id="subscribed_number" placeholder=""/>
                    <text-input v-model="make_complaint_form.amount" :error="make_complaint_form.errors.amount" type="number" label="Amount (₦)" id="amount" placeholder=""/>

                    <text-input v-model="make_complaint_form.date_of_recharge" :error="make_complaint_form.errors.date_of_recharge" type="date" label="Date Of Recharge" id="date_of_recharge" placeholder=""/>
                  </div>

                  <div id="data-div" :class="data_div_open ? '' : 'hide' ">
                    

                    <text-input v-model="make_complaint_form.network" :error="make_complaint_form.errors.network" type="text" label="Network" id="network" placeholder=""/>

                    <text-input v-model="make_complaint_form.subscribed_number" :error="make_complaint_form.errors.subscribed_number" type="number" label="Subscribed Number" id="subscribed_number" placeholder=""/>

                    <text-input v-model="make_complaint_form.data_amount" :error="make_complaint_form.errors.data_amount" type="text" label="Data Amount" id="data_amount" placeholder=""/>

                    <text-input v-model="make_complaint_form.date_of_recharge" :error="make_complaint_form.errors.date_of_recharge" type="date" label="Date Of Recharge" id="date_of_recharge" placeholder=""/>
                  </div>

                  <div id="cable-div" :class="cable_div_open ? '' : 'hide' ">
                    <text-input v-model="make_complaint_form.cable_type" :error="make_complaint_form.errors.cable_type" type="text" label="Cable Type" id="cable_type" placeholder=""/>

                    <text-input v-model="make_complaint_form.amount" :error="make_complaint_form.errors.amount" type="number" label="Amount (₦)" id="amount" placeholder=""/>

                    <text-input v-model="make_complaint_form.cable_owners_name" :error="make_complaint_form.errors.cable_owners_name" type="text" label="Owners Name" id="cable_owners_name" placeholder=""/>

                    <text-input v-model="make_complaint_form.cable_phone_number" :error="make_complaint_form.errors.cable_phone_number" type="number" label="Owners Phone Number" id="cable_phone_number" placeholder=""/>

                    
                    <text-input v-model="make_complaint_form.smart_card_number" :error="make_complaint_form.errors.smart_card_number" type="number" label="IUC Or Smartcard Number" id="smart_card_number" placeholder=""/>

                    <text-input v-model="make_complaint_form.date_of_recharge" :error="make_complaint_form.errors.date_of_recharge" type="date" label="Date Of Recharge" id="date_of_recharge" placeholder=""/>
                  </div>

                  <div id="electricity-div" :class="electricity_div_open ? '' : 'hide' ">

                    <text-input v-model="make_complaint_form.disco" :error="make_complaint_form.errors.disco" type="text" label="Disco" id="disco" placeholder=""/>

                    <text-input v-model="make_complaint_form.meter_type" :error="make_complaint_form.errors.meter_type" type="text" label="Meter Type (prepaid or postpaid)" id="meter_type" placeholder=""/>

                    <text-input v-model="make_complaint_form.meter_number" :error="make_complaint_form.errors.meter_number" type="number" label="Meter Number" id="meter_number" placeholder=""/>

                    <text-input v-model="make_complaint_form.amount" :error="make_complaint_form.errors.amount" type="number" label="Amount (₦)" id="amount" placeholder=""/>

                    <text-input v-model="make_complaint_form.date_of_recharge" :error="make_complaint_form.errors.date_of_recharge" type="date" label="Date Of Recharge" id="date_of_recharge" placeholder=""/>
                  </div>

                  <div id="pos-div" :class="pos_div_open ? '' : 'hide' ">
                    <text-input v-model="make_complaint_form.pos_type" :error="make_complaint_form.errors.pos_type" type="text" label="POS Type" id="pos_type" placeholder=""/>
                  </div>

                  <text-input v-model="make_complaint_form.whatsapp_number" :error="make_complaint_form.errors.whatsapp_number" type="number" label="Your Whatsapp Number" id="whatsapp_number" placeholder=""/>

                  
                  <loading-button :loading="make_complaint_form.processing" class="btn btn-primary col-12" type="submit">Submit</loading-button>
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

    


  },
  data() {
    return {
      make_complaint_form: this.$inertia.form({
        complaint_type: "registration",
        network: "",
        subscribed_number: "",
        amount: "",
        date_of_recharge: "",
        data_amount: "",
        cable_type: "",
        cable_owners_name: "",
        cable_phone_number: "",
        smart_card_number: "",
        disco: "",
        meter_type: "",
        meter_number: "",
        pos_type: "",
        whatsapp_number: "",



      }),
      show_other_overlay: false,

      airtime_div_open: false,
      data_div_open: false,
      cable_div_open: false,
      electricity_div_open: false,
      pos_div_open: false,

    }
  },
  
  mounted() {
    // this.$refs.SelectInput.$refs.select.$on('change', this.complaintTypeChange);
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {

    submitMakeComplaintForm() {
      var self = this;
      self.make_complaint_form.post(self.route('submit_make_complaint_form'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
                  
            $.notify({
              message:"Your Complaint Has Been Successfully Submitted To Admin. Your Complaint Will Soon Be Addressed"
              },{
                type : "success",
                z_index: 20000,
            });
            self.show_other_overlay = true;
            setTimeout(function () {
              self.show_other_overlay = false;
              self.$inertia.visit(self.route('make_complaint'));
            }, 2000);
          }else if(response.invalid_complaint_type){
            swal({
              title: 'Error!',
              text: "Please Select A Complaint Type",
              type: 'error',
              
            });
          }else{

            $.notify({
            message:"Something Went Wrong. Please Try Again"
            },{
              type : "warning",
              z_index: 20000,  
            });
          }
        },onError: (errors) => {
          console.log(self.make_complaint_form.errors)
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

    complaintTypeChange() {
      var self = this;
      var selected = self.make_complaint_form.complaint_type;

      self.make_complaint_form.errors = ""
      self.airtime_div_open = false;
      self.data_div_open = false;
      self.cable_div_open = false;
      self.electricity_div_open = false;
      self.pos_div_open = false;
      if(selected == "airtime"){
        
        self.airtime_div_open = true;
      }else if(selected == "data"){
        self.data_div_open = true;
      }else if(selected == "cable"){
        self.cable_div_open = true;
      }else if(selected == "electricity"){
        
        self.electricity_div_open = true;
      }else if(selected == "pos"){
        self.pos_div_open = true;
      }
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
