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

                        <li class="nav-item">
                            <inertia-link class="nav-link" :href="route('edit_profile', $page.props.user_info.slug)">
                              <span class="sidebar-mini"> EP </span>
                              <span class="sidebar-normal"> Edit Profile </span>
                            </inertia-link>
                        </li>

                        <!-- <li class="nav-item">
                            <a class="nav-link" href="#" @click="showReferralCode">
                              <span class="sidebar-mini"> RL </span>
                              <span class="sidebar-normal"> Referral Link </span>
                            </a>
                        </li> -->

                        
                        <li :class="$page.props.active_page == 'change_password' ? 'nav-item active' : 'nav-item' ">
                            <inertia-link class="nav-link" :href="route('change_password')">
                              <span class="sidebar-mini"> CP </span>
                              <span class="sidebar-normal"> Change Your Password </span>
                            </inertia-link>
                        </li>

                        
                        <li :class="$page.props.active_page == 'change_transaction_password' ? 'nav-item active' : 'nav-item' ">
                            <inertia-link class="nav-link" :href="route('change_transaction_password')">
                              <span class="sidebar-mini"> CTP </span>
                              <span class="sidebar-normal"> Change Transact. Pass. </span>
                            </inertia-link>
                        </li>

                       
                        <!-- <li class="nav-item" v-if="$page.props.upgrade_to_business">
                            
                            <a class="nav-link" href="#" @click="upgradeToBusiness">
                                <span class="sidebar-mini"> BU </span>
                                <p> Business Upgrade  </p>
                            </a>
                        </li> -->
 
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


            
            <li :class="$page.props.active_page == 'make_complaint' ? 'nav-item active' : 'nav-item' ">
                <inertia-link class="nav-link" :href="route('make_complaint')">
                    <i class="fab fa-cuttlefish"></i>
                    <p> Make Complaint </p>
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

                </ul>
              </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#" @click="showReferralCode">
                    <i class="fab fa-r-project"></i>
                    <p> Referral Link </p>
                </a>
            </li>

            
            <li :class="$page.props.active_page == 'airtime' || $page.props.active_page == 'data' || $page.props.active_page == 'cable' || $page.props.active_page == 'electricity' || $page.props.active_page == 'airtime_to_wallet' || $page.props.active_page == 'educational' || $page.props.active_page == 'router' || $page.props.active_page == 'bulk_sms' || $page.props.active_page == 'vtu_history' || $page.props.active_page == 'mini' || $page.props.active_page == 'health' || $page.props.active_page == 'ecommerce' || $page.props.active_page == 'partners' || $page.props.active_page == 'love' || $page.props.active_page == 'smart_business_loan' || $page.props.active_page == 'view_smart_business_loan_history'  ? 'nav-item active' : 'nav-item' ">
              
              
              <a class="nav-link" data-toggle="collapse" href="#services-nav-link" aria-expanded="true">
                <!-- <i class="material-icons">chat</i> -->
                <i class="fas fa-info-circle"></i>
                <p> Services 

                  <b class="caret"></b>
                </p>
              </a>

              <div id="services-nav-link" :class="$page.props.active_page == 'airtime' || $page.props.active_page == 'data' || $page.props.active_page == 'cable' || $page.props.active_page == 'electricity' || $page.props.active_page == 'airtime_to_wallet' || $page.props.active_page == 'educational' || $page.props.active_page == 'router' || $page.props.active_page == 'bulk_sms' || $page.props.active_page == 'vtu_history' || $page.props.active_page == 'mini' || $page.props.active_page == 'health' || $page.props.active_page == 'ecommerce' || $page.props.active_page == 'partners' || $page.props.active_page == 'love' || $page.props.active_page == 'smart_business_loan' || $page.props.active_page == 'view_smart_business_loan_history'  ? 'collapse show' : 'collapse' ">
                <ul class="nav">
                          

                  <!-- <li :class="$page.props.active_page == 'recharge' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('recharge_vtu')">
                        <span class="sidebar-mini"> RV </span>
                        <span class="sidebar-normal"> Recharge VTU</span>
                      </inertia-link>
                  </li> -->

                  <!-- <li :class="$page.props.active_page == 'airtime' || $page.props.active_page == 'data' || $page.props.active_page == 'cable' || $page.props.active_page == 'electricity' || $page.props.active_page == 'airtime_to_wallet' || $page.props.active_page == 'educational' || $page.props.active_page == 'router' || $page.props.active_page == 'bulk_sms' || $page.props.active_page == 'vtu_history'  ? 'nav-item active' : 'nav-item' ">
              
              
                    <a class="nav-link" data-toggle="collapse" href="#recharge-nav-link" aria-expanded="true"> -->
                      <!-- <i class="material-icons">chat</i> -->
                      <!-- <i class="fas fa-info-circle"></i>
                      <i class="fas fa-mobile-alt"></i>
                      <p> Recharge VTU 

                        <b class="caret"></b>
                      </p>
                    </a>

                    <div id="recharge-nav-link" :class="$page.props.active_page == 'airtime' || $page.props.active_page == 'data' || $page.props.active_page == 'cable' || $page.props.active_page == 'electricity' || $page.props.active_page == 'airtime_to_wallet' || $page.props.active_page == 'educational' || $page.props.active_page == 'router' || $page.props.active_page == 'bulk_sms' || $page.props.active_page == 'vtu_history'  ? 'collapse show' : 'collapse' ">
                      <ul class="nav"> -->
                        <li :class="$page.props.active_page == 'airtime' ? 'nav-item active' : 'nav-item' ">
                      
                            <inertia-link class="nav-link" :href="route('airtime_page')">
                              <span class="sidebar-mini"> AT </span>
                              <span class="sidebar-normal"> Airtime Topup</span>
                            </inertia-link>
                        </li>

                        <li :class="$page.props.active_page == 'data' ? 'nav-item active' : 'nav-item' ">
                      
                            <inertia-link class="nav-link" :href="route('data_page')">
                              <span class="sidebar-mini"> ID </span>
                              <span class="sidebar-normal"> Internet Data</span>
                            </inertia-link>
                        </li>

                        <li :class="$page.props.active_page == 'cable_tv' ? 'nav-item active' : 'nav-item' ">
                      
                            <inertia-link class="nav-link" :href="route('cable_tv_page')">
                              <span class="sidebar-mini"> CT </span>
                              <span class="sidebar-normal"> Cable TV</span>
                            </inertia-link>
                        </li>

                        <li :class="$page.props.active_page == 'electricity' ? 'nav-item active' : 'nav-item' ">
                      
                            <inertia-link class="nav-link" :href="route('electricity_page')">
                              <span class="sidebar-mini"> ET </span>
                              <span class="sidebar-normal"> Electricity Topup</span>
                            </inertia-link>
                        </li>

                        <li :class="$page.props.active_page == 'airtime_to_wallet' ? 'nav-item active' : 'nav-item' ">
                      
                            <inertia-link class="nav-link" :href="route('airtime_to_wallet_page')">
                              <span class="sidebar-mini"> AW </span>
                              <span class="sidebar-normal"> Airtime To Wallet</span>
                            </inertia-link>
                        </li>

                        <!-- <li :class="$page.props.active_page == 'bulk_sms' ? 'nav-item active' : 'nav-item' ">
                      
                            <inertia-link class="nav-link" :href="route('bulk_sms_page')">
                              <span class="sidebar-mini"> BS </span>
                              <span class="sidebar-normal"> Bulk SMS</span>
                            </inertia-link>
                        </li> -->

                        <li :class="$page.props.active_page == 'router' ? 'nav-item active' : 'nav-item' ">
                      
                            <inertia-link class="nav-link" :href="route('router_page')">
                              <span class="sidebar-mini"> RR </span>
                              <span class="sidebar-normal"> Router Recharge</span>
                            </inertia-link>
                        </li>

                        <li :class="$page.props.active_page == 'educational' ? 'nav-item active' : 'nav-item' ">
                      
                            <inertia-link class="nav-link" :href="route('educational_page')">
                              <span class="sidebar-mini"> EV </span>
                              <span class="sidebar-normal"> Educational Vouchers</span>
                            </inertia-link>
                        </li>
                      <!-- </ul>
                    </div>
                  </li> -->
                  
                  <!-- <li :class="$page.props.active_page == 'recharge' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('recharge_vtu')">
                        <span class="sidebar-mini"> RV </span>
                        <span class="sidebar-normal"> Recharge VTU</span>
                      </inertia-link>
                  </li> -->


                  
                  <li :class="$page.props.active_page == 'smart_business_loan' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('smart_business_loan')">
                        <span class="sidebar-mini"> SBL </span>
                        <span class="sidebar-normal"> Smart Business Loan</span>
                      </inertia-link>
                  </li>


                  
                  <li :class="$page.props.active_page == 'view_smart_business_loan_history' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('view_smart_business_loan_history')">
                        <span class="sidebar-mini"> BLH </span>
                        <span class="sidebar-normal">  Business Loan History</span>
                      </inertia-link>
                  </li>


                  
                  <li :class="$page.props.active_page == 'health' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('health')">
                        <span class="sidebar-mini"> H </span>
                        <span class="sidebar-normal"> Health</span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'ecommerce' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('ecommerce')">
                        <span class="sidebar-mini"> EC </span>
                        <span class="sidebar-normal"> E-Commerce</span>
                      </inertia-link>
                  </li>


                  
                  <!-- <li :class="$page.props.active_page == 'love' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('user_profile', $page.props.user_info.slug)">
                        <span class="sidebar-mini"> LR</span>
                        <span class="sidebar-normal"> LOVE , SEX &amp; RELATIONSHIP</span>
                      </inertia-link>
                  </li> -->

                </ul>
              </div>
            </li>

            <li :class="$page.props.active_page == 'wallet_statement' || $page.props.active_page == 'funds_transfer' || $page.props.active_page == 'transfer_history' || $page.props.active_page == 'credit_user_wallet' || $page.props.active_page == 'funds_withdrawal' || $page.props.active_page == 'funds_withdrawal_history' || $page.props.active_page == 'wallet_credit_history'   ? 'nav-item active' : 'nav-item' ">
              <a class="nav-link" data-toggle="collapse" href="#e-wallet-nav-link" aria-expanded="true">
                <!-- <i class="material-icons">chat</i> -->
                <i class="fas fa-wallet"></i>
                <p> E-Wallet 

                  <b class="caret"></b>
                </p>
              </a>
             

              <div id="e-wallet-nav-link" :class="$page.props.active_page == 'wallet_statement' || $page.props.active_page == 'funds_transfer' || $page.props.active_page == 'transfer_history' || $page.props.active_page == 'credit_user_wallet' || $page.props.active_page == 'funds_withdrawal' || $page.props.active_page == 'funds_withdrawal_history' || $page.props.active_page == 'wallet_credit_history' ? 'collapse show' : 'collapse' ">
                <ul class="nav">

                  
                  <li :class="$page.props.active_page == 'credit_user_wallet' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('credit_user_wallet')">
                        <span class="sidebar-mini"> CW </span>
                        <span class="sidebar-normal"> Credit Wallet</span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'wallet_credit_history' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('wallet_credit_history')">
                        <span class="sidebar-mini"> WCH </span>
                        <span class="sidebar-normal"> Wallet Credit History </span>
                      </inertia-link>
                  </li>
                          

                  
                  <li :class="$page.props.active_page == 'funds_transfer' ? 'nav-item active' : 'nav-item' ">

                      <inertia-link class="nav-link" :href="route('funds_transfer')">
                        <span class="sidebar-mini"> FT </span>
                        <span class="sidebar-normal"> Funds Transfer</span>
                      </inertia-link>
                  </li>


                  
                  <li :class="$page.props.active_page == 'transfer_history' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('funds_transfer_history')">
                        <span class="sidebar-mini"> TH </span>
                        <span class="sidebar-normal"> Transfer History</span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'funds_withdrawal' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('funds_withdrawal')">
                        <span class="sidebar-mini"> FW </span>
                        <span class="sidebar-normal"> Funds Withdrawal</span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'funds_withdrawal_history' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('funds_withdrawal_history')">
                        <span class="sidebar-mini"> WH </span>
                        <span class="sidebar-normal"> Withdrawal History</span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'wallet_statement' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('wallet_statement')">
                        <span class="sidebar-mini"> ES </span>
                        <span class="sidebar-normal">E-wallet Statement</span>
                      </inertia-link>
                  </li>
                  

                </ul>
              </div>
            </li>

            <li v-if="!$page.props.user_info.coop_registered" :class="$page.props.active_page == 'cooperative_investment' ? 'nav-item active' : 'nav-item' ">
                <inertia-link class="nav-link" :href="route('cooperative_investment')">
                    <i class="far fa-money-bill-alt"></i>
                    <p> Cooperative & Investment </p>
                </inertia-link>
            </li>

            <li v-else :class="$page.props.active_page == 'manage_investments' || $page.props.active_page == 'manage_investment_loans' || $page.props.active_page == 'manage_earnings' ? 'nav-item active' : 'nav-item' ">
              <a class="nav-link" data-toggle="collapse" href="#coop-investment-nav-link" aria-expanded="true">
                <!-- <i class="material-icons">chat</i> -->
                <i class="far fa-money-bill-alt"></i>
                <p> Cooperative & Invest. 

                  <b class="caret"></b>
                </p>
              </a>
             

              <div id="coop-investment-nav-link" :class="$page.props.active_page == 'manage_investments' || $page.props.active_page == 'manage_investment_loans' || $page.props.active_page == 'manage_earnings' ? 'collapse show' : 'collapse' ">
                <ul class="nav">

                  <li :class="$page.props.active_page == 'cooperative_investment' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('cooperative_investment')">
                        <span class="sidebar-mini"> H </span>
                        <span class="sidebar-normal"> Home</span>
                      </inertia-link>
                  </li>

                  <li :class="$page.props.active_page == 'manage_investments' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('manage_investments')">
                        <span class="sidebar-mini"> MI </span>
                        <span class="sidebar-normal"> Manage Investments</span>
                      </inertia-link>
                  </li>

                  <li :class="$page.props.active_page == 'view_investment_history' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('view_investment_history')">
                        <span class="sidebar-mini"> IH </span>
                        <span class="sidebar-normal"> View Investment History </span>
                      </inertia-link>
                  </li>

                  <li :class="$page.props.active_page == 'manage_cooperative_savings' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('manage_cooperative_savings')">
                        <span class="sidebar-mini"> MS </span>
                        <span class="sidebar-normal"> Manage Savings</span>
                      </inertia-link>
                  </li>

                  <li :class="$page.props.active_page == 'view_savings_history' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('view_savings_history')">
                        <span class="sidebar-mini"> SH </span>
                        <span class="sidebar-normal"> View Savings History </span>
                      </inertia-link>
                  </li>

                  
                  
                          

                  
                  <li :class="$page.props.active_page == 'manage_cooperative_earnings' ? 'nav-item active' : 'nav-item' ">

                      <inertia-link class="nav-link" :href="route('manage_cooperative_earnings')">
                        <span class="sidebar-mini"> ME </span>
                        <span class="sidebar-normal"> Manage Earnings</span>
                      </inertia-link>
                  </li>

                  <li :class="$page.props.active_page == 'cooperative_earnings_genealogy_tree' ? 'nav-item active' : 'nav-item' ">

                      <inertia-link class="nav-link" :href="route('cooperative_earnings_genealogy_tree')">
                        <span class="sidebar-mini"> GT </span>
                        <span class="sidebar-normal"> Genealogy Tree</span>
                      </inertia-link>
                  </li>


                  
                  

                </ul>
              </div>
            </li>



            
            <!-- <li :class="$page.props.active_page == 'shop_our_products' || $page.props.active_page == 'center_leader_home' || $page.props.active_page == 'center_connector_home' || $page.props.active_page == 'vendor_home' || $page.props.active_page == 'your_orders' ? 'nav-item active' : 'nav-item' ">
              <a class="nav-link" data-toggle="collapse" href="#mini-importation-link" aria-expanded="true"> -->
                <!-- <i class="material-icons">chat</i> -->
                <!-- <i class="fas fa-truck"></i>
                <p> Mini Importation 

                  <b class="caret"></b>
                </p>
              </a>
              -->

              <!-- <div id="mini-importation-link" :class="$page.props.active_page == 'shop_our_products' || $page.props.active_page == 'center_leader_home' || $page.props.active_page == 'center_connector_home' || $page.props.active_page == 'vendor_home' || $page.props.active_page == 'your_orders' || $page.props.active_page == 'funds_withdrawal_history' || $page.props.active_page == 'wallet_credit_history' ? 'collapse show' : 'collapse' ">
                <ul class="nav">

                  
                  <li :class="$page.props.active_page == 'shop_our_products' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('shop_our_products')">
                        <span class="sidebar-mini"> SP </span>
                        <span class="sidebar-normal">Shop Products </span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'your_orders' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('your_orders')">
                        <span class="sidebar-mini"> YO</span>
                        <span class="sidebar-normal"> Your Orders  </span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'center_leader_home' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('center_leader_home')">
                        <span class="sidebar-mini"> CL </span>
                        <span class="sidebar-normal">Center Leader </span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'center_connector_home' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('center_connector_home')">
                        <span class="sidebar-mini"> CU </span>
                        <span class="sidebar-normal"> Center Connector </span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'vendor_home' ? 'nav-item active' : 'nav-item' ">
                      
                      <inertia-link class="nav-link" :href="route('vendor_home')">
                        <span class="sidebar-mini"> V</span>
                        <span class="sidebar-normal"> Vendor  </span>
                      </inertia-link>
                  </li>

                </ul>
              </div> -->
            <!-- </li> -->

            
            <!-- <li :class="$page.props.active_page == 'social_media' ? 'nav-item active' : 'nav-item' ">
              
              <inertia-link class="nav-link" :href="route('main_chat_page')">
                  <i class="fab fa-whatsapp"></i>
                  <p> Social Media </p>
              </inertia-link>
            </li>
 -->
            
            <li :class="$page.props.active_page == 'user_earnings' ? 'nav-item active' : 'nav-item' ">

              <inertia-link class="nav-link" :href="route('user_earnings')">
                  <i class="fas fa-dollar-sign"></i>
                  <p> Earnings </p>
              </inertia-link>
            </li>

           
            
            <!-- <li class="nav-item">
              
              <inertia-link class="nav-link" :href="route('user_profile', $page.props.user_info.slug) + '?compose=true'">
                <i class="fas fa-edit" style="font-size: 20px;"></i>
                <span class="sidebar-normal">Compose</span>
              </inertia-link>
            </li>
     -->
            
            <!-- <li :class="$page.props.active_page == 'messages' ? 'nav-item active' : 'nav-item' " id="new-messages-nav-item" >
              
              <inertia-link class="nav-link" :href="route('messages')">
                <i class="fab fa-facebook-messenger" style="font-size: 25px;"></i>
                <span class="sidebar-normal">Messages  ({{$page.props.new_message_count}})</span>
              </inertia-link>
            </li> -->
            
            <li :class="$page.props.active_page == 'notifications' ? 'nav-item active' : 'nav-item' " id="new-notifs-nav-item" >
              
              <inertia-link class="nav-link" :href="route('notifications')">
                <i class="material-icons">notifications</i>
                <span class="sidebar-normal" v-html="'Notifications' + $page.props.new_notifs_count"></span>
              </inertia-link>
            </li>

            <li class="nav-item" v-if="$page.props.upgrade_to_business">
                <a class="nav-link" href="#" @click="upgradeToBusiness">
                    <i class="fas fa-underline"></i>
                    <p> Business Upgrade </p>
                </a>
            </li>

            
            
            <!-- <li :class="$page.props.active_page == 'news' || $page.props.active_page == 'feedback' || $page.props.active_page == 'faq' ? 'nav-item active' : 'nav-item' ">
              <a class="nav-link" data-toggle="collapse" href="#tools-nav-link" aria-expanded="true"> -->
                <!-- <i class="material-icons">chat</i> -->
                <!-- <i class="fa fa-wrench"></i>
                <p> Tools 

                  <b class="caret"></b>
                </p>
              </a>
              
              <div id="tools-nav-link" :class="$page.props.active_page == 'news' || $page.props.active_page == 'feedback' || $page.props.active_page == 'faq' ? 'collapse show' : 'collapse' ">
                <ul class="nav">
                          
                  
                  <li :class="$page.props.active_page == 'news' ? 'nav-item active' : 'nav-item' " >
                      <inertia-link class="nav-link" :href="route('news')">
                        <span class="sidebar-mini"> N </span>
                        <span class="sidebar-normal"> News</span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'feedback' ? 'nav-item active' : 'nav-item' " >
                      
                      <inertia-link class="nav-link" :href="route('feedback')">
                        <span class="sidebar-mini"> FB </span>
                        <span class="sidebar-normal"> Feedback</span>
                      </inertia-link>
                  </li>

                  
                  <li :class="$page.props.active_page == 'faq' ? 'nav-item active' : 'nav-item' " >
                      
                      <inertia-link class="nav-link" :href="route('faq')">
                        <span class="sidebar-mini"> F </span>
                        <span class="sidebar-normal"> FAQS</span>
                      </inertia-link>
                  </li>

                </ul>
              </div>
            </li> -->
            
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
                                  <p class="sender-name" v-html="notif.sender + ' ' + notif.new_messages_num_frm_sender">
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
  data() {
    return {
      form: this.$inertia.form({
        

      }),
      upgrade_to_business_request: this.$inertia.form({
        mlm_db_id: this.$page.props.mlm_db_id,

      }),
      user_avatar: "",
      
      message_box_class_name: "hide",
      notification_box_class_name: "hide",
      
    }
  },
  methods: {

    upgradeToBusiness() {

      var self = this;
      

      swal({
        title: 'Note!',
        text: "Are You Sure You Want To Upgrade Your Package For This Account To Business? <p><em class='text-primary'>Note You'll Pay A Sum Of ₦6,500 To Fully Upgrade This Account.</em></p>",
        type: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        cancelButtonText : "No"
      }).then(function(){
        
        self.upgrade_to_business_request.post(self.route('upgrade_mlm_account_to_business'), {
          preserveScroll: true,
          onSuccess: (page) => {
            // console.log(self.$page.props.response_arr)
            var response = JSON.parse(JSON.stringify(self.$page.props.response_arr))
            console.log(response)
            if(response.success){

              setTimeout(function () {
                self.upgrade_to_business_request.post(self.route('upgrade_mlm_account_to_business_through_meetglobal_account'), {
                  preserveScroll: true,
                  onSuccess: (page) => {
                    
                    var response = JSON.parse(JSON.stringify(self.$page.props.response_arr))
                    console.log(response)
                    if(response.success && response.date != "" && response.time != ""){
                      
                      swal({
                       title: 'Success!',
                        text: "Your Account Has Been Successfully Upgraded To Business",
                        type: 'success',
                        allowOutsideClick: false,
                      }).then(function(){
                        
                        document.location.reload()
                        
                      })
                    }else if(response.insuffecient_funds){
                      swal({
                        title: 'Ooops',
                        text: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
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
                })
              }, 10)
              

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
    showReferralCode() {
    
      swal({
        title: 'Referral Link',
        text: "Your Referral Link Is <span class='text-primary' style='font-style: italic;'>https://meetglobalresources.com/login_page?id="+this.$page.props.user_info.slug+"</span>",
        type: 'success'
      })
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
  
  
  mounted() {

    var logo = this.$page.props.logo;
    if(logo == null){
      this.user_avatar = '/images/avatar.jpg';
    }else{
      this.user_avatar = '/storage/images/avatar.jpg' + logo;
    }
  },
  created(){

  },
}
</script>
