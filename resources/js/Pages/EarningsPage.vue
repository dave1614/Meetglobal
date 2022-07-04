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
              

                
                <div class="table-div material-datatables table-responsive" style="">
                  <table class="table table-striped table-bordered nowrap hover display" id="all-earnings-table" cellspacing="0" width="100%" style="width:100%">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th></th>
                        <th>Earnings</th>
                        <th class="text-center">Hstory</th>
                
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td>Basic Sponsor Earnings</td>
                        <td v-html="'₦' + addCommas(basic_sponsor_earnings.toFixed(2))"></td>
                        <td class="td-actions text-center">

                          <a target="_blank" :href="route('view_mlm_earnings_details_basic_sponsor')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a>
                        </td>

                      </tr>


                      <tr>
                        <td>2</td>
                        <td>Business Sponsor Earnings</td>
                        <td v-html="'₦' + addCommas(business_sponsor_earnings.toFixed(2))"></td>
                        <td class="td-actions text-center">
                          <a target="_blank" :href="route('view_mlm_earnings_details_sponsor')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a>
                        </td>
                      </tr>

                      <tr>
                        <td>3</td>
                        <td>Basic Placement Earnings</td>
                        <td v-html="'₦' + addCommas(basic_placement_earnings.toFixed(2))"></td>
                        <td class="td-actions text-center">

                          <a target="_blank" :href="route('view_mlm_earnings_details_basic_placement')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a>
                        </td>
                      </tr>


                      <tr>
                        <td>4</td>
                        <td>Placement Earnings</td>
                        <td v-html="'₦' + addCommas(business_placement_earnings.toFixed(2))"></td>
                        <td class="td-actions text-center">

                          <a target="_blank" :href="route('view_mlm_earnings_details_placement')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i> 
                            <div class="ripple-container"></div>
                          </a>
                        </td>

                      </tr>

                      <tr>
                        <td>5</td>
                        <td>Center Leader Sponsor Bonus</td>
                        <td v-html="'₦' + addCommas(center_leader_sponsor_bonus.toFixed(2))"></td>

                        <td class="td-actions text-center">

                          <a target="_blank" :href="route('view_mlm_earnings_details_center_leader_sponsor')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a> 
                        </td>
                      </tr>

                      <tr>
                        <td>6</td>
                        <td>Center Leader Placement Bonus</td>
                        <td v-html="'₦' + addCommas(center_leader_placement_bonus.toFixed(2))"></td>
                        <td class="td-actions text-center">

                          <a target="_blank" :href="route('view_mlm_earnings_details_center_leader_placement')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a>
                        </td>

                      </tr>


                      <tr>
                        <td>7</td>
                        <td>Center Connector Sponsor Bonus</td>
                        <td v-html="'₦' + addCommas(center_connector_sponsor_bonus.toFixed(2))"></td>

                        <td class="td-actions text-center">

                          <a target="_blank" :href="route('view_mlm_earnings_details_center_connector_sponsor')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a>
                        </td>

                      </tr>

                      <tr>
                        <td>8</td>
                        <td>Center Connector Placement Bonus</td>
                        <td v-html="'₦' + addCommas(center_connector_placement_bonus.toFixed(2))"></td>
                        <td class="td-actions text-center">

                          <a target="_blank" :href="route('view_mlm_earnings_details_center_connector_placement')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a>
                        </td>

                      </tr>


                      <tr>
                        <td>9</td>
                        <td>Vendor Sponsor Bonus</td>
                        <td v-html="'₦' + addCommas(vendor_sponsor_bonus.toFixed(2))"></td>
                        <td class="td-actions text-center">

                          <a target="_blank" :href="route('view_mlm_earnings_details_vendor_sponsor')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a>
                        </td>
                      </tr>


                      <tr>
                        <td>10</td>
                        <td>Vendor Placement Bonus</td>
                        <td v-html="'₦' + addCommas(vendor_placement_bonus.toFixed(2))"></td>
                        <td class="td-actions text-center">

                          <a target="_blank" :href="route('view_mlm_earnings_details_vendor_placement')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a>
                        </td>
                      </tr>


                      <tr>
                        <td>11</td>
                        <td>Center Leader Trade Income</td>
                        <td v-html="'₦' + addCommas(center_leader_selection_income)"></td>
                        <td class="td-actions text-center">

                          <a target="_blank" :href="route('view_mlm_earnings_details_center_leader_selection')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a>
                        </td>
                      </tr>


                      <tr>
                        <td>12</td>
                        <td>Center Connector Trade Income</td>
                        <td v-html="'₦' + addCommas(center_connector_selection_income)"></td>
                        <td class="td-actions text-center">

                          <a target="_blank" :href="route('view_mlm_earnings_details_center_connector_selection')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a>
                        </td>   
                      </tr>


                      <tr>
                        <td>13</td>
                        <td>Vendor Trade Income</td>
                        <td v-html="'₦' + addCommas(vendor_selection_income)"></td>
                        <td class="td-actions text-center">

                          <a target="_blank" :href="route('view_mlm_earnings_details_vendor_selection')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a>
                        </td>
                      </tr>


                      <tr>
                        <td>14</td>
                        <td>Trade / Delivery Income</td>
                        <td v-html="'₦' + addCommas(trade_delivery_income.toFixed(2))"></td>
                        <td class="td-actions text-center">

                          <a target="_blank" :href="route('view_mlm_earnings_details_trade_delivery')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a>
                        </td>
                      </tr>


                      <tr>
                        <td>15</td>
                        <td>VTU Trade Income</td>
              
                        <td v-html="'₦' + addCommas(vtu_trade_income.toFixed(2))"></td>
                        <td class="td-actions text-center">

                          <a target="_blank" :href="route('view_mlm_earnings_details_vtu_trade_income')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a>
                        </td>
                      </tr>


                      <tr>

                        <td>16</td>
                        <td>SGPS Income</td>
                        <td v-html="'₦' + addCommas(sgps_income.toFixed(2))"></td>
                        <td class="td-actions text-center">

                          <a target="_blank" :href="route('view_mlm_earnings_details_sgps_income')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a>
                        </td>
                      </tr>

                      <tr>

                        <td>17</td>
                        <td>Sim Activation Incentive</td>
                        <td v-html="'₦' + addCommas(sim_activation_incentive)"></td>
                        <td class="td-actions text-center">

                          <!-- <a target="_blank" :href="route('view_mlm_earnings_details_sgps_income')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a> -->
                        </td>
                      </tr>


                      <!-- <tr>
                        <td>17</td>
                        <td>Car Award Earnings</td>
                        <td v-html="'₦' + addCommas(car_award_earnings)"></td>
                        <td class="td-actions text-center">
                          <a target="_blank" :href="route('view_mlm_earnings_details_car_award_earnings')" class="btn btn-primary">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="ripple-container"></div>
                          </a>
                        </td>
                      </tr> -->

              
                      <tr>

                        <td><p style="visibility: hidden;">10</p></td>
                        <td></td>
                        <td></td>
                        <td></td>
                      </tr>


                      <tr>

                        <td><p style="visibility: hidden;">11</p></td>
                        <td style="font-weight: 900;">Total Earnings</td>
              
                        <td style="font-weight: 900;" v-html="'₦' + addCommas(total_business_earnings.toFixed(2))"></td>
                        <td style="font-weight: 900;"></td>
                      </tr>


                      <tr>
                        <td><p style="visibility: hidden;">12</p></td>
                        <td style="font-weight: 900;">Total Withdrawable Earnings</td>
              
                        <td style="font-weight: 900;" v-html="'₦' + addCommas(total_withdrawable_business_earnings.toFixed(2))"></td>
                        <td style="font-weight: 900;"></td>
                      </tr>

                    </tbody>
                  </table>
                </div>

                
                <div class="container">
                  <h4>Grand Total Withdrawable Earnings: <em class="text-primary" v-html="'₦' + addCommas(grand_total_withdrawable_earnings.toFixed(2))"></em></h4>

                  <h4>Total Withdrawn: <em class="text-primary" v-html="'₦' + addCommas(total_mlm_withdrawn)"></em></h4>
                  <h4>Monthly Subscription: <em class="text-primary" v-html="'₦' + addCommas(monthly_subscription)"></em></h4>
                  

                  <h4>Grand Total Balance: <em class="text-primary" v-html="'₦' + addCommas(grand_total_balance.toFixed(2))"></em></h4>

          
                  <div>
                    <button class="btn btn-primary" @click="transferToMainAcct">Transfer To Your Main Account</button>
                    <div>

                    </div>

                  </div>
                </div>
              </div>
            </div>  
          </div>

        </div>
      </div>

      <div class="modal fade" data-backdrop="static" id="transfer-to-main-acct-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content" >
            <div class="modal-header">
              <h3 class="modal-title">Transfer To Main Account.</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body" id="modal-body">
              
              <form id="transfer-to-main-acct-form" @submit.prevent="submitTransferToAcctForm">  
                

                <text-input v-model="transfer_to_main_acct_form.amount" :error="transfer_to_main_acct_form.errors.amount" type="number" label="Enter Amount" id="amount" placeholder="in ₦(Naira)"  step="any"/>


                <loading-button :loading="transfer_to_main_acct_form.processing" class="btn btn-primary col-12" type="submit">Submit</loading-button>

              </form>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
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


    basic_sponsor_earnings: [Number,String],
    business_sponsor_earnings: [Number,String],
    basic_placement_earnings: [Number,String],
    business_placement_earnings: [Number,String],
    center_leader_sponsor_bonus: [Number,String],
    center_leader_placement_bonus: [Number,String],
    center_connector_sponsor_bonus: [Number,String],
    center_connector_placement_bonus: [Number,String],
    vendor_sponsor_bonus: [Number,String],
    vendor_placement_bonus: [Number,String],
    center_leader_selection_income: [Number,String],
    center_connector_selection_income: [Number,String],
    vendor_selection_income: [Number,String],
    trade_delivery_income: [Number,String],
    vtu_trade_income: [Number,String],
    sgps_income: [Number,String],
    car_award_earnings: [Number,String],
    total_basic_earnings: [Number,String],
    total_business_earnings: [Number,String],
    total_withdrawable_basic_earnings: [Number,String],
    total_withdrawable_business_earnings: [Number,String],
    grand_total_withdrawable_earnings: [Number,String],
    total_mlm_withdrawn: [Number,String],
    grand_total_balance: [Number,String],
    sim_activation_incentive: [Number,String],
    monthly_subscription: [Number,String],

    

    

  },
  data() {
    return {
      transfer_to_main_acct_form: this.$inertia.form({
        amount: null,

      }),
      
      show_other_overlay: false,

    }
  },
 
  mounted() {
    
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    submitTransferToAcctForm() {
      var self = this;
      
      self.transfer_to_main_acct_form.post(self.route('transfer_to_main_acct'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)


          if(response.success && response.amount > 0){
            $("#transfer-to-main-acct-modal").modal("hide");
            swal({
              title: 'Success',
              text: "You Have Successfully Transferred ₦ " + self.addCommas(response.amount) + " To Your Main Account.",
              type: 'success',
              confirmButtonColor: '#3085d6',
              allowOutsideClick: false,
            }).then(function(){
              self.$inertia.visit(self.route('user_earnings'));
            });
            
          }else if(response.not_enough_money){
            swal({
              title: 'Ooops',
              text: "Amount Entered Exceeds Your Grand Withdrawable Income",
              type: 'error'
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
      });
    },
    transferToMainAcct() {
      $("#transfer-to-main-acct-modal").modal("show");
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
  },
}
</script>
