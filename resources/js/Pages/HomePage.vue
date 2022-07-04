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
  .ql-container.ql-snow {
    border: 0;
}
</style>

<template>
    <div class="content">
      <div >
        <div class="container-fluid">
          <div class="row">
            <div class="col-12 card">
              <div class="card-body" v-if="user_info.providus_account_number != null && user_info.providus_account_name != null">
                <h5 class="text-bold">Personalized Account Funding</h5>
                <h6>Providus Bank</h6>
                <p class="text-bold">
                  <span>From ₦1 to ₦9,999 attracts ₦20 charge</span><br>
                  <span>₦10,000 or greater attracts ₦70 charge</span>
                </p>
                <tr>
                  <td></td>
                  <td><em class="text-primary">{{user_info.providus_account_name}}</em></td>
                </tr>
                <tr>
                  <td></td>
                  <td><em class="text-primary">{{user_info.providus_account_number}}</em></td>
                </tr>
                    
                  
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="fas fa-wallet"></i>
                  </div>
                  <p class="card-category">E-Wallet</p>
                  <h3 class="card-title"><small>₦{{wallet_balance}}</small></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    
                    <inertia-link :href="route('wallet_statement')">View More Info</inertia-link>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-success card-header-icon">
                  <div class="card-icon">
                    <i class="fas fa-dollar-sign"></i>
                  </div>
                  <p class="card-category">Withdrawn</p>
                  <h3 class="card-title"><small>₦{{total_amount_wthdrawn}}</small></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <inertia-link :href="route('funds_withdrawal')">View More Info</inertia-link>
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
                  <p class="card-category">Commision</p>
                  
                  <h3 class="card-title"><small>₦{{different_earnings.total_business_earnings}}</small></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <inertia-link :href="route('user_earnings')">View More Info</inertia-link>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="fas fa-cubes"></i>
                  </div>
                  <p class="card-category">Team Total</p>
                  
                  <h3 class="card-title"><small>{{downline_arr_num}}</small></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <inertia-link :href="route('genealogy_tree')">View More Info</inertia-link>
                  </div>
                </div>
              </div>
            </div>

            

            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-rose card-header-icon">
                  <div class="card-icon">
                    <i class="fas fa-users"></i>
                  </div>
                  <p class="card-category">Business Team Total</p>
                  
                  <h3 class="card-title"><small>{{business_team_total}}</small></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <inertia-link :href="route('genealogy_tree')">View More Info</inertia-link>
                  </div>
                </div>
              </div>
            </div>
          </div>
         


          <div class="row" style="margin-top: 60px;">

            <div class="col-md-6">
              <div class="card card-profile">
                <div class="card-avatar">
                  <a href="javascript:;">
                    
                    <img class="img" :src="user_avatar">
                  </a>
                </div>
                <div class="card-body">
                  <h6 class="card-category text-gray" style="text-transform: lowercase;">{{user_info.user_name}}</h6>
                  <h4 class="card-title" style="text-transform: capitalize;">{{user_info.full_name}}</h4>
                  <p class="card-description">
                    {{short_bio}}
                  </p>
                  
                  <inertia-link :href="route('edit_profile', user_info.slug)" class="btn btn-primary btn-round">Edit Profile</inertia-link>
                 
                  <inertia-link :href="route('user_profile', $page.props.user_info.slug) + '?compose=true'" class="btn btn-info btn-round"><i style="font-size: 15px;" class="fas fa-pen"></i>&nbsp;&nbsp;Compose</inertia-link>
                </div>
                <div class="text-left" style="margin-top: 20px; margin-left: 20px; margin-right: 20px;">
                  <h4 class="text-primary text-center" style="font-weight: bold; margin-bottom: 25px;">Promotion Tools</h4>
                  <div class="row">
                    <p class="text-secondary col-6">Referral Link</p> 
                    <div class="col-6 text-right">
                      <a :href="'https://www.facebook.com/sharer/sharer.php?u=https://meetglobalresources.com/login_page?id=' + user_info.slug" target="_blank" class="btn btn-social btn-link btn-facebook" data-placement="top" data-toggle="tooltip" title="Share On Facebook" style="padding: 0; margin-right: 10px;">
                        <i class="fa fa-facebook-square"> </i>
                      </a>

                      <a :href="'http://twitter.com/share?url=https://meetglobalresources.com/login_page?id=' + user_info.slug" class="btn btn-social btn-link btn-twitter" target="_blank" data-toggle="tooltip" title="Share On Twitter"  style="padding: 0; margin-right: 10px;">
                        <i class="fa fa-twitter"></i>
                      </a>

                      <a :href="'https://api.whatsapp.com/send?text=https://meetglobalresources.com/login_page?id=' + user_info.slug" class="btn btn-social btn-link btn-whatsapp" data-toggle="tooltip" title="Share On Whatsapp"  style="padding: 0;">
                        <img style="background: transparent; width: 30px; height: 20px; margin-right: 6px;" src="/images/whatsapp.svg" alt="">
                      </a>

                    </div>
                  </div>

                  <div class="input-group mb-3">
                    <input type="text" class="form-control" :value="'https://meetglobalresources.com/login_page?id=' + user_info.slug" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                      <button class="btn btn-primary btn-round" type="button" @click='copyText("https://meetglobalresources.com/login_page?id=" + user_info.slug)'>Copy</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

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
                  
                    <h4 class="card-title">Referrals For ({{month_year}})</h4>
                </div>
                <div class="card-footer">
                  <!-- <div class="stats">
                    <i class="material-icons">access_time</i> updated 4 minutes ago
                  </div> -->
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

          <div class="row justify-content-center">
            <h4 class="text-center col-12">Social Media</h4>
            <div class="col-sm-6">
              <img src="/images/ezgif-6-7fdcb0c81d0c.gif" alt="" class="col-12">
              <div class="row justify-content-center">
                
                <inertia-link :href="route('main_chat_page')" class="btn btn-primary btn-round col-3">
                  <i class="material-icons">favorite</i> <small style="font-size: 8px;">Like</small>
                </inertia-link>
                
                <inertia-link :href="route('main_chat_page')" class="btn btn-info btn-round col-3">
                  <i class="far fa-comment" style="font-size: 16px;"></i> <small style="font-size: 8px;">Comment</small>
                </inertia-link>
                
                <inertia-link :href="route('main_chat_page')" class="btn btn-info btn-rose col-3">
                  <i class="far fa-share-square" style="font-size: 16px;"></i> <small style="font-size: 8px;">Share</small>
                </inertia-link>
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
import LoadingButton from '../Shared/LoadingButton'


