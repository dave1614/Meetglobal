<template>
  
  
  <div class="wrapper ">
    <div id="sound" style="visibility: hidden;"></div>
    <div class="sidebar" data-color="purple" data-background-color="white" data-image="/images/sidebar-img.jpeg">
    
      <div class="sidebar-wrapper">
          
          <div class="user">
            <div class="photo">
              
              <img :src="user_avatar" class="small-profile-img" />
            </div>
            <div class="user-info">
                <a data-toggle="collapse" href="#collapseExample" class="username">
                    <span style="text-transform: capitalize;">
                       {{$page.props.user_info.user_name}}
                      <b class="caret"></b>
                    </span>
                </a>
                <div class="collapse" id="collapseExample">
                    <ul class="nav">
                        
                        <li class="nav-item">
                            <inertia-link class="nav-link" :href="route('user_profile', $page.props.user_info.slug)">
                              <span class="sidebar-mini"> YP </span>
                              <span class="sidebar-normal"> Your Profile </span>
                            </inertia-link>
                        </li>

                        <li :class="$page.props.active_page == 'change_password' ? 'nav-item active' : 'nav-item' ">
                            <inertia-link class="nav-link" :href="route('change_password')">
                              <span class="sidebar-mini"> CP </span>
                              <span class="sidebar-normal"> Change Your Password </span>
                            </inertia-link>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#" @click="showReferralCode">
                              <span class="sidebar-mini"> RL </span>
                              <span class="sidebar-normal"> Referral Link </span>
                            </a>
                        </li>
 
                    </ul>
                </div>
            </div>
          </div>
          <ul class="nav">

            <li class="nav-item">
                <a class="nav-link" href="/logout">
                    <i class="fas fa-sign-out-alt" style="font-size: 20px;"></i>
                    <p> Logout </p>
                </a>
            </li>

            <li :class="$page.props.active_page == 'dashboard' ? 'nav-item active' : 'nav-item' ">
                <inertia-link class="nav-link" :href="route('admin_page')">
                    <i class="material-icons">dashboard</i>
                    <p> Dashboard </p>
                </inertia-link>
            </li>

            <li :class="$page.props.active_page == 'create_receipt' ? 'nav-item active' : 'nav-item' ">
              
              <inertia-link class="nav-link" :href="route('create_receipt')">  
                  <i class="fas fa-users"></i>
                  <p> CREATE RECEIPT </p>
              </inertia-link>
            </li>

            <li :class="$page.props.active_page == 'cooperative_members_admin' ? 'nav-item active' : 'nav-item' ">
              
              <inertia-link class="nav-link" :href="route('cooperative_members_admin')">  
                  <i class="far fa-money-bill-alt"></i>
                  <p> COOP. MEMBERS </p>
              </inertia-link>
            </li>

            <li :class="$page.props.active_page == 'cooperative_investments_admin' ? 'nav-item active' : 'nav-item' ">
              
              <inertia-link class="nav-link" :href="route('cooperative_investments_admin')">  
                  <i class="far fa-money-bill-alt"></i>
                  <p> COOP. INVESTMENTS </p>
              </inertia-link>
            </li>

            <li :class="$page.props.active_page == 'airtime_to_wallet_records' ? 'nav-item active' : 'nav-item' ">
              
              <inertia-link class="nav-link" :href="route('airtime_to_wallet_records')">  
                  <i class="fas fa-users"></i>
                  <p> AIRTIME TO WALLET </p>
              </inertia-link>
            </li>

            <li :class="$page.props.active_page == 'front_page_message' ? 'nav-item active' : 'nav-item' ">
              
              <inertia-link class="nav-link" :href="route('front_page_message')">  
                  <i class="fas fa-users"></i>
                  <p> FRONT PAGE MESSAGE </p>
              </inertia-link>
            </li>

            <li :class="$page.props.active_page == 'sim_activation_initiative' ? 'nav-item active' : 'nav-item' ">
              
              <inertia-link class="nav-link" :href="route('sim_activation_initiative')">  
                  <i class="fas fa-users"></i>
                  <p> SIM ACTIVATION INCENTIVE </p>
              </inertia-link>
            </li>

            
            <!-- <li :class="$page.props.active_page == 'manage_epins' ? 'nav-item active' : 'nav-item' ">
              
              <inertia-link class="nav-link" :href="route('manage_epins')">
                  <i class="fab fa-etsy"></i>
                  <p> Manage E-Pins </p>
              </inertia-link>
            </li> -->

            
            <li :class="$page.props.active_page == 'complaints' ? 'nav-item active' : 'nav-item' ">
              <inertia-link class="nav-link" :href="route('complaints')">
                  <i class="fab fa-cuttlefish"></i>
                  <p> Complaints </p>
              </inertia-link>
            </li>


            <li :class="$page.props.active_page == 'genealogy' || $page.props.active_page == 'sponsor' || $page.props.active_page == 'downline'  ? 'nav-item active' : 'nav-item' ">
              
              <a class="nav-link" data-toggle="collapse" href="#network-nav-link" aria-expanded="true">
                <!-- <i class="material-icons">chat</i> -->
                <i class="fa fa-sitemap"></i>
                <p> Network 

                  <b class="caret"></b>
                </p>
              </a>
               
              <div id="network-nav-link" :class="$page.props.active_page == 'genealogy' || $page.props.active_page == 'sponsor' || $page.props.active_page == 'downline'  ? 'collapse show' : 'collapse' ">
                <ul class="nav">
                  
                  <li :class="$page.props.active_page == 'genealogy' ? 'nav-item active' : 'nav-item' ">
                      <inertia-link class="nav-link" :href="route('genealogy_tree')">
                        <span class="sidebar-mini"> GT </span>
                        <span class="sidebar-normal"> Genealogy Tree</span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'sponsor' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('sponsor_tree')">
                        <span class="sidebar-mini"> ST </span>
                        <span class="sidebar-normal"> Sponsor Tree</span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'downline' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('downline_members')">  
                        <span class="sidebar-mini"> DM </span>
                        <span class="sidebar-normal"> Downline Members</span>
                      </inertia-link>
                  </li>

                </ul>
              </div>
            </li>


            
            <li :class="$page.props.active_page == 'account_credit_requests' ? 'nav-item active' : 'nav-item' ">
              
              <inertia-link class="nav-link" :href="route('account_credit_requests')">  
                  <i class="fas fa-users"></i>
                  <p> Account Credit Requests </p>
              </inertia-link>
            </li>


            
            <li :class="$page.props.active_page == 'account_withdrawal_requests' ? 'nav-item active' : 'nav-item' ">
              
              <inertia-link class="nav-link" :href="route('account_withdrawal_requests')">  
                  <i class="fas fa-users"></i>
                  <p> Account Withdrawal Req. </p>
              </inertia-link>
            </li>

            <li :class="$page.props.active_page == 'user_earnings' ? 'nav-item active' : 'nav-item' ">

              <inertia-link class="nav-link" :href="route('user_earnings')">
                  <i class="fas fa-dollar-sign"></i>
                  <p> Earnings </p>
              </inertia-link>
            </li>

            
            <!-- <li :class="$page.props.active_page == 'admin_center_leader' || $page.props.active_page == 'admin_center_connector' || $page.props.active_page == 'admin_vendor' || $page.props.active_page == 'admin_shop'  ? 'nav-item active' : 'nav-item' ">
              
              <a class="nav-link" data-toggle="collapse" href="#mini-importation-link" aria-expanded="true"> -->
                <!-- <i class="material-icons">chat</i> -->
                <!-- <i class="fas fa-truck"></i>
                <p> Mini Importation 

                  <b class="caret"></b>
                </p>
              </a> -->
              
              <!-- <div id="mini-importation-link" :class="$page.props.active_page == 'admin_center_leader' || $page.props.active_page == 'admin_center_connector' || $page.props.active_page == 'admin_vendor' || $page.props.active_page == 'admin_shop'  ? 'collapse show' : 'collapse' ">
                <ul class="nav">


                  
                  <li :class="$page.props.active_page == 'admin_center_leader' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('admin_center_leader')">
                        <span class="sidebar-mini"> CL </span>
                        <span class="sidebar-normal">Center Leader </span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'admin_center_connector' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('admin_center_connector')">
                        <span class="sidebar-mini"> CU </span>
                        <span class="sidebar-normal"> Center Connector </span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'admin_vendor' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('admin_vendor')">
                        <span class="sidebar-mini"> VH </span>
                        <span class="sidebar-normal"> Vendor </span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'admin_shop' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('admin_shop')">
                        <span class="sidebar-mini"> S </span>
                        <span class="sidebar-normal"> Shop </span>
                      </inertia-link>
                  </li>

                  

                </ul>
              </div>
            </li> -->

            <!-- <li class="nav-item">
              
              <inertia-link class="nav-link" :href="route('user_profile', $page.props.user_info.slug) + '?compose=true'">
                <i class="fas fa-edit" style="font-size: 20px;"></i>
                <span class="sidebar-normal">Compose</span>
              </inertia-link>
            </li> -->

            <!-- <li :class="$page.props.active_page == 'messages' ? 'nav-item active' : 'nav-item' " id="new-messages-nav-item" >
              
              <inertia-link class="nav-link" :href="route('messages')">
                <i class="fab fa-facebook-messenger" style="font-size: 25px;"></i>
                <span class="sidebar-normal">Messages  ({{$page.props.new_message_count}})</span>
              </inertia-link>
            </li> -->
            
            <li :class="$page.props.active_page == 'notifications' ? 'nav-item active' : 'nav-item' " id="new-notifs-nav-item" >
              
              <inertia-link class="nav-link" :href="route('notifications')">
                <i class="material-icons">notifications</i>
                <span class="sidebar-normal" v-html="'Notifications ' + $page.props.new_notifs_count + '' "></span>
              </inertia-link>
            </li>

            
            <li :class="$page.props.active_page == 'members_list' || $page.props.active_page == 'change_password'? 'nav-item active' : 'nav-item' ">
              <a class="nav-link" data-toggle="collapse" href="#profile-management-nav-link" aria-expanded="true">
                <!-- <i class="material-icons">chat</i> -->
                <i class="fa fa-address-book-o"></i>
                <p> Profile Management 

                  <b class="caret"></b>
                </p>
              </a>
             
              <div id="profile-management-nav-link" :class="$page.props.active_page == 'members_list' || $page.props.active_page == 'change_password'  ? 'collapse show' : 'collapse' ">
                <ul class="nav">
                          
                  
                  <li :class="$page.props.active_page == 'members_list' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('view_members_list')">
                        <span class="sidebar-mini"> ML </span>
                        <span class="sidebar-normal"> Members List</span>
                      </inertia-link>
                  </li>

                </ul>
              </div>
            </li>


            <li :class="$page.props.active_page == 'view_admin_vtu_earnings' || $page.props.active_page == 'airtime_combo_requests' || $page.props.active_page == 'data_combo_requests' || $page.props.active_page == 'account_credit_history' || $page.props.active_page == 'admin_account_credit_history' || $page.props.active_page == 'account_debit_history' || $page.props.active_page == 'product_advance_history' || $page.props.active_page == 'view_advance_loan_history' ? 'nav-item active' : 'nav-item' ">
              <a class="nav-link" data-toggle="collapse" href="#reports-nav-link" aria-expanded="true">
                <!-- <i class="material-icons">chat</i> -->
                <i class="fa fa-bar-chart"></i>
                <p> Reports 

                  <b class="caret"></b>
                </p>
              </a>
              

              <div id="reports-nav-link" :class="$page.props.active_page == 'view_admin_vtu_earnings' || $page.props.active_page == 'airtime_combo_requests' || $page.props.active_page == 'data_combo_requests' || $page.props.active_page == 'account_credit_history' || $page.props.active_page == 'admin_account_credit_history' || $page.props.active_page == 'account_debit_history' || $page.props.active_page == 'product_advance_history' || $page.props.active_page == 'view_advance_loan_history'  ? 'collapse show' : 'collapse' ">
                <ul class="nav">

                  <li :class="$page.props.active_page == 'view_admin_vtu_earnings' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('view_admin_vtu_earnings')">
                          <span class="sidebar-mini"> AV</span>
                          <p> Admin Vtu Earnings</p>
                      </inertia-link>
                  </li>
                  
                  <li :class="$page.props.active_page == 'view_advance_loan_history' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('view_advance_loan_history')">
                          <span class="sidebar-mini"> AL</span>
                          <p> Advance Loan History</p>
                      </inertia-link>
                  </li>

                  <li :class="$page.props.active_page == 'airtime_combo_requests' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('airtime_combo_requests')">
                          <span class="sidebar-mini"> AC</span>
                          <p> Airtime Combo Requests </p>
                      </inertia-link>
                  </li>


                  
                  <li :class="$page.props.active_page == 'data_combo_requests' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('data_combo_requests')">
                          <span class="sidebar-mini"> DC</span>
                          <p> Data Combo Requests </p>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'account_credit_history' ? 'nav-item active' : 'nav-item' ">
                    <inertia-link class="nav-link" :href="route('account_credit_history')">
                        <span class="sidebar-mini"> AC</span>
                        <p>  Account Credit Hist. </p>
                    </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'admin_account_credit_history' ? 'nav-item active' : 'nav-item' ">
                    
                    <inertia-link class="nav-link" :href="route('admin_account_credit_history')">
                        <span class="sidebar-mini"> CH</span>
                        <p> Admin Acct Credit Hist. </p>
                    </inertia-link>
                  </li>


                  <li :class="$page.props.active_page == 'account_debit_history' ? 'nav-item active' : 'nav-item' ">
                    
                    <inertia-link class="nav-link" :href="route('account_debit_history')">
                        <span class="sidebar-mini"> DH </span>
                        <p> Admin Acct Debit Hist. </p>
                    </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'product_advance_history' ? 'nav-item active' : 'nav-item' ">
                    
                    <inertia-link class="nav-link" :href="route('product_advance_history')">
                        <span class="sidebar-mini"> PA </span>
                        <p> Product Advance Hist. </p>
                    </inertia-link>
                  </li>
                  
                </ul>
              </div>
            </li>


            
            <li :class="$page.props.active_page == 'news' || $page.props.active_page == 'feedback' || $page.props.active_page == 'faq' ? 'nav-item active' : 'nav-item' ">
              <a class="nav-link" data-toggle="collapse" href="#tools-nav-link" aria-expanded="true">
                <!-- <i class="material-icons">chat</i> -->
                <i class="fa fa-wrench"></i>
                <p> Tools 

                  <b class="caret"></b>
                </p>
              </a>
              
              <div id="tools-nav-link" :class="$page.props.active_page == 'news' || $page.props.active_page == 'feedback' || $page.props.active_page == 'faq' ? 'collapse show' : 'collapse' ">
                <ul class="nav">
                          
                  
                  <li :class="$page.props.active_page == 'news' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('manage_news')">
                        <span class="sidebar-mini"> N </span>
                        <span class="sidebar-normal"> News</span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'feedback' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('manage_feedback')">
                        <span class="sidebar-mini"> FB </span>
                        <span class="sidebar-normal"> Feedback</span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'faq' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('manage_faq')">
                        <span class="sidebar-mini"> F </span>
                        <span class="sidebar-normal"> FAQS</span>
                      </inertia-link>
                  </li>

                  
                </ul>
              </div>
            </li>
            
          </ul>
      </div>
    </div>
    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            
            <a class="navbar-brand" href="#">{{$page.props.page_title}}</a>
            
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end">
            <!-- <form class="navbar-form" id="main-search-form">
              <div class="input-group no-border">
                
                <div class="autocomplete">
                  <input type="text" :value="$page.props.global_search_val" class="form-control" placeholder="Search..." autocomplete="off" id="myInput" name="my-input">
                </div>
                <button type="submit" class="btn btn-white btn-round btn-just-icon">
                  <i class="material-icons">search</i>
                  <div class="ripple-container"></div>
                </button>
              </div>
            </form> -->
               
            
            <ul class="navbar-nav">
              
              <!-- <li id="noti_Container">
                  <li id="notification_li">
                    <a href="#" id="notificationLink" @click="notificationLinkClick" style="color: unset;">
                      <i class="fab fa-facebook-messenger" style="font-size: 25px; margin: 20px;">
                        <span class="new-message-num notification" v-if="$page.props.new_message_count > 0">  {{$page.props.new_message_count}}
                        </span>
                      </i>
                    </a>
                    <div id="notificationContainer" :class="message_box_class_name">
                      <div id="notificationTitle">Recent</div>
                        <div id="notificationsBody" class="notifications">
                          <div class="container">
                            <div v-if="$page.props.all_messages.length > 0">
                              <div class="conversation row" v-for="notif in $page.props.all_messages" :key="notif.index">
                                <div class="col-sm-2 avatar">
                                  <img :src="notif.sender_logo" :alt="notif.sender_username">
                                </div>
                                <div class="col-sm-6 message-sender">
                                  <p class="sender-name">{{notif.sender_username + " " + notif.new_messages_num_frm_sender}}
                                 
                                  </p>
                                  <span class="last-message">{{notif.short_message}}</span>
                                </div>
                                <div class="col-sm-4 time-stamp">
                                  <span>
                                    {{notif.post_date}}
                                  </span>
                                </div>
                              </div>
                            </div>
                          
                            <p class='text-danger' v-else>You Do Have Not Sent Or Received Any Messages. Please Go To A Users Profile To Send A Message</p>
                          
                        </div>
                        <div class="text-center">
                          <img src="/images/small-loader.gif" class="small-loader hide" alt="">
                        </div>
                      </div>

                      <div id="notificationFooter"><a href="#" @click="seeAll(this,event)">See All {{$page.props.user_info.conversations_num}}</a></div>
                      </div>

                  </li>

              </li> -->

              <li id="noti_Container2">
                  <li id="notification_li2">
                    <a href="#" id="notificationLink2" @click="notificationLinkClick2" style="color: unset;"><i class="material-icons">notifications</i>
                    <span v-html="$page.props.noti_count"></span>
                   </a>
                      <div id="notificationContainer2" :class="notification_box_class_name">
                      <div id="notificationTitle2">Notifications</div>
                      <div id="notificationsBody2" class="notifications">
                        <div class="container">
                          <div class="" v-if="$page.props.all_notifs.length > 0">
                            <div class="" v-for="notif in $page.props.all_notifs" :key="notif.index">
                              <div class="conversation row"  :style="notif.received == 0 ? 'background-color: #D0D0D0;' : '' " @click="redirectJs(notif.site_url)">
                                <div class="col-sm-2 avatar">
                                  <img :src="notif.sender_logo" :alt="notif.sender_username"/>
                                </div>
                                <div class="col-sm-6 message-sender">
                                  <p class="sender-name">{{notif.sender + " " + notif.new_messages_num_frm_sender}}
                                  </p>
                                  <span class="last-message">{{notif.notif_title_short}}</span>
                                </div>
                                <div class="col-sm-4 time-stamp">
                                  <span>
                                    {{notif.soc_med_time}}
                                  </span>
                                </div>
                              </div>
                            </div>
                          </div>
                          
                          <p class='text-danger' v-else>You Don't Have Any Notifications</p>
                          
                        </div>
                        <div class="text-center">
                          <img src="/images/small-loader.gif" class="small-loader hide" alt="">
                        </div>
                      </div>

                      <div id="notificationFooter"><a href="#" @click="seeAll">See All({{$page.props.notifs_num}})</a></div>
                      </div>

                  </li>

              </li>

              <li class="nav-item" data-toggle="tooltip" title="Logout">
                <a class="nav-link" href="/logout">
                  <i class="fas fa-sign-out-alt" style="font-size: 15px;"></i>
                  <p class="d-lg-none d-md-block">
                    Logout
                  </p>
                </a>
              </li>

              
            </ul>
          </div>
        </div>
      </nav>
      <div class="overlay" style="">
        
      </div>
      <slot />
    
    </div>
  </div>
