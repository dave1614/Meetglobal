const Ziggy = {"url":"http:\/\/laravel.test","port":null,"defaults":{},"routes":{"home_page":{"uri":"\/","methods":["GET","HEAD"]},"test_upload":{"uri":"test_upload","methods":["GET","HEAD"]},"process_test_upload":{"uri":"process_test_upload","methods":["POST"]},"admin_page":{"uri":"admin_page","methods":["GET","HEAD"]},"user_profile":{"uri":"user\/{user_slug}","methods":["GET","HEAD"]},"edit_profile":{"uri":"{user_slug}\/edit-profile","methods":["GET","HEAD"]},"change_password":{"uri":"change_password","methods":["GET","HEAD"]},"change_transaction_password":{"uri":"change_transaction_password","methods":["GET","HEAD"]},"make_complaint":{"uri":"make_complaint","methods":["GET","HEAD"]},"genealogy_tree":{"uri":"genealogy_tree","methods":["GET","HEAD"]},"sponsor_tree":{"uri":"sponsor_tree","methods":["GET","HEAD"]},"recharge_vtu":{"uri":"recharge_vtu","methods":["GET","HEAD"]},"smart_business_loan":{"uri":"smart_business_loan","methods":["GET","HEAD"]},"view_smart_business_loan_history":{"uri":"view_smart_business_loan_history","methods":["GET","HEAD"]},"health":{"uri":"health","methods":["GET","HEAD"]},"ecommerce":{"uri":"ecommerce","methods":["GET","HEAD"]},"credit_user_wallet":{"uri":"credit_user_wallet","methods":["GET","HEAD"]},"wallet_credit_history":{"uri":"wallet_credit_history","methods":["GET","HEAD"]},"funds_transfer":{"uri":"funds_transfer","methods":["GET","HEAD"]},"funds_transfer_history":{"uri":"funds_transfer_history","methods":["GET","HEAD"]},"funds_withdrawal":{"uri":"funds_withdrawal","methods":["GET","HEAD"]},"funds_withdrawal_history":{"uri":"funds_withdrawal_history","methods":["GET","HEAD"]},"wallet_statement":{"uri":"wallet_statement","methods":["GET","HEAD"]},"shop_our_products":{"uri":"shop_our_products","methods":["GET","HEAD"]},"your_orders":{"uri":"your_orders","methods":["GET","HEAD"]},"center_leader_home":{"uri":"center_leader_home","methods":["GET","HEAD"]},"center_connector_home":{"uri":"center_connector_home","methods":["GET","HEAD"]},"vendor_home":{"uri":"vendor_home","methods":["GET","HEAD"]},"main_chat_page":{"uri":"main_chat_page","methods":["GET","HEAD"]},"user_earnings":{"uri":"user_earnings","methods":["GET","HEAD"]},"messages":{"uri":"messages","methods":["GET","HEAD"]},"notifications":{"uri":"notifications","methods":["GET","HEAD"]},"notification":{"uri":"notification\/{notif_id}","methods":["GET","HEAD"]},"news":{"uri":"news","methods":["GET","HEAD"]},"feedback":{"uri":"feedback","methods":["GET","HEAD"]},"faq":{"uri":"faq","methods":["GET","HEAD"]},"print_recharge_pins":{"uri":"print_recharge_pins","methods":["POST"]},"airtime_page":{"uri":"recharge_vtu\/airtime","methods":["GET","HEAD"]},"data_page":{"uri":"recharge_vtu\/data","methods":["GET","HEAD"]},"cable_tv_page":{"uri":"recharge_vtu\/cable_tv","methods":["GET","HEAD"]},"electricity_page":{"uri":"recharge_vtu\/electricity","methods":["GET","HEAD"]},"airtime_to_wallet_page":{"uri":"recharge_vtu\/airtime_to_wallet","methods":["GET","HEAD"]},"bulk_sms_page":{"uri":"recharge_vtu\/bulk_sms","methods":["GET","HEAD"]},"router_page":{"uri":"recharge_vtu\/router","methods":["GET","HEAD"]},"educational_page":{"uri":"recharge_vtu\/educational","methods":["GET","HEAD"]},"user_vtu_history_page":{"uri":"recharge_vtu\/history","methods":["GET","HEAD"]},"view_mlm_earnings_details_basic_sponsor":{"uri":"earning\/basic_sponsor","methods":["GET","HEAD"]},"view_mlm_earnings_details_sponsor":{"uri":"earning\/business_sponsor","methods":["GET","HEAD"]},"view_mlm_earnings_details_basic_placement":{"uri":"earning\/basic_placement","methods":["GET","HEAD"]},"view_mlm_earnings_details_placement":{"uri":"earning\/business_placement","methods":["GET","HEAD"]},"view_mlm_earnings_details_center_leader_sponsor":{"uri":"earning\/center_leader_sponsor","methods":["GET","HEAD"]},"view_mlm_earnings_details_center_leader_placement":{"uri":"earning\/center_leader_placement","methods":["GET","HEAD"]},"view_mlm_earnings_details_center_connector_sponsor":{"uri":"earning\/center_connector_sponsor","methods":["GET","HEAD"]},"view_mlm_earnings_details_center_connector_placement":{"uri":"earning\/center_connector_placement","methods":["GET","HEAD"]},"view_mlm_earnings_details_vendor_sponsor":{"uri":"earning\/vendor_sponsor","methods":["GET","HEAD"]},"view_mlm_earnings_details_vendor_placement":{"uri":"earning\/vendor_placement","methods":["GET","HEAD"]},"view_mlm_earnings_details_center_leader_selection":{"uri":"earning\/center_leader_selection","methods":["GET","HEAD"]},"view_mlm_earnings_details_center_connector_selection":{"uri":"earning\/center_connector_selection","methods":["GET","HEAD"]},"view_mlm_earnings_details_vendor_selection":{"uri":"earning\/vendor_selection","methods":["GET","HEAD"]},"view_mlm_earnings_details_trade_delivery":{"uri":"earning\/trade_delivery","methods":["GET","HEAD"]},"view_mlm_earnings_details_vtu_trade_income":{"uri":"earning\/vtu_trade_income","methods":["GET","HEAD"]},"view_mlm_earnings_details_sgps_income":{"uri":"earning\/sgps_income","methods":["GET","HEAD"]},"view_your_genealogy_tree_down":{"uri":"view_your_genealogy_tree_down","methods":["POST"]},"view_your_genealogy_tree":{"uri":"view_your_genealogy_tree","methods":["POST"]},"transfer_to_main_acct":{"uri":"earnings\/transfer_to_main_acct","methods":["POST"]},"buy_payscribe_educational_voucher_vtu":{"uri":"recharge_vtu\/buy_payscribe_educational_voucher_vtu","methods":["POST"]},"check_if_educational_voucher_is_available":{"uri":"recharge_vtu\/check_if_educational_voucher_is_available","methods":["POST"]},"validate_educational_voucher_info":{"uri":"recharge_vtu\/validate_educational_voucher_info","methods":["POST"]},"recharge_router":{"uri":"recharge_vtu\/recharge_router","methods":["POST"]},"load_router_bundles_and_verify_number":{"uri":"recharge_vtu\/load_router_bundles_and_verify_number","methods":["POST"]},"send_bulk_sms":{"uri":"recharge_vtu\/send_bulk_sms","methods":["POST"]},"process_airtime_to_wallet_transfer":{"uri":"recharge_vtu\/process_airtime_to_wallet_transfer","methods":["POST"]},"validate_airtime_to_wallet_details":{"uri":"recharge_vtu\/validate_airtime_to_wallet_details","methods":["POST"]},"get_charge_for_airtime_to_wallet_transfer":{"uri":"recharge_vtu\/get_charge_for_airtime_to_wallet_transfer","methods":["POST"]},"purchase_electricity_with_payscribe":{"uri":"recharge_vtu\/purchase_electricity_with_payscribe","methods":["POST"]},"purchase_electricity_with_buypower":{"uri":"recharge_vtu\/purchase_electricity_with_buypower","methods":["POST"]},"validate_meter_number_disco":{"uri":"recharge_vtu\/validate_meter_number_disco","methods":["POST"]},"check_if_disco_is_available":{"uri":"recharge_vtu\/check_if_disco_is_available","methods":["POST"]},"purchase_cable_tv_plan":{"uri":"recharge_vtu\/purchase_cable_tv_plan","methods":["POST"]},"validate_decoder_number_cable_plans":{"uri":"recharge_vtu\/validate_decoder_number_cable_plans","methods":["POST"]},"purchase_clubkonnect_data":{"uri":"recharge_vtu\/purchase_clubkonnect_data","methods":["POST"]},"purchase_payscribe_data":{"uri":"recharge_vtu\/purchase_payscribe_data","methods":["POST"]},"purchase_9mobile_combo_data":{"uri":"recharge_vtu\/purchase_9mobile_combo_data","methods":["POST"]},"get_data_plans_by_network":{"uri":"recharge_vtu\/get_data_plans_by_network","methods":["POST"]},"generate_vtu_epin":{"uri":"recharge_vtu\/generate_vtu_epin","methods":["POST"]},"normal_airtime_recharge_request":{"uri":"recharge_vtu\/normal_airtime_recharge_request","methods":["POST"]},"request_9mobile_combo_recharge":{"uri":"recharge_vtu\/request_9mobile_combo_recharge","methods":["POST"]},"validate_withdrawal_otp":{"uri":"validate_withdrawal_otp","methods":["POST"]},"send_withdrwal_otp":{"uri":"send_withdrwal_otp","methods":["POST"]},"enter_amount_withdraw_funds":{"uri":"enter_amount_withdraw_funds","methods":["POST"]},"withdraw_funds_cont":{"uri":"withdraw_funds_cont","methods":["POST"]},"get_forms_for_funds_withdrawal":{"uri":"get_forms_for_funds_withdrawal","methods":["POST"]},"verify_transfer_otp":{"uri":"verify_transfer_otp","methods":["POST"]},"send_transfer_otp":{"uri":"send_transfer_otp","methods":["POST"]},"get_users_email":{"uri":"get_users_email","methods":["POST"]},"transfer_funds_to_user":{"uri":"transfer_funds_to_user","methods":["POST"]},"process_change_password":{"uri":"process_change_password","methods":["POST"]},"submit_proof_of_payment_to_admin_inside_app":{"uri":"submit_proof_of_payment_to_admin_inside_app","methods":["POST"]},"process_change_transaction_password":{"uri":"process_change_transaction_password","methods":["POST"]},"get_users_email_set_transaction_password":{"uri":"get_users_email_set_transaction_password","methods":["POST"]},"send_set_transaction_password_otp":{"uri":"send_set_transaction_password_otp","methods":["POST"]},"verify_set_transaction_password_otp":{"uri":"verify_set_transaction_password_otp","methods":["POST"]},"get_users_email_forgot_transaction_password":{"uri":"get_users_email_forgot_transaction_password","methods":["POST"]},"send_forgot_transaction_password_otp":{"uri":"send_forgot_transaction_password_otp","methods":["POST"]},"verify_forgot_transaction_password_otp":{"uri":"verify_forgot_transaction_password_otp","methods":["POST"]},"upgrade_mlm_account_to_business":{"uri":"upgrade_mlm_account_to_business","methods":["POST"]},"upgrade_mlm_account_to_business_through_meetglobal_account":{"uri":"upgrade_mlm_account_to_business_through_meetglobal_account","methods":["POST"]},"submit_make_complaint_form":{"uri":"submit_make_complaint_form","methods":["POST"]},"get_user_info_by_id":{"uri":"get_user_info_by_id","methods":["POST"]},"track_club_vtu_order":{"uri":"track_club_vtu_order","methods":["POST"]},"track_payscribe_vtu_order_data":{"uri":"track_payscribe_vtu_order_data","methods":["POST"]},"track_payscribe_vtu_epin":{"uri":"track_payscribe_vtu_epin","methods":["POST"]},"track_payscribe_educational_epin":{"uri":"track_payscribe_educational_epin","methods":["POST"]},"airtime_to_wallet_records":{"uri":"airtime_to_wallet_records","methods":["GET","HEAD"]},"run_service_charge_check":{"uri":"run_service_charge_check","methods":["GET","HEAD"]},"front_page_message":{"uri":"front_page_message","methods":["GET","HEAD"]},"sim_activation_initiative":{"uri":"sim_activation_initiative","methods":["GET","HEAD"]},"manage_epins":{"uri":"manage_epins","methods":["GET","HEAD"]},"complaints":{"uri":"complaints","methods":["GET","HEAD"]},"complaint_info":{"uri":"complaint\/{complaint_id}","methods":["GET","HEAD"]},"downline_members":{"uri":"downline_members","methods":["GET","HEAD"]},"account_credit_requests":{"uri":"account_credit_requests","methods":["GET","HEAD"]},"account_withdrawal_requests":{"uri":"account_withdrawal_requests","methods":["GET","HEAD"]},"admin_center_leader":{"uri":"admin_center_leader","methods":["GET","HEAD"]},"admin_center_connector":{"uri":"admin_center_connector","methods":["GET","HEAD"]},"admin_vendor":{"uri":"admin_vendor","methods":["GET","HEAD"]},"admin_shop":{"uri":"admin_shop","methods":["GET","HEAD"]},"view_members_list":{"uri":"view_members_list","methods":["GET","HEAD"]},"view_admin_vtu_earnings":{"uri":"view_admin_vtu_earnings","methods":["GET","HEAD"]},"view_advance_loan_history":{"uri":"view_advance_loan_history","methods":["GET","HEAD"]},"airtime_combo_requests":{"uri":"airtime_combo_requests","methods":["GET","HEAD"]},"data_combo_requests":{"uri":"data_combo_requests","methods":["GET","HEAD"]},"account_credit_history":{"uri":"account_credit_history","methods":["GET","HEAD"]},"admin_account_credit_history":{"uri":"admin_account_credit_history","methods":["GET","HEAD"]},"account_debit_history":{"uri":"account_debit_history","methods":["GET","HEAD"]},"product_advance_history":{"uri":"product_advance_history","methods":["GET","HEAD"]},"manage_news":{"uri":"manage_news","methods":["GET","HEAD"]},"manage_feedback":{"uri":"manage_feedback","methods":["GET","HEAD"]},"manage_faq":{"uri":"manage_faq","methods":["GET","HEAD"]},"admin_earnings":{"uri":"earnings","methods":["GET","HEAD"]},"airtime_combo_recharge_history":{"uri":"airtime_combo_recharge_history","methods":["GET","HEAD"]},"data_combo_recharge_history":{"uri":"data_combo_recharge_history","methods":["GET","HEAD"]},"admin_edit_user_profile":{"uri":"admin_edit_user_profile\/{user_id}","methods":["GET","HEAD"]},"users_account_credit_history":{"uri":"account_credit_history\/{user_id}","methods":["GET","HEAD"]},"users_withdrawal_history":{"uri":"withdrawal_history\/{user_id}","methods":["GET","HEAD"]},"users_vtu_history":{"uri":"vtu_history\/{user_id}","methods":["GET","HEAD"]},"users_transfer_history":{"uri":"transfer_history\/{user_id}","methods":["GET","HEAD"]},"users_admin_credit_history":{"uri":"admin_credit_history\/{user_id}","methods":["GET","HEAD"]},"users_admin_debit_history":{"uri":"admin_debit_history\/{user_id}","methods":["GET","HEAD"]},"users_product_advance_history":{"uri":"product_advance_history\/{user_id}","methods":["GET","HEAD"]},"users_account_statement":{"uri":"account_statement\/{user_id}","methods":["GET","HEAD"]},"users_sponsor_earnings":{"uri":"sponsor_earnings\/{user_id}","methods":["GET","HEAD"]},"users_placement_earnings":{"uri":"placement_earnings\/{user_id}","methods":["GET","HEAD"]},"users_center_leader_sponsor_bonus":{"uri":"center_leader_sponsor_bonus\/{user_id}","methods":["GET","HEAD"]},"users_center_leader_placement_bonus":{"uri":"center_leader_placement_bonus\/{user_id}","methods":["GET","HEAD"]},"users_center_leader_selection_bonus":{"uri":"center_leader_selection_bonus\/{user_id}","methods":["GET","HEAD"]},"users_trade_bonus":{"uri":"trade_bonus\/{user_id}","methods":["GET","HEAD"]},"users_vtu_trade_bonus":{"uri":"vtu_trade_bonus\/{user_id}","methods":["GET","HEAD"]},"users_sgps_bonus":{"uri":"sgps_bonus\/{user_id}","methods":["GET","HEAD"]},"users_car_award_bonus":{"uri":"car_award_bonus\/{user_id}","methods":["GET","HEAD"]},"change_front_page_message":{"uri":"change_front_page_message","methods":["POST"]},"credit_user_sim_incentive_earning":{"uri":"credit_user_sim_incentive_earning","methods":["POST"]},"check_username_sim_incentive":{"uri":"check_username_sim_incentive","methods":["POST"]},"search_users_genealogy_tree":{"uri":"search_users_genealogy_tree","methods":["POST"]},"dismiss_complaint":{"uri":"dismiss_complaint","methods":["POST"]},"credit_user_after_request":{"uri":"credit_user_after_request","methods":["POST"]},"dismiss_user_credit_request":{"uri":"dismiss_user_credit_request","methods":["POST"]},"get_withdrawal_request_account_details":{"uri":"get_withdrawal_request_account_details","methods":["POST"]},"verify_account_credit_withdrawal":{"uri":"verify_account_credit_withdrawal","methods":["POST"]},"dismiss_account_credit_withdrawal":{"uri":"dismiss_account_credit_withdrawal","methods":["POST"]},"mark_combo_record_as_recharged":{"uri":"mark_combo_record_as_recharged","methods":["POST"]},"process_edit_users_profile":{"uri":"process_edit_users_profile","methods":["POST"]},"credit_user":{"uri":"credit_user","methods":["POST"]},"debit_user":{"uri":"debit_user","methods":["POST"]},"home":{"uri":"home","methods":["GET","HEAD"]},"page2":{"uri":"page_2","methods":["POST"]},"edit_user":{"uri":"user\/{id}","methods":["GET","HEAD"]},"create_user":{"uri":"create_user","methods":["GET","HEAD"]},"process_create_user":{"uri":"process_create_user","methods":["PUT"]},"update_user":{"uri":"update_user\/{id}","methods":["PUT"]},"delete_user":{"uri":"delete_user\/{id}","methods":["DELETE"]},"buy_airtime":{"uri":"buy_airtime_vtu","methods":["GET","HEAD"]},"buy_data":{"uri":"buy_data_vtu","methods":["GET","HEAD"]},"data_plans_list":{"uri":"load_data_plans_vtu\/{type}","methods":["GET","HEAD"]},"9mobile_combo_data_plans_list":{"uri":"data_combo\/{type}","methods":["GET","HEAD"]},"process_buy_airtime":{"uri":"process_buy_airtime_vtu","methods":["POST"]},"process_buy_data":{"uri":"process_buy_data_vtu\/{type}","methods":["POST"]},"buy_cable":{"uri":"buy_cable_vtu","methods":["GET","HEAD"]},"verify_cable_tv_number":{"uri":"verify_cable_tv_number\/{type}","methods":["POST"]},"cable_tv_plans":{"uri":"cable_tv_plans\/{type}","methods":["GET","HEAD"]},"process_buy_cable":{"uri":"process_buy_cable_tv_vtu\/{type}","methods":["POST"]},"buy_electricity":{"uri":"buy_electricity_vtu","methods":["GET","HEAD"]},"check_disco_availability":{"uri":"check_if_disco_available","methods":["POST"]},"verify_electricity_details":{"uri":"verify_electricity_details","methods":["POST"]},"vend_electricity":{"uri":"vend_electricity","methods":["POST"]}}};

if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}

export { Ziggy };