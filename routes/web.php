<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [MainController::class,'index'])->name('home_page');
Route::get('/login_page', [MainController::class,'loadLoginPage']);
Route::get('/test', [MainController::class,'loadTestPage']);
Route::get('/test_upload', [MainController::class,'loadTestUploadPage'])->name('test_upload');
Route::post('/process_test_upload', [MainController::class,'processTestUpload'])->name('process_test_upload');
Route::post('/process_sign_in', [MainController::class,'processSignIn']);
Route::post('/process_send_message', [MainController::class,'processSendMessage']);
Route::post('/get_sponsor_info_registration', [MainController::class,'getSponsorInfoRegistration']);
Route::post('/process_user_sign_up', [MainController::class,'processUserSignUp']);
Route::post('/process_user_sign_up_cont', [MainController::class,'processUserSignUpCont']);
Route::get('/registration_step_2/{user_slug}', [MainController::class,'registrationStep2']);
Route::post('/submit_proof_of_payment_to_admin/{user_slug}', [MainController::class,'submiProofOfPaymentToAdmin']);
Route::post('/complete_registration_step_2/{user_slug}', [MainController::class,'completeRegistrationStep2']);
Route::post('/get_placement_mlm_account_registration', [MainController::class,'getPlacementMlmAccountRegistration']);
Route::post('/select_positioning_for_mlm_registration', [MainController::class,'selectPositioningForMlmRegistration']);
Route::post('/check_if_user_name_exists', [MainController::class,'checkIfUsernameExists']);
Route::post('/send_forgot_password_otp', [MainController::class,'sendForgotPasswordOtp']);
Route::post('/verify_user_forgot_password_otp', [MainController::class,'verifyUserFogotPasswordOtp']);
Route::post('/change_password_reset', [MainController::class,'changePasswordReset']);