export default {
  metaInfo: { title: 'Overview' },
  components: {
    Pagination,
    SearchFilter,
    FloatingActionButton,
    TextInput,
    LoadingButton,

  },
  layout: Layout,
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

    wallet_balance: String,
    total_amount_wthdrawn: String,
    different_earnings: Object,
    downline_arr: Array,
    downline_arr_num: String,
    business_team_total: String,
    user_avatar: String,
    short_bio: String,
    month_year: String,
    month_year_2: String,
    top_mlm_earners: Array,
    chartist_arr: Array,
    front_page_title: String,
    front_page_text: String,
    front_page_type: String,


  },
  data() {
    return {
      form: this.$inertia.form({
        

      }),
      chartist_options: {
        seriesBarDistance: 10
      },
      q: ""
    }
  },
  
  mounted() {
    var self = this;
    console.log(this.top_mlm_earners)
    $("#top-ten-mlm-earners-table").DataTable();
    
    swal({
      title: this.front_page_title,
      
      text: "<div id='tmessage'></div>",
      type: this.front_page_type,                                        
    })

    self.q = new Quill('#tmessage', {
      theme : 'snow',
      readOnly : true,
      modules : {
          "toolbar": false
      }
    });

    
      
    self.q.setContents(JSON.parse(self.front_page_text));
    
  },
  created() {
    $("body").removeClass("modal-open");

    
    
    this.chartist_data = {
      labels: ['1st', '2nd', '3rd', '4th'],
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
