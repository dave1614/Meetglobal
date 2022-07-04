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

                        <text-input v-model="filter_rows_form.user_name" :error="filter_rows_form.errors.user_name" type="text" label="User name" id="user_name" placeholder="" class="col-sm-4"/>

                        <text-input v-model="filter_rows_form.amount" :error="filter_rows_form.errors.amount" type="number" label="Amount" id="amount" placeholder="" class="col-sm-4" step="any"/>

                        <text-input v-model="filter_rows_form.duration" :error="filter_rows_form.errors.duration" type="number" label="Investment Period" id="total_earnings" placeholder="e.g 1 for 1 month" class="col-sm-4"/>

                        
                        <div class="col-sm-6">
                          <p>Status: </p>
                          <div class="form-check form-check-radio form-check-inline">
                            <label class="form-check-label">
                              <input v-model="filter_rows_form.status" class="form-check-input" type="radio" id="dismissed" value="pending" name="status">
                              Pending
                              <span class="circle">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>

                          <div class="form-check form-check-radio form-check-inline">
                            <label class="form-check-label">
                              <input v-model="filter_rows_form.status" class="form-check-input" type="radio" id="debited" value="settled" name="status">
                              Settled
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
                        
                        <text-input v-model="filter_rows_form.settled_amount" :error="filter_rows_form.errors.settled_amount" type="number" label="Settled Amount" id="settled_amount" placeholder="" class="col-sm-4" step="any"/>
                          
                        <text-input v-model="filter_rows_form.date" :error="filter_rows_form.errors.date" type="date" label="Date" id="date" placeholder="" class="col-sm-4"/>

                        <text-input v-model="filter_rows_form.settled_date_time" :error="filter_rows_form.errors.settled_date_time" type="date" label="Settled Date" id="settled_date_time" placeholder="" class="col-sm-4"/>


                        <text-input v-model="filter_rows_form.start_date" :error="filter_rows_form.errors.start_date" type="date" label="Start Date" id="start_date" placeholder="" class="col-sm-6"/>

                        <text-input v-model="filter_rows_form.end_date" :error="filter_rows_form.errors.end_date" type="date" label="End Date" id="end_date" placeholder="" class="col-sm-6"/>
                       
                      </div>

                      <div class="row">
                        
                        <button type='button' class='btn btn-secondary col-sm-5 btn-outline-primary' @click="clearFilterRowsForm"><i class="fa fa-close"></i>&nbsp;&nbsp;Clear</button>
                      </div>
                    </form>
                  </div>

                  <div>
                    <p>Enable Investments: </p>
                    <div class="form-check form-check-radio form-check-inline">
                      <label class="form-check-label">
                        <input @change="changeEnableInvestment" v-model="enable_investment_request.status" class="form-check-input" type="radio" id="yes" value="yes" name="enable_investment">
                        Yes
                        <span class="circle">
                          <span class="check"></span>
                        </span>
                      </label>
                    </div>

                    <div class="form-check form-check-radio form-check-inline">
                      <label class="form-check-label">
                        <input @change="changeEnableInvestment" v-model="enable_investment_request.status" class="form-check-input" type="radio" id="no" value="no" name="enable_investment">
                        No
                        <span class="circle">
                          <span class="check"></span>
                        </span>
                      </label>
                    </div>

                  </div>

                  <div class="table-div material-datatables table-responsive" style="" v-if="all_history.data.length > 0">
                    <table id="all-registered-users-table" class="table table-test table-striped table-bordered nowrap hover display" cellspacing="0" width="100%" style="width:100%">
                      <thead class="thead-light">
                        <tr>
                          <th>#</th>
                          <th>Username</th>
                          <th>Status</th>
                          <th>Amount</th>
                          <th>Investm. Period</th>
                          <th>Settled Amt.</th>
                          <th>Settled Date/Time</th>
                          <th>Date / Time</th>

                        </tr>
                      </thead>
                      <tbody>

                                              
                        
                        <tr v-for="row in all_history.data" :key="row.index">
                          <td>{{row.index}}. </td>
                          <td><inertia-link class="nav-link" :href="route('user_profile', row.user_slug)">{{row.user_name}}</inertia-link></td>

                          <td v-if="row.settled == 1"><em class="text-primary">Settled</em></td>
                          <td v-if="row.settled == 0"><em class="text-danger">Pending</em></td>
                          <td v-html="addCommas(row.amount)"></td>
                          <td v-html="row.duration + ' month(s)'"></td>
                          <td><span v-if="row.settled_amount > 0" v-html="addCommas(row.settled_amount)"></span></td>
                          <td v-html="row.settled_date_time"></td>
                          <td v-html="row.date + ' ' + row.time"></td>
                        </tr>
                              
                      
                      </tbody>
                    </table>
                  </div>
                  <h4 v-else class="text-warning">No Data To Display</h4>
                  <pagination class="mt-6" :links="all_history.links" style="margin-top:30px;"/>
                  <p :if="all_history.data.length > 0" style="margin-top:30px;">{{all_history.total}} Total Entries</p>

                  <h4>Total Pending Amount: <em class="text-primary">{{total_pending_amount}}</em></h4>
                </div>
              </div>

          </div>
        </div>
      </div>

      <div class="modal fade" data-backdrop="static" id="credit-user-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title text-center" style="text-transform: capitalize;">Enter Amount To Credit User</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="modal-body">
              <h5><em class="text-primary wallet-balance" v-html="credit_user_modal_title"></em></h5>
              
              <form id="credit-user-form" @submit.prevent="submitCreditUserForm">  
            

                <text-input v-model="credit_user_form.amount" :error="credit_user_form.errors.amount" type="number" label="Amount" id="amount" placeholder="" step="any"/>

                <loading-button :loading="credit_user_form.processing" class="btn btn-primary col-12" type="submit">Submit</loading-button>
              </form>
            </div>

            

            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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


    all_history: Object,

    user_name: [Number,String],
    amount: [Number,String],
    duration: [Number,String],
    status: [Number,String],
    settled_amount: [Number,String],
    settled_date_time: [Number,String],
    total_pending_amount: [Number,String],
    allow_investments: [String],
    
    date: String,
    length: [Number,String],
    start_date: String,
    end_date: String,
  },
  data() {
    return {
      filter_rows_form: this.$inertia.form({
        user_name: this.user_name,
        amount: this.amount,
        duration: this.duration,
        status: this.status,
        settled_amount: this.settled_amount,
        settled_date_time: this.settled_date_time,
        
        date: this.date,
        start_date: this.start_date,
        end_date: this.end_date,

      }),
      enable_investment_request: this.$inertia.form({
        status: this.allow_investments
      }),
      get_user_info_request: this.$inertia.form({
        show_records: true,
        user_id: ""
      }),
      credit_user_form: this.$inertia.form({
        show_records: true,
        amount: "",
        id: "",
        user_id: "",
      }),
      show_other_overlay: false,

      credit_user_modal_title: "",

    }
  },
  watch: {
    filter_rows_form: {
      deep: true,
      handler: throttle(function() {
        this.$inertia.get(this.route('cooperative_investments_admin'), pickBy(this.filter_rows_form), { preserveState: true,preserveScroll: true })
      }, 150),
    },
  },
  mounted() {
    console.log(this.all_history)
    console.log(this.$page.props)
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    changeEnableInvestment(){
      var self = this;
      console.log(this.enable_investment_request.status)
      self.enable_investment_request.post(self.route('change_enable_investment'), {
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response)

            if(response.success){
                    
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

    dismissThisRequest(id,user_id){
      var self = this;
      self.dismiss_request.id = id;
      swal({
        title: 'Warning',
        text: "Are You Sure You Want To Dismiss This Request?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes Dismiss!',
        cancelButtonText : "No"
      }).then(function(){

        self.dismiss_request.post(self.route('dismiss_user_credit_request'), {
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response)

            if(response.success){
                    
              $.notify({
                message:"Request Dismissed Successfully"
                },{
                  type : "success",
                  z_index: 20000,
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
    submitCreditUserForm (){
      var self = this;
      
      swal({
        title: 'Warning',
        text: "Are You Sure You Want To Credit User?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes Proceed!',
        cancelButtonText : "No"
      }).then(function(){
        var user_id = self.get_user_info_request.user_id;
        var id = self.get_user_info_request.id;

        self.credit_user_form.id = id;
        self.credit_user_form.user_id = user_id;

        self.credit_user_form.post(self.route('credit_user_after_request'), {
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response)

            if(response.success){
                    
              $.notify({
                message:"User Credited Successfully"
              },{
                type : "success",
                z_index: 20000,   
              });
              $("#credit-user-modal").modal("hide");
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
    creditThisUser(id,user_id,amount){
      var self = this;
      self.get_user_info_request.user_id = user_id;
      self.get_user_info_request.id = id;

      self.get_user_info_request.post(self.route('get_user_info_by_id'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            var user_name = response[0].user_name;
            var full_name = response[0].full_name;
            var phone = response[0].phone;
            var email = response[0].email;
            // var dob = response[0].dob;
            var address = response[0].address;
            var total_income = response[0].total_income;
            var withdrawn = response[0].withdrawn;
            var wallet_balance = total_income - withdrawn;
            
            
            self.credit_user_modal_title = "Wallet Balance: ₦"+ wallet_balance.toFixed(2);
            self.credit_user_form.amount = amount
            $("#credit-user-modal").modal("show");
            
            
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
    

    clearFilterRowsForm() {

      this.filter_rows_form.user_name = "";
      this.filter_rows_form.amount = "";
      this.filter_rows_form.duration = "";
      this.filter_rows_form.status = "all";
      this.filter_rows_form.settled_amount = "";
      this.filter_rows_form.settled_date_time = "";
      
      this.filter_rows_form.date = "";
      this.filter_rows_form.length = 10;
      this.filter_rows_form.start_date = "";
      this.filter_rows_form.end_date = "";

      
    },

    isNumber(value) 
    {
       return typeof value === 'number' && isFinite(value);
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
