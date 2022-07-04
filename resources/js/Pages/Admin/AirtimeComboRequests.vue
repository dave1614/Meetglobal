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

                        <text-input v-model="filter_rows_form.number" :error="filter_rows_form.errors.number" type="number" label="Number" step="any" id="number" placeholder="" class="col-sm-4"/>                        
                        
                        <text-input v-model="filter_rows_form.date" :error="filter_rows_form.errors.date" type="date" label="Date" id="date" placeholder="" class="col-sm-4"/>

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
                          <th>Members Name</th>
                          <th>Amount ()</th>
                          <th>Mobile Number</th>
                          <th>Date / Time</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>

                                              
                        
                        <tr v-for="row in all_requests.data" :key="row.index">
                          <td>{{row.index}}. </td>

                          <td><inertia-link class="nav-link" :href="route('user_profile', row.user_slug)">{{row.full_name}}</inertia-link></td>


                          
                          <td>{{row.amount}}</td>
                          <td>{{row.number}}</td>
                          <td v-html="row.date + ' / ' + row.time"></td>
                          
                          <td class="td-actions text-center" >
                            
                            <button type="button" title="Mark This Record As Recharged" class="btn btn-success btn-link btn-lg" @click.stop="markThisRecordAsRecharged(row.id)">
                              <i style="font-size: 20px;" class="fas fa-check"></i>
                              <div class="ripple-container"></div>
                            </button>
                          </td>

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

     
      <div @click="openViewComboRechargeHistoryPage">
        <floating-action-button :styles="'background: 9124a3;'" :title="'View Airtime Combo Recharge History'">
          
          <i class="fas fa-clipboard-list" style="font-size: 25px; color: #fff;"></i>
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


    all_requests: Object,
    
    amount: [Number,String],
    number: [Number,String],
    date: String,
    length: [Number,String],
    start_date: String,
    end_date: String,

   
  },
  data() {
    return {
      filter_rows_form: this.$inertia.form({
        length: this.length,
        amount: this.amount,
        number: this.number,
        
        date: this.date,
        start_date: this.start_date,
        end_date: this.end_date,

      }),
      mark_combo_record_as_recharged_request: this.$inertia.form({
        show_records: true,
        id: "",

      }),
      
      show_other_overlay: false,

    }
  },
  watch: {
    filter_rows_form: {
      deep: true,
      handler: throttle(function() {
        this.$inertia.get(this.route('airtime_combo_requests'), pickBy(this.filter_rows_form), { preserveState: true,preserveScroll: true })
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
    openViewComboRechargeHistoryPage() {
      this.$inertia.visit(this.route('airtime_combo_recharge_history'))
    },
    markThisRecordAsRecharged(id) {
      var self = this;
      self.mark_combo_record_as_recharged_request.id = id;

      self.mark_combo_record_as_recharged_request.post(self.route('mark_combo_record_as_recharged'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            $.notify({
              message:"Combo Record Successfully Marked As Recharged"
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
    
    },

    clearFilterRowsForm() {
      this.filter_rows_form.amount = "";
      this.filter_rows_form.number = "";
      this.filter_rows_form.date = "";
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