</template>

<script>


export default {
  components: {
    
  },
  methods: {

    showReferralCode() {
    
      swal({
        title: 'Referral Link',
        text: "Your Referral Link Is <span class='text-primary' style='font-style: italic;'>https://meetglobalresources.com/login_page?id="+this.$page.props.user_info.slug+"</span>",
        type: 'success'
      })
    },
    upgradeToBusiness() {

    },
    redirectJs(url){
      window.location.assign(url)
    },
    seeAll () {
      var self = this;
      self.$inertia.visit(self.route('notifications'));
    },
    notificationLinkClick2(){
      
      var self = this;
      if($(window).width() >= 500){
        
        if(self.notification_box_class_name == "hide"){
          
          if(self.message_box_class_name != "hide"){
            
            self.message_box_class_name = "hide";
          }
          
          self.notification_box_class_name = "";
        }else{
          self.notification_box_class_name = "hide";
        }
        
        $("#notification_count").fadeOut("slow");
      }else{
         self.$inertia.visit(self.route('messages'));
      }
      // return false;
    },

    notificationLinkClick(){
      var self = this;
      if($(window).width() >= 500){
        
        if(self.message_box_class_name == "hide"){
          
          if(self.notification_box_class_name != "hide"){
            
            self.notification_box_class_name = "hide";
          }
          
          self.message_box_class_name = "";
        }else{
          self.message_box_class_name = "hide";
        }
        
        $("#notification_count").fadeOut("slow");
      }else{
         self.$inertia.visit(self.route('messages'));
      }
      // return false;
    },
  },
  data() {
    return {
      form: this.$inertia.form({
        

      }),
      user_avatar: "",
      
      message_box_class_name: "hide",
      notification_box_class_name: "hide",
    }
  },
  
  mounted() {
    var logo = this.$page.props.logo;
    if(logo == null){
      this.user_avatar = '/images/avatar.jpg';
    }else{
      this.user_avatar = '/storage/images/avatar.jpg' + logo;
    }
  },
}
</script>
