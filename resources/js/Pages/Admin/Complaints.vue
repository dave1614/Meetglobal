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
            <div class="card" id="main-card">
                <div class="card-header">
                  
                  <h3 class="card-title"></h3>
                </div>
                <div class="card-body">
                
                  <div class='row justify-content-center'>
                    
                    <form id="filter-rows-form" class="col-sm-12" style="margin-bottom: 35px;">  
                      <div class="form-row">
                        
                        <select-input v-model="filter_rows_form.length" :error="filter_rows_form.errors.length" class="col-sm-4" label="Length">
                          <option value="10">10</option>
                          <option value="20">20</option>
                          <option value="50">50</option>
                          <option value="100">100</option>
                        </select-input>

                        <text-input v-model="filter_rows_form.user_name" :error="filter_rows_form.errors.user_name" type="text" label="User Name" id="user_name" placeholder="" class="col-sm-4"/>

                        <text-input v-model="filter_rows_form.whatsapp_number" :error="filter_rows_form.errors.whatsapp_number" type="number" label="Whatsapp Number" id="whatsapp_number" placeholder="" class="col-sm-4"/>

                        <div class="col-sm-6" style="">
                          <h5 style="">Choose Complain Type: </h5>
                          <select v-model="filter_rows_form.type" :error="filter_rows_form.errors.type" id="type" class="form-control" style="margin-top: -10px;">
                            <!-- <option :value="null" /> -->
                            <option value="all" selected>All</option>
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
                        </div>

                        <div class="col-sm-6">
                          <p>Status: </p>
                          <div class="form-check form-check-radio form-check-inline">
                            <label class="form-check-label">
                              <input v-model="filter_rows_form.status" class="form-check-input" type="radio" id="dismissed" value="dismissed" name="status">
                              Dismissed
                              <span class="circle">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>

                          <div class="form-check form-check-radio form-check-inline">
                            <label class="form-check-label">
                              <input v-model="filter_rows_form.status" class="form-check-input" type="radio" id="pending" value="pending" name="status"> 
                                Pending
                              <span class="circle">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>

                          <div class="form-check form-check-radio form-check-inline">
                            <label class="form-check-label">
                              <input v-model="filter_rows_form.status" class="form-check-input" type="radio" id="all" value="all" name="status" checked> 
                              All
                              <span class="circle">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>

                        </div>

                        <text-input v-model="filter_rows_form.date_of_recharge" :error="filter_rows_form.errors.date_of_recharge" type="date" label="Date Of Recharge" id="date_of_recharge" placeholder="" class="col-sm-4"/>

                        <text-input v-model="filter_rows_form.date" :error="filter_rows_form.errors.date" type="date" label="Complaint Date" id="date" placeholder="" class="col-sm-4"/>

                        <text-input v-model="filter_rows_form.dismissed_date_time" :error="filter_rows_form.errors.dismissed_date_time" type="date" label="Dismissed Date" id="dismissed_date_time" placeholder="" class="col-sm-4"/>

                        <text-input v-model="filter_rows_form.start_date" :error="filter_rows_form.errors.start_date" type="date" label="Start Date" id="start_date" placeholder="" class="col-sm-6"/>

                        <text-input v-model="filter_rows_form.end_date" :error="filter_rows_form.errors.end_date" type="date" label="End Date" id="end_date" placeholder="" class="col-sm-6"/>
                       
                      </div>

                      <div class="row">
                        
                        <button type='button' class='btn btn-secondary col-sm-5 btn-outline-primary' @click="clearFilterRowsForm"><i class="fa fa-close"></i>&nbsp;&nbsp;Clear</button>
                      </div>
                    </form>
                  </div>

                  <div class="table-div material-datatables table-responsive" style="" v-if="all_complaints.data.length > 0">
                    <table id="all-registered-users-table" class="table table-test table-striped table-bordered nowrap hover display" cellspacing="0" width="100%" style="width:100%">
                      <thead class="thead-light">
                        <tr>
                          <th>#</th>
                          <th>Actions</th>
                          <th>Username</th>
                          <th>Complaint Type</th>
                          <th>Status</th>
                          <th>Whatsapp Number</th>
                          <th>Date Of Recharge</th>
                          <th>Complaint Date/Time</th>
                          <th>Dismissed Date/Time</th>

                        </tr>
                      </thead>
                      <tbody>

                                              
                        
                        <tr v-for="complaint in all_complaints.data" :key="complaint.index" @click="viewComplaintInfo(complaint.id)">
                          <td>{{complaint.index}}. </td>

                            
                            <td class="td-actions text-center" v-if="complaint.status == 'Pending'">
                              
                              <button type="button" rel="tooltip" title="Dismiss This Complaint" class="btn btn-danger btn-link btn-lg" @click.stop="dismissThisComplaint(complaint.id,complaint.user_id)">
                                <i style="font-size: 20px;" class="fas fa-times"></i>
                                <div class="ripple-container"></div>
                              </button>
                            </td>
                            
                            <td v-else></td>
                            

                            <td><inertia-link class="nav-link" :href="route('user_profile', complaint.user_slug)">{{complaint.user_name}}</inertia-link></td>

                            <td>{{complaint.complaint_type}}</td>
                            <td>{{complaint.status}}</td>
                            <td>{{complaint.whatsapp_number}}</td>
                            <td>{{complaint.date_of_recharge}}</td>
                            <td>{{complaint.date_time}}</td>
                            <td>{{complaint.dismissed_date_time}}</td>
                        </tr>
                              
                      
                      </tbody>
                    </table>
                  </div>
                  <h4 v-else class="text-warning">No Data To Display</h4>
                  <pagination class="mt-6" :links="all_complaints.links" style="margin-top:30px;"/>
                  <p :if="all_complaints.data.length > 0" style="margin-top:30px;">{{all_complaints.total}} Total Entries</p>
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


    all_complaints: Object,
    user_name: String,
    whatsapp_number: String,
    type: String,
    status: String,
    date_of_recharge: String,
    date: String,
    dismissed_date_time: String,
    length: [Number,String],
    start_date: String,
    end_date: String,

  },
  data() {
    return {
      filter_rows_form: this.$inertia.form({
        length: this.length,
        user_name: this.user_name,
        whatsapp_number: this.whatsapp_number,
        type: this.type,
        status: this.status,
        date_of_recharge: this.date_of_recharge,
        date: this.date,
        dismissed_date_time: this.dismissed_date_time,
        start_date: this.start_date,
        end_date: this.end_date,

      }),
      dismiss_complaint_request: this.$inertia.form({
        id: ""
      }),
      show_other_overlay: false,

    }
  },
  watch: {
    filter_rows_form: {
      deep: true,
      handler: throttle(function() {
        this.$inertia.get(this.route('complaints'), pickBy(this.filter_rows_form), { preserveState: true,preserveScroll: true })
      }, 150),
    },
  },
  mounted() {
    // this.$refs.SelectInput.$refs.select.$on('change', this.complaintTypeChange);
    console.log(this.$page.props)
  },
  created() {
    $("body").removeClass("modal-open");
    
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
                // self.$inertia.visit(self.route('complaints'));
                document.location.reload()
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
    viewComplaintInfo(id) {
      var self = this;
      self.$inertia.visit(self.route('complaint_info',id));

    },

    clearFilterRowsForm() {
      this.filter_rows_form.type = "all";
      this.filter_rows_form.status = "all";
      
      this.filter_rows_form.user_name = "";
      this.filter_rows_form.whatsapp_number = "";
      this.filter_rows_form.date_of_recharge = "";
      this.filter_rows_form.date = "";
      this.filter_rows_form.dismissed_date_time = "";
      this.filter_rows_form.length = 10;
      this.filter_rows_form.start_date = "";
      this.filter_rows_form.end_date = "";
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
