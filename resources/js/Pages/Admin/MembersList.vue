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

                        <text-input v-model="filter_rows_form.full_name" :error="filter_rows_form.errors.full_name" type="text" label="Full name" id="full_name" placeholder="" class="col-sm-4"/>

                        <text-input v-model="filter_rows_form.user_name" :error="filter_rows_form.errors.user_name" type="text" label="User Name" id="user_name" placeholder="" class="col-sm-4"/>

                        <text-input v-model="filter_rows_form.phone" :error="filter_rows_form.errors.phone" type="text" label="Phone Number" id="phone" placeholder="" class="col-sm-4"/>

                        <text-input v-model="filter_rows_form.email" :error="filter_rows_form.errors.email" type="text" label="Email Address" id="email" placeholder="" class="col-sm-4"/>
                        
                      

                        
                        <text-input v-model="filter_rows_form.created_date" :error="filter_rows_form.errors.created_date" type="date" label="Registration Date" id="created_date" placeholder="" class="col-sm-4"/>

                        <text-input v-model="filter_rows_form.start_date" :error="filter_rows_form.errors.start_date" type="date" label="Start Date" id="start_date" placeholder="" class="col-sm-6"/>

                        <text-input v-model="filter_rows_form.end_date" :error="filter_rows_form.errors.end_date" type="date" label="End Date" id="end_date" placeholder="" class="col-sm-6"/>
                       
                      </div>

                      <div class="row">
                        
                        <button type='button' class='btn btn-secondary col-sm-5 btn-outline-primary' @click="clearFilterRowsForm"><i class="fa fa-close"></i>&nbsp;&nbsp;Clear</button>
                      </div>
                    </form>
                  </div>

                  <div class="table-div material-datatables table-responsive" style="" v-if="all_users.data.length > 0">
                    <table id="all-registered-users-table" class="table table-test table-striped table-bordered nowrap hover display" cellspacing="0" width="100%" style="width:100%">
                      <thead class="thead-light">
                        <tr>
                          <th>#</th>

                          <th>Full Name</th>
                          <th>User Name</th>
                          <th>Date Of Registration</th>
                          <th>Phone Number</th>
                          <th>Email</th>
                          <th>Wallet Balance</th>
                          
                          <th>Address</th>
                        </tr>
                      </thead>
                      <tbody>

                                              
                        
                        <tr v-for="row in all_users.data" :key="row.index" @click="performFunctionOnUser(row.id,row.user_name,row.slug)">
                          <td>{{row.index}}. </td>
                          <td style="text-transform:capitalize;">{{row.full_name}}</td>

                          <td><inertia-link class="nav-link" :href="route('user_profile', row.slug)">{{row.user_name}}</inertia-link></td>

                          
                          <td v-html="row.created_date + ' ' + row.created_time"></td>
                          <td>{{row.phone}}</td>
                          <td>{{row.email}}</td>
                          <td><em class="text-primary">{{row.wallet_balance_str}}</em></td>
                          <td>{{row.address}}</td>
                        </tr>
                              
                      
                      </tbody>
                    </table>
                  </div>

                  
                  <h4 v-else class="text-warning">No Data To Display</h4>
                  
                  <pagination class="mt-6" :links="all_users.links" style="margin-top:30px;"/>
                  <p :if="all_users.data.length > 0" style="margin-top:30px;">{{all_users.total}} Total Entries</p>
                  
                  
                </div>
              </div>

          </div>
        </div>
      </div>

      <div class="modal fade" data-backdrop="static" id="debit-user-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title text-center" style="text-transform: capitalize;">Enter Amount To Debit User</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeDebitUserModal">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="modal-body">
              <h5><em class="text-primary wallet-balance" v-html="debit_user_modal_small_title">Wallet Balance: ₦ 50,000.00</em></h5>
              
              <form id="debit-user-form" @submit.prevent="submitDebitUserForm">    
                

                  <text-input v-model="debit_user_form.amount" :error="debit_user_form.errors.amount" type="number" label="Amount" id="amount" step="any" placeholder=""/>
                  <loading-button :loading="debit_user_form.processing" class="btn btn-primary col-12" type="submit">Submit</loading-button>
              </form>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal" @click="closeDebitUserModal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" data-backdrop="static" id="credit-user-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title text-center" style="text-transform: capitalize;">Enter Amount To Credit User</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeCreditUserModal">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="modal-body">
              <h5><em class="text-primary wallet-balance" v-html="credit_user_modal_small_title">Wallet Balance: ₦ 50,000.00</em></h5>
             
              <form id="credit-user-form" @submit.prevent="submitCreditUserForm">  

                <text-input v-model="credit_user_form.amount" :error="credit_user_form.errors.amount" type="number" label="Amount" id="amount" step="any" placeholder=""/>
                <loading-button :loading="credit_user_form.processing" class="btn btn-primary col-12" type="submit">Submit</loading-button>
              </form>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal" @click="closeCreditUserModal">Close</button>
            </div>
          </div>
        </div>
      </div>


      <div class="modal fade" data-backdrop="static" id="perform-function-on-user-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title text-center" style="text-transform: capitalize;">{{perform_function_on_user_modal_title}}</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="modal-body">
             
              <div class="table-responsive">
                <table class="table table-test table-striped table-bordered nowrap hover display" id="full-user-results-table" cellspacing="0" width="100%" style="width:100%">
                
                  <tbody>
                    <tr @click="editUserProfile()">
                      <td>1.</td>
                      <td class="text-primary">Edit Users Profile</td>
                    </tr>
                    
                    <tr @click="creditUsersWallet()">
                      <td>2.</td>
                      <td class="text-primary">Credit Users Wallet</td>
                    </tr>
                    <tr @click="debitUsersWallet()">
                      <td>3.</td>
                      <td class="text-primary">Debit Users Wallet</td>
                    </tr>
                    <tr @click="viewUsersHistory()">
                      <td>4.</td>
                      <td class="text-primary">View Users History</td>
                    </tr>
                    <tr @click="viewUsersEarnings()">
                      <td>5.</td>
                      <td class="text-primary">View Users Earnings</td>
                    </tr>
                    <tr v-if="view_user_coop_savings" @click="viewUsersCoopSavings()">
                      <td>6.</td>
                      <td class="text-primary">View Users Cooperative Savings</td>
                    </tr>
                    
                  </tbody>
                </table>
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal" >Close</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" data-backdrop="static" id="choose-user-history-to-view-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title text-center" style="text-transform: capitalize;">Choose User History To View</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="goBackFromChooseUserHistoryToViewModal">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="modal-body">
             
              <div class="table-responsive">
                <table class="table table-test table-striped table-bordered nowrap hover display" id="full-user-results-table" cellspacing="0" width="100%" style="width:100%">
                
                  <tbody>
                    <tr @click="viewUsersAccountCreditHistory">
                      <td>1.</td>
                      <td class="text-primary">Account Credit History</td>
                    </tr>
                    
                    <tr @click="viewUsersAccountWithdrawalHistory">
                      <td>2.</td>
                      <td class="text-primary">Withdrawal History</td>
                    </tr>
                    <tr @click="viewUsersVTUHistory">
                      <td>3.</td>
                      <td class="text-primary">VTU History</td>
                    </tr>
                    <tr @click="viewUsersTransferHistory">
                      <td>4.</td>
                      <td class="text-primary">Transfer History</td>
                    </tr>
                    <tr @click="viewUsersAdminCreditHistory">
                      <td>5.</td>
                      <td class="text-primary">Admin Credit History</td>
                    </tr>

                    <tr @click="viewUsersAdminDebitHistory">
                      <td>6.</td>
                      <td class="text-primary">Admin Debit History</td>
                    </tr>

                    <tr @click="viewUsersProductAdvanceHistory">
                      <td>7.</td>
                      <td class="text-primary">View Product Advance History</td>
                    </tr>

                    <tr @click="viewUsersAdminAccountStatement">
                      <td>8.</td>
                      <td class="text-primary">Account Statement</td>
                    </tr>
                    
                  </tbody>
                </table>
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal" @click="goBackFromChooseUserHistoryToViewModal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" data-backdrop="static" id="choose-user-earning-to-view-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title text-center" style="text-transform: capitalize;">Choose User Earning To View</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="goBackFromChooseUserEarningToViewModal">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="modal-body">
             
              <div class="table-responsive">
                <table class="table table-test table-striped table-bordered nowrap hover display" id="full-user-results-table" cellspacing="0" width="100%" style="width:100%">
                
                  <tbody>
                    <tr @click="viewUsersAccountSponsorEarnings">
                      <td>1.</td>
                      <td class="text-primary">Sponsor Earnings</td>
                    </tr>
                    
                    <tr @click="viewUsersAccountPlacementEarnings">
                      <td>2.</td>
                      <td class="text-primary">Placement Earnings</td>
                    </tr>
                    <tr @click="viewUsersCenterLeaderSponsorBonus">
                      <td>3.</td>
                      <td class="text-primary">Center Leader Sponsor Bonus</td>
                    </tr>
                    <tr @click="viewUsersCenterLeaderPlacementBonus">
                      <td>4.</td>
                      <td class="text-primary">Center Leader Placement Bonus</td>
                    </tr>
                    <tr @click="viewUsersCenterLeaderSelectionIncome">
                      <td>5.</td>
                      <td class="text-primary">Center Leader Selection Income</td>
                    </tr>
                    <tr @click="viewUsersTradeIncome">
                      <td>6.</td>
                      <td class="text-primary">Trade / Delivery Income</td>
                    </tr>
                    <tr @click="viewUsersVTUTradeIncome">
                      <td>7.</td>
                      <td class="text-primary">VTU Trade Income</td>
                    </tr>
                    <tr @click="viewUsersSGPSIncome">
                      <td>8.</td>
                      <td class="text-primary">SGPS Income</td>
                    </tr>
                    <tr @click="viewUsersCarAwardEarnings">
                      <td>9.</td>
                      <td class="text-primary">Car Award Earnings</td>
                    </tr>
                    
                    
                  </tbody>
                </table>
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal" @click="goBackFromChooseUserEarningToViewModal">Close</button>
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


    all_users: Object,
    
    full_name: String,
    user_name: String,
    phone: [Number,String],
    email: String,
    created_date: String,
    
    length: [Number,String],
    start_date: String,
    end_date: String,

    

  },
  data() {
    return {
      filter_rows_form: this.$inertia.form({
        length: this.length,
        full_name: this.full_name,
        user_name: this.user_name,
        phone: this.phone,
        email: this.email,
        created_date: this.created_date,
        start_date: this.start_date,
        end_date: this.end_date,

      }),

      get_user_info_request: this.$inertia.form({
        show_records: true,
        user_id: ""
      }),
      credit_user_form: this.$inertia.form({
        amount: "",
        user_id: ""
      }),

      debit_user_form: this.$inertia.form({
        amount: "",
        user_id: ""
      }),

      perform_function_on_user_modal_title: "",
      credit_user_modal_small_title: "",
      debit_user_modal_small_title: "",

      view_user_coop_savings: false,
      
      show_other_overlay: false,

    }
  },
  watch: {
    filter_rows_form: {
      deep: true,
      handler: throttle(function() {
        this.$inertia.get(this.route('view_members_list'), pickBy(this.filter_rows_form), { preserveState: true,preserveScroll: true })
      }, 150),
    },
  },
  mounted() {
    console.log(this.all_users)
    console.log(this.$page.props)
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    viewUsersCoopSavings(){
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#perform-function-on-user-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('coop_savings_admin',user_id));
      }, 1500);
    },
    viewUsersAccountSponsorEarnings() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-earning-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_sponsor_earnings',user_id));
      }, 1500);
    },
    viewUsersAccountPlacementEarnings() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-earning-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_placement_earnings',user_id));
      }, 1500);
    },
    viewUsersCenterLeaderSponsorBonus() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-earning-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_center_leader_sponsor_bonus',user_id));
      }, 1500);
    },
    viewUsersCenterLeaderPlacementBonus() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-earning-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_center_leader_placement_bonus',user_id));
      }, 1500);
    },
    viewUsersCenterLeaderSelectionIncome() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-earning-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_center_leader_selection_bonus',user_id));
      }, 1500);
    },
    viewUsersTradeIncome() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-earning-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_trade_bonus',user_id));
      }, 1500);
    },
    viewUsersVTUTradeIncome() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-earning-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_vtu_trade_bonus',user_id));
      }, 1500);
    },
    viewUsersSGPSIncome() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-earning-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_sgps_bonus',user_id));
      }, 1500);
    },
    viewUsersCarAwardEarnings() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-earning-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_car_award_bonus',user_id));
      }, 1500);
    },
    goBackFromChooseUserEarningToViewModal() {
      var self = this;
      self.show_other_overlay = true;

      $("#choose-user-earning-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false;
        $("#perform-function-on-user-modal").modal("show");
      },1500)
    },
    viewUsersAccountCreditHistory(){
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-history-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_account_credit_history',user_id));
      }, 1500);
      
    },
    viewUsersAccountWithdrawalHistory() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-history-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_withdrawal_history',user_id));
      }, 1500);
    },
    viewUsersVTUHistory() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-history-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_vtu_history',user_id));
      }, 1500);
    },
    viewUsersTransferHistory(){
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-history-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_transfer_history',user_id));
      }, 1500);
    },
    viewUsersAdminCreditHistory(){
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-history-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_admin_credit_history',user_id));
      }, 1500);
    },
    viewUsersAdminDebitHistory(){
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-history-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_admin_debit_history',user_id));
      }, 1500);
    },
    viewUsersProductAdvanceHistory() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-history-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_product_advance_history',user_id));
      }, 1500);
    },
    viewUsersAdminAccountStatement() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.show_other_overlay = true
      $("#choose-user-history-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false
        self.$inertia.visit(self.route('users_account_statement',user_id));
      }, 1500);
    },
    goBackFromChooseUserHistoryToViewModal () {
      var self = this;
      self.show_other_overlay = true;

      $("#choose-user-history-to-view-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false;
        $("#perform-function-on-user-modal").modal("show");
      },1500)
    },

    viewUsersEarnings() {
      var self = this;
      self.show_other_overlay = true;
      $("#perform-function-on-user-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false;
        $("#choose-user-earning-to-view-modal").modal("show");
      },1500)
    },
    viewUsersHistory(){
      var self = this;
      self.show_other_overlay = true;
      $("#perform-function-on-user-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false;
        $("#choose-user-history-to-view-modal").modal("show");
      },1500)
    },
    closeDebitUserModal() {
      var self = this;
      self.show_other_overlay = true;
      $("#debit-user-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false;
        $("#perform-function-on-user-modal").modal("show");
      }, 1500)
    },
    submitDebitUserForm() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.debit_user_form.user_id = user_id;
      var amount = self.debit_user_form.amount;
      swal({
        title: 'Warning',
        text: "Are You Sure You Want To Debit User <em class='text-primary'>₦" + amount + "</em> ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes Proceed!',
        cancelButtonText : "No"
      }).then(function(){
        
        self.debit_user_form.post(self.route('debit_user'), {
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response)

            if(response.success){
                
              $.notify({
                message:"User Debited Successfully"
              },{
                type : "success",
                z_index: 20000,    
              });

              self.show_other_overlay = true;
              self.debit_user_form.user_id = "";
              self.debit_user_form.amount = "";
              $("#debit-user-modal").modal("hide");
              setTimeout(function () {
                self.show_other_overlay = false;
                
              }, 1500)
                
            }else if(response.max){

              swal({
                title: 'Ooops',
                text: "This Users Walle Balance Is " + response.wallet_balance.toFixed(2) + ". You Cannot Debit Him Beyond This.",
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
      });
    
    },
    debitUsersWallet() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
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

            
            
            
            self.show_other_overlay = true;
            $("#perform-function-on-user-modal").modal("hide");
            setTimeout(function () {
              self.show_other_overlay = false;
              
              self.debit_user_modal_small_title = "Wallet Balance: ₦"+ wallet_balance.toFixed(2);
              // $("#debit-user-modal #amount").attr("max",wallet_balance);
              $("#debit-user-modal").modal("show");
            }, 1500)
               
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
    submitCreditUserForm() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      self.credit_user_form.user_id = user_id;
      var amount = self.credit_user_form.amount;
      swal({
        title: 'Warning',
        text: "Are You Sure You Want To Credit User <em class='text-primary'>₦" + amount + "</em> ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes Proceed!',
        cancelButtonText : "No"
      }).then(function(){
        
        self.credit_user_form.post(self.route('credit_user'), {
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

              self.show_other_overlay = true;
              self.credit_user_form.user_id = "";
              self.credit_user_form.amount = "";
              $("#credit-user-modal").modal("hide");
              setTimeout(function () {
                self.show_other_overlay = false;
                
              }, 1500)
                
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
    closeCreditUserModal(elem,evt){
      var self = this;
      self.show_other_overlay = true;
      $("#credit-user-modal").modal("hide");
      setTimeout(function () {
        self.show_other_overlay = false;
        $("#perform-function-on-user-modal").modal("show");
      }, 1500)
    },
    creditUsersWallet() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
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

            
            
            self.show_other_overlay = true;
            $("#perform-function-on-user-modal").modal("hide");
            setTimeout(function () {
              self.show_other_overlay = false;
              
              self.credit_user_modal_small_title = "Wallet Balance: ₦"+ wallet_balance.toFixed(2);
              $("#credit-user-modal").modal("show");
            }, 1500)
               
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
    editUserProfile() {
      var self = this;
      var user_id = self.get_user_info_request.user_id;
      $("#perform-function-on-user-modal").modal("hide");
      setTimeout(self.$inertia.visit(self.route('admin_edit_user_profile',user_id)), 100)
      
    },

    performFunctionOnUser(user_id,user_name,user_slug) {

      console.log(user_id)
      console.log(user_name)
      console.log(user_slug)
      var self = this;
      self.view_user_coop_savings = false;
      self.get_user_info_request.user_id = user_id;

      

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
            var coop_db_id = response[0].coop_db_id;
            var wallet_balance = total_income - withdrawn;

            if(coop_db_id != null){
              self.view_user_coop_savings = true;
            }

            
            $("#perform-function-on-user-modal .modal-title").html();
            self.perform_function_on_user_modal_title = "Choose Action To Perform On " + user_name;
            
            $("#perform-function-on-user-modal").modal("show");
               
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
      
    
      this.filter_rows_form.full_name = "";
      this.filter_rows_form.user_name = "";
      this.filter_rows_form.phone = "";
      this.filter_rows_form.email = "";
      this.filter_rows_form.created_date = "";
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
