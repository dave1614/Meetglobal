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

                        <text-input v-model="filter_rows_form.amount" :error="filter_rows_form.errors.amount" type="number" label="Amount" step="any" id="amount" placeholder="" class="col-sm-4"/>

                        <text-input v-model="filter_rows_form.user_name" :error="filter_rows_form.errors.user_name" type="text" label="User Name" id="user_name" placeholder="" class="col-sm-4"/>

                        
                        
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
                              <input v-model="filter_rows_form.status" class="form-check-input" type="radio" id="debited" value="debited" name="status">
                              Debited
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

                        
                        <text-input v-model="filter_rows_form.date" :error="filter_rows_form.errors.date" type="date" label="Request Date" id="date" placeholder="" class="col-sm-4"/>

                        <text-input v-model="filter_rows_form.debited_date_time" :error="filter_rows_form.errors.debited_date_time" type="date" label="Debited Date" id="date" placeholder="" class="col-sm-4"/>

                        <text-input v-model="filter_rows_form.dismissed_date_time" :error="filter_rows_form.errors.dismissed_date_time" type="date" label="Dismissed Date" id="dismissed_date_time" placeholder="" class="col-sm-4"/>

                        <text-input v-model="filter_rows_form.start_date" :error="filter_rows_form.errors.start_date" type="date" label="Start Date" id="start_date" placeholder="" class="col-sm-6"/>

                        <text-input v-model="filter_rows_form.end_date" :error="filter_rows_form.errors.end_date" type="date" label="End Date" id="end_date" placeholder="" class="col-sm-6"/>
                       
                      </div>

                      <div class="row">
                        
                        <button type='button' class='btn btn-secondary col-sm-5 btn-outline-primary' @click="clearFilterRowsForm"><i class="fa fa-close"></i>&nbsp;&nbsp;Clear</button>
                      </div>
                    </form>
                  </div>

                  <div class="table-div material-datatables table-responsive" style="" v-if="all_requests.data.length > 0">
                    <table id="all-registered-users-table" class="table table-test table-striped table-bordered nowrap hover display" cellspacing="0" width="100%" style="width:100%">
                      <thead class="thead-light">
                        <tr>
                          <th>#</th>
                          <th>Actions</th>
                          <th>Username</th>
                          <th>Status</th>
                          <th>Amount</th>
                          <th>Bank Name</th>
                          <th>Account Number</th>
                          <th>Account Name</th>
                          <th>Request Date/Time</th>
                          <th>Debited Date/Time</th>
                          <th>Dismissed Date/Time</th>
                        </tr>
                      </thead>
                      <tbody>

                                              
                        
                        <tr v-for="request in all_requests.data" :key="request.index">
                          <td>{{request.index}}. </td>

                            
                          <td class="td-actions text-center" v-if="request.status == 'Pending'">
                            
                            <button type="button" rel="tooltip" title="Credit This User" class="btn btn-success btn-link btn-lg" @click.stop="creditThisUser(request.id,request.user_id,request.amount)">
                              <i style="font-size: 20px;" class="fas fa-check-square"></i>
                              <div class="ripple-container"></div>
                            </button>


                            <button type="button" rel="tooltip" title="Dismiss This Request" class="btn btn-danger btn-link btn-lg" @click.stop="dismissThisRequest(request.id,request.user_id)">
                              <i style="font-size: 20px;" class="fas fa-times"></i>
                              <div class="ripple-container"></div>
                            </button>

                            
                            <button v-if="request.real_bank_name == ''" type="button" rel="tooltip" title="View Payment Details" class="btn btn-primary btn-link btn-lg" @click.stop="viewPaymentDetails(request.id,request.user_id)">
                              <i style="font-size: 20px;" class="fas fa-info-circle"></i>
                              <div class="ripple-container"></div>
                            </button>
                            

                          </td>
                          
                          <td v-else>
                            
                            <button v-if="request.real_bank_name == ''" type="button" rel="tooltip" title="View Payment Details" class="btn btn-primary btn-link btn-lg" @click.stop="viewPaymentDetails(request.id,request.user_id)">
                              <i style="font-size: 20px;" class="fas fa-info-circle"></i>
                              <div class="ripple-container"></div>
                            </button>
                          
                          </td>
                          

                          <td><inertia-link class="nav-link" :href="route('user_profile', request.user_slug)">{{request.user_name}}</inertia-link></td>

                          
                          <td>{{request.status}}</td>
                          <td>{{request.amount_str}}</td>
                          <td>{{request.real_bank_name}}</td>
                          <td>{{request.account_number}}</td>
                          <td>{{request.account_name}}</td>
                          <td>{{request.date_time}}</td>
                          <td>{{request.debited_date_time}}</td>
                          <td>{{request.dismissed_date_time}}</td>
                        </tr>
                              
                      
                      </tbody>
                    </table>
                  </div>
                  <h4 v-else class="text-warning">No Data To Display</h4>
                  <pagination class="mt-6" :links="all_requests.links" style="margin-top:30px;"/>
                  <p :if="all_requests.data.length > 0" style="margin-top:30px;">{{all_requests.total}} Total Entries</p>
                </div>
              </div>

          </div>
        </div>
      </div>

     

      <div class="modal fade" data-backdrop="static" id="credit-user-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title text-center" style="text-transform: capitalize;">Verify Users Account Credit</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="modal-body">
              <h5><em class="text-primary wallet-balance" v-html="credit_user_modal_title"></em></h5>
              
              <div id="payment-details" v-html="payment_details_div_content">
                
              </div>
              <button class="btn btn-primary" @click="verifyAccountCredit">Verify Account Credit</button>

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


    all_requests: Object,
    user_name: String,
    amount: [Number,String],
    status: String,
    date: String,
    debited_date_time: String,
    dismissed_date_time: String,
    length: [Number,String],
    start_date: String,
    end_date: String,

  },
  data() {
    return {
      filter_rows_form: this.$inertia.form({
        length: this.length,
        amount: this.amount,
        user_name: this.user_name,
        status: this.status,
        debited_date_time: this.debited_date_time,
        date: this.date,
        dismissed_date_time: this.dismissed_date_time,
        start_date: this.start_date,
        end_date: this.end_date,

      }),
      dismiss_request: this.$inertia.form({
        show_records: true,
        id: "",
        user_id: "",
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
      get_withdrawal_request_account_details_request: this.$inertia.form({
        show_records: true,
        id: "",
        user_id: "",
      }),
      verify_account_credit_withdrawal_request: this.$inertia.form({
        show_records: true,
        id: "",
        user_id: "",
      }),
      show_other_overlay: false,

      credit_user_modal_title: "",
      payment_details_div_content: "",

    }
  },
  watch: {
    filter_rows_form: {
      deep: true,
      handler: throttle(function() {
        this.$inertia.get(this.route('account_withdrawal_requests'), pickBy(this.filter_rows_form), { preserveState: true,preserveScroll: true })
      }, 150),
    },
  },
  mounted() {
    console.log(this.all_requests)
    console.log(this.$page.props)
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {

    verifyAccountCredit() {
      var self = this;
      var id = self.get_withdrawal_request_account_details_request.id;
      var user_id = self.get_withdrawal_request_account_details_request.user_id;

      self.verify_account_credit_withdrawal_request.id = id;
      self.verify_account_credit_withdrawal_request.user_id = user_id;

      self.verify_account_credit_withdrawal_request.post(self.route('verify_account_credit_withdrawal'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            $.notify({
              message:"Successfully Marked As Debited"
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
    },
    dismissThisRequest(id,user_id){
      var self = this;
      self.dismiss_request.id = id;
      self.dismiss_request.user_id = user_id;
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

        self.dismiss_request.post(self.route('dismiss_account_credit_withdrawal'), {
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response)

            if(response.success){
                    
              $.notify({
                message:"Successfully Dismissed"
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
            
            self.get_withdrawal_request_account_details_request.id = id;
            self.get_withdrawal_request_account_details_request.user_id = user_id;
            
            self.show_other_overlay = true;
            setTimeout(function () {
              self.show_other_overlay = false;
              self.get_withdrawal_request_account_details_request.post(self.route('get_withdrawal_request_account_details'), {
                preserveScroll: true,
                onSuccess: (page) => {
                  
                  var response = JSON.parse(JSON.stringify(self.response_arr))
                  console.log(response)

                  if(response.success && response.bank_name != "" && response.account_number != "" && response.account_name != ""){
                    var bank_name = response.bank_name;
                    var account_number = response.account_number;
                    var account_name = response.account_name;
                    var text = "Bank Name: <em class='text-primary'>"+bank_name+"</em><br>Account Number: <em class='text-primary'>"+account_number+"</em><br>Account Name: <em class='text-primary'>"+account_name+"</em>";
                    
                    self.payment_details_div_content = text;
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
            }, 100);
            
                        
            
            
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
      
      this.filter_rows_form.status = "pending";
      
      this.filter_rows_form.amount = "";
      this.filter_rows_form.user_name = "";
      this.filter_rows_form.date = "";
      this.filter_rows_form.debited_date_time = "";
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
