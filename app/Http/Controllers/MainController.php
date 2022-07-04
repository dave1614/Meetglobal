<?php

namespace App\Http\Controllers;
session_start();

use DateTime;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\MainModel;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as Support_Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use App\Rules\PhoneNumberRule;
use App\Rules\SponsorUsernameRule;
use App\Rules\UniqueEmailRule;
use App\Rules\RandomBytesRule;
use App\Rules\UsernameSignupUnique;
use App\Rules\UsernameSignUpRegexRule;
use Illuminate\Validation\Rule;
use App\Rules\PhoneNumberEditRule;
use App\Rules\EmailEditRule;
use Route;
use Illuminate\Support\Facades\Artisan;

class MainController extends Controller
{
    //
    public $user;
    public $data = array();
    public $props = array();

    public function __construct(){
        set_time_limit(0);
        $this->user = new User();
        $this->main_model = new MainModel();
        if($this->main_model->confirmLoggedIn()){
            $user_id = $this->main_model->getUserIdWhenLoggedIn();
            $user_info = $this->main_model->getUserInfoByUserId($user_id);

            $user_token = $this->main_model->getUserParamById("token",$user_id);
            // $this->main_model->onRegister($user_id,$user_token);
                
            $this->main_model->checkIfThisUserHasProvidusAccountNumberIfNotGiveHim();
            // $this->meetglobal_model->performAutomaticRemovalOfDispatchRequestsMiniImportationOrders();

            //Check All Basic Accounts Of This User And Change To Business If Its Up to 6500

            // $this->meetglobal_model->performAutomaticUpgradingOfUsersBasicAccountsToBusiness();  
            // $this->meetglobal_model->checkIfMonnifyAccountReferenceIsSetForThisUserAndSetIfNot();
            $this->main_model->checkIfUserHasPendingCoopInvestmentsToBeCredited($user_id);

            $this->main_model->resetMonthsBeforeUsersBusinessPage();

            $this->main_model->checkIfUserHasOverTheRequiredRegistrationAmountInHisTableAndDoTheNeedful($user_id);

            $this->main_model->checkIfAnyUserIsOwingSmartBusinessLoanAndPayServiceCharge();

            
            if(is_object($user_info)){
                
                foreach($user_info as $row){
                    if($this->main_model->checkIfUserIsRegisteredForCooperativeInvestment($user_id)){
                        // $coop_registered = true;
                        
                        
                        $row->coop_registered = true;
                    }else{
                        
                        $row->coop_registered = false;
                    }
                }
                

                $user_info[0] = (array) $user_info[0];
                $this->data = $user_info[0];

                
            }
            // if(isset($_SERVER['HTTP_REFERER'])){
            //     $previous_page = $_SERVER['HTTP_REFERER'];    
            // }else{
            //     $previous_page = "";
            // }

            if(!is_null(URL::previous())){
                $previous_page = URL::previous();    
            }else{
                $previous_page = "";
            }
            $this->data['user_id'] = $user_id;
            $props = [
                'previous_page' => $previous_page,
                'response_arr' => (object)[],
                'user_info' => $this->data,
                'upgrade_to_business' => false,
                
                'new_message_count' => "",
                'new_notifs_count' => '',
                
                'global_search_val' => '',
                'all_messages' => array(),
                'conversations_num' => $this->main_model->getConversationsNum($this->data['user_id']),
                'noti_count' => '',
                'all_notifs' => array(),
                'notifs_num' => $this->main_model->getNotifsNum($user_id)
            ];


            


            $no_of_mlm_accounts = $this->main_model->getNoOfAccountsOwnedByUser($this->data['user_id']);
            $mlm_db_id = $this->main_model->getUsersFirstMlmDbId($this->data['user_id']);
            $props['mlm_db_id'] = $mlm_db_id;
            if($this->main_model->getMlmDbParamById("package",$mlm_db_id) == 1){
                $props['upgrade_to_business'] = true;
            }
            if($this->main_model->getNewMessagesCount($this->data['user_id']) > 0){
                $props['new_message_count'] = "(" . $this->main_model->getNewMessagesCount($this->data['user_id']) . ")" ; 
            }

            $noti_count = $this->main_model->getNotifCount($this->data['user_id']);
            if($noti_count > 99){
                $props['noti_count'] = "99+";
            }else{
                $props['noti_count'] = (String) $noti_count;
            }

            if($this->main_model->getNotifCount($user_id) > 0){
                $props['noti_count'] = '<span class="new-message-num notification">'.$props['noti_count'].'</span>';
            }else{
                $props['noti_count'] = "";
            }

            $props['new_notifs_count'] = $this->main_model->getNewNotifsCount();

            $all_messages = $this->main_model->getConversations($this->data['user_id']);
            $new_all_messages = array();
            $index = 0;
            if(is_array($all_messages)){
                foreach($all_messages as $row){
                    $receiver = $row['receiver'];
                    $sender = $row['sender'];
                    $last_message_id = $row['id'];
                    $received = $row['received'];
                    $date_time = $row['date_time'];
                    $date_time = new DateTime($date_time);
                    $date = $date_time->format('j M Y');
                    $time = $date_time->format('h:i:sa');
                    $post_date = $this->main_model->getSocialMediaTime($date,$time);
                    $message = $row['message'];
                    if($sender == $user_id){
                      $partner = $receiver;
                    }elseif ($receiver == $user_id) {
                      $partner = $sender;
                    }else{
                      $partner = "";
                    }

                    $sender_logo = $this->main_model->getUserLogoById($sender);
                    $sender_username = $this->main_model->getUserNameById($sender);
                    $new_messages_num_frm_sender = $this->main_model->getNumberOfNewMessagesFromSender($user_id,$sender);
                    
                    if($partner !== ""){
                        $index++;
                        $new_all_messages[] = array(
                            'index' => $index,
                            'receiver' => $receiver,
                            'sender' => $sender,
                            'last_message_id' => $last_message_id,
                            'received' => $received,
                            'date_time' => $date_time,
                            'date' => $date,
                            'time' => $time,
                            'post_date' => $post_date,
                            'message' => $message,
                            'partner' => $partner,
                            'sender_logo' => $sender_logo,
                            'sender_username' => $sender_username,
                            'new_messages_num_frm_sender' => $new_messages_num_frm_sender,
                            'short_message' => $this->main_model->custom_echo($message,30)

                        );
                    }
                }
            }
            $props['all_messages'] = $new_all_messages;

            $all_notifs = $this->main_model->getNotifs($user_id);
            $new_notif_arr = array();
            if(is_object($all_notifs)){
                foreach($all_notifs as $row){
                    $index++;
                    $id = $row->id;
                    $sender_id = $row->sender;
                    $sender = $this->main_model->getUserNameById($sender_id);
                    $notif_id = $row->id;
                    $post_id = $row->post_id;
                    $notif_title = $row->title;
                    $received = $row->received;
                    $date_sent = $row->date_sent;
                    $time_sent = $row->time_sent;
                    $received = $row->received;
                    $type = $row->type;
                    $site_url = '';
                    if($type == "follow"){
                      $site_url .= "/".$sender;
                    }else if($type == "post"){
                      $slug = $this->main_model->getPostSlugById($post_id);
                      $site_url .= "/post/".$post_id.'/'.$slug;
                    }else if($type == "comment"){
                      $slug = $this->main_model->getPostSlugById($post_id);
                      $site_url .= "/post/".$post_id.'/'.$slug;
                    }else if($type == "like"){
                      $slug = $this->main_model->getPostSlugById($post_id);
                      $site_url .= "/post/".$post_id.'/'.$slug;
                    }else if($type == "mini_importation"){
                      $slug = $this->main_model->getPostSlugById($post_id);
                      $site_url .= "/mini_importation";
                    }else if($type == "misc"){
                      $slug = $this->main_model->getPostSlugById($post_id);
                      $site_url .= "/notification/".$id;
                    }

                    $site_url = "/mark_notif_as_read?callback_url=".$site_url."&id=".$id;
                    
                    $sender_logo = $this->main_model->getUserLogoById($sender_id);
                    $sender_username = $this->main_model->getUserNameById($sender);
                    $new_messages_num_frm_sender = $this->main_model->getNumberOfNewMessagesFromSender($user_id,$sender);

                    $new_notif_arr[] = array(
                        'id' => $id,
                        'sender_id' => $sender_id,
                        'sender' => $sender,
                        'notif_id' => $notif_id,
                        'post_id' => $post_id,
                        'notif_title' => $notif_title,
                        'received' => $received,
                        'date_sent' => $date_sent,
                        'time_sent' => $time_sent,
                        'received' => $received,
                        'type' => $type,
                        'site_url' => $site_url,
                        'sender_logo' => $sender_logo,
                        'sender_username' => $sender_username,
                        'new_messages_num_frm_sender' => $new_messages_num_frm_sender,
                        'notif_title_short' => $this->main_model->custom_echo($notif_title,80),
                        'soc_med_time' => $this->main_model->getSocialMediaTime($date_sent,$time_sent)
                    );
                }
            }

            $props['all_notifs'] = $new_notif_arr;
            $this->props = $props;
        }
   
    }


    public function runMonthlyServiceChargeCheck(Request $req){
        // $this->main_model->runMonthlyServiceChargeCheck();
        // return View('monthly_service_charge_check');
    }

    public function loadTestPage(Request $request){
       
        // $val = $this->main_model->testSmt();
        // $val = array('status' => 'ORDER_RECEIVED', 'order_id' => '5424425');

        // echo md5(uniqid(rand(), true));
        // return hash("sha512","RDMtTSMjdF9HMTBCQGw=:88400CB30C3DB7F1F97ED3401D53682CA8EF680C678EE027B1B9C866FB994258");
        // return $this->main_model->outputTwoColumns();
        // $this->main_model->changeDateTimeOnTables();
        // Artisan::call('storage:link');
        // $mobilenetwork_code = "02";
        // $amount = "100";
        // $phone_number = "07051942325";
        // $url = "https://www.nellobytesystems.com/APIAirtimeV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&MobileNetwork=". $mobilenetwork_code ."&Amount=" . $amount . "&MobileNumber=" . $phone_number . "";

        // $url = "https://www.nellobytesystems.com/APIEPINV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&MobileNetwork=02&Value=100&Quantity=1";
        // // echo $url;
        // $use_post = true;

        // $response = $this->main_model->vtu_curl($url,$use_post,$post_data=[]);
        //         // $response = json_encode(array('status' => 'ORDER_RECEIVED', 'orderid' => '5424425'));
        // return $response;

        $json = '{"TXN_EPIN":[{"transactionid":"6425025665","transactiondate":"2/1/2022 11:20:31 AM","batchno":"726326","mobilenetwork":"GLO","sno":"580129003028638","pin":"929181631685436","amount":"100"}]}';
        $json = json_decode($json);

        // var_dump($json);
        // return $json->TXN_EPIN[0]->pin;
        $sponsor_user_id = 10;
        $downteam_arr = $this->main_model->getUsersDownTeamCopInv($sponsor_user_id);

        // echo json_encode($downteam_arr);
        $user_id = 177;
        $sponsor_user_id = 10;
        $sponsor_income = 1000;
        $date = date("j M Y");
        $time = date("h:i:sa");
        $coop_id = 2;
        $placement_income = 30;
        $main_placement_income = 2500;
        // $this->main_model->creditUserSponsorIncomeCoopInv($user_id,$sponsor_user_id,$sponsor_income,$date,$time);
        // $this->main_model->creditUserCoopInvPlacementIncome($coop_id,$placement_income,$date,$time);
        // $upteam_arr = $this->main_model->getUsersUpTeamCopInv(24596);
        // echo json_encode($upteam_arr);
        
        // $this->main_model->checkIfTheresAnyPendingUpteamSupportAndCredit($user_id,$date,$time);
        // for($i = 0; $i < count($downteam_arr); $i++){
        //     echo $downteam_arr[$i] . "<br>";
        // }



        // $this->main_model->createFirstCoopWeeklyEarningRecordForUser($user_id,$coop_id,$date,$time);
        // $date = "04 Jun 2022";
        // $this->main_model->creditUsersWeeklyEarnings($user_id,$coop_id,$main_placement_income,$date,$time);

        // echo $this->main_model->checkIfUserHasPendingCoopInvestmentsToBeCredited($user_id);
        
        
        // return $date_time;

        // return $this->main_model->eminenceVtuCurl("", true, $post_data=[]);
        // set_time_limit(0);
        sleep(300);
        echo "Done\n";

    }

    public function loadCreateRecieptPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'create_receipt';
        $props['page_title'] = $this->main_model->custom_echo('CREATE RECEIPT',30);
        // $props['front_page_text'] = $this->main_model->getFrontPageText();
        
        
        return Inertia::render('Admin/CreateReceiptPage',$props);
        
    }

    public function viewCustomReceipt (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if($req->get('details')){
            $details = $req->get('details');
            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            
            $props['page_title'] = "Receipt Page";
            $props['details'] = $details;
            

            return Inertia::render('CustomReceipt',$props);
        }
    }

    public function toggleCoopSavingsAdminWithdrawable(Request $req, $users_user_id){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
        $response_arr = array('success' => false);

        if(isset($post_data->id)){
            $id = $post_data->id;
            
            $admin_withdrawable = $this->main_model->getCoopSavingParamByIdAndUserId("admin_withdrawable",$id,$users_user_id);
            if($admin_withdrawable){
                $admin_withdrawable = 0;
            }else{
                $admin_withdrawable = 1;
            }

            $form_array['admin_withdrawable'] = $admin_withdrawable;

            if($this->main_model->processCoopSavingWithdrawal($form_array,$id,$users_user_id)){
                $response_arr['success'] = true;
            }
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadUsersCoopSavingsPage(Request $req, $users_user_id){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s Coop. Savings",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;

            $props['amount'] = $req->query('amount');
            $props['time_frame'] = $req->query('time_frame');
            $props['status'] = $req->query('status');
            $props['withdrawn_amount'] = $req->query('withdrawn_amount');
            $props['last_withdrawn_date_time'] = $req->query('last_withdrawn_date_time');
            
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');



            if(empty($props['amount'])){
                $props['amount'] = "";
            }

            if(empty($props['time_frame'])){
                $props['time_frame'] = "";
            }

            if(empty($props['status'])){
                $props['status'] = "all";
            }

            if(empty($props['withdrawn_amount'])){
                $props['withdrawn_amount'] = "";
            }

            if(empty($props['last_withdrawn_date_time'])){
                $props['last_withdrawn_date_time'] = "";
            }


            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getSavingsHistoryForUser($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }

            
            $props['all_history'] = $all_history;
            $props['length'] = $length;

            $str = "";
            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $id = $row->id;
                    $user_id = $row->user_id;
                    $time_frame = $row->time_frame;
                    $admin_withdrawable = $row->admin_withdrawable;
                    $time_frame = $row->time_frame;
                    $date_time = $row->date_time;
                    $saving_date = $row->date;
                    $withdrawn = $row->withdrawn;
                    $part_withdrawn = $row->part_withdrawn;
                    $amount = $row->amount;
                    $withdrawn_amount = $row->withdrawn_amount;
                    $last_withdrawn_date_time = $row->last_withdrawn_date_time;
                    $row->withdrawable = 0;

                    $row->pending_amt = $amount - $withdrawn_amount;


                    // $row->full_name = $this->main_model->getUserFullNameByUserId($user_id);
                    // $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);
                    //Check if funds here are withdrawable

                    if($withdrawn == 0 && $part_withdrawn == 0 && $withdrawn_amount == 0.00 && $last_withdrawn_date_time == ""){
                        $row->status = "Unwithdrawn";
                    }else if($withdrawn == 0 && $part_withdrawn == 1 && $withdrawn_amount != 0.00 && $last_withdrawn_date_time != ""){
                        $row->status = "Part Withdrawn";
                    }else if($withdrawn == 1 && $part_withdrawn == 0 && $withdrawn_amount != 0.00 && $last_withdrawn_date_time != ""){
                        $row->status = "Full Withdrawn";
                    }else{
                        $row->status = "";
                    }

                    if($admin_withdrawable == 1){
                        $row->withdrawable = 1;
                        $row->admin_withdrawable_row = true;
                    }else{
                        $row->admin_withdrawable_row = false;
                    }

                    if($time_frame == 0){
                        $row->withdrawable = 1;

                        $row->time_frame = "None";
                        $row->prosp_date = $row->date;
                    }else{
                        
                        $effectiveDate = date('Y-m-d', strtotime("+".$time_frame." months", strtotime($saving_date)));

                        
                        $str .= $effectiveDate . " " . $date . " ";

                        
                        
                        if($date >= $effectiveDate){
                            $str .= "true <br>";
                            $row->withdrawable = 1;
                        }else{
                            $str .= "false <br>";
                        }

                        
                        $effectiveDate1 = date('j M Y', strtotime($effectiveDate));
                        $row->prosp_date = $effectiveDate1;
                        

                    }



            
                    
                    $row->index = $index;                           
                }

            }

           

            return Inertia::render('Admin/CoopInvSavings',$props);
        }
        
    }

    public function processCoopSavingsWithdrawals(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'invalid_amount' => true,'invalid_duration' => true,'not_registered' => true,'invalid_withdrawal' => true);

        if(isset($post_data->amount) && isset($post_data->id)){
            $amount = $post_data->amount;
            $saving_id = $post_data->id;

            
            
            if($this->main_model->checkIfThisCoopSavingsIdIsValidAndBelongsToUser($saving_id,$user_id)){
                $saving_info = $this->main_model->checkUserCoopSavingsInfoById($saving_id);

                if(is_object($saving_info)){
                    foreach($saving_info as $row){
                
                        $id = $row->id;
                        $user_id = $row->user_id;
                        $time_frame = $row->time_frame;
                        $admin_withdrawable = $row->admin_withdrawable;
                        $time_frame = $row->time_frame;
                        $date_time = $row->date_time;
                        $withdrawn = $row->withdrawn;
                        $part_withdrawn = $row->part_withdrawn;
                        $row->amount = $row->amount;
                        $saving_date = $row->date;
                        $withdrawn_amount = $row->withdrawn_amount;
                        $last_withdrawn_date_time = $row->last_withdrawn_date_time;
                        

                        $pending_amt = $row->amount - $withdrawn_amount;
                    }
                    
                    if($this->main_model->checkIfUserIsRegisteredForCooperativeInvestment(null)){
                        $response_arr['not_registered'] = false;

                        $withdrawable = 0;

                        if($admin_withdrawable == 1){
                            $withdrawable = 1;
                        }

                        if($time_frame == 0){
                            $withdrawable = 1;

                            
                        }else{
                            $effectiveDate = date('Y-m-d', strtotime("+".$time_frame." months", strtotime($saving_date)));

                            $date_c = date("Y-m-d");
                            if($date_c >= $effectiveDate){
                                $withdrawable = 1;
                            }

                        }

                        if($withdrawable == 1){
                            $response_arr['invalid_withdrawal'] = false;

                            $validationRules = [
                                'amount' => 'required|numeric|max:'.$pending_amt.'|min:10',
                                
                                
                            ];

                            $messages = [];

                            $validation = Support_Request::validate($validationRules);

                            
                            if($validation){



                                $new_pending_amt = $pending_amt - $amount;
                                $new_withdrawn_amount = $withdrawn_amount + $amount;

                                $withdrawn = 0;
                                $part_withdrawn = 0;
                                if($new_pending_amt == 0){
                                    $withdrawn = 1;
                                }else{
                                    $part_withdrawn = 1;
                                }

                                $form_array = array(
                                    'withdrawn' => $withdrawn,
                                    'part_withdrawn' => $part_withdrawn,
                                    'withdrawn_amount' => $new_withdrawn_amount,
                                    'last_withdrawn_date_time' => $date . " " .$time,
                                );
                                $amount_to_credit_user = $amount;

                                $summary = "Credit Of " . $amount_to_credit_user . " For Cooperative Savings Withdrawal";

                                if($this->main_model->creditUser($user_id,$amount_to_credit_user,$summary)){
                                    if($this->main_model->processCoopSavingWithdrawal($form_array,$saving_id,$user_id)){
                                        $response_arr['success'] = true;
                                    }
                                }
                            }
                        }

                    }

                }
                

            }
            
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadViewSavingsHistoryPage (Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $user_id = $this->data['user_id'];
        $coop_db_id = $this->data['coop_db_id'];
        $user_name = $this->data['user_name'];
        $users_user_id = $user_id;
        $users_user_name = $user_name;
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'view_savings_history';
        $props['page_title'] = $this->main_model->custom_echo("View Savings History",30);
        

        $props['amount'] = $req->query('amount');
        $props['time_frame'] = $req->query('time_frame');
        $props['status'] = $req->query('status');
        $props['withdrawn_amount'] = $req->query('withdrawn_amount');
        $props['last_withdrawn_date_time'] = $req->query('last_withdrawn_date_time');
        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');


        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        if(empty($props['time_frame'])){
            $props['time_frame'] = "";
        }

        if(empty($props['status'])){
            $props['status'] = "all";
        }

        if(empty($props['withdrawn_amount'])){
            $props['withdrawn_amount'] = "";
        }

        if(empty($props['last_withdrawn_date_time'])){
            $props['last_withdrawn_date_time'] = "";
        }

        
        

        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }




        $all_history = $this->main_model->getSavingsHistoryForUser($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }

        $props['all_history'] = $all_history;
        $props['length'] = $length;
        $str = "";
        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $user_id = $row->user_id;
                $time_frame = $row->time_frame;
                $admin_withdrawable = $row->admin_withdrawable;
                $time_frame = $row->time_frame;
                $date_time = $row->date_time;
                $saving_date = $row->date;
                $withdrawn = $row->withdrawn;
                $part_withdrawn = $row->part_withdrawn;
                $amount = $row->amount;
                $withdrawn_amount = $row->withdrawn_amount;
                $last_withdrawn_date_time = $row->last_withdrawn_date_time;
                $row->withdrawable = 0;

                $row->pending_amt = $amount - $withdrawn_amount;


                // $row->full_name = $this->main_model->getUserFullNameByUserId($user_id);
                // $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);
                //Check if funds here are withdrawable

                if($withdrawn == 0 && $part_withdrawn == 0 && $withdrawn_amount == 0.00 && $last_withdrawn_date_time == ""){
                    $row->status = "Unwithdrawn";
                }else if($withdrawn == 0 && $part_withdrawn == 1 && $withdrawn_amount != 0.00 && $last_withdrawn_date_time != ""){
                    $row->status = "Part Withdrawn";
                }else if($withdrawn == 1 && $part_withdrawn == 0 && $withdrawn_amount != 0.00 && $last_withdrawn_date_time != ""){
                    $row->status = "Full Withdrawn";
                }else{
                    $row->status = "";
                }

                if($admin_withdrawable == 1){
                    $row->withdrawable = 1;
                }

                if($time_frame == 0){
                    $row->withdrawable = 1;

                    $row->time_frame = "None";
                    $row->prosp_date = $row->date;
                }else{
                    
                    $effectiveDate = date('Y-m-d', strtotime("+".$time_frame." months", strtotime($saving_date)));

                    // if($id == 19){
                    //     $date = date('Y-m-d',strtotime("13 Jul 2022"));
                    // }
                    
                    $str .= $effectiveDate . " " . $date . " ";

                    
                    
                    if($date >= $effectiveDate){
                        $str .= "true <br>";
                        $row->withdrawable = 1;
                    }else{
                        $str .= "false <br>";
                    }

                    
                    $effectiveDate1 = date('j M Y', strtotime($effectiveDate));
                    $row->prosp_date = $effectiveDate1;
                    

                }



        
                
                $row->index = $index;                           
            }

        }
        // return $str;

        return Inertia::render('ViewSavingsHistory',$props);
    }

    public function processMakeCoopSavings(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'invalid_amount' => true,'invalid_duration' => true,'not_bouyant' => true,'not_registered' => true);

        if(isset($post_data->amount) && isset($post_data->time_frame_num)){
            $amount = $post_data->amount;
            $time_frame = $post_data->time_frame_num;

            
            
            if($time_frame == "N" || $time_frame == "1" || $time_frame == "3" || $time_frame == "6" || $time_frame == "12"){
                $response_arr['invalid_duration'] = false;

                if($time_frame == "N"){
                    $time_frame = 0;
                }

                
                $total_income = $this->data['total_income'];
                $withdrawn = $this->data['withdrawn'];
                $balance = $total_income - $withdrawn;
                if($balance >= $amount){
                    $response_arr['not_bouyant'] = false;
                    if($this->main_model->checkIfUserIsRegisteredForCooperativeInvestment(null)){
                        $response_arr['not_registered'] = false;

                        $form_array = array(
                            'user_id' => $user_id,
                            'amount' => $amount,
                            'time_frame' => $time_frame,
                            'date' => $date,
                            'time' => $time
                        );
                        $amount_to_debit_user = $amount;

                        $summary = "Debit Of " . $amount_to_debit_user . " For Cooperative Savings";

                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                            if($this->main_model->makeSavingForCoopUser($form_array)){
                                $response_arr['success'] = true;
                            }
                        }

                    }

                }
                

            }
            
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadManageCooperativeInvestmentSavingsPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'manage_cooperative_savings';
        $props['csrf'] = csrf_token();
        $props['page_title'] = $this->main_model->custom_echo('Cooperative Savings',25);

        return Inertia::render('ManageCooperativeSavings',$props);
        
    }

    public function purchaseElectricityWithGsubz(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        if(isset($post_data->productCode) && isset($post_data->productToken) && isset($post_data->use_payscribe)){
            $productCode = $post_data->productCode;
            $productToken = $post_data->productToken;
            $use_payscribe = $post_data->use_payscribe;
            if($productCode != "" && $productToken != "" && $use_payscribe){
                $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'order_id' => '','invalid_meterno' => false,'meter_type_not_available' => false,'metertoken' => '','transaction_pending' => false);

                $validationRules = [
                    'disco' => 'required|in:eko,ikeja,abuja,ibadan,enugu,phc,kano,kaduna,jos',
                    'meter_type' => 'required|in:prepaid,postpaid',
                    'meter_number' => 'required|numeric|digits_between:5,15',
                    'amount' => 'required|numeric|min:100|max:50000',
                    'mobile_number' => 'required|numeric|digits_between:5,15',
                    'email' => 'required|email:rfc,dns,strict,spoof,filter',
                    
                ];

                $messages = [];

                $validation = Support_Request::validate($validationRules);

                
                if($validation){
                    $disco = $post_data->disco;
                    $meter_type = $post_data->meter_type;           
                    $meter_number = $post_data->meter_number;
                    $amount = $post_data->amount;
                    $mobile_number = $post_data->mobile_number;
                    $email = $post_data->email;
                    $payscribe_disco = "";
                    $phone_number = $mobile_number;
                    $meter_no = $meter_number;

                    $club_disco = "";
                
                    if($disco == "eko"){
                        $serviceID = "eko-electric";
                        $disco_code = "EKO";
                        $payscribe_disco = "ekedc";
                        $club_disco = "01";
                    }else if($disco == "ikeja"){
                        $disco_code = "IKEJA";
                        $payscribe_disco = "ikedc";
                        $club_disco = "02";
                    }else if($disco == "abuja"){
                        $disco_code = "ABUJA";
                        $payscribe_disco = "aedc";
                        $club_disco = "03";
                    }else if($disco == "ibadan"){
                        $disco_code = "IBADAN";
                        $payscribe_disco = "ibedc";
                        $club_disco = "07";
                    }else if($disco == "enugu"){
                        $disco_code = "ENUGU";
                        $payscribe_disco = "eedc";
                        $club_disco = "09";
                    }else if($disco == "phc"){
                        $disco_code = "PH";
                        $payscribe_disco = "phedc";
                        $club_disco = "05";
                    }else if($disco == "kano"){
                        $disco_code = "KANO";
                        $club_disco = "04";
                    }else if($disco == "kaduna"){
                        $disco_code = "KADUNA";
                        $payscribe_disco = "kedco";
                        $club_disco = "08";
                    }else if($disco == "jos"){
                        $disco_code = "JOS";
                        $club_disco = "06";
                    }

                    

                        
                   
                    if($post_data->sms_check == true){
                        $amount_deb_user = $amount + 5;
                    }else{
                        $amount_deb_user = $amount;
                    }
                    $amount_to_debit_user = $amount;
                    $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);

                    $meter_type = strtolower($meter_type);

                    if($amount_deb_user <= $user_total_amount){

                        $vtu_platform = $this->main_model->getVtuPlatformToUse('','electricity');
                            
                            
                            $url = "https://gsubz.com/api/pay/";
                            $use_post = true;
                            $data = array(
                                'serviceID' => $serviceID,
                                'billersCode' => $meter_no,
                                'variation_code' => $meter_type,
                                'amount' => $amount,

                                'phone' => $mobile_number
                            );
                            $response = $this->main_model->gSubzVtuCurl($url,$use_post,$data);
                            // return $data;
                            // return $response;
                            if($this->main_model->isJson($response)){
                                $response = json_decode($response);
                                if(is_object($response)){
                                    if(isset($response->status)){
                                        if($response->status == "TRANSACTION_SUCCESSFUL" && $response->code == 200){
                                            $summary = "Debit Of " . $amount_to_debit_user . " For Electricity Recharge";

                                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                            
                                                
                                                $order_id = "GS" . $response->content->transactionID;

                                                if(isset($response->message->details->Token)){
                                                    if(!is_null($response->message->details->Token)){
                                                        $metertoken = $response->message->details->Token;
                                                        $this->main_model->sendMeterTokenForPrepaidToUserByNotif($user_id,$email,$date,$time,$order_id,$disco,$meter_no,$amount,$metertoken);
                                                    }
                                                }

                                                $form_array = array(
                                                    'user_id' => $user_id,
                                                    'type' => 'electricity',
                                                    'sub_type' => $disco,
                                                    'date' => $date,
                                                    'time' => $time,
                                                    'amount' => $amount,
                                                    'number' => $meter_no,
                                                    'order_id' => $order_id
                                                );
                                                if($this->main_model->addTransactionStatusPayscribeElectricity($form_array,true)){
                                                    $response_arr['success'] = true;
                                                    $response_arr['order_id'] = $order_id;
                                                    $response_arr['metertoken'] = $metertoken;


                                                    if($post_data->sms_check == true){
                                                        $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                                                        $amount_to_debit_user = 5;
                                                        // echo $user_total_amount;
                                                        // echo $amount;

                                                        if($amount_to_debit_user <= $user_total_amount){
                                                            

                                                            if($meter_type == "prepaid"){
                                                                $to = $phone_number;
                                                                $message = "Your Meter Token For Meter Number " . $meter_no . " Is ". $metertoken;
                                                                $url = "https://www.payscribe.ng/api/v1/sms";

                                                                $use_post = true;
                                                                $post_data = [
                                                                    'to' => $to,
                                                                    'message' => $message
                                                                ];

                                                                
                                                                // var_dump($post_data);

                                                                $response = $this->main_model->payscribeVtuCurl($url,$use_post,$post_data);


                                                                if($this->main_model->isJson($response)){

                                                                    $response = json_decode($response);
                                                                    // var_dump($response);

                                                                    if($response->status && $response->status_code == 200){
                                                
                                                                        $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                                            $order_id = $response->message->details->transaction_id;
                                                                            $form_array = array(
                                                                                'user_id' => $user_id,
                                                                                'type' => 'bulk_sms',
                                                                                'sub_type' => "",
                                                                                'number' => $message,
                                                                                'date' => $date,
                                                                                'time' => $time,
                                                                                'amount' => $amount_to_debit_user,
                                                                                'order_id' => $order_id
                                                                            );
                                                                            if($this->main_model->addTransactionStatusOnly($form_array)){
                                                                                $response_arr['success'] = true;
                                                                                $response_arr['order_id'] = $order_id;
                                                                            }
                                                                        }
                                                                    }else if($response->status && $response->status_code == 201){
                                                                        

                                                                        $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                                            $order_id = $response->message->details->transaction_id;
                                                                            $form_array = array(
                                                                                'user_id' => $user_id,
                                                                                'type' => 'bulk_sms',
                                                                                'sub_type' => "",
                                                                                'number' => $message,
                                                                                'date' => $date,
                                                                                'time' => $time,
                                                                                'amount' => $amount_to_debit_user,
                                                                                'order_id' => $order_id
                                                                            );
                                                                            if($this->main_model->addTransactionStatusOnly($form_array)){
                                                                                $response_arr['success'] = true;
                                                                                $response_arr['order_id'] = $order_id;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }

                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }  
                         
                        
                    }else{
                        $response_arr['insuffecient_funds'] = true;
                    }
                    
                                
                }
            }
            

            $response_arr = json_encode($response_arr);
        }
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function purchaseEminenceData(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'order_id' => '','transaction_pending' => false);

        $validationRules = [
            'network' => 'required|in:mtn,airtel,glo,9mobile',
            'phone_number' => 'required|numeric|digits_between:6,15'
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            // return $post_data->selected_plan['product_id'];
            $network = $post_data->network;
            if(isset($post_data->selected_plan['product_id'])){

                $plan = $post_data->selected_plan['product_id'];
                // $product_code = $post_data->selected_plan['product_code'];
                $product_id = $plan;
                
                
                $phone_number = $post_data->phone_number;
                $url = "https://app.eminencesub.com/api/buy-data";
                $url_2 = "https://app.eminencesub.com/api/data";
              
                if($network == "mtn"){
                    
                    $network_id = 1;
                    $perc_disc = 0;
                    $additional_charge = 0;
                }else if($network == "glo"){
                    
                    $network_id = 3;
                    $perc_disc = 0.04;
                    $additional_charge = 25;
                }else if($network == "airtel"){
                    
                    $network_id = 2;
                    $perc_disc = 0.04;
                    $additional_charge = 30;
                }else if($network == "9mobile"){
                    
                    $network_id = 4;
                    $perc_disc = 0.04;
                    $additional_charge = 25;
                }

                if($network == "mtn"){
                    if($product_id == 6){
                        $perc_disc = 0.04;
                        $additional_charge = 30;
                    }else if($product_id == 7){
                        $perc_disc = 0.04;
                        $additional_charge = 28;
                    }else if($product_id == 8){
                        $perc_disc = 0.04;
                        $additional_charge = 38;
                    }else if($product_id == 9){
                        $perc_disc = 0.04;
                        $additional_charge = 50;
                    }else if($product_id == 10){
                        $perc_disc = 0.04;
                        $additional_charge = 72;
                    }else if($product_id == 11){
                        $perc_disc = 0.04;
                        $additional_charge = 120;
                    }else if($product_id == 12 || $product_id == 13 || $product_id == 14 || $product_id == 15 || $product_id == 16 ){
                        $perc_disc = 0.04;
                        $additional_charge = 100;
                    }
                }

                
                $amount = $this->main_model->getEminenceVtuDataBundleCostByProductId($url_2,$network,$plan);
                // return $amount;
                // echo $product_id;
                $amount_to_debit_user = 0;
                if($amount != 0){
                    $amount_to_debit_user = round(($perc_disc * $amount) + $amount,2);
                    $amount_to_debit_user += $additional_charge;
                }
                // return $serviceID;
                // return $amount_to_debit_user;
                
                if($amount_to_debit_user != 0){
                    
                
                    $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                    
                    if($amount_to_debit_user <= $user_total_amount){
                        
                        
                        $use_post = true;
                        $data = [
                            'plan' => $plan,
                            'network' => $network_id,
                            'phone' => $phone_number,
                            
                        ];

                        
                        $response = $this->main_model->eminenceVtuCurl($url,$use_post,$data);
                        
                        // return $data;
                        // return $response;
                        if($this->main_model->isJson($response)){
                            $response = json_decode($response);
                            // var_dump($response);
                            if(isset($response->message)){
                                if($response->code == 201){
                                    
                                    $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                        $order_id = "TT" . $response->data->reference;
                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'data',
                                            'sub_type' => $network,
                                            'number' => $phone_number,
                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount_to_debit_user,
                                            'order_id' => $order_id
                                        );
                                        if($this->main_model->addTransactionStatus($form_array)){
                                            $response_arr['success'] = true;
                                            $response_arr['order_id'] = $order_id;
                                        }
                                    }
                                }
                            }
                        }
                        
                    
                    }else{
                        $response_arr['insuffecient_funds'] = true;
                    }
                }
            
                
            }
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function purchaseGsubzData(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'order_id' => '','transaction_pending' => false);

        $validationRules = [
            'network' => 'required|in:mtn,airtel,glo,9mobile',
            'phone_number' => 'required|numeric|digits_between:6,15'
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            // return $post_data->selected_plan['product_id'];
            $network = $post_data->network;
            if(isset($post_data->selected_plan['product_id'])){

                $plan = $post_data->selected_plan['product_id'];
                $product_code = $post_data->selected_plan['product_code'];
                $api = "ap_5ee8c242779b0c0db54241ea15a68e98";
                $requestID = md5(mt_rand().time());
                $phone_number = $post_data->phone_number;
                $url_2 = "https://gsubz.com/api/plans/?service=";
                
                $vtu_platform = $this->main_model->getVtuPlatformToUse('data',$network);
                $vtu_platform_shrt = substr($vtu_platform, 0, 5);
                $serviceID = substr($vtu_platform, 6);

                if($network == "mtn"){
                    if (isset($post_data->selected_plan['gsubz_type'])) {
                        // code...
                    
                        // $gsubz_type = $post_data->selected_plan['gsubz_type'];
                        // // $network = "GLO";
                        // // $serviceID = "mtn_sme";
                        // if($gsubz_type == "regular"){
                        //     $serviceID = "mtncg";    
                        // }else{
                        //     $serviceID = "mtn_sme";    
                        // }
                        
                        $net = "Mtn";
                        $perc_disc = 0.04;
                        $additional_charge = 18;
                        if($plan == "179"){
                            $additional_charge = 26;
                        }
                    }
                    
                }else if($network == "glo"){
                    // $network = "GLO";
                    // $serviceID = "glo_data";
                    $net = "Glo";
                    $perc_disc = 0.04;
                    $additional_charge = 15;
                }else if($network == "airtel"){
                    // $network = "AIRTEL";
                    // $serviceID = "airtelcg";
                    // $serviceID = "airtel_cg";
                    $net = "Airtel";
                    $perc_disc = 0.04;
                    $additional_charge = 25;
                }else if($network == "9mobile"){
                    // $network = "9MOBILE";
                    // $serviceID = "etisalat_data";
                    $net = "9mobile";
                    $perc_disc = 0.04;
                    $additional_charge = 15;
                }

                $url_2 .= $serviceID;

                // return $url_2;
                
                $amount = $this->main_model->getGsubzVtuDataBundleCostByProductId($url_2,$network,$plan);
                // return $amount;
                // echo $product_id;
                $amount_to_debit_user = 0;
                if($amount != 0){
                    $amount_to_debit_user = round((0.04 * $amount) + $amount,2);
                    $amount_to_debit_user += $additional_charge;
                }
                // return $serviceID;
                // return $amount_to_debit_user;
                
                if($amount_to_debit_user != 0){
                    
                
                    $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                    
                    if($amount_to_debit_user <= $user_total_amount){
                        
                        $url = "https://gsubz.com/api/pay/";
                        $use_post = true;
                        $data = [
                            'serviceID' => $serviceID,
                            'plan' => $plan,
                            'amount' => $product_code,
                            'api' => $api,
                            'phone' => $phone_number,
                            'requestID' => $requestID,
                        ];

                        
                        $response = $this->main_model->gSubzVtuCurl($url,$use_post,$data);
                        
                        // return $data;
                        // return $response;
                        if($this->main_model->isJson($response)){
                            $response = json_decode($response);
                            // var_dump($response);
                            if(isset($response->status)){
                                // if($response->status == "TRANSACTION_SUCCESSFUL" && $response->code == 200){
                                if($response->code == 200){
                                    
                                    $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                        $order_id = "GS" . $response->content->transactionID;
                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'data',
                                            'sub_type' => $network,
                                            'number' => $phone_number,
                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount_to_debit_user,
                                            'order_id' => $order_id
                                        );
                                        if($this->main_model->addTransactionStatus($form_array)){
                                            $response_arr['success'] = true;
                                            $response_arr['order_id'] = $order_id;
                                        }
                                    }
                                }
                            }
                        }
                        
                    
                    }else{
                        $response_arr['insuffecient_funds'] = true;
                    }
                }
            
                
            }
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function getDataPlansByNetwork(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'data_plans' => '');

        if(isset($post_data->network)){
            $combo = false;
            $network = $post_data->network;
            if(isset($post_data->combo)){
                if($post_data->combo && $network == "9mobile"){
                    $combo = true;
                }
            }
            $response_arr['success'] = true;
            $response_arr['data_plans'] = $this->main_model->loadDataPlansForNetwork($network,$combo);
            // return $response_arr['data_plans'];
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadCoopInvMembersListPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'members_list';
        $props['page_title'] = $this->main_model->custom_echo('Coop. Members List',25);

        $props['full_name'] = $req->query('full_name');
        $props['user_name'] = $req->query('user_name');
        // $props['phone'] = $req->query('phone');
        // $props['email'] = $req->query('email');
        // $props['created_date'] = $req->query('created_date');
        
        // $props['start_date'] = $req->query('start_date');
        // $props['end_date'] = $req->query('end_date');
        


        if(empty($props['full_name'])){
            $props['full_name'] = "";
        }

        if(empty($props['user_name'])){
            $props['user_name'] = "";
        }

        // if(empty($props['phone'])){
        //     $props['phone'] = "";
        // }

        // if(empty($props['email'])){
        //     $props['email'] = "";
        // }

        // if(empty($props['created_date'])){
        //     $props['created_date'] = "";
        // }
        

        // if(empty($props['start_date'])){
        //     $props['start_date'] = "";
        // }

        // if(empty($props['end_date'])){
        //     $props['end_date'] = "";
        // }

        
        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_users = $this->main_model->getUsersCoopInvPaginationByOffset($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_users)){
            $j = 0;
            foreach($all_users as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $coop_db_id = $row->coop_db_id;
                $user_name = $row->user_name;
                $row->phone = $this->main_model->getFullMobileNoByUserName($user_name);
                $date_created = $this->main_model->getCoopInvMlmDbParamById("date_created",$coop_db_id);
                $time_created = $this->main_model->getCoopInvMlmDbParamById("time_created",$coop_db_id);
                $row->date_of_registration = $date_created . " " . $time_created;
                $coop_db_sponsor_id = $row->coop_db_sponsor_id;
                $row->coop_db_sponsor_username = $this->main_model->getUserNameById($coop_db_sponsor_id);
                $row->coop_db_sponsor_slug = $this->main_model->getUserParamById("slug",$coop_db_sponsor_id);
                $row->positioning = $this->main_model->getCoopInvMlmDbParamById("positioning",$coop_db_id);
                $coop_db_placement_coop_db_id = $this->main_model->getCoopInvMlmDbParamById("under",$coop_db_id);
                $coop_db_placement_coop_user_id = $this->main_model->getCoopInvMlmDbParamById("user_id",$coop_db_placement_coop_db_id);
                $row->coop_db_placement_coop_user_name = $this->main_model->getUserNameById($coop_db_placement_coop_user_id);
                $row->coop_db_placement_coop_slug = $this->main_model->getUserParamById("slug",$coop_db_placement_coop_user_id);
                $total_income = $row->total_income;
                $withdrawn = $row->withdrawn;
                $address = $row->address;
                $row->wallet_balance = $total_income - $withdrawn;
                $row->wallet_balance_str = number_format($row->wallet_balance,2);
                $row->address = $this->main_model->custom_echo($address,20);
        
                
                $row->index = $index;                           
            }
        }

        // $all_requests['links'] = $all_requests->links('pagination::bootstrap-4');
        // echo $all_requests->links();
        $props['all_users'] = $all_users;
        $props['length'] = $length;

        return Inertia::render('Admin/CoopMembersList',$props);
        
    }

    public function viewYourCoopInvGenealogyTree(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
        
        if(isset($post_data->show_records) && isset($post_data->mlm_db_id) && isset($post_data->package)){
            $mlm_db_id = $post_data->mlm_db_id;
            $package1 = $post_data->package;
            $response_arr = array('success' => false,'messages' => '');
                        
            // if($this->main_model->checkIfMlmDbIdBelongsToUser($mlm_db_id,$user_id)){
                $response_arr['success'] = true;

                $response_arr['messages'] .= '<div class="tf-tree example">';

                $level = 20;
                $parentID = $mlm_db_id;
                $stage = 3;

                // $left_num = $this->meetglobal_model->getTotalNoOfMlmAccountsUnderUserLeft($mlm_db_id);

                $user_id = $this->main_model->getCoopInvMlmDbParamById("user_id",$parentID);
                $logo = $this->main_model->getUserParamById("logo",$user_id);
                $user_name = $this->main_model->getUserParamById("user_name",$user_id);
                $full_name = $this->main_model->getUserParamById("full_name",$user_id);
                $package = 1;
                $date_created = $this->main_model->getCoopInvMlmDbParamById("date_created",$parentID);
                $index = $this->main_model->getCoopIdsIndexNumber($mlm_db_id);
                $full_phone_number = $this->main_model->getFullMobileNoByUserName($user_name);
                if(is_null($logo)){
                    $logo = '/images/nophoto.jpg';
                }else{
                    $logo = '/storage/images/'. $logo;
                }

                if($package == 1){
                    $package = "basic";
                }else{
                    $package = "business";
                }
                
                $response_arr['messages'] .= '<ul>';
                $response_arr['messages'] .= '<li>';
                $response_arr['messages'] .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';



                $response_arr['messages'] .= '<img class="tree_icon" src="'.$logo.'">';
                $response_arr['messages'] .= '<p class="demo_name_style">&nbsp;';
                $response_arr['messages'] .= $user_name . "  ";

                
                  
                $response_arr['messages'] .= '<i onclick="goCoopInvDownMlm(this,event,'.$mlm_db_id.','.$mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-down" style="cursor:pointer;"></i>';
                

                $response_arr['messages'] .= '</p>';

                $response_arr['messages'] .= '</div>';


                                    
                
                $response_arr['messages'] .= $this->main_model->printCoopInvTree($package1,$mlm_db_id,$level, $parentID,$stage);
                $response_arr['messages'] .= '</li>';
                $response_arr['messages'] .= '</ul>';

                $response_arr['messages'] .= '</div>';
            // }
            $response_arr = json_encode($response_arr);
            return $response_arr;
        }
        
      
    } 

    public function viewYourCoopInvGenealogyTreeDown(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        
        
        if(isset($post_data->mlm_db_id) && isset($post_data->your_mlm_db_id) && isset($post_data->package)){

            $mlm_db_id = $post_data->mlm_db_id;
            $package1 = $post_data->package;
            $your_mlm_db_id = $post_data->your_mlm_db_id;
            if($this->main_model->checkIfCoopDbIdBelongsToUser($your_mlm_db_id,$user_id)){
                $response_arr = array('success' => false,'messages' => '');
                
                
                $response_arr['success'] = true;

                $response_arr['messages'] .= '<div class="tf-tree example">';

                $level = 20;
                $parentID = $mlm_db_id;
                $stage = 3;
                $user_id = $this->main_model->getCoopInvMlmDbParamById("user_id",$parentID);
                $logo = $this->main_model->getUserParamById("logo",$user_id);
                $user_name = $this->main_model->getUserParamById("user_name",$user_id);
                $full_name = $this->main_model->getUserParamById("full_name",$user_id);
                $package = 1;
                $index = $this->main_model->getCoopIdsIndexNumber($mlm_db_id);
                $date_created = $this->main_model->getCoopInvMlmDbParamById("date_created",$parentID);
                $full_phone_number = $this->main_model->getFullMobileNoByUserName($user_name);
                if(is_null($logo)){
                    $logo = '/images/nophoto.jpg';
                }else{
                    $logo = '/storage/images/'. $logo;
                }

                if($package == 1){
                    $package = "basic";
                }else{
                    $package = "business";
                }
                
                $response_arr['messages'] .= '<ul>';
                $response_arr['messages'] .= '<li>';
                $response_arr['messages'] .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';



                $response_arr['messages'] .= '<img class="tree_icon" src="'.$logo.'">';
                $response_arr['messages'] .= '<p class="demo_name_style">&nbsp;';

                $response_arr['messages'] .= '<i onclick="goCoopInvUpMlm(this,event,'.$mlm_db_id.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-up" style="cursor:pointer;"></i>';

                $response_arr['messages'] .= " " . $user_name . "  ";


  
                $response_arr['messages'] .= '<i onclick="goCoopInvDownMlm(this,event,'.$mlm_db_id.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-down" style="cursor:pointer;"></i>';
                

                $response_arr['messages'] .= '</p>';


                $response_arr['messages'] .= '</div>';


                                    
                
                $response_arr['messages'] .= $this->main_model->printCoopInvTree($package1,$your_mlm_db_id,$level, $parentID,$stage);
                $response_arr['messages'] .= '</li>';
                $response_arr['messages'] .= '</ul>';

                $response_arr['messages'] .= '</div>';
                
                $response_arr = json_encode($response_arr);
                return $response_arr;
            }
        }
       
    } 

    public function loadManageCooperativeInvestmentGenealogyTree (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'cooperative_earnings_genealogy_tree';
        $props['page_title'] = $this->main_model->custom_echo("Coop. Inv. Genealogy Tree",30);

        
        $coop_db_id = $this->data['coop_db_id'];
        // $coop_db_id = 1;
        // return $coop_db_id;
        
        $downline_html = "";
              
        
                

        $downline_html .= '<div class="tf-tree example">';

        $level = 20;
        $parentID = $coop_db_id;
        $stage = 3;

        $user_id = $this->main_model->getCoopInvMlmDbParamById("user_id",$parentID);
        $logo = $this->main_model->getUserParamById("logo",$user_id);
        $user_name = $this->main_model->getUserParamById("user_name",$user_id);
        $full_name = $this->main_model->getUserParamById("full_name",$user_id);
        
        $date_created = $this->main_model->getCoopInvMlmDbParamById("date_created",$parentID);
        $index = $this->main_model->getCoopIdsIndexNumber($coop_db_id);
        $full_phone_number = $this->main_model->getFullMobileNoByUserName($user_name);
        if(is_null($logo)){
            $logo = '/images/nophoto.jpg';
        }else{
            $logo = '/storage/images/'. $logo;
        }

        
        $package = "basic";
        $package1 = "basic";
        
            
        $downline_html .= '<ul>';
        $downline_html .= '<li>';
        $downline_html .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';


        $downline_html .= '<img class="tree_icon" src="'.$logo.'">';
        $downline_html .= '<p class="demo_name_style">&nbsp;';
        $downline_html .= $user_name . "  ";

        
          
        $downline_html .= '<i onclick="goCoopInvDownMlm(this,event,'.$coop_db_id.','.$coop_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-down" style="cursor:pointer;"></i>';
        

        $downline_html .= '</p>';

        $downline_html .= '</div>';



                        
              
        $downline_html .= $this->main_model->printCoopInvTree($package1,$coop_db_id,$level, $parentID,$stage);
        $downline_html .= '</li>';
        $downline_html .= '</ul>';

        $downline_html .= '</div>';
        
        
        $props['downline_html'] = $downline_html;


        return Inertia::render('CoopInvGenealogyTree',$props);
        
    }

    public function changeEnableInvestment(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false);

        if($post_data->status){
            $status = $post_data->status;
            

            
            if($status == "yes" || $status == "no"){
                $status = ($status == "yes") ? 1 : 0;
                $form_array = array(
                    'allow_investments' => $status
                );

                $response_arr['success'] = ($this->main_model->updateUserTable($form_array,$user_id)) ? true : false;
            }
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }


    public function loadAdminCooperativeInvestementsPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'cooperative_investments_admin';
        $props['page_title'] = $this->main_model->custom_echo('Cooperative Investments',30);

        $props['user_name'] = $req->query('user_name');
        $props['amount'] = $req->query('amount');
        $props['duration'] = $req->query('duration');
        $props['status'] = $req->query('status');
        $props['settled_amount'] = $req->query('settled_amount');
        $props['settled_date_time'] = $req->query('settled_date_time');
        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');

        if(empty($props['user_name'])){
            $props['user_name'] = "";
        }

        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        if(empty($props['duration'])){
            $props['duration'] = "";
        }

        if(empty($props['status'])){
            $props['status'] = "all";
        }

        if(empty($props['settled_amount'])){
            $props['settled_amount'] = "";
        }

        if(empty($props['settled_date_time'])){
            $props['settled_date_time'] = "";
        }

        
        

        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }

        
        $all_history = $this->main_model->getInvestmentHistoryForAdmin($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $user_id = $row->user_id;
                $user_name = $row->user_name;
                
                $amount = $row->amount;

                


                $row->full_name = $this->main_model->getUserParamById("full_name",$user_id);
                // $user_name = $this->meetglobal_model->getUserParamById("user_name",$user_id);
                $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);
                $row->index = $index;
                        
            }
        }

        $props['all_history'] = $all_history;
        $props['length'] = $length;
        $props['total_pending_amount'] = number_format($this->main_model->getTotalPendingAmountForCoopInvestment(),2);
        $allow_investments = $this->data['allow_investments'];
        $props['allow_investments'] = ($allow_investments == 1) ? "yes" : "no";

        return Inertia::render('Admin/CooperativeInvestments',$props);
        
    }

    public function loadViewInvestmentHistoryPage (Request $req){
        $user_id = $this->data['user_id'];
        $coop_db_id = $this->data['coop_db_id'];
        $user_name = $this->data['user_name'];
        $users_user_id = $user_id;
        $users_user_name = $user_name;
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'view_investment_history';
        $props['page_title'] = $this->main_model->custom_echo("View Investment History",30);
        

        $props['amount'] = $req->query('amount');
        $props['duration'] = $req->query('duration');
        $props['status'] = $req->query('status');
        $props['settled_amount'] = $req->query('settled_amount');
        $props['settled_date_time'] = $req->query('settled_date_time');
        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');


        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        if(empty($props['duration'])){
            $props['duration'] = "";
        }

        if(empty($props['status'])){
            $props['status'] = "all";
        }

        if(empty($props['settled_amount'])){
            $props['settled_amount'] = "";
        }

        if(empty($props['settled_date_time'])){
            $props['settled_date_time'] = "";
        }

        
        

        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }




        $all_history = $this->main_model->getInvestmentHistoryForUser($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }

        $props['all_history'] = $all_history;
        $props['length'] = $length;
        
        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $user_id = $row->user_id;
                

                // $row->full_name = $this->main_model->getUserFullNameByUserId($user_id);
                // $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);


        
                
                $row->index = $index;                           
            }
        }

        return Inertia::render('ViewInvestmentHistory',$props);
    }

    public function processMakeCoopInvestment(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'invalid_amount' => true,'invalid_duration' => true,'not_bouyant' => true,'not_registered' => true,'inv_not_allow' => true);

        if($post_data->amount && $post_data->duration_num){
            $amount = $post_data->amount;
            $duration = $post_data->duration_num;

            
            if($amount % 10000 == 0){
                $response_arr['invalid_amount'] = false;
                if($duration == "1" || $duration == "3" || $duration == "6" || $duration == "12"){
                    $response_arr['invalid_duration'] = false;

                    if($this->main_model->checkIfAdminAllowedInvestments()){
                        $response_arr['inv_not_allow'] = false;
                        $total_income = $this->data['total_income'];
                        $withdrawn = $this->data['withdrawn'];
                        $balance = $total_income - $withdrawn;
                        if($balance >= $amount){
                            $response_arr['not_bouyant'] = false;
                            if($this->main_model->checkIfUserIsRegisteredForCooperativeInvestment(null)){
                                $response_arr['not_registered'] = false;

                                $form_array = array(
                                    'user_id' => $user_id,
                                    'amount' => $amount,
                                    'duration' => $duration,
                                    'date' => $date,
                                    'time' => $time
                                );
                                $amount_to_debit_user = $amount;

                                $summary = "Debit Of " . $amount_to_debit_user . " For Cooperative Investment";

                                if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                    if($this->main_model->makeInvestmentForCoopUser($form_array)){
                                        $response_arr['success'] = true;
                                    }
                                }

                            }

                        }
                    }

                }
            }
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadManageCooperativeInvestmentPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'manage_investments';
        $props['csrf'] = csrf_token();
        $props['page_title'] = $this->main_model->custom_echo('Cooperative Investment',25);

        $allow_investments = true;
        if(!$this->main_model->checkIfAdminAllowedInvestments()){
            $allow_investments = false;
        }
        $props['allow_investments'] = $allow_investments;
        return Inertia::render('ManageCooperativeInvestment',$props);
        
    }

    public function loadManageCooperativeInvestmentLoansPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'manage_investment_loans';
        $props['csrf'] = csrf_token();
        $props['page_title'] = $this->main_model->custom_echo('Cooperative Loans',25);

        $loanable_amount = $this->main_model->getCoopInvLoanableAmountForUser($user_id);
        if($loanable_amount >= 0){
            $props['show_request_loan_btn'] = true;
        }else{
            $props['show_request_loan_btn'] = false;
        }

        $props['loanable_amount'] = number_format($loanable_amount,2);
        $props['pending_loan'] = $this->main_model->checkIfUserDoesNotHaveAnyPendingLoan($user_id);
        $props['debt_amount'] = number_format($this->main_model->getSmartBusinessLoanDebtForUser($user_id),2);
        
        return Inertia::render('CooperativeLoans',$props);
        
    }

    public function processWithdrawCooperativeInvestmentEarnings(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false);

        if(isset($post_data->id)){
            $id = $post_data->id;

            $earning_details = $this->main_model->getWeeklyEarningDetailsById($id);
            if(is_object($earning_details)){
                foreach($earning_details as $row){
                    $id = $row->id;
                    $week = $row->week;
                    $total_earnings = $row->total_earnings;
                    $last_credit_date_time = $row->last_credit_date_time;
                    $date_of_registration = $row->date_of_registration;
                    $charged = $row->charged;
                    $amt_charged = $row->amt_charged;
                    $charged_date_time = $row->charged_date_time;
                    $withdrawable = $row->withdrawable;
                    $withdrawn = $row->withdrawn;
                    $withdrawn_date_time = $row->withdrawn_date_time;
                    $date_time = $row->date_time;

                    if($withdrawable == 1 && $charged == 1 && $withdrawn == 0){
                        $form_array = array(
                            'withdrawn' => 1,
                            'withdrawn_date_time' => $date . " " . $time
                        );

                        $this->main_model->updateCoopWeeklyEarningTableById($form_array,$id);
                        $summary = "Transfer from cooperative investment earnings";
                        $this->main_model->creditUser($user_id,$total_earnings,$summary);
                        $response_arr['success'] = true;
                    }
                }
            }
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadManageCooperativeInvestmentEarningsPage (Request $req){
        $user_id = $this->data['user_id'];
        $coop_db_id = $this->data['coop_db_id'];
        $user_name = $this->data['user_name'];
        $users_user_id = $user_id;
        $users_user_name = $user_name;
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'manage_cooperative_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Manage Coop. Earnings",30);
        

        $props['week_name'] = $req->query('week_name');
        $props['total_earnings'] = $req->query('total_earnings');
        $props['last_credit_date_time'] = $req->query('last_credit_date_time');
        $props['charged'] = $req->query('charged');
        $props['amt_charged'] = $req->query('amt_charged');
        $props['charged_date_time'] = $req->query('charged_date_time');
        $props['withdrawable'] = $req->query('withdrawable');
        $props['withdrawn'] = $req->query('withdrawn');
        $props['withdrawn_date_time'] = $req->query('withdrawn_date_time');
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');


        if(empty($props['week_name'])){
            $props['week_name'] = "";
        }

        if(empty($props['total_earnings'])){
            $props['total_earnings'] = "";
        }

        if(empty($props['last_credit_date_time'])){
            $props['last_credit_date_time'] = "";
        }

        if(empty($props['charged'])){
            $props['charged'] = false;
        }

        if(empty($props['amt_charged'])){
            $props['amt_charged'] = "";
        }

        if(empty($props['charged_date_time'])){
            $props['charged_date_time'] = "";
        }

        if(empty($props['withdrawable'])){
            $props['withdrawable'] = false;
        }

        if(empty($props['withdrawn'])){
            $props['withdrawn'] = false;
        }

        if(empty($props['withdrawn_date_time'])){
            $props['withdrawn_date_time'] = "";
        }


        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }




        $all_weeks = $this->main_model->getCoopInvEarningsWeeksForUser($user_id,$coop_db_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }

        $props['all_weeks'] = $all_weeks;
        $props['length'] = $length;
        
        if(is_object($all_weeks)){
            $j = 0;
            foreach($all_weeks as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $user_id = $row->user_id;
                $week = $row->week;
                $row->week = ucwords(str_replace("_", " ", $row->week));
                
               
                
                

                // $row->full_name = $this->main_model->getUserFullNameByUserId($user_id);
                // $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);


        
                
                $row->index = $index;                           
            }
        }

        return Inertia::render('ManageCooperativeEarnings',$props);
    }

    public function submitSponsorUsernameCoopRegi(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'empty_username' => true,'already_registered' => true,'not_bouyant' => true,'invalid_username' => true,'invalid_placement' => false,'user_profile_img' => '','sponsor_full_name' => '','sponsor_phone_num' => '','sponsor_email_address' => '' , 'sponsor_user_name' => '','sponsor_mlm_db_id' => '');

        if(isset($post_data->user_name)){
            $response_arr['empty_username'] = false;
            $sponsor_user_name = $post_data->user_name;
            if(!$this->main_model->checkIfUserIsRegisteredForCooperativeInvestment(null)){
                $response_arr['already_registered'] = false;
                $total_income = $this->data['total_income'];
                $withdrawn = $this->data['withdrawn'];
                $balance = $total_income - $withdrawn;
                if($balance >= 2000){
                    $response_arr['not_bouyant'] = false;
                    if($this->main_model->checkIfUserNameExists($sponsor_user_name)){
                        $response_arr['invalid_username'] = false;
                        $sponsor_user_id = $this->main_model->getUserIdByName($sponsor_user_name);
                        if($this->main_model->checkIfUserIsRegisteredForCooperativeInvestment($sponsor_user_id)){
                            $response_arr['success'] = true;
                            $response_arr['sponsor_user_name'] = $sponsor_user_name;
                            $sponsor_id = $sponsor_user_id;
                            $user_profile_img = $this->main_model->getUserParamById("logo",$sponsor_id);
                            if(is_null($user_profile_img)){
                                $user_profile_img = "avatar.jpg";
                            }
                            $response_arr['user_profile_img'] = asset('/images/'.$user_profile_img);
                            $sponsor_full_name = $this->main_model->getUserParamById("full_name",$sponsor_id);
                            $response_arr['sponsor_full_name'] = $sponsor_full_name;

                            $sponsor_phone_code = $this->main_model->getUserParamById("phone_code",$sponsor_id);
                            $sponsor_phone_num = $this->main_model->getUserParamById("phone",$sponsor_id);
                            $sponsor_email_address = $this->main_model->getUserParamById("email",$sponsor_id);
                            $response_arr['sponsor_email_address'] = $sponsor_email_address;
                            $response_arr['sponsor_phone_num'] = "+" . $sponsor_phone_code . "" . $sponsor_phone_num;
                            $response_arr['sponsor_mlm_db_id'] = $this->main_model->getUsersFirstCoopDbId($sponsor_user_id);

                        }else{
                            $response_arr['invalid_placement'] = true;
                        }
                    }
                }
                
            }
            
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function registerCoopInvWithoutPlacement(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'details_missing' => true,'already_registered' => true,'not_bouyant' => true,'invalid_sponsor' => true);
        if(isset($post_data->register) && isset($post_data->sponsor_mlm_db_id)){
            
            $response_arr['details_missing'] = false;
 
            $sponsor_mlm_db_id = $post_data->sponsor_mlm_db_id;
            if($this->main_model->checkIfCoopDbIdIsValid($sponsor_mlm_db_id)){
                $response_arr['invalid_sponsor'] = false;
                if(!$this->main_model->checkIfUserIsRegisteredForCooperativeInvestment(null)){
                    $response_arr['already_registered'] = false;
                    $total_income = $this->data['total_income'];
                    $withdrawn = $this->data['withdrawn'];
                    $balance = $total_income - $withdrawn;
                    if($balance >= 2000){
                        $response_arr['not_bouyant'] = false;
                        
                        $amount_to_debit_user = 2000;

                        $summary = "Debit Of " . $amount_to_debit_user . " For Cooperative Investment Registration";

                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){

                            if($this->main_model->performCoopInvRegistrationForUsersWithoutPlacement($user_id,$sponsor_mlm_db_id,$date,$time)){
                                
                                $response_arr['success'] = true;
                                        
                                    
                            }
                          
                        }
                    }
                    
                }
            }   
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function finallyRegisterUserCoopInv(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'details_missing' => true,'empty_username' => true,'already_registered' => true,'not_bouyant' => true,'invalid_username' => true,'invalid_placement' => true,'placement_invalid' => true,'no_available_position' => false,'available_positions' => '','invalid_sponsor' => true);
        if(isset($post_data->mlm_db_id) && isset($post_data->position)){
            if($post_data->position == "left" || $post_data->position == "right"){
                $response_arr['details_missing'] = false;

                if(isset($post_data->user_name) && isset($post_data->sponsor_mlm_db_id)){
                    $response_arr['empty_username'] = false;
                    $placement_user_name = $post_data->user_name;
                    $sponsor_mlm_db_id = $post_data->sponsor_mlm_db_id;
                    if(!$this->main_model->checkIfUserIsRegisteredForCooperativeInvestment(null)){
                        $response_arr['already_registered'] = false;
                        $total_income = $this->data['total_income'];
                        $withdrawn = $this->data['withdrawn'];
                        $balance = $total_income - $withdrawn;
                        if($balance >= 2000){
                            $response_arr['not_bouyant'] = false;
                            if($this->main_model->checkIfCoopDbIdIsValid($sponsor_mlm_db_id)){
                                $response_arr['invalid_sponsor'] = false;
                                if($this->main_model->checkIfUserNameExists($placement_user_name)){
                                    $response_arr['invalid_username'] = false;
                                    $placement_user_id = $this->main_model->getUserIdByName($placement_user_name);
                                    if($this->main_model->checkIfUserIsRegisteredForCooperativeInvestment($placement_user_id)){
                                        if(isset($post_data->mlm_db_id)){
                                            $mlm_db_id = $post_data->mlm_db_id;
                                            if($this->main_model->checkIfThisCoopInvMlmDbIdMatchesWithUserId($placement_user_id,$mlm_db_id)){     
                                                $response_arr['placement_invalid'] = false;
                                                
                                                if($this->main_model->checkIfCoopInvMlmDbIdHasNoAvailablePositionUnderHim($mlm_db_id)){
                                                    $response_arr['no_available_position'] = true;
                                                }else{
                                                    //Remember to debit user 2000 first.
                                                    //Build coop inv income system i.e db and etc.
                                                    //Note code below is not valid yet. Its from the first mlm registration part
                                                    //Earning on registration is 1000 to sponsor and 30 naira to placement

                                                    

                                                    $amount_to_debit_user = 2000;

                                                    $summary = "Debit Of " . $amount_to_debit_user . " For Cooperative Investment Registration";

                                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                         
                                                                    
                                                        $placement_mlm_db_id = $post_data->mlm_db_id;
                                                        $placement_position = $post_data->position;
      
                                                        if($this->main_model->performCoopInvRegistrationForUsersWithPlacement($user_id,$sponsor_mlm_db_id,$placement_mlm_db_id,$placement_position,$date,$time)){
                                                            
                                                            
                                                            $response_arr['success'] = true;
                                                                    
                                                                
                                                        }
                                                      
                                                    }

                                                }
                                            }
                                            
                                        }
                                    }
                                }
                            }
                        }
                        
                    }
                    
                }
            }
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function selectPositioningForCoopInvRegi(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'empty_username' => true,'already_registered' => true,'not_bouyant' => true,'invalid_username' => true,'invalid_placement' => true,'placement_invalid' => true,'no_available_position' => false,'available_positions' => '');

        if(isset($post_data->user_name)){
            $response_arr['empty_username'] = false;
            $placement_user_name = $post_data->user_name;
            if(!$this->main_model->checkIfUserIsRegisteredForCooperativeInvestment(null)){
                $response_arr['already_registered'] = false;
                $total_income = $this->data['total_income'];
                $withdrawn = $this->data['withdrawn'];
                $balance = $total_income - $withdrawn;
                if($balance >= 2000){
                    $response_arr['not_bouyant'] = false;
                    if($this->main_model->checkIfUserNameExists($placement_user_name)){
                        $response_arr['invalid_username'] = false;
                        $placement_user_id = $this->main_model->getUserIdByName($placement_user_name);
                        if($this->main_model->checkIfUserIsRegisteredForCooperativeInvestment($placement_user_id)){
                            if(isset($post_data->mlm_db_id)){
                                $mlm_db_id = $post_data->mlm_db_id;
                                if($this->main_model->checkIfThisCoopInvMlmDbIdMatchesWithUserId($placement_user_id,$mlm_db_id)){     
                                    $response_arr['placement_invalid'] = false;
                                    $response_arr['success'] = true;
                                    if($this->main_model->checkIfCoopInvMlmDbIdHasNoAvailablePositionUnderHim($mlm_db_id)){
                                        $response_arr['no_available_position'] = true;
                                    }else{
                                        $response_arr['available_positions'] = $this->main_model->getAvailablePositionUnderCoopInvMlmDbId($mlm_db_id);
                                    }
                                }
                                
                            }
                        }
                    }
                }
                
            }
            
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function submitPlacementUsernameCoopRegiStep2(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'empty_username' => true,'already_registered' => true,'not_bouyant' => true,'invalid_username' => true,'invalid_placement' => true,'placement_invalid' => true,'all_mlm_ids' => array());

        if(isset($post_data->user_name)){
            $response_arr['empty_username'] = false;
            $placement_user_name = $post_data->user_name;
            if(!$this->main_model->checkIfUserIsRegisteredForCooperativeInvestment(null)){
                $response_arr['already_registered'] = false;
                $total_income = $this->data['total_income'];
                $withdrawn = $this->data['withdrawn'];
                $balance = $total_income - $withdrawn;
                if($balance >= 2000){
                    $response_arr['not_bouyant'] = false;
                    if($this->main_model->checkIfUserNameExists($placement_user_name)){
                        $response_arr['invalid_username'] = false;
                        $placement_user_id = $this->main_model->getUserIdByName($placement_user_name);
                        if($this->main_model->checkIfUserIsRegisteredForCooperativeInvestment($placement_user_id)){
                            $all_mlm_ids = $this->main_model->getAllUsersCoopInvsMlmDbIds($placement_user_id);
                            if(is_array($all_mlm_ids)){
                                $response_arr['placement_invalid'] = false;
                                $response_arr['success'] = true;
                                $response_arr['all_mlm_ids'] = $all_mlm_ids;
                            }
                        }
                    }
                }
                
            }
            
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function submitPlacementUsernameCoopRegi(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'empty_username' => true,'already_registered' => true,'not_bouyant' => true,'invalid_username' => true,'invalid_placement' => true,'user_profile_img' => '','placement_full_name' => '','placement_phone_num' => '','placement_email_address' => '' , 'placement_user_name' => '');

        if(isset($post_data->user_name)){
            $response_arr['empty_username'] = false;
            $placement_user_name = $post_data->user_name;
            if(!$this->main_model->checkIfUserIsRegisteredForCooperativeInvestment(null)){
                $response_arr['already_registered'] = false;
                $total_income = $this->data['total_income'];
                $withdrawn = $this->data['withdrawn'];
                $balance = $total_income - $withdrawn;
                if($balance >= 2000){
                    $response_arr['not_bouyant'] = false;
                    if($this->main_model->checkIfUserNameExists($placement_user_name)){
                        $response_arr['invalid_username'] = false;
                        $placement_user_id = $this->main_model->getUserIdByName($placement_user_name);
                        if($this->main_model->checkIfUserIsRegisteredForCooperativeInvestment($placement_user_id)){
                            $response_arr['success'] = true;
                            $response_arr['placement_user_name'] = $placement_user_name;
                            $sponsor_id = $placement_user_id;
                            $user_profile_img = $this->main_model->getUserParamById("logo",$sponsor_id);
                            if(is_null($user_profile_img)){
                                $user_profile_img = "avatar.jpg";
                            }
                            $response_arr['user_profile_img'] = asset('/images/'.$user_profile_img);
                            $sponsor_full_name = $this->main_model->getUserParamById("full_name",$sponsor_id);
                            $response_arr['placement_full_name'] = $sponsor_full_name;

                            $sponsor_phone_code = $this->main_model->getUserParamById("phone_code",$sponsor_id);
                            $sponsor_phone_num = $this->main_model->getUserParamById("phone",$sponsor_id);
                            $sponsor_email_address = $this->main_model->getUserParamById("email",$sponsor_id);
                            $response_arr['placement_email_address'] = $sponsor_email_address;
                            $response_arr['placement_phone_num'] = "+" . $sponsor_phone_code . "" . $sponsor_phone_num;

                        }
                    }
                }
                
            }
            
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function checkIfUserValidRegCoopInv(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'already_registered' => true);

        if(isset($post_data->show_records)){
            
            if(!$this->main_model->checkIfUserIsRegisteredForCooperativeInvestment(null)){
                $response_arr['success'] = true;
            }
            
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadCooperativeInvestmentPage(Request $req){

        
        $user_id = $this->data['user_id'];
        $user_name = $this->data['user_name'];
        $users_user_id = $user_id;
        $users_user_name = $user_name;
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'cooperative_investment';
        $props['page_title'] = $this->main_model->custom_echo("Cooperative & Investment",30);



       

        return Inertia::render('CooperativeInvestment',$props);
        
        
    }

    public function purchaseElectricityWithPayscribe(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        if(isset($post_data->productCode) && isset($post_data->productToken) && isset($post_data->use_payscribe)){
            $productCode = $post_data->productCode;
            $productToken = $post_data->productToken;
            $use_payscribe = $post_data->use_payscribe;
            if($productCode != "" && $productToken != "" && $use_payscribe){
                $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'order_id' => '','invalid_meterno' => false,'meter_type_not_available' => false,'metertoken' => '','transaction_pending' => false);

                $validationRules = [
                    'disco' => 'required|in:eko,ikeja,abuja,ibadan,enugu,phc,kano,kaduna,jos',
                    'meter_type' => 'required|in:prepaid,postpaid',
                    'meter_number' => 'required|numeric|digits_between:5,15',
                    'amount' => 'required|numeric|min:100|max:50000',
                    'mobile_number' => 'required|numeric|digits_between:5,15',
                    'email' => 'required|email:rfc,dns,strict,spoof,filter',
                    
                ];

                $messages = [];

                $validation = Support_Request::validate($validationRules);

                
                if($validation){
                    $disco = $post_data->disco;
                    $meter_type = $post_data->meter_type;           
                    $meter_number = $post_data->meter_number;
                    $amount = $post_data->amount;
                    $mobile_number = $post_data->mobile_number;
                    $email = $post_data->email;
                    $payscribe_disco = "";
                    $phone_number = $mobile_number;
                    $meter_no = $meter_number;

                    $club_disco = "";
                
                    if($disco == "eko"){
                        $disco_code = "EKO";
                        $payscribe_disco = "ekedc";
                        $club_disco = "01";
                    }else if($disco == "ikeja"){
                        $disco_code = "IKEJA";
                        $payscribe_disco = "ikedc";
                        $club_disco = "02";
                    }else if($disco == "abuja"){
                        $disco_code = "ABUJA";
                        $payscribe_disco = "aedc";
                        $club_disco = "03";
                    }else if($disco == "ibadan"){
                        $disco_code = "IBADAN";
                        $payscribe_disco = "ibedc";
                        $club_disco = "07";
                    }else if($disco == "enugu"){
                        $disco_code = "ENUGU";
                        $payscribe_disco = "eedc";
                        $club_disco = "09";
                    }else if($disco == "phc"){
                        $disco_code = "PH";
                        $payscribe_disco = "phedc";
                        $club_disco = "05";
                    }else if($disco == "kano"){
                        $disco_code = "KANO";
                        $club_disco = "04";
                    }else if($disco == "kaduna"){
                        $disco_code = "KADUNA";
                        $payscribe_disco = "kedco";
                        $club_disco = "08";
                    }else if($disco == "jos"){
                        $disco_code = "JOS";
                        $club_disco = "06";
                    }

                    if($meter_type == "prepaid"){
                        $meter_type = "PREPAID";
                        $club_meter_type = "01";
                    }else if($meter_type == "postpaid"){
                        $meter_type = "POSTPAID";
                        $club_meter_type = "02";
                    }

                        
                   
                    if($post_data->sms_check == true){
                        $amount_deb_user = $amount + 5;
                    }else{
                        $amount_deb_user = $amount;
                    }
                    $amount_to_debit_user = $amount;
                    $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);

                    $meter_type = strtolower($meter_type);

                    if($amount_deb_user <= $user_total_amount){

                        $vtu_platform = $this->main_model->getVtuPlatformToUse('','electricity');
                                // return $vtu_platform;
                        if($vtu_platform == "payscribe" && $payscribe_disco != ""){

                            
                            $url = "https://www.payscribe.ng/api/v1/electricity/vend";
                            $use_post = true;
                            $data = array(
                                'productCode' => $productCode,
                                'productToken' => $productToken,
                                'phone' => $mobile_number
                            );
                            $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);

                            // return $response;
                            if($this->main_model->isJson($response)){
                                $response = json_decode($response);
                                if(is_object($response)){
                                    
                                    if($response->status == true && $response->status_code == 200){
                                        $summary = "Debit Of " . $amount_to_debit_user . " For Electricity Recharge";

                                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                        
                                            
                                            $order_id = $response->message->details->trans_id;

                                            if(isset($response->message->details->Token)){
                                                if(!is_null($response->message->details->Token)){
                                                    $metertoken = $response->message->details->Token;
                                                    $this->main_model->sendMeterTokenForPrepaidToUserByNotif($user_id,$email,$date,$time,$order_id,$disco,$meter_no,$amount,$metertoken);
                                                }
                                            }

                                            $form_array = array(
                                                'user_id' => $user_id,
                                                'type' => 'electricity',
                                                'sub_type' => $disco,
                                                'date' => $date,
                                                'time' => $time,
                                                'amount' => $amount,
                                                'number' => $meter_no,
                                                'order_id' => $order_id
                                            );
                                            if($this->main_model->addTransactionStatusPayscribeElectricity($form_array,true)){
                                                $response_arr['success'] = true;
                                                $response_arr['order_id'] = $order_id;
                                                $response_arr['metertoken'] = $metertoken;


                                                if($post_data->sms_check == true){
                                                    $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                                                    $amount_to_debit_user = 5;
                                                    // echo $user_total_amount;
                                                    // echo $amount;

                                                    if($amount_to_debit_user <= $user_total_amount){
                                                        

                                                        if($meter_type == "prepaid"){
                                                            $to = $phone_number;
                                                            $message = "Your Meter Token For Meter Number " . $meter_no . " Is ". $metertoken;
                                                            $url = "https://www.payscribe.ng/api/v1/sms";

                                                            $use_post = true;
                                                            $post_data = [
                                                                'to' => $to,
                                                                'message' => $message
                                                            ];

                                                            
                                                            // var_dump($post_data);

                                                            $response = $this->main_model->payscribeVtuCurl($url,$use_post,$post_data);


                                                            if($this->main_model->isJson($response)){

                                                                $response = json_decode($response);
                                                                // var_dump($response);

                                                                if($response->status && $response->status_code == 200){
                                            
                                                                    $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                                        $order_id = $response->message->details->transaction_id;
                                                                        $form_array = array(
                                                                            'user_id' => $user_id,
                                                                            'type' => 'bulk_sms',
                                                                            'sub_type' => "",
                                                                            'number' => $message,
                                                                            'date' => $date,
                                                                            'time' => $time,
                                                                            'amount' => $amount_to_debit_user,
                                                                            'order_id' => $order_id
                                                                        );
                                                                        if($this->main_model->addTransactionStatusOnly($form_array)){
                                                                            $response_arr['success'] = true;
                                                                            $response_arr['order_id'] = $order_id;
                                                                        }
                                                                    }
                                                                }else if($response->status && $response->status_code == 201){
                                                                    

                                                                    $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                                        $order_id = $response->message->details->transaction_id;
                                                                        $form_array = array(
                                                                            'user_id' => $user_id,
                                                                            'type' => 'bulk_sms',
                                                                            'sub_type' => "",
                                                                            'number' => $message,
                                                                            'date' => $date,
                                                                            'time' => $time,
                                                                            'amount' => $amount_to_debit_user,
                                                                            'order_id' => $order_id
                                                                        );
                                                                        if($this->main_model->addTransactionStatusOnly($form_array)){
                                                                            $response_arr['success'] = true;
                                                                            $response_arr['order_id'] = $order_id;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }

                                                    }
                                                }
                                            }
                                        }
                                    }else if($response->status == true && $response->status_code == 201){
                                        $response_arr['transaction_pending'] = true;

                                        $summary = "Debit Of " . $amount_to_debit_user . " For Electricity Recharge";

                                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                        
                                            $order_id = $response->message->details->trans_id;

                                            

                                            $form_array = array(
                                                'user_id' => $user_id,
                                                'type' => 'electricity',
                                                'sub_type' => $disco,
                                                'date' => $date,
                                                'time' => $time,
                                                'amount' => $amount,
                                                'number' => $meter_no,
                                                'order_id' => $order_id
                                            );
                                            if($this->main_model->addTransactionStatusOnly($form_array)){
                                                $response_arr['success'] = true;
                                                $response_arr['order_id'] = $order_id;
                                                $response_arr['metertoken'] = "";
                                            }
                                        }
                                    }
                                }
                            }  
                        }else if($vtu_platform == "clubkonnect" && $club_disco != ""){
                            $url = "https://www.nellobytesystems.com/APIElectricityV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&ElectricCompany=" . $club_disco . "&MeterType=" . $club_meter_type . "&MeterNo=" . $meter_no . "&Amount=" . $amount;
                            $use_post = true;

                            $response = $this->main_model->vtu_curl($url,$use_post,$post_data=[]);
                            // return $response;
                            if($this->main_model->isJson($response)){
                                $response = json_decode($response);
                                if(is_object($response)){
                                    $status = $response->status;
                                    $metertoken = "";
                                    if($status == "ORDER_RECEIVED"){
                                        $summary = "Debit Of " . $amount_to_debit_user . " For Electricity Recharge";

                                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                        
                                            
                                            if(isset($response->transactionid)){
                                                $order_id = $response->transactionid;
                                            }else{
                                                $order_id = "";
                                            }

                                            if(isset($response->metertoken)){
                                                $metertoken = $response->metertoken;
                                                $this->main_model->sendMeterTokenForPrepaidToUserByNotif($user_id,$email,$date,$time,$order_id,$disco,$meter_no,$amount,$metertoken);
                                            }

                                            $form_array = array(
                                                'user_id' => $user_id,
                                                'type' => 'electricity',
                                                'sub_type' => $disco,
                                                'date' => $date,
                                                'time' => $time,
                                                'amount' => $amount,
                                                'number' => $meter_no,
                                                'order_id' => $order_id
                                            );
                                            if($this->main_model->addTransactionStatus($form_array,true)){
                                                $response_arr['success'] = true;
                                                $response_arr['order_id'] = $order_id;
                                                $response_arr['metertoken'] = $metertoken;


                                                if($post_data->sms_check == true){
                                                    $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                                                    $amount_to_debit_user = 5;
                                                    // echo $user_total_amount;
                                                    // echo $amount;

                                                    if($amount_to_debit_user <= $user_total_amount){
                                                        

                                                        if($meter_type == "prepaid"){
                                                            $to = $phone_number;
                                                            $message = "Your Meter Token For Meter Number " . $meter_no . " Is ". $metertoken;
                                                            $url = "https://www.payscribe.ng/api/v1/sms";

                                                            $use_post = true;
                                                            $post_data = [
                                                                'to' => $to,
                                                                'message' => $message
                                                            ];

                                                            
                                                            // var_dump($post_data);

                                                            $response = $this->main_model->payscribeVtuCurl($url,$use_post,$post_data);


                                                            if($this->main_model->isJson($response)){

                                                                $response = json_decode($response);
                                                                // var_dump($response);

                                                                if($response->status && $response->status_code == 200){
                                            
                                                                    $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                                        $order_id = $response->message->details->transaction_id;
                                                                        $form_array = array(
                                                                            'user_id' => $user_id,
                                                                            'type' => 'bulk_sms',
                                                                            'sub_type' => "",
                                                                            'number' => $message,
                                                                            'date' => $date,
                                                                            'time' => $time,
                                                                            'amount' => $amount_to_debit_user,
                                                                            'order_id' => $order_id
                                                                        );
                                                                        if($this->main_model->addTransactionStatusOnly($form_array)){
                                                                            $response_arr['success'] = true;
                                                                            $response_arr['order_id'] = $order_id;
                                                                        }
                                                                    }
                                                                }else if($response->status && $response->status_code == 201){
                                                                    

                                                                    $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                                        $order_id = $response->message->details->transaction_id;
                                                                        $form_array = array(
                                                                            'user_id' => $user_id,
                                                                            'type' => 'bulk_sms',
                                                                            'sub_type' => "",
                                                                            'number' => $message,
                                                                            'date' => $date,
                                                                            'time' => $time,
                                                                            'amount' => $amount_to_debit_user,
                                                                            'order_id' => $order_id
                                                                        );
                                                                        if($this->main_model->addTransactionStatusOnly($form_array)){
                                                                            $response_arr['success'] = true;
                                                                            $response_arr['order_id'] = $order_id;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }

                                                    }
                                                }
                                            }
                                             
                                        }
                                    }else if($status == "INVALID_MeterNo"){
                                        $response_arr['invalid_meterno'] = true;
                                                
                                    }else if($status == "MeterType_NOT_AVAILABLE"){
                                        $response_arr['meter_type_not_available'] = true;
                                                
                                    }else if($status == "INSUFFICIENT_BALANCE"){
                                        // $response_arr['invalid_recipient'] = true;
                                                
                                    }else if($status == "INVALID_CREDENTIALS"){
                                        // $response_arr['invalid_recipient'] = true;
                                        // echo "string";
                                                
                                    }
                                }
                                    
                            }
                        }   
                        
                    }else{
                        $response_arr['insuffecient_funds'] = true;
                    }
                    
                                
                }
            }
            

            $response_arr = json_encode($response_arr);
        }
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function validateMeterNumberDisco(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'customer_name' => '','invalid_user' => false,'use_payscribe' => false,'productCode' => '','productToken' => '');

        $validationRules = [
            'disco' => 'required|in:eko,ikeja,abuja,ibadan,enugu,phc,kano,kaduna,jos',
            'meter_type' => 'required|in:prepaid,postpaid',
            'meter_number' => 'required|numeric|digits_between:5,15',
            'amount' => 'required|numeric|min:100|max:50000',
            'mobile_number' => 'required|numeric|digits_between:5,15',
            'email' => 'required|email:rfc,dns,strict,spoof,filter',
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            $disco = $post_data->disco;
            $meter_type = $post_data->meter_type;           
            $meter_number = $post_data->meter_number;
            $amount = $post_data->amount;
            $mobile_number = $post_data->mobile_number;
            $email = $post_data->email;
            $payscribe_disco = "";
            $club_disco = "";
                
            if($disco == "eko"){
                $disco_code = "EKO";
                $payscribe_disco = "ekedc";
                $club_disco = "01";
            }else if($disco == "ikeja"){
                $disco_code = "IKEJA";
                $payscribe_disco = "ikedc";
                $club_disco = "02";
            }else if($disco == "abuja"){
                $disco_code = "ABUJA";
                $payscribe_disco = "aedc";
                $club_disco = "03";
            }else if($disco == "ibadan"){
                $disco_code = "IBADAN";
                $payscribe_disco = "ibedc";
                $club_disco = "07";
            }else if($disco == "enugu"){
                $disco_code = "ENUGU";
                $payscribe_disco = "eedc";
                $club_disco = "09";
            }else if($disco == "phc"){
                $disco_code = "PH";
                $payscribe_disco = "phedc";
                $club_disco = "05";
            }else if($disco == "kano"){
                $disco_code = "KANO";
                $club_disco = "04";
            }else if($disco == "kaduna"){
                $disco_code = "KADUNA";
                $payscribe_disco = "kedco";
                $club_disco = "08";
            }else if($disco == "jos"){
                $disco_code = "JOS";
                $club_disco = "06";
            }

            if($meter_type == "prepaid"){
                $meter_type = "PREPAID";
                $club_meter_type = "01";
            }else if($meter_type == "postpaid"){
                $meter_type = "POSTPAID";
                $club_meter_type = "02";
            }

            

            // $url = "https://www.nellobytesystems.com/APIVerifyElectricityV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&ElectricCompany=".$disco_code."&meterno=".$meter_no;
            $url = "https://api.buypower.ng/v2/check/meter?meter=".$meter_number."&disco=".$disco_code."&vendType=".$meter_type."&orderId=true";
            // echo $url;
            $use_post = false;

            $response = $this->main_model->buyPowerVtuCurl($url,$use_post);

            // return($response);

            if($this->main_model->isJson($response)){
                $response = json_decode($response);
                if(is_object($response)){
                    if(isset($response->name)){
                        
                        $customer_name = $response->name;
                        // $minVendAmount = $response->minVendAmount;
                        // $maxVendAmount = $response->maxVendAmount;
                        
                        if($customer_name != ""){
                            $response_arr['success'] = true;
                            $response_arr['customer_name'] = $customer_name;

                            $eko_platform = $this->main_model->getVtuPlatformToUse("electricity","eko");
                            if($disco == "eko" && $eko_platform == "payscribe"){
                                // return true;
                                $url = "https://www.payscribe.ng/api/v1/electricity/validate";
                                $use_post = true;
                                $data = array(
                                    'meter_number' => $meter_number,
                                    'meter_type' => strtolower($meter_type),
                                    'amount' => $amount,
                                    'service' => $payscribe_disco
                                );
                                // return $data;
                                $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);

                                if($this->main_model->isJson($response)){
                                    $response = json_decode($response);
                                    // return ($response);

                                    if($response->status == true && $response->status_code == 200){
                                        // if($response->message->details->canVend == true){
                                            $response_arr['use_payscribe'] = true;
                                            $response_arr['productCode'] = $response->message->details->productCode;
                                            $response_arr['productToken'] = $response->message->details->productToken;
                                        // }
                                    }
                                }
                            }else{

                                if($amount < 1000){
                                    $vtu_platform = $this->main_model->getVtuPlatformToUse('','electricity');
                                    // return $vtu_platform;
                                    if($vtu_platform == "payscribe" && $payscribe_disco != ""){
                                        $url = "https://www.payscribe.ng/api/v1/electricity/validate";
                                        $use_post = true;
                                        $data = array(
                                            'meter_number' => $meter_number,
                                            'meter_type' => strtolower($meter_type),
                                            'amount' => $amount,
                                            'service' => $payscribe_disco
                                        );
                                        // return $data;
                                        $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);

                                        if($this->main_model->isJson($response)){
                                            $response = json_decode($response);
                                            // var_dump($response);

                                            if($response->status == true && $response->status_code == 200){
                                                // if($response->message->details->canVend == true){
                                                    $response_arr['use_payscribe'] = true;
                                                    $response_arr['productCode'] = $response->message->details->productCode;
                                                    $response_arr['productToken'] = $response->message->details->productToken;
                                                // }
                                            }
                                        }
                                    }else if($vtu_platform == "clubkonnect" && $club_disco != ""){
                                        // $url = "https://www.payscribe.ng/api/v1/electricity/validate";
                                        $url = "https://www.nellobytesystems.com/APIVerifyElectricityV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&ElectricCompany=".$club_disco."&meterno=".$meter_number;
                                        // return $url;
                                        $use_post = true;

                                        $response = $this->main_model->vtu_curl($url,$use_post,$post_data=[]);
                                        // return $response;
                                        if($this->main_model->isJson($response)){
                                            $response = json_decode($response);
                                            
                                            if(is_object($response)){
                                                if($response->customer_name != ""){
                                                    $response_arr['use_payscribe'] = true;
                                                    $response_arr['productCode'] = "yeye666399";
                                                    $response_arr['productToken'] = "yheye66366";
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }else{
                            $response_arr['invalid_user'] = true;
                        }
                    }else{
                        $response_arr['invalid_user'] = true;
                    }
                }else{

                }
            }
                        
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function rechargeRouter(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
        
        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false);
        if(isset($post_data->selected_plan)){
            $validationRules = [
                'router_service' => 'required|in:smile',
                'router_number' => 'required|numeric',
                
            ];

            $messages = [];

            $validation = Support_Request::validate($validationRules);

            
            if($validation){
                $type = $post_data->router_service;
                $router_number = $post_data->router_number;

                $vtu_platform = $this->main_model->getVtuPlatformToUse('router',$type);
                if($vtu_platform == "payscribe"){ 
                                    
                    $name = $post_data->selected_plan['name'];
                    $amount = $post_data->selected_plan['amount'];
                    
                    $product_code = $post_data->productCode;
                    $code = $post_data->selected_plan['code'];
                    $phone = "0" . $this->data['phone'];
                    $vend_type = "subscription";

                    $amount_to_debit_user = $amount;

                                
                    if($type == "smile"){
                        

                        $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                        // echo $user_total_amount;
                        // echo $amount;

                        if($amount_to_debit_user <= $user_total_amount){

                            if($type == "smile"){
                                $real_type = "smile";
                                $url = "https://www.payscribe.ng/api/v1/internet/vend";

                                $data = [
                                    'service' => 'smile',
                                    'vend_type' => $vend_type,
                                    'code' => $code,
                                    'phone' => $phone,
                                    'productCode' => $product_code
                                ];

                                // echo(json_encode($post_data));
                            }

                            $use_post = true;
                            

                            $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);


                            if($this->main_model->isJson($response)){


                                $response = json_decode($response);
                                // var_dump($response);

                                if($response->status && $response->status_code == 200 ){
                                    

                                    $trans_id = $response->message->details->trans_id;
                                    
                                    $summary = "Debit Of " . $amount_to_debit_user . " For " . $real_type . " Router Recharge";
                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                        
                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'router',
                                            'sub_type' => $real_type,
                                            'number' => $router_number,
                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount,
                                            'order_id' => $trans_id
                                        );
                                        if($this->main_model->addTransactionStatus($form_array)){
                                            $response_arr['success'] = true;
                                            $response_arr['order_id'] = $trans_id;
                                        }
                                    }
                                }
                            }
                        }else{
                            $response_arr['insuffecient_funds'] = true;
                        }
                            
                        
                    }
                }else{
                    
                    
                    
                    $phone = "0" . $this->data['phone'];

                    $plan = $post_data->selected_plan['package_id'];
                         

                    $package_amount = $this->main_model->getPackageAmountForSmileClub("smile",$plan);
                    // return $package_amount;
                    // echo is_numeric($package_amount);
                    if(is_numeric($package_amount)){

                        $amount = $package_amount;

                        $amount_to_debit_user = $amount;
                        $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                        // echo $user_total_amount;

                        // echo $amount;

                        if($amount_to_debit_user <= $user_total_amount){

                            // $url = "https://www.nellobytesystems.com/APICableTVV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&CableTV=".$operator."&Package=".$plan."&SmartCardNo=".$decoder_number;

                            $url = "https://www.nellobytesystems.com/APISmileV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&MobileNetwork=smile-direct&datatplan=".$plan."&MobileNumber=".$router_number."";
                            // return $url;
                            $use_post = true;
                            

                            $response = $this->main_model->vtu_curl($url,$use_post,$post_data=[]);
                            // return $response;
                            if($this->main_model->isJson($response)){
                                $response = json_decode($response);
                                
                                if(is_object($response)){
                                    if($response->status == "ORDER_RECEIVED"){
                                        
                                        $summary = "Debit Of " . $amount_to_debit_user . " For " . $real_type . " Router Recharge";
                                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                            $order_id = $response->transactionid;
                                            $form_array = array(
                                                'user_id' => $user_id,
                                                'type' => 'router',
                                                'sub_type' => $real_type,
                                                'number' => $router_number,
                                                'date' => $date,
                                                'time' => $time,
                                                'amount' => $amount,
                                                'order_id' => $trans_id
                                            );
                                            if($this->main_model->addTransactionStatus($form_array)){
                                                $response_arr['success'] = true;
                                                $response_arr['order_id'] = $trans_id;
                                            }
                                        }
                                        
                                    }
                                }
                            }
                        }
                            
                    }else{
                        $response_arr['insuffecient_funds'] = true;
                    }
           
                }
                           
            }
        }

            
           
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadRouterBundlesAndVerifyNumber(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
            
        $response_arr = array('success' => false,'router_plans' => '','incorrect_number' => false,'customer_name' => '','productCode' => '','platform' => '');

        $validationRules = [
            'router_service' => 'required|in:smile',
            'router_number' => 'required|numeric',
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            $type = $post_data->router_service;
            $router_number = $post_data->router_number;
            
            $vtu_platform = $this->main_model->getVtuPlatformToUse('router',$type);
            if($vtu_platform == "payscribe"){               
                $response_arr['platform'] = 'payscribe';
                if($type == "smile"){
                    $real_type = "smile";
                    $url = "https://www.payscribe.ng/api/v1/internet/bundles";
                }

                $use_post = true;
                $data = [
                    'type' => $real_type,
                    'account' => $router_number
                ];

                $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);


                if($this->main_model->isJson($response)){

                    $response = json_decode($response);
                    // var_dump($response);

                    if($response->status && $response->status_code == 200 && isset($response->message->details)){
                        
                        $plans = $response->message->details->bundles;
                        $customer_name = $response->message->details->customer_name;
                        $productCode = $response->message->details->productCode;
                        $response_arr['customer_name'] = $customer_name;
                        $response_arr['productCode'] = $productCode;

                        if(is_array($plans)){
                            $response_arr['type'] = $real_type;
                            $response_arr['success'] = true;
                            
                            $index = 0;
                            $router_plans = array();

                            for($i = 0; $i < count($plans); $i++){
                                
                                $index++;
                                $name = $plans[$i]->name;
                                $code = $plans[$i]->code;
                                $amount = $plans[$i]->amount;
                                $validity = $plans[$i]->validity;
                                $router_plans[] = array(
                                    'index' => $index,
                                    'name' => $name,
                                    'code' => $code,
                                    'amount' => $amount,
                                    'validity' => $validity
                                );

                            }

                            $response_arr['router_plans'] = $router_plans;
                        }
                    
                    }
                    // else if($response->description == "There was an error validating SMILE please try again later."){
                    else{
                        $response_arr['incorrect_number'] = true;
                    
                    }
                }
            }else{               
                $response_arr['platform'] = 'club';
                if($type == "smile"){
                    $real_type = "smile";
                    $url = "https://www.nellobytesystems.com/APIVerifySmileV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&MobileNetwork=smile-direct&MobileNumber=".$router_number;
                }

                $use_post = true;
                $data = [
                    'type' => $real_type,
                    'account' => $router_number
                ];

                $use_post = true;

                    
                $response = $this->main_model->vtu_curl($url,$use_post,$post_data=[]);

                // return $response;
                if($this->main_model->isJson($response)){
                    $response = json_decode($response);
                     // var_dump($response);
                    if(is_object($response)){
                        if(isset($response->content->Customer_Name)){
                            if($response->content->Customer_Name != "" && $response->content->Customer_Name != "INVALID_SMARTCARDNO"){                        
                                
                                $customer_name = $response->content->Customer_Name;
                                
                                $response_arr['customer_name'] = $customer_name;
                                

                                // if(is_array($plans)){
                                //     $response_arr['type'] = $real_type;
                                //     $response_arr['success'] = true;
                                    
                                //     $index = 0;
                                //     $router_plans = array();

                                //     for($i = 0; $i < count($plans); $i++){
                                        
                                //         $index++;
                                //         $name = $plans[$i]->name;
                                //         $code = $plans[$i]->code;
                                //         $amount = $plans[$i]->amount;
                                //         $validity = $plans[$i]->validity;
                                //         $router_plans[] = array(
                                //             'index' => $index,
                                //             'name' => $name,
                                //             'code' => $code,
                                //             'amount' => $amount,
                                //             'validity' => $validity
                                //         );

                                //     }

                                //     $response_arr['router_plans'] = $router_plans;
                                // }

                                $url = "https://www.nellobytesystems.com/APISmilePackagesV2.asp";
                        //                  // echo $url;
                                $use_post = true;

                                $response = $this->main_model->vtu_curl($url,$use_post,$post_data=[]);

                                if($this->main_model->isJson($response)){
                                    $response = json_decode($response);
                                    // var_dump($response);
                                    if(is_object($response)){
                                        // return $response->MOBILE_NETWORK->{'smile-direct'}[0]->PRODUCT;
                                        if(isset($response->MOBILE_NETWORK->{'smile-direct'}[0]->PRODUCT)){
                                            $response_arr['success'] = true;
                                            $index = 0;
                                            $new_arr = array();
                                            

                                            $rows = $response->MOBILE_NETWORK->{'smile-direct'}[0]->PRODUCT;
                                            for($i = 0; $i < count($rows); $i++){
                                                $index++;

                                                $package_id = $rows[$i]->PACKAGE_ID;
                                                $package_name = $rows[$i]->PACKAGE_NAME;
                                                $package_amount = $rows[$i]->PACKAGE_AMOUNT;



                                                $new_arr[$i]['index'] = $index;
                                                $new_arr[$i]['package_id'] = $package_id;
                                                $new_arr[$i]['name'] = $package_name;
                                                $new_arr[$i]['amount'] = number_format($package_amount);
                                                $new_arr[$i]['type'] = $type;
                                                
                                            }

                                            $response_arr['router_plans'] = $new_arr;
                                                    
                                        }
                                    }
                                }
                            }else{
                                $response_arr['incorrect_number'] = true;
                            }
                        }else{
                            $response_arr['incorrect_number'] = true;
                        }
                    }
                }
            }
            
                        
        }
            
           
        $response_arr = json_encode($response_arr);
        // return $response_arr;
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function purchaseCableTvPlan(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'insuffecient_funds' => false,'order_id' => '','invalid_no' => false,'error_message' => '','transaction_pending' => false);

        
            // return $vtu_platform;
        if(isset($post_data->operator) && isset($post_data->decoder_number) && isset($post_data->selected_plan)){
            $operator = $post_data->operator;
            $decoder_number = $post_data->decoder_number;
            $selected_plan = $post_data->selected_plan;

            if($operator == "dstv" || $operator == "gotv" || $operator == "startimes"){
                $vtu_platform = $this->main_model->getVtuPlatformToUse('cable',$operator);
                if($vtu_platform == "payscribe"){
                
                    if(isset($post_data->productCode) && isset($post_data->productToken)){
                                    
                        
                        $productCode = $post_data->productCode;
                        $productToken = $post_data->productToken;
                        $phone = "0" . $this->data['phone'];
                        


                        
                        if($operator == "dstv" || $operator == "gotv"){
                            $plan = $selected_plan['package_id'];
                            $url = "https://www.payscribe.ng/api/v1/multichoice/validate";
                            $use_post = true;
                            $data = array(
                                'type' => $operator,
                                'account' => $decoder_number
                            );
                            $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);

                            if($this->main_model->isJson($response)){
                                $response = json_decode($response);

                                if($response->status == true && $response->status_code == 200){
                                    if(isset($response->message->details->customer_name)){
                                        $customer_name = $response->message->details->customer_name;
                                        $bouquets = $response->message->details->bouquets;
                                        // $productCode = $response->message->details->productCode;
                                        if(is_array($bouquets)){

                                            
                                            for($i = 0; $i < count($bouquets); $i++){
                                                $package_id = $bouquets[$i]->plan;
                                                $package_name = $bouquets[$i]->name;
                                                $package_amount = $bouquets[$i]->amount;

                                                if($package_id == $plan){
                                                    $amount_to_debit_user = $package_amount;
                                                    $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                                                    // echo $user_total_amount;
                                                    // echo $amount;

                                                    if($amount_to_debit_user <= $user_total_amount){
                                                        
                                                        $url = "https://www.payscribe.ng/api/v1/multichoice/vend";
                                                        $use_post = true;
                                                        
                                                        // $transaction_id = "PS" . mt_rand(10000000, 99999999);

                                                        $data = array(
                                                            'plan' => $plan,
                                                            'productCode' => $productCode,
                                                            'phone' => $phone,
                                                            'productToken' => $productToken
                                                        );
                                                        

                                                        $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);

                                                        if($this->main_model->isJson($response)){
                                                            $response = json_decode($response);
                                                            // var_dump($response);
                                                            if(is_object($response)){

                                                                $status = $response->status;
                                                                $status_code = $response->status_code;

                                                                if($status == true && $status_code == 200){
                                                                    $summary = "Debit Of " . $amount_to_debit_user . " For CableTV Recharge";
                                                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                                    
                                                                        $order_id = $response->message->details->trans_id;
                                                                        $form_array = array(
                                                                            'user_id' => $user_id,
                                                                            'type' => 'cable',
                                                                            'sub_type' => $operator,
                                                                            'number' => $decoder_number,
                                                                            'date' => $date,
                                                                            'time' => $time,
                                                                            'amount' => $amount_to_debit_user,
                                                                            'order_id' => $order_id
                                                                        );
                                                                    
                                                                        if($this->main_model->addTransactionStatus($form_array)){
                                                                            $response_arr['success'] = true;
                                                                            $response_arr['order_id'] = $order_id;
                                                                        }
                                                                    }   
                                                                }else if($status == true && $status_code == 201){
                                                                    $response_arr['transaction_pending'] = true;
                                                                    $summary = "Debit Of " . $amount_to_debit_user . " For CableTV Recharge";
                                                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                                    
                                                                        $order_id = $response->message->details->trans_id;
                                                                        $form_array = array(
                                                                            'user_id' => $user_id,
                                                                            'type' => 'cable',
                                                                            'sub_type' => $operator,
                                                                            'number' => $decoder_number,
                                                                            'date' => $date,
                                                                            'time' => $time,
                                                                            'amount' => $amount_to_debit_user,
                                                                            'order_id' => $order_id
                                                                        );
                                                                    
                                                                        if($this->main_model->addTransactionStatusOnly($form_array)){
                                                                            $response_arr['success'] = true;
                                                                            $response_arr['order_id'] = $order_id;
                                                                        }
                                                                    }   
                                                                }else{
                                                                    if(isset($response->message->description)){
                                                                        if($response->message->description != ""){
                                                                            $response_arr['error_message'] = $response->message->description;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        
                                                    }else{
                                                        $response_arr['insuffecient_funds'] = true;
                                                    }
                                                }
                                            }
                                        }
                                    }else{
                                        $response_arr['invalid_no'] = true;
                                    }
                                }
                            }       
                        }else if($operator == "startimes"){
                            
                            // echo "string";
                            
                            if(isset($selected_plan['cycle'])){
                                $cycle = $selected_plan['cycle'];
                                $package_name = $selected_plan['package_name'];
                                $amount = $selected_plan['amount'];

                                
                                $url = "https://www.payscribe.ng/api/v1/startimes/validate";
                                $use_post = true;
                                $post_data = array(
                                    'account' => $decoder_number
                                );
                                $response = $this->main_model->payscribeVtuCurl($url,$use_post,$post_data);
                                // var_dump($response);
                                if($this->main_model->isJson($response)){
                                    $response = json_decode($response);

                                    if($response->status == true && $response->status_code == 200){
                                        if(isset($response->message->details->customer_name)){
                                            $customer_name = $response->message->details->customer_name;
                                            $bouquets = $response->message->details->bouquets;
                                            // $productCode = $response->message->details->productCode;

                                            $amount_to_debit_user = $amount;
                                            $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                                            // echo $user_total_amount;
                                            // echo $amount;

                                            if($amount_to_debit_user <= $user_total_amount){
                                                
                                                $url = "https://www.payscribe.ng/api/v1/startimes/vend";
                                                $use_post = true;
                                                
                                                // $reference_id = "PS" . mt_rand(10000000, 99999999);

                                                $post_data = array(
                                                    'plan' => $package_name,
                                                    'cycle' => $cycle,
                                                    'productCode' => $productCode,
                                                    'phone' => $phone,
                                                    'productToken' => $productToken
                                                );
                                                
                                                // echo json_encode($post_data);

                                                $response = $this->main_model->payscribeVtuCurl($url,$use_post,$post_data);
                                                // var_dump($response);

                                                if($this->main_model->isJson($response)){
                                                    $response = json_decode($response);
                                                    // var_dump($response);
                                                    if(is_object($response)){
                                                        $status = $response->status;
                                                        $status_code = $response->status_code;

                                                        if($status == true && $status_code == 200){
                                                            $summary = "Debit Of " . $amount_to_debit_user . " For CableTV Recharge";
                                                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                            
                                                                $order_id = $response->message->details->trans_id;
                                                                $form_array = array(
                                                                    'user_id' => $user_id,
                                                                    'type' => 'cable',
                                                                    'sub_type' => $operator,
                                                                    'number' => $decoder_number,
                                                                    'date' => $date,
                                                                    'time' => $time,
                                                                    'amount' => $amount_to_debit_user,
                                                                    'order_id' => $order_id
                                                                );
                                                            
                                                                if($this->main_model->addTransactionStatus($form_array)){
                                                                    $response_arr['success'] = true;
                                                                    $response_arr['order_id'] = $order_id;
                                                                }
                                                            }
                                                        }else if($status == true && $status_code == 201){
                                                            $response_arr['transaction_pending'] = true;
                                                            $summary = "Debit Of " . $amount_to_debit_user . " For CableTV Recharge";
                                                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                            
                                                                $order_id = $response->message->details->trans_id;
                                                                $form_array = array(
                                                                    'user_id' => $user_id,
                                                                    'type' => 'cable',
                                                                    'sub_type' => $operator,
                                                                    'number' => $decoder_number,
                                                                    'date' => $date,
                                                                    'time' => $time,
                                                                    'amount' => $amount_to_debit_user,
                                                                    'order_id' => $order_id
                                                                );
                                                            
                                                                if($this->main_model->addTransactionStatusOnly($form_array)){
                                                                    $response_arr['success'] = true;
                                                                    $response_arr['order_id'] = $order_id;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                
                                            }else{
                                                $response_arr['insuffecient_funds'] = true;
                                            }
                                        }else{
                                            $response_arr['invalid_no'] = true;
                                        }
                                    }
                                }
                            }
                                    
                        }

                        
                    }
                }else{
                    
                    if($operator == "dstv"){
                        $cable_type = "01";
                        $club_type = "DStv";
                    }else if($operator == "gotv"){
                        $cable_type = "02";
                        $club_type = "GOtv";
                    }else if($operator == "startimes"){
                        $cable_type = "03";
                        $club_type = "Startimes";
                    }
                    $phone = "0" . $this->data['phone'];
                        


                        
                       
                    $plan = $selected_plan['package_id'];
                    $url = "https://www.nellobytesystems.com/APIVerifyCableTVV1.0.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&CableTV=".$cable_type."&SmartCardNo=".$decoder_number;
                    // return $url;
                    $use_post = true;

                    
                    $response = $this->main_model->vtu_curl($url,$use_post,$post_data=[]);

                    // return $response;
                    if($this->main_model->isJson($response)){
                        $response = json_decode($response);
                         // var_dump($response);
                        if(is_object($response)){
                            if(isset($response->customer_name)){
                                if($response->customer_name != "" && $response->customer_name != "INVALID_SMARTCARDNO"){
                                   

                                    $package_amount = $this->main_model->getPackageAmountForCableTvClub($club_type,$plan);
                                    // return $package_amount;
                                    // echo is_numeric($package_amount);
                                    if(is_numeric($package_amount)){

                                        $amount = $package_amount;

                                        $amount_to_debit_user = $amount;
                                        $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                                        // echo $user_total_amount;

                                        // echo $amount;

                                        if($amount_to_debit_user <= $user_total_amount){

                                            $url = "https://www.nellobytesystems.com/APICableTVV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&CableTV=".$operator."&Package=".$plan."&SmartCardNo=".$decoder_number;
                                            // return $url;
                                            $use_post = true;
                                            

                                            $response = $this->main_model->vtu_curl($url,$use_post,$post_data=[]);
                                            // echo $response;
                                            if($this->main_model->isJson($response)){
                                                $response = json_decode($response);
                                                
                                                if(is_object($response)){
                                                    if($response->status == "ORDER_RECEIVED"){
                                                        $summary = "Debit Of " . $amount_to_debit_user . " For CableTV Recharge";
                                                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                            $order_id = $response->transactionid;

                                                            $form_array = array(
                                                                'user_id' => $user_id,
                                                                'type' => 'cable',
                                                                'sub_type' => $operator,
                                                                'number' => $decoder_number,
                                                                'date' => $date,
                                                                'time' => $time,
                                                                'amount' => $amount_to_debit_user,
                                                                'order_id' => $order_id
                                                            );
                                                        
                                                            if($this->main_model->addTransactionStatus($form_array)){
                                                                $response_arr['success'] = true;
                                                                $response_arr['order_id'] = $order_id;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                            
                                    }else{
                                        $response_arr['insuffecient_funds'] = true;
                                    }
 
                                }
                            }else{
                                $response_arr['invalid_no'] = true;
                            }
                        }
                    }       
                        
                    
                }
            }
        }

        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function validateDecoderNumberCablePlans(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'messages' => '','customer_name' => '','invalid_user' => false,'cable_plans' => '','platform' => '');

        $validationRules = [
            'operator' => 'required|in:dstv,gotv,startimes',
            'decoder_number' => 'required|numeric|digits_between:5,15'
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            $operator = $post_data->operator;
            $decoder_number = $post_data->decoder_number;

            $vtu_platform = $this->main_model->getVtuPlatformToUse('cable',$operator);
            // return $vtu_platform;

            if($vtu_platform == "payscribe"){
                $response_arr['platform'] = "payscribe";

                if($operator == "dstv" || $operator == "gotv"){
                    if($operator == "dstv"){
                        $type = "dstv";
                    }else if($operator == "gotv"){
                        $type = "gotv";
                    }

                    

                    $url = "https://www.payscribe.ng/api/v1/multichoice/validate";
                    $use_post = true;
                    $data = array(
                        'type' => $type,
                        'account' => $decoder_number
                    );
                    $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);

                    if($this->main_model->isJson($response)){
                        $response = json_decode($response);
                        if($response->status == true && $response->status_code == 200){
                            if(isset($response->message->details->customer_name)){
                                $customer_name = $response->message->details->customer_name;
                                $bouquets = $response->message->details->bouquets;
                                $productCode = $response->message->details->productCode;
                                $productToken = $response->message->details->productToken;
                                if(is_array($bouquets)){
                                    $response_arr['success'] = true;
                                    $response_arr['customer_name'] = $customer_name;
                                    $response_arr['productCode'] = $productCode;
                                    $response_arr['productToken'] = $productToken;

                                    
                                    
                                    $index = 0;
                                    $new_arr = array();
                                   

                                    
                                    for($i = 0; $i < count($bouquets); $i++){
                                        $index++;

                                        $package_id = $bouquets[$i]->plan;
                                        $package_name = $bouquets[$i]->name;
                                        $package_amount = $bouquets[$i]->amount;

                                        $new_arr[$i]['index'] = $index;
                                        $new_arr[$i]['package_id'] = $package_id;
                                        $new_arr[$i]['name'] = $package_name;
                                        $new_arr[$i]['amount'] = number_format($package_amount);
                                        $new_arr[$i]['type'] = $type;
                                        

                                    }

                                    $response_arr['cable_plans'] = $new_arr;

                                }
                            }else if(isset($response->message->description)){
                                $response_arr['invalid_user'] = true;
                            }
                        }
                    }   

                }else if($operator == "startimes"){
                    $response_arr = array('success' => false,'messages' => '','customer_name' => '','invalid_user' => false);
                    $type = "STARTIMES";
                    
                    $url = "https://www.payscribe.ng/api/v1/startimes/validate";
                    $use_post = true;
                    $data = array(
                        'account' => $decoder_number
                    );
                    $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);

                    if($this->main_model->isJson($response)){
                        $response = json_decode($response);
                        if($response->status == true && $response->status_code == 200){
                            if(isset($response->message->details->customer_name)){
                                $customer_name = $response->message->details->customer_name;
                                $bouquets = $response->message->details->bouquets;
                                $productCode = $response->message->details->productCode;
                                $productToken = $response->message->details->productToken;
                                if(is_array($bouquets)){
                                    $response_arr['success'] = true;
                                    $response_arr['customer_name'] = $customer_name;
                                    $response_arr['productCode'] = $productCode;
                                    $response_arr['productToken'] = $productToken;

                                    $index = 0;
                                        
                                    for($i = 0; $i < count($bouquets); $i++){
                                        $index++;

                                        
                                        $package_name = $bouquets[$i]->name;
                                        $cycles = $bouquets[$i]->cycles;
                                        $bouquets[$i]->index = $index;
                                        $bouquets[$i]->package_id = $package_name;
                                    }
                                    $response_arr['cable_plans'] = $bouquets;
                                }
                            }else if(isset($response->message->description)){
                                $response_arr['invalid_user'] = true;
                            }
                        }
                    }   
                }
            }else{
                $response_arr['platform'] = "club";
                if($operator == "dstv" || $operator == "gotv" || $operator == "startimes"){
                    
                    if($operator == "dstv"){
                        $cable_type = "01";
                        $club_type = "DStv";
                    }else if($operator == "gotv"){
                        $cable_type = "02";
                        $club_type = "GOtv";
                    }else if($operator == "startimes"){
                        $cable_type = "03";
                        $club_type = "Startimes";
                    }

                    $url = "https://www.nellobytesystems.com/APIVerifyCableTVV1.0.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&CableTV=".$cable_type."&SmartCardNo=".$decoder_number;
                     // echo $url;
                    $use_post = true;

                    
                    $response = $this->main_model->vtu_curl($url,$use_post,$post_data=[]);

                    // return $response;
                    if($this->main_model->isJson($response)){
                        $response = json_decode($response);
                         // var_dump($response);
                        if(is_object($response)){
                            if(isset($response->customer_name)){
                                if($response->customer_name != "" && $response->customer_name != "INVALID_SMARTCARDNO"){
                                    
                                    $response_arr['customer_name'] = $response->customer_name;

                                    $url = "https://www.nellobytesystems.com/APICableTVPackagesV2.asp";
                        //                  // echo $url;
                                    $use_post = true;

                                    $response = $this->main_model->vtu_curl($url,$use_post,$post_data=[]);

                                    if($this->main_model->isJson($response)){
                                        $response = json_decode($response);
                                        // var_dump($response);
                                        if(is_object($response)){
                                            if(isset($response->TV_ID->$club_type)){
                                                $response_arr['success'] = true;
                                                $index = 0;
                                                $new_arr = array();
                                                

                                                $rows = $response->TV_ID->$club_type[0]->PRODUCT;
                                                for($i = 0; $i < count($rows); $i++){
                                                    $index++;

                                                    $package_id = $rows[$i]->PACKAGE_ID;
                                                    $package_name = $rows[$i]->PACKAGE_NAME;
                                                    $package_amount = $rows[$i]->PACKAGE_AMOUNT;



                                                    $new_arr[$i]['index'] = $index;
                                                    $new_arr[$i]['package_id'] = $package_id;
                                                    $new_arr[$i]['name'] = $package_name;
                                                    $new_arr[$i]['amount'] = number_format($package_amount);
                                                    $new_arr[$i]['type'] = $operator;
                                                    
                                                }

                                                $response_arr['cable_plans'] = $new_arr;
                                                        
                                            }
                                        }
                                    }
                                }else{
                                    $response_arr['invalid_user'] = true;
                                }
                            }
                        }
                    }
                }

            }
        }
        
        // return $response_arr;
        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function processEditProfile(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        

        $response_arr = array('success' => false,'messages' => "",'incorrect_transaction_password' => true);

        if(isset($post_data->transaction_password)){
            $transaction_password = $post_data->transaction_password;

            $hashed_transaction_password = sha1($transaction_password);

        
            if($hashed_transaction_password == $this->data['transaction_password']){
                $response_arr['incorrect_transaction_password'] = false;
                $validationRules = [
                    'phone_number' => ['required', new PhoneNumberEditRule($user_id)],
                    'email_address' => ['required','email:rfc,dns,strict,spoof,filter','between:7,50',new EmailEditRule($user_id)],
                    
                    'full_name' => 'required|between:5,100',
                    'address' => 'required|between:15,1000'
                ];

                $validation = Support_Request::validate($validationRules);
            
                if($validation){
                    
                    
                    $phone = $post_data->phone_number;
                    $email = $post_data->email_address;
                    $full_name = $post_data->full_name;
                    $address = $post_data->address;
                    
                    //Check If Mobile Number Changed
                        
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                    if($ip_address == "::1"){
                        $ip_address = "197.211.60.81";
                    }
            
                    
                   
                    $calling_code = "234";                                  
                    $country_id = 151;
                    
                
                    $form_array = array(
                        'phone' => $phone,
                        'address' => $address,
                        'phone_code' => $calling_code,
                        'country_id' => $country_id,
                        'email' => $email,
                        'full_name' => $full_name
                    );
                    if($this->main_model->updateUserTable($form_array,$user_id)){

                        $response_arr['success'] = true;
                    }
                        
                }
            }
        }
            

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadEditProfilePage (Request $req){

        
        // $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        

        
        $props['page_title'] = $this->main_model->custom_echo('Edit Your Profile',30);
        
        
        

        return Inertia::render('EditProfile',$props);
        
        
    }

    public function loadAirtimeToWalletRecordsPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'airtime_to_wallet_records';
        $props['page_title'] = $this->main_model->custom_echo('Airtime To Wallet Records',30);

        $props['user_name'] = $req->query('user_name');
        $props['amount_requested'] = $req->query('amount_requested');
        $props['amount_credited'] = $req->query('amount_credited');
        $props['admin_amount'] = $req->query('admin_amount');
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');


        if(empty($props['user_name'])){
            $props['user_name'] = "";
        }

        if(empty($props['amount_requested'])){
            $props['amount_requested'] = "";
        }

        if(empty($props['amount_credited'])){
            $props['amount_credited'] = "";
        }

        if(empty($props['admin_amount'])){
            $props['admin_amount'] = "";
        }



        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_requests = $this->main_model->getAirtimeToWalletRecordsPagination($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_requests)){
            $j = 0;
            foreach($all_requests as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $user_id = $row->user_id;
                $user_name = $row->user_name;
                
                $date = $row->date;
                
                

                // $row->full_name = $this->main_model->getUserFullNameByUserId($user_id);
                // $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);


        
                
                $row->index = $index;                           
            }
        }


        // $all_requests['links'] = $all_requests->links('pagination::bootstrap-4');
        // echo $all_requests->links();
        $props['all_requests'] = $all_requests;
        $props['length'] = $length;

        return Inertia::render('Admin/AirtimeToWalletRecords',$props);
        
    }

    public function changeFrontPageMessage(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
        
        $response_arr = array('success' => false);
        
        if(isset($post_data->text)){
            
            $text = $post_data->text;
                
                
                    
            
            $form_array = array(
                'text' => $text
            );

            if($this->main_model->updateFrontPageMessageTable($form_array)){
                $response_arr['success'] = true;
            }
                    
                
        }
      
           
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadFrontPageMessagePage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'front_page_message';
        $props['page_title'] = $this->main_model->custom_echo('FRONT PAGE MESSAGE',30);
        $props['front_page_text'] = $this->main_model->getFrontPageText();
        
        
        return Inertia::render('Admin/FrontPageMessage',$props);
        
    }

    public function checkIfEducationalVoucherAvailability(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
        $response_arr = array('success' => false);
        
        
        $type = $post_data->voucher_type;
        
        if($this->main_model->checkIfEducationalVoucherIsAvailable($type)){
            $response_arr['success'] = true;
        }
                            
        
           
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadNotificationPage (Request $req,$notif_id){
        $user_id = $this->data['user_id'];

        if($this->main_model->checkIfNotifIsForThisUser($notif_id,$user_id)){
        
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $notif_details = $this->main_model->getNotificationsDetails($notif_id);
            if(is_object($notif_details)){
                foreach($notif_details as $row){
                
                   

                    $id = $row->id;
                    $sender = $row->sender;
                    $receiver = $row->receiver;
                    $title = $row->title;
                    $message = $row->message;
                    $date_sent = $row->date_sent;
                    $time_sent = $row->time_sent;
                    $received = $row->received;
                    $action_taken = $row->action_taken;
                    $btn1 = $row->btn_1;
                    $btn2 = $row->btn_2;
                    $btn3 = $row->btn_3;

                    $this->main_model->markNotifAsRead($notif_id,$user_id);
                    $date = date("j M Y");
                    $time = date("h:i:sa");
                    $form_array = array(
                        'date_received' => $date,
                        'time_received' => $time,
                        'received' => 1
                    );
                    if($this->main_model->updateNotif($form_array,$notif_id)){

                        $props['active_page'] = 'notifications';
                        $props['page_title'] = $title;

                        $props['notif_info'] = $notif_details[0];
                    
                    

                        return Inertia::render('NotificationPage',$props);
                    }else{
                        abort(404);
                    }
                }
            }else{
                abort(404);
            }
        }else{
            abort(404);
        }
        
    }

    public function loadNotificationsPage (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'notifications';
        $props['page_title'] = $this->main_model->custom_echo("Notifications",30);

        $length = 10;
        
        $all_notifications = $this->main_model->getAllNotificationsForUser($user_id);

        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_notifications)){
            $j = 0;
            foreach($all_notifications as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $sender_id = $row->sender;
                $sender = $this->main_model->getUserNameById($sender_id);
                $notif_id = $row->id;
                $post_id = $row->post_id;
                $notif_title = $row->title;
                $received = $row->received;
                $date_sent = $row->date_sent;
                $time_sent = $row->time_sent;
                $received = $row->received;
                $type = $row->type;
                $site_url = "";
                
                $site_url .= "/notification/".$id;

                $row->notif_title = $this->main_model->custom_echo($notif_title,80);
                $row->format_time = $this->main_model->getSocialMediaTime($date_sent,$time_sent);

                
                $logo = $this->main_model->getUserParamById('logo',$sender_id);
                if(is_null($logo)){
                    $logo = '/images/avatar.jpg';
                }else{
                    $logo = '/storage/images/'. $logo;
                }

                $path = public_path($logo);
    
                if(!file_exists($path)){
                  $row->logo = $logo;
                } else {
                  $row->logo = '/images/avatar.jpg';
               }
                

                $site_url = "/mark_notif_as_read?callback_url=".$site_url."&id=".$id;
                $row->index = $index;                           
            }
        }



        $props['all_notifications'] = $all_notifications;
        $props['length'] = $length;

        
        

        return Inertia::render('NotificationsPage',$props);
        
    }

    public function loadUserProfilePage (Request $req,$user_slug){

        if($this->main_model->checkIfSlugIsValid($user_slug)){
            $user_id = $this->main_model->getUserIdBySlug($user_slug);
            $user_name = $this->main_model->getUserNameById($user_id);
            
            
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            
            
            $props['active_page'] = 'user_profile';
            $props['page_title'] = $this->main_model->custom_echo($user_name . "'s Profile Page",30);

            $logo = $this->main_model->getUserParamById('logo',$user_id);
            if(is_null($logo)){
                $logo = '/images/avatar.jpg';
            }else{
                $logo = '/storage/images/'. $logo;
            }

            $props['user_logo'] = $logo;
            $props['users_name'] = $user_name;

            

            return Inertia::render('UserProfilePage',$props);
        }else{
            abort(404);
        }
        
    }

    public function creditSimIncentiveEarning(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
        
        $response_arr = array('success' => true,'user_name_invalid' => false);
        
        if(isset($post_data->user_name)){
            $validationRules = [
                'amount' => 'required|numeric',
                
            ];

            $messages = [];

            $validation = Support_Request::validate($validationRules);

            
            if($validation){
                $user_name = $post_data->user_name;
                $amount = $post_data->amount;
                if($this->main_model->checkIfUserNameExists($user_name)){
                    
                    $user_id = $this->main_model->getUserIdByName($user_name);
                    $sim_activation_incentive = $this->main_model->getUserParamById("sim_activation_incentive",$user_id);
                    $sim_activation_incentive += $amount;
                    $form_array = array(
                        'sim_activation_incentive' => $sim_activation_incentive
                    );

                    if($this->main_model->updateUserTable($form_array,$user_id)){
                        $response_arr['success'] = true;
                    }
                    
                }else{
                    $response_arr['user_name_invalid'] = true;
                }
                                             
            }
        }
      
           
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function checkUsernameSimIncentive(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
        
        $response_arr = array('success' => true,'user_name_invalid' => false);
        
        $validationRules = [
            'user_name' => 'required',
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            $user_name = $post_data->user_name;
            
            if($this->main_model->checkIfUserNameExists($user_name)){
                $response_arr['success'] = true;
                $user_id = $this->main_model->getUserIdByName($user_name);
                $sim_activation_incentive = number_format($this->main_model->getUserParamById("sim_activation_incentive",$user_id),2);
                $response_arr['sim_activation_incentive'] = $sim_activation_incentive;
            }else{
                $response_arr['user_name_invalid'] = true;
            }
                                         
        }
        
      
           
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadSimActivationInitiativePage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'sim_activation_initiative';
        $props['page_title'] = $this->main_model->custom_echo('SIM ACTIVATION INCENTIVE',30);

        
        
        return Inertia::render('Admin/SimActivationInitiative',$props);
        
    }

    public function loadAdminVtuEarningsPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'view_admin_vtu_earnings';
        $props['page_title'] = $this->main_model->custom_echo('Admin Vtu Earnings',25);

        $props['month_year'] = date("M Y");

        $props['mtn_airtime_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("mtn_airtime_earnings"),2);
        $props['airtel_airtime_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("airtel_airtime_earnings"),2);
        $props['glo_airtime_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("glo_airtime_earnings"),2);
        $props['mobile_airtime_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("9mobile_airtime_earnings"),2);

        $props['mtn_data_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("mtn_data_earnings"),2);
        $props['airtel_data_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("airtel_data_earnings"),2);
        $props['glo_data_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("glo_data_earnings"),2);
        $props['mobile_data_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("9mobile_data_earnings"),2);

        $props['dstv_cable_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("dstv_cable_earnings"),2);
        $props['gotv_cable_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("gotv_cable_earnings"),2);
        $props['startimes_cable_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("startimes_cable_earnings"),2);

        $props['ikeja_electricity_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("ikeja_electricity_earnings"),2);
        $props['eko_electricity_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("eko_electricity_earnings"),2);
        $props['abuja_electricity_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("abuja_electricity_earnings"),2);
        $props['kano_electricity_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("kano_electricity_earnings"),2);
        $props['jos_electricity_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("jos_electricity_earnings"),2);
        $props['ibadan_electricity_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("ibadan_electricity_earnings"),2);
        $props['enugu_electricity_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("enugu_electricity_earnings"),2);
        $props['phc_electricity_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("phc_electricity_earnings"),2);
        $props['kaduna_electricity_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("kaduna_electricity_earnings"),2);

        $props['airtime_to_wallet_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("airtime_to_wallet_earnings"),2);
        $props['educational_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("educational_earnings"),2);
        $props['smile_router_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("smile_router_earnings"),2);
        $props['bulk_sms_earnings'] = number_format($this->main_model->getCurrentAdminVtuEarningForMonthByParam("bulk_sms_earnings"),2);
        
        return Inertia::render('Admin/AdminVtuEarnings',$props);
        
    }

    public function loadDownlineMembersPage (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'downline';
        $props['page_title'] = $this->main_model->custom_echo("Downline Members",30);

        $props['user_name'] = $req->query('user_name');
        $props['level'] = $req->query('level');
        $props['package'] = $req->query('package');
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



        if(empty($props['user_name'])){
            $props['user_name'] = "";
        }

        if(empty($props['level'])){
            $props['level'] = "";
        }

        if(empty($props['package'])){
            $props['package'] = "none";
        }
       
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getMlmDownlinePaginationByOffset($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $user_id = $row->user_id;
                $row->user_name = $this->main_model->getUserNameById($user_id);
                $row->full_name = $this->main_model->getUserParamById("full_name",$user_id);
                  
                $date_created = $row->date_created;
                $time_created = $row->time_created;
                $stage = $row->stage;
                $level = $stage;
                $under = $row->under;
                $sponsor = $row->sponsor;
                $positioning = $row->positioning;
                $package = $row->package;
                $row->level_str = number_format($level);

                if($package == 1){
                    $row->package = "Basic";
                }else{
                    $row->package = "Business";
                }
                $placement_user_id = $this->main_model->getMlmDbParamById("user_id",$under);
                $row->placement_user_slug = $this->main_model->getUserParamById("slug",$placement_user_id);
                $row->placement_user_name = $this->main_model->getUserNameById($placement_user_id);
                $row->placement_str = $row->placement_user_name. " (" . $positioning . ")";


                $sponsor_user_id = $this->main_model->getMlmDbParamById("user_id",$sponsor);
                $row->sponsor_user_slug = $this->main_model->getUserParamById("slug",$sponsor_user_id);
                $row->sponsor_user_name = $this->main_model->getUserNameById($sponsor_user_id);
                $row->index = $index;                            
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        
        
        $props['total_downline_num'] = number_format($this->main_model->getTotalNumberOfDownlineInMlmSystem());
        $props['total_levels'] = number_format($this->main_model->getNumberOfLevelsInMlmSystem());

        return Inertia::render('Admin/DownlineMembers',$props);
        
    }

    public function searchUsersGenealogy(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        
        
        if(isset($post_data->show_records) && isset($post_data->user_name)){
            $response_arr = array('success' => false,'messages' => '','invalid_user' => true);

            $user_name = $post_data->user_name;
            if($this->main_model->checkIfUserNameExists($user_name)){
                $response_arr['invalid_user'] = false;
                $user_id = $this->main_model->getUserIdByName($user_name);
                $mlm_db_id = $this->main_model->getUsersFirstMlmDbId($user_id);
                $package1 = $this->main_model->getMlmDbParamById("package",$mlm_db_id);
                
                
                if($this->main_model->checkIfMlmDbIdBelongsToUser($mlm_db_id,$user_id)){
                    $response_arr['success'] = true;

                    $response_arr['messages'] .= '<div class="tf-tree example">';

                    $level = 20;
                    $parentID = $mlm_db_id;
                    $stage = 3;

                    // $left_num = $this->meetglobal_model->getTotalNoOfMlmAccountsUnderUserLeft($mlm_db_id);

                    $user_id = $this->main_model->getMlmDbParamById("user_id",$parentID);
                    $logo = $this->main_model->getUserParamById("logo",$user_id);
                    $user_name = $this->main_model->getUserParamById("user_name",$user_id);
                    $full_name = $this->main_model->getUserParamById("full_name",$user_id);
                    $package = $this->main_model->getMlmDbParamById("package",$parentID);
                    $date_created = $this->main_model->getMlmDbParamById("date_created",$parentID);
                    $index = $this->main_model->getMlmIdsIndexNumber($mlm_db_id);
                    $full_phone_number = $this->main_model->getFullMobileNoByUserName($user_name);
                    if(is_null($logo)){
                        $logo = '/images/nophoto.jpg';
                    }else{
                        $logo = '/storage/images/'. $logo;
                    }

                    if($package == 1){
                        $package = "basic";
                    }else{
                        $package = "business";
                    }
                    
                    $response_arr['messages'] .= '<ul>';
                    $response_arr['messages'] .= '<li>';
                    $response_arr['messages'] .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';



                    $response_arr['messages'] .= '<img class="tree_icon" src="'.$logo.'">';
                    $response_arr['messages'] .= '<p class="demo_name_style">&nbsp;';
                    $response_arr['messages'] .= $user_name . "  ";

                    
                      
                    $response_arr['messages'] .= '<i onclick="goDownMlm(this,event,'.$mlm_db_id.','.$mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-down" style="cursor:pointer;"></i>';
                    

                    $response_arr['messages'] .= '</p>';

                    $response_arr['messages'] .= '</div>';


                                        
                    
                    $response_arr['messages'] .= $this->main_model->printTree($package1,$mlm_db_id,$level, $parentID,$stage);
                    $response_arr['messages'] .= '</li>';
                    $response_arr['messages'] .= '</ul>';

                    $response_arr['messages'] .= '</div>';
                }
            }
            return json_encode($response_arr);
        }
       
    } 

    public function loadSponsorTreePage (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'sponsor';
        $props['page_title'] = $this->main_model->custom_echo("Sponsor Tree",30);

        $mlm_db_id = $this->main_model->getUsersFirstMlmDbId($user_id);
        $package1 = $this->main_model->getMlmDbParamById('package',$mlm_db_id);
        $downline_html = "";
              
        if($this->main_model->checkIfMlmDbIdBelongsToUser($mlm_db_id,$user_id)){
                

            $downline_html .= '<div class="tf-tree example">';

            $level = 20;
            $parentID = $mlm_db_id;
            $stage = 1;

            // $left_num = $this->meetglobal_model->getTotalNoOfMlmAccountsUnderUserLeft($mlm_db_id);

            $user_id = $this->main_model->getMlmDbParamById("user_id",$parentID);
            $logo = $this->main_model->getUserParamById("logo",$user_id);
            $user_name = $this->main_model->getUserParamById("user_name",$user_id);
            $full_name = $this->main_model->getUserParamById("full_name",$user_id);
            $package = $this->main_model->getMlmDbParamById("package",$parentID);
            $date_created = $this->main_model->getMlmDbParamById("date_created",$parentID);
            $index = $this->main_model->getMlmIdsIndexNumber($mlm_db_id);
            $full_phone_number = $this->main_model->getFullMobileNoByUserName($user_name);
            if(is_null($logo)){
                $logo = '/images/nophoto.jpg';
            }else{
                $logo = '/storage/images/'. $logo;
            }

            if($package == 1){
              $package = "basic";
            }else{
              $package = "business";
            }
                
            $downline_html .= '<ul>';
            $downline_html .= '<li>';
            $downline_html .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';


            $downline_html .= '<img class="tree_icon" src="'.$logo.'">';
            $downline_html .= '<p class="demo_name_style">';
            $downline_html .= $user_name;

            

            $downline_html .= '</p>';

            $downline_html .= '</div>';



                            
                  
            $downline_html .= $this->main_model->printSponsorTree($level, $parentID,$stage);
            $downline_html .= '</li>';
            $downline_html .= '</ul>';

            $downline_html .= '</div>';
        }
              
        $props['downline_html'] = $downline_html;


        return Inertia::render('SponsorTree',$props);
        
    }

    public function viewYourGenealogyTreeDown(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        
        
        if(isset($post_data->mlm_db_id) && isset($post_data->your_mlm_db_id) && isset($post_data->package)){

            $mlm_db_id = $post_data->mlm_db_id;
            $package1 = $post_data->package;
            $your_mlm_db_id = $post_data->your_mlm_db_id;
            if($this->main_model->checkIfMlmDbIdBelongsToUser($your_mlm_db_id,$user_id)){
                $response_arr = array('success' => false,'messages' => '');
                
                
                $response_arr['success'] = true;

                $response_arr['messages'] .= '<div class="tf-tree example">';

                $level = 20;
                $parentID = $mlm_db_id;
                $stage = 3;
                $user_id = $this->main_model->getMlmDbParamById("user_id",$parentID);
                $logo = $this->main_model->getUserParamById("logo",$user_id);
                $user_name = $this->main_model->getUserParamById("user_name",$user_id);
                $full_name = $this->main_model->getUserParamById("full_name",$user_id);
                $package = $this->main_model->getMlmDbParamById("package",$parentID);
                $index = $this->main_model->getMlmIdsIndexNumber($mlm_db_id);
                $date_created = $this->main_model->getMlmDbParamById("date_created",$parentID);
                $full_phone_number = $this->main_model->getFullMobileNoByUserName($user_name);
                if(is_null($logo)){
                    $logo = '/images/nophoto.jpg';
                }else{
                    $logo = '/storage/images/'. $logo;
                }

                if($package == 1){
                    $package = "basic";
                }else{
                    $package = "business";
                }
                
                $response_arr['messages'] .= '<ul>';
                $response_arr['messages'] .= '<li>';
                $response_arr['messages'] .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';



                $response_arr['messages'] .= '<img class="tree_icon" src="'.$logo.'">';
                $response_arr['messages'] .= '<p class="demo_name_style">&nbsp;';

                $response_arr['messages'] .= '<i onclick="goUpMlm(this,event,'.$mlm_db_id.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-up" style="cursor:pointer;"></i>';

                $response_arr['messages'] .= " " . $user_name . "  ";


  
                $response_arr['messages'] .= '<i onclick="goDownMlm(this,event,'.$mlm_db_id.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-down" style="cursor:pointer;"></i>';
                

                $response_arr['messages'] .= '</p>';


                $response_arr['messages'] .= '</div>';


                                    
                
                $response_arr['messages'] .= $this->main_model->printTree($package1,$your_mlm_db_id,$level, $parentID,$stage);
                $response_arr['messages'] .= '</li>';
                $response_arr['messages'] .= '</ul>';

                $response_arr['messages'] .= '</div>';
                
                $response_arr = json_encode($response_arr);
                return $response_arr;
            }
        }
       
    } 

    public function viewYourGenealogyTree(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
        
        if(isset($post_data->show_records) && isset($post_data->mlm_db_id) && isset($post_data->package)){
            $mlm_db_id = $post_data->mlm_db_id;
            $package1 = $post_data->package;
            $response_arr = array('success' => false,'messages' => '');
                        
            if($this->main_model->checkIfMlmDbIdBelongsToUser($mlm_db_id,$user_id)){
                $response_arr['success'] = true;

                $response_arr['messages'] .= '<div class="tf-tree example">';

                $level = 20;
                $parentID = $mlm_db_id;
                $stage = 3;

                // $left_num = $this->meetglobal_model->getTotalNoOfMlmAccountsUnderUserLeft($mlm_db_id);

                $user_id = $this->main_model->getMlmDbParamById("user_id",$parentID);
                $logo = $this->main_model->getUserParamById("logo",$user_id);
                $user_name = $this->main_model->getUserParamById("user_name",$user_id);
                $full_name = $this->main_model->getUserParamById("full_name",$user_id);
                $package = $this->main_model->getMlmDbParamById("package",$parentID);
                $date_created = $this->main_model->getMlmDbParamById("date_created",$parentID);
                $index = $this->main_model->getMlmIdsIndexNumber($mlm_db_id);
                $full_phone_number = $this->main_model->getFullMobileNoByUserName($user_name);
                if(is_null($logo)){
                    $logo = '/images/nophoto.jpg';
                }else{
                    $logo = '/storage/images/'. $logo;
                }

                if($package == 1){
                    $package = "basic";
                }else{
                    $package = "business";
                }
                
                $response_arr['messages'] .= '<ul>';
                $response_arr['messages'] .= '<li>';
                $response_arr['messages'] .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';



                $response_arr['messages'] .= '<img class="tree_icon" src="'.$logo.'">';
                $response_arr['messages'] .= '<p class="demo_name_style">&nbsp;';
                $response_arr['messages'] .= $user_name . "  ";

                
                  
                $response_arr['messages'] .= '<i onclick="goDownMlm(this,event,'.$mlm_db_id.','.$mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-down" style="cursor:pointer;"></i>';
                

                $response_arr['messages'] .= '</p>';

                $response_arr['messages'] .= '</div>';


                                    
                
                $response_arr['messages'] .= $this->main_model->printTree($package1,$mlm_db_id,$level, $parentID,$stage);
                $response_arr['messages'] .= '</li>';
                $response_arr['messages'] .= '</ul>';

                $response_arr['messages'] .= '</div>';
            }
            $response_arr = json_encode($response_arr);
            return $response_arr;
        }
        
      
    }  

    public function loadGenealogyTreePage (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'genealogy';
        $props['page_title'] = $this->main_model->custom_echo("Genealogy Tree",30);

        $mlm_db_id = $this->main_model->getUsersFirstMlmDbId($user_id);
        $package1 = $this->main_model->getMlmDbParamById('package',$mlm_db_id);
        $downline_html = "";
              
        if($this->main_model->checkIfMlmDbIdBelongsToUser($mlm_db_id,$user_id)){
                

            $downline_html .= '<div class="tf-tree example">';

            $level = 20;
            $parentID = $mlm_db_id;
            $stage = 3;

            $user_id = $this->main_model->getMlmDbParamById("user_id",$parentID);
            $logo = $this->main_model->getUserParamById("logo",$user_id);
            $user_name = $this->main_model->getUserParamById("user_name",$user_id);
            $full_name = $this->main_model->getUserParamById("full_name",$user_id);
            $package = $this->main_model->getMlmDbParamById("package",$parentID);
            $date_created = $this->main_model->getMlmDbParamById("date_created",$parentID);
            $index = $this->main_model->getMlmIdsIndexNumber($mlm_db_id);
            $full_phone_number = $this->main_model->getFullMobileNoByUserName($user_name);
            if(is_null($logo)){
                $logo = '/images/nophoto.jpg';
            }else{
                $logo = '/storage/images/'. $logo;
            }

            if($package == 1){
              $package = "basic";
            }else{
              $package = "business";
            }
                
            $downline_html .= '<ul>';
            $downline_html .= '<li>';
            $downline_html .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';


            $downline_html .= '<img class="tree_icon" src="'.$logo.'">';
            $downline_html .= '<p class="demo_name_style">&nbsp;';
            $downline_html .= $user_name . "  ";

            
              
            $downline_html .= '<i onclick="goDownMlm(this,event,'.$mlm_db_id.','.$mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-down" style="cursor:pointer;"></i>';
            

            $downline_html .= '</p>';

            $downline_html .= '</div>';



                            
                  
            $downline_html .= $this->main_model->printTree($package1,$mlm_db_id,$level, $parentID,$stage);
            $downline_html .= '</li>';
            $downline_html .= '</ul>';

            $downline_html .= '</div>';
        }
        
        $props['downline_html'] = $downline_html;


        return Inertia::render('GenealogyTree',$props);
        
    }

    public function viewMlmEarningSgpsIncome (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("SGPS Income Details",30);

        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



       
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getMlmHistorySGPSIncomeByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $vat = $row->vat;
                
                $amount = $amount - (($vat / 100) * $amount);
                
                $row->amount_str = number_format($amount,2);
                
                $date = $row->date;
                $time = $row->time;
                $row->date_time = $date . " " . $time;
                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        
        $props['sgps_income'] = $this->main_model->getUsersSGPSIncome($user_id);

        return Inertia::render('SgpsIncome',$props);
        
    }

    public function viewMlmEarningTradeDelivery (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Trade Delivery Income Details",30);

        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



       
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getMlmHistoryTradeDeliveryIncomeByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $vat = $row->vat;
                
                $amount = $amount - (($vat / 100) * $amount);
                
                $row->amount_str = number_format($amount,2);
                
                $date = $row->date;
                $time = $row->time;
                $row->date_time = $date . " " . $time;
                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        
        
        $props['trade_delivery_income'] = $this->main_model->getUsersTradeDeliveryIncome($user_id);

        return Inertia::render('TradeDeliveryIncome',$props);
        
    }

    public function viewMlmEarningVendorSelection (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Vendor Trade Income Details",30);

        
        
        
        $props['vendor_selection_income'] = $this->main_model->getUserParamById("vendor_selection_income",$user_id);

        return Inertia::render('VendorTradeIncome',$props);
        
    }

    public function viewMlmEarningCenterConnectorSelection (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Center Connector Trade Income Details",30);

        
        
        $props['center_connector_selection_income'] = $this->main_model->getUserParamById("center_connector_selection_income",$user_id);

        return Inertia::render('CenterConnectorTradeIncome',$props);
        
    }

    public function viewMlmEarningCenterLeaderSelection (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Center Leader Trade Income Details",30);

        
        
        
        $props['center_leader_selection_income'] = $this->main_model->getUserParamById("center_leader_selection_income",$user_id);

        return Inertia::render('CenterLeaderTradeIncome',$props);
        
    }

    public function viewMlmEarningVendorPlacement (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Vendor Placement Income Details",30);

        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



       
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getMlmHistoryVendorPlacementByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $vat = $row->vat;
                
                $amount = $amount - (($vat / 100) * $amount);
                
                $row->amount_str = number_format($amount,2);
                
                $date = $row->date;
                $time = $row->time;
                $row->date_time = $date . " " . $time;
                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        
        
        $props['vednor_placement_bonus'] = $this->main_model->getUsersVendorPlacementBonus($user_id);

        return Inertia::render('VendorPlacementIncome',$props);
        
    }

    public function viewMlmEarningVendorSponsor (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Vendor Sponsor Income Details",30);

        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



       
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getMlmHistoryVendorSponsorByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $vat = $row->vat;
                
                $amount = $amount - (($vat / 100) * $amount);
                
                $row->amount_str = number_format($amount,2);
                
                $date = $row->date;
                $time = $row->time;
                $row->date_time = $date . " " . $time;
                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        
        $props['vendor_sponsor_bonus'] = $this->main_model->getUsersVendorSponsorBonus($user_id);

        return Inertia::render('VendorSponsorIncome',$props);
        
    }

    public function viewMlmEarningCenterConnectorPlacement (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Center Connector Placement Income Details",30);

        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



       
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getMlmHistoryCenterConnectorPlacementByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $vat = $row->vat;
                
                $amount = $amount - (($vat / 100) * $amount);
                
                $row->amount_str = number_format($amount,2);
                
                $date = $row->date;
                $time = $row->time;
                $row->date_time = $date . " " . $time;
                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        
        

        
        $props['center_connector_placement_bonus'] = $this->main_model->getUsersCenterConnectorPlacementBonus($user_id);

        return Inertia::render('CenterConnectorPlacementIncome',$props);
        
    }

    public function viewMlmEarningCenterConnectorSponsor (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Center Connector Sponsor Income Details",30);

        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



       
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getMlmHistoryCenterConnectorSponsorByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $vat = $row->vat;
                
                $amount = $amount - (($vat / 100) * $amount);
                
                $row->amount_str = number_format($amount,2);
                
                $date = $row->date;
                $time = $row->time;
                $row->date_time = $date . " " . $time;
                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        
        

        $props['center_connector_sponsor_bonus'] = $this->main_model->getUsersCenterConnectorSponsorBonus($user_id);

        return Inertia::render('CenterConnectorSponsorIncome',$props);
        
    }

    public function viewMlmEarningCenterLeaderPlacement (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Center Leader Placement Income Details",30);

        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



       
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getMlmHistoryCenterLeaderPlacementByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $vat = $row->vat;
                
                $amount = $amount - (($vat / 100) * $amount);
                
                $row->amount_str = number_format($amount,2);
                
                $date = $row->date;
                $time = $row->time;
                $row->date_time = $date . " " . $time;
                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        
        
        
        $props['center_leader_placement_bonus'] = $this->main_model->getUsersCenterLeaderPlacementBonus($user_id);

        return Inertia::render('CenterLeaderPlacementIncome',$props);
        
    }

    public function viewMlmEarningCenterLeader (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Center Leader Sponsor Income Details",30);

        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



       
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getMlmHistoryCenterLeaderSponsorByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $vat = $row->vat;
                
                $amount = $amount - (($vat / 100) * $amount);
                
                $row->amount_str = number_format($amount,2);
                
                $date = $row->date;
                $time = $row->time;
                $row->date_time = $date . " " . $time;
                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        
        
        $props['center_leader_sponsor_bonus'] = $this->main_model->getUsersCenterLeaderSponsorBonus($user_id);

        return Inertia::render('CenterLeaderSponsorIncome',$props);
        
    }

    public function viewMlmEarningBusinessPlacement (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Placement Income Details",30);

        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



       
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getMlmHistoryPlacementByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $vat = $row->vat;
                
                $amount = $amount - (($vat / 100) * $amount);
                
                $row->amount_str = number_format($amount,2);
                
                $date = $row->date;
                $time = $row->time;
                $row->date_time = $date . " " . $time;
                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        
        
        $props['business_placement_earnings'] = $this->main_model->getUsersMlmBusinessPlacementEarnings($user_id);

        return Inertia::render('BusinnessPlacementIncome',$props);
        
    }


    public function viewMlmEarningBasicPlacement (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Basic Placement Income Details",30);

        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



       
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getMlmHistoryBasicPlacementByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $vat = $row->vat;
                
                $amount = $amount - (($vat / 100) * $amount);
                
                $row->amount_str = number_format($amount,2);
                
                $date = $row->date;
                $time = $row->time;
                $row->date_time = $date . " " . $time;
                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        
        $props['basic_placement_earnings'] = $this->main_model->getUsersMlmBasicPlacementEarnings($user_id);

        return Inertia::render('BasicPlacementIncome',$props);
        
    }

    public function viewMlmEarningBusinessSponsor (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Sponsor Income Details",30);

        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



       
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getMlmHistorySponsorByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $vat = $row->vat;
                
                $amount = $amount - (($vat / 100) * $amount);
                
                $row->amount_str = number_format($amount,2);
                
                $date = $row->date;
                $time = $row->time;
                $row->date_time = $date . " " . $time;
                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        $props['business_sponsor_earnings'] = $this->main_model->getUsersMlmBusinessSponsorEarnings($user_id);

        return Inertia::render('BusinessSponsorIncome',$props);
        
    }

    public function viewMlmEarningBasicSponsor (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Basic Sponsor Income Details",30);

        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



       
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getMlmHistoryBasicSponsorByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $vat = $row->vat;
                
                $amount = $amount - (($vat / 100) * $amount);
                
                $row->amount_str = number_format($amount,2);
                
                $date = $row->date;
                $time = $row->time;
                $row->date_time = $date . " " . $time;
                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        $props['basic_sponsor_earnings'] = $this->main_model->getUsersMlmBasicSponsorEarnings($user_id);

        return Inertia::render('BasicSponsorIncome',$props);
        
    }

    public function viewMlmEarningVtuTradeIncome (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("VTU Trade Income Details",30);

        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



       
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getMlmHistoryVTUTradeIncomeByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $vat = $row->vat;
                
                $amount = $amount - (($vat / 100) * $amount);
                
                $row->amount_str = number_format($amount,2);
                
                $date = $row->date;
                $time = $row->time;
                $row->date_time = $date . " " . $time;
                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        $props['vtu_trade_income'] = $this->main_model->getUsersVtuTradeIncome($user_id);

        return Inertia::render('VtuTradeIncomeHistory',$props);
        
    }

    public function transferEarningsToMainAcct(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
        
        $response_arr = array('success' => true,'not_enough_money' => true,'amount' => 0);
        
        $validationRules = [
            'amount' => 'required|numeric',
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            $amount = $post_data->amount;
            
            if($amount > 0){
                $amount_withdrawable = $this->main_model->getTotalWithdrawableMlmIncome($user_id);

                if($amount <= $amount_withdrawable){
                    $response_arr['not_enough_money'] = true;

                    if($this->main_model->transferMoneyFromMlmAccountToMainAccount($user_id,$amount)){
                        $response_arr['success'] = true;
                        $response_arr['amount'] = $amount;
                    }
                }
            }
                                         
        }
        
      
           
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadUserEarningsPage (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'user_earnings';
        $props['page_title'] = $this->main_model->custom_echo("Earnings",30);

        $props['basic_sponsor_earnings'] = $this->main_model->getUsersMlmBasicSponsorEarnings($user_id);
        $props['business_sponsor_earnings'] = $this->main_model->getUsersMlmBusinessSponsorEarnings($user_id);
        $props['basic_placement_earnings'] = $this->main_model->getUsersMlmBasicPlacementEarnings($user_id);
        $props['business_placement_earnings'] = $this->main_model->getUsersMlmBusinessPlacementEarnings($user_id);
        $props['center_leader_sponsor_bonus'] = $this->main_model->getUsersCenterLeaderSponsorBonus($user_id);
        $props['center_leader_placement_bonus'] = $this->main_model->getUsersCenterLeaderPlacementBonus($user_id);
        $props['center_connector_sponsor_bonus'] = $this->main_model->getUsersCenterConnectorSponsorBonus($user_id);
        $props['center_connector_placement_bonus'] = $this->main_model->getUsersCenterConnectorPlacementBonus($user_id);
        $props['vendor_sponsor_bonus'] = $this->main_model->getUsersVendorSponsorBonus($user_id);
        $props['vendor_placement_bonus'] = $this->main_model->getUsersVendorPlacementBonus($user_id);
        $props['center_leader_selection_income'] = $this->main_model->getUserParamById("center_leader_selection_income",$user_id);
        $props['center_connector_selection_income'] = $this->main_model->getUserParamById("center_connector_selection_income",$user_id);
        $props['vendor_selection_income'] = $this->main_model->getUserParamById("vendor_selection_income",$user_id);
        $props['trade_delivery_income'] = $this->main_model->getUsersTradeDeliveryIncome($user_id);
        $props['vtu_trade_income'] = $this->main_model->getUsersVtuTradeIncome($user_id);
        // $props['vtu_trade_income'] = 20050.65676;
        $props['sgps_income'] = $this->main_model->getUsersSGPSIncome($user_id);
        $props['car_award_earnings'] = $this->main_model->getUsersCarAwardEarnings($user_id);
        $props['sim_activation_incentive'] = $this->main_model->getUserParamById("sim_activation_incentive",$user_id);

        $props['total_basic_earnings'] = $props['basic_sponsor_earnings'] + $props['basic_placement_earnings'];

        $props['total_business_earnings'] = $props['business_sponsor_earnings'] + $props['business_placement_earnings'] + $props['center_leader_sponsor_bonus'] + $props['center_leader_placement_bonus'] + $props['center_leader_selection_income'] + $props['center_connector_selection_income'] + $props['vendor_selection_income'] + $props['trade_delivery_income'] + $props['vtu_trade_income'] + $props['sgps_income'] + $props['center_connector_sponsor_bonus'] + $props['center_connector_placement_bonus'] + $props['vendor_sponsor_bonus'] + $props['vendor_placement_bonus'] + $props['sim_activation_incentive'];

        $props['total_business_earnings'] += $props['total_basic_earnings'];

        $props['total_withdrawable_basic_earnings'] = $props['total_basic_earnings'];
                  
        $props['total_withdrawable_business_earnings'] = $this->main_model->getTotalBusinessWithdrawableEarnings($user_id);

        $props['grand_total_withdrawable_earnings'] = $props['total_withdrawable_business_earnings'];
        $props['total_mlm_withdrawn'] = $this->main_model->getUserParamById("mlm_withdrawn",$user_id);
        $props['monthly_subscription'] = $this->main_model->getUserParamById("monthly_subscription",$user_id);
        $props['grand_total_balance'] = $props['grand_total_withdrawable_earnings'] - $props['total_mlm_withdrawn'] - $props['monthly_subscription'];

        return Inertia::render('EarningsPage',$props);
        
    }

    public function loadEcommercePage (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'ecommerce';
        $props['page_title'] = $this->main_model->custom_echo("E-Commerce",30);


        return Inertia::render('EcommercePage',$props);
        
    }

    public function loadHealthPage (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'health';
        $props['page_title'] = $this->main_model->custom_echo("Health",30);


        return Inertia::render('Health',$props);
        
    }

    public function loadSmartBusinessLoanHistoryPage (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'view_smart_business_loan_history';
        $props['page_title'] = $this->main_model->custom_echo("Smart Business Loan Hist.",30);

        $props['status'] = $req->query('status');
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



        if(empty($props['status'])){
            $props['status'] = "all";
        }

        

        
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getUsersSmartBusinessLoanHistoryByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $amount_paid = $row->amount_paid;
                $balance = $amount - $amount_paid;
                $last_date_time_paid = $row->last_date_time_paid;
              
                $user_id = $row->user_id;
                $service_charge = $row->service_charge;
                $date_time = $row->date_time;


                $row->status = "";
                if($amount == $amount_paid){
                  $row->status = "Cleared";
                }else{
                  $row->status = "Pending";
                }

                $row->full_name = $this->main_model->getUserParamById("full_name",$user_id);
                $row->user_name = $this->main_model->getUserParamById("user_name",$user_id);
                $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);


                $row->amount_str = number_format($amount,2);
                $row->amount_paid_str = number_format($amount_paid,2);
                $row->balance_str = number_format($balance,2);
                $row->service_charge_str = number_format($service_charge,2);

                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        return Inertia::render('SmartBusinessLoanHistory',$props);
        
    }

    public function loadSmartBusinessLoanPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'smart_business_loan';
        $props['csrf'] = csrf_token();
        $props['page_title'] = $this->main_model->custom_echo('Smart Business Loan',25);

        $loanable_amount = $this->main_model->getSmartBusinessLoanableAmountForUser($user_id);
        if($loanable_amount >= 2000){
            $props['show_request_loan_btn'] = true;
        }else{
            $props['show_request_loan_btn'] = false;
        }

        $props['loanable_amount'] = number_format($loanable_amount,2);
        $props['debt_amount'] = number_format($this->main_model->getSmartBusinessLoanDebtForUser($user_id),2);
        
        return Inertia::render('SmartBusinessLoan',$props);
        
    }

    public function recieveProvidusWebhooks(Request $req){
        $json_post = file_get_contents('php://input');
                
        $post = json_decode($json_post);
        $date = date("j M Y");
        $time = date("h:i:sa");
        $this->main_model->addMinifyAccountWebhookJsonData($json_post,$date,$time);

        if(isset($post->sessionId) && isset($post->accountNumber) && isset($post->tranRemarks) && isset($post->transactionAmount) && isset($post->settledAmount) && isset($post->feeAmount) && isset($post->vatAmount) && isset($post->currency) && isset($post->initiationTranRef) && isset($post->settlementId) && isset($post->sourceAccountNumber) && isset($post->sourceAccountName) && isset($post->sourceBankName) && isset($post->channelId) && isset($post->tranDateTime)){

            $sessionId = $post->sessionId;
            $accountNumber = $post->accountNumber;
            $tranRemarks = $post->tranRemarks;
            $transactionAmount = $post->transactionAmount;
            $settledAmount = $post->settledAmount;
            $feeAmount = $post->feeAmount;
            $vatAmount = $post->vatAmount;
            $currency = $post->currency;
            $initiationTranRef = $post->initiationTranRef;
            $settlementId = $post->settlementId;
            $sourceAccountNumber = $post->sourceAccountNumber;
            $sourceAccountName = $post->sourceAccountName;
            $sourceBankName = $post->sourceBankName;
            $channelId = $post->channelId;
            $tranDateTime = $post->tranDateTime;

            $response_arr = array();

            $headers = getallheaders();
            // $X_Auth_Signature = $headers['X-Patreon-Event'];
            // $X_Patreon_Signature = $headers['X-Patreon-Signature'];

            if(isset($headers['X-Auth-Signature']) && strtolower($headers['X-Auth-Signature']) == hash("sha512","RDMtTSMjdF9HMTBCQGw=:88400CB30C3DB7F1F97ED3401D53682CA8EF680C678EE027B1B9C866FB994258") && $this->main_model->checkIfThisProvidusAccountNumberIsValid($accountNumber) && !$this->main_model->checkIfThisProvidusWebhookIsDuplicate($settlementId)){
                $response_arr = array(
                    'requestSuccessful' => true,
                    'sessionId' => $sessionId,
                    'responseMessage' => 'success',
                    'responseCode' => '00'
                );

                $form_array = array(
                    'sessionId' => $sessionId,
                    'accountNumber' => $accountNumber,
                    'tranRemarks' => $tranRemarks,
                    'transactionAmount' => $transactionAmount,
                    'settledAmount' => $settledAmount,
                    'feeAmount' => $feeAmount,
                    'vatAmount' => $vatAmount,
                    'currency' => $currency,
                    'initiationTranRef' => $initiationTranRef,
                    'settlementId' => $settlementId,
                    'sourceAccountNumber' => $sourceAccountNumber,
                    'sourceAccountName' => $sourceAccountName,
                    'sourceBankName' => $sourceBankName,
                    'channelId' => $channelId,
                    'tranDateTime' => $tranDateTime
                );

                $this->main_model->addProvidusTransactionRecord($form_array);

                $amount_to_credit = 0;

                if($transactionAmount >= 1 && $transactionAmount <= 9999){
                    $amount_to_credit = $transactionAmount - 20;
                }else if($transactionAmount >= 10000){
                    $amount_to_credit = $transactionAmount - 70;
                }

                // echo $amount_to_credit;

                $user_info = $this->main_model->getUserInfoByUserProvidusAccountNumber($accountNumber);

                if(is_object($user_info)){
                    foreach($user_info as $user){
                        $created = $user->created;
                        $user_id = $user->id;
                        $user_name = $user->user_name;
                        $email = $user->email;
                        $phone = $user->phone;
                        $country_id = $user->country_id;
                        $state_id = $user->state_id;
                        $address = $user->address;
                        $user_slug = $user->slug;
                        
                        $logo = $user->logo;
                        $cover_photo = $user->cover_photo;
                        $bio = $user->bio;
                    }

                    if($created == 1){
                        $summary = "Direct Credit Of " . $amount_to_credit . " Using Instant Credit";
                        if($this->main_model->creditUser($user_id,$amount_to_credit,$summary)){
                            $form_array = array(
                                'user_id' => $user_id,
                                'amount' => $amount_to_credit,
                                'date' => $date,
                                'time' => $time,
                                'payment_option' => 'providus',
                                'reference' => $settlementId
                            );
                            if($this->main_model->addNewAccountCreditHistory($form_array)){
                                $form_array = array(
                                    'user_id' => $user_id,
                                    'amount_credited' => $amount_to_credit
                                );

                                $this->main_model->updateProvidusTransactionBySettlementId($form_array,$settlementId);
                            }
                        }
                    }else{
                        if($this->main_model->creditUsersRegistrationAmount($user_id,$amount_to_credit)){
                            
                        }
                    }
                }   



            }else if($this->main_model->checkIfThisProvidusWebhookIsDuplicate($settlementId)){
                $response_arr = array(
                    'requestSuccessful' => true,
                    'sessionId' => $sessionId,
                    'responseMessage' => 'duplicate transaction',
                    'responseCode' => '01'
                );
            }else if(!isset($headers['X-Auth-Signature']) || $headers['X-Auth-Signature'] != hash("sha512","dGVzdF9Qcm92aWR1cw==:29A492021F4B709A8D1152C3EF4D32DC5A7092723ECAC4C511781003584B48873CCBFEBDEAE89CF22ED1CB1A836213549BC6638A3B563CA7FC009BEB3BC30CF8") || !$this->main_model->checkIfThisProvidusAccountNumberIsValid($accountNumber)){
                $response_arr = array(
                    'requestSuccessful' => true,
                    'sessionId' => $sessionId,
                    'responseMessage' => 'rejected transaction',
                    'responseCode' => '02'
                );
            }
            
            echo json_encode($response_arr);

        }
    }

    public function recievePayscribeWebhooks(Request $req){
        $json_post = file_get_contents('php://input');
                
        $post = json_decode($json_post);
        // $post = (Object) $req->input();

        $date = date("j M Y");
        $time = date("h:i:sa");
        $this->main_model->addMinifyAccountWebhookJsonData($json_post,$date,$time);

        if(isset($post->status) && $post->status == true && isset($post->event_type)){

            $transaction_status = $post->transaction_status;
            $trans_id = $post->trans_id;
            $event_type = $post->event_type;
            $order_id = $trans_id;
            $amount_given = $post->amount;
            
            if($event_type == "AIRTIME_TO_CASH_TRANSFER" && $transaction_status == "approve"){

                
                $date = date("j M Y");
                $time = date("h:i:sa");

                
                if($this->main_model->checkIfTransactionIdIsValidPayscribeAirtimeToWallet($trans_id)){
                    $user_id = $this->main_model->getVtuTransactionParamByOrderId("user_id",$order_id);
                    $user_name = $this->main_model->getUserNameById($user_id);
                    $amount_requested = $this->main_model->getVtuTransactionParamByOrderId("amount",$order_id);
                    $approved_status = $this->main_model->getVtuTransactionParamByOrderId("approved",$order_id);
                    $table_id = $this->main_model->getVtuTransactionParamByOrderId("id",$order_id);
                    // echo "string";
                    if($approved_status == 0){

                        $amount_to_credit = (0.05 * $amount_requested);
                        $amount_to_credit = $amount_given - $amount_to_credit;

                        $admin_amount = (0.05 * $amount_requested);
                        
                        $summary = " Credit Of " . $amount_to_credit . " For Airtime To Wallet Transfer";
                        if($this->main_model->creditUser($user_id,$amount_to_credit,$summary)){
                            
                            $form_array = array(
                                'approved' => 1
                            );
                            if($this->main_model->updateVtuTable($form_array,$table_id)){
                                $form_array = array(
                                    'user_id' => $user_id,
                                    'user_name' => $user_name,
                                    'amount_requested' => $amount_requested,
                                    'amount_credited' => $amount_to_credit,
                                    'admin_amount' => $admin_amount,
                                    'date' => $date . " " . $time,
                                );
                                $this->main_model->addAirtimeToWalletRecord($form_array);
                            }
                        }
                        
                    }
                }
                
            }

        }
    }

    public function userVtuHistoryPage(Request $req){

        
        $user_id = $this->data['user_id'];
        $user_name = $this->data['user_name'];
        $users_user_id = $user_id;
        $users_user_name = $user_name;
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'vtu_history';
        $props['page_title'] = $this->main_model->custom_echo("VTU History",30);

        $props['users_user_id'] = $users_user_id;
        $props['users_user_name'] = $users_user_name;
        $props['type'] = $req->query('type');
        $props['sub_type'] = $req->query('sub_type');
        $props['order_id'] = $req->query('order_id');
        $props['number'] = $req->query('number');
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');


        if(empty($props['type'])){
            $props['type'] = "";
        }

        if(empty($props['sub_type'])){
            $props['sub_type'] = "";
        }

       
        if(empty($props['order_id'])){
            $props['order_id'] = "";
        }

        if(empty($props['number'])){
            $props['number'] = "";
        }


        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getVtuTransactionHistoryForThisUser($users_user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $amount = $row->amount;
                $date = $row->date;
                $time = $row->time;
                $type = $row->type;
                $sub_type = $row->sub_type;
                $order_id = $row->order_id;
                $number = $row->number;

                $row->order_id_cut = substr($order_id, 0,2);
                $row->amount_str = number_format($amount,2);
                
                $row->index = $index;                           
            }
        }

        

        $props['all_history'] = $all_history;
        $props['length'] = $length;

        return Inertia::render('VTUHistory',$props);
        
        
    }
    public function buyEminenceEducationalVoucherVtu(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
        
        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'epins' => '','invalid_amount' => false,'invalid_recipient' => false);

        $url = "https://app.eminencesub.com/api/result-checker";
        $use_post = false;
        
        $response = $this->main_model->eminenceVtuCurl($url,$use_post);
                        
        // return $data;
        // return $response;
        if($this->main_model->isJson($response)){
            $response = json_decode($response);
            // var_dump($response);
            if(isset($response->data)){
                if($response->code == 200){

                    $voucher_types = $response->data;

                    $voucher_arr = [];
                    foreach($voucher_types as $row){
                        $voucher_name = $row->name;
                        $voucher_price = $row->price;

                        $voucher_arr[] = $voucher_name;

                        if($voucher_name == $post_data->voucher_type){
                            $price = $voucher_price + 250;
                        }
                    }

                    $voucher_str = implode(",", $voucher_arr);

                    // return $voucher_str;
                    // return $price;
        
                    $validationRules = [
                        'voucher_type' => 'required|in:'.$voucher_str,
                        // 'quantity' => 'required|numeric|min:1|max:20',
                        
                    ];

                    $messages = [];

                    $validation = Support_Request::validate($validationRules);

                    
                    if($validation){
                        $type = $post_data->voucher_type;
                        $quantity = 1;
                          
                        $amount_to_debit_user = $price * $quantity;
                        // return $amount_to_debit_user;
                        
                        

                        $response_arr['amount_to_debit_user'] = $amount_to_debit_user;
                        
                        $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                        
                        
                        
                        if($amount_to_debit_user <= $user_total_amount){

                            $url = "https://app.eminencesub.com/api/buy-result-checker";
                            
                            $use_post = true;
                            $data = [
                                'type' => $type,
                                
                            ];

                            // return $data;

                            $response = $this->main_model->eminenceVtuCurl($url,$use_post,$data);
                            // $response_arr['response'] = $response;
                            // return $response;
                            if($this->main_model->isJson($response)){
                                $response = json_decode($response);
                                if(is_object($response)){
                                    $code = $response->code;
                                    $message = $response->message;

                                    if($code == 201){
                                    
                                    
                                       
                                        $summary = "Debit Of " . $amount_to_debit_user . " For Vtu ".$type." E-Pin Generation";
                                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                            // $epin = $this->meetglobal_model->generateUnusedEpinForThisNetworkAnAmount($code,$amount);
                                            $order_id = 'TC' . $response->data->reference;
                                            $pin = $response->data->pin;
                                            $form_array = array(
                                                'user_id' => $user_id,
                                                'type' => 'e-pin',
                                                'sub_type' => 'educational_voucher_epin',
                                                
                                                'date' => $date,
                                                'time' => $time,
                                                'amount' => $amount_to_debit_user,
                                                'order_id' => $order_id
                                            );
                                            if($this->main_model->addVTUTransactionStatusEducationalVoucher($form_array)){
                                                $response_arr['success'] = true;
                                                $response_arr['epins'] = $pin;
                                                
                                                
                                            }
                                        }
                                    }
                                }
                            }
                            
                            
                            
                        }else{
                            $response_arr['insuffecient_funds'] = true;
                        }
                                                     
                    }
                }
            }
        
        }
           
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function buyPayscribeEducationalVoucherVtu(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
        
        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'epins' => '','invalid_amount' => false,'invalid_recipient' => false);
        
        $validationRules = [
            'voucher_type' => 'required|in:waec,neco',
            'quantity' => 'required|numeric|min:1|max:20',
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            $type = $post_data->voucher_type;
            $quantity = $post_data->quantity;
                                
            if($type == "waec"){
              $amount_to_debit_user = 1900 * $quantity;
              $type_id = 2;
            }else{
              $amount_to_debit_user = 850 * $quantity;
              $type_id = 3;
            }
            

            $response_arr['amount_to_debit_user'] = $amount_to_debit_user;
            
            $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
            
            
            
            if($amount_to_debit_user <= $user_total_amount){

                $url = "https://www.payscribe.ng/api/v1/epins/vend";
                
                $use_post = true;
                $data = [
                    'qty' => $quantity,
                    'id' => $type_id
                ];

                $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);
                // $response_arr['response'] = $response;
                // return $response;
                if($this->main_model->isJson($response)){

                    $response = json_decode($response);
                    // echo json_encode($response);

                    if($response->status && $response->status_code == 200){
                        $order_id = $response->message->trans_id;
                        $details = $response->message->details;

                        // if(is_array($details)){
                        if(!is_array($details)){
                            $details = array();
                        } 


                            $summary = "Debit Of " . $amount_to_debit_user . " For Vtu ".$type." E-Pin Generation";
                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                // $epin = $this->meetglobal_model->generateUnusedEpinForThisNetworkAnAmount($code,$amount);
                                $form_array = array(
                                    'user_id' => $user_id,
                                    'type' => 'e-pin',
                                    'sub_type' => 'educational_voucher_epin',
                                    
                                    'date' => $date,
                                    'time' => $time,
                                    'amount' => $amount_to_debit_user,
                                    'order_id' => $order_id
                                );
                                if($this->main_model->addVTUTransactionStatusEducationalVoucher($form_array)){
                                    $response_arr['success'] = true;
                                    $response_arr['epins'] = $details;
                                    
                                    
                                }
                            }
                        // }
                    }
                }
                
                
                
            }else{
                $response_arr['insuffecient_funds'] = true;
            }
                                         
        }
        
      
           
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function validateEducationalVoucherInfo(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
        
        $response_arr = array('success' => false,'amount' => '','payable' => '');
        
        $validationRules = [
            'voucher_type' => 'required|in:waec,neco',
            'quantity' => 'required|numeric|min:1|max:20',
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            $response_arr['success'] = true;
            $type = $post_data->voucher_type;
            $quantity = $post_data->quantity;
                                
            if($type == "waec"){
                $amount = 1900;
                $payable = $amount * $quantity;
                $type_id = 2;
            }else{
                $amount = 850;
                $payable = $amount * $quantity;
                $type_id = 3;
            }

            $response_arr['amount'] = $amount;
            $response_arr['payable'] = $payable;
                       
        }
        
      
           
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadEducationalPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'educational';
        $props['csrf'] = csrf_token();
        $props['page_title'] = $this->main_model->custom_echo('Educational Vouchers',25);

        $url = "https://app.eminencesub.com/api/result-checker";
        $use_post = false;
        
        $response = $this->main_model->eminenceVtuCurl($url,$use_post);
                        
        // return $data;
        // return $response;
        if($this->main_model->isJson($response)){
            $response = json_decode($response);
            // var_dump($response);
            if(isset($response->data)){
                if($response->code == 200){
                    $props['voucher_types'] = $response->data;
                    
                    $i = 0;
                    
                    foreach($props['voucher_types'] as $row){
                    
                        $name = $row->name;
                        $price = $row->price;

                        if($name == "waec"){
                            $i++;
                            $row->index = $i;
                            $row->image = "/images/west-african-examinations-council-waec-logo.jpg";
                            $row->price = $price + 250;
                        }else if($name == "neco"){
                            $i++;
                            $row->index = $i;
                            $row->image = "/images/neco.png";
                            $row->price = $price + 250;
                        }else if($name == "nabteb"){
                            $i++;
                            $row->index = $i;
                            $row->image = "/images/nabteb.png";
                            $row->price = $price + 250;
                        }else if($name == "nbais"){
                            $i++;
                            $row->index = $i;
                            $row->image = "/images/nbais.jpg";
                            $row->price = $price + 250;
                        }

                        
                    }
                    // return $props['voucher_types'];
                    return Inertia::render('EducationalVouchers',$props);
                }
            }
        }
        
        
        
    }

   

    

    public function loadRouterPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'router';
        $props['csrf'] = csrf_token();
        $props['page_title'] = $this->main_model->custom_echo('Router Recharge',25);
        
        return Inertia::render('RouterRecharge',$props);
        
    }

    public function sendBulkSms(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
            
        $response_arr = array('success' => false,'messages' => '','recepients_exceeded' => false,'insuffecient_funds' => false,'order_id' => '','transaction_pending' => false);

        $validationRules = [
            'recepients' => 'required',
            'message' => 'required|max:140',
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            $recepients = $post_data->recepients;
            $message = $post_data->message;
            $recepients_arr = explode(",", $recepients);
            $recepients_num = count($recepients_arr);

            if($recepients_num <= 200){
                
                $message_cost = 3 * $recepients_num;
                $amount_to_debit_user = $message_cost;

                $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                // echo $user_total_amount;
                // echo $amount;

                if($amount_to_debit_user <= $user_total_amount){
                    $to = implode(",", $recepients_arr);
                    

                    $url = "https://www.payscribe.ng/api/v1/sms";

                    $use_post = true;
                    $data = [
                        'to' => $to,
                        'message' => $message
                    ];

                    // var_dump($post_data);

                    $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);


                    if($this->main_model->isJson($response)){

                        $response = json_decode($response);
                        // var_dump($response);

                        if($response->status && $response->status_code == 200){
                                        
                            $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                $order_id = $response->message->details->transaction_id;
                                $form_array = array(
                                    'user_id' => $user_id,
                                    'type' => 'bulk_sms',
                                    'sub_type' => "",
                                    'number' => $message,
                                    'date' => $date,
                                    'time' => $time,
                                    'amount' => $amount_to_debit_user,
                                    'order_id' => $order_id
                                );
                                if($this->main_model->addTransactionStatusOnly($form_array)){
                                    $response_arr['success'] = true;
                                    $response_arr['order_id'] = $order_id;
                                }
                            }
                        }else if($response->status && $response->status_code == 201){
                            $response_arr['transaction_pending'] = true;

                            $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                $order_id = $response->message->details->transaction_id;
                                $form_array = array(
                                    'user_id' => $user_id,
                                    'type' => 'bulk_sms',
                                    'sub_type' => "",
                                    'number' => $message,
                                    'date' => $date,
                                    'time' => $time,
                                    'amount' => $amount_to_debit_user,
                                    'order_id' => $order_id
                                );
                                if($this->main_model->addTransactionStatusOnly($form_array)){
                                    $response_arr['success'] = true;
                                    $response_arr['order_id'] = $order_id;
                                }
                            }
                        }
                        
                    }
                }else{
                    $response_arr['insuffecient_funds'] = true;
                }
            }else{
                $response_arr['recepients_exceeded'] = true;
            }
            
            
           
                        
        }
            
           
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadBulkSmsPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'bulk_sms';
        $props['csrf'] = csrf_token();
        $props['page_title'] = $this->main_model->custom_echo('Bulk SMS',25);
        
        return Inertia::render('BulkSMS',$props);
        
    }

    public function processAirtimeToWalletTransfer(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
            
        $response_arr = array('success' => false,'messages' => '','network' => '');

        $validationRules = [
            'network' => 'required|in:mtn,glo,airtel,9mobile',
            'amount' => 'required|numeric|min:1000|max:20000',
            'phone_number' => 'required|numeric|digits_between:5,15',
            'from' => 'required|numeric|digits_between:5,15',
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            $type = $post_data->network;
            $phone_number = $post_data->from;
            $amount = $post_data->amount;
            $from = $post_data->phone_number;
            
            
            if($type == "mtn" || $type == "glo" || $type == "airtel" || $type == "9mobile"){
                

                
                    
                $url = "https://www.payscribe.ng/api/v1/airtime_to_wallet/vend";
                $use_post = true;
                
                if($type == "glo"){
                    $network = "GLO";
                    
                    $network_2 = "glo";
                    $perc_charge = 5;
                    
                }else if($type == "airtel"){
                    $network = "AIRTEL";
                    
                    $network_2 = "airtel";
                    $perc_charge = 5;
                    
                }else if($type == "9mobile"){
                    $network = "9MOBILE";
                    $network_2 = "9mobile";
                    $perc_charge = 5;
                }else if($type == "mtn"){
                    $network = "MTN";
                    
                    $network_2 = "mtn";
                    $perc_charge = 5;
                    
                }
            

                $response_arr['network'] = $network_2;
                
                $data = [
                    'network' => $network_2,
                    'phone_number' => $phone_number,
                    'amount' => $amount,
                    'from' => $from
                ];

                
                // return $data;
                $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);


                if($this->main_model->isJson($response)){
                    $response = json_decode($response);
                    // var_dump($response);
                    if(is_object($response)){
                        if($response->status && $response->status_code == 200){
                        
                            
                            $order_id = $response->message->trans_id;
                            $form_array = array(
                                'user_id' => $user_id,
                                'type' => 'airtime_to_wallet',
                                'sub_type' => $network_2,
                                'number' => $from,
                                'date' => $date,
                                'time' => $time,
                                'amount' => $amount,
                                'order_id' => $order_id
                            );
                            if($this->main_model->addTransactionStatusAirtimeToWallet($form_array)){
                                $response_arr['success'] = true;
                                $response_arr['order_id'] = $order_id;
                            }
                        }
                    }
                }
                
            }
                        
        }
            
           
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function validateAirtimeToWalletDetails(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
            
        $response_arr = array('success' => false);

        $validationRules = [
            'network' => 'required|in:mtn,glo,airtel,9mobile',
            'amount' => 'required|numeric|min:1000|max:20000',
            'phone_number' => 'required|numeric|digits_between:5,15',
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            $response_arr['success'] = true;
                        
        }
            
           
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function getChargeForAirtimeToWalletTransfer(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
            
        $response_arr = array('success' => false,'messages' => '','network' => '');

        
        if(isset($post_data->network)){
            $type = $post_data->network;
            
            if($type == "mtn" || $type == "glo" || $type == "airtel" || $type == "9mobile"){
                
                    
                $url = "https://www.payscribe.ng/api/v1/airtime_to_wallet";
                $use_post = false;
                
                if($type == "glo"){
                    $network = "GLO";
                    
                    $network_2 = "glo";
                    $perc_charge = 5;
                    
                }else if($type == "airtel"){
                    $network = "AIRTEL";
                    
                    $network_2 = "airtel";
                    $perc_charge = 5;
                    
                }else if($type == "9mobile"){
                    $network = "9MOBILE";
                    $network_2 = "9mobile";
                    $perc_charge = 5;
                }else if($type == "mtn"){
                    $network = "MTN";
                    
                    $network_2 = "mtn";
                    $perc_charge = 5;
                    
                }
            

                $response_arr['network'] = $network_2;
                
                
                $response = $this->main_model->payscribeVtuCurl($url,$use_post);


                if($this->main_model->isJson($response)){
                    $response = json_decode($response);
                    // var_dump($response);
                    if(is_object($response)){
                        if($response->status && $response->status_code == 200){
                        
                            $response_arr['network'] = $network_2;
                            // var_dump($response->message->details);
                            
                            $details = $response->message->details;
                            

                            if(is_array($details)){

                                
                                $j = 0;

                                for($i = 0; $i < count($details); $i++){
                                    $instruction = $details[$i]->instruction;
                                    $transfer_code = $details[$i]->transfer_code;
                                    $network_name = $details[$i]->network_name;
                                    $status = $details[$i]->status;
                                    $phone_number = $details[$i]->phone_number;
                                    $rate = $details[$i]->rate;
                                    

                                    if($status && $network_name == $network_2){
                                        $response_arr['success'] = true;
                                        $charge = 100 - $rate;
                                        $charge = $charge - $perc_charge;
                                        $response_arr['charge'] = $charge;
                                        $response_arr['phone_number'] = $phone_number;
                                        $response_arr['transfer_code'] = $transfer_code;
                                        $response_arr['instruction'] = $instruction;
                                    }
                                }
                                
                            }
                        }
                    }
                }
            }
        }
            
           
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadAirtimeToWalletPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'airtime_to_wallet';
        $props['csrf'] = csrf_token();
        $props['page_title'] = $this->main_model->custom_echo('Airtime To Wallet',25);
        
        return Inertia::render('AirtimeToWallet',$props);
        
    }

    public function purchaseElectricityWithBuypower(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
            
            
        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'order_id' => '','invalid_meterno' => false,'meter_type_not_available' => false,'metertoken' => '','transaction_pending' => false);

        $validationRules = [
            'disco' => 'required|in:eko,ikeja,abuja,ibadan,enugu,phc,kano,kaduna,jos',
            'meter_type' => 'required|in:prepaid,postpaid',
            'meter_number' => 'required|numeric|digits_between:5,15',
            'amount' => 'required|numeric|min:100|max:50000',
            'mobile_number' => 'required|numeric|digits_between:5,15',
            'email' => 'required|email:rfc,dns,strict,spoof,filter',
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            $disco = $post_data->disco;
            $meter_type = $post_data->meter_type;           
            $meter_number = $post_data->meter_number;
            $amount = $post_data->amount;
            $mobile_number = $post_data->mobile_number;
            $email = $post_data->email;
            $payscribe_disco = "";
            $phone_number = $mobile_number;
            $meter_no = $meter_number;
                
            if($disco == "eko"){
                $disco_code = "EKO";
                $payscribe_disco = "ekedc";
            }else if($disco == "ikeja"){
                $disco_code = "IKEJA";
                $payscribe_disco = "ikedc";
            }else if($disco == "abuja"){
                $disco_code = "ABUJA";
                $payscribe_disco = "aedc";
            }else if($disco == "ibadan"){
                $disco_code = "IBADAN";
                $payscribe_disco = "ibedc";
            }else if($disco == "enugu"){
                $disco_code = "ENUGU";
                $payscribe_disco = "eedc";
            }else if($disco == "phc"){
                $disco_code = "PH";
                $payscribe_disco = "phedc";
            }else if($disco == "kano"){
                $disco_code = "KANO";
                
            }else if($disco == "kaduna"){
                $disco_code = "KADUNA";
                $payscribe_disco = "kedco";
            }else if($disco == "jos"){
                $disco_code = "JOS";
            }

            if($meter_type == "prepaid"){
                $meter_type = "PREPAID";
            }else if($meter_type == "postpaid"){
                $meter_type = "POSTPAID";
            }

            if($post_data->sms_check == true){
                $amount_deb_user = $amount + 5;
            }else{
                $amount_deb_user = $amount;
            }
            $amount_to_debit_user = $amount;
            $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);

            $meter_type = strtolower($meter_type);

            if($amount_deb_user <= $user_total_amount){

                

                    
                $url = "https://api.buypower.ng/v2/vend";
                $use_post = true;

                $order_id = "BP" . mt_rand(10000000, 99999999);

                $data = array(
                    'meter' => $meter_no,
                    'disco' => $disco_code,
                    'vendType' => $meter_type,
                    'orderId' => $order_id,
                    'phone' => $phone_number,
                    'paymentType' => 'ONLINE',
                    'amount' => $amount,
                    'email' => $email
                );
                

                $response = $this->main_model->buyPowerVtuCurl($url,$use_post,$data);

                // var_dump($response);
                // return $response;

                if($this->main_model->isJson($response)){
                    $response = json_decode($response);
                    if(is_object($response)){
                        if(isset($response->status)){
                            $status = $response->status;
                        
                            $metertoken = "";
                            if($status == true){

                                $summary = "Debit Of " . $amount_to_debit_user . " For Electricity Recharge";

                                if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                
                                    // if(isset($response->message->details->reference_number)){
                                    //  $order_id = $response->message->details->reference_number;
                                    // }else{
                                    //  $order_id = "";
                                    // }

                                    if(isset($response->data->token)){
                                        $metertoken = $response->data->token;
                                        $this->main_model->sendMeterTokenForPrepaidToUserByNotif($user_id,$email,$date,$time,$order_id,$disco,$meter_no,$amount,$metertoken);
                                    }

                                    $form_array = array(
                                        'user_id' => $user_id,
                                        'type' => 'electricity',
                                        'sub_type' => $disco,
                                        'date' => $date,
                                        'time' => $time,
                                        'amount' => $amount,
                                        'number' => $meter_no,
                                        'order_id' => $order_id
                                    );
                                    if($this->main_model->addTransactionStatus($form_array)){
                                        $response_arr['success'] = true;
                                        $response_arr['order_id'] = $order_id;
                                        $response_arr['metertoken'] = $metertoken;

                                        if($post_data->sms_check == true){
                                            $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                                            $amount_to_debit_user = 5;
                                            // echo $user_total_amount;
                                            // echo $amount;

                                            if($amount_to_debit_user <= $user_total_amount){
                                                

                                                if($meter_type == "prepaid"){
                                                    $to = $phone_number;
                                                    $message = "Your Meter Token For Meter Number " . $meter_no . " Is ". $metertoken;
                                                    $url = "https://www.payscribe.ng/api/v1/sms";

                                                    $use_post = true;
                                                    $data = [
                                                        'to' => $to,
                                                        'message' => $message
                                                    ];

                                                    
                                                    // var_dump($post_data);

                                                    $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);


                                                    if($this->main_model->isJson($response)){

                                                        $response = json_decode($response);
                                                        // var_dump($response);

                                                        if($response->status && $response->status_code == 200){
                                    
                                                            $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                                $order_id = $response->message->details->transaction_id;
                                                                $form_array = array(
                                                                    'user_id' => $user_id,
                                                                    'type' => 'bulk_sms',
                                                                    'sub_type' => "",
                                                                    'number' => $message,
                                                                    'date' => $date,
                                                                    'time' => $time,
                                                                    'amount' => $amount_to_debit_user,
                                                                    'order_id' => $order_id
                                                                );
                                                                if($this->main_model->addTransactionStatusOnly($form_array)){
                                                                    $response_arr['success'] = true;
                                                                    $response_arr['order_id'] = $order_id;
                                                                }
                                                            }
                                                        }else if($response->status && $response->status_code == 201){
                                                            

                                                            $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                                $order_id = $response->message->details->transaction_id;
                                                                $form_array = array(
                                                                    'user_id' => $user_id,
                                                                    'type' => 'bulk_sms',
                                                                    'sub_type' => "",
                                                                    'number' => $message,
                                                                    'date' => $date,
                                                                    'time' => $time,
                                                                    'amount' => $amount_to_debit_user,
                                                                    'order_id' => $order_id
                                                                );
                                                                if($this->main_model->addTransactionStatusOnly($form_array)){
                                                                    $response_arr['success'] = true;
                                                                    $response_arr['order_id'] = $order_id;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }

                                            }
                                        }
                                    }
                                }
                                
                            }
                        }
                    }
                }
    

                
                
            }else{
                $response_arr['insuffecient_funds'] = true;
            }
            
                        
        }
            
           
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    

    

    public function checkIfDiscoIsAvailable(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false);

        if(isset($post_data->disco)){
            $disco = $post_data->disco;
            
            if($disco == "eko" || $disco == "ikeja" || $disco == "abuja" || $disco == "ibadan" || $disco == "enugu" || $disco == "phc" || $disco == "kano" || $disco == "kaduna" || $disco == "jos"){
                
                if($disco == "eko"){
                    $disco_code = "EKO";
                }else if($disco == "ikeja"){
                    $disco_code = "IKEJA";
                }else if($disco == "abuja"){
                    $disco_code = "ABUJA";
                }else if($disco == "ibadan"){
                    $disco_code = "IBADAN";
                }else if($disco == "enugu"){
                    $disco_code = "ENUGU";
                }else if($disco == "phc"){
                    $disco_code = "PH";
                }else if($disco == "kano"){
                    $disco_code = "KANO";
                }else if($disco == "kaduna"){
                    $disco_code = "KADUNA";
                }else if($disco == "jos"){
                    $disco_code = "JOS";
                }
  
                


                $url = "https://api.buypower.ng/v2/discos/status";
                $use_post = false;


                $response = $this->main_model->buyPowerVtuCurl($url,$use_post);

                if($this->main_model->isJson($response)){
                    $response = json_decode($response);
                    if(is_object($response)){

                        if(isset($response->$disco_code)){
                            $status = $response->$disco_code;
                            if($status){
                                if($disco == "eko"){
                                    $response_arr['success'] = true;    
                                }else{
                                    $response_arr['success'] = true;
                                }
                            }
                            
                        }
                                
                    }
                }
            }
            
        }
             
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
        

    }

    public function loadElectricityPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'electricity';
        
        $props['page_title'] = $this->main_model->custom_echo('Electricity Topup',25);

        
        // return json_encode($props['data_plans']);
        
        return Inertia::render('ElectricityPopup',$props);
        
    }

    

    

    public function loadCableTvPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'cable_tv';
        
        $props['page_title'] = $this->main_model->custom_echo('Cable Tv Recharge',25);

        
        // return json_encode($props['data_plans']);
        
        return Inertia::render('CableTV',$props);
        
    }


    public function purchaseClubKonnectData(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'order_id' => '','transaction_pending' => false);

        $validationRules = [
            'network' => 'required|in:mtn,airtel,glo,9mobile',
            'phone_number' => 'required|numeric|digits_between:6,15'
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            // return $post_data->selected_plan['product_id'];
            $network = $post_data->network;
            if(isset($post_data->selected_plan['product_id'])){

                $product_id = $post_data->selected_plan['product_id'];
                // if($network == "MTN"){
                //     $mobilenetwork_code = "01";
                //     $perc_disc = 0.04;
                
                    

                //     $amount = $this->main_model->getVtuDataBundleCostByProductId($network,$product_id);
                    
                //     $phone_number = $post_data->phone_number;
                    
                //     $amount_to_debit_user = round(($perc_disc * $amount) + $amount,2);
                //     $amount_to_debit_user += 5;
                //     $amount_to_debit_user += 2;
                        
                    

                    
                //     $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                    
                //     if($amount_to_debit_user <= $user_total_amount){
                        
                            

                //         $url = "https://www.nellobytesystems.com/APIDatabundleV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&MobileNetwork=".$mobilenetwork_code."&DataPlan=".$product_id."&MobileNumber=".$phone_number;
                //         $use_post = true;
                        
                        
                //         $response = $this->main_model->vtu_curl($url,$use_post,$post_data = []);
                //         // $response = json_encode(array('status' => 'ORDER_RECEIVED', 'orderid' => '542425'));
                //             // return $response;
                        

                //         if($this->main_model->isJson($response)){
                //             $response = json_decode($response);
                //             // var_dump($response);
                //             if($response->status == "ORDER_RECEIVED"){
                //                 $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                //                 if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                //                     $order_id = $response->orderid;
                //                     $form_array = array(
                //                         'user_id' => $user_id,
                //                         'type' => 'data',
                //                         'sub_type' => $network,
                //                         'number' => $phone_number,
                //                         'date' => $date,
                //                         'time' => $time,
                //                         'amount' => $amount_to_debit_user,
                //                         'order_id' => $order_id
                //                     );
                //                     if($this->main_model->addTransactionStatus($form_array)){
                //                         $response_arr['success'] = true;
                //                         $response_arr['order_id'] = $order_id;
                //                     }
                //                 }
                //             }
                //         }   

                //     }else{
                //         $response_arr['insuffecient_funds'] = true;
                //     }
                    
                    
                // }else{

                    if($network == "mtn"){
                        
                        $mobilenetwork_code = "01";
                        $perc_disc = 0.04;
                        if($product_id == "500"){
                            
                            $amt_to_add = 17.12;
                        }else if($product_id == "1000" || $product_id == "2000" || $product_id == "3000" || $product_id == "5000"){
                            
                            $amt_to_add = 7.92;
                        }else{
                            $amt_to_add = 4;
                        }
                        $perc_disc = 0.04;
                        $amt_to_add = 10;
                    }elseif($network == "glo"){
                        
                        $mobilenetwork_code = "02";
                        $perc_disc = 0.04;
                        $amt_to_add = 3;
                    }else if($network == "9mobile"){
                        
                        $mobilenetwork_code = "03";
                        $perc_disc = 0.04;
                        $amt_to_add = 0;
                    }else if($network == "airtel"){
                        
                        $mobilenetwork_code = "04";
                        $perc_disc = 0.04;
                        $amt_to_add = 4;
                    }


                    
                   
                    $amount = $this->main_model->getVtuDataBundleCostByProductId($network,$product_id);
                    

                    
                    
                    if($amount != 0){
                        $phone_number = $post_data->phone_number;
                        
                        // $amount_to_debit_user = round(($perc_disc * $amount) + $amount,2);
                        
                        $amount_to_debit_user = round((($perc_disc * $amount) + $amount) + $amt_to_add,2);

                        if($network == "mtn" && $product_id == "10000"){
                            $amount_to_debit_user = 2600;
                        }

                        return $amount_to_debit_user;
                            
                        

                        // return $amount_to_debit_user;
                        $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                        
                        if($amount_to_debit_user <= $user_total_amount){
                            
                            // $url = "https://www.nellobytesystems.com/APIVerifyElectricityV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&ElectricCompany=".$disco_code."&meterno=".$meter_no;

                            $url = "https://www.nellobytesystems.com/APIDatabundleV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&MobileNetwork=".$mobilenetwork_code."&DataPlan=".$product_id."&MobileNumber=".$phone_number;
                            // return $url;
                            $use_post = true;
                            // $post_data = array(
                            //  'network' => $network,
                            //  'plan' => $product_id,
                            //  'recipent' => $mobile_no
                            // );
                            
                            $response = $this->main_model->vtu_curl($url,$use_post,$post_data = []);
                            // $response = json_encode(array('status' => 'ORDER_RECEIVED', 'orderid' => '542425'));
                            // return $response;
                            if($this->main_model->isJson($response)){
                                $response = json_decode($response);
                                // var_dump($response);
                                if($response->status == "ORDER_RECEIVED"){
                                    $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                        $order_id = $response->orderid;
                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'data',
                                            'sub_type' => $network,
                                            'number' => $phone_number,
                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount_to_debit_user,
                                            'order_id' => $order_id
                                        );
                                        if($this->main_model->addTransactionStatus($form_array)){
                                            $response_arr['success'] = true;
                                            $response_arr['order_id'] = $order_id;
                                        }
                                    }
                                }
                            }   

                            
                        }else{
                            $response_arr['insuffecient_funds'] = true;
                        }
                    }
                
                
            }
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function purchasePayscribeData(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'order_id' => '','transaction_pending' => false);

        $validationRules = [
            'network' => 'required|in:mtn,airtel,glo,9mobile',
            'phone_number' => 'required|numeric|digits_between:6,15'
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            // return $post_data->selected_plan['product_id'];
            $network = $post_data->network;
            if(isset($post_data->selected_plan['product_id'])){

                $product_id = $post_data->selected_plan['product_id'];
                if($network == "mtn"){
                    $amount = $this->main_model->getPayscribeVtuDataBundleCostByProductId($network,$product_id);
                            
                    if($amount != 0){
                        $phone_number = $post_data->phone_number;
                        
                        $amount_to_debit_user = round((0.04 * $amount) + $amount,2);
                        $amount_to_debit_user += 10;
                        
                        $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                        
                        if($amount_to_debit_user <= $user_total_amount){
                            
                            $url = "https://www.payscribe.ng/api/v1/data/vend";
                            $use_post = true;
                            $data = array(
                                'network' => $network,
                                'plan' => $product_id,
                                'recipent' => $phone_number
                            );

                            if(isset($post_data->ported)){
                                if($post_data->ported == true){
                                    $data['ported'] = true;
                                }
                            }

                            // echo json_encode($post_data);
                            
                            $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);
                            

                            if($this->main_model->isJson($response)){
                                $response = json_decode($response);
                                
                                // var_dump($response);
                                if($response->status && $response->status_code == 200){
                                    
                                    $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                        $order_id = $response->message->details->transaction_id;
                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'data',
                                            'sub_type' => $network,
                                            'number' => $phone_number,
                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount_to_debit_user,
                                            'order_id' => $order_id
                                        );
                                        if($this->main_model->addTransactionStatus($form_array)){
                                            $response_arr['success'] = true;
                                            $response_arr['order_id'] = $order_id;
                                        }
                                    }
                                }else if($response->status && $response->status_code == 201){
                                    $response_arr['transaction_pending'] = true;

                                    $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                        $order_id = $response->message->details->transaction_id;
                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'data',
                                            'sub_type' => $network,
                                            'number' => $phone_number,
                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount_to_debit_user,
                                            'order_id' => $order_id
                                        );
                                        if($this->main_model->addTransactionStatusOnly($form_array)){
                                            $response_arr['success'] = true;
                                            $response_arr['order_id'] = $order_id;
                                        }
                                    }
                                }
                            }
                        
                        }else{
                            $response_arr['insuffecient_funds'] = true;
                        }
                    }
                }else{

                    if($network == "glo"){
                        // $network = "GLO";
                        
                        $net = "Glo";
                        $perc_disc = 0.04;
                        $additional_charge = 12;
                    }else if($network == "airtel"){
                        // $network = "AIRTEL";
                        
                        $net = "Airtel";
                        $perc_disc = 0.04;
                        $additional_charge = 4;
                    }else if($network == "9mobile"){
                        // $network = "9MOBILE";
                        
                        $net = "9mobile";
                        $perc_disc = 0.04;
                        $additional_charge = 20;
                    }

                    $phone_number = $post_data->phone_number;
                    
                    $amount = $this->main_model->getPayscribeVtuDataBundleCostByProductId($network,$product_id);
                    // echo $product_id;
                    $amount_to_debit_user = 0;
                    if($amount != 0){
                        $amount_to_debit_user = round((0.04 * $amount) + $amount,2);
                        $amount_to_debit_user += $additional_charge;
                    }
                    
                    
                    if($amount_to_debit_user != 0){
                        
                    
                        $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                        
                        if($amount_to_debit_user <= $user_total_amount){
                            
                            $url = "https://www.payscribe.ng/api/v1/data/vend";
                            $use_post = true;
                            $data = array(
                                'network' => $net,
                                'plan' => $product_id,
                                'recipent' => $phone_number
                            );

                            if(isset($post_data->ported)){
                                if($post_data->ported == true){
                                    $data['ported'] = true;
                                }
                                
                            }

                            
                            $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);
                            

                            if($this->main_model->isJson($response)){
                                $response = json_decode($response);
                                // var_dump($response);
                                if($response->status && $response->status_code == 200){
                                    
                                    $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                        $order_id = $response->message->details->transaction_id;
                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'data',
                                            'sub_type' => $network,
                                            'number' => $phone_number,
                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount_to_debit_user,
                                            'order_id' => $order_id
                                        );
                                        if($this->main_model->addTransactionStatus($form_array)){
                                            $response_arr['success'] = true;
                                            $response_arr['order_id'] = $order_id;
                                        }
                                    }
                                }else if($response->status && $response->status_code == 201){
                                    $response_arr['transaction_pending'] = true;
                                    $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                        $order_id = $response->message->details->transaction_id;
                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'data',
                                            'sub_type' => $network,
                                            'number' => $phone_number,
                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount_to_debit_user,
                                            'order_id' => $order_id
                                        );
                                        if($this->main_model->addTransactionStatusOnly($form_array)){
                                            $response_arr['success'] = true;
                                            $response_arr['order_id'] = $order_id;
                                        }
                                    }
                                }
                            }
                            
                        
                        }else{
                            $response_arr['insuffecient_funds'] = true;
                        }
                    }
                }
                
            }
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function purchase9mobileComboData(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'order_id' => '','transaction_pending' => false);

        $validationRules = [
            'network' => 'required|in:9mobile',
            'phone_number' => 'required|numeric|digits_between:6,15'
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            // return $post_data->selected_plan['product_id'];
            if(isset($post_data->selected_plan['product_id'])){

                $product_id = $post_data->selected_plan['product_id'];
                $amount = $this->main_model->get9mobileComboCostByProductId($product_id);
                $phone_number = $post_data->phone_number;
                $amount_to_debit_user = $amount;
                
                // echo $amount_to_debit_user;
                $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                
                if($amount_to_debit_user <= $user_total_amount){
                      
                    $data_amount = $this->main_model->get9mobileComboDataAmountByProductId($product_id);
                    if($data_amount != ""){
                        $form_array = array(
                            'user_id' => $user_id,
                            'type' => 'data',
                            'sub_type' => '9mobile',
                            'number' => $phone_number,
                            'date' => $date,
                            'time' => $time,
                            'amount' => $amount_to_debit_user,
                            'order_id' => ""
                        );
                        if($this->main_model->addTransactionStatus($form_array)){
                            $form_array = array(
                                'user_id' => $user_id,
                                'number' => $phone_number,
                                'amount' => $data_amount,
                                'date' => $date,
                                'time' => $time
                            );
                            if($this->main_model->addComboRequest($form_array)){
                                $summary = "Debit Of " . $amount_to_debit_user . " For 9mobile Data Recharge";
                                if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                    $response_arr['success'] = true;
                                }
                            }
                            
                        }
                    }
                    
                }else{
                    $response_arr['insuffecient_funds'] = true;
                }
                
            }
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    

    public function loadDataPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'data';
        
        $props['page_title'] = $this->main_model->custom_echo('Internet Data Topup',25);

        // $props['plans'] = $this->main_model->loadDataPlansForNetwork('mtn');
        // return json_encode($props['data_plans']);
        
        return Inertia::render('InternetData',$props);
        
    }

    public function loadPrintRechargePinsPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;
        $post_data = (Object) $req->input();

        
        $this->data['page_title'] = 'Print Recharge E-Pins';

        if(isset($post_data->epins) && isset($post_data->amount)){
            $this->data['epins'] = $post_data->epins;
            $this->data['amount'] = $post_data->amount;
            if($this->main_model->isJson($this->data['epins'])){
                return View('print_recharge_pins',$this->data);
            }
        }
        
        
        
    }

    public function generateEpin(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'epins' => '','invalid_amount' => false,'invalid_recipient' => false,'epins_json' => '','pin' => '');

        $validationRules = [
            'network' => 'required|in:mtn,airtel,glo,9mobile',
            'amount' => 'required|numeric|in:100,200,500',
            'recharge_type' => 'required|in:epin',
            'quantity' => 'required|numeric|min:1|max:20'
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            $network = $post_data->network;
            $amount = $post_data->amount;
            $quantity = $post_data->quantity;

            if($network == "mtn"){
                $club_network = "01";
                $discount = 0.01;
            }else if($network == "glo"){
                $club_network = "02";
                $discount = 0.02;
            }else if($network == "9mobile"){
                $club_network = "03";
                $discount = 0.02;
            }else if($network == "airtel"){
                $club_network = "04";
                $discount = 0.02;
            } 

            
            
            
            $amount_to_debit_user = $amount - ($discount * $amount);
            $amount_to_debit_user = $amount_to_debit_user * $quantity;

            $response_arr['amount_to_debit_user'] = $amount_to_debit_user;
            
            $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
            
            // return $amount_to_debit_user;
            
            if($amount_to_debit_user <= $user_total_amount){

                

                // $url = "https://www.payscribe.ng/api/v1/rechargecard";

                $url = "https://www.nellobytesystems.com/APIEPINV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&MobileNetwork=".$club_network."&Value=".$amount."&Quantity=".$quantity;
                
                // return $url;
                $use_post = false;
                // $data = [
                //     'qty' => $quantity,
                //     'amount' => $amount,
                //     'display_name' => "Payscribe"
                // ];

                // $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);
                // $response = $this->main_model->generateRandomEpinData($quantity);
                        
                // return $response;
                $response = $this->main_model->vtu_curl($url,$use_post,$post_data=[]);
                // $response = json_encode(array('status' => 'ORDER_RECEIVED', 'orderid' => '5424425'));
                // return $response;

                // $response = '{"TXN_EPIN":[{"transactionid":"6425025665","transactiondate":"2/1/2022 11:20:31 AM","batchno":"726326","mobilenetwork":"GLO","sno":"580129003028638","pin":"929181631685436","amount":"100"}]}';
                if($this->main_model->isJson($response)){
                    $response = json_decode($response);
                    if(is_object($response)){
                        $transactionid = $response->TXN_EPIN[0]->transactionid;
                        $pin = $response->TXN_EPIN[0]->pin;
                        $order_id = $transactionid;

                        if($transactionid != ""){

                            $summary = "Debit Of " . $amount_to_debit_user . " For Vtu E-Pin Generation";
                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                
                                $form_array = array(
                                    'user_id' => $user_id,
                                    'type' => 'e-pin',
                                    'sub_type' => 'payscribe_epin',
                                    
                                    'date' => $date,
                                    'time' => $time,
                                    'amount' => $amount_to_debit_user,
                                    'order_id' => $order_id
                                );
                                if($this->main_model->addVTUTransactionStatus1($form_array)){
                                    $response_arr['success'] = true;
                                    // $index = 0;
                                    // for($i = 0; $i < count($details); $i++){
                                    //     $index++;
                                    //     $details[$i]->index = $index;
                                    // }
                                    // $response_arr['epins'] = $details;
                                    // $response_arr['epins_json'] = json_encode($details);
                                    $response_arr['pin'] = $pin;
                                    $response_arr['amount'] = $amount;
                                }
                            }
                        }
                    }
                }
                                
            }else{
                $response_arr['insuffecient_funds'] = true;
            }
                                
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function normalAirtimeRechargeRequest(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'order_id' => '','invalid_amount' => false,'invalid_recipient' => false);

        $validationRules = [
            'network' => 'required|in:9mobile,mtn,airtel,glo',
            'amount' => 'required|numeric|min:100|max:50000',
            'recharge_type' => 'required|in:normal',
            'phone_number' => 'required|numeric|digits_between:6,15'
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            $network = $post_data->network;
            
            
            $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
            // echo $user_total_amount;
            // echo $amount;
            $amount = $post_data->amount;
            if($amount <= $user_total_amount){
                
                $recharge_type = $post_data->recharge_type;
                $phone_number = $post_data->phone_number;
                $airtime_bonus = $post_data->airtime_bonus;
                $amount_to_debit_user = $amount;
                
                if($network == "mtn"){
                    $mobilenetwork_code = "01";
                    $eminence_ntwrk = "MTN";
                    $serviceID = "mtn";
                }else if($network == "glo"){
                    $mobilenetwork_code = "02";
                    $eminence_ntwrk = "GLO";
                    $serviceID = "glo";
                }else if($network == "9mobile"){
                    $mobilenetwork_code = "03";
                    $eminence_ntwrk = "ETISALAT";
                    $serviceID = "etisalat";
                }else if($network == "airtel"){
                    $mobilenetwork_code = "04";
                    $eminence_ntwrk = "AIRTEL";
                    $serviceID = "airtel";
                }

                $url = "https://www.nellobytesystems.com/APIAirtimeV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&MobileNetwork=". $mobilenetwork_code ."&Amount=" . $amount . "&MobileNumber=" . $phone_number;

                $url_2 = "https://app.eminencesub.com/api/buy-airtime";
                $url_3 = "https://gsubz.com/api/pay/";

                if($network == "mtn" && $airtime_bonus == true){
                    $url .= "&BonusType=01";
                }else if($network == "glo" && $airtime_bonus == true){
                    $url .= "&BonusType=02";
                }

                // return $url;

                $use_post = true;

                $vtu_platform = $this->main_model->getVtuPlatformToUse('airtime',$network);
                $vtu_platform_shrt = substr($vtu_platform, 0, 8);
                if($vtu_platform == "gsubz"){
                    
                    $api = $this->main_model->getGsubzApiKey();
                    // return $type;
                    $post_data = [
                        "phone" => $phone_number,
                        "amount" => $amount,
                        "serviceID" => $serviceID,
                        "api" => $api,
                    ];

                    

                    $response = $this->main_model->gSubzVtuCurl($url_3,$use_post,$post_data);
                   
                    
                    if($this->main_model->isJson($response)){
                        $response = json_decode($response);
                        if(is_object($response)){
                            $code = $response->code;
                            $status = $response->status;

                            if($code == 200 && $status == "TRANSACTION_SUCCESSFUL"){
                                $summary = "Debit Of " . $amount . " For Airtime Recharge";
                                if($this->main_model->debitUser($user_id,$amount,$summary)){
                                    
                                    $order_id = "GS" . $response->content->transactionID;
                                    $form_array = array(
                                        'user_id' => $user_id,
                                        'type' => 'airtime',
                                        'sub_type' => $network,
                                        'number' => $phone_number,
                                        'date' => $date,
                                        'time' => $time,
                                        'amount' => $amount,
                                        'order_id' => $order_id
                                    );
                                    if($this->main_model->addTransactionStatus($form_array)){
                                        $response_arr['success'] = true;
                                        $response_arr['order_id'] = $order_id;
                                    }
                                }
                            }
                        }
                    }
                }else{
                    if($vtu_platform_shrt != "eminence"){

                        $response = $this->main_model->vtu_curl($url,$use_post,$post_data=[]);
                        // $response = json_encode(array('status' => 'ORDER_RECEIVED', 'orderid' => '5424425'));
                        // echo $response;

                        if($this->main_model->isJson($response)){
                            $response = json_decode($response);
                            if(is_object($response)){
                                $status = $response->status;

                                if($status == "ORDER_RECEIVED"){
                                    $summary = "Debit Of " . $amount . " For Airtime Recharge";
                                    if($this->main_model->debitUser($user_id,$amount,$summary)){
                                        $order_id = $response->orderid;
                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'airtime',
                                            'sub_type' => $network,
                                            'number' => $phone_number,
                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount,
                                            'order_id' => $order_id
                                        );
                                        if($this->main_model->addTransactionStatus($form_array)){
                                            $response_arr['success'] = true;
                                            $response_arr['order_id'] = $order_id;
                                        }
                                    }
                                }else if($status == "INVALID_AMOUNT"){
                                    $response_arr['invalid_amount'] = true;
                                    
                                }else if($status == "INVALID_RECIPIENT"){
                                    $response_arr['invalid_recipient'] = true;
                                    
                                }else if($status == "INSUFFICIENT_BALANCE"){
                                    // $response_arr['invalid_recipient'] = true;
                                    // echo "string";
                                    
                                }else if($status == "INVALID_CREDENTIALS"){
                                    // $response_arr['invalid_recipient'] = true;
                                    // echo "string";
                                    
                                }
                            }
                        }
                    }else{
                        $type = strtoupper(substr($vtu_platform, 9));
                        // return $type;
                        $post_data = [
                            "phone" => $phone_number,
                            "amount" => $amount,
                            "network" => $eminence_ntwrk,
                            "type" => $type,
                        ];

                        

                        $response = $this->main_model->eminenceVtuCurl($url_2,$use_post,$post_data);
                       
                        // return $response;
                        if($this->main_model->isJson($response)){
                            $response = json_decode($response);
                            if(is_object($response)){
                                $status = $response->status;
                                $message = $response->message;

                                if($status == true){
                                    $summary = "Debit Of " . $amount . " For Airtime Recharge";
                                    if($this->main_model->debitUser($user_id,$amount,$summary)){
                                        $order_id = $response->data->reference;
                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'airtime',
                                            'sub_type' => $network,
                                            'number' => $phone_number,
                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount,
                                            'order_id' => $order_id
                                        );
                                        if($this->main_model->addTransactionStatus($form_array)){
                                            $response_arr['success'] = true;
                                            $response_arr['order_id'] = $order_id;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
            }else{
                $response_arr['insuffecient_funds'] = true;
            }
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function request9mobileComboRecharge(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'order_id' => '','invalid_amount' => false,'invalid_recipient' => false);

        $validationRules = [
            'network' => 'required|in:9mobile',
            'amount' => 'required|numeric|min:1000|max:50000',
            'recharge_type' => 'required|in:combo',
            'phone_number' => 'required|numeric|digits_between:6,15'
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
            // echo $user_total_amount;
            // echo $amount;
            $amount = $post_data->amount;
            if($amount <= $user_total_amount){
                $network = $post_data->network;
                
                $recharge_type = $post_data->recharge_type;
                $phone_number = $post_data->phone_number;

                $form_array = array(
                    'user_id' => $user_id,
                    'type' => 'airtime',
                    'sub_type' => $network,
                    'number' => $phone_number,
                    'date' => $date,
                    'time' => $time,
                    'amount' => $amount,
                    'order_id' => ""
                );
                if($this->main_model->addTransactionStatus($form_array)){
                    $form_array = array(
                        'user_id' => $user_id,
                        'number' => $phone_number,
                        'amount' => $amount,
                        'date' => $date,
                        'time' => $time
                    );
                    if($this->main_model->addComboRequest($form_array)){
                        $summary = "Debit Of " . $amount . " For 9mobile Combo Airtime Recharge";
                        if($this->main_model->debitUser($user_id,$amount,$summary)){
                            $response_arr['success'] = true;
                        }
                    }
                    
                }
            }else{
                $response_arr['insuffecient_funds'] = true;
            }
        }
        

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadAirtimePage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'airtime';
        $props['csrf'] = csrf_token();
        $props['page_title'] = $this->main_model->custom_echo('Airtime Topup',25);
        
        return Inertia::render('AirtimeTopup',$props);
        
    }

    public function processValidateWithdrawalOtp(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'messages' => '','not_bouyant' => false,'too_small' => false,'incomplete_detais' => false,'incorrect_otp' => false);

        if(isset($post_data->amount)){

            if(isset($post_data->otp)){
                
                $otp = $post_data->otp;
                $hashed_otp = sha1($otp);


                if($hashed_otp == $this->data['transaction_password']){
                    // $response_arr['done'] = true;
            
                    $amount = $post_data->amount;
                    if(is_numeric($amount)){
                        if($amount >= 200){
                            $total_income = $this->main_model->getUserParamById("total_income",$user_id);
                            $withdrawn = $this->main_model->getUserParamById("withdrawn",$user_id);
                            $recepient_code = $this->main_model->getUserParamById("recepient_code",$user_id);
                            $available_income = $total_income - $withdrawn;
                            $amount_with_charge = $amount + 100;
                            if($amount_with_charge <= $available_income){
                                
                                // $funds_amount = $amount - 100;
                                // $funds_amount = $funds_amount - (0.02 * $amount);
                                
                                // $funds_amount = $funds_amount * 100;
                            
                                $response_arr['no_refer'] = false;
                                
                                $date = date("j M Y");
                                $time = date("h:i:sa");

                                $user_bank_name = $this->data['bank_name'];
                                $account_number = $this->data['account_number'];

                                $banks_arr = $this->main_model->paystackCurl("https://api.paystack.co/bank",FALSE);
                                $banks_arr = json_decode($banks_arr);

                                if($banks_arr->status && $banks_arr->message == "Banks retrieved"){

                
                
                    
                    
                                    $bank_names = $banks_arr->data;


                                    foreach($bank_names as $row){
                                        $bank_name = $row->name;
                                        $code = $row->code;
                                        $long_code = $row->longcode;
                                        $active = $row->active;
                                        $is_deleted = $row->is_deleted;
                        
                                        
                        
                                        if($code == $user_bank_name){ 
                                            // echo $bank_name;

                                            $account_number_test = $this->main_model->paystackCurl("https://api.paystack.co/bank/resolve?account_number=".$account_number."&bank_code=".$user_bank_name,FALSE);
                                            // echo $account_number_test;

                                            $account_number_test = json_decode($account_number_test);
                                            if(is_object($account_number_test)){
                                                // echo "string";
                                                if($account_number_test->status == "true"){
                                    
            
                                                    $account_name = $account_number_test->data->account_name;
                                                    
                                    

                                                    $summary = "Withdrawal Of " . $amount;
                                                    if($this->main_model->debitUser($user_id,$amount,$summary)){
                                                        $summary = "Withdrawal Charge";
                                                        if($this->main_model->debitUser($user_id,100,$summary)){
                                                            $form_array = array(
                                                                'user_id' => $user_id,
                                                                'user_name' => $this->data['user_name'],
                                                                'amount' => $amount,
                                                                'bank_name' => $user_bank_name,
                                                                'real_bank_name' => $bank_name,
                                                                'account_number' => $account_number,
                                                                'account_name' => $account_name,
                                                                'date' => $date,
                                                                'time' => $time
                                                            );
                                                            if($this->main_model->sendWithrawalRequest($form_array)){

                                                                $response_arr['success'] = true;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                
                            }else{
                                $response_arr['not_bouyant'] = true;
                            }
                        }else{
                            $response_arr['too_small'] = true;
                        }
                    }
                    
                }else{
                    $response_arr['incorrect_otp'] = true;
                }
                            
            }else{
                $response_arr['incomplete_detais'] = true;
            }
                        
                
            
        }

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function processSendWithdrawalOtp(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        if(isset($post_data->amount)){
            $response_arr = array('success' => false,'messages' => '','not_bouyant' => false,'too_small' => false);
            $amount = $post_data->amount;
            if(is_numeric($amount)){
                if($amount >= 200){
                    $available_income = $this->data['total_income'] - $this->data['withdrawn'];
                    $amount = $amount + 100;
                    if($amount <= $available_income){
                        $otp = rand ( 10000 , 99999 );
                        $email_arr = array($this->data['email']);
                        
                            
                        $response_arr['success'] = true;
                        //Change This Once In Development
                        if($_SERVER['SERVER_NAME'] == "127.0.0.1"){
                            $response_arr['otp'] = $otp;
                        }
                        $response_arr['phone_number'] = $this->data['phone'];
                        $response_arr['code'] = $this->data['phone_code'];
                        $response_arr['email'] = $this->data['email'];

                                            
                    
                    }else{
                        $response_arr['not_bouyant'] = true;
                    }
                }else{
                    $response_arr['too_small'] = true;
                }
            }
            
        }

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function processEnterAmountWithdrawFunds(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'not_bouyant' => false,'too_small' => false);
        if(isset($post_data->amount)){
            
            $amount = $post_data->amount;
            if(is_numeric($amount)){
                if($amount >= 200){
                    $available_income = $this->data['total_income'] - $this->data['withdrawn'];
                    $amount = $amount + 100;
                    if($amount <= $available_income){
                        $response_arr['success'] = true;
                        $response_arr['phone_number'] = $this->data['phone'];
                        $response_arr['code'] = $this->data['phone_code'];
                    }else{
                        $response_arr['not_bouyant'] = true;
                    }
                }else{
                    $response_arr['too_small'] = true;
                }
            }
            
        }

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function processWithdrawFundsCont(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'messages' => '','bank_details' => "",'account_name' => '','invalid_account' => false);
                    
        
        $validationRules = [
            'bank_name' => 'required|numeric',
            'account_number' => 'required|numeric',
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            
            $bank_name = $post_data->bank_name;
            $account_number = $post_data->account_number;
            
            $account_number_test =$this->main_model->paystackCurl("https://api.paystack.co/bank/resolve?account_number=".$account_number."&bank_code=".$bank_name,FALSE);

            $account_number_test = json_decode($account_number_test);
            // return $account_number_test;
            if(is_object($account_number_test)){
                // echo "string";
                if($account_number_test->status == "true"){
                    $data = [
                        "type" =>  "nuban",
                         "name" => $this->data['full_name'],
                         // "description" => "Payment For ".$health_facility_name,
                         "account_number" => $account_number,
                         "bank_code" => $bank_name,
                         "currency" => "NGN"
                    ];

                    $create_transfer_recipient = $this->main_model->paystackCurl("https://api.paystack.co/transferrecipient",TRUE,$data);
                    $create_transfer_recipient = json_decode($create_transfer_recipient);
                    if(is_object($create_transfer_recipient)){
                        
                        if($create_transfer_recipient->status == TRUE){

                            $recepient_code = $create_transfer_recipient->data->recipient_code;
                            $form_array = array(
                                'bank_name' => $bank_name,
                                'account_number' => $account_number,
                                'recepient_code' => $recepient_code
                            );
                            if($this->main_model->updateUserTable($form_array,$user_id)){
                                $response_arr['account_name'] = $account_number_test->data->account_name;
                                $response_arr['success'] = true;
                            }
                        }
                    }   
                }else{
                    $response_arr['invalid_account'] = true;
                }
            }
            
        }

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function processGetFormsForFundsWithdrawal(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        $response_arr = array('success' => false,'code' => '','account_number' => '');
        if(isset($post_data->show_records)){
            
            $response_arr['success'] = true;
            $response_arr['code'] = $this->data['bank_name'];
            $response_arr['account_number'] = $this->data['account_number'];
            
        }

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadFundsWithdrawalPage (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'funds_withdrawal';
        $props['page_title'] = $this->main_model->custom_echo("Funds Withdrawal",30);

        $props['total_income'] = $this->main_model->getUserParamById("total_income",$user_id);
        $props['withdrawn'] = $this->main_model->getUserParamById("withdrawn",$user_id);

        $props['balance'] = $props['total_income'] - $props['withdrawn'];
        $props['balance_str'] = number_format($props['balance'],2);

        $props['providus_account_number'] = $this->main_model->getUserParamById("providus_account_number",$user_id);
        $props['providus_account_name'] = $this->main_model->getUserParamById("providus_account_name",$user_id);

        if($this->main_model->getIfTransactionPasswordHasBeenInputedByUser($user_id)){
            $props['transaction_password_inputed'] = true;
        }else{
            $props['transaction_password_inputed'] = false;
        }

        $banks_arr = $this->main_model->paystackCurl("https://api.paystack.co/bank",FALSE);
            
        $banks_arr = json_decode($banks_arr);
        $props['banks_arr'] = $banks_arr;
        
        return Inertia::render('FundsWithdrawal',$props);
        
    }


    public function processGetUsersEmail(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'email' => '');

        if(isset($post_data->show_records)){
            $response_arr['success'] = true;
            $email = $this->main_model->getUserParamById("email",$user_id);
            $response_arr['email'] = $email;
        }

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }



    public function processVerifyTransferOtp(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
            
        $response_arr = array('success' => false,'messages' => array(),'incomplete_detais' => false,'amount_not_numeric' => false,'amount_too_small' => false,'not_bouyant' => false,'invalid_recipient' => false,'incorrect_otp' => false);

       
        
        $validationRules = [
            'transaction_password' => 'required',
            
        ];

        $messages = [];

        $validation = Support_Request::validate($validationRules);

        
        if($validation){
            
            if(isset($post_data->transaction_password) && isset($post_data->amount) && isset($post_data->recepient_id)){
                $amount = $post_data->amount;
                $recepient_id = $post_data->recepient_id; 
                $otp = $post_data->transaction_password;

                if(is_numeric($amount)){
                    if($amount >= 200){
                        $total_income = $this->main_model->getUserParamById("total_income",$user_id);
                        $withdrawn = $this->main_model->getUserParamById("withdrawn",$user_id);
                        $available_income = $total_income - $withdrawn;
                        if($amount <= $available_income){
                            
                            if($this->main_model->checkIfUserIdIsValid($recepient_id)){
                                
                                $hashed_otp = sha1($otp);

        
                                if($hashed_otp == $this->data['transaction_password']){
                            
                                    $date = date("j M Y");
                                    $time = date("h:i:sa");
                                    if($this->main_model->transferFundsToUser($user_id,$recepient_id,$amount,$date,$time)){
                                        session_unset();
                                        $response_arr['success'] = true;
                                    }
                                }else{
                                    $response_arr['incorrect_otp'] = true;
                                }
                        
                            }else{
                                $response_arr['invalid_recipient'] = true;
                            }
                        }else{
                            $response_arr['not_bouyant'] = true;
                        }
                        
                    }else{
                        $response_arr['amount_too_small'] = true;
                    }
                }else{
                    $response_arr['amount_not_numeric'] = true;
                }
            }else{
                $response_arr['incomplete_detais'] = true;
            }
            
        }
        
        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function processSendTransferOtp(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'email' => '');

        if(isset($post_data->show_records)){
            
            $email = $this->main_model->getUserParamById("email",$user_id);
            $response_arr['email'] = $email;

            $otp = rand ( 10000 , 99999 );
            $email_arr = array($email);
            // if($this->meetglobal_model->sendOtpEmail($email_arr,$otp)){
                // $this->session->set_userdata('otp',$otp);
                // $this->session->set_userdata('transfer_first_step',$user_name);
                //Change This Once In Development
                $response_arr['success'] = true;
                // if($_SERVER['SERVER_NAME'] == "localhost"){
                //  // $response_arr['otp'] = $otp;
                // }
            // }    
        }

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function processTransferFundsToUser(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'not_bouyant' => false,'too_small' => false,'recepient_does_not_exist' => true,'recepient_fullname' => '','users_id' => '');
        if(isset($post_data->amount) && isset($post_data->recepient_username)){
            
            $amount = $post_data->amount;
            $recepient_username = $post_data->recepient_username;
            if(is_numeric($amount)){
                if($amount >= 200){
                    $available_income = $this->data['total_income'] - $this->data['withdrawn'];
                    if($amount <= $available_income){
                        
                        if($this->main_model->checkIfUserNameExists($recepient_username)){
                            $response_arr['success'] = true;
                            $recepient_id = $this->main_model->getUserIdByName($recepient_username);
                            $response_arr['users_id'] = $recepient_id;
                            $recepient_fullname = $this->main_model->getUserFullNameByUserId($recepient_id);
                            $response_arr['recepient_does_not_exist'] = false;
                            $response_arr['phone_number'] = $this->data['phone'];
                            $response_arr['code'] = $this->data['phone_code'];
                            $response_arr['recepient_fullname'] = $recepient_fullname;
                        }
                        
                    }else{
                        $response_arr['not_bouyant'] = true;
                    }
                }else{
                    $response_arr['too_small'] = true;
                }
            }
            
        }

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadFundsTransferPage (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'funds_transfer';
        $props['page_title'] = $this->main_model->custom_echo("Funds Transfer",30);

        $props['total_income'] = $this->main_model->getUserParamById("total_income",$user_id);
        $props['withdrawn'] = $this->main_model->getUserParamById("withdrawn",$user_id);

        $props['balance'] = $props['total_income'] - $props['withdrawn'];
        $props['balance_str'] = number_format($props['balance'],2);

        $props['providus_account_number'] = $this->main_model->getUserParamById("providus_account_number",$user_id);
        $props['providus_account_name'] = $this->main_model->getUserParamById("providus_account_name",$user_id);

        if($this->main_model->getIfTransactionPasswordHasBeenInputedByUser($user_id)){
            $props['transaction_password_inputed'] = true;
        }else{
            $props['transaction_password_inputed'] = false;
        }

        
        return Inertia::render('FundsTransfer',$props);
        
    }

    public function submitProofOfPaymentToAdminInsideApp(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'url' => '','messages' => '',"errors" => "","empty" => true,"only_one_image" => false);

                            
                    
        if(!empty($_FILES['image']['name'])){
            $response_arr['empty'] = false;
            $image_files_count = count(array($_FILES['image']['name']));
            
            if($image_files_count == 1){
                $response_arr['only_one_image'] = true;

                // $req->validate([
                // 'image' => 'required|mimes:csv,txt,xlx,xls,pdf|max:2048'
                // ]);

                $validationRules = [
                    'image' => 'required|mimes:png,jpeg,jpg,gif,webp|max:4000',
                    'amount' => 'required|numeric',
                    'depositors_name' => 'required|max:150',
                    'date_of_payment' => 'required',
                    
                ];

                $messages = [];

                $validation = Support_Request::validate($validationRules);
        
                
                if($validation){
                    
                    // $fileModel = new FileUpload;

                    // echo "string";
                    if($req->file('image')) {
                        // echo "string";
                        $image = $req->file('image');

                        if($_SERVER['SERVER_NAME'] == "127.0.0.1"){
                            $fileName = time().'_'.$image->getClientOriginalName();
                            $filePath = $image->storePubliclyAs('/public/images', $fileName);
                            $extension = $image->getClientOriginalExtension();
                            $filePath = str_replace('public', 'storage', $filePath);

                            $file = public_path($filePath);
                        }else{
                            $fileName = time().'_'.$image->getClientOriginalName();
                            $filePath = $image->storeAs('images', $fileName,'public_uploads');
                            // $path = $request->photo->storeAs('images', 'filename.jpg', 'public_uploads');
                            // echo $filePath . "<br>";
                            $extension = $image->getClientOriginalExtension();
                            $filePath = str_replace('public', 'storage', $filePath);

                            $file = base_path('storage/' .$filePath);
                        }
                        if( file_exists($file)){

                            
                            // $response_arr['image_name'] = $fileName;
                             
                            // $response_arr['success'] = true;

                            $amount = $post_data->amount;
                            $depositors_name = $post_data->depositors_name;
                            $date_of_payment = $post_data->date_of_payment;
                            

                            $form_array = array(
                                'user_id' => $user_id,
                                'user_name' => $this->data['user_name'],
                                'amount' => $amount,
                                'depositors_name' => $depositors_name,
                                'date_of_payment' => $date_of_payment,
                                'image' => $fileName,
                                'date' => $date,
                                'time' => $time
                            );

                            

                            if($this->main_model->addPaymentProofRequest($form_array)){
                                $response_arr['success'] = true;
                                
                            }else{
                                $file_path = public_path().'/storage/images/'.$fileName;
                                unlink($file_path);
                            }
                               
                        
                        }else{
                            $response_arr['image_errors'] = 'We ran into some errors when uploading your file';
                        }
                    }
                }
            }
        }
        
        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    } 

    public function loadCreditUserWalletPage (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'credit_user_wallet';
        $props['page_title'] = $this->main_model->custom_echo("Credit Wallet",30);

        $props['total_income'] = $this->main_model->getUserParamById("total_income",$user_id);
        $props['withdrawn'] = $this->main_model->getUserParamById("withdrawn",$user_id);

        $props['balance'] = $props['total_income'] - $props['withdrawn'];
        $props['balance_str'] = number_format($props['balance'],2);

        $props['providus_account_number'] = $this->main_model->getUserParamById("providus_account_number",$user_id);
        $props['providus_account_name'] = $this->main_model->getUserParamById("providus_account_name",$user_id);


        
        return Inertia::render('CreditWallet',$props);
        
    }

    public function processTestUpload (Request $req){
        
        
        $response_arr = array('success' => false,'messages' => array(),"image_errors" => "","image_empty" => true,"image_only_one_image" => false,'image_name' => '');
        $post_data = (Object) $req->input(); 

        if(!empty($_FILES['image']['name'])){
            $response_arr['image_empty'] = false;
            $image_files_count = count(array($_FILES['image']['name']));
            
            if($image_files_count == 1){
                $response_arr['image_only_one_image'] = true;

                // $req->validate([
                // 'image' => 'required|mimes:csv,txt,xlx,xls,pdf|max:2048'
                // ]);

                $validationRules = [
                    'image' => 'required|mimes:png,jpeg,jpg,gif,webp|max:2048'
                    
                ];

                $messages = [];

                $validation = Support_Request::validate($validationRules);
        
                
                if($validation){
                    
                    // $fileModel = new FileUpload;

                    // echo "string";
                    if($req->file('image')) {
                        // echo "string";
                        $image = $req->file('image');

                        $fileName = time().'_'.$image->getClientOriginalName();
                        $filePath = $image->storePubliclyAs('/public/images', $fileName);
                        $extension = $image->getClientOriginalExtension();
                        $filePath = str_replace('public', 'storage', $filePath);

                        $file = public_path($filePath);

                        if( file_exists($file)){

                            
                            $response_arr['image_name'] = $fileName;
                             
                            $response_arr['success'] = true;
                               
                        
                        }else{
                            $response_arr['image_errors'] = 'We ran into some errors when uploading your file';
                        }
                    }
                }
            }
        }
        
        $response_arr = json_encode($response_arr);
        // return $response_arr;

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
        
    }

    public function loadTestUploadPage (Request $req){
        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props = [];

        

        return Inertia::render('TestUpload',$props);
        
    }

    public function loadWalletStatementPage (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'wallet_statement';
        $props['page_title'] = $this->main_model->custom_echo("E-Wallet Statement",30);

        $props['amount'] = $req->query('amount');
        $props['balance'] = $req->query('balance');
        $props['summary'] = $req->query('summary');
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        if(empty($props['balance'])){
            $props['balance'] = "";
        }

        if(empty($props['summary'])){
            $props['summary'] = "";
        }

        
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getAdminAccountStatementForThisUserByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                        
                        
                $date = $row->date;
                $time = $row->time;
                $summary = $row->summary;
                $amount_after = $row->amount_after;
                $amount_before = $row->amount_before;
                

                $balance = $amount_after;

                if($amount_after > $amount_before){
                  $row->amount_str = "<span class='text-success'>+".number_format($amount,2)."</span>";
                }else if($amount_after < $amount_before){
                  $row->amount_str = "<span class='text-danger'>-".number_format($amount,2)."</span>";
                }else{
                  $row->amount_str = "<span class='text-primary'>0</span>";
                }


                $row->balance_str = number_format($balance,2);

                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        return Inertia::render('WalletStatement',$props);
        
    }

    public function loadFundsWithdrawalHistoryPage (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'funds_withdrawal_history';
        $props['page_title'] = $this->main_model->custom_echo("Withdrawal History",30);

        $props['amount'] = $req->query('amount');
        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getUsersWithdrawalHistoryPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $date = $row->date;
                $time = $row->time;
                
                $row->amount_str = number_format($amount,2);

                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        return Inertia::render('WithdrawalHistory',$props);
        
    }

    public function loadFundsTransferHistoryPage (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'transfer_history';
        $props['page_title'] = $this->main_model->custom_echo("Transfer History",30);

        $props['amount'] = $req->query('amount');
        
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        
        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getTransferHistoryForThisUserByPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $amount = $row->amount;
                $row->date = $row->date;
                $row->time = $row->time;
                $sender = $row->sender;
                $recepient = $row->recepient;

                $row->user_id = $user_id;
                
                
                // $dob = $row->dob;
                // echo $sender;
                if($sender == $user_id){
                  $row->sender_full_name = "you";
                  $row->sender_username = "you";
                  $row->amount_str = "<span class='text-danger'>₦" .number_format($amount,2) . "</span>";
                }else{
                  $row->sender_username = $this->main_model->getUserNameById($sender);
                  $row->sender_full_name = $this->main_model->getUserParamById("full_name",$sender);
                  $row->sender_slug = $this->main_model->getUserParamById("slug",$sender);
                  // $row->sender_full_name = "<a target='_blank' href='".site_url('meetglobal/'.$row->sender_slu)."'>".$row->sender_full_name."</a>";
                  
                }

                if($recepient == $user_id){
                  $row->recepient_full_name = "you";
                  $row->recepient_username = "you";
                  $row->amount_str = "<span class='text-success'>₦" .number_format($amount,2) . "</span>";
                }else{
                  $row->recepient_username = $this->main_model->getUserNameById($recepient);
                  $row->recepient_full_name = $this->main_model->getUserParamById("full_name",$recepient);
                  $row->recepient_slug = $this->main_model->getUserParamById("slug",$recepient);
                  // $row->recepient_full_name = "<a target='_blank' href='".site_url('meetglobal/'.$row->recepient_slug)."'>".$row->recepient_full_name."</a>";
                  
                }



                

                // if(!$row->full_name){
                //     $row->full_name = "Nwogo David";
                // }
                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        return Inertia::render('TransferHistory',$props);
        
    }

    public function loadWalletCreditHistoryPage (Request $req){
        $user_id = $this->data['user_id'];
        
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }

        
        
        $props['active_page'] = 'wallet_credit_history';
        $props['page_title'] = $this->main_model->custom_echo("Wallet Credit History",30);

        $props['amount'] = $req->query('amount');
        $props['payment_option'] = $req->query('payment_option');
        $props['reference'] = $req->query('reference');
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');



        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        if(empty($props['payment_option'])){
            $props['payment_option'] = "";
        }

        if(empty($props['reference'])){
            $props['reference'] = "";
        }

        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getUsersAccountCreditHistoryPagination($user_id,$req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $amount = $row->amount;
                $payment_option = $row->payment_option;
                $reference = $row->reference;
                $date = $row->date;
                $time = $row->time;
                $row->date_time = $date . " " . $time;
                
                $row->amount_str = number_format($amount,2);
                $row->index = $index;                           
            }
        }



        $props['all_history'] = $all_history;
        $props['length'] = $length;

        return Inertia::render('WalletCreditHistory',$props);
        
    }

    public function loadUsersCarAwardBonusEarningsPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s Car Award Bonus",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');




            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getMlmHistoryCarAwardIncomeByPagination($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $amount = $row->amount;
                    $vat = $row->vat;
                    $mlm_db_id = $row->mlm_db_id;
                    $user_id = $row->user_id;
                    $index1 = $this->main_model->getMlmIdsIndexNumber($mlm_db_id);
                    $user_name = $this->main_model->getUserParamById("user_name",$user_id);
                    $row->income_source = $user_name . " (".$index1.")";

                    $amount = $amount - (($vat / 100) * $amount);
                    
                    
                    $date = $row->date;
                    $time = $row->time;
                    $row->date_time = $date . " " . $time;
                    
                    $row->amount_str = number_format($amount,2);
                    $row->index = $index;                           
                }
            }

            $car_award_earnings = $this->main_model->getUsersCarAwardEarnings($users_user_id);
            $props['car_award_earnings'] = "Total Amount: ₦". number_format($car_award_earnings,2);


            $props['all_history'] = $all_history;
            $props['length'] = $length;

            return Inertia::render('Admin/UsersCarAwardBonusEarnings',$props);
        }
        
    }


    public function loadUsersSgpsBonusEarningsPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s SGPS Bonus",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');




            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getMlmHistorySGPSIncomeByPagination($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $amount = $row->amount;
                    $vat = $row->vat;
                    $mlm_db_id = $row->mlm_db_id;
                    $user_id = $row->user_id;
                    $index1 = $this->main_model->getMlmIdsIndexNumber($mlm_db_id);
                    $user_name = $this->main_model->getUserParamById("user_name",$user_id);
                    $row->income_source = $user_name . " (".$index1.")";

                    $amount = $amount - (($vat / 100) * $amount);
                    
                    
                    $date = $row->date;
                    $time = $row->time;
                    $row->date_time = $date . " " . $time;
                    
                    $row->amount_str = number_format($amount,2);
                    $row->index = $index;                           
                }
            }

            $sgps_income = $this->main_model->getUsersSGPSIncome($users_user_id);
            $props['sgps_income'] = "Total Amount: ₦". number_format($sgps_income,2);
            $props['all_history'] = $all_history;
            $props['length'] = $length;

            return Inertia::render('Admin/UsersSgpsBonusEarnings',$props);
        }
        
    }

    public function loadUsersVtuTradeBonusEarningsPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s VTU Trade Bonus",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');




            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getMlmHistoryVTUTradeIncomeByPagination($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $amount = $row->amount;
                    $vat = $row->vat;
                    $mlm_db_id = $row->mlm_db_id;
                    $user_id = $row->user_id;
                    $index1 = $this->main_model->getMlmIdsIndexNumber($mlm_db_id);
                    $user_name = $this->main_model->getUserParamById("user_name",$user_id);
                    $row->income_source = $user_name . " (".$index1.")";

                    $amount = $amount - (($vat / 100) * $amount);
                    
                    
                    $date = $row->date;
                    $time = $row->time;
                    $row->date_time = $date . " " . $time;
                    
                    $row->amount_str = number_format($amount,2);
                    $row->index = $index;                           
                }
            }

            $vtu_trade_income = $this->main_model->getUsersVtuTradeIncome($users_user_id);
            $props['vtu_trade_income'] = "Total Amount: ₦". number_format($vtu_trade_income,2);
            $props['all_history'] = $all_history;
            $props['length'] = $length;

            return Inertia::render('Admin/UsersVTUTradeBonusEarnings',$props);
        }
        
    }

    public function loadUsersTradeBonusEarningsPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s Trade Bonus",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');




            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getMlmHistoryTradeDeliveryIncomeByPagination($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $amount = $row->amount;
                    $vat = $row->vat;
                    $mlm_db_id = $row->mlm_db_id;
                    $user_id = $row->user_id;
                    $index1 = $this->main_model->getMlmIdsIndexNumber($mlm_db_id);
                    $user_name = $this->main_model->getUserParamById("user_name",$user_id);
                    $row->income_source = $user_name . " (".$index1.")";

                    $amount = $amount - (($vat / 100) * $amount);
                    
                    
                    $date = $row->date;
                    $time = $row->time;
                    $row->date_time = $date . " " . $time;
                    
                    $row->amount_str = number_format($amount,2);
                    $row->index = $index;                           
                }
            }

            
            $props['all_history'] = $all_history;
            $props['length'] = $length;

            $trade_delivery_income = $this->main_model->getUsersTradeDeliveryIncome($user_id);
            $props['trade_delivery_income'] =  "<h4 class='text-primary' style='font-weight: bold;'>Total Amount: ₦". number_format($trade_delivery_income,2). " </h4>";

            return Inertia::render('Admin/UsersTradeBonusEarnings',$props);
        }
        
    }

    public function loadUsersCenterLeaderSelectionBonusEarningsPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s Center Leader Selection Bonus",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            
            

            $center_leader_selection_income = $this->main_model->getUserParamById("center_leader_selection_income",$users_user_id);
             
            
            $props['center_leader_selection_income'] = '<p>₦'.number_format($center_leader_selection_income,2).'</p>';

            return Inertia::render('Admin/UsersCenterLeaderSelectionBonusEarnings',$props);
        }
        
    }

    public function loadUsersCenterLeaderPlacementBonusEarningsPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s Center Leader Placement Bonus",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');




            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getMlmHistoryCenterLeaderPlacementByPagination($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $amount = $row->amount;
                    $vat = $row->vat;
                    $mlm_db_id = $row->mlm_db_id;
                    $user_id = $row->user_id;
                    $index1 = $this->main_model->getMlmIdsIndexNumber($mlm_db_id);
                    $user_name = $this->main_model->getUserParamById("user_name",$user_id);
                    $row->income_source = $user_name . " (".$index1.")";

                    $amount = $amount - (($vat / 100) * $amount);
                    
                    
                    $date = $row->date;
                    $time = $row->time;
                    $row->date_time = $date . " " . $time;
                    
                    $row->amount_str = number_format($amount,2);
                    $row->index = $index;                           
                }
            }

            
            $props['all_history'] = $all_history;
            $props['length'] = $length;
            $center_leader_placement_bonus = $this->main_model->getUsersCenterLeaderPlacementBonus($user_id);
            $props['center_leader_placement_bonus'] =  "<h4 class='text-primary' style='font-weight: bold;'>Total Amount: ₦". number_format($center_leader_placement_bonus,2). " </h4>";

            return Inertia::render('Admin/UsersCenterLeaderPlacementBonusEarnings',$props);
        }
        
    }

    public function loadUsersCenterLeaderSponsorBonusEarningsPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s Center Leader Sponsor Bonus",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');




            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getMlmHistoryCenterLeaderSponsorByPagination($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $amount = $row->amount;
                    $vat = $row->vat;
                    $mlm_db_id = $row->mlm_db_id;
                    $user_id = $row->user_id;
                    $index1 = $this->main_model->getMlmIdsIndexNumber($mlm_db_id);
                    $user_name = $this->main_model->getUserParamById("user_name",$user_id);
                    $row->income_source = $user_name . " (".$index1.")";

                    $amount = $amount - (($vat / 100) * $amount);
                    
                    
                    $date = $row->date;
                    $time = $row->time;
                    $row->date_time = $date . " " . $time;
                    
                    $row->amount_str = number_format($amount,2);
                    $row->index = $index;                           
                }
            }

            
            $props['all_history'] = $all_history;
            $props['length'] = $length;
            $center_leader_sponsor_bonus = $this->main_model->getUsersCenterLeaderSponsorBonus($user_id);
            $props['center_leader_sponsor_bonus'] =  "<h4 class='text-primary' style='font-weight: bold;'>Total Amount: ₦". number_format($center_leader_sponsor_bonus,2). " </h4>";

            return Inertia::render('Admin/UsersCenterLeaderSponsorBonusEarnings',$props);
        }
        
    }

    public function loadUsersPlacementEarningsPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s Placement Earnings",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');




            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getMlmHistoryPlacementByPagination($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $amount = $row->amount;
                    $vat = $row->vat;
                    $mlm_db_id = $row->mlm_db_id;
                    $user_id = $row->user_id;
                    $index1 = $this->main_model->getMlmIdsIndexNumber($mlm_db_id);
                    $user_name = $this->main_model->getUserParamById("user_name",$user_id);
                    $row->income_source = $user_name . " (".$index1.")";

                    $amount = $amount - (($vat / 100) * $amount);
                    
                    
                    $date = $row->date;
                    $time = $row->time;
                    $row->date_time = $date . " " . $time;
                    
                    $row->amount_str = number_format($amount,2);
                    $row->index = $index;                           
                }
            }

            
            $props['all_history'] = $all_history;
            $props['length'] = $length;

            $business_placement_earnings = $this->main_model->getUsersMlmBusinessPlacementEarnings($user_id);
            $props['business_placement_earnings'] =  "<h4 class='text-primary' style='font-weight: bold;'>Total Amount: ₦". number_format($business_placement_earnings,2). " </h4>";

            return Inertia::render('Admin/UsersPlacementEarnings',$props);
        }
        
    }

    public function loadUsersSponsorEarningsPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s Sponsor Earnings",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');




            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getMlmHistorySponsorByPagination($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $amount = $row->amount;
                    $vat = $row->vat;
                    $mlm_db_id = $row->mlm_db_id;
                    $user_id = $row->user_id;
                    $index1 = $this->main_model->getMlmIdsIndexNumber($mlm_db_id);
                    $user_name = $this->main_model->getUserParamById("user_name",$user_id);
                    $row->income_source = $user_name . " (".$index1.")";

                    $amount = $amount - (($vat / 100) * $amount);
                    
                    
                    $date = $row->date;
                    $time = $row->time;
                    $row->date_time = $date . " " . $time;
                    
                    $row->amount_str = number_format($amount,2);
                    $row->index = $index;                           
                }
            }

            
            $props['all_history'] = $all_history;
            $props['length'] = $length;

            $business_sponsor_earnings = $this->main_model->getUsersMlmBusinessSponsorEarnings($user_id);
            $props['business_sponsor_earnings'] =  "<h4 class='text-primary' style='font-weight: bold;'>Total Amount: ₦". number_format($business_sponsor_earnings,2). " </h4>";

            return Inertia::render('Admin/UsersSponsorEarnings',$props);
        }
        
    }

    public function loadUsersAccountStatementPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s Acct Statement",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            $props['amount'] = $req->query('amount');
            $props['balance'] = $req->query('balance');
            $props['summary'] = $req->query('summary');

            
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');


            if(empty($props['amount'])){
                $props['amount'] = "";
            }

            if(empty($props['balance'])){
                $props['balance'] = "";
            }

            if(empty($props['summary'])){
                $props['summary'] = "";
            }


            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getAdminAccountStatementForThisUser($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $amount = $row->amount;
                        
                        
                    $date = $row->date;
                    $time = $row->time;
                    $summary = $row->summary;
                    $amount_after = $row->amount_after;
                    $amount_before = $row->amount_before;
                    $row->date_time = $date . " " . $time;

                    $row->balance = $amount_after;

                    if($amount_after > $amount_before){
                      $row->amount = "<span class='text-success'>+".number_format($amount,2)."</span>";
                    }else if($amount_after < $amount_before){
                      $row->amount = "<span class='text-danger'>-".number_format($amount,2)."</span>";
                    }else{
                      $row->amount = "<span class='text-primary'>0</span>";
                    }
                    
                    $row->balance_str = number_format($row->balance,2);
                    $row->index = $index;                           
                }
            }

            
            $props['all_history'] = $all_history;
            $props['length'] = $length;

            return Inertia::render('Admin/UsersAccountStatement',$props);
        }
        
    }

    public function loadUsersproductAdvanceHistoryPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s Product Advance Hist.",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            $props['amount'] = $req->query('amount');
            $props['amount_paid'] = $req->query('amount_paid');
            $props['status'] = $req->query('status');

            
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');


            if(empty($props['amount'])){
                $props['amount'] = "";
            }

            if(empty($props['amount_paid'])){
                $props['amount_paid'] = "";
            }

            if(empty($props['status'])){
                $props['status'] = "all";
            }


            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getUsersProductAdvanceHistory($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $summary = $row->summary;
                    $amount = $row->amount;
                    $amount_paid = $row->amount_paid;
                    $balance = $amount - $amount_paid;
                    $last_date_time_paid = $row->last_date_time_paid;
                  
                    $user_id = $row->user_id;
                    $service_charge = $row->service_charge;
                    $date_time = $row->date_time;

                    $charged_num = $row->charged_num;
                    $row->total_amount_charge = $charged_num * $service_charge;
                    $last_service_charge_date = $row->last_service_charge_date;

                    $row->profit_made = 0;
                    if($amount == $amount_paid){
                      $row->profit_made = ($charged_num * $service_charge);
                    }



                    $row->status = "";
                    if($amount == $amount_paid){
                      $row->status = "Cleared";
                    }else{
                      $row->status = "Pending";
                    }

                    $row->full_name = $this->main_model->getUserParamById("full_name",$user_id);
                    $row->user_name = $this->main_model->getUserParamById("user_name",$user_id);
                    $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);

                    $row->amount_str = number_format($amount,2);
                    $row->amount_paid_str = number_format($amount_paid,2);
                    $row->balance_str = number_format($balance,2);
                    $row->service_charge_str = number_format($service_charge,2);
                    $row->profit_made_str = number_format($row->profit_made,2);
                    $row->total_amount_charge_str = number_format($row->total_amount_charge,2);
                    
                    $row->index = $index;                           
                }
            }

            $props['total_amount_requested'] = number_format($this->main_model->getTotalAmountRequestedAdvanceLoanForUser($user_id),2);
            $props['total_amount_paid_back'] = number_format($this->main_model->getTotalAmountPaidBackAdvanceLoanForUser($user_id),2);
            $props['total_balance'] = number_format($this->main_model->getTotalBalanceAdvanceLoanForUser($user_id),2);
            $props['total_profit_made'] = number_format($this->main_model->getTotalProfitMadeForUser($user_id),2);

            $props['all_history'] = $all_history;
            $props['length'] = $length;

            return Inertia::render('Admin/UsersProductAdvanceHistory',$props);
        }
        
    }

    public function loadUsersAdminDebitHistoryPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s Admin Debit His.",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            $props['amount'] = $req->query('amount');
            
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');


            if(empty($props['amount'])){
                $props['amount'] = "";
            }


            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getAdminDebitHistoryForThisUser($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $amount = $row->amount;
                        

                    $row->amount_str = number_format($amount,2);
                    
                    $row->index = $index;                           
                }
            }

            

            $props['all_history'] = $all_history;
            $props['length'] = $length;

            return Inertia::render('Admin/UsersAdminDebitHistory',$props);
        }
        
    }

    public function loadUsersAdminCreditHistoryPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s Admin Credit His.",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            $props['amount'] = $req->query('amount');
            
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');


            if(empty($props['amount'])){
                $props['amount'] = "";
            }


            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getAdminCreditHistoryForThisUser($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $amount = $row->amount;
                        

                    $row->amount_str = number_format($amount,2);
                    
                    $row->index = $index;                           
                }
            }

            

            $props['all_history'] = $all_history;
            $props['length'] = $length;

            return Inertia::render('Admin/UsersAdminCreditHistory',$props);
        }
        
    }

    public function loadUsersTransferHistoryPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s Funds Transfer His.",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            $props['amount'] = $req->query('amount');
            
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');


            if(empty($props['amount'])){
                $props['amount'] = "";
            }


            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getTransferHistoryForThisUser($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $id = $row->id;
                    $amount = $row->amount;
                    $date = $row->date;
                    $time = $row->time;
                    $sender = $row->sender;
                    $recepient = $row->recepient;


                    
                    
                    // $dob = $row->dob;
                    if($sender == $user_id){
                      $row->sender_full_name = "you";
                      $row->sender_username = "you";
                    }else{
                      $row->sender_username = $this->main_model->getUserNameById($sender);
                      $row->sender_full_name = $this->main_model->getUserParamById("full_name",$sender);
                      $row->sender_slug = $this->main_model->getUserParamById("slug",$sender);
                      // $row->sender_full_name = "<a target='_blank' href='".site_url('meetglobal/'.$sender_slug)."'>".$sender_full_name."</a>";
                    }

                    if($recepient == $user_id){
                      $row->recepient_full_name = "you";
                      $row->recepient_username = "you";
                    }else{
                      $row->recepient_username = $this->main_model->getUserNameById($recepient);
                      $row->recepient_full_name = $this->main_model->getUserParamById("full_name",$recepient);
                      $row->recepient_slug = $this->main_model->getUserParamById("slug",$recepient);
                      // $row->recepient_full_name = "<a target='_blank' href='".site_url('meetglobal/'.$recepient_slug)."'>".$recepient_full_name."</a>";
                    }

                    $row->amount_str = number_format($amount,2);
                    
                    $row->index = $index;                           
                }
            }

            

            $props['all_history'] = $all_history;
            $props['length'] = $length;

            return Inertia::render('Admin/UsersTransferHistory',$props);
        }
        
    }

    public function trackPayscribeEducationalEpin(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'messages' => '');
        if(isset($post_data->show_records) && isset($post_data->order_id)){
            $order_id = $post_data->order_id;
            
            
            
            $url = "https://www.payscribe.ng/api/v1/epins/retrieve?trans_id=".$order_id;
            $use_post = false;

            $response = $this->main_model->payscribeVtuCurl($url, $use_post, $post_data = []);


            if($this->main_model->isJson($response)){
                $response = json_decode($response);
                $response_arr['response'] = $response;
                // var_dump($response);
                if($response->status && $response->status_code == 200){
                    
                    $details = $response->message->details;

                    if(is_array($details)){
                
                
                        $response_arr['success'] = true;

                        $j = 0;
                        $response_arr['messages'] .= "<div class='container'>";
                        for($i = 0; $i < count($details); $i++){
                            $j++;
                            $pin = $details[$i]->pin;
                            $date_purchased = $details[$i]->date_purchased;
                            $name = $details[$i]->name;
                            $response_arr['messages'] .= "<div style='margin-bottom: 15px;'>";
                            $response_arr['messages'] .= "<p><b>NAME: </b>".$name."</p>";
                            $response_arr['messages'] .= "<p><b>PIN: </b>".$pin."</p>";
                            $response_arr['messages'] .= "<p><b>Date Purchased: </b>".$date_purchased."</p>";
                            // $response_arr['messages'] .= "<p class='col-6'>".$code."</p>";
                            $response_arr['messages'] .= "</div>";
                        }

                        $response_arr['messages'] .= "</div>";
                    }

                }
            }   

        }


        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function trackPayscribeVtuEpin(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'messages' => '','amount' => '', 'epins_json' => ''); 
        if(isset($post_data->show_records) && isset($post_data->order_id)){
            $order_id = $post_data->order_id;
            
            
            
            $url = "https://www.payscribe.ng/api/v1/rechargecardpins?trans_id=".$order_id;
            $use_post = false;

            $response = $this->main_model->payscribeVtuCurl($url, $use_post, $post_data = []);


            if($this->main_model->isJson($response)){
                $response = json_decode($response);
                // var_dump($response);
                if($response->status && $response->status_code == 200){
                    
                    $details = $response->message->details;
                    $amount = $response->message->amount;

                    if(is_array($details)){
                
                
                        $response_arr['success'] = true;

                        $j = 0;
                        $response_arr['messages'] .= "<div class='container'>";
                        for($i = 0; $i < count($details); $i++){
                            $j++;
                            $pin = $details[$i]->pin;
                            $code = $details[$i]->code;
                            $response_arr['messages'] .= "<div class='row' style='margin-bottom: 5px;'>";
                            $response_arr['messages'] .= "<p class='col-1'>".$j."</p>";
                            $response_arr['messages'] .= "<p class='col-11'>".$pin."</p>";
                            // $response_arr['messages'] .= "<p class='col-6'>".$code."</p>";
                            $response_arr['messages'] .= "</div>";
                        }

                        $response_arr['messages'] .= "</div>";
                        $response_arr['epins_json'] = json_encode($details);
                        $response_arr['amount'] = $amount;
                    }

                }
            }   

            
            
        }


        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function trackEminenceVtuOrder(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'messages' => '');
        if(isset($post_data->show_records) && isset($post_data->order_id)){
            $order_id = $post_data->order_id;
            $order_id_cut = substr($order_id, 0,2);
            
            if($order_id_cut == "TT" || $order_id_cut == "TC"){
                $order_id = substr($order_id, 2);
            }
            
            
            $url = "https://app.eminencesub.com/api/show-transaction/".$order_id;
            $use_post = false;

            $response = $this->main_model->eminenceVtuCurl($url,$use_post,$post_data = []);
            // echo $response;

            if($this->main_model->isJson($response)){
                $response = json_decode($response);
                if(is_object($response)){
                    if($response->status == 200){
                            

                        // var_dump($response->message->details);
                        $details = $response->data;
                        $response_arr['success'] = true;

                        $description = $details->description;
                        
                        
                        $amount = $details->amount;
                        $description = $details->description;
                        $transaction_status = $details->status;
                        $amount_paid = $this->main_model->getVtuTransactionParamByOrderId("amount",$order_id);
                        $refunded_status = $this->main_model->getVtuTransactionParamByOrderId("refunded",$order_id);
                        $table_id = $this->main_model->getVtuTransactionParamByOrderId("id",$order_id);





                        $response_arr['messages'] .= 'Transaction Status: <em class="text-primary">' . $transaction_status . '</em><br>';
                        

                        if($order_id_cut == "TC"){
                            $pin = $details->pin;

                            $response_arr['messages'] .= 'Pin: <em class="text-primary">' . $pin . '</em><br>';
                        }
                        
                        // $response_arr['messages'] .= 'Amount: <em class="text-primary">' . number_format($amount,2) . '</em><br>';
                    
                        $response_arr['messages'] .= 'Description: <em class="text-primary">' . $description . '</em><br>';
                        
                        
                    }
                }
            }   

            
            
        }
            

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function trackPayscribeVtuOrderData(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'messages' => '');
        if(isset($post_data->show_records) && isset($post_data->order_id)){
            $order_id = $post_data->order_id;
            
            
            
            $url = "https://www.payscribe.ng/api/v1/report?trans_id=".$order_id;
            $use_post = true;

            $response = $this->main_model->payscribeVtuCurl($url,$use_post,$post_data = []);
            // echo $response;

            if($this->main_model->isJson($response)){
                $response = json_decode($response);
                if(is_object($response)){
                    if($response->status){
                            
                        // var_dump($response->message->details);
                        $details = $response->message->details;
                        $response_arr['success'] = true;

                        $description = $details->description;
                        
                        
                        $amount = $details->amount;
                        $date_initiated = $details->date_initiated;
                        $transaction_status = $details->transaction_status;
                        $amount_paid = $this->main_model->getVtuTransactionParamByOrderId("amount",$order_id);
                        $refunded_status = $this->main_model->getVtuTransactionParamByOrderId("refunded",$order_id);
                        $table_id = $this->main_model->getVtuTransactionParamByOrderId("id",$order_id);



                        // if($transaction_status == "refunded"){
                        //  if($refunded_status == 0){
                        //      $type = $this->meetglobal_model->getVtuTransactionParamByOrderId("type",$order_id);
                        //      $commision_amount = $this->meetglobal_model->getCommissionAmountPaidToUser($type,$amount_paid);
                        //      $summary = "VTU Transaction Refund For ORDER ID: ". $order_id;
                        //      if($this->meetglobal_model->creditUser($user_id,$amount_paid,$summary)){
                        //          $form_array = array(
                        //              "refunded" => 1
                        //          );
                        //          $this->meetglobal_model->updateVtuTable($form_array,$table_id);
                        //      }
                        //  }
                        // }

                        $response_arr['messages'] .= 'Transaction Status: <em class="text-primary">' . $transaction_status . '</em><br>';
                        $data = null;
                        if(isset($details->data)){
                            $data = $details->data;
                        }
                        

                        if(!is_null($data)){
                        
                            $type = $this->main_model->getVtuTransactionParamByOrderId("type",$order_id);
                            if($type == "electricity"){
                                if(isset($data->MeterType)){
                                    $MeterType = $data->MeterType;
                                    $response_arr['messages'] .= 'Meter Type: <em class="text-primary">' . $MeterType . '</em><br>';
                                    if($MeterType == "prepaid"){
                                        if(isset($data->Token)){
                                            $Token = $data->Token;
                                            $response_arr['messages'] .= 'Meter Token: <em class="text-primary">' . $Token . '</em><br>';
                                        }
                                    }
                                }
                                
                            }else if($type == "data"){

                            }
                        }

                        
                        // $response_arr['messages'] .= 'Amount: <em class="text-primary">' . number_format($amount,2) . '</em><br>';
                        $response_arr['messages'] .= 'Date Initiated: <em class="text-primary">' .$date_initiated . '</em><br>';
                        $response_arr['messages'] .= 'Description: <em class="text-primary">' . $description . '</em><br>';
                        
                        
                    }
                }
            }   

            
            
        }
            

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function trackClubVtuOrder(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'messages' => '');
        if(isset($post_data->show_records) && isset($post_data->order_id)){
            $order_id = $post_data->order_id;
            
            $url = "https://www.nellobytesystems.com/APIQueryV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&OrderID=".$order_id;
            $use_post = true;

            $response = $this->main_model->vtu_curl($url,$use_post,$post_data=[]);


            if($this->main_model->isJson($response)){
                $response = json_decode($response);
                if(is_object($response)){
                // var_dump($response->message->details);
                
                    $response_arr['success'] = true;
                    $description = "";
                    $date_initiated = "";
                    $status = "";
                    $remark = "";

                    if(isset($response->ordertype)){
                        $description = $response->ordertype;
                    }

                    if(isset($response->date)){
                        $date_initiated = $response->date;
                    }

                    if(isset($response->status)){
                        $status = $response->status;
                    }

                    if(isset($response->remark)){
                        $remark = $response->remark;
                    }
                    
                    

                    $vtu_type = $this->main_model->getVtuTransactionParamByOrderId("type",$order_id);

                    // echo $this->main_model->getVtuTransactionParamByOrderId("refunded",$order_id);

                    if(($status == "ORDER_CANCELLED" || $status == "ORDER_REFUNDED" || $status == "refunded") && $this->main_model->getVtuTransactionParamByOrderId("refunded",$order_id) == 0){
                        if($vtu_type != "cable tv"){
                            // $this->main_model->refundFundsAndMarkAsRefunded($order_id);
                        }
                    }

                    // $type = $this->main_model->getVtuTransactionParamByOrderId("type",$order_id);
                    // if($type == "data"){
                    //  $amount = round((0.04 * $amount) + $amount,2);
                    // }

                    $response_arr['messages'] .= 'Transaction Status: <em class="text-primary">' . $status . '</em><br>';
                    // $response_arr['messages'] .= 'Amount: <em class="text-primary">' . number_format($amount,2) . '</em><br>';
                    $response_arr['messages'] .= 'Date Initiated: <em class="text-primary">' .$date_initiated . '</em><br>';
                    $response_arr['messages'] .= 'Order Type: <em class="text-primary">' . $description . '</em><br>';
                    $response_arr['messages'] .= 'Remark: <em class="text-primary">' . $remark . '</em>';
                    
                }
            }   

            
            
        }
            

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadUsersVtuHistoryPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s VTU Hist.",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            $props['type'] = $req->query('type');
            $props['sub_type'] = $req->query('sub_type');
            $props['order_id'] = $req->query('order_id');
            $props['number'] = $req->query('number');
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');


            if(empty($props['type'])){
                $props['type'] = "";
            }

            if(empty($props['sub_type'])){
                $props['sub_type'] = "";
            }

           
            if(empty($props['order_id'])){
                $props['order_id'] = "";
            }

            if(empty($props['number'])){
                $props['number'] = "";
            }


            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getVtuTransactionHistoryForThisUser($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $id = $row->id;
                    $amount = $row->amount;
                    $date = $row->date;
                    $time = $row->time;
                    $type = $row->type;
                    $sub_type = $row->sub_type;
                    $order_id = $row->order_id;
                    $number = $row->number;

                    $row->order_id_cut = substr($order_id, 0,2);
                    $row->amount_str = number_format($amount,2);
                    
                    $row->index = $index;                           
                }
            }

            

            $props['all_history'] = $all_history;
            $props['length'] = $length;

            return Inertia::render('Admin/UsersVTUHistory',$props);
        }
        
    }

    public function loadUsersAccountWithdrawalHistoryPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s Withdrawal His.",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            $props['amount'] = $req->query('amount');
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');


            if(empty($props['amount'])){
                $props['amount'] = "";
            }



            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getUsersWithdrawalHistory($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $amount = $row->amount;
                    
                    $date = $row->date;
                    $time = $row->time;
                    $row->date_time = $date . " " . $time;
                    $row->amount_str = number_format($amount,2);
                    
                    $row->index = $index;                           
                }
            }

            

            $props['all_history'] = $all_history;
            $props['length'] = $length;

            return Inertia::render('Admin/UsersWithdrawalHistory',$props);
        }
        
    }

    public function loadUsersAccountCreditHistoryPage(Request $req, $users_user_id){

        
        $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($users_user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $users_user_name = $this->main_model->getUserNameById($users_user_id);
            
            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo($users_user_name ."'s Acct Cre. Hist.",30);

            $props['users_user_id'] = $users_user_id;
            $props['users_user_name'] = $users_user_name;
            $props['amount'] = $req->query('amount');
            $props['payment_option'] = $req->query('payment_option');
            $props['reference'] = $req->query('reference');
            $props['date'] = $req->query('date');
            $props['start_date'] = $req->query('start_date');
            $props['end_date'] = $req->query('end_date');


            if(empty($props['amount'])){
                $props['amount'] = "";
            }

            if(empty($props['payment_option'])){
                $props['payment_option'] = "";
            }

           
            if(empty($props['reference'])){
                $props['reference'] = "";
            }


            if(empty($props['date'])){
                $props['date'] = "";
            }
            

            if(empty($props['start_date'])){
                $props['start_date'] = "";
            }

            if(empty($props['end_date'])){
                $props['end_date'] = "";
            }

            $length = $req->query("length");
            if(empty($length)){
                $length = 10;
            }
            
            $all_history = $this->main_model->getUsersAccountCreditHistory($users_user_id,$req,$length);
            
            $page = $req->query('page');
            if(empty($page)){
                $page = 1;
            }


            if(is_object($all_history)){
                $j = 0;
                foreach($all_history as $row){
                    
                            

                    $j++;

                    $index = $j +(($page - 1) * $length);

                    $amount = $row->amount;
                    $payment_option = $row->payment_option;
                    $reference = $row->reference;
                    $date = $row->date;
                    $time = $row->time;
                    $row->date_time = $date . " " . $time;
                    $row->amount_str = number_format($amount,2);
                    
                    $row->index = $index;                           
                }
            }

            

            $props['all_history'] = $all_history;
            $props['length'] = $length;

            return Inertia::render('Admin/UsersAccountCreditHistory',$props);
        }
        
    }

    public function processDebitUser(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'messages' => "",'max' => true);

        if(isset($post_data->user_id)){
            $user_id = $post_data->user_id;
        
            $validationRules = [
                'amount' => ['required', 'numeric']
            ];

            $validation = Support_Request::validate($validationRules);
        
            if($validation){
                
                $amount = $post_data->amount;
                $total_income = $this->main_model->getUserParamById("total_income",$user_id);
                $withdrawn = $this->main_model->getUserParamById("withdrawn",$user_id);
            
                $wallet_balance = $total_income - $withdrawn;

                // if($amount <= $wallet_balance){
                    $response_arr['max'] = false;
                    $summary = "Admin Debit Of " . $amount;
                    if($this->main_model->debitUser($user_id,$amount,$summary)){
                        if($this->main_model->addAdminDebitHistory($user_id,$amount,$date,$time)){
                            $response_arr['success'] = true;
                        }
                    }
                // }else{
                //  $response_arr['wallet_balance'] = $wallet_balance;
                // }          
            }
        }
            

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function processCreditUser(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'messages' => "");

        if(isset($post_data->user_id)){
            $user_id = $post_data->user_id;
        
            $validationRules = [
                'amount' => ['required', 'numeric']
            ];

            $validation = Support_Request::validate($validationRules);
        
            if($validation){
                
                $amount = $post_data->amount;
                $summary = "Admin Credit Of " . $amount;

                if($this->main_model->creditUser($user_id,$amount,$summary)){
                    if($this->main_model->debitUser($user_id,20,"Admin Credit Charge")){
                        $this->main_model->addAdminCreditHistory($user_id,$amount,$date,$time);
                        $response_arr['success'] = true;
                    }
                }
                    
            }
        }
            

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function processEditUsersProfile(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        

        $response_arr = array('success' => false,'messages' => "");

        if(isset($post_data->user_id)){
            $user_id = $post_data->user_id;

            $validationRules = [
                'phone_number' => ['required', new PhoneNumberEditRule($user_id)],
                'email_address' => ['required','email:rfc,dns,strict,spoof,filter','between:7,50',new EmailEditRule($user_id)],
                
                'full_name' => 'required|between:5,100',
                'address' => 'required|between:15,1000'
            ];

            $validation = Support_Request::validate($validationRules);
        
            if($validation){
                
                
                $phone = $post_data->phone_number;
                $email = $post_data->email_address;
                $full_name = $post_data->full_name;
                $address = $post_data->address;
                
                //Check If Mobile Number Changed
                    
                $ip_address = $_SERVER['REMOTE_ADDR'];
                if($ip_address == "::1"){
                    $ip_address = "197.211.60.81";
                }
        
                
               
                $calling_code = "234";                                  
                $country_id = 151;
                
            
                $form_array = array(
                    'phone' => $phone,
                    'address' => $address,
                    'phone_code' => $calling_code,
                    'country_id' => $country_id,
                    'email' => $email,
                    'full_name' => $full_name
                );
                if($this->main_model->updateUserTable($form_array,$user_id)){

                    $response_arr['success'] = true;
                }
                    
            }
        }
            

        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }

    public function loadAdminEditUserProfilePage (Request $req,$user_id){

        
        // $user_id = $this->data['user_id'];
        if($this->main_model->checkIfUserIdIsValid($user_id)){
            $props = $this->props;

            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }
            

            $props['active_page'] = 'members_list';
            $props['page_title'] = $this->main_model->custom_echo('Edit User Profile',25);
            $user_info = $this->main_model->getUserInfoByUserId($user_id);
            if(is_object($user_info)){
                
                foreach($user_info as $key => $value){
                    $props['users_arr'][$key] = $value;
                }
            }

            $props['users_arr'] = $props['users_arr'][0];
            
            

            return Inertia::render('Admin/EditUserProfile',$props);
        }
        
    }

    public function loadMembersListPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'members_list';
        $props['page_title'] = $this->main_model->custom_echo('Members List',25);

        $props['full_name'] = $req->query('full_name');
        $props['user_name'] = $req->query('user_name');
        $props['phone'] = $req->query('phone');
        $props['email'] = $req->query('email');
        $props['created_date'] = $req->query('created_date');
        
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');
        


        if(empty($props['full_name'])){
            $props['full_name'] = "";
        }

        if(empty($props['user_name'])){
            $props['user_name'] = "";
        }

        if(empty($props['phone'])){
            $props['phone'] = "";
        }

        if(empty($props['email'])){
            $props['email'] = "";
        }

        if(empty($props['created_date'])){
            $props['created_date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        
        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_users = $this->main_model->getUsersPaginationByOffset($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_users)){
            $j = 0;
            foreach($all_users as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $user_name = $row->user_name;
                $row->phone = $this->main_model->getFullMobileNoByUserName($user_name);
                $total_income = $row->total_income;
                $withdrawn = $row->withdrawn;
                $address = $row->address;
                $row->wallet_balance = $total_income - $withdrawn;
                $row->wallet_balance_str = number_format($row->wallet_balance,2);
                $row->address = $this->main_model->custom_echo($address,20);
        
                
                $row->index = $index;                           
            }
        }

        // $all_requests['links'] = $all_requests->links('pagination::bootstrap-4');
        // echo $all_requests->links();
        $props['all_users'] = $all_users;
        $props['length'] = $length;

        return Inertia::render('Admin/MembersList',$props);
        
    }

    public function loadProductAdvanceHistoryPage  (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'product_advance_history';
        $props['page_title'] = $this->main_model->custom_echo('Product Advance History',25);

        $props['amount'] = $req->query('amount');
        $props['amount_paid'] = $req->query('amount_paid');
        $props['status'] = $req->query('status');
        $props['date'] = $req->query('date');
        
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');


        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        if(empty($props['amount_paid'])){
            $props['amount_paid'] = "";
        }

       
        if(empty($props['status'])){
            $props['status'] = "all";
        }


        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getProductAdvanceHistoryForAllUsersByPagination($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $user_id = $row->user_id;
                $summary = $row->summary;
                $amount = $row->amount;
                $amount_paid = $row->amount_paid;
                $row->balance = $amount - $amount_paid;
                $last_date_time_paid = $row->last_date_time_paid;
                $service_charge = $row->service_charge;
                
                $date_time = $row->date_time;

                $charged_num = $row->charged_num;
                $row->total_amount_charge = $charged_num * $service_charge;
                $last_service_charge_date = $row->last_service_charge_date;

                $row->profit_made = 0;
                if($amount == $amount_paid){
                  $row->profit_made = ($charged_num * $service_charge);
                }




                $row->status = "";
                if($amount == $amount_paid){
                  $row->status = "Cleared";
                }else{
                  $row->status = "Pending";
                }

                $row->amount_str = number_format($amount,2);
                $row->amount_paid_str = number_format($amount_paid,2);
                $row->balance_str = number_format($row->balance,2);
                $row->service_charge_str = number_format($service_charge,2);
                $row->profit_made_str = number_format($row->profit_made,2);
                $row->total_amount_charge_str = number_format($row->total_amount_charge,2);
                $row->full_name = $this->main_model->getUserParamById("full_name",$user_id);
                $row->user_name = $this->main_model->getUserParamById("user_name",$user_id);

                $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);
                if(!$row->user_slug){
                    $row->user_slug = "dave1614";
                }

                if(!$row->user_name){
                    $row->user_name = "dave1614";
                }

                if(!$row->full_name){
                    $row->full_name = "Nwogo David";
                }
        
                
                $row->index = $index;                           
            }
        }

        

        $props['all_history'] = $all_history;
        $props['length'] = $length;

        return Inertia::render('Admin/ProductAdvanceLoanHistory',$props);
        
    }

    public function loadAccountDeditHistoryPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'account_debit_history';
        $props['page_title'] = $this->main_model->custom_echo('Admin Debit History',25);

        $props['amount'] = $req->query('amount');
        $props['date'] = $req->query('date');
        
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');
        


        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        

        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        
        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getAdminDebitHistoryForAllUsersByPagination($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $user_id = $row->user_id;
                
                $date = $row->date;
                $time = $row->time;
                $amount = $row->amount;
                $row->date_time = $date . " " . $time;


                $row->full_name = $this->main_model->getUserFullNameByUserId($user_id);
                $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);

                $row->user_name = $this->main_model->getUserParamById("user_name",$user_id);

                
                if(!$row->user_slug){
                    $row->user_slug = "dave1614";
                }

                if(!$row->user_name){
                    $row->user_name = "dave1614";
                }

                if(!$row->full_name){
                    $row->full_name = "Nwogo David";
                }

                $row->amount_str = number_format($amount,2);
        
                
                $row->index = $index;                           
            }
        }

        // $all_requests['links'] = $all_requests->links('pagination::bootstrap-4');
        // echo $all_requests->links();
        $props['all_history'] = $all_history;
        $props['length'] = $length;

        return Inertia::render('Admin/AdminAccountDebitHistory',$props);
        
    }

    public function loadAdminAccountCreditHistoryPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'admin_account_credit_history';
        $props['page_title'] = $this->main_model->custom_echo('Admin Credit History',25);

        $props['amount'] = $req->query('amount');
        $props['date'] = $req->query('date');
        
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');
        


        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        

        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        
        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getAdminCreditHistoryForAllUsersByPagination($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $user_id = $row->user_id;
                
                $date = $row->date;
                $time = $row->time;
                $amount = $row->amount;
                $row->date_time = $date . " " . $time;


                $row->full_name = $this->main_model->getUserFullNameByUserId($user_id);
                $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);

                $row->user_name = $this->main_model->getUserParamById("user_name",$user_id);

                
                if(!$row->user_slug){
                    $row->user_slug = "dave1614";
                }

                if(!$row->user_name){
                    $row->user_name = "dave1614";
                }

                if(!$row->full_name){
                    $row->full_name = "Nwogo David";
                }

                $row->amount_str = number_format($amount,2);
        
                
                $row->index = $index;                           
            }
        }

        // $all_requests['links'] = $all_requests->links('pagination::bootstrap-4');
        // echo $all_requests->links();
        $props['all_history'] = $all_history;
        $props['length'] = $length;

        return Inertia::render('Admin/AdminAccountCreditHistory',$props);
        
    }


    public function loadAccountCreditHistoryPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'account_credit_history';
        $props['page_title'] = $this->main_model->custom_echo('Account Credit History',25);

        $props['amount'] = $req->query('amount');
        
        $props['payment_option'] = $req->query('payment_option');
        $props['reference'] = $req->query('reference');
        $props['date'] = $req->query('date');
        
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');
        


        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        if(empty($props['payment_option'])){
            $props['payment_option'] = "";
        }

        if(empty($props['reference'])){
            $props['reference'] = "";
        }

        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        
        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getAccountCreditHistoryForAllUsersPaginationByOffset($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $user_id = $row->user_id;
                
                $date = $row->date;
                $time = $row->time;
                $amount = $row->amount;
                $row->date_time = $date . " " . $time;


                $row->full_name = $this->main_model->getUserFullNameByUserId($user_id);
                $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);

                $row->user_name = $this->main_model->getUserParamById("user_name",$user_id);

                
                if(!$row->user_slug){
                    $row->user_slug = "dave1614";
                }

                if(!$row->user_name){
                    $row->user_name = "dave1614";
                }

                if(!$row->full_name){
                    $row->full_name = "Nwogo David";
                }

                $row->amount_str = number_format($amount,2);
        
                
                $row->index = $index;                           
            }
        }

        // $all_requests['links'] = $all_requests->links('pagination::bootstrap-4');
        // echo $all_requests->links();
        $props['all_history'] = $all_history;
        $props['length'] = $length;

        return Inertia::render('Admin/AccountCreditHistory',$props);
        
    }

    public function loadDataComboRechargeHistoryPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'data_combo_requests';
        $props['page_title'] = $this->main_model->custom_echo('Data Combo History',25);

        $props['amount'] = $req->query('amount');
        $props['number'] = $req->query('number');
        $props['date'] = $req->query('date');
        $props['credited_date'] = $req->query('credited_date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');
        


        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        if(empty($props['number'])){
            $props['number'] = "";
        }



        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['credited_date'])){
            $props['credited_date'] = "";
        }

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        
        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getDataComboHistoryPaginationByOffset($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $user_id = $row->user_id;
                $number = $row->number;
                $amount = $row->amount;
                $date = $row->date;
                $time = $row->time;
                $credited = $row->credited;

                $credited_date = $row->credited_date;
                $credited_time = $row->credited_time;

                $row->full_name = $this->main_model->getUserFullNameByUserId($user_id);
                $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);


                
                if(!$row->user_slug){
                    $row->user_slug = "dave1614";
                }

                if(!$row->full_name){
                    $row->full_name = "Nwogo David";
                }
        
                
                $row->index = $index;                           
            }
        }

        // $all_requests['links'] = $all_requests->links('pagination::bootstrap-4');
        // echo $all_requests->links();
        $props['all_history'] = $all_history;
        $props['length'] = $length;

        return Inertia::render('Admin/DataComboHistory',$props);
        
    }

    public function loadDataComboRequestsPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'data_combo_requests';
        $props['page_title'] = $this->main_model->custom_echo('Data Combo Requests',25);

        $props['amount'] = $req->query('amount');
        $props['number'] = $req->query('number');
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');


        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        if(empty($props['number'])){
            $props['number'] = "";
        }



        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_requests = $this->main_model->getDataComboRequestsHistoryPaginationByOffset($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_requests)){
            $j = 0;
            foreach($all_requests as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $user_id = $row->user_id;
                $number = $row->number;
                $amount = $row->amount;
                $date = $row->date;
                $time = $row->time;
                $credited = $row->credited;

                $row->full_name = $this->main_model->getUserFullNameByUserId($user_id);
                $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);


                
                if(!$row->user_slug){
                    $row->user_slug = "dave1614";
                }

                if(!$row->full_name){
                    $row->full_name = "Nwogo David";
                }
        
                
                $row->index = $index;                           
            }
        }

        $props['total_amount_requested'] = number_format($this->main_model->getTotalAmountRequestedAdvanceLoanForAllUsers(),2);
        $props['total_amount_paid_back'] = number_format($this->main_model->getTotalAmountPaidBackAdvanceLoanForAllUsers(),2);
        $props['total_balance'] = number_format($this->main_model->getTotalBalanceAdvanceLoanForAllUsers(),2);
        $props['total_profit_made'] = number_format($this->main_model->getTotalProfitMadeForAllUsers(),2);

        // $all_requests['links'] = $all_requests->links('pagination::bootstrap-4');
        // echo $all_requests->links();
        $props['all_requests'] = $all_requests;
        $props['length'] = $length;

        return Inertia::render('Admin/DataComboRequests',$props);
        
    }

    public function loadAirtimeComboRechargeHistoryPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'airtime_combo_requests';
        $props['page_title'] = $this->main_model->custom_echo('Airtime Combo History',25);

        $props['amount'] = $req->query('amount');
        $props['number'] = $req->query('number');
        $props['date'] = $req->query('date');
        $props['credited_date'] = $req->query('credited_date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');
        


        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        if(empty($props['number'])){
            $props['number'] = "";
        }



        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['credited_date'])){
            $props['credited_date'] = "";
        }

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        
        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getAirtimeComboHistoryPaginationByOffset($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $user_id = $row->user_id;
                $number = $row->number;
                $amount = $row->amount;
                $date = $row->date;
                $time = $row->time;
                $credited = $row->credited;

                $credited_date = $row->credited_date;
                $credited_time = $row->credited_time;

                $row->full_name = $this->main_model->getUserFullNameByUserId($user_id);
                $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);


                
                if(!$row->user_slug){
                    $row->user_slug = "dave1614";
                }

                if(!$row->full_name){
                    $row->full_name = "Nwogo David";
                }
        
                
                $row->index = $index;                           
            }
        }

        // $all_requests['links'] = $all_requests->links('pagination::bootstrap-4');
        // echo $all_requests->links();
        $props['all_history'] = $all_history;
        $props['length'] = $length;

        return Inertia::render('Admin/AirtimeComboHistory',$props);
        
    }

    public function markComboRecordAsRecharged(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false);
        if(isset($post_data->id)){
            $id = $post_data->id;
            

            $form_array = array(
                'credited' => 1,
                'credited_date' => $date,
                'credited_time' => $time
            );
            
            if($this->main_model->markComboRecordAsRecharged($form_array,$id,$date,$time)){
                $response_arr['success'] = true;
            }
        }

        
        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    } 


    public function loadAirtimeComboRequestsPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'airtime_combo_requests';
        $props['page_title'] = $this->main_model->custom_echo('Airtime Combo Requests',25);

        $props['amount'] = $req->query('amount');
        $props['number'] = $req->query('number');
        $props['date'] = $req->query('date');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');


        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        if(empty($props['number'])){
            $props['number'] = "";
        }



        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_requests = $this->main_model->getAirtimeComboRequestsHistoryPaginationByOffset($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_requests)){
            $j = 0;
            foreach($all_requests as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $user_id = $row->user_id;
                $number = $row->number;
                $amount = $row->amount;
                $date = $row->date;
                $time = $row->time;
                $credited = $row->credited;

                $row->full_name = $this->main_model->getUserFullNameByUserId($user_id);
                $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);


                
                if(!$row->user_slug){
                    $row->user_slug = "dave1614";
                }

                if(!$row->full_name){
                    $row->full_name = "Nwogo David";
                }
        
                
                $row->index = $index;                           
            }
        }

        $props['total_amount_requested'] = number_format($this->main_model->getTotalAmountRequestedAdvanceLoanForAllUsers(),2);
        $props['total_amount_paid_back'] = number_format($this->main_model->getTotalAmountPaidBackAdvanceLoanForAllUsers(),2);
        $props['total_balance'] = number_format($this->main_model->getTotalBalanceAdvanceLoanForAllUsers(),2);
        $props['total_profit_made'] = number_format($this->main_model->getTotalProfitMadeForAllUsers(),2);

        // $all_requests['links'] = $all_requests->links('pagination::bootstrap-4');
        // echo $all_requests->links();
        $props['all_requests'] = $all_requests;
        $props['length'] = $length;

        return Inertia::render('Admin/AirtimeComboRequests',$props);
        
    }

    public function loadAdvanceLoanHistoryPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'view_advance_loan_history';
        $props['page_title'] = $this->main_model->custom_echo('Advance Loan History',25);

        $props['amount'] = $req->query('amount');
        $props['amount_paid'] = $req->query('amount_paid');
        $props['status'] = $req->query('status');
        $props['date'] = $req->query('date');
        
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');


        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        if(empty($props['amount_paid'])){
            $props['amount_paid'] = "";
        }

       
        if(empty($props['status'])){
            $props['status'] = "all";
        }


        if(empty($props['date'])){
            $props['date'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_history = $this->main_model->getProductAdvanceHistoryForAllUsersByPagination($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_history)){
            $j = 0;
            foreach($all_history as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $user_id = $row->user_id;
                $summary = $row->summary;
                $amount = $row->amount;
                $amount_paid = $row->amount_paid;
                $row->balance = $amount - $amount_paid;
                $last_date_time_paid = $row->last_date_time_paid;
                $service_charge = $row->service_charge;
                
                $date_time = $row->date_time;

                $charged_num = $row->charged_num;
                $row->total_amount_charge = $charged_num * $service_charge;
                $last_service_charge_date = $row->last_service_charge_date;

                $row->profit_made = 0;
                if($amount == $amount_paid){
                  $row->profit_made = ($charged_num * $service_charge);
                }




                $row->status = "";
                if($amount == $amount_paid){
                  $row->status = "Cleared";
                }else{
                  $row->status = "Pending";
                }

                $row->amount_str = number_format($amount,2);
                $row->amount_paid_str = number_format($amount_paid,2);
                $row->balance_str = number_format($row->balance,2);
                $row->service_charge_str = number_format($service_charge,2);
                $row->profit_made_str = number_format($row->profit_made,2);
                $row->total_amount_charge_str = number_format($row->total_amount_charge,2);
                $row->full_name = $this->main_model->getUserParamById("full_name",$user_id);
                $row->user_name = $this->main_model->getUserParamById("user_name",$user_id);

                $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);
                if(!$row->user_slug){
                    $row->user_slug = "dave1614";
                }

                if(!$row->user_name){
                    $row->user_name = "dave1614";
                }
        
                
                $row->index = $index;                           
            }
        }

        $props['total_amount_requested'] = number_format($this->main_model->getTotalAmountRequestedAdvanceLoanForAllUsers(),2);
        $props['total_amount_paid_back'] = number_format($this->main_model->getTotalAmountPaidBackAdvanceLoanForAllUsers(),2);
        $props['total_balance'] = number_format($this->main_model->getTotalBalanceAdvanceLoanForAllUsers(),2);
        $props['total_profit_made'] = number_format($this->main_model->getTotalProfitMadeForAllUsers(),2);

        $props['all_history'] = $all_history;
        $props['length'] = $length;

        return Inertia::render('Admin/AdvanceLoanHistory',$props);
        
    }


    public function dismissAccountCreditWithdrawal(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false);    
        if(isset($post_data->id)){
            $id = $post_data->id;
            
            $user_id = $this->main_model->getWithdrawalRequestParamById("user_id",$id);
            $amount = $this->main_model->getWithdrawalRequestParamById("amount",$id);
            $form_array = array(
                'dismissed' => 1,
                'dismissed_date_time' => $date . " " . $time
            );

            if($this->main_model->updateWithdrawalRequest($form_array,$id)){
                $summary = "Refund For Withdrawal Request Dismissal";
                $amount = $amount + 100;
                if($this->main_model->creditUser($user_id,$amount,$summary)){
                    $response_arr['success'] = true;
                }
            }
        }

        
        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    } 

    public function verifyAccountCreditWithdrawal(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
            
        $response_arr = array('success' => false);
        if(isset($post_data->id)){
            $id = $post_data->id;
            
            $user_id = $this->main_model->getWithdrawalRequestParamById("user_id",$id);
            $amount = $this->main_model->getWithdrawalRequestParamById("amount",$id);
            $form_array = array(
                'debited' => 1,
                'debited_date_time' => $date . " " . $time
            );

            if($this->main_model->updateWithdrawalRequest($form_array,$id)){
                if($this->main_model->addWithrawalHistory($user_id,$amount)){
                    $response_arr['success'] = true;
                }
            }

            
        }

        
        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    } 

    public function getWithdrwawalRequestAccountDetails(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => true,'bank_name' => '','account_number' => '','account_name' => '');
        if(isset($post_data->id)){
            $id = $post_data->id;
            

            $bank_name = $this->main_model->getWithdrawalRequestParamById("real_bank_name",$id);
            $account_name = $this->main_model->getWithdrawalRequestParamById("account_name",$id);
            $account_number = $this->main_model->getWithdrawalRequestParamById("account_number",$id);

            

            

            $response_arr['bank_name'] = $bank_name;
            $response_arr['account_number'] = $account_number;
            $response_arr['account_name'] = $account_name;
        }

        
        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    } 


    public function loadAccountWithdrawalRequestsPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'account_withdrawal_requests';
        $props['page_title'] = $this->main_model->custom_echo('Account Withdrawal Requests',25);
        $props['amount'] = $req->query('amount');
        $props['user_name'] = $req->query('user_name');
        $props['status'] = $req->query('status');
        $props['date'] = $req->query('date');
        $props['debited_date_time'] = $req->query('debited_date_time');
        $props['dismissed_date_time'] = $req->query('dismissed_date_time');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');


        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        if(empty($props['user_name'])){
            $props['user_name'] = "";
        }

        if(empty($props['status'])){
            $props['status'] = "pending";
        }


        if(empty($props['date'])){
            $props['date'] = "";
        }

        if(empty($props['debited_date_time'])){
            $props['debited_date_time'] = "";
        }

        if(empty($props['dismissed_date_time'])){
            $props['dismissed_date_time'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_requests = $this->main_model->getAccountWithdrawalRequestsForAllUsersByPagination($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_requests)){
            $j = 0;
            foreach($all_requests as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $user_id = $row->user_id;
                $user_name = $row->user_name;
                $amount = $row->amount;
                $real_bank_name = $row->real_bank_name;
                $account_number = $row->account_number;
                $account_name = $row->account_name;
                
                $date = $row->date;
                $time = $row->time;
                $row->date_time = $date . " " . $time; 
                $debited = $row->debited;
                
                $debited_date_time = $row->debited_date_time;
                $dismissed = $row->dismissed;
                $dismissed_date_time = $row->dismissed_date_time;



                $row->amount_str = number_format($amount,2);
                $row->status = "";
                if($debited == 0 && $dismissed == 0){
                  $row->status = "Pending";
                }else if($debited == 1 && $dismissed == 0){
                  $row->status = "Debited";
                }else if($debited == 0 && $dismissed == 1){
                  $row->status = "Dismissed";
                }

                $row->full_name = $this->main_model->getUserParamById("full_name",$user_id);
                // $user_name = $this->meetglobal_model->getUserParamById("user_name",$user_id);
                $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);
                // echo $row->user_slug;
                if(!$row->user_slug){
                    $row->user_slug = "dave1614";
                }

                if(!$row->full_name){
                    $row->full_name = "dave1614";
                }
                
                $row->index = $index;                           
            }
        }

        $props['all_requests'] = $all_requests;
        $props['length'] = $length;

        return Inertia::render('Admin/AccountWithdrawalRequests',$props);
        
    }

    public function dismissUserCreditRequest(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false);
        if(isset($post_data->id)){
            
            $id = $post_data->id;
            
            $form_array = array(
                'dismissed' => 1,
                'dismissed_date_time' => $date . " " . $time
            );
            if($this->main_model->updateCreditAccountPaymentProofsRecord($form_array,$id)){
                $response_arr['success'] = true;
            }
            
        }

        
        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    } 

    public function creditUserAfterRequest(Request $req){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false);
        if(isset($post_data->id) && isset($post_data->amount) && isset($post_data->user_id)){
            
            $id = $post_data->id;
            $amount = $post_data->amount;
            $user_id = $post_data->user_id;
            
            if($this->main_model->creditUser($user_id,$amount,"Credit By Admin")){
                // if($this->meetglobal_model->debitUser($user_id,20,"Admin Credit Charge")){
                
                    $form_array = array(
                        'credited' => 1,
                        'credited_amount' => $amount,
                        'credited_date_time' => $date . " " . $time
                    );
                    if($this->main_model->updateCreditAccountPaymentProofsRecord($form_array,$id)){
                        $this->main_model->addAdminCreditHistory($user_id,$amount,$date,$time);
                        $response_arr['success'] = true;
                    }
                // }
            }
        }

        
        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    } 

    public function getUserInfoById(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false);

        if(isset($post_data->show_records) && isset($post_data->user_id)){
            $user_id = $post_data->user_id;
            $user_info = $this->main_model->getUserInfoByUserId($user_id);
            if(is_object($user_info)){
                
                foreach($user_info as $key => $value){
                    $response_arr[$key] = $value;
                }
                $response_arr['success'] = true;
            }
        }

        
        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    } 

    public function loadAccountCreditRequestsPage (Request $req){

        
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'account_credit_requests';
        $props['page_title'] = $this->main_model->custom_echo('Account Credit Requests',25);
        $props['amount'] = $req->query('amount');
        $props['user_name'] = $req->query('user_name');
        $props['depositors_name'] = $req->query('depositors_name');
        $props['credited_amount'] = $req->query('credited_amount');
        $props['status'] = $req->query('status');
        $props['date_of_payment'] = $req->query('date_of_payment');
        $props['date'] = $req->query('date');
        $props['credited_date_time'] = $req->query('credited_date_time');
        $props['dismissed_date_time'] = $req->query('dismissed_date_time');
        $props['start_date'] = $req->query('start_date');
        $props['end_date'] = $req->query('end_date');


        if(empty($props['amount'])){
            $props['amount'] = "";
        }

        if(empty($props['user_name'])){
            $props['user_name'] = "";
        }

        if(empty($props['depositors_name'])){
            $props['depositors_name'] = "";
        }

        if(empty($props['credited_amount'])){
            $props['credited_amount'] = "";
        }

        if(empty($props['status'])){
            $props['status'] = "pending";
        }

        if(empty($props['date_of_payment'])){
            $props['date_of_payment'] = "";
        }

        if(empty($props['date'])){
            $props['date'] = "";
        }

        if(empty($props['credited_date_time'])){
            $props['credited_date_time'] = "";
        }

        if(empty($props['dismissed_date_time'])){
            $props['dismissed_date_time'] = "";
        }
        

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_requests = $this->main_model->getAccountCreditRequestsForAllUsersByPagination($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_requests)){
            $j = 0;
            foreach($all_requests as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $user_id = $row->user_id;
                $user_name = $row->user_name;
                $row->date_time = $row->date . " " . $row->time; 
                $credited = $row->credited;
                $credited_amount = $row->credited_amount;
                $credited_date_time = $row->credited_date_time;
                $dismissed = $row->dismissed;
                $dismissed_date_time = $row->dismissed_date_time;
                $amount = $row->amount;

                $row->amount_str = number_format($amount,2);
                $row->credited_amount_str = number_format($credited_amount,2);


                $row->status = "";
                if($credited == 0 && $dismissed == 0){
                  $row->status = "Pending";
                }else if($credited == 1 && $dismissed == 0){
                  $row->status = "Credited";
                }else if($credited == 0 && $dismissed == 1){
                  $row->status = "Dismissed";
                }

                $row->full_name = $this->main_model->getUserParamById("full_name",$user_id);
                // $user_name = $this->meetglobal_model->getUserParamById("user_name",$user_id);
                $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);
                $row->index = $index;
                        
            }
        }

        $props['all_requests'] = $all_requests;
        $props['length'] = $length;

        return Inertia::render('Admin/AccountCreditRequests',$props);
        
    }

    public function dismissComplaint(Request $req){
       $date = date("j M Y");
        $time = date("h:i:sa");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false);
        if(isset($post_data->id)){
            
            $id = $post_data->id;
            
            $form_array = array(
                'dismissed' => 1,
                'dismissed_date_time' => $date . " " . $time
            );
            if($this->main_model->updateComplaintRecord($form_array,$id)){
                $response_arr['success'] = true;
            }
            
        }

        
        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    } 

    public function loadComplaintInfoPage (Request $req,$id){


        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'complaints';
        $props['page_title'] = $this->main_model->custom_echo('Complaint Info',25);
        

        
        $complaint_info = $this->main_model->getComplaintInfoById($id);
        if(is_object($complaint_info)){
            foreach($complaint_info as $row){
                $user_id = $row->user_id;
                $dismissed = $row->dismissed;
                $dismissed_date_time = $row->dismissed_date_time;
                $whatsapp_number = $row->whatsapp_number;
                $amount = $row->amount;
                $type = $row->type;

                $row->status = "";
                if($dismissed == 0){
                  $row->status = "Pending";
                }else if($dismissed == 1){
                  $row->status = "Dismissed";
                }

                $row->full_name = $this->main_model->getUserParamById("full_name",$user_id);
                // $user_name = $this->meetglobal_model->getUserParamById("user_name",$user_id);
                $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);
                $row->amount = number_format($amount,2);
                $row->new_whatsapp_number = $this->main_model->convertLocalNumberToInter($whatsapp_number);

                if($type == "registration"){
                  $complaint_type = "Registration";
                }else if($type == "commission"){
                  $complaint_type = "Commission";
                }else if($type == "airtime"){
                  $complaint_type = "Airtime";
                }else if($type == "data"){
                  $complaint_type = "Data Bundle";
                }else if($type == "cable"){
                  $complaint_type = "Cable TV";
                }else if($type == "electricity"){
                  $complaint_type = "Electricity";
                }else if($type == "pos"){
                  $complaint_type = "POS";
                }else if($type == "mini_importation"){
                  $complaint_type = "Mini Importation";
                }else if($type == "smart_business_loan"){
                  $complaint_type = "Smart Business Loan";
                }else if($type == "withdrawal"){
                  $complaint_type = "Withdrawal";
                }else if($type == "seminar_invite"){
                  $complaint_type = "Invite For Seminar";
                }else if($type == "flyers_and_tools"){
                  $complaint_type = "Flyers And Other Tools";
                }

                $row->complaint_type = $complaint_type;
            }
        }
        
       
        $props['complaint_info'] = $complaint_info[0];
        

        return Inertia::render('Admin/ComplaintInfo',$props);
        
    }

    public function loadComplaintsPage (Request $req){


        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'complaints';
        $props['page_title'] = $this->main_model->custom_echo('Complaints',25);
        $props['user_name'] = $req->query('user_name');
        $props['whatsapp_number'] = $req->query('whatsapp_number');
        $props['type'] = $req->query('type');
        $props['status'] = $req->query('status');
        $props['date_of_recharge'] = $req->query('date_of_recharge');
        $props['date'] = $req->query('date');
        $props['dismissed_date_time'] = $req->query('dismissed_date_time');

        if(empty($props['user_name'])){
            $props['user_name'] = "";
        }

        if(empty($props['whatsapp_number'])){
            $props['whatsapp_number'] = "";
        }

        if(empty($props['type'])){
            $props['type'] = "all";
        }

        if(empty($props['status'])){
            $props['status'] = "all";
        }

        if(empty($props['date_of_recharge'])){
            $props['date_of_recharge'] = "";
        }

        if(empty($props['date'])){
            $props['date'] = "";
        }

        if(empty($props['dismissed_date_time'])){
            $props['dismissed_date_time'] = "";
        }

        if(empty($props['start_date'])){
            $props['start_date'] = "";
        }

        if(empty($props['end_date'])){
            $props['end_date'] = "";
        }

        $length = $req->query("length");
        if(empty($length)){
            $length = 10;
        }
        
        $all_complaints = $this->main_model->getComplaintsForAllUsersByPagination($req,$length);
        
        $page = $req->query('page');
        if(empty($page)){
            $page = 1;
        }


        if(is_object($all_complaints)){
            $j = 0;
            foreach($all_complaints as $row){
                
                        

                $j++;

                $index = $j +(($page - 1) * $length);

                $id = $row->id;
                $user_id = $row->user_id;
                $user_name = $row->user_name;
                $type = $row->type;
                $whatsapp_number = $row->whatsapp_number;
                $date_of_recharge = $row->date_of_recharge;
                
                $date_time = $row->date_time;
                
               
                $dismissed = $row->dismissed;
                $dismissed_date_time = $row->dismissed_date_time;

                if($type == "registration"){
                  $complaint_type = "Registration";
                }else if($type == "commission"){
                  $complaint_type = "Commission";
                }else if($type == "airtime"){
                  $complaint_type = "Airtime";
                }else if($type == "data"){
                  $complaint_type = "Data Bundle";
                }else if($type == "cable"){
                  $complaint_type = "Cable TV";
                }else if($type == "electricity"){
                  $complaint_type = "Electricity";
                }else if($type == "pos"){
                  $complaint_type = "POS";
                }else if($type == "mini_importation"){
                  $complaint_type = "Mini Importation";
                }else if($type == "smart_business_loan"){
                  $complaint_type = "Smart Business Loan";
                }else if($type == "withdrawal"){
                  $complaint_type = "Withdrawal";
                }else if($type == "seminar_invite"){
                  $complaint_type = "Invite For Seminar";
                }else if($type == "flyers_and_tools"){
                  $complaint_type = "Flyers And Other Tools";
                }

                $row->complaint_type = $complaint_type;


                $status = "";
                if($dismissed == 0){
                  $status = "Pending";
                }else if($dismissed == 1){
                  $status = "Dismissed";
                }

                $row->status = $status;

                $row->full_name = $this->main_model->getUserParamById("full_name",$user_id);
                // $user_name = $this->meetglobal_model->getUserParamById("user_name",$user_id);
                $row->user_slug = $this->main_model->getUserParamById("slug",$user_id);
                $row->index = $index;
                        
            }
        }

        $props['all_complaints'] = $all_complaints;
        $props['length'] = $length;

        return Inertia::render('Admin/Complaints',$props);
        
    }

    public function submitMakeComplaintForm(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        // return $_SERVER['HTTP_REFERER'];
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'messages' => '','invalid_complaint_type' => true);
        if(isset($post_data->complaint_type)){
            $response_arr['invalid_complaint_type'] = false;
            $type = $post_data->complaint_type;

            
            $validation_rules = [
                'complaint_type' => ['required', Rule::in(['registration', 'commission', 'airtime', 'data', 'cable', 'electricity', 'pos', 'mini_importation', 'smart_business_loan' , 'withdrawal' , 'seminar_invite', 'flyers_and_tools'])]
            ];


            if($type == "airtime" || $type == "cable" || $type == "electricity"){
                $validation_rules['amount'] = ['required', 'numeric'];
            }

            if($type == "airtime" || $type == "data"){

                $validation_rules['subscribed_number'] = ['required', 'numeric'];
            }

            if($type == "data"){

                $validation_rules['data_amount'] = ['required'];
            }

            if($type == "airtime" || $type == "data" || $type == "cable" || $type == "electricity"){

                $validation_rules['date_of_recharge'] = ['required'];
            }


            if($type == "airtime" || $type == "data"){

                $validation_rules['network'] = ['required'];
            }

            if($type == "cable"){

                $validation_rules['cable_type'] = ['required'];
            }

            if($type == "cable"){
                $validation_rules['cable_owners_name'] = ['required'];
            }

            if($type == "cable"){
                
                $validation_rules['cable_phone_number'] = ['required', 'numeric'];
            }

            if($type == "cable"){

                $validation_rules['smart_card_number'] = ['required', 'numeric'];
            }

            if($type == "electricity"){

                $validation_rules['disco'] = ['required'];
            }

            if($type == "electricity"){
               
                $validation_rules['meter_type'] = ['required'];
            }

            if($type == "electricity"){

                $validation_rules['meter_number'] = ['required', 'numeric'];
            }

            if($type == "pos"){
                
                $validation_rules['pos_type'] = ['required'];
            }

           
            $validation_rules['whatsapp_number'] = ['required','numeric'];
            
            $validation = Support_Request::validate($validation_rules);
            if($validation){
                $whatsapp_number = $post_data->whatsapp_number;
                $date = date("j M Y");
                $time = date("h:i:sa");

                $date_time = $date . " " . $time;
                $form_array = array(
                    'user_id' => $user_id,
                    'user_name' => $this->data['user_name'],
                    'date_time' => $date_time,
                    'type' => $type,
                    'whatsapp_number' => $whatsapp_number
                );


                if($type == "airtime" || $type == "cable" || $type == "electricity"){
                    $amount = $post_data->amount;
                    $form_array['amount'] = $amount;
                }

                if($type == "airtime" || $type == "data"){
                    $subscribed_number = $post_data->subscribed_number;
                    $form_array['subscribed_number'] = $subscribed_number;

                }

                if($type == "data"){

                    $data_amount = $post_data->data_amount;
                    $form_array['data_amount'] = $data_amount;
                }

                if($type == "airtime" || $type == "data" || $type == "cable" || $type == "electricity"){

                    $date_of_recharge = $post_data->date_of_recharge;
                    $form_array['date_of_recharge'] = $date_of_recharge;
                }


                if($type == "airtime" || $type == "data"){

                    $network = $post_data->network;
                    $form_array['network'] = $network;
                }

                if($type == "cable"){

                    $cable_type = $post_data->cable_type;
                    $form_array['cable_type'] = $cable_type;
                }

                if($type == "cable"){
                    $cable_owners_name = $post_data->cable_owners_name;
                    $form_array['cable_owners_name'] = $cable_owners_name;
                }

                if($type == "cable"){
                    $cable_phone_number = $post_data->cable_phone_number;
                    $form_array['cable_phone_number'] = $cable_phone_number;
                }

                if($type == "cable"){

                    $iuc_number = $post_data->smart_card_number;
                    $form_array['iuc_number'] = $iuc_number;
                }

                if($type == "electricity"){

                    $disco = $post_data->disco;
                    $form_array['disco'] = $disco;
                }

                if($type == "electricity"){
                    $meter_type = $post_data->meter_type;
                    $form_array['meter_type'] = $meter_type;
                }

                if($type == "electricity"){
                    $meter_number = $post_data->meter_number;
                    $form_array['meter_number'] = $meter_number;
                }

                if($type == "pos"){
                    $pos_type = $post_data->pos_type;
                    $form_array['pos_type'] = $pos_type;
                }



                if($this->main_model->addComplaint($form_array)){
                    $response_arr['success'] = true;
                }
                    
            }

        }

        // $response_arr = json_encode($response_arr);
        // $_SESSION['response_arr'] = $response_arr;
        
        // return Redirect::to(URL::route('make_complaint'));
        
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    } 

    public function loadMakeComplaintPage (Request $req){


        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'make_complaint';
        $props['page_title'] = $this->main_model->custom_echo('Make Complaint',25);
        

        return Inertia::render('MakeComplaint',$props);
        
    }

    public function verifyForgotTransactionPasswordOtp(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'messages' => array(),'expired' => false,'incomplete_detais' => false,'incorrect_otp' => false,'transaction_password_not_set' => true);
        if($this->data['transaction_password'] != ""){
            $response_arr['transaction_password_not_set'] = false;
            
            $validation = Support_Request::validate([
                'transfer_otp' => ['required','numeric', 'digits:5']
            ]);
            if($validation){
                if(isset($_SESSION['otp']) && isset($_SESSION['forgot_transaction_password_first_step'])){
                    $user_name = $_SESSION['forgot_transaction_password_first_step'];
                    $session_otp = $_SESSION['otp'];

                    if($this->main_model->checkIfUserNameExists($user_name)){
                        $user_id = $this->main_model->getUserIdByName($user_name);

                        if(isset($post_data->transfer_otp) && isset($post_data->transaction_password)){
                            
                            $new_transaction_password = $post_data->transaction_password; 
                            $otp = $post_data->transfer_otp;

                            
                            if($otp == $session_otp){
                        
                                $date = date("j M Y");
                                $time = date("h:i:sa");
                                $hashed_new_transaction_password = sha1($new_transaction_password);
                                $form_array = array(
                                    'transaction_password' => $hashed_new_transaction_password
                                );
                                if($this->main_model->updateUserTable($form_array,$user_id)){
                                    session_unset();
                                    $response_arr['success'] = true;
                                }
                            }else{
                                $response_arr['incorrect_otp'] = true;
                            }
                                    
                                        
                            
                        }else{
                            $response_arr['incomplete_detais'] = true;
                        }
                    }       
                }else{
                    $response_arr['expired'] = true;
                }
                
            }
        }

        $response_arr = json_encode($response_arr);
        $_SESSION['response_arr'] = $response_arr;
        
        return Redirect::to(URL::route('change_transaction_password'));
    }

    public function sendForgotTransactionPasswordOtp(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'email' => '','transaction_password_not_set' => true);

        if(isset($post_data->show_records)){
            if($this->data['transaction_password'] != ""){
                $response_arr['transaction_password_not_set'] = false;
                $email = $this->main_model->getUserParamById("email",$user_id);
                $response_arr['email'] = $email;

                $otp = rand ( 10000 , 99999 );
                $email_arr = array($email);
                if($this->main_model->sendOtpEmail($email_arr,$otp)){
                    $_SESSION['otp'] = $otp;
                    $_SESSION['forgot_transaction_password_first_step'] = $this->data['user_name'];
                    //Change This Once In Development
                    $response_arr['success'] = true;
                    if($_SERVER['SERVER_NAME'] == "127.0.0.1"){
                        $response_arr['otp'] = $otp;
                    }
                }
            }   
        }

        $response_arr = json_encode($response_arr);
        $_SESSION['response_arr'] = $response_arr;
        
        return Redirect::to(URL::route('change_transaction_password'));
    }

    public function getUsersEmailForgotTransactionPassword(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'email' => '','transaction_password_not_set' => true);

        if(isset($post_data->show_records)){
            if($this->data['transaction_password'] != ""){
                $response_arr['transaction_password_not_set'] = false;
                $response_arr['success'] = true;
                $email = $this->main_model->getUserParamById("email",$user_id);
                $response_arr['email'] = $email;
            }
        }

        $response_arr = json_encode($response_arr);
        $_SESSION['response_arr'] = $response_arr;
        
        return Redirect::to(URL::route('change_transaction_password'));
    }

    public function upgradeMlmAccountToBusinessThroughMeetglobal(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        // return $_SERVER['HTTP_REFERER'];
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'date' => '','time' => '','insuffecient_funds' => '');
        if(isset($post_data->mlm_db_id)){
            $mlm_db_id = $post_data->mlm_db_id;
            
            if($this->main_model->checkIfMlmDbIdBelongsToUser($mlm_db_id,$user_id) && ($this->main_model->getMlmDbParamById("package",$mlm_db_id) == 1)){
                $amount = 6500;
                
                $amount_to_debit_user = $amount;
                $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                // echo $user_total_amount;
                // echo $amount;

                if($amount_to_debit_user <= $user_total_amount){
                    $summary = "Debit Of " . $amount_to_debit_user . " To Upgrade Basic Account To Business";
                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){

                        $date = date("j M Y");
                        $time = date("h:i:sa");
                        if($this->main_model->upgradeMlmAccountToBusiness($mlm_db_id,$user_id,$date,$time)){
                            $response_arr['success'] = true;
                            $response_arr['date'] = $date;
                            $response_arr['time'] = $time;
                        }
                    }
                }else{
                    $response_arr['insuffecient_funds'] = true;
                }   
            }
            
        }
        
        
        
        $response_arr = json_encode($response_arr);
        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    } 

    public function upgradeMlmAccountToBusiness(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'url' => '');
        if(isset($post_data->mlm_db_id)){
            $mlm_db_id = $post_data->mlm_db_id;
            
            if($this->main_model->checkIfMlmDbIdBelongsToUser($mlm_db_id,$user_id) && ($this->main_model->getMlmDbParamById("package",$mlm_db_id) == 1)){
                $reference_code = substr(md5(uniqid(rand(), true)),4);
                $total_amount = 6500;
                $total_amount = $total_amount * 100;
                // $email = $this->main_model->getUserEmailByUserId($user_id);
                // $token = $this->main_model->getUserTokenByUserId($user_id);
                
                // $name = $this->main_model->getUserFullNameByUserId($user_id);
                $response_arr['success'] = true;
                // $redirect_url = site_url('meetglobal/process_mlm_business_upgrade_payment/?mlm_db_id='.$mlm_db_id);
                
                
                // $auth_url = $this->paystack->init($reference_code, $total_amount,$email,[
                //     'name' => $name
                // ],$redirect_url);
                // if (filter_var($auth_url, FILTER_VALIDATE_URL)){
                //     $response_arr['success'] = true;
                //     $response_arr['url'] = $auth_url;
                // }
            }
            
        }

        
        $response_arr = json_encode($response_arr);

        if(isset($_SERVER['HTTP_REFERER'])){
            // $previous_page = $_SERVER['HTTP_REFERER']; 
            
            $_SESSION['response_arr'] = $response_arr;   
            return Redirect::to($_SERVER['HTTP_REFERER']);
        }else{
            return $response_arr;
        }
    }  

    public function verifySetTransactionPasswordOtp(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'messages' => array(),'expired' => false,'incomplete_detais' => false,'incorrect_otp' => false,'transaction_password_already_set' => true);
        if($this->data['transaction_password'] == ""){
            $response_arr['transaction_password_already_set'] = false;
            $validation = Support_Request::validate([
                'transfer_otp' => ['required','numeric', 'digits:5']
            ]);
            if($validation){

                if(isset($_SESSION['otp']) && isset($_SESSION['set_transaction_password_first_step'])){
                    $user_name = $_SESSION['set_transaction_password_first_step'];
                    $session_otp = $_SESSION['otp'];

                    if($this->main_model->checkIfUserNameExists($user_name)){
                        $user_id = $this->main_model->getUserIdByName($user_name);
                        // return $post_data;
                        if(isset($post_data->transfer_otp) && isset($post_data->transaction_password)){
                            
                            $new_transaction_password = $post_data->transaction_password; 
                            $otp = $post_data->transfer_otp;

                            
                            if($otp == $session_otp){
                        
                                $date = date("j M Y");
                                $time = date("h:i:sa");
                                $hashed_new_transaction_password = sha1($new_transaction_password);
                                $form_array = array(
                                    'transaction_password' => $hashed_new_transaction_password
                                );
                                if($this->main_model->updateUserTable($form_array,$user_id)){
                                    session_unset();
                                    $response_arr['success'] = true;
                                }
                            }else{
                                $response_arr['incorrect_otp'] = true;
                            }
                                    
                                        
                            
                        }else{
                            $response_arr['incomplete_detais'] = true;
                        }
                    }       
                }else{
                    $response_arr['expired'] = true;
                }
                
            }
        }

        $response_arr = json_encode($response_arr);
        $_SESSION['response_arr'] = $response_arr;
        
        return Redirect::to(URL::route('change_transaction_password'));
    }

    public function sendSetTransactionPasswordOtp(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'email' => '','transaction_password_already_set' => true);

        if(isset($post_data->show_records)){
            if($this->data['transaction_password'] == ""){
                $response_arr['transaction_password_already_set'] = false;
                $email = $this->main_model->getUserParamById("email",$user_id);
                $response_arr['email'] = $email;

                $otp = rand ( 10000 , 99999 );
                $email_arr = array($email);
                if($this->main_model->sendOtpEmail($email_arr,$otp)){
                    $_SESSION['otp'] = $otp;
                    $_SESSION['set_transaction_password_first_step'] = $this->data['user_name'];
                    //Change This Once In Development
                    $response_arr['success'] = true;
                    if($_SERVER['SERVER_NAME'] == "127.0.0.1"){
                        $response_arr['otp'] = $otp;
                    }
                }
            }   
        }

        $response_arr = json_encode($response_arr);
        $_SESSION['response_arr'] = $response_arr;
        
        return Redirect::to(URL::route('change_transaction_password'));
    }

    public function getUsersEmailSetTransactionPassword(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'email' => '','transaction_password_already_set' => true);

        if(isset($post_data->show_records)){
            // return $this->data['transaction_password'];
            if($this->data['transaction_password'] == ""){
                $response_arr['transaction_password_already_set'] = false;
                $response_arr['success'] = true;
                $email = $this->main_model->getUserParamById("email",$user_id);
                $response_arr['email'] = $email;
            }
        }

        $response_arr = json_encode($response_arr);
        $_SESSION['response_arr'] = $response_arr;
        
        return Redirect::to(URL::route('change_transaction_password'));
    }

    public function processChangeTransactionPassword(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'messages' => '','wrong_password' => false,'new_passwords_mismatch' => true);
         
        $validation = Support_Request::validate([
            'old_password' => ['required', 'min:4'],
            'new_password' => ['required', 'min:4'],
            'new_password_confirm' => ['required', 'min:4'],
        ]);
        if($validation){
            $old_password = $post_data->old_password;
            $new_password = $post_data->new_password;
            $new_password_confirm = $post_data->new_password_confirm;
            $old_hashed = $this->main_model->getUserHashedById($user_id);
            if($new_password == $new_password_confirm){
                $response_arr['new_passwords_mismatch'] = false;
                $old_hashed = $this->data['transaction_password'];
                if($old_hashed == sha1($old_password)){
                    $form_array = array(
                        'transaction_password' => sha1($new_password)
                    );
                    if($this->main_model->updateUserTable($form_array,$user_id)){
                        // $this->session->set_flashdata('password_changed',true);
                        $response_arr['success'] = true;
                    }
                }else{
                    $response_arr['wrong_password'] = true;
                }
            }
        }
            

        $response_arr = json_encode($response_arr);
        $_SESSION['response_arr'] = $response_arr;
        
        return Redirect::to(URL::route('change_transaction_password'));
    }

    public function changeTransactionPasswordPage(Request $req){


        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'change_transaction_password';
        $props['page_title'] = $this->main_model->custom_echo('Transaction Password',25);
        

        return Inertia::render('ChangeTransactionPassword',$props);
        
    }

    public function processChangePassword(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        $user_id = $this->data['user_id'];
        
        $response_arr = array('success' => false,'messages' => '','wrong_password' => false);
         
        $validation = Support_Request::validate([
            'old_password' => ['required', 'min:4'],
            'new_password' => ['required', 'min:4']
        ]);
        if($validation){
            $old_password = $post_data->old_password;
            $new_password = $post_data->new_password;
            $old_hashed = $this->main_model->getUserHashedById($user_id);
            if($old_hashed == sha1($old_password)){
                $form_array = array(
                    'hashed' => sha1($new_password)
                );      
                if($this->main_model->updateUserTable($form_array,$user_id)){
                    $response_arr['success'] = true;
                }
            }else{
                $response_arr['wrong_password'] = true;
            }
        }
            

        $response_arr = json_encode($response_arr);
        $_SESSION['response_arr'] = $response_arr;
        
        return Redirect::to(URL::route('change_password'));
    }

    public function changePasswordPage(Request $req){


        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'change_password';
        $props['page_title'] = $this->main_model->custom_echo('Change Your Password',25);

        return Inertia::render('ChangePassword',$props);
        
    }

    public function loadAdminPage(Request $req){
        $user_id = $this->data['user_id'];
        $props = $this->props;

        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }
        
        $props['active_page'] = 'dashboard';
        $props['page_title'] = $this->main_model->custom_echo('Overview',25);


        $year = date("Y");
        $month = date("M");
        $month_year = $month . " " . $year;
        $props['month_year'] = $month_year;
        $props['month_year_2'] = date("M Y");
        $top_mlm_earners = $this->main_model->getTopTenMlmEarnersForTheMonth();
        $index = 0;
        $new_arr = array();
        if(is_array($top_mlm_earners)){
            $i = 0;
            foreach($top_mlm_earners as $row){
                $index++;
                
                // $user_id = $row->id;
                $user_name = $row->user_name;
                $full_name = $row->full_name;
                $user_slug = $row->slug;
                $year = date("Y");
                $month = date("M");

                $month_year = $month . " " . $year;
                $month_amt_str = strtolower($month . "_earnings");
                $amount = $row->$month_amt_str;
                
                if($amount > 0){
                    $new_arr[] = array(
                        'index' => $index,
                        'user_name' => $user_name,
                        'full_name' => $full_name,
                        'user_slug' => $user_slug,
                        'year' => $year,
                        'month' => $month,
                        'month_year' => $month_year,
                        'month_amt_str' => $month_amt_str,
                        'amount' => number_format($amount,2)
                    );
                }

            }
        }

        $props['top_mlm_earners'] = $new_arr;
        
        if($this->data['is_admin'] == 1){
            $props['today_registered_users'] = (String) number_format($this->main_model->getTotalNumberOfUsersRegisteredToday());
            $props['total_registerd_users'] = (String) number_format($this->main_model->getTotalNumberOfRegisteredUsers());
            $props['total_amount_online_payment'] = (String) number_format($this->main_model->getTotalAmountForOnlinePaymentMadeToday($user_id),2);
            $props['total_amount_withdrawn_today'] = (String) number_format($this->main_model->getTotalAmountWithdrawnToday($user_id),2);

            $props['total_pending_amount_product_advance'] = (String) number_format($this->main_model->getTotalPendingAmountForProductAdvance(),2);
            $props['total_profit_made_for_all_users'] = (String) number_format($this->main_model->getTotalProfitMadeForAllUsers());
            $props['year'] = date("Y");

            $first_twenty_users = $this->main_model->getLastTwentyUsersRegisteredUsers();
            $index = 0;
            $new_twenty_users_arr = array();
            if(is_array($first_twenty_users)){
                foreach($first_twenty_users as $row){
                    $index++;
                    // $user_id = $row->id;
                    $user_name = $row->user_name;
                    $full_name = $row->full_name;
                    $user_slug = $row->slug;
                    $time = $row->time;

                    $new_twenty_users_arr[] = array(
                        'user_name' => $user_name,
                        'full_name' => $full_name,
                        'user_slug' => $user_slug,
                        'time' => $time
                    );
                }
            }

            $props['first_twenty_users'] = $new_twenty_users_arr;
            $props['chartist_arr'] = array(
                $this->main_model->getChartistNumberForRegistrationsByMonth("Jan"),
                $this->main_model->getChartistNumberForRegistrationsByMonth("Feb"),
                $this->main_model->getChartistNumberForRegistrationsByMonth("Mar"),
                $this->main_model->getChartistNumberForRegistrationsByMonth("Apr"),
                $this->main_model->getChartistNumberForRegistrationsByMonth("May"),
                $this->main_model->getChartistNumberForRegistrationsByMonth("Jun"),
                $this->main_model->getChartistNumberForRegistrationsByMonth("Jul"),
                $this->main_model->getChartistNumberForRegistrationsByMonth("Aug"),
                $this->main_model->getChartistNumberForRegistrationsByMonth("Sep"),
                $this->main_model->getChartistNumberForRegistrationsByMonth("Oct"),
                $this->main_model->getChartistNumberForRegistrationsByMonth("Nov"),
                $this->main_model->getChartistNumberForRegistrationsByMonth("Dec")
            );
            

            return Inertia::render('Admin/HomePage',$props);    
        }else{
            $props['wallet_balance'] = (String) number_format($this->main_model->getUsersWalletBalance($user_id),2);
            $props['total_amount_wthdrawn'] = (String) number_format($this->main_model->getTotalAmountWithdrawnByUser($user_id),2);

            $basic_sponsor_earnings = $this->main_model->getUsersMlmBasicSponsorEarnings($user_id);
            $business_sponsor_earnings = $this->main_model->getUsersMlmBusinessSponsorEarnings($user_id);
            $basic_sponsor_earnings = $this->main_model->getUsersMlmBasicSponsorEarnings($user_id);
            $business_sponsor_earnings = $this->main_model->getUsersMlmBusinessSponsorEarnings($user_id);
            $basic_placement_earnings = $this->main_model->getUsersMlmBasicPlacementEarnings($user_id);
            $business_placement_earnings = $this->main_model->getUsersMlmBusinessPlacementEarnings($user_id);
            $basic_placement_earnings = $this->main_model->getUsersMlmBasicPlacementEarnings($user_id);
            $business_placement_earnings = $this->main_model->getUsersMlmBusinessPlacementEarnings($user_id);
            $center_leader_sponsor_bonus = $this->main_model->getUsersCenterLeaderSponsorBonus($user_id);
            $center_leader_placement_bonus = $this->main_model->getUsersCenterLeaderPlacementBonus($user_id);
            $center_connector_sponsor_bonus = $this->main_model->getUsersCenterConnectorSponsorBonus($user_id);
            $center_connector_placement_bonus = $this->main_model->getUsersCenterConnectorPlacementBonus($user_id);
            $vendor_sponsor_bonus = $this->main_model->getUsersVendorSponsorBonus($user_id);
            $vendor_placement_bonus = $this->main_model->getUsersVendorPlacementBonus($user_id);
            $center_leader_selection_income = $this->main_model->getUserParamById("center_leader_selection_income",$user_id);
            $center_connector_selection_income = $this->main_model->getUserParamById("center_connector_selection_income",$user_id);
            $vendor_selection_income = $this->main_model->getUserParamById("vendor_selection_income",$user_id);
            $trade_delivery_income = $this->main_model->getUsersTradeDeliveryIncome($user_id);
            $vtu_trade_income = $this->main_model->getUsersVtuTradeIncome($user_id);
            $sgps_income = $this->main_model->getUsersSGPSIncome($user_id);
            $car_award_earnings = $this->main_model->getUsersCarAwardEarnings($user_id);
            
            $total_basic_earnings = $basic_sponsor_earnings + $basic_placement_earnings;
            $total_business_earnings = $business_sponsor_earnings + $business_placement_earnings + $center_leader_sponsor_bonus + $center_leader_placement_bonus + $center_leader_selection_income + $center_connector_selection_income + $vendor_selection_income + $trade_delivery_income + $car_award_earnings + $vtu_trade_income + $sgps_income + $center_connector_sponsor_bonus + $center_connector_placement_bonus + $vendor_sponsor_bonus + $vendor_placement_bonus;


            $total_business_earnings += $total_basic_earnings;

            $props['different_earnings'] = array(
                'basic_sponsor_earnings' => $basic_sponsor_earnings,
                'business_sponsor_earnings' => $business_sponsor_earnings,
                'basic_placement_earnings' => $basic_placement_earnings,
                'business_placement_earnings' => $business_placement_earnings,
                'center_leader_sponsor_bonus' => $center_leader_sponsor_bonus,
                'center_leader_placement_bonus' => $center_leader_placement_bonus,
                'center_connector_sponsor_bonus' => $center_connector_sponsor_bonus,
                'center_connector_placement_bonus' => $center_connector_placement_bonus,
                'vendor_sponsor_bonus' => $vendor_sponsor_bonus,
                'vendor_placement_bonus' => $vendor_placement_bonus,
                'center_leader_selection_income' => $center_leader_selection_income,
                'center_connector_selection_income' => $center_connector_selection_income,
                'vendor_selection_income' => $vendor_selection_income,
                'trade_delivery_income' => $trade_delivery_income,
                'vtu_trade_income' => $vtu_trade_income,
                'sgps_income' => $sgps_income,
                'car_award_earnings' => $car_award_earnings,
                'total_basic_earnings' => $total_basic_earnings,
                'total_business_earnings' => number_format($total_business_earnings,2)
            );

            $mlm_db_id = $this->main_model->getUsersFirstMlmDbId($user_id);
            $props['mlm_db_id'] = $mlm_db_id;
            $downline_arr = $this->main_model->getDownlineArr1($mlm_db_id);
            $props['downline_arr'] = $downline_arr;
            $props['downline_arr_num'] = number_format(count($props['downline_arr']));

            $business_team_total = 0;
            for($i = 0; $i < count($downline_arr); $i++){
              $arr = $downline_arr[$i];
              $package = $arr[2];
              if($package == 2){
                $business_team_total++;
              }
            }
            $props['business_team_total'] = (String) number_format($business_team_total);
            if(is_null($this->data['logo'])){
                $user_avatar = '/images/avatar.jpg';
            }else{
                $user_avatar = '/storage/images/'. $this->data['logo'];
            }
            $props['user_avatar'] = $user_avatar;
            $props['short_bio'] = $this->main_model->custom_echo($this->data['bio'],150);
            
           
            $props['chartist_arr'] = $this->main_model->getChartistArrayForUserDownlineForTheMonth($user_id);
            // $props['chartist_arr'] = json_encode(array(
            //     'labels' =>  ['1st', '2nd', '3rd', '4th'],
            //     'series' =>  $chartist_arr
            // ));

            // return json_encode($props['chartist_arr']);

            $props['front_page_title'] = $this->main_model->getFrontPageTitle();
            $props['front_page_text'] = $this->main_model->getFrontPageText();
            $props['front_page_type'] = $this->main_model->getFrontPageType();


            return Inertia::render('HomePage',$props);
        }
        
    }

    public function processUserLogout(Request $req){
        if (isset($_COOKIE['meetrem'])) {
            unset($_COOKIE['meetrem']); 
            setcookie('meetrem', null, -1, '/'); 
            return redirect('/');
        }
    }

    public function changePasswordReset(Request $req){
        $post_data = (Object) $req->input(); 

        $response_arr = array('success' => false,'expired' => false,'new_password_absent' => false);

        if(isset($_SESSION['forgot_pass_second_step'])){
            $user_name = $_SESSION['forgot_pass_second_step'];
            
            if($this->main_model->checkIfUserNameExists($user_name)){
                $response_arr['user_name'] = $user_name;
                
                if(isset($post_data->new_password)){
                    $new_password = $post_data->new_password;
                    $token = md5(uniqid(rand(), true));
                    $user_id = $this->main_model->getUserIdByName($user_name);
                    if($this->main_model->onRegister($user_id,$token)){
                        if($this->main_model->changeUserPassword($user_id,$token,$new_password)){
                            
                            session_unset();
                            
                            $req->session()->flash('password_resetted', true);
                            $response_arr['success'] = true;
                            $response_arr['url'] = '/admin_page';
                        } 
                    }
                }else{
                    $response_arr['new_password_absent'] = true;
                }

            }       
        }else{
            $response_arr['expired'] = true;
        }
        echo json_encode($response_arr);
                

        

    }

    public function verifyUserFogotPasswordOtp(Request $req){
        $post_data = (Object) $req->input(); 

        $response_arr = array('success' => false,'expired' => false,'otp_not_present' => false,'incorrect_otp' => false);
        if(isset($_SESSION['otp']) && isset($_SESSION['forgot_pass_first_step'])){
            $user_name = $_SESSION['forgot_pass_first_step'];
            $session_otp = $_SESSION['otp'];

            if($this->main_model->checkIfUserNameExists($user_name)){

                if(isset($post_data->otp_input)){
                    $otp = $post_data->otp_input;
                    if($otp == $session_otp){
                        // $this->session->set_userdata('otp',$otp);
                        $_SESSION['forgot_pass_second_step'] = $user_name;
                        $response_arr['success'] = true;
                        $response_arr['user_name'] = $user_name;
                        // $response_arr['url'] = site_url('meetglobal/index/change_password_reset');
                        

                    }else{
                        $response_arr['incorrect_otp'] = true;
                    }
                }else{
                    $response_arr['otp_not_present'] = true;
                }
            }       
        }else{
            $response_arr['expired'] = true;
        }
        echo json_encode($response_arr);

    }

    public function checkIfUsernameExists(Request $req){
        $post_data = (Object) $req->input(); 

        $response_arr = array('success' => false,'empty' => true,'no_post' => true,'mobile' => '','full_name' => '','user_id' => '','email' => '');
        if(isset($post_data->user_name)){
            $user_name = $post_data->user_name;
            $response_arr['no_post'] = false;
            if($user_name !== ""){
                $response_arr['empty'] = false;
                if($this->main_model->userExists($user_name)){
                    $response_arr['success'] = true;
                    $user_id = $this->main_model->getUserIdByName($user_name);
                    $response_arr['mobile'] = $this->main_model->getFullMobileNoByUserName($user_name);
                    $response_arr['full_name'] = $this->main_model->getUserFullNameById($user_id);
                    $email = $this->main_model->getUserParamById("email",$user_id);
                    $response_arr['email'] = $email;
                    $response_arr['user_id'] = $user_id;
                    $response_arr['phone'] = $this->main_model->getUserPhoneNumberByUserId($user_id);
                    $response_arr['phone_code'] = $this->main_model->getUserPhoneCodeByUserId($user_id);
                    $response_arr['url'] = '/send_forgot_password_otp';

                }
            }
        }
        $response_arr = json_encode($response_arr);
        echo $response_arr;

    }

    public function sendForgotPasswordOtp(Request $req){
        $post_data = (Object) $req->input(); 

        $response_arr = array('success' => false,'empty' => true,'no_post' => true,'mobile' => '','full_name' => '','user_id' => '','email' => '');
        if(isset($post_data->user_name)){
            $user_name = $post_data->user_name;
            $response_arr['no_post'] = false;
            if($user_name !== ""){
                $response_arr['empty'] = false;
                if($this->main_model->userExists($user_name)){
                    
                    $user_id = $this->main_model->getUserIdByName($user_name);
                    $response_arr['mobile'] = $this->main_model->getFullMobileNoByUserName($user_name);
                    $response_arr['full_name'] = $this->main_model->getUserFullNameById($user_id);
                    $email = $this->main_model->getUserParamById("email",$user_id);
                    $response_arr['email'] = $email;
                    $response_arr['user_id'] = $user_id;
                    $response_arr['phone'] = $this->main_model->getUserPhoneNumberByUserId($user_id);
                    $response_arr['phone_code'] = $this->main_model->getUserPhoneCodeByUserId($user_id);
                    $response_arr['url'] = '/send_forgot_password_otp';

                    $otp = rand ( 10000 , 99999 );
                    $email_arr = array($email);
                    if($this->main_model->sendOtpEmail($email_arr,$otp)){
                        
                        $_SESSION['otp'] = $otp;
                        $_SESSION['forgot_pass_first_step'] = $user_name;
                        
                        //Change This Once In Development
                        $response_arr['success'] = true;
                        // $response_arr['otp'] = $otp;
                        if($_SERVER['SERVER_NAME'] == "127.0.0.1"){
                            $response_arr['otp'] = $otp;
                        }
                    }   

                }
            }
        }
        $response_arr = json_encode($response_arr);
        echo $response_arr;
    }



    public function selectPositioningForMlmRegistration(Request $req){
        $date_time = date("Y-m-d") . " " . date("H:i:s");
        $post_data = (Object) $req->input(); 
        if(isset($post_data->mlm_db_id)){
            $mlm_db_id = $post_data->mlm_db_id;
            if($this->main_model->checkIfMlmDbIdIsValid($mlm_db_id)){

                $response_arr = array('success' => false,'messages' => '','no_available_position' => false);
                if($this->main_model->checkIfMlmDbIdHasNoAvailablePositionUnderHim($mlm_db_id)){
                    $response_arr['no_available_position'] = true;
                }else{
                    $available = $this->main_model->getAvailablePositionUnderMlmDbId($mlm_db_id);
                    if($available == "left"){
                        $response_arr['success'] = true;
                        $response_arr['messages'] .= '<button type="button" class="btn btn-warning btn-round" onclick="goBackFromSelectPlacementPosition(this,event)"> < < Go Back</button>';

                        $response_arr['messages'] .= "<h4 class='text-primary'>Click To Select Position.</h4>";
                        $response_arr['messages'] .= '<div class="table-div material-datatables table-responsive" style="">';
                        $response_arr['messages'] .= '<table class="table table-striped table-bordered nowrap hover display" id="select-placement-position-table" cellspacing="0" width="100%" style="width:100%">';
                        $response_arr['messages'] .= '<thead>';
                        $response_arr['messages'] .= '<tr>';
                        $response_arr['messages'] .= '<th>#</th>';
                        $response_arr['messages'] .= '<th>Available Positions</th>';
                        
                        $response_arr['messages'] .= '</tr>';
                        $response_arr['messages'] .= '<tbody>';
                        $response_arr['messages'] .= '<tr style="cursor:pointer;" onclick="selectThisPositionPlacement(this,event)" data-mlm-db-id="'.$mlm_db_id.'" data-position="left">';
                        $response_arr['messages'] .= '<td>1</td>';
                        $response_arr['messages'] .= '<td>Left</td>';
                        $response_arr['messages'] .= '</tr>';
                        $response_arr['messages'] .= '</tbody>';
                        $response_arr['messages'] .= '</table>';
                        $response_arr['messages'] .= '</div>';

                    }else if($available == "right"){
                        $response_arr['success'] = true;
                        $response_arr['messages'] .= '<button type="button" class="btn btn-warning btn-round" onclick="goBackFromSelectPlacementPosition(this,event)"> < < Go Back</button>';

                        $response_arr['messages'] .= "<h4 class='text-primary'>Click To Select Position.</h4>";
                        $response_arr['messages'] .= '<div class="table-div material-datatables table-responsive" style="">';
                        $response_arr['messages'] .= '<table class="table table-striped table-bordered nowrap hover display" id="select-placement-position-table" cellspacing="0" width="100%" style="width:100%">';
                        $response_arr['messages'] .= '<thead>';
                        $response_arr['messages'] .= '<tr>';
                        $response_arr['messages'] .= '<th>#</th>';
                        $response_arr['messages'] .= '<th>Available Positions</th>';
                        
                        $response_arr['messages'] .= '</tr>';
                        $response_arr['messages'] .= '<tbody>';
                        $response_arr['messages'] .= '<tr style="cursor:pointer;" onclick="selectThisPositionPlacement(this,event)" data-mlm-db-id="'.$mlm_db_id.'" data-position="right">';
                        $response_arr['messages'] .= '<td>1</td>';
                        $response_arr['messages'] .= '<td>Right</td>';
                        $response_arr['messages'] .= '</tr>';
                        $response_arr['messages'] .= '</tbody>';
                        $response_arr['messages'] .= '</table>';
                        $response_arr['messages'] .= '</div>';


                    }else if($available == "both"){
                        $response_arr['success'] = true;
                        $response_arr['messages'] .= '<button type="button" class="btn btn-warning btn-round" onclick="goBackFromSelectPlacementPosition(this,event)"> < < Go Back</button>';

                        $response_arr['messages'] .= "<h4 class='text-primary'>Click To Select Position.</h4>";
                        $response_arr['messages'] .= '<div class="table-div material-datatables table-responsive" style="">';
                        $response_arr['messages'] .= '<table class="table table-striped table-bordered nowrap hover display" id="select-placement-position-table" cellspacing="0" width="100%" style="width:100%">';
                        $response_arr['messages'] .= '<thead>';
                        $response_arr['messages'] .= '<tr>';
                        $response_arr['messages'] .= '<th>#</th>';
                        $response_arr['messages'] .= '<th>Available Positions</th>';
                        
                        $response_arr['messages'] .= '</tr>';
                        $response_arr['messages'] .= '<tbody>';
                        $response_arr['messages'] .= '<tr style="cursor:pointer;" onclick="selectThisPositionPlacement(this,event)" data-mlm-db-id="'.$mlm_db_id.'" data-position="left">';
                        $response_arr['messages'] .= '<td>1</td>';
                        $response_arr['messages'] .= '<td>Left</td>';
                        $response_arr['messages'] .= '</tr>';

                        $response_arr['messages'] .= '<tr style="cursor:pointer;" onclick="selectThisPositionPlacement(this,event)" data-mlm-db-id="'.$mlm_db_id.'" data-position="right">';
                        $response_arr['messages'] .= '<td>2</td>';
                        $response_arr['messages'] .= '<td>Right</td>';
                        $response_arr['messages'] .= '</tr>';
                        $response_arr['messages'] .= '</tbody>';
                        $response_arr['messages'] .= '</table>';
                        $response_arr['messages'] .= '</div>';


                    }else{

                    }
                }
                echo json_encode($response_arr);
            }
        }
    }

    public function getPlacementMlmAccountRegistration(Request $req){
        $date_time = date("Y-m-d") . " " . date("H:i:s");
        $post_data = (Object) $req->input(); 
        if(isset($post_data->placement_user_name)){
            $response_arr = array('success' => false,'user_name_does_not_exist' => false,'messages' => '');

            $placement_user_name = $post_data->placement_user_name;
            
            if($this->main_model->checkIfUserNameExists($placement_user_name)){
                $placement_user_id = $this->main_model->getUserIdByName($placement_user_name);
                if($this->main_model->getUserParamById("created",$placement_user_id) == 1){
                    $response_arr['success'] = true;
                    $sponsor_id = $this->main_model->getUserIdByName($placement_user_name);
                    
                    $all_mlm_ids = $this->main_model->getAllUsersMlmDbIds($sponsor_id);
                    if(is_array($all_mlm_ids)){
                        $response_arr['success'] = true;
                        $response_arr['messages'] .= "<div class='container'>";
                        $response_arr['messages'] .= "<button type='button' class='btn btn-warning btn-round' onclick='goBackFromSelectPlacementMlmAccount(this,event)'> < < Go Back</button>";

                        $response_arr['messages'] .= "<div class='select-placement-table-div'>";
                        $response_arr['messages'] .= "<p>Click To Select ".$placement_user_name."'s Mlm Account To Use As Placement.</p>";
                        $response_arr['messages'] .= '<div class="table-div material-datatables table-responsive" style="">';
                        $response_arr['messages'] .= '<table class="table table-striped table-bordered nowrap hover display" id="select-placement-table" cellspacing="0" width="100%" style="width:100%">';
                        $response_arr['messages'] .= '<thead>';
                        $response_arr['messages'] .= '<tr>';
                        $response_arr['messages'] .= '<th>#</th>';
                        $response_arr['messages'] .= '<th>Mlm Account</th>';
                        
                        
                        $response_arr['messages'] .= '</tr>';
                        $response_arr['messages'] .= '<tbody>';


                        for($i = 0; $i < count($all_mlm_ids); $i++){
                            $j = $i + 1;
                            $mlm_db_id = $all_mlm_ids[$i];
                            $mlm_db_id = (Integer) $mlm_db_id;
                            // var_dump($mlm_db_id);
                            $response_arr['messages'] .= '<tr style="cursor:pointer;" data-str="'.$placement_user_name. ' ('.$j.')' .'"  data-mlm-db-id="'.$mlm_db_id.'" onclick="selectThisUserAsPlacement(this,event)">';
                            $response_arr['messages'] .= '<td>'.$j.'</td>';
                            $response_arr['messages'] .= '<td>'.$placement_user_name. ' ('.$j.')' .'</td>';
                            $response_arr['messages'] .= '</tr>';
                        }
                        $response_arr['messages'] .= '</tbody>';
                        $response_arr['messages'] .= '</table>';
                        $response_arr['messages'] .= "</div>";
                        $response_arr['messages'] .= "</div>";

                        $response_arr['messages'] .= "<div class='select-placement-position-table-div' style='display: none;'>";

                        $response_arr['messages'] .= "</div>";
                        $response_arr['messages'] .= "</div>";
                    }
                }else{
                    $response_arr['user_name_does_not_exist'] = true;
                }
            }else{
                $response_arr['user_name_does_not_exist'] = true;
            }
            
            echo json_encode($response_arr);
        }
    }

    public function completeRegistrationStep2(Request $req,$user_slug){
        $date_time = date("Y-m-d") . " " . date("H:i:s");
        $post_data = (Object) $req->input(); 

        if($this->main_model->checkIfUserHasHalfRegisteredBySlug($user_slug) && $this->main_model->getUserNameBySlug($user_slug)){
            $prospective_user_name = $this->main_model->getUserNameBySlug($user_slug);
            $user_id = $this->main_model->getUserIdByName($prospective_user_name);
            $user_info = $this->main_model->getUserInfoByUserId($user_id);
            $this->data['user_id'] = $user_id;
            $this->data['user_slug'] = $user_slug;
            if(is_object($user_info)){
                foreach($user_info as $user){
                    $this->data['user_name'] = $user->user_name;
                    $this->data['email'] = $user->email;
                    $this->data['phone'] = $user->phone;
                    $this->data['phone_code'] = $user->phone_code;
                    $this->data['country_id'] = $user->country_id;
                    $this->data['state_id'] = $user->state_id;
                    $this->data['address'] = $user->address;
                    $this->data['user_slug'] = $user->slug;
                    $this->data['date'] = $user->date;
                    $this->data['time'] = $user->time;
                    $this->data['logo'] = $user->logo;
                    $this->data['cover_photo'] =  $user->cover_photo;

                    $registration_amt_paid = $user->registration_amt_paid;
                    $this->data['balance'] = 500 - $registration_amt_paid;
                    $balance = $this->data['balance'];
                    $start_date = $this->data['date'];  
                    $date = strtotime($start_date);
                    $this->data['account_expiry_date'] = strtotime("+14 day", $date);
                    $this->data['account_expiry_date'] = date("j M Y",$this->data['account_expiry_date']);
                    $sponsor_user_id = $user->sponsor_user_id;
                    $sponsor_user_name = $this->main_model->getUserNameById($sponsor_user_id);
                    $user_token = $user->token;
                }
                $date = date("j M Y");
                $time = date("h:i:sa");

                if($balance <= 0){
                    $response_arr = array('success' => false,'url' => '');
                    
                    if(isset($post_data->position_selected)){
                        if(isset($post_data->placement_id) && isset($post_data->position)){
                                
                            $placement_mlm_db_id = $post_data->placement_id;
                            $placement_position = $post_data->position;

                            if($this->main_model->checkIfMlmDbIdIsValid($placement_mlm_db_id)){
                                if(!$this->main_model->checkIfUserAlreadyHasAnMlmAccountInTheSystem($user_id)){

                                    if($this->main_model->performMlmRegistrationForFirstTimeUsersWithPlacement($user_id,$sponsor_user_name,$placement_mlm_db_id,$placement_position,$date,$time,1)){
                                        $form_array = array(
                                            'created' => 1,
                                            'created_date' => $date,
                                            'created_time' => $time,
                                        );
                                        if($this->main_model->updateUserTable($form_array,$user_id)){
                                            if($this->main_model->onRegister($user_id,$user_token)){
                                                $response_arr['success'] = true;
                                                $response_arr['url'] = '/admin_page';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }else{  
                            
                        if(!$this->main_model->checkIfUserAlreadyHasAnMlmAccountInTheSystem($user_id)){

                            if($this->main_model->performMlmRegistrationForFirstTimeUsersWithOutPlacement($user_id,$sponsor_user_name,$date,$time,1)){
                                $form_array = array(
                                    'created' => 1,
                                    'created_date' => $date,
                                    'created_time' => $time,
                                );
                                if($this->main_model->updateUserTable($form_array,$user_id)){
                                    if($this->main_model->onRegister($user_id,$user_token)){
                                        $response_arr['success'] = true;
                                        $response_arr['url'] = '/admin_page';
                                    }
                                }
                            }
                        }
                            
                    }
                }

                echo json_encode($response_arr);

                
            }
        }else{
            return View('no_access');
        }
    }

    public function submiProofOfPaymentToAdmin(Request $req,$user_slug){
        $date_time = date("Y-m-d") . " " . date("H:i:s");
        if($this->main_model->checkIfUserHasHalfRegisteredBySlug($user_slug) && $this->main_model->getUserNameBySlug($user_slug)){
            $prospective_user_name = $this->main_model->getUserNameBySlug($user_slug);
            $user_id = $this->main_model->getUserIdByName($prospective_user_name);
            $user_info = $this->main_model->getUserInfoByUserId($user_id);
            $this->data['user_id'] = $user_id;
            $this->data['user_slug'] = $user_slug;
            if(is_object($user_info)){
                foreach($user_info as $user){
                    $this->data['user_name'] = $user->user_name;
                    $this->data['email'] = $user->email;
                    $this->data['phone'] = $user->phone;
                    $this->data['phone_code'] = $user->phone_code;
                    $this->data['country_id'] = $user->country_id;
                    $this->data['state_id'] = $user->state_id;
                    $this->data['address'] = $user->address;
                    $this->data['user_slug'] = $user->slug;
                    $this->data['date'] = $user->date;
                    $this->data['time'] = $user->time;
                    $this->data['logo'] = $user->logo;
                    $this->data['cover_photo'] =  $user->cover_photo;

                    $center_leader =  $user->center_leader;
                    
                }
                $date = date("j M Y");
                $time = date("h:i:sa");


                $response_arr = array('success' => false,'url' => '','messages' => '',"errors" => "","empty" => true,"only_one_image" => false);

                $config['encrypt_name'] = true;
                $config['upload_path']  = './assets/images';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']     = '4000';

                

                $post_data = (Object) $req->input(); 

                if(!empty($_FILES['image']['name'])){
                    $response_arr['empty'] = false;
                    
                    $files_count = count(array($_FILES['image']['name']));
                    
                    if($files_count == 1){
                        $response_arr['only_one_image'] = true;

                        // $req->validate([
                        // 'image' => 'required|mimes:csv,txt,xlx,xls,pdf|max:2048'
                        // ]);

                       

                        $validationRules = [
                            'image' => 'required|mimes:png,jpeg,jpg,gif,webp|max:4000',
                            'amount' => 'required|numeric',
                            'depositors_name' => 'required|max:150',
                            'date_of_payment' => 'required',
                            
                        ];

                        $messages = [];

                        $validation = Validator::make($req->all(), $validationRules,$messages);
                        
                        if ($validation->fails()){
                            $validation_errors = $validation->errors();
                            $all_errors = $validation_errors->get("*");
                            // var_dump($all_errors);
                            // echo json_encode($all_errors);

                            foreach ($all_errors as $key => $value) {
                                $errors_str = "<p style='color: red' class='form-error'>";
                                for($i = 0; $i < count($all_errors[$key]); $i++){
                                    $error = $all_errors[$key][$i];

                                    $errors_str .= $error;
                                    if(count($all_errors[$key]) > 1 && $i != count($all_errors[$key])){
                                        $errors_str .= "<br>";
                                    }
                                }
                                $errors_str .= "</p>";

                                // echo $key . "<br>";
                                $response_arr['messages'][$key] = $errors_str;
                            }
                        }else{
                            // $fileModel = new FileUpload;

                            // echo "string";
                            if($req->file('image')) {
                                // echo "string";
                                $image = $req->file('image');

                                
                                // echo $file . "<br>";
                                if($_SERVER['SERVER_NAME'] == "127.0.0.1"){
                                    $fileName = time().'_'.$image->getClientOriginalName();
                                    $filePath = $image->storePubliclyAs('/public/images', $fileName);
                                    $extension = $image->getClientOriginalExtension();
                                    $filePath = str_replace('public', 'storage', $filePath);

                                    $file = public_path($filePath);
                                }else{
                                    $fileName = time().'_'.$image->getClientOriginalName();
                                    $filePath = $image->storeAs('images', $fileName,'public_uploads');
                                    // $path = $request->photo->storeAs('images', 'filename.jpg', 'public_uploads');
                                    // echo $filePath . "<br>";
                                    $extension = $image->getClientOriginalExtension();
                                    $filePath = str_replace('public', 'storage', $filePath);

                                    $file = base_path('storage/' .$filePath);
                                }

                                if( file_exists($file)){

                                    $amount = $post_data->amount;
                                    $depositors_name = $post_data->depositors_name;
                                    $date_of_payment = $post_data->date_of_payment;
                                    $image = $fileName;

                                    $form_array = array(
                                        'user_id' => $user_id,
                                        'user_name' => $this->data['user_name'],
                                        'amount' => $amount,
                                        'depositors_name' => $depositors_name,
                                        'date_of_payment' => $date_of_payment,
                                        'image' => $image,
                                        'date' => $date,
                                        'time' => $time
                                    );

                                    if($this->main_model->addPaymentProofRequest($form_array)){
                                        $response_arr['success'] = true;
                                        
                                    }else{
                                        $file_path = public_path().'/storage/images/'.$fileName;
                                        unlink($file_path);
                                    }
                                }else{
                                    $response_arr['errors'] = 'We ran into some errors when uploading your file';
                                }
                            }
                        }
                    }
                }
                echo json_encode($response_arr);
                
            }
        }else{
            return View('no_access');
        }
    }

    public function registrationStep2(Request $request,$user_slug){
        if($this->main_model->checkIfUserHasHalfRegisteredBySlug($user_slug) && $this->main_model->getUserNameBySlug($user_slug)){
            $prospective_user_name = $this->main_model->getUserNameBySlug($user_slug);
            $user_id = $this->main_model->getUserIdByName($prospective_user_name);
            $user_info = $this->main_model->getUserInfoByUserId($user_id);
            $this->data['user_id'] = $user_id;
            $this->data['user_slug'] = $user_slug;
            if(is_object($user_info)){
                foreach($user_info as $user){
                    $this->data['user_name'] = $user->user_name;
                    $this->data['email'] = $user->email;
                    $this->data['phone'] = $user->phone;
                    $this->data['phone_code'] = $user->phone_code;
                    $this->data['country_id'] = $user->country_id;
                    $this->data['state_id'] = $user->state_id;
                    $this->data['address'] = $user->address;
                    $this->data['user_slug'] = $user->slug;
                    $this->data['date'] = $user->date;
                    $this->data['time'] = $user->time;
                    $this->data['logo'] = $user->logo;
                    $this->data['cover_photo'] =  $user->cover_photo;

                    $registration_amt_paid = $user->registration_amt_paid;
                    $this->data['balance'] = 500 - $registration_amt_paid;
                    $start_date = $this->data['date'];  
                    $date = strtotime($start_date);
                    $this->data['account_expiry_date'] = strtotime("+14 day", $date);
                    $this->data['account_expiry_date'] = date("j M Y",$this->data['account_expiry_date']);
                }
                return View('registration_step_2',$this->data);
            } 
        }else{
            return View('no_access');
        }
        
    }

    public function processUserSignUpCont(Request $req){
        $date_time = date("Y-m-d") . " " . date("H:i:s");
        $post_data = (Object) $req->input(); 

        $date = date("j M Y");
        $time = date("h:i:sa");
        $data = array('success' => false,'expired' => false,'sponsor_does_not_exist' => false,'incorrect_otp' => false,'phone_used' => false); 
        if(isset($post_data->otp_input)){
            if(isset($_SESSION['otp']) && isset($_SESSION['sponsor_user_name'])){
                $session_otp = $_SESSION['otp'];
                $otp_input = $post_data->otp_input;
                $sponsor_user_name = $_SESSION['sponsor_user_name'];
                $sponsor_user_id = $this->main_model->getUserIdByName($sponsor_user_name);

                if($this->main_model->checkIfUserNameExists($sponsor_user_name)){
                    $proceed = false;

                    if($session_otp == $otp_input){

                        $password = $_SESSION['password'];
                        $user_name = $_SESSION['user_name'];
                        $full_name = $_SESSION['full_name'];
                        $token = $_SESSION['token'];
                        $email = $_SESSION['email'];
                        $hashed = $_SESSION['hashed'];
                        $slug = $_SESSION['slug'];
                        $phone_code = $_SESSION['code'];
                        $phone = $_SESSION['phone_number'];

                        $sponsor_user_name = $_SESSION['sponsor_user_name'];

                        
                        
                        if($phone[0] == "0"){
                            $phone = substr($phone, 1);
                        }
                        
                        if($this->main_model->phone_unique($phone_code,$phone)){
                            $country_code = "ng";
                            // if($_SERVER['SERVER_NAME'] == "127.0.0.1"){
                            //     $country_code = "ng";
                            // }else{
                            //     $url = "https://restcountries.eu/rest/v2/callingcode/".$phone_code;
                            //     $country_info = $this->main_model->curl($url, false, $data1 = []);
                                
                            //     // echo $country_info;
                            //     if($this->main_model->isJson($country_info)){
                            //         $country_info = json_decode($country_info);
                            //         if(is_array($country_info)){
                            //             $country_code = $country_info[0]->alpha2Code;
                            //         }
                            //     } 
                            // }

                            if($_SERVER['SERVER_NAME'] == "127.0.0.1"){
                                $monnify_base_url = "https://api.monnify.com";
                            }else{
                                $monnify_base_url = "https://api.monnify.com";
                            }

                            $country_id = $this->main_model->getCountryIdByCountryCode(strtolower($country_code));

                            $user_array = array(
                                'sponsor_user_id' => $sponsor_user_id,
                                'monnify_account_reference' => "",
                                'created' => 0,
                                'user_name' => $user_name,
                                'full_name' => $full_name,
                                'hashed' => $hashed,
                                'token' => $token,
                                'phone' => $phone,
                                'phone_code' => $phone_code,
                                'email' => $email,
                                'country_id' => $country_id,
                                'slug' => $slug,
                                'date' => $date,
                                'time' => $time,
                                'date_time' => $date_time
                            );
                            $user_get = $req->query('user');
                            if(!empty($user_get)){
                                // echo "string";
                                $id = $user_get;
                                // echo $id;
                                if($this->main_model->getUserIdByName($id) !== false){
                                    
                                    $id = $this->main_model->getUserIdByName($id);
                                    $user_array = array_merge($user_array,array('referred_by' => $id));
                                }
                            }   

                            if($this->main_model->createUser($user_array)){
                                $user_id = $this->main_model->getUserIdByToken($token);
                                // if($this->meetglobal_model->onRegister($user_id,$token)){
                                    
                                $data['success'] = true;
                                
                                
                                $data['url'] = '/registration_step_2/'.$slug;
                                $_SESSION['account_created_success'] = true;
                                
                            
                                        
                                // }
                            }
                               
                                
                        }else{
                            $data['phone_used'] = true;
                        }
                        
                    }else{
                        $data['incorrect_otp'] = true;
                    }
                    
                }else{
                    $data['sponsor_does_not_exist'] = true;
                }
            }else{
                $data['expired'] = true;
            }
        }   
        echo json_encode($data);
        
    }

    public function processUserSignUp(Request $req){
        $date_time = date("Y-m-d") . " " . date("H:i:s");
        $post_data = (Object) $req->input(); 
        if(isset($post_data->phone) && isset($post_data->random_bytes) && isset($post_data->full_name) && isset($post_data->user_name_sign_up) && isset($post_data->password_sign_up) && isset($post_data->sponsor_user_name)){
            $user_name_sign_up = $post_data->user_name_sign_up;
            $sponsor_user_name = $post_data->sponsor_user_name;

            $response_arr = array('success' => false,'messages' => array(),'half_registered' => false,'already_registered' => false);
            

            $validationRules = [
                'sponsor_user_name' => ['required', new SponsorUsernameRule],
                'email' => ['required','email:rfc,dns,strict,spoof,filter','between:7,50'],
                'phone' => ['required','numeric','digits_between:7,15', new PhoneNumberRule],
                'random_bytes' => ['required', new RandomBytesRule],
                'full_name' => 'required|between:5,100',
                'user_name_sign_up' => ['required','between:4,15', new UsernameSignupUnique, new UsernameSignUpRegexRule],
                'password_sign_up' => 'required|min:5'
            ];

            $messages = [
                'user_name_sign_up.required' => 'The user name field is required',
                'user_name_sign_up.between' => 'The user name field must be between :min and :max characters.',
                'password_sign_up.required' => 'The Password Field Is required',
                'password_sign_up.min' => 'Password must be at least :min characters',
            ];

            // $messages = [];

            $validation = Validator::make($req->all(), $validationRules,$messages);
            
            if ($validation->fails()){
                $validation_errors = $validation->errors();
                $all_errors = $validation_errors->get("*");
                // return json_encode($all_errors);

                foreach ($all_errors as $key => $value) {
                    $errors_str = "";
                    // $errors_str = "<p>";
                    for($i = 0; $i < count($all_errors[$key]); $i++){
                        $error = $all_errors[$key][$i];

                        $errors_str .= $error;
                        if(count($all_errors[$key]) > 1 && $i != count($all_errors[$key])){
                            // $errors_str .= "<br>";
                        }
                    }
                    // $errors_str .= "</p>";

                    $response_arr['messages'][$key] = $errors_str;
                }
            }else{
                session_unset();

                $ip_address = $_SERVER['REMOTE_ADDR'];
                if($ip_address == "::1"){
                    $ip_address = "197.211.60.81";
                }
                            
                
                $calling_code = "234";
                
                $country_id = 151;
                
                $_SESSION['country_id'] = $country_id;
                    


                $password = $post_data->password_sign_up;
                $user_name = strtolower($post_data->user_name_sign_up);
                $full_name = $post_data->full_name;
                $token = md5(uniqid(rand(), true));
                $phone = $post_data->phone;
                $email = $post_data->email;                           
                $phone_code = $calling_code;
                $hashed = sha1($password);
                $slug = Str::slug($user_name, '-');
                $date = date("j M Y");
                $time = date("h:i:sa");

    
                $_SESSION['password'] = $password;
                $_SESSION['user_name'] = $user_name;
                $_SESSION['full_name'] = $full_name;
                $_SESSION['token'] = $token;
                $_SESSION['email'] = $email;
                $_SESSION['hashed'] = $hashed;
                $_SESSION['slug'] = $slug;
                $_SESSION['code'] = $phone_code;
                $_SESSION['phone_number'] = $phone;
                $_SESSION['sponsor_user_name'] = $sponsor_user_name;
               

                $response_arr['code'] = $phone_code;
                if(substr($phone, 0,1) == "0"){
                    $phone = substr($phone, 1);
                }
                $response_arr['phone_number'] = $phone;
                $response_arr['email'] = $email;

                $mobile_no = "+" . $phone_code . "" . $phone;
                $response_arr['test'] = $mobile_no;
                $otp = rand ( 10000 , 99999 );
                $email_arr = array($email);
                if($this->main_model->sendOtpEmail($email_arr,$otp)){
                    
                    $_SESSION['otp'] = $otp;
                    //Change This Once In Production
                    if($_SERVER['SERVER_NAME'] == "127.0.0.1"){
                        $response_arr['otp'] = $otp;
                    }
                    $response_arr['success'] = true;
                }
            }
            
            echo json_encode($response_arr);
        }
    }



    public function getSponsorInfoRegistration(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");

        $post_data = (Object) $req->input(); 
        if(isset($post_data->sponsor_user_name)){
            $response_arr = array('success' => false,'user_name_does_not_exist' => false,'user_profile_img' => '','sponsor_full_name' => '','sponsor_phone_num' => '','sponsor_email_address' => '');

            $sponsor_user_name = $post_data->sponsor_user_name;
            
            if($this->main_model->checkIfUserNameExists($sponsor_user_name)){
                $sponsor_user_id = $this->main_model->getUserIdByName($sponsor_user_name);
                if($this->main_model->getUserParamById("created",$sponsor_user_id) == 1){
                    $response_arr['success'] = true;
                    $sponsor_id = $this->main_model->getUserIdByName($sponsor_user_name);
                    $user_profile_img = $this->main_model->getUserParamById("logo",$sponsor_id);
                    if(is_null($user_profile_img)){
                        $user_profile_img = "avatar.jpg";
                    }
                    $response_arr['user_profile_img'] = "<img class='col-sm-4' src='".asset('/images/'.$user_profile_img)."' style='border-radius: 50%; width: 100px; height: 100px;' alt='Sponsor Profile Image'>";
                    $sponsor_full_name = $this->main_model->getUserParamById("full_name",$sponsor_id);
                    $response_arr['sponsor_full_name'] = $sponsor_full_name;

                    $sponsor_phone_code = $this->main_model->getUserParamById("phone_code",$sponsor_id);
                    $sponsor_phone_num = $this->main_model->getUserParamById("phone",$sponsor_id);
                    $sponsor_email_address = $this->main_model->getUserParamById("email",$sponsor_id);
                    $response_arr['sponsor_email_address'] = $sponsor_email_address;
                    $response_arr['sponsor_phone_num'] = "+" . $sponsor_phone_code . "" . $sponsor_phone_num;
                }else{
                    $response_arr['user_name_does_not_exist'] = true;
                }
            }else{
                $response_arr['user_name_does_not_exist'] = true;
            }
            
            echo json_encode($response_arr);
        }
    }

    public function index(Request $req){
        if($this->main_model->confirmLoggedIn($req)){
            $this->data['logged_in'] = true;
        }else{
            
            $this->data['logged_in'] = false;
        }
        return View('home',$this->data);
    }

    public function processSendMessage(Request $req){

        $date = date("Y-m-d");
        $time = date("H:i:s");

        $post_data = (Object) $req->input(); 
        $response_arr = array('success' => false,'messages' => []);

        
        $validationRules = [
            'name' => 'required|between:6,100',
            'message' => 'required|between:10,1000',
            'mobile_number' => 'required|numeric|digits_between:7,15'

        ];

        $messages = [
            'required' => 'Text Must Be Entered In The :attribute Field',
            
            'between' => 'The :attribute value :input is not between :min - :max.',
            'in' => 'The :attribute must be one of the following types: :values',
        ];

        $messages = [];

        $validation = Validator::make($req->all(), $validationRules,$messages);
        
        if ($validation->fails()){
            $validation_errors = $validation->errors();
            $all_errors = $validation_errors->get("*");

            foreach ($all_errors as $key => $value) {
                $errors_str = "<p style='color: red' class='form-error'>";
                for($i = 0; $i < count($all_errors[$key]); $i++){
                    $error = $all_errors[$key][$i];

                    $errors_str .= $error;
                    if(count($all_errors[$key]) > 1 && $i != count($all_errors[$key])){
                        $errors_str .= "<br>";
                    }
                }
                $errors_str .= "</p>";

                $response_arr['messages'][$key] = $errors_str;
            }
        }else{
            $mobile = $post_data->mobile_number;
            $name = $post_data->name;
            $message = $post_data->message;
            

            $form_array = array(
                'mobile' => $mobile,
                'name' => $name,
                'message' => $message,
                'date' => $date,
                'time' => $time
            );

            if($this->main_model->uploadFrontPageMessage($form_array)){
                $response_arr['success'] = true;
            }
        }
        
        echo json_encode($response_arr);
    }

    public function testHome(Request $req){
        $search_val = $req->query('search');
        $filters = [
            'search' => ''
        ];

        $query = User::where("id","!=",0);
        
        if(!empty($search_val)){
            $query = $query->where('name', 'like','%' .$search_val.'%');
            
            $query = $query->orWhere('email', 'like','%' .$search_val.'%');
            
        }


        $query = $query->paginate(10);
        if(!empty($search_val)){
            $query = $query->appends(['search' => $search_val]);
            $filters['search'] = $search_val;
        }

        $users = $query;
        return Inertia::render('Home',[
            'filters' => $filters,
            'users' => $users
        ]);
    }

    public function processVendElectricity(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        
        $user_id = $this->main_model->getUserIdWhenLoggedIn();
        
        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'order_id' => '','invalid_meterno' => false,'meter_type_not_available' => false,'metertoken' => '','transaction_pending' => false);

        if(isset($post_data->disco) && isset($post_data->meter_type) && isset($post_data->amount)){
            $disco = $post_data->disco;
            $meter_type = $post_data->meter_type;
            $amount = $post_data->amount;
            // echo $this->input->post('amount') . "<br>";
            if($disco == "eko" || $disco == "ikeja" || $disco == "abuja" || $disco == "ibadan" || $disco == "enugu" || $disco == "phc" || $disco == "kano" || $disco == "kaduna" || $disco == "jos"){
                
                $metertoken = "";
                
                $validation = Support_Request::validate([
                    'amount' => ['required', 'numeric', 'max:50000'],
                    'meter_number' => ['required', 'numeric'],
                    'phone_number' => ['required', 'numeric','digits_between:7,12'],
                    'email' => ['required','email','between:10,100'],
                ]);
                if($validation){
                    $amount = $post_data->amount;
                    $meter_no = $post_data->meter_number;
                    $phone_number = $post_data->phone_number;
                    $email = $post_data->email;

                    $amount_to_debit_user = $amount;
                    $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                    // echo $user_total_amount;
                    // echo $amount;

                    // return json_encode($post_data);

                    $meter_type = strtolower($meter_type);

                    if($amount_to_debit_user <= $user_total_amount){

                        if(isset($post_data->use_payscribe) && $post_data->use_payscribe == true){
                            if($disco == "eko"){
                                $payscribe_disco = "ekedc";
                            }else if($disco == "ikeja"){
                                $payscribe_disco = "ikedc";
                            }else if($disco == "abuja"){
                                $payscribe_disco = "aedc";
                            }else if($disco == "ibadan"){
                                $payscribe_disco = "ibedc";
                            }else if($disco == "phc"){
                                $payscribe_disco = "phedc";
                            }else if($disco == "kaduna"){
                                $payscribe_disco = "kedco";
                            }else if($disco == "enugu"){
                                $payscribe_disco = "eedc";
                            }

                            $url = "https://www.payscribe.ng/api/v1/electricity/validate";
                            $use_post = true;
                            $data = array(
                                'meter_number' => $meter_no,
                                'meter_type' => strtolower($meter_type),
                                'amount' => $amount,
                                'service' => $payscribe_disco
                            );
                            $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);

                            if($this->main_model->isJson($response)){
                                $response = json_decode($response);
                                // var_dump($response);

                                if($response->status == true && $response->status_code == 200){
                                    
                                        
                                    $productCode = $response->message->details->productCode;
                                    $productToken = $response->message->details->productToken;

                                    

                                    $url = "https://www.payscribe.ng/api/v1/electricity/vend";
                                    $use_post = true;
                                    $data = array(
                                        'productCode' => $productCode,
                                        'productToken' => $productToken,
                                        'phone' => $phone_number
                                    );
                                    $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);

                                    if($this->main_model->isJson($response)){
                                        $response = json_decode($response);
                                        if(is_object($response)){
                                            
                                            if($response->status == true && $response->status_code == 200){
                                                $summary = "Debit Of " . $amount_to_debit_user . " For Electricity Recharge";

                                                if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                
                                                    if(isset($response->message->details->Reference)){
                                                        $order_id = $response->message->details->Reference;
                                                        $order_id_arr = explode("|", $order_id);
                                                        $order_id = $order_id_arr[0];
                                                    }else{
                                                        $order_id = "";
                                                    }

                                                    if(isset($response->message->details->Token)){
                                                        if(!is_null($response->message->details->Token)){
                                                            $metertoken = $response->message->details->Token;
                                                            $this->main_model->sendMeterTokenForPrepaidToUserByNotif($user_id,$email,$date,$time,$order_id,$disco,$meter_no,$amount,$metertoken);
                                                        }
                                                    }

                                                    $form_array = array(
                                                        'user_id' => $user_id,
                                                        'type' => 'electricity',
                                                        'sub_type' => $disco,
                                                        'date' => $date,
                                                        'time' => $time,
                                                        'amount' => $amount,
                                                        'number' => $meter_no,
                                                        'order_id' => $order_id
                                                    );
                                                    if($this->main_model->addTransactionStatus($form_array,true)){
                                                        $response_arr['success'] = true;
                                                        $response_arr['order_id'] = $order_id;
                                                        $response_arr['metertoken'] = $metertoken;


                                                        
                                                        if(isset($post_data->sms_check) && $post_data->sms_check == true){
                                                            $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                                                            $amount_to_debit_user = 5;
                                                            // echo $user_total_amount;
                                                            // echo $amount;

                                                            if($amount_to_debit_user <= $user_total_amount){
                                                                

                                                                if($meter_type == "prepaid"){
                                                                    $to = $phone_number;
                                                                    $message = "Your Meter Token For Meter Number " . $meter_no . " Is ". $metertoken;
                                                                    $url = "https://www.payscribe.ng/api/v1/sms";

                                                                    $use_post = true;
                                                                    $data = [
                                                                        'to' => $to,
                                                                        'message' => $message
                                                                    ];

                                                                    
                                                                    // var_dump($post_data);

                                                                    $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);


                                                                    if($this->main_model->isJson($response)){

                                                                        $response = json_decode($response);
                                                                        // var_dump($response);

                                                                        if($response->status && $response->status_code == 200){
                                                    
                                                                            $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                                                $order_id = $response->message->details->transaction_id;
                                                                                $form_array = array(
                                                                                    'user_id' => $user_id,
                                                                                    'type' => 'bulk_sms',
                                                                                    'sub_type' => "",
                                                                                    'number' => $message,
                                                                                    'date' => $date,
                                                                                    'time' => $time,
                                                                                    'amount' => $amount_to_debit_user,
                                                                                    'order_id' => $order_id
                                                                                );
                                                                                if($this->main_model->addTransactionStatus($form_array)){
                                                                                    $response_arr['success'] = true;
                                                                                    $response_arr['order_id'] = $order_id;
                                                                                }
                                                                            }
                                                                        }else if($response->status && $response->status_code == 201){
                                                                            

                                                                            $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                                                $order_id = $response->message->details->transaction_id;
                                                                                $form_array = array(
                                                                                    'user_id' => $user_id,
                                                                                    'type' => 'bulk_sms',
                                                                                    'sub_type' => "",
                                                                                    'number' => $message,
                                                                                    'date' => $date,
                                                                                    'time' => $time,
                                                                                    'amount' => $amount_to_debit_user,
                                                                                    'order_id' => $order_id
                                                                                );
                                                                                if($this->main_model->addTransactionStatus($form_array)){
                                                                                    $response_arr['success'] = true;
                                                                                    $response_arr['order_id'] = $order_id;
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }

                                                            }
                                                        }
                                                    }
                                                }
                                            }else if($response->status == true && $response->status_code == 201){
                                                $response_arr['transaction_pending'] = true;

                                                $summary = "Debit Of " . $amount_to_debit_user . " For Electricity Recharge";

                                                if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                
                                                    if(isset($response->message->details->Reference)){
                                                        $order_id = $response->message->details->Reference;
                                                        $order_id_arr = explode("|", $order_id);
                                                        $order_id = $order_id_arr[0];
                                                    }else{
                                                        $order_id = "";
                                                    }

                                                    

                                                    $form_array = array(
                                                        'user_id' => $user_id,
                                                        'type' => 'electricity',
                                                        'sub_type' => $disco,
                                                        'date' => $date,
                                                        'time' => $time,
                                                        'amount' => $amount,
                                                        'number' => $meter_no,
                                                        'order_id' => $order_id
                                                    );
                                                    if($this->main_model->addTransactionStatusOnly($form_array)){
                                                        $response_arr['success'] = true;
                                                        $response_arr['order_id'] = $order_id;
                                                        $response_arr['metertoken'] = $metertoken;
                                                    }
                                                }
                                            }
                                        }
                                    }       

                                    
                                }
                            }

                        }else{
                            
                            if($disco == "eko"){
                                $disco_code = "EKO";
                            }else if($disco == "ikeja"){
                                $disco_code = "IKEJA";
                            }else if($disco == "abuja"){
                                $disco_code = "ABUJA";
                            }else if($disco == "ibadan"){
                                $disco_code = "IBADAN";
                            }else if($disco == "enugu"){
                                $disco_code = "ENUGU";
                            }else if($disco == "phc"){
                                $disco_code = "PH";
                            }else if($disco == "kano"){
                                $disco_code = "KANO";
                            }else if($disco == "kaduna"){
                                $disco_code = "KADUNA";
                            }else if($disco == "jos"){
                                $disco_code = "JOS";
                            }


                            $url = "https://api.buypower.ng/v2/vend";
                            $use_post = true;

                            $order_id = "BP" . mt_rand(10000000, 99999999);

                            $data = array(
                                'meter' => $meter_no,
                                'disco' => $disco_code,
                                'vendType' => $meter_type,
                                'orderId' => $order_id,
                                'phone' => $phone_number,
                                'paymentType' => 'ONLINE',
                                'amount' => $amount,
                                'email' => $email
                            );
                            

                            $response = $this->main_model->buyPowerVtuCurl($url,$use_post,$data);

                            // var_dump($response);

                            if($this->main_model->isJson($response)){
                                $response = json_decode($response);
                                if(is_object($response)){
                                    if(isset($response->status)){
                                        $status = $response->status;
                                    
                                        $metertoken = "";
                                        if($status == true){

                                            $summary = "Debit Of " . $amount_to_debit_user . " For Electricity Recharge";

                                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                            
                                                // if(isset($response->message->details->reference_number)){
                                                //  $order_id = $response->message->details->reference_number;
                                                // }else{
                                                //  $order_id = "";
                                                // }

                                                if(isset($response->data->token)){
                                                    $metertoken = $response->data->token;
                                                    $this->main_model->sendMeterTokenForPrepaidToUserByNotif($user_id,$email,$date,$time,$order_id,$disco,$meter_no,$amount,$metertoken);
                                                }

                                                $form_array = array(
                                                    'user_id' => $user_id,
                                                    'type' => 'electricity',
                                                    'sub_type' => $disco,
                                                    'date' => $date,
                                                    'time' => $time,
                                                    'amount' => $amount,
                                                    'number' => $meter_no,
                                                    'order_id' => $order_id
                                                );
                                                if($this->main_model->addTransactionStatus($form_array)){
                                                    $response_arr['success'] = true;
                                                    $response_arr['order_id'] = $order_id;
                                                    $response_arr['metertoken'] = $metertoken;

                                                    if(isset($post_data->sms_check) && $post_data->sms_check == true){
                                                        $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                                                        $amount_to_debit_user = 5;
                                                        // echo $user_total_amount;
                                                        // echo $amount;

                                                        if($amount_to_debit_user <= $user_total_amount){
                                                            

                                                            if($meter_type == "prepaid"){
                                                                $to = $phone_number;
                                                                $message = "Your Meter Token For Meter Number " . $meter_no . " Is ". $metertoken;
                                                                $url = "https://www.payscribe.ng/api/v1/sms";

                                                                $use_post = true;
                                                                $data = [
                                                                    'to' => $to,
                                                                    'message' => $message
                                                                ];

                                                                
                                                                // var_dump($post_data);

                                                                $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);


                                                                if($this->main_model->isJson($response)){

                                                                    $response = json_decode($response);
                                                                    // var_dump($response);

                                                                    if($response->status && $response->status_code == 200){
                                                
                                                                        $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                                            $order_id = $response->message->details->transaction_id;
                                                                            $form_array = array(
                                                                                'user_id' => $user_id,
                                                                                'type' => 'bulk_sms',
                                                                                'sub_type' => "",
                                                                                'number' => $message,
                                                                                'date' => $date,
                                                                                'time' => $time,
                                                                                'amount' => $amount_to_debit_user,
                                                                                'order_id' => $order_id
                                                                            );
                                                                            if($this->main_model->addTransactionStatus($form_array)){
                                                                                $response_arr['success'] = true;
                                                                                $response_arr['order_id'] = $order_id;
                                                                            }
                                                                        }
                                                                    }else if($response->status && $response->status_code == 201){
                                                                        

                                                                        $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                                            $order_id = $response->message->details->transaction_id;
                                                                            $form_array = array(
                                                                                'user_id' => $user_id,
                                                                                'type' => 'bulk_sms',
                                                                                'sub_type' => "",
                                                                                'number' => $message,
                                                                                'date' => $date,
                                                                                'time' => $time,
                                                                                'amount' => $amount_to_debit_user,
                                                                                'order_id' => $order_id
                                                                            );
                                                                            if($this->main_model->addTransactionStatus($form_array)){
                                                                                $response_arr['success'] = true;
                                                                                $response_arr['order_id'] = $order_id;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }

                                                        }
                                                    }
                                                }
                                            }
                                            
                                        }
                                    }
                                }
                            }

                        }
                        
                    }else{
                        $response_arr['insuffecient_funds'] = true;
                    }
                }
            }

           
            
        }
        $response_arr = json_encode($response_arr);
        $_SESSION['response_arr'] = $response_arr;
        
        return Redirect::to(URL::route('buy_electricity'));
         
    }

    public function verifyElectricityDetails(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        
        $user_id = $this->main_model->getUserIdWhenLoggedIn();
        
        $response_arr = array('success' => false,'customer_name' => '','invalid_user' => false,'use_payscribe' => false);

        $validation = Support_Request::validate([
            'amount' => ['required', 'numeric', 'max:50000'],
            'meter_number' => ['required', 'numeric'],
            'phone_number' => ['required', 'numeric','digits_between:7,12'],
            'email' => ['required','email','between:10,100'],
        ]);
        if($validation){


            if(isset($post_data->disco) && isset($post_data->meter_type) && isset($post_data->amount)){
                $disco = $post_data->disco;
                $meter_type = $post_data->meter_type;
                $amount = $post_data->amount;
                
                
                if($disco == "eko" || $disco == "ikeja" || $disco == "abuja" || $disco == "ibadan" || $disco == "enugu" || $disco == "phc" || $disco == "kano" || $disco == "kaduna" || $disco == "jos"){

                    $meter_no = $post_data->meter_number;
                    $payscribe_disco = "";
                        
                    if($disco == "eko"){
                        $disco_code = "EKO";
                        $payscribe_disco = "ekedc";
                    }else if($disco == "ikeja"){
                        $disco_code = "IKEJA";
                        $payscribe_disco = "ikedc";
                    }else if($disco == "abuja"){
                        $disco_code = "ABUJA";
                        $payscribe_disco = "aedc";
                    }else if($disco == "ibadan"){
                        $disco_code = "IBADAN";
                        $payscribe_disco = "ibedc";
                    }else if($disco == "enugu"){
                        $disco_code = "ENUGU";
                        $payscribe_disco = "eedc";
                    }else if($disco == "phc"){
                        $disco_code = "PH";
                        $payscribe_disco = "phedc";
                    }else if($disco == "kano"){
                        $disco_code = "KANO";
                        
                    }else if($disco == "kaduna"){
                        $disco_code = "KADUNA";
                        $payscribe_disco = "kedco";
                    }else if($disco == "jos"){
                        $disco_code = "JOS";
                    }

                    if($meter_type == "prepaid"){
                        $meter_type = "PREPAID";
                    }else if($meter_type == "postpaid"){
                        $meter_type = "POSTPAID";
                    }


                    $url = "https://api.buypower.ng/v2/check/meter?meter=".$meter_no."&disco=".$disco_code."&vendType=".$meter_type."&orderId=true";
                    // echo $url;
                    $use_post = false;

                    $response = $this->main_model->buyPowerVtuCurl($url,$use_post);

                    // var_dump($response);

                    if($this->main_model->isJson($response)){
                        $response = json_decode($response);
                        if(is_object($response)){
                            if(isset($response->name)){
                                
                                $customer_name = $response->name;
                                // $minVendAmount = $response->minVendAmount;
                                // $maxVendAmount = $response->maxVendAmount;
                                
                                if($customer_name != ""){
                                    $response_arr['success'] = true;
                                    $response_arr['customer_name'] = $customer_name;

                                    if($amount < 1000 && $payscribe_disco != ""){
                                        $url = "https://www.payscribe.ng/api/v1/electricity/validate";
                                        $use_post = true;
                                        $data = array(
                                            'meter_number' => $meter_no,
                                            'meter_type' => strtolower($meter_type),
                                            'amount' => $amount,
                                            'service' => $payscribe_disco
                                        );
                                        $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);

                                        if($this->main_model->isJson($response)){
                                            $response = json_decode($response);
                                            // var_dump($response);

                                            if($response->status == true && $response->status_code == 200){
                                                // if($response->message->details->canVend == true){
                                                    $response_arr['use_payscribe'] = true;
                                                // }
                                            }
                                        }
                                    }
                                }else{
                                    $response_arr['invalid_user'] = true;
                                }
                            }else{
                                $response_arr['invalid_user'] = true;
                            }
                        }else{

                        }
                    }
                }
                
            }
        }
       
        $response_arr = json_encode($response_arr);
        $_SESSION['response_arr'] = $response_arr;
        
        return Redirect::to(URL::route('buy_electricity'));
         
    }


    

    public function loadBuyElectricityVTUPage(Request $req){
        if(isset($_SERVER['HTTP_REFERER'])){
            $previous_page = $_SERVER['HTTP_REFERER'];    
        }else{
            $previous_page = "";
        }
        $props = [
            'previous_page' => $previous_page,
            'response_arr' => (object)[]
        ];


        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }


        return Inertia::render('BuyElectricityVTU',$props);
    }




    public function processBuyCableVtu(Request $req,$type){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        // return json_encode($post_data);
        
        
        $user_id = $this->main_model->getUserIdWhenLoggedIn();
                
        if(isset($post_data->multichoice_type) && isset($post_data->smart_card_no) && isset($post_data->amount) && isset($post_data->plan) && isset($post_data->productCode) && isset($post_data->productToken)){

        
            $response_arr = array('success' => false,'insuffecient_funds' => false,'order_id' => '','invalid_no' => false,'error_message' => '','transaction_pending' => false);
                 
            $multichoice_type = $post_data->multichoice_type;
            $type = $post_data->multichoice_type;
            $smart_card_no = $post_data->smart_card_no;
            $amount = $post_data->amount;
            $plan = $post_data->plan;
            $productCode = $post_data->productCode;
            $productToken = $post_data->productToken;
            $package_name = $post_data->package_name;
            
            $phone = "07051942325";

            if($multichoice_type == "dstv" || $multichoice_type == "gotv"){
                
                    
                
                $url = "https://www.payscribe.ng/api/v1/multichoice/validate";
                $use_post = true;
                $data = array(
                    'type' => $multichoice_type,
                    'account' => $smart_card_no
                );
                $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);

                if($this->main_model->isJson($response)){
                    $response = json_decode($response);

                    if($response->status == true && $response->status_code == 200){
                        if(isset($response->message->details->customer_name)){
                            $customer_name = $response->message->details->customer_name;
                            $bouquets = $response->message->details->bouquets;
                            // $productCode = $response->message->details->productCode;
                            if(is_array($bouquets)){

                                
                                for($i = 0; $i < count($bouquets); $i++){
                                    $package_id = $bouquets[$i]->plan;
                                    $package_name = $bouquets[$i]->name;
                                    $package_amount = $bouquets[$i]->amount;

                                    if($package_id == $plan){
                                        $amount_to_debit_user = $package_amount;
                                        $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                                        // echo $user_total_amount;
                                        // echo $amount;

                                        if($amount_to_debit_user <= $user_total_amount){
                                            
                                            $url = "https://www.payscribe.ng/api/v1/multichoice/vend";
                                            $use_post = true;
                                            
                                            // $transaction_id = "PS" . mt_rand(10000000, 99999999);
                                            
                                            $data = array(
                                                'plan' => $plan,
                                                'productCode' => $productCode,
                                                'phone' => $phone,
                                                'productToken' => $productToken
                                            );
                                            

                                            $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);

                                            if($this->main_model->isJson($response)){
                                                $response = json_decode($response);
                                                // var_dump($response);
                                                if(is_object($response)){

                                                    $status = $response->status;
                                                    $status_code = $response->status_code;

                                                    if($status == true && $status_code == 200){
                                                        $summary = "Debit Of " . $amount_to_debit_user . " For CableTV Recharge";
                                                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                        
                                                            $order_id = $response->message->details->trans_id;
                                                            $form_array = array(
                                                                'user_id' => $user_id,
                                                                'type' => 'cable tv',
                                                                'sub_type' => $multichoice_type,
                                                                'number' => $smart_card_no,
                                                                'date' => $date,
                                                                'time' => $time,
                                                                'amount' => $amount_to_debit_user,
                                                                'order_id' => $order_id
                                                            );
                                                        
                                                            if($this->main_model->addTransactionStatus($form_array)){
                                                                $response_arr['success'] = true;
                                                                $response_arr['order_id'] = $order_id;
                                                            }
                                                        }   
                                                    }else if($status == true && $status_code == 201){
                                                        $response_arr['transaction_pending'] = true;
                                                        $summary = "Debit Of " . $amount_to_debit_user . " For CableTV Recharge";
                                                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                        
                                                            $order_id = $response->message->details->trans_id;
                                                            $form_array = array(
                                                                'user_id' => $user_id,
                                                                'type' => 'cable tv',
                                                                'sub_type' => $multichoice_type,
                                                                'number' => $smart_card_no,
                                                                'date' => $date,
                                                                'time' => $time,
                                                                'amount' => $amount_to_debit_user,
                                                                'order_id' => $order_id
                                                            );
                                                        
                                                            if($this->main_model->addTransactionStatus($form_array)){
                                                                $response_arr['success'] = true;
                                                                $response_arr['order_id'] = $order_id;
                                                            }
                                                        }   
                                                    }else{
                                                        if(isset($response->message->description)){
                                                            if($response->message->description != ""){
                                                                $response_arr['error_message'] = $response->message->description;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            
                                        }else{
                                            $response_arr['insuffecient_funds'] = true;
                                        }
                                    }
                                }
                            }
                        }else{
                            $response_arr['invalid_no'] = true;
                        }
                    }
                }       
            }elseif($multichoice_type == "startimes"){
                
                if(isset($post_data->cycle)){
                    // return "startimes";
                    $cycle = $post_data->cycle;
                    $package_name = $post_data->package_name;
                    
                    $url = "https://www.payscribe.ng/api/v1/startimes/validate";
                    $use_post = true;
                    $data = array(
                        'account' => $smart_card_no
                    );
                    $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);
                    // var_dump($response);
                    if($this->main_model->isJson($response)){
                        $response = json_decode($response);

                        if($response->status == true && $response->status_code == 200){
                            if(isset($response->message->details->customer_name)){
                                $customer_name = $response->message->details->customer_name;
                                $bouquets = $response->message->details->bouquets;
                                // $productCode = $response->message->details->productCode;

                                $amount_to_debit_user = $amount;
                                $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                                // echo $user_total_amount;
                                // echo $amount;

                                if($amount_to_debit_user <= $user_total_amount){
                                    
                                    $url = "https://www.payscribe.ng/api/v1/startimes/vend";
                                    $use_post = true;
                                    
                                    // $reference_id = "PS" . mt_rand(10000000, 99999999);

                                    $data = array(
                                        'plan' => $package_name,
                                        'cycle' => $cycle,
                                        'productCode' => $productCode,
                                        'phone' => $phone,
                                        'productToken' => $productToken
                                    );
                                    
                                    // echo json_encode($post_data);

                                    $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);
                                    // var_dump($response);

                                    if($this->main_model->isJson($response)){
                                        $response = json_decode($response);
                                        // var_dump($response);
                                        if(is_object($response)){
                                            $status = $response->status;
                                            $status_code = $response->status_code;

                                            if($status == true && $status_code == 200){
                                                $summary = "Debit Of " . $amount_to_debit_user . " For CableTV Recharge";
                                                if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                
                                                    $order_id = $response->message->details->trans_id;
                                                    $form_array = array(
                                                        'user_id' => $user_id,
                                                        'type' => 'cable tv',
                                                        'sub_type' => $multichoice_type,
                                                        'number' => $smart_card_no,
                                                        'date' => $date,
                                                        'time' => $time,
                                                        'amount' => $amount_to_debit_user,
                                                        'order_id' => $order_id
                                                    );
                                                
                                                    if($this->main_model->addTransactionStatus($form_array)){
                                                        $response_arr['success'] = true;
                                                        $response_arr['order_id'] = $order_id;
                                                    }
                                                }
                                            }else if($status == true && $status_code == 201){
                                                $response_arr['transaction_pending'] = true;
                                                $summary = "Debit Of " . $amount_to_debit_user . " For CableTV Recharge";
                                                if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                
                                                    $order_id = $response->message->details->trans_id;
                                                    $form_array = array(
                                                        'user_id' => $user_id,
                                                        'type' => 'cable tv',
                                                        'sub_type' => $multichoice_type,
                                                        'number' => $smart_card_no,
                                                        'date' => $date,
                                                        'time' => $time,
                                                        'amount' => $amount_to_debit_user,
                                                        'order_id' => $order_id
                                                    );
                                                
                                                    if($this->main_model->addTransactionStatus($form_array)){
                                                        $response_arr['success'] = true;
                                                        $response_arr['order_id'] = $order_id;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    
                                }else{
                                    $response_arr['insuffecient_funds'] = true;
                                }
                            }else{
                                $response_arr['invalid_no'] = true;
                            }
                        }
                    }
                }
                        
            }

            $response_arr = json_encode($response_arr);
            $_SESSION['response_arr'] = $response_arr;
            
            return Redirect::to(URL::route('cable_tv_plans',$type) . '?dn='. $smart_card_no);
            
        }

    }

    public function loadCablePlansVTUPage(Request $req,$type){
        if($type == "dstv" || $type == "gotv" || $type == "startimes"){
            
            if(isset($_SERVER['HTTP_REFERER'])){
                $previous_page = $_SERVER['HTTP_REFERER'];    
            }else{
                $previous_page = "";
            }
            $props = [
                'previous_page' => $previous_page,
                'response_arr' => (object)[],
                'cable_plans' => [],
                'no_decoder_number' => true,
                'invalid_decoder_number' => true,
                'customer_name' => '',
                'productCode' => '',
                'productToken' => '',
                'decoder_number' => '',
                'type' => $type,
                'main_type' => ''
            ];

            if($type == "dstv" || $type == "gotv"){
                $props['main_type'] = 'multichoice';
            }else{
                $props['main_type'] = 'startimes';
            }


            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            $decoder_number = $req->query('dn');
            if(!empty($decoder_number)){
                $props['no_decoder_number'] = false;
                if($type == "dstv" || $type == "gotv"){


                    $url = "https://www.payscribe.ng/api/v1/multichoice/validate";
                    $use_post = true;
                    $data = array(
                        'type' => $type,
                        'account' => $decoder_number
                    );
                    $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);
                    // return $response;

                    if($this->main_model->isJson($response)){
                        $response = json_decode($response);
                        if($response->status == true && $response->status_code == 200){
                            if(isset($response->message->details->customer_name)){
                                $customer_name = $response->message->details->customer_name;
                                $bouquets = $response->message->details->bouquets;
                                $productCode = $response->message->details->productCode;
                                $productToken = $response->message->details->productToken;
                                if(is_array($bouquets)){
                                    $props['invalid_decoder_number'] = false;
                                    $props['customer_name'] = $customer_name;
                                    $props['productCode'] = $productCode;
                                    $props['productToken'] = $productToken;
                                    $props['decoder_number'] = $decoder_number;
                                    
                                    $index = 0;
                                    $new_arr = array();
                                   

                                    
                                    for($i = 0; $i < count($bouquets); $i++){
                                        $index++;

                                        $package_id = $bouquets[$i]->plan;
                                        $package_name = $bouquets[$i]->name;
                                        $package_amount = $bouquets[$i]->amount;

                                        $new_arr[$i]['index'] = $index;
                                        $new_arr[$i]['package_id'] = $package_id;
                                        $new_arr[$i]['name'] = $package_name;
                                        $new_arr[$i]['amount'] = number_format($package_amount);
                                        $new_arr[$i]['type'] = $type;

                                    }

                                    $props['cable_plans'] = $new_arr;
                                }
                            }else if(isset($response->message->description)){
                                $props['invalid_decoder_number'] = true;
                            }
                        }
                    }   
       
                }else if($type == "startimes"){
               
                    $url = "https://www.payscribe.ng/api/v1/startimes/validate";
                    $use_post = true;
                    $data = array(
                        'account' => $decoder_number
                    );
                    $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);

                    if($this->main_model->isJson($response)){
                        $response = json_decode($response);
                        if($response->status == true && $response->status_code == 200){
                            if(isset($response->message->details->customer_name)){
                                $customer_name = $response->message->details->customer_name;
                                $bouquets = $response->message->details->bouquets;
                                $productCode = $response->message->details->productCode;
                                $productToken = $response->message->details->productToken;
                                if(is_array($bouquets)){
                                    $props['invalid_decoder_number'] = false;
                                    $props['customer_name'] = $customer_name;
                                    $props['productCode'] = $productCode;
                                    $props['productToken'] = $productToken;
                                    $props['decoder_number'] = $decoder_number;

                                    $index = 0;
                                    
                                    for($i = 0; $i < count($bouquets); $i++){
                                        $index++;

                                        
                                        $package_name = $bouquets[$i]->name;
                                        $cycles = $bouquets[$i]->cycles;
                                        $bouquets[$i]->index = $index;
                                        $bouquets[$i]->package_id = $package_name;
                                    }
                                    $props['cable_plans'] = $bouquets;
                                    
                                }
                            }else if(isset($response->message->description)){
                                $props['invalid_decoder_number'] = true;
                            }
                        }
                    }   
                }
            }


            return Inertia::render('CableTvPlans',$props);
        }else{
            abort(404);
        }
    }

    public function verifyCableTvNumber(Request $req,$type){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        
        
        $user_id = $this->main_model->getUserIdWhenLoggedIn();
        $response_arr = array('success' => false,'messages' => '','customer_name' => '','invalid_user' => false);

        if($type == "startimes"){
            $decoder_number = $post_data->smart_card_number;
            $validation = Support_Request::validate([
                'smart_card_number' => ['required', 'numeric', 'digits_between:5,20']
            ]);
        }else{
            $decoder_number = $post_data->iuc_number;
            $validation = Support_Request::validate([
                'iuc_number' => ['required', 'numeric', 'digits_between:5,20']
            ]);
        }

        if($validation){
        
            if($type == "dstv" || $type == "gotv"){


                $url = "https://www.payscribe.ng/api/v1/multichoice/validate";
                $use_post = true;
                $data = array(
                    'type' => $type,
                    'account' => $decoder_number
                );
                $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);
                // return $response;

                if($this->main_model->isJson($response)){
                    $response = json_decode($response);
                    if($response->status == true && $response->status_code == 200){
                        if(isset($response->message->details->customer_name)){
                            $customer_name = $response->message->details->customer_name;
                            $bouquets = $response->message->details->bouquets;
                            $productCode = $response->message->details->productCode;
                            $productToken = $response->message->details->productToken;
                            if(is_array($bouquets)){
                                $response_arr['success'] = true;
                                $response_arr['customer_name'] = $customer_name;
                                // $response_arr['productCode'] = $productCode;
                                // $response_arr['productToken'] = $productToken;
                            }
                        }else if(isset($response->message->description)){
                            $response_arr['invalid_user'] = true;
                        }
                    }
                }   
   
            }else if($type == "startimes"){
               
                $url = "https://www.payscribe.ng/api/v1/startimes/validate";
                $use_post = true;
                $data = array(
                    'account' => $decoder_number
                );
                $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);

                if($this->main_model->isJson($response)){
                    $response = json_decode($response);
                    if($response->status == true && $response->status_code == 200){
                        if(isset($response->message->details->customer_name)){
                            $customer_name = $response->message->details->customer_name;
                            $bouquets = $response->message->details->bouquets;
                            $productCode = $response->message->details->productCode;
                            $productToken = $response->message->details->productToken;
                            if(is_array($bouquets)){
                                $response_arr['success'] = true;
                                $response_arr['customer_name'] = $customer_name;
                                // $response_arr['productCode'] = $productCode;
                                // $response_arr['productToken'] = $productToken;

                                
                            }
                        }else if(isset($response->message->description)){
                            $response_arr['invalid_user'] = true;
                        }
                    }
                }   
            }
        }
        
        
        $response_arr = json_encode($response_arr);
        $_SESSION['response_arr'] = $response_arr;
        
        return Redirect::to(URL::route('buy_cable'));
        
    }

    public function loadBuyCableVTUPage(Request $req){
        if(isset($_SERVER['HTTP_REFERER'])){
            $previous_page = $_SERVER['HTTP_REFERER'];    
        }else{
            $previous_page = "";
        }
        $props = [
            'previous_page' => $previous_page,
            'response_arr' => (object)[]
        ];


        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }


        return Inertia::render('BuyCableVTU',$props);
    }

    public function load9mobileComboDataPlansVtu(Request $req,$type){
        if($type == "9mobile"){
            if(isset($_SERVER['HTTP_REFERER'])){
                $previous_page = $_SERVER['HTTP_REFERER'];    
            }else{
                $previous_page = "";
            }
            $props = [
                'previous_page' => $previous_page,
                'response_arr' => (object)[],
                'data_plans' => [],
                'type' => $type
            ];


            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            
            $bundles = $this->main_model->get9mobileComboDataBundles();
                                    

            if(is_object($bundles)){
                
                $index = 0;
                $j = -1;

                
                $new_arr = array();
                foreach($bundles as $row){
                    $index++;
                    $j++;
                    $product_id = $row->id;
                    $product_name = $row->data_amount;
                    $product_amount = $row->amount;
                    $new_arr[$j]['index'] = $index;
                    $new_arr[$j]['amount'] = number_format($product_amount,2);
                    $new_arr[$j]['product_name'] = $product_name;
                    $new_arr[$j]['type'] = $type;
                    $new_arr[$j]['product_id'] = $product_id;
                    $new_arr[$j]['sub_type'] = 'combo';
                    $new_arr[$j]['combo'] = true;
                   
                }
                $props['data_plans'] = $new_arr; 
            }

            return Inertia::render('9MobileComboVTU',$props);
        }
    }

    public function processBuyDataVtu(Request $req,$type){
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $post_data = (Object) $req->input();
        
        
        $user_id = $this->main_model->getUserIdWhenLoggedIn();
        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'order_id' => '','transaction_pending' => false);

        if(isset($post_data->plan)){
            $product_id = $post_data->plan['product_id'];
            
            $sub_type = $post_data->plan['sub_type'];
            
            if($type == "mtn" || $type == "glo" || $type == "airtel" || $type == "9mobile"){
                
                if(Support_Request::validate([
                    'mobile_number' => ['required', 'numeric', 'digits_between:6,15']
                ])){
                    
                    // $response_arr = json_decode('{"success": true,"messages": "","insuffecient_funds": false,"order_id": "PS163143562383655","transaction_pending": false}');
                    if($type == "mtn"){
                        
                        $network = "Mtn";
                        $mobilenetwork_code = "01";
                        $perc_disc = 0.04;
                    
                        if($sub_type == "clubkonnect"){

                            $amount = $this->main_model->getVtuDataBundleCostByProductId($type,$product_id);
                            
                            $mobile_no = $post_data->mobile_number;
                            if($post_data->plan['combo'] == false){
                                $amount_to_debit_user = round(($perc_disc * $amount) + $amount,2);
                                if($type == "mtn"){
                                    $amount_to_debit_user += 5;
                                }else{
                                    $amount_to_debit_user += 2;
                                }
                                $amount_to_debit_user += 2;
                                
                            }else{
                                $amount_to_debit_user = $amount;
                            }

                            
                            $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                            
                            if($amount_to_debit_user <= $user_total_amount){
                                
                                    

                                $url = "https://www.nellobytesystems.com/APIDatabundleV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&MobileNetwork=".$mobilenetwork_code."&DataPlan=".$product_id."&MobileNumber=".$mobile_no;
                                $use_post = true;
                                
                                
                                $response = $this->main_model->vtu_curl($url,$use_post,[]);
                                

                                if($this->main_model->isJson($response)){
                                    $response = json_decode($response);
                                    // var_dump($response);
                                    if($response->status == "ORDER_RECEIVED"){
                                        $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                            $order_id = $response->orderid;
                                            $form_array = array(
                                                'user_id' => $user_id,
                                                'type' => 'data',
                                                'sub_type' => $type,
                                                'number' => $mobile_no,
                                                'date' => $date,
                                                'time' => $time,
                                                'amount' => $amount_to_debit_user,
                                                'order_id' => $order_id
                                            );
                                            if($this->main_model->addTransactionStatus($form_array)){
                                                $response_arr['success'] = true;
                                                $response_arr['order_id'] = $order_id;
                                            }
                                        }
                                    }
                                }   

                            }else{
                                $response_arr['insuffecient_funds'] = true;
                            }
                        
                        }else if($sub_type == "payscribe"){
                            $amount = $this->main_model->getPayscribeVtuDataBundleCostByProductId($type,$product_id);
            
                            if($amount != 0){
                                
                                $mobile_no = $post_data->mobile_number;
                                
                                $amount_to_debit_user = round((0.04 * $amount) + $amount,2);
                                $amount_to_debit_user += 10;
                                
                                $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                                
                                if($amount_to_debit_user <= $user_total_amount){
                                    
                                    $url = "https://www.payscribe.ng/api/v1/data/vend";
                                    $use_post = true;
                                    $data = array(
                                        'network' => $network,
                                        'plan' => $product_id,
                                        'recipent' => $mobile_no
                                    );

                                    if($post_data->ported){
                                        $data['ported'] = true;
                                    }

                                    // return json_encode($data);
                                    
                                    $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);
                                    

                                    if($this->main_model->isJson($response)){
                                        $response = json_decode($response);
                                        
                                        // var_dump($response);
                                        if($response->status && $response->status_code == 200){
                                            
                                            $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                $order_id = $response->message->details->transaction_id;
                                                $form_array = array(
                                                    'user_id' => $user_id,
                                                    'type' => 'data',
                                                    'sub_type' => $type,
                                                    'number' => $mobile_no,
                                                    'date' => $date,
                                                    'time' => $time,
                                                    'amount' => $amount_to_debit_user,
                                                    'order_id' => $order_id
                                                );
                                                if($this->main_model->addTransactionStatus($form_array)){
                                                    $response_arr['success'] = true;
                                                    $response_arr['order_id'] = $order_id;
                                                }
                                            }
                                        }else if($response->status && $response->status_code == 201){
                                            $response_arr['transaction_pending'] = true;

                                            $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                $order_id = $response->message->details->transaction_id;
                                                $form_array = array(
                                                    'user_id' => $user_id,
                                                    'type' => 'data',
                                                    'sub_type' => $type,
                                                    'number' => $mobile_no,
                                                    'date' => $date,
                                                    'time' => $time,
                                                    'amount' => $amount_to_debit_user,
                                                    'order_id' => $order_id
                                                );
                                                if($this->main_model->addTransactionStatus($form_array)){
                                                    $response_arr['success'] = true;
                                                    $response_arr['order_id'] = $order_id;
                                                }
                                            }
                                        }
                                    }
                                
                                }else{
                                    $response_arr['insuffecient_funds'] = true;
                                }
                            }
                        }   
                    
                    }else if($type == "glo" || $type == "9mobile" || $type == "airtel"){
                        
                        if($type == "glo"){
                            $network = "GLO";
                            
                            $network = "Glo";
                            $perc_disc = 0.04;
                            $additional_charge = 12;
                        }else if($type == "airtel"){
                            $network = "AIRTEL";
                            
                            $network = "Airtel";
                            $perc_disc = 0.04;
                            $additional_charge = 4;
                        }else if($type == "9mobile"){
                            $network = "9MOBILE";
                            
                            $network = "9mobile";
                            $perc_disc = 0.04;
                            $additional_charge = 20;
                        }
                    
                        
                        
                        $mobile_no = $post_data->mobile_number;
                        
                        if($post_data->plan['combo'] == false){
                            $amount = $this->main_model->getPayscribeVtuDataBundleCostByProductId($type,$product_id);
                            $amount_to_debit_user = round((0.04 * $amount) + $amount,2);
                            $amount_to_debit_user += $additional_charge;
                        }else{
                            $amount_to_debit_user = $this->main_model->get9mobileComboCostByProductId($product_id);
                        }
                        
                        if($amount_to_debit_user != 0){
                            
                        
                            $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                            
                            if($amount_to_debit_user <= $user_total_amount){
                                if($post_data->plan['combo'] == false){
                                    $url = "https://www.payscribe.ng/api/v1/data/vend";
                                    $use_post = true;
                                    $data = array(
                                        'network' => $network,
                                        'plan' => $product_id,
                                        'recipent' => $mobile_no
                                    );

                                    if($post_data->ported){
                                        $data['ported'] = true;
                                    }

                                    
                                    $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);
                                    

                                    if($this->main_model->isJson($response)){
                                        $response = json_decode($response);
                                        // var_dump($response);
                                        if($response->status && $response->status_code == 200){
                                            
                                            $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                $order_id = $response->message->details->transaction_id;
                                                $form_array = array(
                                                    'user_id' => $user_id,
                                                    'type' => 'data',
                                                    'sub_type' => $type,
                                                    'number' => $mobile_no,
                                                    'date' => $date,
                                                    'time' => $time,
                                                    'amount' => $amount_to_debit_user,
                                                    'order_id' => $order_id
                                                );
                                                if($this->main_model->addTransactionStatus($form_array)){
                                                    $response_arr['success'] = true;
                                                    $response_arr['order_id'] = $order_id;
                                                }
                                            }
                                        }else if($response->status && $response->status_code == 201){
                                            $response_arr['transaction_pending'] = true;
                                            $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                $order_id = $response->message->details->transaction_id;
                                                $form_array = array(
                                                    'user_id' => $user_id,
                                                    'type' => 'data',
                                                    'sub_type' => $type,
                                                    'number' => $mobile_no,
                                                    'date' => $date,
                                                    'time' => $time,
                                                    'amount' => $amount_to_debit_user,
                                                    'order_id' => $order_id
                                                );
                                                if($this->main_model->addTransactionStatus($form_array)){
                                                    $response_arr['success'] = true;
                                                    $response_arr['order_id'] = $order_id;
                                                }
                                            }
                                        }
                                    }
                                }else{
                                
                                    if($type == "9mobile"){
                                        $data_amount = $this->main_model->get9mobileComboDataAmountByProductId($product_id);
                                        if($data_amount != ""){
                                            $form_array = array(
                                                'user_id' => $user_id,
                                                'type' => 'data',
                                                'sub_type' => $type,
                                                'number' => $mobile_no,
                                                'date' => $date,
                                                'time' => $time,
                                                'amount' => $amount_to_debit_user,
                                                'order_id' => ""
                                            );
                                            if($this->main_model->addTransactionStatus($form_array)){
                                                $form_array = array(
                                                    'user_id' => $user_id,
                                                    'number' => $mobile_no,
                                                    'amount' => $data_amount,
                                                    'date' => $date,
                                                    'time' => $time
                                                );
                                                if($this->main_model->addComboRequest($form_array)){
                                                    $summary = "Debit Of " . $amount_to_debit_user . " For 9mobile Data Recharge";
                                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                        $response_arr['success'] = true;
                                                        // return json_encode($response_arr);
                                                    }
                                                }
                                                
                                            }
                                        }
                                    }
                                    
                                }
                            
                            }else{
                                $response_arr['insuffecient_funds'] = true;
                            }
                        }
                    
                    }else{
                        
                        if($type == "mtn"){
                            $network = "Mtn";
                            $mobilenetwork_code = "01";
                            $perc_disc = 0.04;
                        }elseif($type == "glo"){
                            $network = "Glo";
                            $mobilenetwork_code = "02";
                            $perc_disc = 0.04;
                        }else if($type == "9mobile"){
                            $network = "9mobile";
                            $mobilenetwork_code = "03";
                            $perc_disc = 0.04;
                        }else if($type == "airtel"){
                            $network = "Airtel";
                            $mobilenetwork_code = "04";
                            $perc_disc = 0.04;
                        }


                        if($post_data->plan['combo'] == false){
                            $amount = $this->main_model->getVtuDataBundleCostByProductId($type,$product_id);
                        }else{
                            $amount = $this->main_model->get9mobileComboCostByProductId($product_id);
                        }
                        if($amount != 0){
                            
                            $mobile_no = $post_data->mobile_number;
                            if($post_data->plan['combo'] == false){
                                $amount_to_debit_user = round(($perc_disc * $amount) + $amount,2);
                                if($type == "mtn"){
                                    $amount_to_debit_user += 5;
                                }else{
                                    $amount_to_debit_user += 2;
                                }
                                $amount_to_debit_user += 2;
                                
                            }else{
                                $amount_to_debit_user = $amount;
                            }

                            // echo $amount_to_debit_user;
                            $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                            
                            if($amount_to_debit_user <= $user_total_amount){
                               if($post_data->plan['combo'] == false){
                                    

                                    $url = "https://www.nellobytesystems.com/APIDatabundleV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&MobileNetwork=".$mobilenetwork_code."&DataPlan=".$product_id."&MobileNumber=".$mobile_no;
                                    $use_post = true;
                                    
                                    
                                    $response = $this->main_model->vtu_curl($url,$use_post,[]);
                                    
                                    
                                    if($this->main_model->isJson($response)){
                                        $response = json_decode($response);
                                        // var_dump($response);
                                        if($response->status == "ORDER_RECEIVED"){
                                            $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                            if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                $order_id = $response->orderid;
                                                $form_array = array(
                                                    'user_id' => $user_id,
                                                    'type' => 'data',
                                                    'sub_type' => $type,
                                                    'number' => $mobile_no,
                                                    'date' => $date,
                                                    'time' => $time,
                                                    'amount' => $amount_to_debit_user,
                                                    'order_id' => $order_id
                                                );
                                                if($this->main_model->addTransactionStatus($form_array)){
                                                    $response_arr['success'] = true;
                                                    $response_arr['order_id'] = $order_id;
                                                }
                                            }
                                        }
                                    }   

                                }else{
                                    
                                    if($type == "9mobile"){
                                        $data_amount = $this->main_model->get9mobileComboDataAmountByProductId($product_id);
                                        if($data_amount != ""){
                                            $form_array = array(
                                                'user_id' => $user_id,
                                                'type' => 'data',
                                                'sub_type' => $type,
                                                'number' => $mobile_no,
                                                'date' => $date,
                                                'time' => $time,
                                                'amount' => $amount_to_debit_user,
                                                'order_id' => ""
                                            );
                                            if($this->main_model->addTransactionStatus($form_array)){
                                                $form_array = array(
                                                    'user_id' => $user_id,
                                                    'number' => $mobile_no,
                                                    'amount' => $data_amount,
                                                    'date' => $date,
                                                    'time' => $time
                                                );
                                                if($this->main_model->addComboRequest($form_array)){
                                                    $summary = "Debit Of " . $amount_to_debit_user . " For 9mobile Data Recharge";
                                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                                        $response_arr['success'] = true;
                                                    }
                                                }
                                                
                                            }
                                        }
                                    }
                                    
                                }
                            }else{
                                $response_arr['insuffecient_funds'] = true;
                            }
                        }
                    }   
                }
            }
        }
        
        $response_arr = json_encode($response_arr);
        $_SESSION['response_arr'] = $response_arr;
        if($post_data->plan['combo'] == false){
            return Redirect::to(URL::route('data_plans_list',$type));
        }else{
            return Redirect::to(URL::route('9mobile_combo_data_plans_list',$type));
        }
    }

    public function loadDataPlansVtu(Request $req,$type){
        if($type == "mtn" || $type == "glo" || $type == "airtel" || $type == "9mobile"){
            if(isset($_SERVER['HTTP_REFERER'])){
                $previous_page = $_SERVER['HTTP_REFERER'];    
            }else{
                $previous_page = "";
            }
            $props = [
                'previous_page' => $previous_page,
                'response_arr' => (object)[],
                'data_plans' => [],
                'type' => $type
            ];


            if(isset($_SESSION['response_arr'])){
                $response_arr = $_SESSION['response_arr'];
                unset($_SESSION['response_arr']);
                $props['response_arr'] = json_decode($response_arr);
            }

            
                
            

            if($type == "mtn"){
                $url_1 = "https://www.nellobytesystems.com/APIDatabundlePlansV1.asp";
                $url_2 = "https://www.payscribe.ng/api/v1/data/lookup";
                $use_post = true;

                $response = $this->main_model->vtu_curl($url_1,$use_post,$post_data=[]);


                if($this->main_model->isJson($response)){
                    $response = json_decode($response);
                    if(is_object($response)){
                        
                    
                        $network = "MTN";
                        $network_num = 01;
                        $network_2 = "Mtn";
                        $perc_disc = 0.04;
                    

                        
                        
                        $plans = $response->MOBILE_NETWORK->$network[0]->PRODUCT;
                        

                        if(is_array($plans)){

                            $post_data = array(
                                'network' => $network_2
                            );

                            // return $post_data;

                            
                            $response = $this->main_model->payscribeVtuCurl($url_2,$use_post,$post_data);


                            if($this->main_model->isJson($response)){
                                $response = json_decode($response);
                                // var_dump($response);
                                if(is_object($response)){
                                    if($response->status && $response->status_code == 200){
                                    
                                        
                                        // var_dump($response->message->details[]);
                                        
                                        $plans_2 = $response->message->details[0]->plans;
                                        

                                        if(is_array($plans_2)){

                                            
                                            $j = 0;

                                            // echo json_encode($plans);
                                            // echo json_encode($plans_2);

                                            $plan_1_new_arr = array();
                                            $plan_2_new_arr = array();
                                            for($i = 0; $i < count($plans); $i++){
                                                
                                                $product_code = $plans[$i]->PRODUCT_CODE;
                                                $product_id = $plans[$i]->PRODUCT_ID;
                                                $product_name = $plans[$i]->PRODUCT_NAME;
                                                $product_amount = $plans[$i]->PRODUCT_AMOUNT;
                                                // $product_amount = $product_amount + 20;

                                                $product_amount = round(($perc_disc * $product_amount) + $product_amount,2);
                                                

                                                if($type == "mtn"){
                                                    $product_amount += 5;
                                                }else{
                                                    $product_amount += 2;
                                                }

                                                $product_amount += 2;
                                                // echo $product_id . "<br>";

                                                if($product_id != "500" && $product_id != "1000" && $product_id != "2000" && $product_id != "3000" && $product_id != "5000"){
                                                    // echo $product_id . "<br>";
                                                    $plan_1_new_arr[$i] = [
                                                        "type" => $type,
                                                        "sub_type" => "clubkonnect",
                                                        "product_name" => $product_name,
                                                        "amount" => $product_amount,
                                                        "product_id" => $product_id,
                                                        "product_code" => $product_code
                                                    ];
                                                }

                                                
                                            }

                                            for($i = 0; $i < count($plans_2); $i++){
                                                
                                                $product_id = $plans_2[$i]->plan_code;
                                                // $product_id = $plans[$i]->PRODUCT_ID;
                                                $product_name = $plans_2[$i]->name;
                                                $product_amount = $plans_2[$i]->amount;
                                                // $product_amount = $product_amount + 20;
                                                
                                                $product_amount = round((0.04 * $product_amount) + $product_amount,2);

                                                $product_amount += 10;
                                            
                                                

                                                $plan_2_new_arr[$i] = [
                                                    "type" => $type,
                                                    "sub_type" => "payscribe",
                                                    "product_name" => $product_name,
                                                    "amount" => $product_amount,
                                                    "product_id" => $product_id,
                                                    "product_code" => ""

                                                ];

                                            }

                                            $all_plans_arr = array_merge($plan_2_new_arr,$plan_1_new_arr);
                                            
                                            $index = 0;
                                            for($i = 0; $i < count($all_plans_arr); $i++){
                                                $index++;
                                                $all_plans_arr[$i]['index'] = $index;
                                                $product_amount = $all_plans_arr[$i]['amount'];
                                                $all_plans_arr[$i]['amount'] = number_format($product_amount,2);
                                                $all_plans_arr[$i]['combo'] = false;
                                            }
                                            $props['data_plans'] = $all_plans_arr;
                                        }
                                    }
                                }
                            }
                        }
                        

                    }
                }
            
            
            }else if($type == "glo" || $type == "9mobile" || $type == "airtel"){
                
                $url = "https://www.payscribe.ng/api/v1/data/lookup";
                $use_post = true;
                
                if($type == "glo"){
                    $network = "GLO";
                    
                    $network_2 = "Glo";
                    $perc_disc = 0.04;
                    $additional_charge = 12;
                }else if($type == "airtel"){
                    $network = "AIRTEL";
                    
                    $network_2 = "Airtel";
                    $perc_disc = 0.04;
                    $additional_charge = 4;
                }else if($type == "9mobile"){
                    $network = "9MOBILE";
                    
                    $network_2 = "9mobile";
                    $perc_disc = 0.04;
                    $additional_charge = 20;

                }
            

                
                
                

                $post_data = array(
                    'network' => $network_2
                );

                
                $response = $this->main_model->payscribeVtuCurl($url,$use_post,$post_data);


                if($this->main_model->isJson($response)){
                    $response = json_decode($response);
                    // var_dump($response);
                    if(is_object($response)){
                        if($response->status && $response->status_code == 200){
                        
                            
                            // var_dump($response->message->details);
                            
                            $plans_2 = $response->message->details[0]->plans;
                            

                            if(is_array($plans_2)){

                                
                                $j = 0;

                                // echo json_encode($plans);
                                // echo json_encode($plans_2);

                                
                                $plan_2_new_arr = array();
                                

                                for($i = 0; $i < count($plans_2); $i++){
                                    
                                    $product_id = $plans_2[$i]->plan_code;
                                    // $product_id = $plans[$i]->PRODUCT_ID;
                                    $product_name = $plans_2[$i]->name;
                                    $product_amount = $plans_2[$i]->amount;
                                    // $product_amount = $product_amount + 20;
                                    
                                    $product_amount = round((0.04 * $product_amount) + $product_amount,2);

                                    $product_amount += $additional_charge;
                                
                                    

                                    $plan_2_new_arr[$i] = [
                                        "type" => $type,
                                        "sub_type" => "payscribe",
                                        "product_name" => $product_name,
                                        "amount" => $product_amount,
                                        "product_id" => $product_id,
                                        "product_code" => ""

                                    ];

                                }

                                $all_plans_arr = $plan_2_new_arr;

                    
                                $index = 0;
                                for($i = 0; $i < count($all_plans_arr); $i++){
                                    $index++;
                                    $all_plans_arr[$i]['index'] = $index;
                                    $product_amount = $all_plans_arr[$i]['amount'];
                                    $all_plans_arr[$i]['amount'] = number_format($product_amount,2);
                                    $all_plans_arr[$i]['combo'] = false;
                                }
                                $props['data_plans'] = $all_plans_arr;

                            }
                        }
                    }
                }
            
            }else{

                $url = "https://www.nellobytesystems.com/APIDatabundlePlansV1.asp";
                $use_post = true;

                $response = $this->main_model->vtu_curl($url,$use_post,$post_data=[]);


                if($this->main_model->isJson($response)){
                    $response = json_decode($response);
                    if(is_object($response)){
                        
                        if($type == "mtn"){
                            $network = "MTN";
                            $network_num = 01;
                            $perc_disc = 0.04;
                        }elseif($type == "glo"){
                            $network = "Glo";
                            $network_num = 02;
                            $perc_disc = 0.04;
                        }else if($type == "9mobile"){
                            $network = "9mobile";
                            $network_num = 03;
                            $perc_disc = 0.04;
                        }else if($type == "airtel"){
                            $network = "Airtel";
                            $network_num = 04;
                            $perc_disc = 0.04;
                        }

                        
                        
                        $plans = $response->MOBILE_NETWORK->$network[0]->PRODUCT;
                        

                        if(is_array($plans)){
                            $index = 0;
                            $real_plans_arr = array();

                            for($i = 0; $i < count($plans); $i++){
                                $index++;
                                $product_code = $plans[$i]->PRODUCT_CODE;
                                $product_id = $plans[$i]->PRODUCT_ID;
                                $product_name = $plans[$i]->PRODUCT_NAME;
                                $product_amount = $plans[$i]->PRODUCT_AMOUNT;
                                // $product_amount = $product_amount + 20;
                                if($type == "mtn"){
                                    $product_amount += 5;
                                }else{
                                    $product_amount += 2;
                                }

                                $product_amount += 2;

                                $product_amount = round(($perc_disc * $product_amount) + $product_amount,2);

                                $real_plans_arr[$i]['index'] = $index;
                                $real_plans_arr[$i]['amount'] = number_format($product_amount,2);
                                $real_plans_arr[$i]['product_code'] = $product_code;
                                $real_plans_arr[$i]['product_id'] = $product_id;
                                $real_plans_arr[$i]['product_name'] = $product_name;
                                $real_plans_arr[$i]['sub_type'] = 'clubkonnect';
                                $real_plans_arr[$i]['type'] = $type;
                                $real_plans_arr[$i]['combo'] = false;
                            }

                            $props['data_plans'] = $real_plans_arr;
                        }
                        

                    }
                }
            }

            return Inertia::render('DataPlansVTU',$props);
        }
    }

    public function loadBuyDataVTUPage(Request $req){
        if(isset($_SERVER['HTTP_REFERER'])){
            $previous_page = $_SERVER['HTTP_REFERER'];    
        }else{
            $previous_page = "";
        }
        $props = [
            'previous_page' => $previous_page,
            'response_arr' => (object)[]
        ];


        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }


        return Inertia::render('BuyDataVTU',$props);
    }

    public function generateVtuEpin(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");

        $user_id = $this->main_model->getUserIdWhenLoggedIn();

        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'epins' => '','invalid_amount' => false,'invalid_recipient' => false,'epins_json');
        $post_data = (Object) $req->input();
        // return $post_data;

        if(isset($post_data->type)){
            $type = $post_data->type;
            // echo $this->input->post('amount') . "<br>";
            if($type == "mtn" || $type == "glo" || $type == "airtel" || $type == "9mobile"){
                
                if(Support_Request::validate([
                    'amount' => ['required', 'numeric','min:100','max:20000'],
                    'quantity' => ['required', 'numeric','min:1','max:20']
                ])){
                    
                    $amount = $post_data->amount;
                    $quantity = $post_data->quantity;
                    $amount_to_debit_user = $amount - (0.02 * $amount);
                    $amount_to_debit_user = $amount_to_debit_user * $quantity;

                    $response_arr['amount_to_debit_user'] = $amount_to_debit_user;
                    
                    $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                    
                    
                    
                    if($amount_to_debit_user <= $user_total_amount){

                        $url = "https://www.payscribe.ng/api/v1/rechargecard";
                        
                        $use_post = true;
                        $post_data = [
                            'qty' => $quantity,
                            'amount' => $amount,
                            'display_name' => "Payscribe"
                        ];

                        $response = $this->main_model->payscribeVtuCurl($url,$use_post,$post_data);
                                

                        if($this->main_model->isJson($response)){
                            $response = json_decode($response);
                            // var_dump($response);
                            if($response->status && $response->status_code == 200){
                                $order_id = $response->message->trans_id;
                                $details = $response->message->details;

                                if(is_array($details)){

                                    $summary = "Debit Of " . $amount_to_debit_user . " For Vtu E-Pin Generation";
                                    if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                        // $epin = $this->main_model->generateUnusedEpinForThisNetworkAnAmount($code,$amount);
                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'e-pin',
                                            'sub_type' => 'payscribe_epin',
                                            
                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount_to_debit_user,
                                            'order_id' => $order_id
                                        );
                                        if($this->main_model->addTransactionStatus($form_array)){
                                            $response_arr['success'] = true;
                                            $response_arr['epins'] = $details;
                                            $response_arr['epins_json'] = json_encode($details);
                                            
                                            $response_arr['amount'] = $amount;
                                        }
                                    }
                                }
                            }
                        }
                        
                    }else{
                        $response_arr['insuffecient_funds'] = true;
                    }
                }
                
            }
        }
        $response_arr = json_encode($response_arr);
        $_SESSION['response_arr'] = $response_arr;
        return Redirect::to(URL::route('buy_airtime'));
    }

    public function processBuyAirtimeVtu(Request $req){
        $date = date("Y-m-d");
        $time = date("H:i:s");

        $user_id = $this->main_model->getUserIdWhenLoggedIn();

        $response_arr = array('success' => false,'messages' => '','insuffecient_funds' => false,'order_id' => '','invalid_amount' => false,'invalid_recipient' => false);
        $post_data = (Object) $req->input();
        // return $post_data;

        if(isset($post_data->type)){
            $type = $post_data->type;
            // echo $this->input->post('amount') . "<br>";
            if($type == "mtn" || $type == "glo" || $type == "airtel" || $type == "9mobile"){
                
                if(Support_Request::validate([
                    'amount' => ['required', 'numeric','min:100','max:50000'],
                    'mobile_number' => ['required', 'numeric', 'digits_between:6,15']
                ])){
                    
                    $amount = $post_data->amount;
                    $mobile_no = $post_data->mobile_number;
                    if($type == "mtn"){
                        $amount_to_debit_user = $amount;
                    }else if($type == "glo"){
                        $amount_to_debit_user = $amount;
                    }else if($type == "9mobile"){
                        $amount_to_debit_user = $amount;
                    }else if($type == "airtel"){
                        $amount_to_debit_user = $amount;
                    }
                    
                    $user_total_amount = $this->main_model->getUserTotalAmountByUse($user_id);
                    // echo $user_total_amount;
                    // echo $amount;

                    if($amount_to_debit_user <= $user_total_amount){
                        
                        
                        if(!isset($post_data->combo)){
                            if($type == "mtn"){
                                $mobilenetwork_code = "01";
                            }else if($type == "glo"){
                                $mobilenetwork_code = "02";
                            }else if($type == "9mobile"){
                                $mobilenetwork_code = "03";
                            }else if($type == "airtel"){
                                $mobilenetwork_code = "04";
                            }

                            $url = "https://www.nellobytesystems.com/APIAirtimeV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&MobileNetwork=". $mobilenetwork_code ."&Amount=" . $amount . "&MobileNumber=" . $mobile_no;
                            // return $url;

                            // if($this->input->post('five_times')){
                            //  $url .= "bonustype=01";
                            // }



                            $use_post = true;

                            $response = $this->main_model->vtu_curl($url,$use_post,$post_data=[]);
                            // return $response;

                            if($this->main_model->isJson($response)){
                                $response = json_decode($response);
                                if(is_object($response)){
                                    $status = $response->status;

                                    if($status == "ORDER_RECEIVED"){
                                        $summary = "Debit Of " . $amount_to_debit_user . " For Airtime Recharge";
                                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                            $order_id = $response->orderid;
                                            $form_array = array(
                                                'user_id' => $user_id,
                                                'type' => 'airtime',
                                                'sub_type' => $type,
                                                'number' => $mobile_no,
                                                'date' => $date,
                                                'time' => $time,
                                                'amount' => $amount,
                                                'order_id' => $order_id
                                            );
                                            if($this->main_model->addTransactionStatus($form_array)){
                                                $response_arr['success'] = true;
                                                $response_arr['order_id'] = $order_id;
                                            }
                                        }
                                    }else if($status == "INVALID_AMOUNT"){
                                        $response_arr['invalid_amount'] = true;
                                        
                                    }else if($status == "INVALID_RECIPIENT"){
                                        $response_arr['invalid_recipient'] = true;
                                        
                                    }else if($status == "INSUFFICIENT_BALANCE"){
                                        // $response_arr['invalid_recipient'] = true;
                                        // echo "string";
                                        
                                    }else if($status == "INVALID_CREDENTIALS"){
                                        // $response_arr['invalid_recipient'] = true;
                                        // echo "string";
                                        
                                    }
                                }
                            }
                        }else{
                            if($type == "9mobile"){
                                $form_array = array(
                                    'user_id' => $user_id,
                                    'type' => 'airtime',
                                    'sub_type' => $type,
                                    'number' => $mobile_no,
                                    'date' => $date,
                                    'time' => $time,
                                    'amount' => $amount,
                                    'order_id' => ""
                                );
                                if($this->main_model->addTransactionStatus($form_array)){
                                    $form_array = array(
                                        'user_id' => $user_id,
                                        'number' => $mobile_no,
                                        'amount' => $amount,
                                        'date' => $date,
                                        'time' => $time
                                    );
                                    if($this->main_model->addComboRequest($form_array)){
                                        $summary = "Debit Of " . $amount_to_debit_user . " For 9mobile Combo Airtime Recharge";
                                        if($this->main_model->debitUser($user_id,$amount_to_debit_user,$summary)){
                                            $response_arr['success'] = true;
                                        }
                                    }
                                    
                                }
                            }
                            
                        }
                        
                    }else{
                        $response_arr['insuffecient_funds'] = true;
                    }
                }
                
            }
        }
        $response_arr = json_encode($response_arr);
        return Redirect::to(URL::route('buy_airtime').'?response_arr='.$response_arr);
    }

    public function processSignIn(Request $req){
        $post_data = (Object) $req->input(); 
        $response_arr = array('success' => false,'messages' => array(),'test' => 0,'user_exists' => true,'wrong_password' => false,'user_info' => array(),'half_registered' => false);

        
        $validationRules = [
            'user_name_login' => ['required','max:40'],
            'password_login' => 'required|min:5'
        ];

        

        $messages = [
            'user_name_login.required' => 'The user name field is required',
            'user_name_login.max' => 'The user name field must not have more than :max characters',
            'password_login.min' => 'The password field must have at least :min characters',
        ];


        $validation = Validator::make($req->all(), $validationRules,$messages);
        
        if ($validation->fails()){
            $validation_errors = $validation->errors();
            $all_errors = $validation_errors->get("*");

            foreach ($all_errors as $key => $value) {
                $errors_str = "<p style='color: red' class='form-error'>";
                for($i = 0; $i < count($all_errors[$key]); $i++){
                    $error = $all_errors[$key][$i];

                    $errors_str .= $error;
                    if(count($all_errors[$key]) > 1 && $i != count($all_errors[$key])){
                        $errors_str .= "<br>";
                    }
                }
                $errors_str .= "</p>";

                $response_arr['messages'][$key] = $errors_str;
            }
        }else{
            

            $user_name = $post_data->user_name_login;
            $hashed = sha1($post_data->password_login);
            if($this->main_model->userExists($user_name)){
                
                $response_arr['test'] = 1;
                if($this->main_model->password_verify($user_name,$hashed)){
                    
                    $user_info = $this->main_model->getUserInfoByUserName($user_name);
                    if(is_object($user_info)){
                        foreach($user_info as $user){
                            $user_id = $user->id;
                            $user_token = $user->token;
                            $created = $user->created;
                            $user_slug = $user->slug;
                        }
                        if($created == 1){
                            
                            if($this->main_model->onRegister($user_id,$user_token)){
                                $this->main_model->updateUserLastActivity($user_id);
                                
                                $req->session()->flash('login', true);
                                $response_arr['success'] = true;
                                
                                $response_arr['url'] = "/admin_page";
                                
                                if(!isset($post_data->android)){
                                    $response_arr['user_info'] = $this->main_model->getAllUserInfo($user_id);
                                }                                   
                            }
                        }else{
                            $response_arr['success'] = true;
                                
                            $response_arr['url'] = '/registration_step_2/'.$user_slug;
                        }   

                    }
                }else{
                    $response_arr['wrong_password'] = true;
                }
            }else{
                $response_arr['user_exists'] = false;
            }
        }
        echo json_encode($response_arr);     
    }

    

    public function loadLoginPage(Request $request){
        if($this->main_model->confirmLoggedIn($request,true)){
            $request->session()->flash('login', true);
            return redirect("/admin_page");
        }
        
        return View('login',$this->data);
    }

    public function loadBuyAirtimeVTUPage(Request $req){
        if(isset($_SERVER['HTTP_REFERER'])){
            $previous_page = $_SERVER['HTTP_REFERER'];    
        }else{
            $previous_page = "";
        }
        $props = [
            'previous_page' => $previous_page,
            'response_arr' => (object)[]
        ];


        if(isset($_SESSION['response_arr'])){
            $response_arr = $_SESSION['response_arr'];
            unset($_SESSION['response_arr']);
            $props['response_arr'] = json_decode($response_arr);
        }


        return Inertia::render('BuyAirtimeVTU',$props);
    }

    public function loadRechargeVTUPage(Request $req){
        $props = [
            
            'response_arr' => (object)[]
        ];

        $response_arr = $req->query('response_arr');
        if(!empty($response_arr)){
            $props['response_arr'] = json_decode($response_arr);
        }


        return Inertia::render('RechargeVTU',$props);
    }

    public function processCreateNewUser(Request $req){

        $date = date("Y-m-d");
        $time = date("H:i:s");

        $response_arr = array('success' => false);
        $post_data = (Object) $req->input();
        
        if(Support_Request::validate([
            'name' => ['required', 'max:100'],
            'email' => ['required', 'max:50', 'email'],
            'password' => ['required', 'min:8'],
        ])){
            $name = $post_data->name;
            $email = $post_data->email;
            $password = $post_data->password;
            $form_array  = array(
                'name' => $name,
                'email' => $email,
                'email_verified_at' => $date . " " . $time,
                'created_at' => $date . " " . $time,
                'password' => Hash::make($password)
            );

            if($this->user->addNewUser($form_array)){
                $response_arr['success'] = true;
            }
        }
        $response_arr = json_encode($response_arr);
        return Redirect::to(URL::route('create_user').'?response_arr='.$response_arr);
    }

    public function createUserPage(Request $req){

        $props = [
            
            'response_arr' => (object)[]
        ];

        $response_arr = $req->query('response_arr');
        if(!empty($response_arr)){
            $props['response_arr'] = json_decode($response_arr);
        }


        return Inertia::render('CreateUser',$props);
    }

    public function deleteUser(Request $req,$user_id){
        $response_arr = array('success' => false,'invalid_id' => true);
        if($this->user->checkIfUserIdIsValid($user_id)){
            $response_arr['invalid_id'] = false;
            if($this->user->deleteUser($user_id)){
                $response_arr['success'] = true;
            }
        }
        $response_arr = json_encode($response_arr);

        return Redirect::to(URL::route('edit_user',$user_id).'?response_arr='.$response_arr);
    }

    public function updateUserInfo(Request $req,$user_id){
        $date = date("Y-m-d");
        $time = date("H:i:s");

        $response_arr = array('success' => false);
        $post_data = (Object) $req->input();
        
        if(Support_Request::validate([
            'name' => ['required', 'max:100'],
            'email' => ['required', 'max:50', 'email']
        ])){
            $name = $post_data->name;
            $email = $post_data->email;
            $form_array  = array(
                'name' => $name,
                'email' => $email,
                'updated_at' => $date . " " . $time
            );

            if($this->user->editUser($form_array,$user_id)){
                $response_arr['success'] = true;
            }
        }
        $response_arr = json_encode($response_arr);
        return Redirect::to(URL::route('edit_user',$user_id).'?response_arr='.$response_arr);
    }

    public function editUserPage(Request $req,$id){
        $user_info = User::find($id);
        $props = [
            'user_info' => $user_info,
            'response_arr' => (object)[]
        ];

        $response_arr = $req->query('response_arr');
        if(!empty($response_arr)){
            $props['response_arr'] = json_decode($response_arr);
        }


        return Inertia::render('EditUser',$props);
    }

    

    public function secondPage(Request $req){

        $post_data = (Object) $req->input();

        $info = "";
        if(isset($post_data->info)){
            $info = $post_data->info;
        }

        return Inertia::render('Page2',[
            'info' => $info
        ]);
    }
}
