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
      <div >
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="fas fa-user-plus"></i>
                  </div>
                  <p class="card-category">New Members</p>
                  <h3 class="card-title"><small style="font-size: 15px;font-weight: bold;">+{{today_registered_users}}</small></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <a href="#">Click For Details</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-success card-header-icon">
                  <div class="card-icon">
                    <i class="fas fa-users"></i>
                  </div>
                  <p class="card-category">Total Members</p>
                  <h3 class="card-title"><small style="font-size: 15px;font-weight: bold;">{{total_registerd_users}}</small></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    
                    
                    <a href="#">Click For Details</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-primary card-header-icon">
                  <div class="card-icon">
                    <i class="fas fa-money-bill-wave"></i>
                  </div>
                  <p class="card-category">Online Payment</p>
                  <h3 class="card-title"><small style="font-size: 15px;font-weight: bold;">+ ₦{{total_amount_online_payment}}</small></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    
                    <a href="#">Click For Details</a>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="fas fa-dollar-sign"></i>
                  </div>
                  <p class="card-category">Withdrawn</p>
                  
                  <h3 class="card-title"><small style="font-size: 15px;font-weight: bold;">+ ₦{{total_amount_withdrawn_today}}</small></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    
                    <inertia-link :href="route('admin_earnings')">Click For Details</inertia-link>
                  </div>
                </div>
              </div>
            </div>


            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
                    
                  </div>
                  <p class="card-category">Uncleared Product Loan Advance</p>
                  
                  <h3 class="card-title"><small style="font-size: 15px;font-weight: bold;">₦{{total_pending_amount_product_advance}}</small></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    
                    <inertia-link :href="route('view_advance_loan_history')">Click For Details</inertia-link>
                  </div>
                </div>
              </div>
            </div>


            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="fas fa-dollar-sign"></i>
                  </div>
                  <p class="card-category">Total Smart Business Profit</p>
                  <h3 class="card-title"><small style="font-size: 15px;font-weight: bold;">{{total_profit_made_for_all_users}}</small></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    
                    <inertia-link :href="route('view_advance_loan_history')">Click For Details</inertia-link>
                  </div>
                </div>
              </div>
            </div>
          </div>
          



          <div class="row" style="margin-top: 60px;">

            <div class="col-md-6">
              <div class="card card-chart">
                <div class="card-header card-header-success">
                  <chartist
                      
                      type="Line"
                      :data="chartist_data"
                      :options="chartist_options" >
                  </chartist>
                </div>
                <div class="card-body">
                  <h4 class="card-title">Monthly Registrations ({{year}})</h4>
                  
                </div>
                <div class="card-footer">
                  
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card">
                <div class="card-header card-header-icon card-header-primary">
                  <div class="card-icon">
                    <i class="fas fa-clipboard-list" style="font-size: 30px;"></i>
                  </div>
                  <h3 class="card-title">Last 20 Users Registered Today</h3>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-test table-striped table-bordered nowrap hover display" id="users-registered-table" cellspacing="0" width="100%" style="width:100%">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Username</th>
                          <th>Full Name</th>
                          <th>Registration Time</th>
                        </tr>
                      </thead>
                    
                      <tbody v-if="first_twenty_users.length > 0">
                        
                        <tr v-for="user in first_twenty_users" :key="user.index">
                          <td>{{user.index}}</td>
                          <td><inertia-link target="_blank" :href="route('user_profile',user.user_slug)">{{user.user_name}}</inertia-link></td>
                          <td style="text-transform: capitalize;">{{user.full_name}}</td>
                          <td>{{user.time}}</td>
                        </tr>

                      </tbody>
                    </table>
                  </div>

                </div>
              </div>
            </div>
          </div>


          <div class="row" style="">
            <h3 class="col-12">Team Perfomance</h3>
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Top 10 Mlm Earners ({{ month_year_2 }})</h3>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-test table-striped table-bordered nowrap hover display" id="top-ten-mlm-earners-table" cellspacing="0" width="100%" style="width:100%">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Username</th>
                          <th>Full Name</th>
                          <th>Amount</th>
                        </tr>
                      </thead>
                    
                      <tbody v-if="top_mlm_earners.length > 0">
                        
                        <tr v-for="earner in top_mlm_earners" :key="earner.index">
                          <td>{{earner.index}}</td>
                          <td><inertia-link target="_blank" :href="route('user_profile', earner.user_slug)">{{earner.user_name}}</inertia-link></td>
                          <td style="text-transform: capitalize;">{{earner.full_name}}</td>
                          <td>{{earner.amount}}</td>
                        </tr>

                      </tbody>
                    </table>
                  </div>
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

import Layout from '../../Shared/Layout'
import AdminLayout from '../../Shared/AdminLayout'
import Pagination from '../../Shared/Pagination'
import SearchFilter from '../../Shared/SearchFilter'
import FloatingActionButton from '../../Shared/FloatingActionButton'
import mapValues from 'lodash/mapValues'
import throttle from 'lodash/throttle'
import pickBy from 'lodash/pickBy'
import TextInput from '../../Shared/TextInput'
import LoadingButton from '../../Shared/LoadingButton'


export default {
  metaInfo: { title: 'Overview' },
  components: {
    Pagination,
    SearchFilter,
    FloatingActionButton,
    TextInput,
    LoadingButton,

  },
  layout: AdminLayout,
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

    today_registered_users: String,
    total_registerd_users: String,
    total_amount_online_payment: String,
    total_pending_amount_product_advance: String,
    total_profit_made_for_all_users: String,
    year: String,
    first_twenty_users: Array,
    month_year: String,
    month_year_2: String,
    top_mlm_earners: Array,
    chartist_arr: Array,
    total_amount_withdrawn_today: String,

  },
  data() {
    return {
      form: this.$inertia.form({
        

      }),
      chartist_options: {
        axisX: {
            showGrid: true
        },
        chartPadding: {
            top: 0,
            right: 5,
            bottom: 0,
            left: 0
        },

      },
      
    }
  },
  
  mounted() {
    console.log(this.top_mlm_earners)
    $("#top-ten-mlm-earners-table").DataTable();
    
  },
  created() {

    $("body").removeClass("modal-open");
    
    this.chartist_data = {
      labels: ['J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'],
      series: [
        this.chartist_arr
      ]
    };
  },
  methods: {
    
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
