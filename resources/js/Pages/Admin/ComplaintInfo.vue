<style>
  tbody tr{
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
            <div class="card" id="view-complaints-card" style="">
              <div class="card-header">
                <!-- <button @click="goBackFromViewComplaintsCard" class="btn btn-warning btn-round">Go Back</button> -->
                <h3>Complaint Info</h3>
              </div>
              <div class="card-body">
                <h5>Complaint Type: </h5>
                <h6>{{complaint_info.complaint_type}}</em></h6>


                
                <div class="" v-if="complaint_info.type == 'airtime' || complaint_info.type == 'cable' || complaint_info.type == 'electricity'">
                  <h5>Amount: </h5>
                  <h6>{{complaint_info.amount}}</em></h6>
                </div>

                <div class="" v-if="complaint_info.type == 'airtime' || complaint_info.type == 'data'">
                  <h5>Subscribed Number: </h5>
                  <h6>{{complaint_info.subscribed_number}}</em></h6>
                </div>

                <div class="" v-if="complaint_info.type == 'data'">
                  <h5>Data Amount: </h5>
                  <h6>{{complaint_info.data_amount}}</em></h6>
                </div>

                <div class="" v-if="complaint_info.type == 'airtime' || complaint_info.type == 'data' || complaint_info.type == 'cable' || complaint_info.type == 'electricity'">
                  <h5>Date Of Recharge: </h5>
                  <h6>{{complaint_info.date_of_recharge}}</em></h6>
                </div>


                <div class="" v-if="complaint_info.type == 'airtime' || complaint_info.type == 'data'">
                  <h5>Network: </h5>
                  <h6>{{complaint_info.network}}</em></h6>
                </div>

                <div class="" v-if="complaint_info.type == 'cable'">
                  <h5>Cable Type: </h5>
                  <h6>{{complaint_info.cable_type}}</em></h6>
                </div>

                <div class="" v-if="complaint_info.type == 'cable'">
                  <h5>Owners Name: </h5>
                  <h6>{{complaint_info.cable_owners_name}}</em></h6>
                </div>

                <div class="" v-if="complaint_info.type == 'cable'">
                  <h5>Owners Phone Number: </h5>
                  <h6>{{complaint_info.cable_phone_number}}</em></h6>
                </div>

                <div class="" v-if="complaint_info.type == 'cable'">
                  <h5>Smart Card Number: </h5>
                  <h6>{{complaint_info.iuc_number}}</em></h6>
                </div>

                <div class="" v-if="complaint_info.type == 'electricity'">
                  <h5>Disco: </h5>
                  <h6>{{complaint_info.disco}}</em></h6>
                </div>

                <div class="" v-if="complaint_info.type == 'electricity'">
                  <h5>Meter Type: </h5>
                  <h6>{{complaint_info.meter_type}}</em></h6>
                </div>

                <div class="" v-if="complaint_info.type == 'electricity'">
                  <h5>Meter Number: </h5>
                  <h6>{{complaint_info.meter_number}}</em></h6>
                </div>

                <div class="" v-if="complaint_info.type == 'pos'">
                  <h5>POS Type: </h5>
                  <h6>{{complaint_info.pos_type}}</em></h6>
                </div>

                <div class="" v-if="complaint_info.type == 'pos'">
                  <h5>POS Type: </h5>
                  <h6>{{complaint_info.pos_type}}</em></h6>
                </div>
             
                <h5>Whatsapp Number: </h5>
                <h6><a tartget="_blank" :href="'https://wa.me/'+ complaint_info.new_whatsapp_number">{{complaint_info.whatsapp_number}}</a></h6>

                            
                <button v-if="complaint_info.status == 'Pending'" class="btn btn-danger" @click="dismissThisComplaint(complaint_info.id,complaint_info.user_id)">
                Dismiss This Complaint
                </button>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div @click="goBackFromComplaintInfo">
        <floating-action-button :styles="'background: 9124a3;'" :title="'Go Back'">
          
          <i class="fas fa-arrow-left" style="font-size: 25px; color: #fff;"></i>
        </floating-action-button>
      </div>
      <footer class="footer">
        <div class="container-fluid">
          
        </div>
      </footer>
    </div>
  
  <!--   Core JS Files   -->
 
</template>

<script>

import Layout from '../../Shared/Layout'
import AdminLayout from '../../Shared/AdminLayout'
import Pagination from '../../Shared/Pagination'
import SearchFilter from '../../Shared/SearchFilter'
import FloatingActionButton from '../../Shared/FloatingActionButton'
import mapValues from 'lodash/mapValues'
import throttle from 'lodash/throttle'
import pickBy from 'lodash/pickBy'
import TextInput from '../../Shared/TextInput'
import SelectInput from '../../Shared/SelectInput'
import LoadingButton from '../../Shared/LoadingButton'


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


    complaint_info: Object,
  },
  data() {
    return {
      form: this.$inertia.form({
        
      }),
      dismiss_complaint_request: this.$inertia.form({
        id: ""
      }),
      show_other_overlay: false,
      
    }
  },
  
  mounted() {
    // this.$refs.SelectInput.$refs.select.$on('change', this.complaintTypeChange);
    console.log(this.previous_page)
    console.log(this.$page.props)
  },
  created() {
    $("body").removeClass("modal-open");
    console.log(this.complaint_info)
  },
  methods: {
    dismissThisComplaint(id,user_id){
      var self = this;
      self.dismiss_complaint_request.id = id;
      swal({
        title: 'Warning',
        text: "Are You Sure You Want To Dismiss This Complaint?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes Dismiss!',
        cancelButtonText : "No"
      }).then(function(){

        self.dismiss_complaint_request.post(self.route('dismiss_complaint'), {
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response)

            if(response.success){
                    
              $.notify({
                message:"Complaint Dismissed Successfully"
                },{
                  type : "success",
                  z_index: 20000,
              });
              self.show_other_overlay = true;
              setTimeout(function () {
                self.show_other_overlay = false;
                self.$inertia.visit(self.route('complaint_info',id));
              }, 2000);
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
      });
    },
    goBackFromComplaintInfo() {
      
      
      // if(this.previous_page != this.route('complaints')){
      //   this.$inertia.visit(this.route('complaints'));
      // }else{
      //   window.history.back()
      // }
      window.history.back()
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
