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
  .subhead{
    font-weight: bold;
    font-size: 16px;
    margin-top: 15px;
  }

  .network-card{
    cursor: pointer;
    transition: border 0.05s;
  }

  .network-card, .network-card .card-body, .network-card img{
    padding: 0;
  }

  .network-card.selected{
    padding: 5px;
    border: 2px solid #9c27b0;
  }

  .network-card img{
    height: 100%;
  }

  .amount-card{
    cursor: pointer;
    transition: border 0.01s;
  }

  .amount-card span{
    font-size: 14px;
    font-weight: bold;
  }

  .amount-card.selected{
    /*padding: 5px;*/
    border: 2px solid #9c27b0;
  }

  @media screen and (min-width: 574px) {
    .amount-card{
      margin-right: 10px;
    }
  }

  b{
    font-weight: bold;
  }
</style>

<template>
    <div class="content">
      <div id="other-overlay" :style="show_other_overlay == true ? 'display: block;' : 'display: none;' "></div>
      <div class="">
        <div class="container-fluid">

          <div class="card animate__animated  animate__bounceIn" v-show="show_register_card">
            <div class="card-header">
              <button @click="goBackFromRegisterUser" class="btn btn-warning btn-round">Go Back</button>
              <h3 class="card-title">Enter Registration Details</h3>
              <em class="text-primary">Note: Registration Fee Is ₦2,000.</em>
            </div>
            <div class="card-body">
              
              <form class="animate__animated animate__fadeIn" v-show="!show_sponsor_details && show_sponsor_form && !show_select_placement_position && !show_placement_choice && !show_placement_details && !show_palcement_mlm_db_ids"  @submit.prevent="submitSelectSponsorForm">  
                
                <h4 style="font-weight: bold;">Select Sponsor</h4>

                <text-input v-model="select_sponsor_form.user_name" :error="select_sponsor_form.errors.user_name" type="text" label="" id="user_name" placeholder="Enter Sponsor Username...." class="col-sm-6 col-12"/>
                <loading-button :loading="select_sponsor_form.processing" class="btn btn-primary col-sm-6 col-12" type="submit">Proceed</loading-button>
              </form>

              <div class='container animate__animated animate__fadeIn' v-show="show_sponsor_details &&  !show_sponsor_form &&  !show_select_placement_position &&  !show_placement_details && !show_placement_choice && !show_palcement_mlm_db_ids">
                
                
                <h4 style='font-size: 20px; font-weight: 700;' class=''>Sponsor Details</h4>
                <a href="#" @click.prevent="enterANewSponsor"> < < Go Back</a>
                <div class='row' style='margin-top: 22px;'>
                  
                  <img class='col-sm-4' :src='sponsor_user_profile_img' style='border-radius: 50%; width: 100px; height: 100px;' alt='Sponsor Profile Image'>
                  <div class='col-12'>
                    <p class='text-left' style='font-size: 16px; font-weight: 500;'>Full Name: <em class='text-primary' v-html="sponsor_full_name"></em></p>
                    <p class='text-left' style='font-size: 16px; font-weight: 500;'>User Name: <em class='text-primary' v-html="sponsor_user_name"></em></p>
                    <p class='text-left' style='font-size: 16px; font-weight: 500;'>Phone Number: <em class='text-primary' v-html="sponsor_phone_num"></em></p>
                    <p class='text-left' style='font-size: 16px; font-weight: 500;'>Email Adress: <em class='text-primary' v-html="sponsor_email_address"></em></p>
                    <button type='button' class='btn btn-success' @click='proceedToPlacementChoice'>Proceed > > </button>
                  </div>
                </div>
              </div>

              <div v-show="!show_sponsor_details && !show_sponsor_form && !show_select_placement_position && show_placement_choice && !show_placement_details && !show_palcement_mlm_db_ids" class="animate__animated animate__fadeIn">
                <a href="#" @click.prevent="goBackFromPlacementChoice"> < < Go Back</a>
                <h5>Do you have a placement?</h5>
                <div class="form-check form-check-radio form-check-inline">
                  <label class="form-check-label">
                   
                    <input @change="placementQuesClick" v-model="registration_request.placement_ques" class="form-check-input" type="radio" name="placement_ques" id="yes" value="1">
                    Yes
                    <span class="circle">
                        <span class="check"></span>
                    </span>
                  </label>
                </div>

                <div class="form-check form-check-radio form-check-inline">
                  <label class="form-check-label">
                    <input @change="placementQuesClick" v-model="registration_request.placement_ques" class="form-check-input" type="radio" name="placement_ques" id="no" value="0" >
                    No
                    <span class="circle">
                        <span class="check"></span>
                    </span>
                  </label>
                </div>
              </div>  

              <form class="animate__animated animate__fadeIn" v-show="!show_sponsor_details && !show_sponsor_form && !show_select_placement_position && !show_placement_choice && !show_placement_details && !show_palcement_mlm_db_ids"  @submit.prevent="submitSelectPlacementForm">  
                <a href="#" @click.prevent="goBackFromEnterPlacementUsernameForm"> < < Go Back</a>
                <text-input v-model="select_placement_form.user_name" :error="select_placement_form.errors.user_name" type="text" label="" id="user_name" placeholder="Enter Placement's Username...." class="col-sm-6 col-12"/>
                <loading-button :loading="select_placement_form.processing" class="btn btn-primary col-sm-6 col-12" type="submit">Proceed</loading-button>
              </form>

              <div class='container animate__animated animate__fadeIn' v-show="!show_sponsor_details && !show_sponsor_form && !show_select_placement_position &&  show_placement_details && !show_placement_choice && !show_palcement_mlm_db_ids">
                
                
                <h4 style='font-size: 20px; font-weight: 700;' class=''>Placement Details</h4>
                <a href="#" @click.prevent="enterANewPlacement"> < < Go Back</a>
                <div class='row' style='margin-top: 22px;'>
                  
                  <img class='col-sm-4' :src='placement_user_profile_img' style='border-radius: 50%; width: 100px; height: 100px;' alt='Sponsor Profile Image'>
                  <div class='col-12'>
                    <p class='text-left' style='font-size: 16px; font-weight: 500;'>Full Name: <em class='text-primary' v-html="placement_full_name"></em></p>
                    <p class='text-left' style='font-size: 16px; font-weight: 500;'>User Name: <em class='text-primary' v-html="placement_user_name"></em></p>
                    <p class='text-left' style='font-size: 16px; font-weight: 500;'>Phone Number: <em class='text-primary' v-html="placement_phone_num"></em></p>
                    <p class='text-left' style='font-size: 16px; font-weight: 500;'>Email Adress: <em class='text-primary' v-html="placement_email_address"></em></p>
                    <button type='button' class='btn btn-success' @click='proceedToPlacementMlmAccount'>Proceed > > </button>
                  </div>
                </div>
              </div>

              <div v-show="!show_sponsor_details && !show_sponsor_form && !show_select_placement_position && !show_placement_details && !show_placement_choice && show_palcement_mlm_db_ids" class='container animate__animated animate__fadeIn'>
                
                <a href="#" @click.prevent="goBackFromSelectPlacementMlmAccount"> < < Go Back</a>

                <div class='select-placement-table-div'>
                  
                  <p v-html="'Click To Select '+ placement_user_name + 's' + ' Mlm Account To Use As Placement.'" style="text-transform: capitalize; font-weight: bold" class="text-primary">
                    
                  </p>
                  <div class="table-div material-datatables table-responsive" style="">
                    <table class="table table-striped table-bordered nowrap hover display" id="select-placement-table" cellspacing="0" width="100%" style="width:100%">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Mlm Account</th>
                        </tr>
                      </thead>
                      <tbody>

                        <tr @click="selectThisUserAsPlacement(placement_user_name + ' (1)',mlm_db_id.id)" style="cursor:pointer;" v-for="mlm_db_id in all_mlm_ids" :key="mlm_db_id.i">
                          <td>{{mlm_db_id.i}}</td>
                          <td v-html="placement_user_name + ' (1)'"></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

              </div>

              <div v-show="!show_sponsor_details && !show_sponsor_form && show_select_placement_position && !show_placement_details && !show_placement_choice && !show_palcement_mlm_db_ids" class='container animate__animated animate__fadeIn'>
                
                <a href="#" @click.prevent="goBackFromSelectPlacementPosition"> < < Go Back</a>

                <h4 style="font-weight: bold;">Click To Select Position.</h4>
                <div class="table-div material-datatables table-responsive" style="">
                  <table class="table table-striped table-bordered nowrap hover display" id="select-placement-position-table" cellspacing="0" width="100%" style="width:100%">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Available Positions</th>
                        
                      </tr>
                    </thead>
                    <tbody>
                      
                      <tr v-if="available_positions == 'left'" style="cursor:pointer;" @click="selectThisPositionPlacement('left')" >
                        <td>1</td>
                        <td>Left</td>
                      </tr>
                      

                      
                      <tr v-if="available_positions == 'right'" style="cursor:pointer;" @click="selectThisPositionPlacement('right')" >
                        <td>1</td>
                        <td>Right</td>
                      </tr>
                      

                      
                      <tr v-if="available_positions == 'both'" style="cursor:pointer;" @click="selectThisPositionPlacement('left')" >
                        <td>1</td>
                        <td>Left</td>
                      </tr>

                      <tr v-if="available_positions == 'both'" style="cursor:pointer;" @click="selectThisPositionPlacement('right')" >
                        <td>2</td>
                        <td>Right</td>
                      </tr>
                      

                    </tbody>
                  </table>
                </div>
              </div>
                        

            </div>
          </div>

          <div class="card" v-show="!show_register_card">
            <div class="card-header">
              
            </div>
            <div class="card-body">
              <h3 class="" style="font-weight: bold;">De-Meet Cooperative and  Investment.(De-Meet C&I).</h3>

              <div class="row" style="margin-top: 20px;">
                <div class="col-sm-6">
                  <span v-show="show_short_desc" class="animate__animated  animate__fadeInRight"><b>Cooperative Networking events</b><br>
                  The main purpose of the cooperative is to promote enterprenuership through regular local Community enterprenuership networking events initiated....
                  <a href="#" @click.prevent="toggleDescLength">Show More</a>
                  
                 </span>

                  <span v-show="!show_short_desc" class="animate__animated  animate__fadeInLeft">

                  <b>Cooperative Networking events</b><br>
                  The main purpose of the cooperative is to promote enterprenuership through regular local Community enterprenuership networking events initiated by members.<br> <br>
                  Base on team volume, members gets N200,000 support fund once or twice every year after setting up Enterprenuership Networking Event in their local Community.<br><br>

                
                  <b>Fund Raising / Compensation plan.</b><br>
                  This is the first 2*18 generation compensation plan for fund raising where you earn from your Uplines and from your Downlines as well. <br>
                  You don't have Downlines? Then earn from your upline weekly.<br>
                  You have Downlines? Then you earn up and down weekly.<br>
                  You have started earning? Then from your weekly earnings, automatically share N2,000 to support and appreciate your downteam Cooperative members.<br>
                  Everyone earns.<br><br>

                  <b>Compensation Plan</b><br>
                  Registration = N2000 <br>
                  Sponsor bonus = N500<br>
                  Weekly Placement bonus = N20 × 2*18 generation.<br>
                  Weekly Upline Support = Based on Uplines team Strength <br>

                  <b>Savings culture.</b><br>
                  Save as little as N100, N200, N500 etc weekly.<br><br>

                  <b>Loan.</b><br>
                  Loan up to 100% of your previous month earnings.<br><br>

                  <a href="#" @click.prevent="toggleDescLength">Show Less</a>
                  </span>
                </div>
              </div>

              <div style="height:2px; background-color: #B0B0B0; width: 100%; border-radius: 4px; margin-top:20px; margin-bottom: 20px;"></div>

              <div class="" v-if="!user_info.coop_registered" >
                <h4 style="font-weight: bold; margin-bottom: 20px;">To Register</h4>

                <button @click="registerUser" class="btn btn-primary">Click Here</button>
              </div>
              <div class="" v-else>
                <h4 style="font-weight: bold; margin-bottom: 20px;">Access Functionality Below: </h4>

                <div class="list-group">
                  
                  <inertia-link class="list-group-item list-group-item-action d-flex justify-content-between align-items-center list-group-item-primary" :href="route('manage_investments')">
                    <span class="badge badge-primary badge-pill">1.</span>
                    <span style="font-weight: bold;">Manage Investments</span>
                    
                  </inertia-link>
                  <!-- <inertia-link class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" :href="route('manage_investment_loans')">
                    <span class="badge badge-primary badge-pill">2.</span>
                    <span style="font-weight: bold;">Manage Loans</span>
                      
                  </inertia-link> -->

                  <inertia-link class="list-group-item list-group-item-action d-flex justify-content-between align-items-center " :href="route('manage_cooperative_savings')">
                    <span class="badge badge-primary badge-pill">2.</span>
                      <span style="font-weight: bold;">Manage Savings</span>
                      
                  </inertia-link>

                  <inertia-link class="list-group-item list-group-item-action d-flex justify-content-between align-items-center list-group-item-primary " :href="route('manage_cooperative_earnings')">
                    <span class="badge badge-primary badge-pill">3.</span>
                      <span style="font-weight: bold;">Manage Earnings</span>
                      
                  </inertia-link>

                  

                  <inertia-link class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" :href="route('cooperative_earnings_genealogy_tree')">
                    <span class="badge badge-primary badge-pill">4.</span>
                      <span style="font-weight: bold;">Genealogy Tree</span>
                      
                  </inertia-link>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

     
      <div class="modal fade" data-backdrop="static" id="preview-transaction-modal" data-focus="true" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content" >
            <div class="modal-header text-center">
              <h3 class="modal-title">Preview Transaction</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body" id="modal-body">

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
import TextinputGroup from '../Shared/TextinputGroup'
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
    TextinputGroup,
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

    csrf: String,

    

  },
  data() {
    return {
      
      buy_airtime_request: this.$inertia.form({
        network: "mtn",
        selected_amount: 100,
        entered_amount: "100",
        phone_number: null,
        amount: null,
        recharge_type: "normal",
        quantity: 1,
        payable: null,
        epin_check: false,
        airtime_bonus: false,
      }),

      check_if_user_valid_request: this.$inertia.form({
        show_records: true,
      }),

      registration_request: this.$inertia.form({
        show_records: true,
        placement_ques: null,

      }),
      select_sponsor_form: this.$inertia.form({
        user_name: null,

      }),
      select_placement_form: this.$inertia.form({
        user_name: null,

      }),
      select_placement_mlmdb_id_form: this.$inertia.form({
        sponsor_mlm_db_id: null,
        user_name: null,
        mlm_db_id: null,
        position: null,
        
      }),
      register_user_without_placement: this.$inertia.form({
        register: true,
        sponsor_mlm_db_id: null,
      }),

      
      
      show_other_overlay: false,
      show_short_desc: true,
      show_sponsor_form: false,
      show_sponsor_details: false,
      show_register_card: false,
      show_placement_choice: false,
      show_placement_details: false,
      show_palcement_mlm_db_ids: false,
      show_select_placement_position: false,

      sponsor_user_profile_img: null,
      sponsor_full_name: null,
      sponsor_user_name: null,
      sponsor_phone_num: null,
      sponsor_email_address: null,

      placement_user_profile_img: null,
      placement_full_name: null,
      placement_user_name: null,
      placement_phone_num: null,
      placement_email_address: null,
      all_mlm_ids: [],
      available_positions: null,
      

    }
  },
  
  mounted() {
    
    console.log(this.$page.props)
  },
  created() {
    $("body").removeClass("modal-open");
    
  },
  methods: {
    goBackFromPlacementChoice() {
      var self = this;
      self.show_sponsor_form = false;
      self.show_sponsor_details = true;
      self.show_placement_details = false;
      self.show_placement_choice = false;
      self.show_palcement_mlm_db_ids = false;
      self.show_select_placement_position = false;
    },
    proceedToPlacementChoice(){
      var self = this;
      self.show_sponsor_form = false;
      self.show_sponsor_details = false;
      self.show_placement_details = false;
      self.show_placement_choice = true;
      self.show_palcement_mlm_db_ids = false;
      self.show_select_placement_position = false;
    },
    enterANewSponsor() {
      var self = this;
      self.show_sponsor_form = true;
      self.show_sponsor_details = false;
      self.show_placement_details = false;
      self.show_placement_choice = false;
      self.show_palcement_mlm_db_ids = false;
      self.show_select_placement_position = false;
    },
    submitSelectSponsorForm(){
      var self = this;
      
      self.select_sponsor_form.post(self.route('submit_sponsor_username_coop_regi'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success && response.user_profile_img != "" && response.sponsor_full_name != "" && response.sponsor_phone_num && response.sponsor_email_address != "" && response.sponsor_user_name != "" && response.sponsor_mlm_db_id != ""){
            
            self.sponsor_user_name = response.sponsor_user_name;
            self.sponsor_user_profile_img = response.user_profile_img;
            self.sponsor_full_name = response.sponsor_full_name;
            self.sponsor_phone_num = response.sponsor_phone_num;
            self.sponsor_email_address = response.sponsor_email_address;
            self.select_placement_mlmdb_id_form.sponsor_mlm_db_id = response.sponsor_mlm_db_id;
            self.register_user_without_placement.sponsor_mlm_db_id = response.sponsor_mlm_db_id;
            self.show_sponsor_form = false;
            self.show_sponsor_details = true;
            self.show_placement_details = false;
            self.show_placement_choice = false;
            self.show_palcement_mlm_db_ids = false;
            self.show_select_placement_position = false;
          }else if(response.empty_username){
            swal({
              title: 'Ooops',
              text: "The sponsor username field is required",
              type: 'error'
            })
          }else if(response.already_registered){
            swal({
              title: 'Ooops',
              text: "Seems You're Already Registered",
              type: 'error'
            })
          }else if(response.not_bouyant){
            swal({
              title: 'Ooops',
              text: "You do not have enough funds for registration. <br><br> <em class='text-primary'>Note: Registration fee is ₦2,000</em>",
              type: 'error'
            })
          }else if(response.invalid_username){
            swal({
              title: 'Ooops',
              text: "This user does not exist.",
              type: 'error'
            })
          }else if(response.invalid_placement){
            swal({
              title: 'Ooops',
              text: "Seems this user is not registered in <em class='text-primary'>De-Meet Cooperative and  Investment</em>.<br><br> Please select a registered user.",
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
    },
    placementQuesClick() {
      var self = this;
      if(self.registration_request.placement_ques == 0){
        swal({
          title: 'Proceed?',
          text: "Are you sure you want to proceed?<br><br> <em class='text-primary'>Note: A placement will be automatically selected for you.</em>",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#FF0000',
          confirmButtonText: 'Yes Proceed',
          cancelButtonText : "Cancel",
          allowOutsideClick: false,
        }).then(function(){
          $.notify({
            message:"Completing Your Registration... Please Wait."
          },{
              type : "success"  
          });
          
          self.register_user_without_placement.post(self.route('register_coop_inv_without_placem'), {
            preserveScroll: true,
            onSuccess: (page) => {
              
              var response = JSON.parse(JSON.stringify(self.response_arr))
              console.log(response)

              var text_html = "Your Cooperative Investment Account Has Been Created Successfully."
              if(response.success){
                swal({
                  title: 'Success',
                  text: text_html,
                  type: 'success',
                  confirmButtonColor: '#3085d6',
                  allowOutsideClick: false,
                }).then(function(){
                  self.$inertia.visit(self.route('cooperative_investment'));
                });
              }else if(response.invalid_sponsor){
                swal({
                  title: 'Ooops',
                  text: "Seems sponsor selected is not registered in <em class='text-primary'>De-Meet Cooperative and  Investment</em>.<br><br> Please go back and select a registered sponsor.",
                  type: 'error'
                })
              }else if(response.already_registered){
                swal({
                  title: 'Ooops',
                  text: "Seems You're Already Registered",
                  type: 'error'
                })
              }else if(response.not_bouyant){
                swal({
                  title: 'Ooops',
                  text: "You do not have enough funds for registration. <br><br> <em class='text-primary'>Note: Registration fee is ₦2,000</em>",
                  type: 'error'
                })
              }else if(response.details_missing){
                swal({
                  title: 'Ooops',
                  text: "Key details missing in this request. Please try again.",
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
        },function(dismiss){
          if(dismiss == 'cancel'){
            self.registration_request.placement_ques = null;
          }
        });
      }else{
        self.show_placement_choice = false;
        self.show_placement_details = false;
        self.show_palcement_mlm_db_ids = false;
        self.show_select_placement_position = false;
      }
    },
    goBackFromSelectPlacementPosition(){
      var self = this;
      self.show_placement_details = false;
      self.show_placement_choice = false;
      self.show_palcement_mlm_db_ids = true;
      self.show_select_placement_position = false;
    },
    selectThisPositionPlacement(position) {
      var self = this;
      console.log(position)
      var placement_id = self.select_placement_mlmdb_id_form.mlm_db_id;
      self.select_placement_mlmdb_id_form.position = position;
      
      if(placement_id != "" && position != ""){
        swal({
          title: 'Confirm?',
          text: "Are You Sure You Want To Select Position " + position + "?",
          type: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes',
          cancelButtonText : "No"
        }).then(function(){
            
          $.notify({
            message:"Placement Selected. Completing Your Registration..."
          },{
              type : "success"  
          });
          

          

          self.select_placement_mlmdb_id_form.post(self.route('finally_register_user_coop_inv'), {
            preserveScroll: true,
            onSuccess: (page) => {
              
              var response = JSON.parse(JSON.stringify(self.response_arr))
              console.log(response)

              var text_html = "Your Cooperative Investment Account Has Been Created Successfully."
              if(response.success){
                swal({
                  title: 'Success',
                  text: text_html,
                  type: 'success',
                  confirmButtonColor: '#3085d6',
                  allowOutsideClick: false,
                }).then(function(){
                  self.$inertia.visit(self.route('cooperative_investment'));
                });
              }else if(response.invalid_sponsor){
                swal({
                  title: 'Ooops',
                  text: "Seems sponsor selected is not registered in <em class='text-primary'>De-Meet Cooperative and  Investment</em>.<br><br> Please go back and select a registered sponsor.",
                  type: 'error'
                })
              }else if(response.no_available_position){
                swal({
                  title: 'No Available Position',
                  text: "Sorry No Available Position Under This Account.",
                  type: 'error'
                })
              }else if(response.placement_invalid){
                swal({
                  title: 'Ooops',
                  text: "Seems this user is not registered in <em class='text-primary'>De-Meet Cooperative and  Investment</em>.<br><br> Please select a registered user.",
                  type: 'error'
                })
              }else if(response.empty_username){
                swal({
                  title: 'Ooops',
                  text: "The placement username field is required",
                  type: 'error'
                })
              }else if(response.already_registered){
                swal({
                  title: 'Ooops',
                  text: "Seems You're Already Registered",
                  type: 'error'
                })
              }else if(response.not_bouyant){
                swal({
                  title: 'Ooops',
                  text: "You do not have enough funds for registration. <br><br> <em class='text-primary'>Note: Registration fee is ₦2,000</em>",
                  type: 'error'
                })
              }else if(response.invalid_username){
                swal({
                  title: 'Ooops',
                  text: "This user does not exist.",
                  type: 'error'
                })
              }else if(response.invalid_placement){
                swal({
                  title: 'Ooops',
                  text: "Seems this user is not registered in <em class='text-primary'>De-Meet Cooperative and  Investment</em>.<br><br> Please select a registered user.",
                  type: 'error'
                })
              }else if(response.details_missing){
                swal({
                  title: 'Ooops',
                  text: "Key details missing in this request. Please try again.",
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
      }
    },
    selectThisUserAsPlacement(str,mlm_db_id){
      var self = this;
      self.select_placement_mlmdb_id_form.mlm_db_id = mlm_db_id;
      self.select_placement_mlmdb_id_form.user_name = self.select_placement_form.user_name;
      console.log(str)
      swal({
        title: 'Proceed?',
        html: "Are You Sure You Want To Select <em class='text-primary'>"+str+"</em> As Your Placement?",
        type: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes Proceed!',
        cancelButtonText : "No Cancel"
      }).then(function(){
        
        
        
        self.select_placement_mlmdb_id_form.post(self.route('select_positioning_for_coop_inv_regi'), {
          preserveScroll: true,
          onSuccess: (page) => {
            
            var response = JSON.parse(JSON.stringify(self.response_arr))
            console.log(response)

            if(response.success && response.available_positions != ""){
              self.available_positions = response.available_positions;
              
              self.show_placement_details = false;
              self.show_placement_choice = false;
              self.show_palcement_mlm_db_ids = false;
              self.show_select_placement_position = true;
            }else if(response.no_available_position){
              swal({
                title: 'No Available Position',
                text: "Sorry No Available Position Under This Account.",
                type: 'error'
              })
            }else if(response.placement_invalid){
              swal({
                title: 'Ooops',
                text: "Seems this user is not registered in <em class='text-primary'>De-Meet Cooperative and  Investment</em>.<br><br> Please select a registered user.",
                type: 'error'
              })
            }else if(response.empty_username){
              swal({
                title: 'Ooops',
                text: "The placement username field is required",
                type: 'error'
              })
            }else if(response.already_registered){
              swal({
                title: 'Ooops',
                text: "Seems You're Already Registered",
                type: 'error'
              })
            }else if(response.not_bouyant){
              swal({
                title: 'Ooops',
                text: "You do not have enough funds for registration. <br><br> <em class='text-primary'>Note: Registration fee is ₦2,000</em>",
                type: 'error'
              })
            }else if(response.invalid_username){
              swal({
                title: 'Ooops',
                text: "This user does not exist.",
                type: 'error'
              })
            }else if(response.invalid_placement){
              swal({
                title: 'Ooops',
                text: "Seems this user is not registered in <em class='text-primary'>De-Meet Cooperative and  Investment</em>.<br><br> Please select a registered user.",
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
    },
    goBackFromSelectPlacementMlmAccount(){
      var self = this;
      self.show_placement_details = true;
      self.show_placement_choice = false;
      self.show_palcement_mlm_db_ids = false;
      self.show_select_placement_position = false;
    },
    proceedToPlacementMlmAccount(){
      var self = this;
      
      self.select_placement_form.post(self.route('submit_placement_username_coop_regi_step2'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success && response.all_mlm_ids.length > 0){
            self.all_mlm_ids = response.all_mlm_ids;
            
            self.show_placement_details = false;
            self.show_placement_choice = false;
            self.show_palcement_mlm_db_ids = true;
            self.show_select_placement_position = false;
          }else if(response.placement_invalid){
            swal({
              title: 'Ooops',
              text: "Seems this user is not registered in <em class='text-primary'>De-Meet Cooperative and  Investment</em>.<br><br> Please select a registered user.",
              type: 'error'
            })
          }else if(response.empty_username){
            swal({
              title: 'Ooops',
              text: "The placement username field is required",
              type: 'error'
            })
          }else if(response.already_registered){
            swal({
              title: 'Ooops',
              text: "Seems You're Already Registered",
              type: 'error'
            })
          }else if(response.not_bouyant){
            swal({
              title: 'Ooops',
              text: "You do not have enough funds for registration. <br><br> <em class='text-primary'>Note: Registration fee is ₦2,000</em>",
              type: 'error'
            })
          }else if(response.invalid_username){
            swal({
              title: 'Ooops',
              text: "This user does not exist.",
              type: 'error'
            })
          }else if(response.invalid_placement){
            swal({
              title: 'Ooops',
              text: "Seems this user is not registered in <em class='text-primary'>De-Meet Cooperative and  Investment</em>.<br><br> Please select a registered user.",
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
    },
    enterANewPlacement() {
      var self = this;
      self.show_placement_details = false;
      self.show_placement_choice = false;
      self.show_palcement_mlm_db_ids = false;
      self.show_select_placement_position = false;
    },
    submitSelectPlacementForm(){
      var self = this;
      
      self.select_placement_form.post(self.route('submit_placement_username_coop_regi'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success && response.user_profile_img != "" && response.placement_full_name != "" && response.placement_phone_num && response.placement_email_address != "" && response.placement_user_name != ""){
            
            self.placement_user_name = response.placement_user_name;
            self.placement_user_profile_img = response.user_profile_img;
            self.placement_full_name = response.placement_full_name;
            self.placement_phone_num = response.placement_phone_num;
            self.placement_email_address = response.placement_email_address;
            self.show_placement_details = true;
            self.show_placement_choice = false;
            self.show_palcement_mlm_db_ids = false;
            self.show_select_placement_position = false;
          }else if(response.empty_username){
            swal({
              title: 'Ooops',
              text: "The placement username field is required",
              type: 'error'
            })
          }else if(response.already_registered){
            swal({
              title: 'Ooops',
              text: "Seems You're Already Registered",
              type: 'error'
            })
          }else if(response.not_bouyant){
            swal({
              title: 'Ooops',
              text: "You do not have enough funds for registration. <br><br> <em class='text-primary'>Note: Registration fee is ₦2,000</em>",
              type: 'error'
            })
          }else if(response.invalid_username){
            swal({
              title: 'Ooops',
              text: "This user does not exist.",
              type: 'error'
            })
          }else if(response.invalid_placement){
            swal({
              title: 'Ooops',
              text: "Seems this user is not registered in <em class='text-primary'>De-Meet Cooperative and  Investment</em>.<br><br> Please select a registered user.",
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
    },
    goBackFromEnterPlacementUsernameForm() {
      var self = this;
      self.show_placement_choice = true;
      self.show_placement_details = false;
      self.show_palcement_mlm_db_ids = false;
      self.show_select_placement_position = false;
      self.registration_request.placement_ques = null;
    },
    
    goBackFromRegisterUser() {
      var self = this;
      self.show_register_card = false
      self.show_sponsor_form = false;
      self.show_placement_choice = false;
      self.show_placement_details = false;
      self.show_palcement_mlm_db_ids = false;
      self.show_select_placement_position = false;
    },
    registerUser() {

      var self = this;
      
      self.check_if_user_valid_request.post(self.route('check_if_user_valid_to_register_coop_inv'), {
        preserveScroll: true,
        onSuccess: (page) => {
          
          var response = JSON.parse(JSON.stringify(self.response_arr))
          console.log(response)

          if(response.success){
            self.show_register_card = true;
            self.show_sponsor_form = true;
            self.show_placement_choice = false;
            self.show_placement_details = false;
            self.show_palcement_mlm_db_ids = false;
            self.show_select_placement_position = false;
          }else{
            swal({
              title: 'Ooops',
              text: "Seems You're Already Registerd",
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
    
      

    },
    toggleDescLength(){
      this.show_short_desc = !this.show_short_desc;
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