Route::group(['middleware'=>['protectedPages']],function(){


    Route::get('/admin_page', [MainController::class,'loadAdminPage'])->name('admin_page');
    Route::get('/user/{user_slug}', [MainController::class,'loadUserProfilePage'])->name('user_profile');
    Route::get('/edit_profile/{user_slug}', [MainController::class,'loadEditProfilePage'])->name('edit_profile');
    Route::get('/change_password', [MainController::class,'changePasswordPage'])->name('change_password');
    Route::get('/change_transaction_password', [MainController::class,'changeTransactionPasswordPage'])->name('change_transaction_password');
    Route::get('/make_complaint', [MainController::class,'loadMakeComplaintPage'])->name('make_complaint');
    Route::get('/genealogy_tree', [MainController::class,'loadGenealogyTreePage'])->name('genealogy_tree');
    Route::get('/sponsor_tree', [MainController::class,'loadSponsorTreePage'])->name('sponsor_tree');
    Route::get('/recharge_vtu',[MainController::class,'loadRechargeVTUPage'])->name('recharge_vtu');
    Route::get('/smart_business_loan',[MainController::class,'loadSmartBusinessLoanPage'])->name('smart_business_loan');
    Route::get('/view_smart_business_loan_history',[MainController::class,'loadSmartBusinessLoanHistoryPage'])->name('view_smart_business_loan_history');
    Route::get('/health',[MainController::class,'loadHealthPage'])->name('health');
    Route::get('/ecommerce',[MainController::class,'loadEcommercePage'])->name('ecommerce');
    Route::get('/credit_user_wallet',[MainController::class,'loadCreditUserWalletPage'])->name('credit_user_wallet');
    Route::get('/wallet_credit_history',[MainController::class,'loadWalletCreditHistoryPage'])->name('wallet_credit_history');
    Route::get('/funds_transfer',[MainController::class,'loadFundsTransferPage'])->name('funds_transfer');
    Route::get('/funds_transfer_history',[MainController::class,'loadFundsTransferHistoryPage'])->name('funds_transfer_history');
    Route::get('/funds_withdrawal',[MainController::class,'loadFundsWithdrawalPage'])->name('funds_withdrawal');
    Route::get('/funds_withdrawal_history',[MainController::class,'loadFundsWithdrawalHistoryPage'])->name('funds_withdrawal_history');
    Route::get('/wallet_statement',[MainController::class,'loadWalletStatementPage'])->name('wallet_statement');
    Route::get('/shop_our_products',[MainController::class,'loadShopOurProductsPage'])->name('shop_our_products');
    Route::get('/your_orders',[MainController::class,'loadYourOrdersPage'])->name('your_orders');
    Route::get('/center_leader_home',[MainController::class,'loadCenterLeaderHomePage'])->name('center_leader_home');
    Route::get('/center_connector_home',[MainController::class,'loadCenterConnectorHomePage'])->name('center_connector_home');
    Route::get('/vendor_home',[MainController::class,'loadVendorHomePage'])->name('vendor_home');
    Route::get('/main_chat_page',[MainController::class,'loadMainChatPage'])->name('main_chat_page');
    Route::get('/user_earnings',[MainController::class,'loadUserEarningsPage'])->name('user_earnings');
    Route::get('/messages',[MainController::class,'loadMessagesPage'])->name('messages');

    Route::get('/notifications',[MainController::class,'loadNotificationsPage'])->name('notifications');
    Route::get('/notification/{notif_id}',[MainController::class,'loadNotificationPage'])->name('notification');
    Route::get('/news',[MainController::class,'loadNewsPage'])->name('news');
    Route::get('/feedback',[MainController::class,'loadFeedbackPage'])->name('feedback');
    Route::get('/faq',[MainController::class,'loadFaqPage'])->name('faq');
    Route::get('/cooperative_investment',[MainController::class,'loadCooperativeInvestmentPage'])->name('cooperative_investment');


        
    
    Route::post('/print_recharge_pins',[MainController::class,'loadPrintRechargePinsPage'])->name('print_recharge_pins');
    
    Route::get('/recharge_vtu/airtime',[MainController::class,'loadAirtimePage'])->name('airtime_page');
    Route::get('/recharge_vtu/data',[MainController::class,'loadDataPage'])->name('data_page');
    Route::get('/recharge_vtu/cable_tv',[MainController::class,'loadCableTvPage'])->name('cable_tv_page');
    Route::get('/recharge_vtu/electricity',[MainController::class,'loadElectricityPage'])->name('electricity_page');
    Route::get('/recharge_vtu/airtime_to_wallet',[MainController::class,'loadAirtimeToWalletPage'])->name('airtime_to_wallet_page');
    Route::get('/recharge_vtu/bulk_sms',[MainController::class,'loadBulkSmsPage'])->name('bulk_sms_page');
    Route::get('/recharge_vtu/router',[MainController::class,'loadRouterPage'])->name('router_page');
    Route::get('/recharge_vtu/educational',[MainController::class,'loadEducationalPage'])->name('educational_page');
    
    Route::get('/recharge_vtu/history',[MainController::class,'userVtuHistoryPage'])->name('user_vtu_history_page');

    Route::get('/earning/basic_sponsor',[MainController::class,'viewMlmEarningBasicSponsor'])->name('view_mlm_earnings_details_basic_sponsor');
    Route::get('/earning/business_sponsor',[MainController::class,'viewMlmEarningBusinessSponsor'])->name('view_mlm_earnings_details_sponsor');
    Route::get('/earning/basic_placement',[MainController::class,'viewMlmEarningBasicPlacement'])->name('view_mlm_earnings_details_basic_placement');
    Route::get('/earning/business_placement',[MainController::class,'viewMlmEarningBusinessPlacement'])->name('view_mlm_earnings_details_placement');
    Route::get('/earning/center_leader_sponsor',[MainController::class,'viewMlmEarningCenterLeader'])->name('view_mlm_earnings_details_center_leader_sponsor');
    Route::get('/earning/center_leader_placement',[MainController::class,'viewMlmEarningCenterLeaderPlacement'])->name('view_mlm_earnings_details_center_leader_placement');
    Route::get('/earning/center_connector_sponsor',[MainController::class,'viewMlmEarningCenterConnectorSponsor'])->name('view_mlm_earnings_details_center_connector_sponsor');
    Route::get('/earning/center_connector_placement',[MainController::class,'viewMlmEarningCenterConnectorPlacement'])->name('view_mlm_earnings_details_center_connector_placement');
    Route::get('/earning/vendor_sponsor',[MainController::class,'viewMlmEarningVendorSponsor'])->name('view_mlm_earnings_details_vendor_sponsor');
    Route::get('/earning/vendor_placement',[MainController::class,'viewMlmEarningVendorPlacement'])->name('view_mlm_earnings_details_vendor_placement');
    Route::get('/earning/center_leader_selection',[MainController::class,'viewMlmEarningCenterLeaderSelection'])->name('view_mlm_earnings_details_center_leader_selection');
    Route::get('/earning/center_connector_selection',[MainController::class,'viewMlmEarningCenterConnectorSelection'])->name('view_mlm_earnings_details_center_connector_selection');
    Route::get('/earning/vendor_selection',[MainController::class,'viewMlmEarningVendorSelection'])->name('view_mlm_earnings_details_vendor_selection');
    Route::get('/earning/trade_delivery',[MainController::class,'viewMlmEarningTradeDelivery'])->name('view_mlm_earnings_details_trade_delivery');
    Route::get('/earning/vtu_trade_income',[MainController::class,'viewMlmEarningVtuTradeIncome'])->name('view_mlm_earnings_details_vtu_trade_income');
    Route::get('/earning/sgps_income',[MainController::class,'viewMlmEarningSgpsIncome'])->name('view_mlm_earnings_details_sgps_income');
    Route::get('/custom_receipt',[MainController::class,'viewCustomReceipt'])->name('custom_receipt');
    
    
    //Post Requests
    
    Route::post('/process_edit_profile',[MainController::class,'processEditProfile'])->name('process_edit_profile');
    Route::post('/view_your_genealogy_tree_down',[MainController::class,'viewYourGenealogyTreeDown'])->name('view_your_genealogy_tree_down');
    Route::post('/view_your_genealogy_tree',[MainController::class,'viewYourGenealogyTree'])->name('view_your_genealogy_tree');
    Route::post('/earnings/transfer_to_main_acct',[MainController::class,'transferEarningsToMainAcct'])->name('transfer_to_main_acct');
    Route::post('/recharge_vtu/buy_payscribe_educational_voucher_vtu',[MainController::class,'buyPayscribeEducationalVoucherVtu'])->name('buy_payscribe_educational_voucher_vtu');
    Route::post('/recharge_vtu/buy_eminence_educational_voucher_vtu',[MainController::class,'buyEminenceEducationalVoucherVtu'])->name('buy_eminence_educational_voucher_vtu');
    
    Route::post('/recharge_vtu/check_if_educational_voucher_is_available',[MainController::class,'checkIfEducationalVoucherAvailability'])->name('check_if_educational_voucher_is_available');
    Route::post('/recharge_vtu/validate_educational_voucher_info',[MainController::class,'validateEducationalVoucherInfo'])->name('validate_educational_voucher_info');
    Route::post('/recharge_vtu/recharge_router',[MainController::class,'rechargeRouter'])->name('recharge_router');
    Route::post('/recharge_vtu/load_router_bundles_and_verify_number',[MainController::class,'loadRouterBundlesAndVerifyNumber'])->name('load_router_bundles_and_verify_number');
    Route::post('/recharge_vtu/send_bulk_sms',[MainController::class,'sendBulkSms'])->name('send_bulk_sms');
    Route::post('/recharge_vtu/process_airtime_to_wallet_transfer',[MainController::class,'processAirtimeToWalletTransfer'])->name('process_airtime_to_wallet_transfer');
    Route::post('/recharge_vtu/validate_airtime_to_wallet_details',[MainController::class,'validateAirtimeToWalletDetails'])->name('validate_airtime_to_wallet_details');
    Route::post('/recharge_vtu/get_charge_for_airtime_to_wallet_transfer',[MainController::class,'getChargeForAirtimeToWalletTransfer'])->name('get_charge_for_airtime_to_wallet_transfer');
    Route::post('/recharge_vtu/purchase_electricity_with_gsubz',[MainController::class,'purchaseElectricityWithGsubz'])->name('purchase_electricity_with_gsubz');
    Route::post('/recharge_vtu/purchase_electricity_with_payscribe',[MainController::class,'purchaseElectricityWithPayscribe'])->name('purchase_electricity_with_payscribe');
    Route::post('/recharge_vtu/purchase_electricity_with_buypower',[MainController::class,'purchaseElectricityWithBuypower'])->name('purchase_electricity_with_buypower');
    Route::post('/recharge_vtu/validate_meter_number_disco',[MainController::class,'validateMeterNumberDisco'])->name('validate_meter_number_disco');
    Route::post('/recharge_vtu/check_if_disco_is_available',[MainController::class,'checkIfDiscoIsAvailable'])->name('check_if_disco_is_available');
    Route::post('/recharge_vtu/purchase_cable_tv_plan',[MainController::class,'purchaseCableTvPlan'])->name('purchase_cable_tv_plan');
    Route::post('/recharge_vtu/validate_decoder_number_cable_plans',[MainController::class,'validateDecoderNumberCablePlans'])->name('validate_decoder_number_cable_plans');
    Route::post('/recharge_vtu/purchase_gsubz_data',[MainController::class,'purchaseGsubzData'])->name('purchase_gsubz_data');
    Route::post('/recharge_vtu/purchase_eminence_data',[MainController::class,'purchaseEminenceData'])->name('purchase_eminence_data');
    Route::post('/recharge_vtu/purchase_clubkonnect_data',[MainController::class,'purchaseClubKonnectData'])->name('purchase_clubkonnect_data');
    Route::post('/recharge_vtu/purchase_payscribe_data',[MainController::class,'purchasePayscribeData'])->name('purchase_payscribe_data');
    Route::post('/recharge_vtu/purchase_9mobile_combo_data',[MainController::class,'purchase9mobileComboData'])->name('purchase_9mobile_combo_data');
    Route::post('/recharge_vtu/get_data_plans_by_network',[MainController::class,'getDataPlansByNetwork'])->name('get_data_plans_by_network');
    Route::post('/recharge_vtu/generate_vtu_epin',[MainController::class,'generateEpin'])->name('generate_vtu_epin');
    Route::post('/recharge_vtu/normal_airtime_recharge_request',[MainController::class,'normalAirtimeRechargeRequest'])->name('normal_airtime_recharge_request');
    Route::post('/recharge_vtu/request_9mobile_combo_recharge',[MainController::class,'request9mobileComboRecharge'])->name('request_9mobile_combo_recharge');
    Route::post('/validate_withdrawal_otp', [MainController::class,'processValidateWithdrawalOtp'])->name('validate_withdrawal_otp');
    Route::post('/send_withdrwal_otp', [MainController::class,'processSendWithdrawalOtp'])->name('send_withdrwal_otp');
    Route::post('/enter_amount_withdraw_funds', [MainController::class,'processEnterAmountWithdrawFunds'])->name('enter_amount_withdraw_funds');
    Route::post('/withdraw_funds_cont', [MainController::class,'processWithdrawFundsCont'])->name('withdraw_funds_cont');
    Route::post('/get_forms_for_funds_withdrawal', [MainController::class,'processGetFormsForFundsWithdrawal'])->name('get_forms_for_funds_withdrawal');
    Route::post('/verify_transfer_otp', [MainController::class,'processVerifyTransferOtp'])->name('verify_transfer_otp');
    Route::post('/send_transfer_otp', [MainController::class,'processSendTransferOtp'])->name('send_transfer_otp');
    Route::post('/get_users_email', [MainController::class,'processGetUsersEmail'])->name('get_users_email');
    Route::post('/transfer_funds_to_user', [MainController::class,'processTransferFundsToUser'])->name('transfer_funds_to_user');
    Route::post('/process_change_password', [MainController::class,'processChangePassword'])->name('process_change_password');
    Route::post('/submit_proof_of_payment_to_admin_inside_app', [MainController::class,'submitProofOfPaymentToAdminInsideApp'])->name('submit_proof_of_payment_to_admin_inside_app');
    Route::post('/process_change_transaction_password', [MainController::class,'processChangeTransactionPassword'])->name('process_change_transaction_password');
    Route::post('/get_users_email_set_transaction_password', [MainController::class,'getUsersEmailSetTransactionPassword'])->name('get_users_email_set_transaction_password');
    Route::post('/send_set_transaction_password_otp', [MainController::class,'sendSetTransactionPasswordOtp'])->name('send_set_transaction_password_otp');
    Route::post('/verify_set_transaction_password_otp', [MainController::class,'verifySetTransactionPasswordOtp'])->name('verify_set_transaction_password_otp');
    Route::post('/get_users_email_forgot_transaction_password', [MainController::class,'getUsersEmailForgotTransactionPassword'])->name('get_users_email_forgot_transaction_password');
    Route::post('/send_forgot_transaction_password_otp', [MainController::class,'sendForgotTransactionPasswordOtp'])->name('send_forgot_transaction_password_otp');
    Route::post('/verify_forgot_transaction_password_otp', [MainController::class,'verifyForgotTransactionPasswordOtp'])->name('verify_forgot_transaction_password_otp');
    Route::post('/upgrade_mlm_account_to_business', [MainController::class,'upgradeMlmAccountToBusiness'])->name('upgrade_mlm_account_to_business');
    Route::post('/upgrade_mlm_account_to_business_through_meetglobal_account', [MainController::class,'upgradeMlmAccountToBusinessThroughMeetglobal'])->name('upgrade_mlm_account_to_business_through_meetglobal_account');

    Route::post('/submit_make_complaint_form', [MainController::class,'submitMakeComplaintForm'])->name('submit_make_complaint_form');
    Route::post('/get_user_info_by_id', [MainController::class,'getUserInfoById'])->name('get_user_info_by_id');
    Route::post('/track_club_vtu_order',[MainController::class,'trackClubVtuOrder'])->name('track_club_vtu_order');
    Route::post('/track_payscribe_vtu_order_data',[MainController::class,'trackPayscribeVtuOrderData'])->name('track_payscribe_vtu_order_data');
    Route::post('/track_eminence_vtu_order',[MainController::class,'trackEminenceVtuOrder'])->name('track_eminence_vtu_order');
    Route::post('/track_payscribe_vtu_epin',[MainController::class,'trackPayscribeVtuEpin'])->name('track_payscribe_vtu_epin');
    Route::post('/track_payscribe_educational_epin',[MainController::class,'trackPayscribeEducationalEpin'])->name('track_payscribe_educational_epin');
    Route::post('/check_if_user_valid_to_register_coop_inv',[MainController::class,'checkIfUserValidRegCoopInv'])->name('check_if_user_valid_to_register_coop_inv');
    Route::post('/submit_sponsor_username_coop_regi',[MainController::class,'submitSponsorUsernameCoopRegi'])->name('submit_sponsor_username_coop_regi');
    Route::post('/submit_placement_username_coop_regi',[MainController::class,'submitPlacementUsernameCoopRegi'])->name('submit_placement_username_coop_regi');
    Route::post('/submit_placement_username_coop_regi_step2',[MainController::class,'submitPlacementUsernameCoopRegiStep2'])->name('submit_placement_username_coop_regi_step2');
    Route::post('/select_positioning_for_coop_inv_regi',[MainController::class,'selectPositioningForCoopInvRegi'])->name('select_positioning_for_coop_inv_regi');
    Route::post('/finally_register_user_coop_inv',[MainController::class,'finallyRegisterUserCoopInv'])->name('finally_register_user_coop_inv');
    Route::post('/register_coop_inv_without_placem',[MainController::class,'registerCoopInvWithoutPlacement'])->name('register_coop_inv_without_placem');


    //Strictly Admin Routes
    Route::group(['middleware'=>['protectedAdminPages']],function(){

        Route::get('/create_receipt',[MainController::class,'loadCreateRecieptPage'])->name('create_receipt');
        Route::get('/coop_savings/{user_id}',[MainController::class,'loadUsersCoopSavingsPage'])->name('coop_savings_admin');
        Route::get('/cooperative_members_savings',[MainController::class,'loadCoopInvMembersSavings'])->name('cooperative_members_savings');
        Route::get('/cooperative_members_admin',[MainController::class,'loadCoopInvMembersListPage'])->name('cooperative_members_admin');
        Route::get('/cooperative_investments_admin',[MainController::class,'loadAdminCooperativeInvestementsPage'])->name('cooperative_investments_admin');
        Route::get('/airtime_to_wallet_records',[MainController::class,'loadAirtimeToWalletRecordsPage'])->name('airtime_to_wallet_records');
        Route::get('/run_service_charge_check',[MainController::class,'runMonthlyServiceChargeCheck'])->name('run_service_charge_check');
        Route::get('/front_page_message',[MainController::class,'loadFrontPageMessagePage'])->name('front_page_message');
        Route::get('/sim_activation_initiative',[MainController::class,'loadSimActivationInitiativePage'])->name('sim_activation_initiative');
        Route::get('/manage_epins',[MainController::class,'loadManageEpinsPage'])->name('manage_epins');
        Route::get('/complaints',[MainController::class,'loadComplaintsPage'])->name('complaints');
        Route::get('/complaint/{complaint_id}',[MainController::class,'loadComplaintInfoPage'])->name('complaint_info');
        Route::get('/downline_members',[MainController::class,'loadDownlineMembersPage'])->name('downline_members');
        Route::get('/account_credit_requests',[MainController::class,'loadAccountCreditRequestsPage'])->name('account_credit_requests');
        Route::get('/account_withdrawal_requests',[MainController::class,'loadAccountWithdrawalRequestsPage'])->name('account_withdrawal_requests');
        Route::get('/admin_center_leader',[MainController::class,'loadAdminCenterLeaderPage'])->name('admin_center_leader');
        Route::get('/admin_center_connector',[MainController::class,'loadAdminCenterConnectorPage'])->name('admin_center_connector');
        Route::get('/admin_vendor',[MainController::class,'loadAdminVendorPage'])->name('admin_vendor');
        Route::get('/admin_shop',[MainController::class,'loadAdminShopPage'])->name('admin_shop');
        Route::get('/view_members_list',[MainController::class,'loadMembersListPage'])->name('view_members_list');
         Route::get('/view_admin_vtu_earnings',[MainController::class,'loadAdminVtuEarningsPage'])->name('view_admin_vtu_earnings');
        Route::get('/view_advance_loan_history',[MainController::class,'loadAdvanceLoanHistoryPage'])->name('view_advance_loan_history');
        Route::get('/airtime_combo_requests',[MainController::class,'loadAirtimeComboRequestsPage'])->name('airtime_combo_requests');
        Route::get('/data_combo_requests',[MainController::class,'loadDataComboRequestsPage'])->name('data_combo_requests');
        Route::get('/account_credit_history',[MainController::class,'loadAccountCreditHistoryPage'])->name('account_credit_history');
        Route::get('/admin_account_credit_history',[MainController::class,'loadAdminAccountCreditHistoryPage'])->name('admin_account_credit_history');
        Route::get('/account_debit_history',[MainController::class,'loadAccountDeditHistoryPage'])->name('account_debit_history');
        Route::get('/product_advance_history',[MainController::class,'loadProductAdvanceHistoryPage'])->name('product_advance_history');
        Route::get('/manage_news',[MainController::class,'loadManageNewsPage'])->name('manage_news');
        Route::get('/manage_feedback',[MainController::class,'loadManageFeedbackPage'])->name('manage_feedback');
        Route::get('/manage_faq',[MainController::class,'loadManageFaq'])->name('manage_faq');
        Route::get('/earnings',[MainController::class,'loadAdminEarningsPage'])->name('admin_earnings');
        Route::get('/airtime_combo_recharge_history',[MainController::class,'loadAirtimeComboRechargeHistoryPage'])->name('airtime_combo_recharge_history');
        Route::get('/data_combo_recharge_history',[MainController::class,'loadDataComboRechargeHistoryPage'])->name('data_combo_recharge_history');
        Route::get('/admin_edit_user_profile/{user_id}',[MainController::class,'loadAdminEditUserProfilePage'])->name('admin_edit_user_profile');
        Route::get('/account_credit_history/{user_id}',[MainController::class,'loadUsersAccountCreditHistoryPage'])->name('users_account_credit_history');
        Route::get('/withdrawal_history/{user_id}',[MainController::class,'loadUsersAccountWithdrawalHistoryPage'])->name('users_withdrawal_history');
        Route::get('/vtu_history/{user_id}',[MainController::class,'loadUsersVtuHistoryPage'])->name('users_vtu_history');
        Route::get('/transfer_history/{user_id}',[MainController::class,'loadUsersTransferHistoryPage'])->name('users_transfer_history');
        Route::get('/admin_credit_history/{user_id}',[MainController::class,'loadUsersAdminCreditHistoryPage'])->name('users_admin_credit_history');
        Route::get('/admin_debit_history/{user_id}',[MainController::class,'loadUsersAdminDebitHistoryPage'])->name('users_admin_debit_history');
        Route::get('/product_advance_history/{user_id}',[MainController::class,'loadUsersproductAdvanceHistoryPage'])->name('users_product_advance_history');
        Route::get('/account_statement/{user_id}',[MainController::class,'loadUsersAccountStatementPage'])->name('users_account_statement');
        Route::get('/sponsor_earnings/{user_id}',[MainController::class,'loadUsersSponsorEarningsPage'])->name('users_sponsor_earnings');
        Route::get('/placement_earnings/{user_id}',[MainController::class,'loadUsersPlacementEarningsPage'])->name('users_placement_earnings');
        Route::get('/center_leader_sponsor_bonus/{user_id}',[MainController::class,'loadUsersCenterLeaderSponsorBonusEarningsPage'])->name('users_center_leader_sponsor_bonus');
        Route::get('/center_leader_placement_bonus/{user_id}',[MainController::class,'loadUsersCenterLeaderPlacementBonusEarningsPage'])->name('users_center_leader_placement_bonus');
        Route::get('/center_leader_selection_bonus/{user_id}',[MainController::class,'loadUsersCenterLeaderSelectionBonusEarningsPage'])->name('users_center_leader_selection_bonus');
        Route::get('/trade_bonus/{user_id}',[MainController::class,'loadUsersTradeBonusEarningsPage'])->name('users_trade_bonus');
        Route::get('/vtu_trade_bonus/{user_id}',[MainController::class,'loadUsersVtuTradeBonusEarningsPage'])->name('users_vtu_trade_bonus');
        Route::get('/sgps_bonus/{user_id}',[MainController::class,'loadUsersSgpsBonusEarningsPage'])->name('users_sgps_bonus');
        Route::get('/car_award_bonus/{user_id}',[MainController::class,'loadUsersCarAwardBonusEarningsPage'])->name('users_car_award_bonus');

        
        Route::post('/toggle_coop_savings_admin_withdrawable/{user_id}',[MainController::class,'toggleCoopSavingsAdminWithdrawable'])->name('toggle_coop_savings_admin_withdrawable');
        Route::post('/change_enable_investment',[MainController::class,'changeEnableInvestment'])->name('change_enable_investment');
        
        Route::post('/change_front_page_message',[MainController::class,'changeFrontPageMessage'])->name('change_front_page_message');
        Route::post('/credit_user_sim_incentive_earning',[MainController::class,'creditSimIncentiveEarning'])->name('credit_user_sim_incentive_earning');
        Route::post('/check_username_sim_incentive',[MainController::class,'checkUsernameSimIncentive'])->name('check_username_sim_incentive');
        Route::post('/search_users_genealogy_tree',[MainController::class,'searchUsersGenealogy'])->name('search_users_genealogy_tree');
        Route::post('/dismiss_complaint',[MainController::class,'dismissComplaint'])->name('dismiss_complaint');
        Route::post('/credit_user_after_request',[MainController::class,'creditUserAfterRequest'])->name('credit_user_after_request');
        Route::post('/dismiss_user_credit_request',[MainController::class,'dismissUserCreditRequest'])->name('dismiss_user_credit_request');
        Route::post('/get_withdrawal_request_account_details',[MainController::class,'getWithdrwawalRequestAccountDetails'])->name('get_withdrawal_request_account_details');
        Route::post('/verify_account_credit_withdrawal',[MainController::class,'verifyAccountCreditWithdrawal'])->name('verify_account_credit_withdrawal');
        Route::post('/dismiss_account_credit_withdrawal',[MainController::class,'dismissAccountCreditWithdrawal'])->name('dismiss_account_credit_withdrawal');
        Route::post('/mark_combo_record_as_recharged',[MainController::class,'markComboRecordAsRecharged'])->name('mark_combo_record_as_recharged');
        Route::post('/process_edit_users_profile',[MainController::class,'processEditUsersProfile'])->name('process_edit_users_profile');
        Route::post('/credit_user',[MainController::class,'processCreditUser'])->name('credit_user');
        Route::post('/debit_user',[MainController::class,'processDebitUser'])->name('debit_user');
        

    });
    
    //Stricty cooperative routes
    Route::group(['middleware'=>['cooperativePages']],function(){

        Route::get('/coop_inv/view_savings_history',[MainController::class,'loadViewSavingsHistoryPage'])->name('view_savings_history');
        Route::get('/coop_inv/manage_savings',[MainController::class,'loadManageCooperativeInvestmentSavingsPage'])->name('manage_cooperative_savings');
        Route::get('/coop_inv/manage_investments',[MainController::class,'loadManageCooperativeInvestmentPage'])->name('manage_investments');
        Route::get('/coop_inv/manage_investment_loans',[MainController::class,'loadManageCooperativeInvestmentLoansPage'])->name('manage_investment_loans');
        Route::get('/coop_inv/manage_earnings',[MainController::class,'loadManageCooperativeInvestmentEarningsPage'])->name('manage_cooperative_earnings');
        Route::get('/coop_inv/genealogy_tree',[MainController::class,'loadManageCooperativeInvestmentGenealogyTree'])->name('cooperative_earnings_genealogy_tree');
        Route::post('/coop_inv/view_your_genealogy_tree_down',[MainController::class,'viewYourCoopInvGenealogyTreeDown'])->name('view_your_coop_inv_genealogy_tree_down');
        Route::post('/coop_inv/view_your_genealogy_tree',[MainController::class,'viewYourCoopInvGenealogyTree'])->name('vview_your_coop_inv_genealogy_tree');
        Route::get('/coop_inv/view_investment_history',[MainController::class,'loadViewInvestmentHistoryPage'])->name('view_investment_history');
        Route::post('/coop_inv/withdraw_earnings',[MainController::class,'processWithdrawCooperativeInvestmentEarnings'])->name('process_withdraw_coop_inv_earnings');
        Route::post('/coop_inv/make_coop_investment',[MainController::class,'processMakeCoopInvestment'])->name('process_make_coop_investment');
        Route::post('/coop_inv/make_coop_savings',[MainController::class,'processMakeCoopSavings'])->name('process_make_coop_savings');
        Route::post('/coop_inv/coop_savings_withdrawals',[MainController::class,'processCoopSavingsWithdrawals'])->name('process_coop_savings_withdrawals');
    });

    //Logout
    Route::get('/logout',[MainController::class,'processUserLogout']);



    Route::get('/home', [MainController::class,'testHome'])->name('home');
    Route::post('/page_2', [MainController::class,'secondPage'])->name('page2');
    Route::get('/user/{id}', [MainController::class,'editUserPage'])->name('edit_user');
    Route::get('/create_user', [MainController::class,'createUserPage'])->name('create_user');
    Route::put('/process_create_user', [MainController::class,'processCreateNewUser'])->name('process_create_user');
    Route::put('/update_user/{id}', [MainController::class,'updateUserInfo'])->name('update_user');
    Route::delete('/delete_user/{id}', [MainController::class,'deleteUser'])->name('delete_user');

    
    Route::get('/buy_airtime_vtu',[MainController::class,'loadBuyAirtimeVTUPage'])->name('buy_airtime');
    Route::get('/buy_data_vtu',[MainController::class,'loadBuyDataVTUPage'])->name('buy_data');
    Route::get('/load_data_plans_vtu/{type}',[MainController::class,'loadDataPlansVtu'])->name('data_plans_list');
    Route::get('/data_combo/{type}',[MainController::class,'load9mobileComboDataPlansVtu'])->name('9mobile_combo_data_plans_list');
    Route::post('/process_buy_airtime_vtu',[MainController::class,'processBuyAirtimeVtu'])->name('process_buy_airtime');
    Route::post('/process_buy_data_vtu/{type}',[MainController::class,'processBuyDataVtu'])->name('process_buy_data');
    // Route::post('/generate_vtu_epin',[MainController::class,'generateVtuEpin'])->name('generate_vtu_epin');
    Route::get('/buy_cable_vtu',[MainController::class,'loadBuyCableVTUPage'])->name('buy_cable');
    Route::get('/load_data_plans_vtu/{type}',[MainController::class,'loadDataPlansVtu'])->name('data_plans_list');
    Route::get('/buy_cable_vtu',[MainController::class,'loadBuyCableVTUPage'])->name('buy_cable');
    Route::post('/verify_cable_tv_number/{type}',[MainController::class,'verifyCableTvNumber'])->name('verify_cable_tv_number');
    Route::get('/cable_tv_plans/{type}',[MainController::class,'loadCablePlansVTUPage'])->name('cable_tv_plans');
    Route::post('/process_buy_cable_tv_vtu/{type}',[MainController::class,'processBuyCableVtu'])->name('process_buy_cable');
    Route::get('/buy_electricity_vtu',[MainController::class,'loadBuyElectricityVTUPage'])->name('buy_electricity');
    Route::post('/check_if_disco_available',[MainController::class,'checkIfDiscoIsAvailable'])->name('check_disco_availability');
    Route::post('/verify_electricity_details',[MainController::class,'verifyElectricityDetails'])->name('verify_electricity_details');
    Route::post('/vend_electricity',[MainController::class,'processVendElectricity'])->name('vend_electricity');
});
