<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminte\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use DateTime;

class MainModel extends Model
{
    use HasFactory;

    public function getCoopSavingParamByIdAndUserId($param,$saving_id,$user_id){
        $query = DB::table('coop_savings')->where('id', $saving_id)->where('user_id', $user_id)->get($param);
        if($query->count() == 1){
            return $query[0]->$param;
        }else{
            return false;
        }
    }

    public function processCoopSavingWithdrawal($form_array,$saving_id,$user_id){
        return DB::table('coop_savings')->where('id',$saving_id)->where('user_id', $user_id)->update($form_array);
    }


    public function checkUserCoopSavingsInfoById($saving_id){
        $query = DB::table('coop_savings')->where('id', $saving_id)->get();
        if($query->count() == 1){
            return $query;
        }else{
            return false;
        }
    }

    public function checkIfThisCoopSavingsIdIsValidAndBelongsToUser($saving_id,$user_id){
        $query = DB::table('coop_savings')->where('id', $saving_id)->where('user_id', $user_id)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function getSavingsHistoryForUser($user_id,$req,$length){
        
        
        $amount = $req->query('amount');
        $time_frame = $req->query('time_frame');
        $status = $req->query('status');
        $withdrawn_amount = $req->query('withdrawn_amount');
        $last_withdrawn_date_time = $req->query('last_withdrawn_date_time');
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');

        
        $query = DB::table('coop_savings')->where('id','!=', 0)->where('user_id',$user_id);
            
        

        if(!empty($amount)){
            $query = $query->where('amount', 'like', $amount.'%');

        }

        if(!empty($time_frame)){
            $query = $query->where('time_frame', $time_frame);

        }

        if(!empty($status)){
            
            if($status == "unwithdrawn"){
                
                $query = $query->where('withdrawn',0)->where('part_withdrawn',0)->where('withdrawn_amount',0.00)->where('last_withdrawn_date_time',"");
            }else if($status == "part_withdrawn"){
                
                $query = $query->where('withdrawn',0)->where('part_withdrawn',1)->where('withdrawn_amount','!=',0.00)->where('last_withdrawn_date_time','!=',"");
            }else if($status == "full_withdrawn"){
                
                $query = $query->where('withdrawn',1)->where('part_withdrawn',0)->where('withdrawn_amount','!=',0.00)->where('last_withdrawn_date_time','!=',"");
            }else if($status == "all"){
                
            }
        }

        if(!empty($withdrawn_amount)){
            $query = $query->where('withdrawn_amount', 'like', $withdrawn_amount.'%');

        }

        if(!empty($last_withdrawn_date_time)){
            
            if($last_withdrawn_date_time != ""){
               $last_withdrawn_date_time = date("j M Y", strtotime($last_withdrawn_date_time));    
            }
            $query = $query->where('last_withdrawn_date_time', 'like',  $last_withdrawn_date_time.'%');
        }

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;

    }

    public function makeSavingForCoopUser($form_array){
        $user_id = $form_array['user_id'];
        $form_array['user_name'] = $this->getUserNameById($user_id);
        return DB::table('coop_savings')->insert($form_array);
    }


    public function getUsersCoopInvPaginationByOffset($req,$length){
        
        $full_name = $req->query('full_name');
        $user_name = $req->query('user_name');
        // $phone = $req->query('phone');
        // $email = $req->query('email');
        // $created_date = $req->query('created_date');
        
        // $start_date = $req->query('start_date');
        // $end_date = $req->query('end_date');
        
        
        $query = DB::table('users')->where('is_admin', 0)->where('coop_db_id','!=', NULL);
        

        if(!empty($full_name)){
            $query = $query->where('full_name', 'like', '%' . $full_name.'%');

        }

        if(!empty($user_name)){
            $query = $query->where('user_name', 'like', '%' . $user_name.'%');

        }

        // if(!empty($phone)){
        //     $query = $query->where('phone', 'like', '%' . $phone.'%');

        // }

        // if(!empty($email)){
        //     $query = $query->where('email', 'like', '%' . $email.'%');

        // }

        


        // if(!empty($created_date)){
            
        //     if($created_date != ""){
        //        $created_date = date("j M Y", strtotime($created_date));    
        //     }
        //     $query = $query->where('created_date', 'like',  $created_date.'%');
        // }

        

        // if(!empty($start_date) && !empty($end_date)){

        //     $start_date = date("Y-m-d", strtotime($start_date));  
        //     $end_date = date("Y-m-d", strtotime($end_date));  
        //     $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        // }


        $query = $query->orderBy("full_name","ASC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 


    }

    public function checkIfCoopDbIdBelongsToUser($mlm_db_id,$user_id){
        
        $query = DB::table('coop_db')->where('user_id', $user_id)->where('id', $mlm_db_id)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function printCoopInvTree($package1,$your_mlm_db_id,$level=0, $parentID=null,$stage,$return_str = "",$j = 0)
        {
            $j++;
            // echo $j;
            // echo $level;

            // echo '<ul>';
            // echo '<li>';
            // echo '<span class="tf-nc">'.$parentID.'</span>';
            // Create the query
            $num_1 = false;
            $query_str = "SELECT * FROM coop_db WHERE ";
            if($parentID == null) {
                $query_str .= "under IS NULL";
            }
            else {
                $query_str .= "`under`=" . intval($parentID);
            }

            $query_str .= " ORDER BY positioning ASC";
            // Execute the query and go through the results.
            
            $query = DB::select($query_str);
            if(count($query) > 0){
                if(count($query) == 1){
                    $num_1 = true;
                }
                
                $return_str .= '<ul>';
                foreach($query as $row)
                {
                    
                    
                    // Print the current ID;
                    $currentID = $row->id;
                    $positioning = $row->positioning;
                    $user_id = $row->user_id;
                    $date_created = $row->date_created;
                    $logo = $this->getUserParamById("logo",$user_id);
                    $user_name = $this->getUserParamById("user_name",$user_id);
                    $full_name = $this->getUserParamById("full_name",$user_id);
                    $package = "basic";
                    $index = $this->getCoopIdsIndexNumber($currentID);
                    $full_phone_number = $this->getFullMobileNoByUserName($user_name);

                    
                    if(is_null($logo)){
                        $logo = '/images/nophoto.jpg';
                    }else{
                        $logo = '/storage/images/'. $logo;
                    }
                    if($num_1){
                        if($positioning == "left"){
                            $return_str .= '<li>';
                            $return_str .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';

                            $return_str .= '<img class="tree_icon" src="'.$logo.'"">';
                            $return_str .= '<p class="demo_name_style">';

                            
                            $return_str.= '<i onclick="goCoopInvUpMlm(this,event,'.$currentID.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-up" style="cursor:pointer;"></i>';
                            

                            $return_str .= " " . $user_name . "  ";

                            
                              
                            $return_str.= '<i onclick="goCoopInvDownMlm(this,event,'.$currentID.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-down" style="cursor:pointer;"></i>';
                            

                            $return_str .= '</p>';
                            $return_str .= '</div>';
                            // $return_str .= '</div>';
                            // $return_str .= '</li>';

                            // $return_str .= '<li>';
                            // $return_str .= '<div style="cursor:pointer;" class="tf-nc register" data-under="'.$parentID .'">';
                            
                            // $return_str .= '<p class="register-text">Register</p>';
                            // // echo '<span class="tf-nc">'.$currentID.'</span>';
                            // $return_str .= '</div>';
                            // $return_str .= '</li>';
                           
                        }else{
                            
                            $return_str .= '<li>';
                            $return_str .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';

                            $return_str .= '<img class="tree_icon" src="'.$logo.'">';
                            $return_str .= '<p class="demo_name_style">';


                            $return_str.= '<i onclick="goCoopInvUpMlm(this,event,'.$currentID.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-up" style="cursor:pointer;"></i>';
                            

                            $return_str .= " " . $user_name . "  ";

                            
                              
                            $return_str.= '<i onclick="goCoopInvDownMlm(this,event,'.$currentID.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-down" style="cursor:pointer;"></i>';

                            $return_str .= '</p>';
                            $return_str .= '</div>';
                        }
                    }else{
                        $return_str .= '<li>';
                        $return_str .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';


                        $return_str .= '<img class="tree_icon" src="'.$logo.'">';
                        $return_str .= '<p class="demo_name_style">';

                        $return_str.= '<i onclick="goCoopInvUpMlm(this,event,'.$currentID.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-up" style="cursor:pointer;"></i>';
                            

                        $return_str .= " " . $user_name . "  ";

                        
                          
                        $return_str.= '<i onclick="goCoopInvDownMlm(this,event,'.$currentID.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-down" style="cursor:pointer;"></i>';

                        $return_str .= '</p>';
                        $return_str .= '</div>';
                        // $return_str .= '</div>';
                            
                    }
                    
                    for($i = 0; $i < $level; $i++) {
                        $return_str .= " ";
                    }

                    // echo $currentID . PHP_EOL;
                    // Print all children of the current ID
                    if($j < $stage){
                        // echo $j;
                        $return_str = $this->printCoopInvTree($package1,$your_mlm_db_id,$level+1, $currentID,$stage,$return_str,$j);
                    }
                   
                    $return_str .= '</li>';
                }
                $return_str .= '</ul>';
                
            }
            else {
          
            }
            
            return $return_str;
        }


    public function getCoopIdsIndexNumber($mlm_db_id){
        $array = array();
        $user_id = $this->getCoopInvMlmDbParamById("user_id",$mlm_db_id);
        // $this->db->select("id");
        // $this->db->from("mlm_db");
        // $this->db->where("user_id",$user_id);
        // $this->db->order_by("id","ASC");

        // $query = $this->db->get();

        $query = DB::table('coop_db')->where("user_id",$user_id)->orderBy("id","ASC")->get('id');
        if($query->count() > 0){
            foreach($query as $row){
                $id = $row->id;
                $array[] = $id;
            }
        }

        if(count($array) > 0){
            for($i = 0; $i < count($array); $i++){
                if($mlm_db_id == $array[$i]){
                    return $i + 1;
                }
            }
        }
    }

    public function insertRecord(){
        $date = date("j M Y");
        $time = date("h:i:sa");
        DB::table('test')->insert(array('date' => $date,'time' => $time));
    }

    public function getTotalPendingAmountForCoopInvestment(){
        $total_amount = 0;
        $query = DB::table('coop_investments')->where('settled',0)->get('amount');
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $total_amount += $amount;
            }
        }
        return $total_amount;
    }

    public function getInvestmentHistoryForAdmin($req,$length){
        
        $user_name = $req->query('user_name');
        $amount = $req->query('amount');
        $duration = $req->query('duration');
        $status = $req->query('status');
        $settled_amount = $req->query('settled_amount');
        $settled_date_time = $req->query('settled_date_time');
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');

        
        $query = DB::table('coop_investments')->where('id','!=', 0);
            
        if(!empty($user_name)){
            $query = $query->where('user_name', 'like', '%' . $user_name . '%');

        }

        if(!empty($amount)){
            $query = $query->where('amount', 'like', $amount.'%');

        }

        if(!empty($duration)){
            $query = $query->where('duration', $duration);

        }

        if(!empty($status)){
            
            if($status == "pending"){
                
                $query = $query->where('settled',0);
            }else if($status == "settled"){
                
                $query = $query->where('settled',1);
            }else if($status == "all"){
                
            }
        }

        if(!empty($settled_amount)){
            $query = $query->where('settled_amount', 'like', $settled_amount.'%');

        }

        if(!empty($settled_date_time)){
            
            if($settled_date_time != ""){
               $settled_date_time = date("j M Y", strtotime($settled_date_time));    
            }
            $query = $query->where('settled_date_time', 'like',  $settled_date_time.'%');
        }

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;

    }

    public function getInvestmentHistoryForUser($user_id,$req,$length){
        
        
        $amount = $req->query('amount');
        $duration = $req->query('duration');
        $status = $req->query('status');
        $settled_amount = $req->query('settled_amount');
        $settled_date_time = $req->query('settled_date_time');
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');

        
        $query = DB::table('coop_investments')->where('id','!=', 0)->where('user_id',$user_id);
            
        

        if(!empty($amount)){
            $query = $query->where('amount', 'like', $amount.'%');

        }

        if(!empty($duration)){
            $query = $query->where('duration', $duration);

        }

        if(!empty($status)){
            
            if($status == "pending"){
                
                $query = $query->where('settled',0);
            }else if($status == "settled"){
                
                $query = $query->where('settled',1);
            }else if($status == "all"){
                
            }
        }

        if(!empty($settled_amount)){
            $query = $query->where('settled_amount', 'like', $settled_amount.'%');

        }

        if(!empty($settled_date_time)){
            
            if($settled_date_time != ""){
               $settled_date_time = date("j M Y", strtotime($settled_date_time));    
            }
            $query = $query->where('settled_date_time', 'like',  $settled_date_time.'%');
        }

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;

    }

    public function checkIfUserHasPendingCoopInvestmentsToBeCredited($user_id){
        $query = DB::table('coop_investments')->where('user_id', $user_id)->where('settled',0)->get();
        if($query->count() > 0){
            foreach($query as $row){
                $id = $row->id;
                $user_id = $row->user_id;
                $amount = $row->amount;
                $duration = $row->duration;
                $settled = $row->settled;
                $date = $row->date;
                $time = $row->time;
                $date_time = $row->date_time;

                $duration_in_days = $duration * 30;

                $curr_date = date("Y-m-d");
                // $curr_date = "17 Apr 2022";
                $date_diff = $this->dateDiffInDays($date, $curr_date);
                

                if($date_diff >= $duration_in_days){
                    $summary = "Cooperative Investment Capital";
                    $this->creditUser($user_id,$amount,$summary);

                    $interest = (0.05 * $amount) * $duration;
                    

                    $summary = "Cooperative Investment Interest";
                    $this->creditUser($user_id,$interest,$summary);

                    $form_array = array(
                        'settled' => 1,
                        'settled_amount' => $amount + $interest,
                        'settled_date_time' => date("j M Y h:i:sa")
                    );

                    DB::table('coop_investments')->where('id',$id)->update($form_array);
                }
            }
        }
    }

    public function makeInvestmentForCoopUser($form_array){
        $user_id = $form_array['user_id'];
        $form_array['user_name'] = $this->getUserNameById($user_id);
        return DB::table('coop_investments')->insert($form_array);
    }

    public function checkIfAdminAllowedInvestments(){
        $query = DB::table('users')->where('id', 10)->get('allow_investments');
        if($query->count() == 1){
            if($query[0]->allow_investments == 1){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function getCoopInvLoanableAmountForUser($user_id){
        $loanable_amount = 0;

        if($this->checkIfUserDoesNotHaveAnyPendingLoan($user_id)){
            $loanable_amount = $this->getTotalCoopInEarningsInLast30Days1($user_id);
            // $loanable_amount = 50000;

            // if($loanable_amount > 10000){
            //     $loanable_amount = 10000;
            // }
            $loanable_amount = $loanable_amount / 2;
        }
        
        
        return $loanable_amount;
    }

    public function checkIfUserDoesNotHaveAnyPendingLoan($user_id){
        $query = DB::table('coop_loans')->where('user_id',$user_id)->where('paid',0)->get();
        if($query->count() > 0){
            return false;
        }else{
            return true;
        }
    }

    public function getTotalCoopInEarningsInLast30Days1($user_id){
        $total_amount = 0;
        $query = DB::table('coop_weekly_earnings')->where('user_id',$user_id)->orderBy("id","DESC")->limit(4)->get();

        if($query->count() > 0){
            foreach($query as $row){
                $total_earnings = $row->total_earnings;
                $total_amount += $total_earnings;
            }
        }

        return $total_amount;
    }

    public function getTotalCoopInEarningsInLast30Days($user_id){
        $total_amount = 0;
        $d2 = date('c', strtotime('-30 days'));

        $start_date = date("Y-m-d", strtotime($d2));  
        $end_date = date("Y-m-d");  
        
        $query = DB::table('coop_earnings')->where('user_id',$user_id)->whereBetween('date_time', [$start_date, $end_date])->get();
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $total_amount += $amount;
            }
        }
        return $total_amount;
    }

    public function getWeeklyEarningDetailsById($id){
        $query = DB::table('coop_weekly_earnings')->where('id',$id)->get();
        if($query->count() == 1){
            return $query;
        }else{
            return false;
        }
    }

    public function getCoopInvEarningsWeeksForUser($user_id,$coop_db_id,$req,$length){
        
        
        $week_name = $req->query('week_name');
        $total_earnings = $req->query('total_earnings');
        $last_credit_date_time = $req->query('last_credit_date_time');
        $charged = $req->query('charged');
        $amt_charged = $req->query('amt_charged');
        $charged_date_time = $req->query('charged_date_time');
        $withdrawable = $req->query('withdrawable');
        $withdrawn = $req->query('withdrawn');
        $withdrawn_date_time = $req->query('withdrawn_date_time');
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');

        


        
        $query = DB::table('coop_weekly_earnings')->where('id','!=', 0)->where('user_id',$user_id)->where('coop_db_id', $coop_db_id);
            
        if(!empty($week_name)){
            $week_name = "week_" . $week_name;
            $query = $query->where('week','like', $week_name.'%');

        }

        if(!empty($total_earnings)){
            $query = $query->where('total_earnings', 'like', '%' . $total_earnings.'%');

        }

        if(!empty($last_credit_date_time)){
            
            if($last_credit_date_time != ""){
               $last_credit_date_time = date("j M Y", strtotime($last_credit_date_time));    
            }
            $query = $query->where('last_credit_date_time', 'like',  $last_credit_date_time.'%');
        }

        if(!empty($charged)){
            if($charged){
                $query = $query->where('charged', 1);
                if(!empty($amt_charged)){
                    $query = $query->where('amt_charged', 'like', '%' . $amt_charged.'%');

                }

                if(!empty($charged_date_time)){
                    if($charged_date_time != ""){
                       $charged_date_time = date("j M Y", strtotime($charged_date_time));    
                    }
                    $query = $query->where('charged_date_time', 'like',  $charged_date_time.'%');
                }
            }
            

        }

        if(!empty($withdrawable)){
            if($withdrawable){
                $query = $query->where('withdrawable',1);
            }
        }

        if(!empty($withdrawn)){
            if($withdrawn){
                $query = $query->where('withdrawn', 1);

                if(!empty($withdrawn_date_time)){
                    if($withdrawn_date_time != ""){
                       $withdrawn_date_time = date("j M Y", strtotime($withdrawn_date_time));    
                    }
                    $query = $query->where('withdrawn_date_time', 'like',  $withdrawn_date_time.'%');
                }
            }
            

        }


        if(!empty($date)){
            
            if($date != ""){
               $date = date("Y-m-d", strtotime($date));    
            }
            $query = $query->where('date_time', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;

    }


    public function creditUsersWeeklyEarnings($user_id,$coop_db_id,$main_placement_income,$date,$time){
        // $this->performWeeklyChecksOnUsersCoopInvEarnings($user_id,$coop_db_id,$date,$time);
        $current_week = $this->getCurrentWeekDetailsWeeklyCoopEarnings($user_id,$coop_db_id);
        if(is_object($current_week)){
            foreach($current_week as $row){
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

                $new_total_earnings = $total_earnings + $main_placement_income;

                $form_array = array(
                    'total_earnings' => $new_total_earnings,
                    'last_credit_date_time' => $date . " " . $time
                );

                $this->updateCoopWeeklyEarningTableById($form_array,$id);
            }
        }
        
    }

    public function getCurrentWeekDetailsWeeklyCoopEarnings($user_id,$coop_db_id){
        $query = DB::table('coop_weekly_earnings')->where('user_id', $user_id)->where('coop_db_id', $coop_db_id)->orderBy("id","desc")->limit(1)->get();
        if($query->count() == 1){
            return $query;
        }else{
            return false;
        }
    }

    public function performWeeklyChecksOnUsersCoopInvEarnings($user_id,$coop_db_id,$date,$time){
        if($this->checkIfUserAlreadyHasRecordInCoopWeeklyEarnings($user_id,$coop_db_id)){
            $date_of_registration = $this->getCoopUsersDateOfRegistrationWeeklyEarnings($user_id,$coop_db_id);
            // $date_of_registration = date('j M Y', strtotime($date_of_registration));
            $users_current_week = $this->getUsersCurrentWeekCoopInvWeeklyEarnings($user_id,$coop_db_id);
            $users_current_week_id = $this->getUsersCoopInvWeeklyEarningsParamByUserIdCoopIdWeekName("id",$user_id,$coop_db_id,$users_current_week);
            $users_current_week_date = date('j M Y', strtotime($this->getUsersCoopInvWeeklyEarningsParamById("date_time",$users_current_week_id)));
            $date_diff = $this->dateDiffInDays($users_current_week_date, $date);
            if($date_diff >= 7){
                //Perform The debiting of 2000 if user has up to 2000 earnings for that week then create new week
                $old_week_number = substr($users_current_week, 5);
                $new_week_number = $old_week_number + 1;
                $new_week_name = "week_" . $new_week_number;
                $total_week_earnings = $this->getUsersCoopInvWeeklyEarningsParamById("total_earnings",$users_current_week_id);
                //If user has up to 2000 the previous week, debit him and make him able to withdraw
                $service_charge = 2000;
                if($total_week_earnings >= $service_charge){
                    $earnings_rem = $total_week_earnings - $service_charge;

                    $form_array = array(
                        'total_earnings' => $earnings_rem,
                        'charged' => 1,
                        'amt_charged' => $service_charge,
                        'charged_date_time' => $date . " " . $time,
                        'withdrawable' => 1,
                    );
                    $this->updateCoopWeeklyEarningTableById($form_array,$users_current_week_id);
                    $form_array = array(
                        'week' => $new_week_name,
                        'user_id' => $user_id,
                        'coop_db_id' => $coop_db_id,
                        'date_of_registration' => $date_of_registration,

                    );
                    $this->createNewCoopWeeklyEarningRecord($form_array);
                    
                    $sponsor_income = $this->getSponsorChargeForCoopInv();
                    $sponsors_user_id = $this->getUserParamById('coop_db_sponsor_id',$user_id);
                    $this->creditUserSponsorIncomeCoopInv($user_id,$sponsors_user_id,$sponsor_income,$date,$time);
                    $placement_income = $this->getPlacementChargeForCoopInv();
                    $this->creditUserCoopInvPlacementIncome($coop_db_id,$placement_income,$date,$time);
                }else{
                    //If user does not have up to 2000, create a new week for him and move the amount to this new week
                    $form_array = array(
                        'week' => $new_week_name,
                        'user_id' => $user_id,
                        'coop_db_id' => $coop_db_id,
                        'total_earnings' => $total_week_earnings, 
                        'date_of_registration' => $date_of_registration,

                    );
                    $this->createNewCoopWeeklyEarningRecord($form_array);
                }
            }
        }else{
            $this->createFirstCoopWeeklyEarningRecordForUser($user_id,$coop_db_id,$date,$time);
        }
    }

    public function createNewCoopWeeklyEarningRecord($form_array){
        return DB::table('coop_weekly_earnings')->insert($form_array);
    }

    public function updateCoopWeeklyEarningTableById($form_array,$week_earning_id){
        DB::table('coop_weekly_earnings')->where('id',$week_earning_id)->update($form_array);
    }

    public function getUsersCoopInvWeeklyEarningsParamById($param,$users_current_week_id){
        $query = DB::table('coop_weekly_earnings')->where('id',$users_current_week_id)->get($param);
        if($query->count() == 1){
            return $query[0]->$param;
        }
    }

    public function getUsersCoopInvWeeklyEarningsParamByUserIdCoopIdWeekName($param,$user_id,$coop_db_id,$week_name){
        $query = DB::table('coop_weekly_earnings')->where('user_id',$user_id)->where('coop_db_id',$coop_db_id)->where('week', $week_name)->get($param);
        if($query->count() == 1){
            return $query[0]->$param;
        }
    }

    public function getUsersCurrentWeekCoopInvWeeklyEarnings($user_id,$coop_db_id){
        $week = "";
        $query = DB::table('coop_weekly_earnings')->where('user_id',$user_id)->where('coop_db_id',$coop_db_id)->orderBy('id','desc')->limit(1)->get('week');
        if($query->count() == 1){
            $week = $query[0]->week;
        }
        return $week;
    }

    public function dateDiffInDays($date1, $date2)
    {
        $date1_ts = strtotime($date1);
        $date2_ts = strtotime($date2);
        $diff = $date2_ts - $date1_ts;
        return round($diff / 86400);
    }

    public function getCoopUsersDateOfRegistrationWeeklyEarnings($user_id,$coop_db_id){
        $date_of_registration = "";
        $query = DB::table('coop_weekly_earnings')->where('user_id',$user_id)->where('coop_db_id',$coop_db_id)->orderBy('id', 'asc')->limit(1)->get('date_of_registration');
        if($query->count() == 1){
            $date_of_registration = $query[0]->date_of_registration;
        }
        return $date_of_registration;
    }

    public function createFirstCoopWeeklyEarningRecordForUser($user_id,$coop_db_id,$date,$time){
        //Check if user already has a record here
        
        if(!$this->checkIfUserAlreadyHasRecordInCoopWeeklyEarnings($user_id,$coop_db_id)){
            $form_array = array(
                'week' => 'week_1',
                'user_id' => $user_id,
                'coop_db_id' => $coop_db_id,
                'date_of_registration' => $date . ' ' . $time
            );
            return DB::table('coop_weekly_earnings')->insert($form_array);
        }
    }

    public function checkIfUserAlreadyHasRecordInCoopWeeklyEarnings($user_id,$coop_db_id){
        $query = DB::table('coop_weekly_earnings')->where('user_id', $user_id)->where('coop_db_id', $coop_db_id)->get();
        if($query->count() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function checkIfThisUsersUpteamHasPendingAmountAndCreditHim($user_id,$upteam_coop_db_id,$date,$time){
        $query = DB::table('coop_pending_upteam_payments')->where('coop_db_id',$upteam_coop_db_id)->where('total_amount','>',0)->where('users_num','>',0)->get();
        if($query->count() > 0){
            foreach($query as $row){
                $id = $row->id;
                $total_amount = $row->total_amount;
                $users_num = $row->users_num;

                $users_share = $total_amount / $users_num;
                $users_num_rem = $users_num - 1;
                $total_amount_rem = $total_amount - $users_share;

                DB::table('coop_earnings')->insert(array('user_id' => $user_id,'charge_type' => 3,'amount' => $users_share,'date' => $date,'time' => $time));

                $coop_db_upteam_support = $this->getUserParamById("coop_db_upteam_support",$user_id);
                $new_coop_db_upteam_support = $coop_db_upteam_support + $users_share;
                $form_array = array(
                    'coop_db_upteam_support' => $new_coop_db_upteam_support
                );

                $this->updateUserTable($form_array,$user_id);
                $coop_db_id = $this->getUserParamById("coop_db_id",$user_id);
                $this->creditUsersWeeklyEarnings($user_id,$coop_db_id,$users_share,$date,$time);
                if($users_num_rem > 0){
                    $form_array = array(
                        'total_amount' => $total_amount_rem,
                        'users_num' => $users_num_rem
                    );
                    DB::table('coop_pending_upteam_payments')->where('id',$id)->update($form_array);
                    
                }else{
                    DB::table('coop_pending_upteam_payments')->where('id',$id)->delete();
                }
            }
        }
    }

    public function getUsersUpTeamCopInv($user_id){
        $ret_arr = array();
        $coop_db_id = $this->getUsersCoopDbIdByUserId($user_id);
        for($i = $coop_db_id - 1; $i >= $coop_db_id - 5; $i--){
            if($this->checkIfCoopDbIdIsValid($i)){
                $ret_arr[] = $i;
            }else{
                break;
            }
        }
        return $ret_arr;
    }

    public function checkIfTheresAnyPendingUpteamSupportAndCredit($user_id,$date,$time){
        $coop_db_id = $this->getUsersCoopDbIdByUserId($user_id);
        $upteam_arr = $this->getUsersUpTeamCopInv($user_id);
        if(count($upteam_arr) > 0){
            for($i = 0; $i < count($upteam_arr); $i++){
                $upteam_coop_db_id = $upteam_arr[$i];
                $this->checkIfThisUsersUpteamHasPendingAmountAndCreditHim($user_id,$upteam_coop_db_id,$date,$time);
            }
        }
    }

    public function getUsersCoopDbIdByUserId($user_id){
        $query = DB::table('coop_db')->where('user_id',$user_id)->get("id");
        if($query->count() == 1){
            return $query[0]->id;
        }
    }

    public function getUsersDownTeamCopInv($sponsor_user_id){
        $ret_arr = array();
        $sponsors_coop_db_id = $this->getUsersCoopDbIdByUserId($sponsor_user_id);
        for($i = $sponsors_coop_db_id + 1; $i <= $sponsors_coop_db_id + 5; $i++){
            if($this->checkIfCoopDbIdIsValid($i)){
                $ret_arr[] = $i;
            }else{
                break;
            }
        }
        return $ret_arr;
    }

    public function getUsersFirstCoopDbId($user_id){
        $query = DB::table('coop_db')->where("user_id",$user_id)->orderBy("id","ASC")->limit(1)->get();
        if($query->count() == 1){
            return $query[0]->id;
        }
    }

    public function performCoopInvRegistrationForUsersWithoutPlacement($user_id,$sponsor_mlm_db_id,$date,$time){

        // $sponsors_user_id = $this->getUsersSponsorsUserId($user_id);
        $sponsors_user_id = $this->getCoopInvMlmDbParamById("user_id",$sponsor_mlm_db_id);
         
        if($this->registerUserInCoopInv3($user_id,$sponsors_user_id,$date,$time)){
            return true;
        }
            
            
    }

    public function getPositioningOfMlmUserDirectCoopInv($stage,$sponsor_id){
        
        $query = DB::table('coop_db')->where('stage', $stage)->where('under', $sponsor_id)->get();
        if($query->count() == 1){
            $query[0]->positioning;
        }else{
            return false;
        }
    }

    public function getPositioningOfImmediateChildOfThisUserCoopInv($parent_id){
        $query = DB::table('coop_db')->where('under',$parent_id)->get();
        if($query->count() == 1){
            return $query[0]->positioning;
        }
    }

    public function getNumberOfImmediateChildrenOfThisUserCoopInv($parent_id){
        
        $query = DB::table('coop_db')->where('under',$parent_id)->get();
        return $query->count();
    }

    public function checkIfThisUserHasHisNextLevelFullCoopInv($parent_id){
        
        $query = DB::table('coop_db')->where("under",$parent_id)->orderBy("id","ASC")->get("id");
        if($query->count() >= 2){
            return true;
        }else{
            return false;
        }
    }

    public function getIdsOfChildrenCoopInv($current_array){
        $ret_arr = array();
        if(is_array($current_array)){
            for($i = 0; $i < count($current_array); $i++){
                $id = $current_array[$i];
                
                $query = DB::table('coop_db')->where('under', $id)->get();
                if($query->count() > 0){
                    foreach($query as $row){
                        $id1 = $row->id;
                        $ret_arr[] = $id1;
                    }
                }
            }
        }
        return $ret_arr;
    }

    public function getChildrenIdsOfParentCoopInv($sponsors_first_mlm_db_id){
        $ret_arr = array();
        
        $query = DB::table('coop_db')->where("under",$sponsors_first_mlm_db_id)->orderBy("id","ASC")->get('id');
        if($query->count() > 0){
            foreach($query as $row){
                $id = $row->id;
                $ret_arr[] = $id;
            }
        }
        return $ret_arr;
    }

    public function fixUserInNextAvailableSpaceForCoopInvMlm($sponsors_user_id,$user_id,$date,$time){
        //If Type Is Next Available space
        
        
        //Get Sponsors First Mlm Db Id
        
        $sponsors_first_mlm_db_id = 1;
        
        // echo $sponsors_first_mlm_db_id . "<br>";
        //Get The Stage Here
        $sponsors_first_mlm_db_stage = 0;
        // echo $sponsors_first_mlm_db_stage;
        // echo $sponsors_first_mlm_db_stage;
        //Get The First Generation Under Sponsor Thats Empty
        
        $query = DB::table('coop_db')->where('under', $sponsors_first_mlm_db_id)->get();
        $number_under_him = $query->count();
        //If First Level Under Him Is Full
        if($number_under_him == 2){
            $i = 1;
            $current_array = array();
            while (true) {
                $i++;
                // echo $i;

                $previous_stage = $i - 1;
                $stage_to_check_for = $sponsors_first_mlm_db_stage + $previous_stage;
                $current_stage = $sponsors_first_mlm_db_stage + $i;


                
                if($i == 2){
                    $parents_ids = $this->getChildrenIdsOfParentCoopInv($sponsors_first_mlm_db_id);
                    // print_r($parents_ids);
                }else{
                    $parents_ids = $this->getIdsOfChildrenCoopInv($current_array);
                }
                // var_dump($parents_ids);
                if(is_array($parents_ids)){
                    $current_array = $parents_ids;
                    for($j = 0; $j < count($parents_ids); $j++){
                        $parent_id = $parents_ids[$j];
                        if(!$this->checkIfThisUserHasHisNextLevelFullCoopInv($parent_id)){
                            
                            $parents_children_num = $this->getNumberOfImmediateChildrenOfThisUserCoopInv($parent_id);
                            // echo $parents_children_num;
                            if($parents_children_num == 0){
                                $positioning = "left";
                            }else if($parents_children_num == 1){
                                $other_users_positioning = $this->getPositioningOfImmediateChildOfThisUserCoopInv($parent_id);
                                if($other_users_positioning == "right"){
                                    $positioning = "left";
                                }else{
                                    $positioning = "right";
                                }
                            }
                            $form_array = array(
                                'user_id' => $user_id,
                                'under' => $parent_id,
                                'stage' => $current_stage,
                                'positioning' => $positioning,
                                'date_created' => $date,
                                'time_created' => $time,
                                
                            );
                            
                            $mlm_db_id = DB::table('coop_db')->insertGetId($form_array);                           

                            $form_array = array(
                                'coop_db_id' => $mlm_db_id,
                                'coop_db_sponsor_id' => $sponsors_user_id
                            );

                            $this->updateUserTable($form_array,$user_id);
                            
                            //Create first record in coop_weekly_earnings for user
                            $this->createFirstCoopWeeklyEarningRecordForUser($user_id,$mlm_db_id,$date,$time);

                            $placement_income = $this->getPlacementChargeForCoopInv();
                            $this->checkIfTheresAnyPendingUpteamSupportAndCredit($user_id,$date,$time);
            
                            $this->creditUserCoopInvPlacementIncome($mlm_db_id,$placement_income,$date,$time);

                            return true;
                            
                            break 2;
                        }
                    }
                }
                
            }
        }else{ //If Not
            $new_stage = $sponsors_first_mlm_db_stage + 1;
            if($number_under_him == 1){
                $other_users_positioning = $this->getPositioningOfMlmUserDirectCoopInv($new_stage,$sponsors_first_mlm_db_id);
                if($other_users_positioning == "right"){
                    $positioning = "left";
                }else{
                    $positioning = "right";
                }
            }else{
                $positioning = "left";
            }
            
            $form_array = array(
                'user_id' => $user_id,
                
                'under' => $sponsors_first_mlm_db_id,
                'stage' => $new_stage,
                'positioning' => $positioning,
                'date_created' => $date,
                'time_created' => $time,
                
            );
        
            $mlm_db_id = DB::table('coop_db')->insertGetId($form_array);
            $form_array = array(
                'coop_db_id' => $mlm_db_id,
                'coop_db_sponsor_id' => $sponsors_user_id
            );

            $this->updateUserTable($form_array,$user_id);

            //Create first record in coop_weekly_earnings for user
            $this->createFirstCoopWeeklyEarningRecordForUser($user_id,$mlm_db_id,$date,$time);
            
            $placement_income = $this->getPlacementChargeForCoopInv();
            $this->checkIfTheresAnyPendingUpteamSupportAndCredit($user_id,$date,$time);
            $this->creditUserCoopInvPlacementIncome($mlm_db_id,$placement_income,$date,$time);
            return true;
        }

        
    }

    public function getIdsToCreditCopInvPlacement($coop_id){
        $ret_arr = array();
        for($i = 1; $i <= 18; $i++){
            
            $query = DB::table('coop_db')->where('id', $coop_id)->get();
            if($query->count() == 1){
                foreach($query as $row){
                    $under = $row->under;
                    if(!is_null($under)){
                        $user_id = $this->getCoopInvMlmDbParamById("user_id",$under);
                        // $this->getIdsToCreditPlacement($under);
                        $coop_id = $under;
                        $ret_arr[] = array(
                            'coop_id' => $under,
                            'user_id' => $user_id
                        );
                        
                    }else{
                        $ret_arr[] =  array(
                            'coop_id' => 1,
                            'user_id' => $this->getAdminId()
                        );
                    }
                }
            }

        }
        return $ret_arr;
    }

    public function creditUserCoopInvPlacementIncome($coop_id,$placement_income,$date,$time){
        $date = date("j M Y");
        $time = date("h:i:sa");

        $creditors_user_id = $this->getCoopInvMlmDbParamById("user_id",$coop_id);
        $ids_to_credit = $this->getIdsToCreditCopInvPlacement($coop_id);
        $ids_to_credit_num = count($ids_to_credit);
        $main_placement_income = (50 / 100) * $placement_income;
        for($i = 0; $i < count($ids_to_credit); $i++){
            $user_id = $ids_to_credit[$i]['user_id'];
            $placements_mlm_db_id = $ids_to_credit[$i]['coop_id'];
            $placement_user_id = $user_id;

            //Check If This user is owing loan previously and credit
            

            if(DB::table('coop_earnings')->insert(array('user_id' => $user_id,'charge_type' => 2,'amount' => $main_placement_income,'date' => $date,'time' => $time))){


                $coop_db_placement_income = $this->getUserParamById("coop_db_placement_income",$user_id);
                $new_coop_db_placement_income = $coop_db_placement_income + $main_placement_income;
                $form_array = array(
                    'coop_db_placement_income' => $new_coop_db_placement_income
                );

                $this->creditUsersWeeklyEarnings($user_id,$placements_mlm_db_id,$main_placement_income,$date,$time);

                if($this->updateUserTable($form_array,$user_id)){
                    $form_array = array();


                    $downteam_arr = $this->getUsersDownTeamCopInv($placement_user_id);
                    $downteam_num = count($downteam_arr);
                    // echo json_encode($downteam_arr);
                    $total_amount_to_share = $placement_income - $main_placement_income;
                    $amount_per_user = $total_amount_to_share / 5;
                    $downteam_rem = 5 - $downteam_num;
                    $amount_rem = $amount_per_user * $downteam_rem;
                    if($downteam_num > 0){
                        for($j = 0; $j < count($downteam_arr); $j++){
                            $downteam_coop_db_id = $downteam_arr[$j];
                            $downteam_user_id = $this->getCoopInvMlmDbParamById("user_id",$downteam_coop_db_id);

                            DB::table('coop_earnings')->insert(array('user_id' => $downteam_user_id,'charge_type' => 3,'amount' => $amount_per_user,'date' => $date,'time' => $time));

                            $coop_db_upteam_support = $this->getUserParamById("coop_db_upteam_support",$downteam_user_id);
                            $new_coop_db_upteam_support = $coop_db_upteam_support + $amount_per_user;
                            $form_array = array(
                                'coop_db_upteam_support' => $new_coop_db_upteam_support
                            );

                            $this->updateUserTable($form_array,$downteam_user_id);
                            $this->creditUsersWeeklyEarnings($downteam_user_id,$downteam_coop_db_id,$amount_per_user,$date,$time);

                        }
                    }

                    if($downteam_rem > 0){
                        
                        DB::table('coop_pending_upteam_payments')->insert(array('coop_db_id' => $placements_mlm_db_id,'total_amount' => $amount_rem,'users_num' => $downteam_rem));
                        
                    }

                    if($i == ($ids_to_credit_num - 1)){
                        return true;
                    }


                }
            }
        }
        return true;
    }

    public function getPlacementChargeForCoopInv(){
        
        $query = DB::table('coop_charges')->where('id', 2)->get();
        if($query->count() == 1){
            return $query[0]->amount;
        }
    }

    public function getCoopInvMlmDbParamById($param,$mlm_db_id){
        
        $query = DB::table('coop_db')->where('id', $mlm_db_id)->select($param)->get();
        if($query->count() == 1){
            return $query[0]->$param;
        }
    }


    public function getUsersSponsorsUserId($user_id){
        $mlm_db_id = $this->getUsersFirstMlmDbId($user_id);
        $sponsors_mlm_db_id = $this->getMlmDbParamById("sponsor",$mlm_db_id);
        $sponsors_user_id = $this->getMlmDbParamById("user_id",$sponsors_mlm_db_id);
        return $sponsors_user_id;
    }

    public function checkIfThisCoopInvPlacementPositionIsAvailable($placement_id,$positioning){
        $query = DB::table('coop_db')->where('under', $placement_id)->where('positioning', $positioning)->get();
        if($query->count() == 1){
            return false;
        }else{
            return true;
        }
    }


    public function fixUserInPositionCoopInvMlm($sponsors_user_id,$placement_id,$positioning,$user_id,$date,$time){
        if($this->checkIfThisCoopInvPlacementPositionIsAvailable($placement_id,$positioning)){
            $placement_stage =  $this->getCoopInvMlmDbParamById("stage",$placement_id);
            $new_stage = $placement_stage + 1;
            $form_array = array(
                'user_id' => $user_id,
                'under' => $placement_id,
                'stage' => $new_stage,
                'positioning' => $positioning,
                'date_created' => $date,
                'time_created' => $time,
            );
            
            $coop_db_id = DB::table('coop_db')->insertGetId($form_array);

            $form_array = array(
                'coop_db_id' => $coop_db_id,
                'coop_db_sponsor_id' => $sponsors_user_id
            );

            $this->updateUserTable($form_array,$user_id);

            //Create first record in coop_weekly_earnings for user
            $this->createFirstCoopWeeklyEarningRecordForUser($user_id,$coop_db_id,$date,$time);
            
            
            $placement_income = $this->getPlacementChargeForCoopInv();
            $this->checkIfTheresAnyPendingUpteamSupportAndCredit($user_id,$date,$time);
            $this->creditUserCoopInvPlacementIncome($coop_db_id,$placement_income,$date,$time);
            return true;
            
        }else{
            if($this->fixUserInNextAvailableSpaceForCoopInvMlm($sponsors_user_id,$user_id,$date,$time)){
                return true;
            }
        }   
    }

    public function registerUserInCoopInv2($user_id,$sponsors_user_id,$placement_id,$positioning,$date,$time){
        if($this->fixUserInPositionCoopInvMlm($sponsors_user_id,$placement_id,$positioning,$user_id,$date,$time)){
            
            $sponsor_income = $this->getSponsorChargeForCoopInv();
            $this->creditUserSponsorIncomeCoopInv($user_id,$sponsors_user_id,$sponsor_income,$date,$time);
            // $this->checkIfTheresAnyPendingUpteamSupportAndCredit($user_id,$date,$time);
            return true;
                
            
        }
    }

    public function registerUserInCoopInv3($user_id,$sponsors_user_id,$date,$time){
        if($this->fixUserInNextAvailableSpaceForCoopInvMlm($sponsors_user_id,$user_id,$date,$time)){
            $sponsor_income = $this->getSponsorChargeForCoopInv();
            $this->creditUserSponsorIncomeCoopInv($user_id,$sponsors_user_id,$sponsor_income,$date,$time);
            // $this->checkIfTheresAnyPendingUpteamSupportAndCredit($user_id,$date,$time);
            return true;
        }
    }

    public function creditUserSponsorIncomeCoopInv($user_id,$sponsor_user_id,$sponsor_income,$date,$time){
        
        $date = date("j M Y");
        $time = date("h:i:sa");

        $main_sponsor_income = (50 / 100) * $sponsor_income;
        $sponsors_coop_db_id = $this->getUsersCoopDbIdByUserId($sponsor_user_id);

        if(DB::table('coop_earnings')->insert(array('user_id' => $sponsor_user_id,'charge_type' => 1,'amount' => $main_sponsor_income,'date' => $date,'time' => $time))){
            $coop_db_sponsor_income = $this->getUserParamById("coop_db_sponsor_income",$sponsor_user_id);
            $new_coop_db_sponsor_income = $coop_db_sponsor_income + $main_sponsor_income;
            $form_array = array(
                'coop_db_sponsor_income' => $new_coop_db_sponsor_income
            );

            if($this->updateUserTable($form_array,$sponsor_user_id)){
                $this->creditUsersWeeklyEarnings($sponsor_user_id,$sponsors_coop_db_id,$main_sponsor_income,$date,$time);

                $downteam_arr = $this->getUsersDownTeamCopInv($sponsor_user_id);
                $downteam_num = count($downteam_arr);
                $total_amount_to_share = $sponsor_income - $main_sponsor_income;
                $amount_per_user = $total_amount_to_share / 5;
                $downteam_rem = 5 - $downteam_num;
                $amount_rem = $amount_per_user * $downteam_rem;
                if($downteam_num > 0){
                    for($i = 0; $i < count($downteam_arr); $i++){
                        $downteam_coop_db_id = $downteam_arr[$i];
                        $downteam_user_id = $this->getCoopInvMlmDbParamById("user_id",$downteam_coop_db_id);

                        DB::table('coop_earnings')->insert(array('user_id' => $downteam_user_id,'charge_type' => 3,'amount' => $amount_per_user,'date' => $date,'time' => $time));

                        $coop_db_upteam_support = $this->getUserParamById("coop_db_upteam_support",$downteam_user_id);
                        $new_coop_db_upteam_support = $coop_db_upteam_support + $amount_per_user;
                        $form_array = array(
                            'coop_db_upteam_support' => $new_coop_db_upteam_support
                        );

                        $this->updateUserTable($form_array,$downteam_user_id);
                        $this->creditUsersWeeklyEarnings($downteam_user_id,$downteam_coop_db_id,$amount_per_user,$date,$time);

                    }
                }

                if($downteam_rem > 0){
                    
                    DB::table('coop_pending_upteam_payments')->insert(array('coop_db_id' => $sponsors_coop_db_id,'total_amount' => $amount_rem,'users_num' => $downteam_rem));
                    
                }

                return true;
                
            }
        }
    }

    public function getSponsorChargeForCoopInv(){
        
        $query = DB::table('coop_charges')->where('id', 1)->get();
        if($query->count() == 1){
            return $query[0]->amount;
        }
    }

    

    public function performCoopInvRegistrationForUsersWithPlacement($user_id,$sponsor_mlm_db_id,$placement_mlm_db_id,$placement_position,$date,$time){

        
        // $sponsors_user_id = $this->getUsersSponsorsUserId($user_id);
        $sponsors_user_id = $this->getCoopInvMlmDbParamById("user_id",$sponsor_mlm_db_id);
        
        
        if($placement_position == "left" || $placement_position == "right"){
            
                
            if($placement_position == "left"){
                $next_available_position = "right";
            }else{
                $next_available_position = "left";
            }

            

            if($this->checkIfThisCoopInvPlacementPositionIsAvailable($placement_mlm_db_id,$placement_position)){
                // echo "string";
            
                if($this->registerUserInCoopInv2($user_id,$sponsors_user_id,$placement_mlm_db_id,$placement_position,$date,$time)){
                    
                    return true;
                }
            }else if($this->checkIfThisCoopInvPlacementPositionIsAvailable($placement_mlm_db_id,$next_available_position)){
                if($this->registerUserInCoopInv2($user_id,$sponsors_user_id,$placement_mlm_db_id,$next_available_position,$date,$time)){
                    // echo "string";
                    return true;
                }
            }else{
                if($this->registerUserInCoopInv3($user_id,$sponsors_user_id,$date,$time)){
                    return true;
                }
            }
                
            
        }
            
    }

    public function checkIfCoopDbIdIsValid($coop_db_id){
        
        $query = DB::table('coop_db')->where('id', $coop_db_id)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function getAvailablePositionUnderCoopInvMlmDbId($coop_db_id){
        if($this->checkIfCoopDbIdIsValid($coop_db_id)){
            
            $query = DB::table('coop_db')->where('under', $coop_db_id)->get();

            if($query->count() == 0){
                return "both";
            }else if($query->count() == 1){
                $taken_id = $this->getChildrenOfParent($coop_db_id)[0]->id;
                $taken_position = $this->getMlmDbParamById("positioning",$taken_id);
                if($taken_position == "left"){
                    return "right";
                }else{
                    return "left";
                }
            }else{
                return false;
            }
        }   
    }

    public function checkIfCoopInvMlmDbIdHasNoAvailablePositionUnderHim($mlm_db_id){
        
        $query = DB::table('coop_db')->where('under', $mlm_db_id)->get();
        if($query->count() == 2){
            return true;
        }else{
            return false;
        }
    }

    public function checkIfThisCoopInvMlmDbIdMatchesWithUserId($user_id,$mlm_db_id){
        $query = DB::table('coop_db')->where('user_id',$user_id)->where('id',$mlm_db_id)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function getAllUsersCoopInvsMlmDbIds($user_id){
        $ret_arr = array();
        
        $query = DB::table('coop_db')->where("user_id",$user_id)->orderBy("id","ASC")->get("id");
        if($query->count() == 1){
            $i = 0;
            foreach($query as $row){
                $i++;
                $id = (Integer) $row->id;
                $ret_arr[] = (Object) [
                    'i' => $i,
                    'id' => $id,
                ]; 
            }
        }
        return $ret_arr;
    }

    public function checkIfUserIsRegisteredForCooperativeInvestment($user_id){

        if(is_null($user_id)){
            $user_id = $this->getUserIdWhenLoggedIn();
        }

        $query = DB::table('coop_db')->where('user_id',$user_id)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function addTransactionStatusPayscribeElectricity($form_array,$payscribe_data = false){  
        $date = date("j M Y");
        $time = date("h:i:sa");     
        
        if(DB::table('vtu_transactions')->insert($form_array)){
            $user_id = $form_array['user_id'];
            $amount = $form_array['amount'];
            $type = $form_array['type'];
            $sub_type = $form_array['sub_type'];
            if($type == "airtime" || $type == "data"){
                if($type == "data"){
                    $amount = $amount - 2;
                }
                $perc_amount = round((0.1 / 100) * $amount,2);
                $perc_amount_2 = round((0.08 / 100) * $amount,2);
                $purchaser_amount = round((2 / 100) * $amount,2);

            }else if($type == "electricity"){
                if($payscribe_data){
                    $perc_amount = round((0.1 / 100) * $amount,2);
                    $perc_amount_2 = round((0.08 / 100) * $amount,2);
                    $purchaser_amount = round((0.50 / 100) * $amount,2);
                }else{
                    $perc_amount = round((0.1 / 100) * $amount,2);
                    $perc_amount_2 = round((0.08 / 100) * $amount,2);
                    $purchaser_amount = round((0.105 / 100) * $amount,2);
                }
            }else if($type == "cable"){
                $perc_amount = round((0.1 / 100) * $amount,2);
                $perc_amount_2 = round((0.08 / 100) * $amount,2);
                $purchaser_amount = round((0.105 / 100) * $amount,2);
            }else if($type == "router"){
                $perc_amount = round((0.1 / 100) * $amount,2);
                $perc_amount_2 = round((0.08 / 100) * $amount,2);
                $purchaser_amount = round((1 / 100) * $amount,2);
            }
            $vtu_income_vat = $this->getVtuTradeVatCharge();
            $vtu_income_vat_val = round(($vtu_income_vat / 100) * $perc_amount,2);
            $vtu_income_vat_val_2 = round(($vtu_income_vat / 100) * $perc_amount_2,2);

            $real_vtu_income = round(($perc_amount - $vtu_income_vat_val),2);
            $real_vtu_income_2 = round(($perc_amount_2 - $vtu_income_vat_val_2),2);

            $charge_type = 14;

            $mlm_db_id = $this->getUsersFirstMlmDbId($user_id);
            $ids_to_credit = $this->getIdsToCreditVtu($mlm_db_id);


            

            if(DB::table('mlm_earnings')->insert(array('user_id' => $user_id,'mlm_db_id' => $mlm_db_id,'charge_type' => $charge_type,'amount' => $purchaser_amount,'vat' => 0,'date' => $date,'time' => $time))){

                $this->updateUsersBusinessIncomeForTheMonth($user_id,$real_vtu_income);

                $total_business_income = $this->getUserParamById("total_business_income",$user_id);
                $new_total_business_income = $total_business_income + $real_vtu_income;

                $form_array = array(
                    'total_business_income' => $new_total_business_income
                );
                
                $this->updateUserTable($form_array,$user_id);
                return true;
            }

            
        }
    }

    public function getAirtimeToWalletRecordsPagination($req,$length){
        
        

        $user_name = $req->query('user_name');
        $amount_requested = $req->query('amount_requested');
        $amount_credited = $req->query('amount_credited');
        $admin_amount = $req->query('admin_amount');
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('airtime_to_wallet_records')->where('id','!=', 0);
            
        if(!empty($user_name)){
            $query = $query->where('user_name', 'like', '%' . $user_name.'%');

        }

        if(!empty($amount_requested)){
            $query = $query->where('amount_requested', 'like', '%' . $amount_requested.'%');

        }

        if(!empty($amount_credited)){
            $query = $query->where('amount_credited', 'like', '%' . $amount_credited.'%');

        }

        if(!empty($admin_amount)){
            $query = $query->where('admin_amount', 'like', '%' . $admin_amount.'%');

        }


        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;

    }

    public function addAirtimeToWalletRecord($form_array){
        return DB::table('airtime_to_wallet_records')->insert($form_array);
    }

    public function getIdsToCreditSgps($mlm_db_id){
        $ret_arr = array();
        $ret_arr[] = array(
            'mlm_db_id' => $mlm_db_id,
            'user_id' => $this->getMlmDbParamById("user_id",$mlm_db_id)
        );
        for($i = 1; $i <= 15; $i++){
            
            $query = DB::table('mlm_db')->where('id', $mlm_db_id)->get('under');
            if($query->count() == 1){
                foreach($query as $row){
                    $under = $row->under;
                    if(!is_null($under)){
                        $user_id = $this->getMlmDbParamById("user_id",$under);
                        // $this->getIdsToCreditPlacement($under);
                        $mlm_db_id = $under;
                        $ret_arr[] = array(
                            'mlm_db_id' => $under,
                            'user_id' => $user_id
                        );
                        
                    }else{
                        $ret_arr[] =  array(
                            'mlm_db_id' => 1,
                            'user_id' => $this->getAdminId()
                        );
                    }
                }
                
            }

        }

        return $ret_arr;
    }

    public function creditUsersSgpsIncome($amount_to_share,$admin_amount,$user_id){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $charge_type = 15;

        DB::table('mlm_earnings')->insert(array('user_id' => 10,'mlm_db_id' => 1,'charge_type' => $charge_type,'amount' => $admin_amount,'vat' => 0,'date' => $date,'time' => $time));

        $mlm_db_id = $this->getUsersFirstMlmDbId($user_id);
        $ids_to_credit = $this->getIdsToCreditSgps($mlm_db_id);



        $ids_to_credit_num = count($ids_to_credit);
        for($i = 0; $i < count($ids_to_credit); $i++){
            $user_id = $ids_to_credit[$i]['user_id'];
            $placements_mlm_db_id = $ids_to_credit[$i]['mlm_db_id'];

            
            
            $amount_to_credit_placement = $amount_to_share;
            $real_amount_to_credit_placement = $amount_to_share;
            $vtu_vat_val = 0;
            

            

            if(DB::table('mlm_earnings')->insert(array('user_id' => $user_id,'mlm_db_id' => $placements_mlm_db_id,'charge_type' => $charge_type,'amount' => $amount_to_credit_placement,'vat' => $vtu_vat_val,'date' => $date,'time' => $time))){

                $total_vat = $this->getUserParamById("admin_vat_total",$this->getAdminId());
                $new_vat_total = $total_vat + $vtu_vat_val;
                $form_array = array(
                    'admin_vat_total' => $new_vat_total
                );



                if($this->updateUserTable($form_array,$this->getAdminId())){
                    

                    $form_array = array();

                    $this->updateUsersBusinessIncomeForTheMonth($user_id,$real_amount_to_credit_placement);
                    $total_business_income = $this->getUserParamById("total_business_income",$user_id);
                    $new_total_business_income = $total_business_income + $real_amount_to_credit_placement;

                    $form_array = array(
                        'total_business_income' => $new_total_business_income
                    );
                    

                    if($this->updateUserTable($form_array,$user_id)){

                    }

                    if($i == ($ids_to_credit_num - 1)){
                        return true;
                    }
                }
            }
        }
    }

    public function runMonthlyServiceChargeCheck(){
        $date = date("Y-m-d");
        $time = date("H:i:s"); 
        
        
        $query = DB::table('users')->where('id','!=',10)->get();
        if($query->count() > 0){
            foreach($query as $row){
                $user_id = $row->id;

                $this->runTheMainCheckAndDebitingMnthlyServiceCharge($user_id);
                
            }
        }
        
    }

    public function runTheMainCheckAndDebitingMnthlyServiceCharge($user_id){
        $last_sgps_debited_date = $this->getUserParamById("last_sgps_debited_date",$user_id);
        $last_sgps_checked_date = $this->getUserParamById("last_sgps_checked_date",$user_id);;
        $total_income = $this->getUserParamById("total_income",$user_id);
        $withdrawn = $this->getUserParamById("withdrawn",$user_id);
        $balance = $total_income - $withdrawn;

        $balance = $this->getTotalWithdrawableMlmIncome($user_id);

        $curr_date_time = date("Y-m-d H:i:s");
        
        $curr_date_time_strtotime  = strtotime($curr_date_time);
        $last_sgps_debited_date_strtotime = strtotime($last_sgps_debited_date);
        $last_sgps_checked_date_strtotime = strtotime($last_sgps_checked_date);

        $when_last_debited = $curr_date_time_strtotime - $last_sgps_debited_date_strtotime;

        //Check Difference in days from when last debited. If its within 30 days, don't bother checking. If its more than 30 days continue checking
        $when_last_debited_days = $when_last_debited / 86400;
        if($when_last_debited_days >= 30){

            //Check If User Has At Least 200 naira in his wallet. If he has debit him and share to upline. If not, leave him.
            $total_amount_owed = 6.67 * $when_last_debited_days;

            if($balance >= 6.67){
                if($balance < $total_amount_owed){
                    $total_amount_owed = $balance;
                }
                $total_days_worth_addition = round($total_amount_owed / 6.67);
                
                $new_last_sgps_debited_date = date('Y-m-d H:i:s', strtotime($last_sgps_debited_date. ' + '.$total_days_worth_addition.' days'));
                
                if($this->debitUserMonthlySubsription($user_id,$total_amount_owed)){
                    $form_array = array(
                        'last_sgps_debited_date' => $new_last_sgps_debited_date,
                    );
                    if($this->updateUserTable($form_array,$user_id)){
                        $amount_to_share = (5 / 100) * $total_amount_owed;
                        $admin_amount = (20 / 100) * $total_amount_owed;

                        $this->creditUsersSgpsIncome($amount_to_share,$admin_amount,$user_id);
                    }
                }
                
            }

        }
    }

    public function debitUserMonthlySubsription($user_id,$total_amount_owed){
        $monthly_subscription = $this->getUserParamById("monthly_subscription",$user_id);
        $total_monthly_subscription = $monthly_subscription + $total_amount_owed;
        $form_array = array(
            'monthly_subscription' => $total_monthly_subscription
        );
        if($this->updateUserTable($form_array,$user_id)){
            $form_array = array(
                'user_id' => $user_id,
                'amount' => $total_amount_owed
            );
            return DB::table('monthly_service_charge_history')->insert($form_array);
        }
    }

    public function updateFrontPageMessageTable($form_array){
        try{
            DB::table('front_page_message')->where('id',1)->update($form_array);
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function getFrontPageType(){
        $query = DB::table('front_page_message')->where('id', 1)->get("type");
        if($query->count() == 1){
            return $query[0]->type;
        }
    }

    public function getFrontPageText(){
        $query = DB::table('front_page_message')->where('id', 1)->get("text");
        if($query->count() == 1){
            return $query[0]->text;
        }
    }

    public function getFrontPageTitle(){
        $query = DB::table('front_page_message')->where('id', 1)->get("title");
        if($query->count() == 1){
            return $query[0]->title;
        }
    }

    public function checkIfEducationalVoucherIsAvailable($type){
        $ret = false;
        $url = "https://www.payscribe.ng/api/v1/epins";

        
        $use_post = true;
        $amount = 0;

        $response = $this->payscribeVtuCurl($url,$use_post,$post_data=[]);
        
        if($this->isJson($response)){
            $response = json_decode($response);
            // var_dump($response);
            if(is_object($response)){
                if($response->status == true){

                    $out = array_column($response->message->details[0]->collection, null, "name");

                    // echo json_encode($out['glo']);
                    if($type == "waec"){
                        
                        $available = $out['WAEC Result Checker']->available;
                    }else{
                        
                        $available = $out['NECO Result Checker']->available;
                    }
                    
                    if($available < 1){
                        $ret = false;
                    }else{
                        $ret = true;
                    }
                }
            }
        }

        return $ret;
    }


    public function changeDateTimeOnTables(){

        // $query = DB::table('account_credit_history')->select("date","time","id")->where('id','>=', 1)->where('id','<=', 5)->get();
        // if($query->count() > 0){
        //     foreach($query as $row){
        //         $id = $row->id;
        //         $date = $row->date;
        //         $time = $row->time;

        //         $originalDate = $date . " " . $time;
        //         $newDate = date("Y-m-d H:i:s", strtotime($originalDate));
                
        //         echo $newDate . "<br>";

        //         try{
        //             $query = DB::table('account_credit_history')->where('id', $id)->update(array('date_time' => $newDate));
        //         }catch(Exception $e){
        //             // return false;
        //         }
        //     }
        // }
        
    }

    public function updateNotif($form_array,$id){
        try{
            DB::table('notif')->where('id', $id)->update($form_array);
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function markNotifAsRead($notif_id,$user_id){
        try{
            DB::table('notif')->where('id', $notif_id)->where('receiver', $user_id)->update(array('received' => 1));
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function getNotificationsDetails($notif_id){
        $query = DB::table('notif')->where('id', $notif_id)->get();
        if($query->count() == 1){
            return $query;
        }else{
            return false;
        }
    }

    public function checkIfNotifIsForThisUser($notif_id,$user_id){
        $query = DB::table('notif')->where('id', $notif_id)->where('receiver', $user_id)->get("id");
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function getAllNotificationsForUser($user_id){
        
        
        $query = DB::table('notif')->where('receiver',$user_id);

        $query = $query->orderBy("id","DESC")
                    ->paginate(10)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 
            
    }

    public function getUserIdBySlug($user_slug){
        if($this->checkIfSlugIsValid($user_slug)){
            $query = DB::table('users')->where('slug', $user_slug)->select("id")->get();
            if($query->count() == 1){
                return $query[0]->id;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function checkIfSlugIsValid($user_slug){
        $query = DB::table('users')->where('slug', $user_slug)->select("slug")->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function getMlmDownlinePaginationByOffset($user_id,$req,$length){
        
        $user_name = $req->query('user_name');
        $level = $req->query('level');
        $package = $req->query('package');
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');

        // $query_str = "SELECT * FROM `loan_advances` WHERE `user_id` = ".$user_id . " AND service_charge != 0 ";


        $query = DB::table('mlm_db')->where('id','!=', 1);
        


        if(!empty($user_name)){
            if($this->checkIfUserNameExists($user_name)){
                $user_id = $this->getUserIdByName($user_name);
                $query = $query->where('user_id', $user_id);
            }
        }

       

        if(!empty($level)){
            if($level >= 1){
                $query = $query->where('stage', $level);
            }
        }

        
        if(!empty($package)){
            
            if($package == "basic" || $package == "business"){
                if($package == "basic"){
                    $package = 1;
                }else{
                    $package = 2;
                }
                $query = $query->where('package', $package);
                
            }
        }

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date_created', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("stage","ASC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 
            

    }

    public function getNumberOfLevelsInMlmSystem(){
            
        // $this->db->select("stage");
        // $this->db->from("mlm_db");
        // $this->db->order_by("stage","DESC");
        // $this->db->limit(1);

        // $query = $this->db->get();

        $query = DB::table('mlm_db')->select("stage")->orderBy("stage","DESC")->limit(1)->get();

        return $query[0]->stage + 1;
    }

    public function getTotalNumberOfDownlineInMlmSystem(){
        // $query = $this->db->get("mlm_db");
        $query = DB::table('mlm_db')->select("id")->get();
        return $query->count() - 1;
    }

    public function checkIfUserIsAdmin(){
        $user_id = $this->getUserIdWhenLoggedIn();
        // $query = $this->db->get_where('users',array('id' => $user_id,'is_admin' => 1));

        $query = DB::table('users')->where('id', $user_id)->where('is_admin',1)->select("id")->get();
        if($query->count() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function printSponsorTree($level=0, $parentID=null,$stage,$return_str = "",$j = 0)
        {
        $j++;
        // echo $j;
        // echo $level;

        // echo '<ul>';
        // echo '<li>';
        // echo '<span class="tf-nc">'.$parentID.'</span>';
        // Create the query
        $num_1 = false;
        $query_str = "SELECT * FROM mlm_db WHERE ";
        if($parentID == null) {
            $query_str .= "sponsor IS NULL";
        }
        else {
            $query_str .= "`sponsor`=" . intval($parentID);
        }

        $query_str .= " ORDER BY positioning ASC";
        // Execute the query and go through the results.
        
        $query = DB::select($query_str);
        if(count($query) > 0)
        {
            if(count($query) == 1){
                $num_1 = true;
            }
            
            $return_str .= '<ul>';
            foreach($query as $row)
            {
                
                
                // Print the current ID;
                $currentID = $row->id;
                $positioning = $row->positioning;
                $user_id = $row->user_id;
                $logo = $this->getUserParamById("logo",$user_id);
                $user_name = $this->getUserParamById("user_name",$user_id);
                $full_name = $this->getUserParamById("full_name",$user_id);
                $date_created = $row->date_created;
                $full_phone_number = $this->getFullMobileNoByUserName($user_name);
                $index = $this->getMlmIdsIndexNumber($currentID);
                $package = $row->package;
                if($package == 1){
                    $package = "basic";
                }else{
                    $package = "business";
                }
                if(is_null($logo)){
                    $logo = '/images/nophoto.jpg';
                }else{
                    $logo = '/storage/images/'. $logo;
                }
                if($num_1){
                    if($positioning == "left"){
                        $return_str .= '<li>';
                        $return_str .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';

                        $return_str .= '<img class="tree_icon" src="'.$logo.'">';
                        $return_str .= '<p class="demo_name_style">';
                        $return_str .= $user_name ;

                        $return_str .= '</p>';

                        $return_str .= '</div>';

                        
                        // $return_str .= '</li>';
                        
                       
                       
                    }else{
                        

                        $return_str .= '<li>';
                        $return_str .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';


                        $return_str .= '<img class="tree_icon" src="'.$logo.'">';
                        $return_str .= '<p class="demo_name_style">';
                        $return_str .= $user_name ;

                        $return_str .= '</p>';

                        $return_str .= '</div>';

                        
                        
                    }
                }else{
                    $return_str .= '<li>';
                    $return_str .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';

                    
                    $return_str .= '<img class="tree_icon" src="'.$logo.'">';
                    $return_str .= '<p class="demo_name_style">';
                    $return_str .= $user_name ;

                    $return_str .= '</p>';

                    $return_str .= '</div>';
                        
                }
                
                for($i = 0; $i < $level; $i++) {
                    $return_str .= " ";
                }

                // echo $currentID . PHP_EOL;
                // Print all children of the current ID
                if($j < $stage){
                    // echo $j;
                    $return_str = $this->printSponsorTree($level+1, $currentID,$stage,$return_str,$j);
                }
               
                $return_str .= '</li>';
            }
            $return_str .= '</ul>';
            
        }
        else {
            
        }
        // echo '</li>';
        // echo '</ul>';
        return $return_str;
    }

    public function printTree($package1,$your_mlm_db_id,$level=0, $parentID=null,$stage,$return_str = "",$j = 0)
        {
            $j++;
            // echo $j;
            // echo $level;

            // echo '<ul>';
            // echo '<li>';
            // echo '<span class="tf-nc">'.$parentID.'</span>';
            // Create the query
            $num_1 = false;
            $query_str = "SELECT * FROM mlm_db WHERE ";
            if($parentID == null) {
                $query_str .= "under IS NULL";
            }
            else {
                $query_str .= "`under`=" . intval($parentID);
            }

            $query_str .= " ORDER BY positioning ASC";
            // Execute the query and go through the results.
            
            $query = DB::select($query_str);
            if(count($query) > 0){
                if(count($query) == 1){
                    $num_1 = true;
                }
                
                $return_str .= '<ul>';
                foreach($query as $row)
                {
                    
                    
                    // Print the current ID;
                    $currentID = $row->id;
                    $positioning = $row->positioning;
                    $user_id = $row->user_id;
                    $date_created = $row->date_created;
                    $logo = $this->getUserParamById("logo",$user_id);
                    $user_name = $this->getUserParamById("user_name",$user_id);
                    $full_name = $this->getUserParamById("full_name",$user_id);
                    $package = $row->package;
                    $index = $this->getMlmIdsIndexNumber($currentID);
                    $full_phone_number = $this->getFullMobileNoByUserName($user_name);

                    if($package == 1){
                        $package = "basic";
                    }else{
                        $package = "business";
                    }
                    if(is_null($logo)){
                        $logo = '/images/nophoto.jpg';
                    }else{
                        $logo = '/storage/images/'. $logo;
                    }
                    if($num_1){
                        if($positioning == "left"){
                            $return_str .= '<li>';
                            $return_str .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';

                            $return_str .= '<img class="tree_icon" src="'.$logo.'"">';
                            $return_str .= '<p class="demo_name_style">';

                            
                            $return_str.= '<i onclick="goUpMlm(this,event,'.$currentID.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-up" style="cursor:pointer;"></i>';
                            

                            $return_str .= " " . $user_name . "  ";

                            
                              
                            $return_str.= '<i onclick="goDownMlm(this,event,'.$currentID.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-down" style="cursor:pointer;"></i>';
                            

                            $return_str .= '</p>';
                            $return_str .= '</div>';
                            // $return_str .= '</div>';
                            // $return_str .= '</li>';

                            // $return_str .= '<li>';
                            // $return_str .= '<div style="cursor:pointer;" class="tf-nc register" data-under="'.$parentID .'">';
                            
                            // $return_str .= '<p class="register-text">Register</p>';
                            // // echo '<span class="tf-nc">'.$currentID.'</span>';
                            // $return_str .= '</div>';
                            // $return_str .= '</li>';
                           
                        }else{
                            
                            $return_str .= '<li>';
                            $return_str .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';

                            $return_str .= '<img class="tree_icon" src="'.$logo.'">';
                            $return_str .= '<p class="demo_name_style">';


                            $return_str.= '<i onclick="goUpMlm(this,event,'.$currentID.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-up" style="cursor:pointer;"></i>';
                            

                            $return_str .= " " . $user_name . "  ";

                            
                              
                            $return_str.= '<i onclick="goDownMlm(this,event,'.$currentID.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-down" style="cursor:pointer;"></i>';

                            $return_str .= '</p>';
                            $return_str .= '</div>';
                        }
                    }else{
                        $return_str .= '<li>';
                        $return_str .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';


                        $return_str .= '<img class="tree_icon" src="'.$logo.'">';
                        $return_str .= '<p class="demo_name_style">';

                        $return_str.= '<i onclick="goUpMlm(this,event,'.$currentID.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-up" style="cursor:pointer;"></i>';
                            

                        $return_str .= " " . $user_name . "  ";

                        
                          
                        $return_str.= '<i onclick="goDownMlm(this,event,'.$currentID.','.$your_mlm_db_id.')" data-package="'.$package1.'" class="fas fa-arrow-down" style="cursor:pointer;"></i>';

                        $return_str .= '</p>';
                        $return_str .= '</div>';
                        // $return_str .= '</div>';
                            
                    }
                    
                    for($i = 0; $i < $level; $i++) {
                        $return_str .= " ";
                    }

                    // echo $currentID . PHP_EOL;
                    // Print all children of the current ID
                    if($j < $stage){
                        // echo $j;
                        $return_str = $this->printTree($package1,$your_mlm_db_id,$level+1, $currentID,$stage,$return_str,$j);
                    }
                   
                    $return_str .= '</li>';
                }
                $return_str .= '</ul>';
                
            }
            else {
          
            }
            
            return $return_str;
        }


    // public function printTree($package1,$your_mlm_db_id,$level=0, $parentID=null,$stage,$return_arr = array(),$j = 0,$ind = 0)
    //     {
    //     $j++;
        
    //     // Create the query
    //     $num_1 = false;
    //     $query_str = "SELECT * FROM mlm_db WHERE ";
    //     if($parentID == null) {
    //         $query_str .= "under IS NULL";
    //     }
    //     else {
    //         $query_str .= "`under`=" . intval($parentID);
    //     }

    //     $query_str .= " ORDER BY positioning ASC";
    //     // Execute the query and go through the results.
        
    //     $query = DB::select($query_str);
    //     if(count($query) > 0){
    //         if(count($query) == 1){
    //             $num_1 = true;
    //         }
            
            
    //         foreach($query as $row)
    //         {
                
                
    //             // Print the current ID;
    //             $currentID = $row->id;
    //             $positioning = $row->positioning;
    //             $user_id = $row->user_id;
    //             $date_created = $row->date_created;
    //             $logo = $this->getUserParamById("logo",$user_id);
    //             $user_name = $this->getUserParamById("user_name",$user_id);
    //             $full_name = $this->getUserParamById("full_name",$user_id);
    //             $package = $row->package;
    //             $index = $this->getMlmIdsIndexNumber($currentID);
    //             $full_phone_number = $this->getFullMobileNoByUserName($user_name);

    //             if($package == 1){
    //                 $package = "basic";
    //             }else{
    //                 $package = "business";
    //             }
                

    //             if(is_null($logo)){
    //                 $logo = '/images/nophoto.jpg';
    //             }else{
    //                 $logo = '/storage/images/'. $logo;
    //             }

    //             $ind++;

    //             $return_arr[] = array(
    //                 'ind' => $ind,
    //                 'currentID' => $currentID,
    //                 'positioning' => $positioning,
    //                 'user_id' => $user_id,
    //                 'date_created' => $date_created,
    //                 'logo' => $logo,
    //                 'user_name' => $user_name,
    //                 'full_name' => $full_name,
    //                 'package' => $package,
    //                 'index' => $index,
    //                 'full_phone_number' => $full_phone_number,
    //             );
                
                
    //             for($i = 0; $i < $level; $i++) {
    //                 // $return_str .= " ";
    //             }

    //             // echo $currentID . PHP_EOL;
    //             // Print all children of the current ID
    //             if($j < $stage){
    //                 // echo $j;
    //                 $return_arr = $this->printTree($package1,$your_mlm_db_id,$level+1, $currentID,$stage,$return_arr,$j,$ind);
    //             }
                
    //         }
           
    //     }
    //     else {
      
    //     }
        
    //     return $return_arr;
    // }

    public function getMlmHistoryTradeDeliveryIncomeByPagination($user_id,$req,$length){
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('mlm_earnings')->where('user_id',$user_id)->where('charge_type',13);
        
        

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 



    }

    public function getMlmHistoryVendorPlacementByPagination($user_id,$req,$length){
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('mlm_earnings')->where('user_id',$user_id)->where('charge_type',17);
        
        

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 



    }

    public function getMlmHistoryVendorSponsorByPagination($user_id,$req,$length){
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('mlm_earnings')->where('user_id',$user_id)->where('charge_type',16);
        
        

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 



    }

    public function getMlmHistoryCenterConnectorPlacementByPagination($user_id,$req,$length){
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('mlm_earnings')->where('user_id',$user_id)->where('charge_type',19);
        
        

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 



    }

    public function getMlmHistoryCenterConnectorSponsorByPagination($user_id,$req,$length){
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('mlm_earnings')->where('user_id',$user_id)->where('charge_type',18);
        
        

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 



    }

    public function getMlmHistoryBasicPlacementByPagination($user_id,$req,$length){
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('mlm_earnings')->where('user_id',$user_id)->where('charge_type',2);
        
        

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 



    }

    public function getMlmHistoryBasicSponsorByPagination($user_id,$req,$length){
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('mlm_earnings')->where('user_id',$user_id)->where('charge_type',1);
        
        

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 



    }


    public function getUsersSmartBusinessLoanHistoryByPagination($user_id,$req,$length){
        
        $status = $req->query('status');
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');

        // $query_str = "SELECT * FROM `loan_advances` WHERE `user_id` = ".$user_id . " AND service_charge != 0 ";


        $query = DB::table('loan_advances')->where('user_id', $user_id)->where('service_charge','!=', 0);

        
        if(!empty($status)){
            
            if($status == "pending"){
                
                $query = $query->whereRaw('amount != amount_paid');
            }else if($status == "cleared"){
                
                $query = $query->whereRaw('amount = amount_paid');
            }
        }

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date_time', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('datetime', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 

    }

    public function transferMoneyFromMlmAccountToMainAccount($user_id,$amount){
        $mlm_withdrawn = $this->getUserParamById("mlm_withdrawn",$user_id);
        $new_mlm_withdrawn = $mlm_withdrawn + $amount;
        
        
        try{
            DB::table('users')->where('id', $user_id)->update(array('mlm_withdrawn' => $new_mlm_withdrawn));
            $summary = "Earnings To Main Wallet Transfer";
            if($this->creditUser($user_id,$amount,$summary)){

                if($this->debitUser($user_id,5,"Wallet Transfer Charge")){
                    return true;
                }
            }
        }catch(Exception $e){
            return false;
        }
    }


    public function getTotalWithdrawableMlmIncome($user_id){
        $basic_sponsor_earnings = $this->getUsersMlmBasicSponsorEarnings($user_id);
        $business_sponsor_earnings = $this->getUsersMlmBusinessSponsorEarnings($user_id);
        $basic_placement_earnings = $this->getUsersMlmBasicPlacementEarnings($user_id);
        $business_placement_earnings = $this->getUsersMlmBusinessPlacementEarnings($user_id);
        $center_leader_sponsor_bonus = $this->getUsersCenterLeaderSponsorBonus($user_id);
        $center_leader_placement_bonus = $this->getUsersCenterLeaderPlacementBonus($user_id);
        $center_leader_selection_income = $this->getUserParamById("center_leader_selection_income",$user_id);
        $trade_delivery_income = $this->getUsersTradeDeliveryIncome($user_id);
        $vtu_trade_income = $this->getUsersVtuTradeIncome($user_id);
        $car_award_earnings = $this->getUsersCarAwardEarnings($user_id);

        // $total_basic_earnings = $basic_sponsor_earnings + $basic_placement_earnings;
        $total_basic_earnings = 0;
        $total_business_earnings = $business_sponsor_earnings + $business_placement_earnings + $center_leader_sponsor_bonus + $center_leader_placement_bonus + $center_leader_selection_income + $trade_delivery_income + $car_award_earnings + $vtu_trade_income;

        $total_withdrawable_basic_earnings = $total_basic_earnings;
        $total_withdrawable_business_earnings = $this->getTotalBusinessWithdrawableEarnings($user_id);

        $grand_total_withdrawable_earnings = $total_withdrawable_basic_earnings + $total_withdrawable_business_earnings;
        $total_mlm_withdrawn = $this->getUserParamById("mlm_withdrawn",$user_id);
        $monthly_subscription = $this->getUserParamById("monthly_subscription",$user_id);
        $grand_total_balance = $grand_total_withdrawable_earnings - $total_mlm_withdrawn - $monthly_subscription;

        return $grand_total_balance;
    }

    public function outputTwoColumns(){
        $query = DB::table('users')->where('id', 177)->select('full_name','user_name')->get();
        return $query;
    }

    public function getTotalBusinessWithdrawableEarnings($user_id){
        $total = 0;
        $users_mlm_db_id = $this->getUsersFirstMlmDbId($user_id);
        $users_package = $this->getMlmDbParamById("package",$users_mlm_db_id);
        // echo $users_mlm_db_id;

        
        $query_str = "SELECT date,amount FROM mlm_earnings WHERE user_id = ".$user_id." AND (charge_type = 1 OR charge_type = 2 OR charge_type = 3 OR charge_type = 4 OR charge_type = 7 OR charge_type = 12 OR charge_type = 13 OR charge_type = 14 OR charge_type = 15 OR charge_type = 16 OR charge_type = 17 OR charge_type = 18 OR charge_type = 19)";
        
        $query = DB::select($query_str);
        
        // dd($query_str);
        $num = 0;
        if(count($query) > 0){
            foreach($query as $row){
                
                // $mlm_user_id = $row->user_id;
                $date = $row->date;
                $amount = $row->amount;
                // $vat = $row->vat;
                $vat = 0;
                $vat_perc = $vat / 100;

                $sub_total = ($amount - ($amount * $vat_perc));

                $start_date = "18 Sep 2020";
                $start_date = date("j M Y",strtotime($start_date));
                $start_date = strtotime($start_date); 
                $end_date = strtotime($date); 
                  
                
                
                $date_diff = ($start_date - $end_date)/60/60/24; 
                // echo $date_diff . "<br>";

                if($users_package == 2){
                    // $num++;
                    $total += $sub_total;
                }else{

                    if($date_diff <= 0){

                        $total += $sub_total;
                    }
                }
                
            }
        }
        // dd($total);

        $total = $total + $this->getUserParamById("center_leader_selection_income",$user_id) + $this->getUserParamById("center_connector_selection_income",$user_id) + $this->getUserParamById("vendor_selection_income",$user_id) + $this->getUserParamById("sim_activation_incentive",$user_id);

        if($users_package == 2){
            $total = $total;
        }else{
            if($total >= 2000){
                $total = 2000;
            }
        }
        return $total;
    }

    

    public function getAdminAccountStatementForThisUserByPagination($user_id,$req,$length){
        
        $amount = $req->query('amount');
        $balance = $req->query('balance');
        $summary = $req->query('summary');
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');



        $query = DB::table('account_statement')->where('user_id', $user_id);

        
        if(!empty($amount)){
            
            $query = $query->where('amount', 'like', '%'. $amount.'%');
        }

        if(!empty($balance)){
            
            $query = $query->where('amount_after', 'like', '%'. $balance.'%');
        }

        if(!empty($summary)){
            
            $query = $query->where('summary', 'like', '%'. $summary.'%');
        }


        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 

    }

    public function getSmartBusinessLoanDebtForUser($user_id){
        $debt_amount = 0;
        // $this->db->select("*");
        // $this->db->from("loan_advances");
        // $this->db->where("user_id",$user_id);
        // $this->db->order_by("id","DESC");
        // $this->db->limit(1);

        $query = DB::table('loan_advances')->where("user_id",$user_id)->orderBy("id","DESC")->limit(1)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $amount = $row->amount;
                $amount_paid = $row->amount_paid;

                $debt_amount = $amount - $amount_paid;
            }
        }

        return $debt_amount;
    }

    public function checkIfUserIsEligibleForLoan($user_id){
        $count = 0;
        $wallet_balance = $this->getUsersWalletBalance($user_id);
        if($wallet_balance < 0){
            $count++;
        }else{
            // $query = $this->db->get_where('loan_advances',array('user_id' => $user_id));
            // $this->db->select("*");
            // $this->db->from("loan_advances");
            // $this->db->where("user_id",$user_id);
            // $this->db->order_by("id","DESC");

            $query = DB::table('loan_advances')->where("user_id",$user_id)->orderBy("id","DESC")->get();
            if($query->count() > 0){
                foreach($query as $row){
                    $amount = $row->amount;
                    $amount_paid = $row->amount_paid;

                    if($amount != $amount_paid){
                        $count++;
                        break;
                    }
                }
            }
        }

        if($count > 0){
            return false;
        }else{
            return true;
        }
    }

    public function getSmartBusinessLoanableAmountForUser($user_id){
        $loanable_amount = 0;

        // if($this->checkIfUserIsEligibleForLoan($user_id)){
        //     $loanable_amount = ($this->getUsersMlmBusinessSponsorEarnings($user_id) + $this->getUsersMlmBusinessPlacementEarnings($user_id) + $this->getUsersMlmBasicSponsorEarnings($user_id) + $this->getUsersMlmBasicPlacementEarnings($user_id)) / 2;
        //     // $loanable_amount = 50000;

        //     if($loanable_amount > 10000){
        //         $loanable_amount = 10000;
        //     }
        // }
        
        
        return $loanable_amount;
    }

    public function checkIfAnyUserIsOwingSmartBusinessLoanAndPayServiceCharge(){
        $query_str = "SELECT id,service_charge,amount_paid,last_service_charge_date,charged_num FROM loan_advances WHERE amount != amount_paid AND service_charge != 0 AND last_service_charge_date != '' AND last_service_charge_date != date_time";
        $query = DB::select($query_str);
        
        
        if(count($query) > 0){
            foreach($query as $row){
                $id = $row->id;
                
                $service_charge = $row->service_charge;
                $amount_paid = $row->amount_paid;
                $last_service_charge_date = $row->last_service_charge_date;
                $charged_num = $row->charged_num;

                $start_date = $last_service_charge_date;
                $start_date = date("j M Y",strtotime($start_date));
                $start_date = strtotime($start_date); 
                $end_date = strtotime(date("j M Y")); 
                  
                
                
                $date_diff = ($end_date - $start_date)/60/60/24; 

                if($date_diff > 0){

                    if($date_diff % 30 == 0){
                        $new_amount_paid = $amount_paid - $service_charge;
                        $charged_num++;
                        $date = date("j M Y");
                        $time = date("h:i:sa");
                        
                        try{
                            DB::table('loan_advances')->where('id', $id)->update(array('amount_paid' => $new_amount_paid,'last_service_charge_date' => $date . " " . $time,'charged_num' => $charged_num));
                            return true;
                        }catch(Exception $e){
                            return false;
                        }
                    }else{
                        // echo "string <br>";
                    }
                }
                


            }
        }
    }

    public function checkIfUserHasOverTheRequiredRegistrationAmountInHisTableAndDoTheNeedful($user_id){
        $registration_amt_paid = $this->getUserParamById("registration_amt_paid",$user_id);
        $created = $this->getUserParamById("created",$user_id);
        $rgwb_paid = $this->getUserParamById("rgwb_paid",$user_id);
        if($registration_amt_paid > 500){
            $amount = $registration_amt_paid - 500;
            $summary = "Account Credit Of " . $amount;
            $created_date = $this->getUserParamById("created_date",$user_id);

            $start_date = "01 Sep 2021";
            $start_date = date("j M Y",strtotime($start_date));
            $start_date = strtotime($start_date); 
            $end_date = strtotime($created_date); 
              
            
            
            $date_diff = ($start_date - $end_date)/60/60/24; 
            // echo $date_diff . "<br>";



            if($date_diff <= 0){

                $form_array = array(
                    'registration_amt_paid' => 500
                );
                if($this->updateUserTable($form_array,$user_id)){
                    if($this->creditUser($user_id,$amount,$summary)){
                        // return true;
                    }
                }
            }
        }

        // if($registration_amt_paid == 6500 && $created == 1 && $rgwb_paid == 0){
        //  $amount = 650;
        //  $summary = "Registration Welcome Bonus";
        //  $form_array = array(
        //      'rgwb_paid' => 1
        //  );
        //  if($this->updateUserTable($form_array,$user_id)){
        //      if($this->creditUser($user_id,$amount,$summary)){
        //          // return true;
        //      }
        //  }
        // }
    }

    public function resetMonthsBeforeUsersBusinessPage(){
        $date = new DateTime(); //Today
        $lastDay = $date->format("Y-m-t"); //Get last day
        $dateMinus12 = $date->modify("-10 months"); // Last day 12 months ago
        
        $eleven_months_ago = $dateMinus12->format("M");
        // return $this->db->update('users',array(strtolower($eleven_months_ago . "_earnings") => '0'));

        try{
            DB::table('users')->update(array(strtolower($eleven_months_ago . "_earnings") => '0'));
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function providusVtuCurl($url, $use_post, $post_data=[]){
        $auth_signature = hash("sha512","RDMtTSMjdF9HMTBCQGw=:88400CB30C3DB7F1F97ED3401D53682CA8EF680C678EE027B1B9C866FB994258");
        $client_id = "RDMtTSMjdF9HMTBCQGw=";
        $headers = [
            'X-Auth-Signature' => $auth_signature,
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache',
            'Client-Id' => $client_id
        ];
        if(!$use_post){
            $response = Http::withOptions([
                'http_errors' => false,
                'verify' => false,
            ])->withHeaders($headers)->get($url);
        }else{
            $response = Http::withOptions([
                'http_errors' => false,
                'verify' => false,
            ])->withHeaders($headers)->post($url, $post_data);
        }
        return $response;
    }

    public function checkIfThisUserHasProvidusAccountNumberIfNotGiveHim(){
        $user_id = $this->getUserIdWhenLoggedIn();
        $providus_account_number = $this->getUserParamById("providus_account_number",$user_id);
        // echo $providus_account_number;
        if(is_null($providus_account_number)){
            $full_name = $this->getUserParamById("full_name",$user_id);
            // $full_name = "Nwogodo Ikechukwudo Nnannaya Igwe Udo";
            // if(count($full_name) > 19){
            //  $full_name = substr('abcdef', 0, 19);
            // }
            $url = "https://vps.providusbank.com/vps/api/PiPCreateReservedAccountNumber";
            
            $post_data = [
                "account_name" => $full_name,
                "bvn" => ""
            ];

            $use_post = true;
            $response = $this->providusVtuCurl($url, $use_post, $post_data);
            // setcookie('test',$response,time() + 7200,'/');
            // echo $response;
            if($this->isJson($response)){
                $response = json_decode($response);
                if($response->responseCode == "00" && $response->requestSuccessful == true){

                    $account_number = $response->account_number;
                    $account_name = $response->account_name;

                    $form_array = array(
                        "providus_account_number" => $account_number,
                        "providus_account_name" => $account_name
                    );

                    $this->updateUserTable($form_array,$user_id);
                }
            }

        }else{
            // echo "string";
        }
    }

    public function updateProvidusTransactionBySettlementId($form_array,$settlementId){
        // return $this->db->update('providus_transactions',$form_array,array('settlementId' => $settlementId));
        try{
            DB::table('providus_transactions')->where('settlementId', $settlementId)->update($form_array);
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function addNewAccountCreditHistory($form_array){
        
        return DB::table('account_credit_history')->insert($form_array);
    }

    public function getUserInfoByUserProvidusAccountNumber($providus_account_number){
        // $query = $this->db->get_where('users',array('providus_account_number' => $providus_account_number));
        $query = DB::table('users')->where('providus_account_number', $providus_account_number)->get();
        if($query->count() == 1){
            return $query;
        }else{
            return false;
        }
    }

    public function addProvidusTransactionRecord($form_array){
        
        return DB::table('providus_transactions')->insert($form_array);
    }


    public function checkIfThisProvidusWebhookIsDuplicate($settlementId){
        
        $query = DB::table('providus_transactions')->where('settlementId', $settlementId)->get();
        if($query->count() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function checkIfThisProvidusAccountNumberIsValid($accountNumber){
        
        $query = DB::table('users')->where('providus_account_number', $accountNumber)->get('id');
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function updateVtuTable($form_array,$id){
        
        try{
            DB::table('vtu_transactions')->where('id', $id)->update($form_array);
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function checkIfTransactionIdIsValidPayscribeAirtimeToWallet($order_id){
        
        $query = DB::table('vtu_transactions')->where('order_id', $order_id)->where('type', 'airtime_to_wallet')->get();
        if($query->count() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function addMinifyAccountWebhookJsonData($json_post,$date,$time){
        return DB::table('test_table')->insert(array('test' => $json_post,'date' => $date,'time' => $time));
    }

    public function addTransactionStatusAirtimeToWallet($form_array){   
        $date = date("j M Y");
        $time = date("h:i:sa");     
        return DB::table('vtu_transactions')->insert($form_array);
    }

    public function addTransactionStatusOnly($form_array){  
        $date = date("j M Y");
        $time = date("h:i:sa");     
        return DB::table('vtu_transactions')->insert($form_array);
    }

    public function getVtuPlatformToUse($type,$network){
        if($type != "" ){
            $str = $network . '_' . $type;
        }else{
            $str = $network;
        }
        // echo $str;
        $query = DB::table('vtu_platform')->where('name', $str)->get('platform');
        if($query->count() == 1){
            return $query[0]->platform;
        }
    }

    public function getPackageAmountForSmileClub($club_type,$product_code){
        $amount = false;
        $url = "https://www.nellobytesystems.com/APISmilePackagesV2.asp";
        // echo $url;
        $use_post = true;

        $response = $this->vtu_curl($url,$use_post,$post_data=[]);

        if($this->isJson($response)){
            $response = json_decode($response);
            // var_dump($response);
            if(is_object($response)){
                if(isset($response->MOBILE_NETWORK->{'smile-direct'}[0]->PRODUCT)){
                    $j = 0;
                    
                    $rows = $response->MOBILE_NETWORK->{'smile-direct'}[0]->PRODUCT;
                    for($i = 0; $i < count($rows); $i++){
                        $j++;

                        $package_id = $rows[$i]->PACKAGE_ID;
                        $package_name = $rows[$i]->PACKAGE_NAME;
                        $package_amount = $rows[$i]->PACKAGE_AMOUNT;

                        if($package_id == $product_code){
                            $amount = $package_amount;
                        }
                    }
                }
            }
        }

        return $amount;

    }

    public function getPackageAmountForCableTvClub($club_type,$product_code){
        $amount = false;
        $url = "https://www.nellobytesystems.com/APICableTVPackagesV2.asp";
        // echo $url;
        $use_post = true;

        $response = $this->vtu_curl($url,$use_post,$post_data=[]);

        if($this->isJson($response)){
            $response = json_decode($response);
            // var_dump($response);
            if(is_object($response)){
                if(isset($response->TV_ID->$club_type)){
                    $j = 0;
                    
                    $rows = $response->TV_ID->$club_type[0]->PRODUCT;
                    for($i = 0; $i < count($rows); $i++){
                        $j++;

                        $package_id = $rows[$i]->PACKAGE_ID;
                        $package_name = $rows[$i]->PACKAGE_NAME;
                        $package_amount = $rows[$i]->PACKAGE_AMOUNT;

                        if($package_id == $product_code){
                            $amount = $package_amount;
                        }
                    }
                }
            }
        }

        return $amount;

    }

    public function generateDataPlansForNetworkClub($network){
        $data_plans = array();
        $url = "https://www.nellobytesystems.com/APIDatabundlePlansV1.asp";
        $use_post = true;

        $response = $this->vtu_curl($url,$use_post,$post_data=[]);


        if($this->isJson($response)){
            $response = json_decode($response);
            if(is_object($response)){
                
                if($network == "mtn"){
                    $network = "MTN";
                    $network_num = 01;
                    $perc_disc = 0.04;
                }elseif($network == "glo"){
                    $network = "Glo";
                    $network_num = 02;
                    $perc_disc = 0.04;
                }else if($network == "9mobile"){
                    $network = "9mobile";
                    $network_num = 03;
                    $perc_disc = 0.04;
                }else if($network == "airtel"){

                    $network = "Airtel";
                    $network_num = 04;
                    $perc_disc = 0.04;
                }

                
                
                $plans = $response->MOBILE_NETWORK->$network[0]->PRODUCT;
                // return json_encode($plans);
                // return $network;
                

                if(is_array($plans)){
                    $index = 0;
                    $real_plans_arr = array();

                    for($i = 0; $i < count($plans); $i++){
                        
                        $product_code = $plans[$i]->PRODUCT_CODE;
                        $product_id = $plans[$i]->PRODUCT_ID;
                        $product_name = $plans[$i]->PRODUCT_NAME;
                        $product_amount = $plans[$i]->PRODUCT_AMOUNT;
                        // $product_amount = $product_amount + 20;
                        if($network == "MTN"){
                            if($product_code == "500"){
                                
                                $amt_to_add = 17.12;
                            }else if($product_code == "1000" || $product_code == "2000" || $product_code == "3000" || $product_code == "5000"){
                                
                                $amt_to_add = 7.92;
                            }else{
                                $amt_to_add = 4;
                            }
                        }else if($network == "Glo"){
                            $amt_to_add = 3;
                        }else if($network == "9mobile"){
                            $amt_to_add = 0;
                        }else if($network == "Airtel"){
                            $amt_to_add = 4;
                        }

                        $product_amount = round((($perc_disc * $product_amount) + $product_amount) + $amt_to_add,2);

                        if(($product_code != "3000" && $network == "MTN") || $network == "Glo" || $network == "9mobile" || $network == "Airtel") {
                        // if($network == "MTN" || $network == "Glo" || $network == "9mobile" || $network == "Airtel") {
                            $index++;
                            $real_plans_arr[$index - 1]['index'] = $index;
                            $real_plans_arr[$index - 1]['amount'] = number_format($product_amount,2);
                            $real_plans_arr[$index - 1]['product_code'] = $product_code;
                            $real_plans_arr[$index - 1]['product_id'] = $product_id;
                            $real_plans_arr[$index - 1]['product_name'] = $product_name;
                            $real_plans_arr[$index - 1]['sub_type'] = 'clubkonnect';
                            $real_plans_arr[$index - 1]['network'] = $network;
                            $real_plans_arr[$index - 1]['combo'] = false;
                        }
                    }

                    $data_plans = $real_plans_arr;
                }
                

            }
        }
        return $data_plans;
    }

    public function generateDataPlansForNetworkPayscribe($network){
        $data_plans = array();
        $url = "https://www.payscribe.ng/api/v1/data/lookup";
        $use_post = true;
        
        
        if($network == "glo"){
            $network = "GLO";
            
            $network_2 = "Glo";
            $perc_disc = 0.04;
            $additional_charge = 12;
        }else if($network == "airtel"){
            $network = "AIRTEL";
            
            $network_2 = "Airtel";
            $perc_disc = 0.04;
            $additional_charge = 4;
        }else if($network == "9mobile"){
            $network = "9MOBILE";
            
            $network_2 = "9mobile";
            $perc_disc = 0.04;
            $additional_charge = 30;

        }
    

        $post_data = array(
            'network' => $network_2
        );

        
        $response = $this->payscribeVtuCurl($url,$use_post,$post_data);


        if($this->isJson($response)){
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
                                "network" => $network,
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
                        $data_plans = $all_plans_arr;

                    }
                }
            }
        }
        return $data_plans;
    }

    public function generateDataPlansForNetworkEminence($network){
        $data_plans = array();
        $url = "https://app.eminencesub.com/api/data";
        $use_post = false;
        
        
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
    
        
        $response = $this->eminenceVtuCurl($url,$use_post,$post_data = []);
        // return $response;

        if($this->isJson($response)){
            $response = json_decode($response);
            // return $response;
            
            if(is_object($response)){
                if($response->code == 200){
                
                    
                    $plans = $response->data;
                    

                    if(is_array($plans)){

                        $j = 0;
                        
                        $plan_new_arr = [];
                        

                        for($i = 0; $i < count($plans); $i++){
                            // $perc_disc = 0;
                            // $additional_charge = 0;
                            
                            $product_id = $plans[$i]->plan_id;
                            $network_id_cmp = $plans[$i]->network_id;
                            // $product_id = $plans[$i]->PRODUCT_ID;
                            $product_name = $plans[$i]->name;
                            $product_amount = $plans[$i]->price;
                            $validity = $plans[$i]->validity;

                            $product_name .= " (" . $validity . ")";

                            if(is_numeric($product_amount)){

                                // $product_amount = $product_amount + 20;
                                
                                

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

                                $product_amount = round(($perc_disc * $product_amount) + $product_amount,2);

                                $product_amount += $additional_charge;
                            
                                
                                if($network_id_cmp == $network_id){
                                    $plan_obj = [
                                        "network" => $network,
                                        "sub_type" => "eminence",
                                        "product_name" => $product_name,
                                        "amount" => $product_amount,
                                        "product_id" => $product_id,
                                        "product_code" => ""

                                    ];

                                    array_push($plan_new_arr,$plan_obj);
                                }
                            }

                        }

                        // return $plan_new_arr;

                        $all_plans_arr = $plan_new_arr;

            
                        $index = 0;
                        for($i = 0; $i < count($all_plans_arr); $i++){
                            $index++;
                            $all_plans_arr[$i]['index'] = $index;
                            $product_amount = $all_plans_arr[$i]['amount'];
                            $all_plans_arr[$i]['amount'] = number_format($product_amount,2);
                            $all_plans_arr[$i]['combo'] = false;
                        }

                        if($network == "mtn"){
                           $plan_new_2_arr = $this->generateDataPlansForNetworkClub("mtn");
                           // return $plan_new_2_arr;

                           $new_plan_new_2_arr = array();
                           for($i = 0; $i < count($plan_new_2_arr); $i++){
                                if($plan_new_2_arr[$i]['product_code'] != "500" && $plan_new_2_arr[$i]['product_code'] != "1000" && $plan_new_2_arr[$i]['product_code'] != "1500" && $plan_new_2_arr[$i]['product_code'] != "2000" && $plan_new_2_arr[$i]['product_code'] != "3000" && $plan_new_2_arr[$i]['product_code'] != "4500" && $plan_new_2_arr[$i]['product_code'] != "5000" && $plan_new_2_arr[$i]['product_code'] != "6000" && $plan_new_2_arr[$i]['product_code'] != "10000"){

                                    // $product_amount = $plan_new_2_arr[$i]['amount'] ;
                                    
                                    // $product_amount = floatval(preg_replace('/[^\d.]/', '', $product_amount));
                                    // // $product_amount = floatval(preg_replace("/[^0-9]/", "", $product_amount ));

                                    // $perc_disc = 0.04;
                                    // $additional_charge = 10;

                                    // // $perc_amount = ($perc_disc * $product_amount);
                                    // // $product_amount = $perc_amount;
                                    
                                    // $product_amount = round(($perc_disc * $product_amount) + $product_amount,2);

                                    // // $product_amount += $additional_charge;
                                    // $plan_new_2_arr[$i]['amount'] = $product_amount;
                                    $new_plan_new_2_arr[$i] = $plan_new_2_arr[$i];
                                }
                           }
                           // return $new_plan_new_2_arr;
                           $all_plans_arr = array_merge($all_plans_arr,$new_plan_new_2_arr);

                           $index = 0;
                            for($i = 0; $i < count($all_plans_arr); $i++){
                                $index++;
                                $all_plans_arr[$i]['index'] = $index;
                                $product_amount = $all_plans_arr[$i]['amount'];


                                
                                if(!strpos($product_amount, ',')){
                                    $all_plans_arr[$i]['amount'] = number_format($product_amount,2);
                                }else{
                                    $all_plans_arr[$i]['amount'] = $product_amount;
                                }
                                $all_plans_arr[$i]['combo'] = false;
                            }
                        }

                        $data_plans = $all_plans_arr;

                    }
                }
            }
        }
        return $data_plans;
    }

    public function generateMtnDataPlans(){
        $data_plans = array();
        $network = "mtn";
        $url_1 = "https://www.nellobytesystems.com/APIDatabundlePlansV1.asp";
        $url_2 = "https://www.payscribe.ng/api/v1/data/lookup";
        $use_post = true;

        $response = $this->vtu_curl($url_1,$use_post,$post_data=[]);


        if($this->isJson($response)){
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

                    // dd($post_data);
                    
                    $response = $this->payscribeVtuCurl($url_2,$use_post,$post_data);


                    if($this->isJson($response)){
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
                                        

                                       
                                        $product_amount += 10;
                                        
                                        // $product_amount += 2;

                                        if($product_id == "10000"){
                                            $product_amount = 2600;
                                        }
                                        // echo $product_id . "<br>";

                                        if($product_id != "500" && $product_id != "1000" && $product_id != "1500" && $product_id != "2000" && $product_id != "3000" && $product_id != "4500" && $product_id != "5000" && $product_id != "6000" && $product_id != "10000"){
                                        
                                            // echo $product_id . "<br>";
                                            $plan_1_new_arr[$i] = [
                                                "network" => $network,
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
                                            "network" => $network,
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
                                    $data_plans = $all_plans_arr;
                                }
                            }
                        }
                    }
                }
                

            }
        }
        return $data_plans;
    }

    public function getGsubzApiKey(){
        return "ap_5ee8c242779b0c0db54241ea15a68e98";
    }

    public function gSubzVtuCurl($url, $use_post, $post_data=[]){
        // $api = "ap_5ee8c242779b0c0db54241ea15a68e98";
        $api = $this->getGsubzApiKey();
        $headers = [
            'Authorization' => 'Bearer '.$api,
            
            
        ];
        if(!$use_post){
            $response = Http::withOptions([
                'http_errors' => false,
                'verify' => false,
            ])->withHeaders($headers)->get($url);
        }else{
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://gsubz.com/api/pay/',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_SSL_VERIFYHOST => 0,
              CURLOPT_SSL_VERIFYPEER => FALSE,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => $post_data,
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ap_5ee8c242779b0c0db54241ea15a68e98',
                'Cookie: PHPSESSID=6f4bddcae09aa26dace3c6bd726c23f5'
              ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            
        }
        return $response;
    }

    public function getEminenceVtuDataBundleCostByProductId($url,$type,$product_id){
        

        
        $use_post = false;
        $amount = 0;

        $response = $this->eminenceVtuCurl($url,$use_post,$post_data=[]);
        
        if($this->isJson($response)){
            $response = json_decode($response);
            if(is_object($response)){
                if(is_array($response->data)){
                    $plans = $response->data;


                    for($i = 0; $i < count($plans); $i++){
                        $PRODUCT_ID = $plans[$i]->plan_id;
                        if($PRODUCT_ID == $product_id){
                            $amount = $plans[$i]->price;
                            break;
                        }
                    }
                }
            }
        }

        return $amount;
    }

    public function getGsubzVtuDataBundleCostByProductId($url,$type,$product_id){
        

        
        $use_post = false;
        $amount = 0;

        $response = $this->gSubzVtuCurl($url,$use_post,$post_data=[]);
        
        if($this->isJson($response)){
            $response = json_decode($response);
            if(is_object($response)){
                if(is_array($response->plans)){
                    $plans = $response->plans;


                    for($i = 0; $i < count($plans); $i++){
                        $PRODUCT_ID = $plans[$i]->value;
                        if($PRODUCT_ID == $product_id){
                            $amount = $plans[$i]->price;
                            break;
                        }
                    }
                }
            }
        }

        return $amount;
    }

    public function generateDataPlansForNetworkGsubz($network,$serviceID = NULL){
        $data_plans = array();
        if(!is_null($serviceID)){
            $url = "https://gsubz.com/api/plans/?service=".$serviceID;
            $use_post = false;
            
            
            if($network == "mtn"){
                $network = "MTN";
                // $url = "https://gsubz.com/api/plans/?service=mtn_sme";
                // $url_2 = "https://gsubz.com/api/plans/?service=mtncg";
                
                $network_2 = "Mtn";
                $perc_disc = 0.04;
                $additional_charge = 18;
            }else if($network == "glo"){
                $network = "GLO";
                // $url .= "glo_data";
                $network_2 = "Glo";
                $perc_disc = 0.04;
                $additional_charge = 15;
            }else if($network == "airtel"){
                $network = "AIRTEL";
                // $url .= "airtelcg";
                // $url .= "airtel_cg";
                $network_2 = "Airtel";
                $perc_disc = 0.04;
                $additional_charge = 25;
            }else if($network == "9mobile"){
                $network = "9MOBILE";
                // $url .= "etisalat_data";
                $network_2 = "9mobile";
                $perc_disc = 0.04;
                $additional_charge = 15;

            }
        

            // $post_data = array(
            //     'network' => $network_2
            // );



            
            $response = $this->gSubzVtuCurl($url,$use_post);


            if($this->isJson($response)){
                $response = json_decode($response);
                // var_dump($response);
                if(is_object($response)){
                    if(is_array($response->plans)){
                    
                       
                        $plans = $response->plans;
                        

                        if(is_array($plans)){

                            if($network == "MTN"){
                                for($i = 0; $i < count($plans); $i++){
                                    if(strpos($url, "sme")){
                                        $plans[$i]->gsubz_type = "sme";
                                    
                                    }else{
                                        $plans[$i]->gsubz_type = "regular";    
                                    }
                                }

                                // if(strpos($url, "sme")){
                                //     $plans = [];
                                // }

                                // $response = $this->gSubzVtuCurl($url_2,$use_post);


                                // if($this->isJson($response)){
                                //     $response = json_decode($response);
                                    
                                //     if(is_object($response)){
                                //         if(is_array($response->plans)){
                                //             $plans_2 = $response->plans;
                                            

                                //             if(is_array($plans_2)){
                                //                 for($i = 0; $i < count($plans_2); $i++){
                                
                                //                     $plans_2[$i]->gsubz_type = "regular";
                                //                 }
                                //                 if($mtn_type != "cg"){
                                //                     $plans_2 = [];
                                //                 }
                                //                 $plans = array_merge($plans,$plans_2);
                                //             }
                                //         }
                                //     }
                                // }
                            }

                            // return $plans;
                            
                            $j = 0;

                            
                            $plan_new_arr = array();
                            

                            for($i = 0; $i < count($plans); $i++){
                                
                                $product_id = $plans[$i]->value;
                                // $product_id = $plans[$i]->PRODUCT_ID;
                                $product_name = $plans[$i]->displayName;
                                $product_amount = $plans[$i]->price;
                                // $product_amount = $product_amount + 20;
                                
                                $product_amount = round((0.04 * $product_amount) + $product_amount,2);

                                $product_amount += $additional_charge;

                                if($network == "MTN"){
                                    $gsubz_type = $plans[$i]->gsubz_type;
                                }
                                
                            
                                

                                $plan_new_arr[$i] = [
                                    "network" => $network,
                                    "sub_type" => "gsubz",
                                    "product_name" => $product_name,
                                    "amount" => $product_amount,
                                    "product_id" => $product_id,
                                    "product_code" => $plans[$i]->price,
                                    

                                ];

                                if($network == "MTN"){
                                    $plan_new_arr[$i]['gsubz_type'] = $gsubz_type;
                                }

                                if($network == "MTN" && $product_id == "179"){
                                    $plan_new_arr[$i]['amount'] = $product_amount + 8;
                                }

                            }

                            

                            $price = array_column($plan_new_arr, 'amount');
                            array_multisort($price, SORT_ASC, $plan_new_arr);
                            $all_plans_arr = $plan_new_arr;
                        
                            $index = 0;
                            for($i = 0; $i < count($all_plans_arr); $i++){
                                $index++;
                                $all_plans_arr[$i]['index'] = $index;
                                $product_amount = $all_plans_arr[$i]['amount'];
                                $all_plans_arr[$i]['amount'] = number_format($product_amount,2);
                                $all_plans_arr[$i]['combo'] = false;
                            }

                            if($network == "MTN"){
                               $plan_new_2_arr = $this->generateDataPlansForNetworkClub("mtn");
                               $new_plan_new_2_arr = array();
                               for($i = 0; $i < count($plan_new_2_arr); $i++){
                                    if($plan_new_2_arr[$i]['product_code'] != "500" && $plan_new_2_arr[$i]['product_code'] != "1000" && $plan_new_2_arr[$i]['product_code'] != "2000" && $plan_new_2_arr[$i]['product_code'] != "3000" && $plan_new_2_arr[$i]['product_code'] != "10000"){
                                        $new_plan_new_2_arr[$i] = $plan_new_2_arr[$i];
                                    }
                               }
                               // return $plan_new_2_arr;
                               $all_plans_arr = array_merge($all_plans_arr,$new_plan_new_2_arr);

                               $index = 0;
                                for($i = 0; $i < count($all_plans_arr); $i++){
                                    $index++;
                                    $all_plans_arr[$i]['index'] = $index;
                                    $product_amount = $all_plans_arr[$i]['amount'];
                                    if(!strpos($product_amount, ',')){
                                        $all_plans_arr[$i]['amount'] = number_format($product_amount,2);
                                    }else{
                                        $all_plans_arr[$i]['amount'] = $product_amount;
                                    }
                                    $all_plans_arr[$i]['combo'] = false;
                                }
                            }

                            if($network == "AIRTEL"){
                               $plan_new_2_arr = $this->generateDataPlansForNetworkClub("airtel");
                               $new_plan_new_2_arr = array();
                               for($i = 0; $i < count($plan_new_2_arr); $i++){
                                    if($plan_new_2_arr[$i]['product_code'] > 5000){
                                        $new_plan_new_2_arr[$i] = $plan_new_2_arr[$i];
                                    }
                               }
                               // return $plan_new_arr;
                               $all_plans_arr = array_merge($all_plans_arr,$new_plan_new_2_arr);

                               $index = 0;
                                for($i = 0; $i < count($all_plans_arr); $i++){
                                    $index++;
                                    $all_plans_arr[$i]['index'] = $index;
                                    $product_amount = $all_plans_arr[$i]['amount'];
                                    if(!strpos($product_amount, ',')){
                                        $all_plans_arr[$i]['amount'] = number_format($product_amount,2);
                                    }else{
                                        $all_plans_arr[$i]['amount'] = $product_amount;
                                    }
                                    $all_plans_arr[$i]['combo'] = false;
                                }
                            }

                            $data_plans = $all_plans_arr;

                        }
                    }
                }
            }
        }
        return $data_plans;
    }
    

    public function loadDataPlansForNetwork($network,$combo = false){
        $data_plans = array();
        if(!$combo){
            $vtu_platform = $this->getVtuPlatformToUse('data',$network);
            $vtu_platform_shrt = substr($vtu_platform, 0, 5);
            if($network == "mtn"){
                
                if($vtu_platform == "payscribe"){
                    $data_plans = $this->generateMtnDataPlans();
                }else if($vtu_platform_shrt == "gsubz"){
                    $serviceID = substr($vtu_platform, 6);
                    $data_plans = $this->generateDataPlansForNetworkGsubz($network,$serviceID);
                }else if($vtu_platform_shrt == "gsubz_mtn_cg"){
                    $data_plans = $this->generateDataPlansForNetworkGsubz($network,"cg");
                }else if($vtu_platform == "eminence"){
                    $data_plans = $this->generateDataPlansForNetworkEminence($network);
                }else{
                    $data_plans = $this->generateDataPlansForNetworkClub($network);
                }
            }else if($network == "glo" || $network == "9mobile" || $network == "airtel" || $network == "mtn"){
                if($vtu_platform == "payscribe"){
                    $data_plans = $this->generateDataPlansForNetworkPayscribe($network);
                }else if($vtu_platform_shrt == "gsubz"){
                    $serviceID = substr($vtu_platform, 6);
                    $data_plans = $this->generateDataPlansForNetworkGsubz($network,$serviceID);
                }else if($vtu_platform == "eminence"){
                    
                    $data_plans = $this->generateDataPlansForNetworkEminence($network);
                }else{
                    $data_plans = $this->generateDataPlansForNetworkClub($network);
                }
            
            }
        }else if($combo && $network == "9mobile"){
            $bundles = $this->get9mobileComboDataBundles();
                                    

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
                    $new_arr[$j]['network'] = $network;
                    $new_arr[$j]['product_id'] = $product_id;
                    $new_arr[$j]['sub_type'] = 'combo';
                    $new_arr[$j]['combo'] = true;
                   
                }
                $data_plans = $new_arr; 
            }
        }
        return $data_plans;
    }

    public function generateRandomEpinData($quantity = 50){
        $permitted_chars = '0123456789AFtXZ';
        $details_arr = array();
        for($i = 1; $i <= $quantity; $i++){
            $random_number = $this->randomNumber(15);
            $details_arr[] = array(
                'pin' => $random_number,
                'code' => '*174*' . $random_number . '#'
            );
        }
        $array = array(
            'status' => true,
            'message' => array(
                'trans_id' => $this->generate_string($permitted_chars,6),
                'details' => $details_arr
            ),
            'status_code' => 200,
        );
        return json_encode($array);
    }

    public function generate_string($input, $strength = 16) {
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }
     
        return $random_string;
    }

    public function randomNumber($length) {
        $result = '';
        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }

    public function addVTUTransactionStatus1($form_array){  
        $date = date("j M Y");
        $time = date("h:i:sa");     
        return DB::table('vtu_transactions')->insert($form_array);
    }

    public function sendWithrawalRequest($form_array){
        
        return DB::table('withdrawal_request')->insert($form_array);
    }

    public function paystackCurl($url, $use_post, $post_data=[]){

        $headers = [
            'Authorization' => 'Bearer sk_live_82b42e494e55a2f3bf8f458ebf01070a50a7e668',
            'Content-Type' => 'application/json'
        ];
        if(!$use_post){
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders($headers)->get($url);
        }else{
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders($headers)->post($url, $post_data);
        }
        return $response;
    }

    public function transferFundsToUser($user_id,$recepient_id,$amount,$date,$time){
            
        $sender_full_name = $this->getUserParamById("full_name",$user_id);
        $sender_slug = $this->getUserParamById("slug",$user_id);
        $summary = "Member To Member Transfer";
        if($this->debitUser($user_id,$amount,$summary)){
            if($this->creditUser($recepient_id,$amount,$summary)){
            
                $title = "Transfer Credit Alert";
                $message = "This Is To Alert You That " . number_format($amount,2) . " Was Transferred To You By <a target='_blank' href='/user/".$sender_slug."'>".$sender_full_name."</a>";
                

                $form_array = array(
                    'sender' => "System",
                    'receiver' => $recepient_id,
                    'title' => $title,
                    'message' => $message,
                    'date_sent' => $date,
                    'time_sent' => $time,
                    'type' => 'misc'
                );

                if($this->sendMessage($form_array)){
                    $form_array = array(
                        'sender' => $user_id,
                        'recepient' => $recepient_id,
                        'amount' => $amount,
                        'date' => $date,
                        'time' => $time
                    );
                    
                    return DB::table("transfer_funds_history")->insert($form_array);
                }
            }
        }
    }

    public function getIfTransactionPasswordHasBeenInputedByUser($user_id){
        
        $query = DB::table('users')->where('id',$user_id)->where('transaction_password', '')->get();
        if($query->count() == 1){
            return false;
        }else{
            return true;
        }
    }

    

    public function getUsersWithdrawalHistoryPagination($user_id,$req,$length){
        
        $amount = $req->query('amount');
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');



        $query = DB::table('withdrawal_history')->where('user_id', $user_id);
        
        if(!empty($amount)){
            
            $query = $query->where('amount', 'like', '%'. $amount.'%');
        }

        
        
        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 

 
    }

    public function getTransferHistoryForThisUserByPagination($user_id,$req,$length){
        
        $amount = $req->query('amount');
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        
        // $query_str = "SELECT * FROM `transfer_funds_history` WHERE (`sender` = " . $user_id . " OR `recepient` = ". $user_id . ")";


        $query = DB::table('transfer_funds_history')->where(function ($query) use ($user_id){
            $query->where('sender',$user_id)
                  ->orWhere('recepient', $user_id);
        });
        
        if(!empty($amount)){
            
            $query = $query->where('amount', 'like', '%'. $amount.'%');
        }

        
        
        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 

 
    }

    public function getUsersAccountCreditHistoryPagination($user_id,$req,$length){
        
        $amount = $req->query('amount');
        $payment_option = $req->query('payment_option');
        $reference = $req->query('reference');
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('account_credit_history')->where('user_id',$user_id);
        
        if(!empty($amount)){
            
            $query = $query->where('amount', 'like', '%'. $amount.'%');
        }

        if(!empty($payment_option)){
            
            $query = $query->where('payment_option', 'like', '%'. $payment_option.'%');
        }

        if(!empty($reference)){
            
            $query = $query->where('reference', 'like', '%'. $reference.'%');
        }


        
        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 
 
    }

    public function getMlmHistoryCarAwardIncomeByPagination($user_id,$req,$length){
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('mlm_earnings')->where('user_id',$user_id)->where('charge_type',11);
        
        

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 



    }

    public function getMlmHistorySGPSIncomeByPagination($user_id,$req,$length){
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('mlm_earnings')->where('user_id',$user_id)->where('charge_type',15);
        
        

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 



    }

    public function getMlmHistoryVTUTradeIncomeByPagination($user_id,$req,$length){
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('mlm_earnings')->where('user_id',$user_id)->where('charge_type',14);
        
        

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 



    }


   


    public function getMlmHistoryCenterLeaderPlacementByPagination($user_id,$req,$length){
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('mlm_earnings')->where('user_id',$user_id)->where('charge_type',12);
        
        

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 



    }


    public function getMlmHistoryCenterLeaderSponsorByPagination($user_id,$req,$length){
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('mlm_earnings')->where('user_id',$user_id)->where('charge_type',7);
        
        

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 



    }

    public function getMlmHistoryPlacementByPagination($user_id,$req,$length){
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('mlm_earnings')->where('user_id',$user_id)->where('charge_type',4);
        

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 



    }


    public function getMlmIdsIndexNumber($mlm_db_id){
        $array = array();
        $user_id = $this->getMlmDbParamById("user_id",$mlm_db_id);
        // $this->db->select("id");
        // $this->db->from("mlm_db");
        // $this->db->where("user_id",$user_id);
        // $this->db->order_by("id","ASC");

        // $query = $this->db->get();

        $query = DB::table('mlm_db')->where("user_id",$user_id)->orderBy("id","ASC")->get('id');
        if($query->count() > 0){
            foreach($query as $row){
                $id = $row->id;
                $array[] = $id;
            }
        }

        if(count($array) > 0){
            for($i = 0; $i < count($array); $i++){
                if($mlm_db_id == $array[$i]){
                    return $i + 1;
                }
            }
        }
    }

    public function getMlmHistorySponsorByPagination($user_id,$req,$length){
        
        
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('mlm_earnings')->where('user_id',$user_id)->where('charge_type',3);
        

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;  

    }

    public function getAdminAccountStatementForThisUser($user_id,$req,$length){
        
        $amount = $req->query('amount');
        $balance = $req->query('balance');
        $summary = $req->query('summary');
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('account_statement')->where('user_id',$user_id);
        

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }

        if(!empty($balance)){
            $query = $query->where('amount_after', 'like', '%' . $balance.'%');

        }

        if(!empty($summary)){
            $query = $query->where('summary', 'like', '%' . $summary.'%');

        }



        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;   


    }

    public function getTotalProfitMadeForUser($user_id){
        $total_profit_made = 0;
        $query_str = "SELECT charged_num,service_charge FROM loan_advances WHERE user_id = ".$user_id . " AND amount = amount_paid";
        $query = DB::select($query_str);
        
        if(count($query) > 0){
            foreach($query as $row){
                $charged_num = $row->charged_num;
                $service_charge = $row->service_charge;
                $profit_made = ($charged_num * $service_charge);
                $total_profit_made += $profit_made;
            }
        }
        return $total_profit_made;
    }

    public function getTotalBalanceAdvanceLoanForUser($user_id){
        $total_balance = 0;
        $query_str = "SELECT amount,amount_paid FROM loan_advances WHERE user_id = ".$user_id;
        $query = DB::select($query_str);
        
        if(count($query) > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $amount_paid = $row->amount_paid;
                $balance = $amount - $amount_paid;
                $total_balance += $balance;
            }
        }
        return $total_balance;
    }

    public function getTotalAmountPaidBackAdvanceLoanForUser($user_id){
        $total_amount_paid = 0;
        
        $query = DB::table('loan_advances')->where('user_id', $user_id)->get('amount_paid');
        if($query->count() > 0){
            foreach($query as $row){
                $amount_paid = $row->amount_paid;
                $total_amount_paid += $amount_paid;
            }
        }
        return $total_amount_paid;
    }

    public function getTotalAmountRequestedAdvanceLoanForUser($user_id){
        $total_amount = 0;
        
        $query = DB::table('loan_advances')->where('user_id',$user_id)->get('amount');
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $total_amount += $amount;
            }
        }
        return $total_amount;
    }

    public function getUsersProductAdvanceHistory($user_id,$req,$length){
        
        $amount = $req->query('amount');
        $amount_paid = $req->query('amount_paid');
        $status = $req->query('status');
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('loan_advances')->where('user_id',$user_id);
        

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }

        if(!empty($amount_paid)){
            $query = $query->where('amount_paid', 'like', '%' . $amount_paid.'%');

        }

        if(!empty($status)){
            
            if($status == "pending"){
                
                $query = $query->whereRaw('amount != amount_paid');
            }else if($status == "cleared"){
                
                $query = $query->whereRaw('amount = amount_paid');
            }
        }


        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date_time', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('datetime', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;            
    }

    public function getAdminDebitHistoryForThisUser($user_id,$req,$length){
        
        $amount = $req->query('amount');
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('admin_debit_users_history')->where('user_id',$user_id);
        
        

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;            


    }

    public function getAdminCreditHistoryForThisUser($user_id,$req,$length){
        
        $amount = $req->query('amount');
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('admin_fund_users_history')->where('user_id',$user_id);
        
        

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;            

    }

    public function getTransferHistoryForThisUser($user_id,$req,$length){
        
        $amount = $req->query('amount');
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        // $query = DB::table('transfer_funds_history')->where('sender',$user_id)->orWhere('recepient', $user_id);
        $query = DB::table('transfer_funds_history')->where(function ($query) use ($user_id){
            $query->where('sender',$user_id)
                  ->orWhere('recepient', $user_id);
        });
        

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;

    }


    public function getVtuTransactionParamByOrderId($param,$order_id){
        
        $query = DB::table('vtu_transactions')->where('order_id', $order_id)->get();
        if($query->count() == 1){
            return $query[0]->$param;
        }else{
            return false;
        }
    }

    public function getVtuTransactionHistoryForThisUser($user_id,$req,$length){
        
        $type = $req->query('type');
        $sub_type = $req->query('sub_type');
        $order_id = $req->query('order_id');
        $number = $req->query('number');
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('vtu_transactions')->where('user_id', $user_id);


        if(!empty($type)){
            $query = $query->where('type', 'like', '%' . $type.'%');

        }

        if(!empty($sub_type)){
            $query = $query->where('sub_type', 'like', '%' . $sub_type.'%');

        }

        if(!empty($order_id)){
            $query = $query->where('order_id', 'like', '%' . $order_id.'%');

        }

  

        if(!empty($number)){
            $query = $query->where('number', 'like', '%' . $number.'%');

        }

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;
    }

    public function getUsersWithdrawalHistory($user_id,$req,$length){
        
        $amount = $req->query('amount');
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('withdrawal_history')->where('user_id', $user_id);


        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }

        


        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;
        
    }

    public function getUsersAccountCreditHistory($user_id,$req,$length){
        
        $amount = $req->query('amount');
        $payment_option = $req->query('payment_option');
        $reference = $req->query('reference');
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('account_credit_history')->where('user_id', $user_id);

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }

        if(!empty($payment_option)){
            $query = $query->where('payment_option', 'like', '%' . $payment_option.'%');

        }

        if(!empty($reference)){
            $query = $query->where('reference', 'like', '%' . $reference.'%');

        }


        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;
        
    }

    public function addAdminDebitHistory($user_id,$amount,$date,$time){
        return DB::table('admin_debit_users_history')->insert(array('user_id' => $user_id,'amount' => $amount,'date' => $date,'time' => $time));
    }

    public function checkIfEmailExists($email){
        
        $query = DB::table('users')->where('email', $email)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function checkIfEmailIsInId($email,$user_id){
        
        $query = DB::table('users')->where('id', $user_id)->where('email', $email)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function checkIfUserIdIsValid($user_id){
        
        $query = DB::table('users')->where('id', $user_id)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }


    public function getUsersPaginationByOffset($req,$length){
        
        $full_name = $req->query('full_name');
        $user_name = $req->query('user_name');
        $phone = $req->query('phone');
        $email = $req->query('email');
        $created_date = $req->query('created_date');
        
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');
        
        
        $query = DB::table('users')->where('is_admin', 0);
        

        if(!empty($full_name)){
            $query = $query->where('full_name', 'like', '%' . $full_name.'%');

        }

        if(!empty($user_name)){
            $query = $query->where('user_name', 'like', '%' . $user_name.'%');

        }

        if(!empty($phone)){
            $query = $query->where('phone', 'like', '%' . $phone.'%');

        }

        if(!empty($email)){
            $query = $query->where('email', 'like', '%' . $email.'%');

        }

        


        if(!empty($created_date)){
            
            if($created_date != ""){
               $created_date = date("j M Y", strtotime($created_date));    
            }
            $query = $query->where('created_date', 'like',  $created_date.'%');
        }

        

        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("full_name","ASC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query; 


    }

    
    public function getAdminDebitHistoryForAllUsersByPagination($req,$length){
        
        $amount = $req->query('amount');
        
        $date = $req->query('date');
        
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');
        
        
        $query = DB::table('admin_debit_users_history')->where('user_id','!=', 0);


        
        

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }


        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }

        

        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;  

    }

    public function getAdminCreditHistoryForAllUsersByPagination($req,$length){
        
        $amount = $req->query('amount');
        
        $date = $req->query('date');
        
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');
        
        
        $query = DB::table('admin_fund_users_history')->where('user_id','!=', 0);

        
        

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }


        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }

        

        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;


         
    }

    public function getAccountCreditHistoryForAllUsersPaginationByOffset($req,$length){
        
        

        $amount = $req->query('amount');
        $payment_option = $req->query('payment_option');
        $date = $req->query('date');
        $reference = $req->query('reference');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');
        
        
        $query = DB::table('account_credit_history')->where('user_id','!=', 0);

        
        

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }

        if(!empty($payment_option)){
            $query = $query->where('payment_option', 'like','%' . $payment_option.'%');

        }



        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }

        if(!empty($reference)){
            
            
            $query = $query->where('reference', 'like', '%' . $reference.'%');
        }

        

        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;
         
    }

    public function getDataComboHistoryPaginationByOffset($req,$length){
        
        

        $amount = $req->query('amount');
        $number = $req->query('number');
        $date = $req->query('date');
        $credited_date = $req->query('credited_date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');
        
        
        $query = DB::table('combo_recharge_vtu')->where('credited', 1)->where('amount', 'like', "%GB");
        

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }

        if(!empty($number)){
            $query = $query->where('number', 'like', $number.'%');

        }


        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }

        if(!empty($credited_date)){
            
            if($credited_date != ""){
                $credited_date = date("j M Y", strtotime($credited_date));  
            }
            $query = $query->where('credited_date', 'like',  $credited_date.'%');
        }

        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;

    }

    public function getDataComboRequestsHistoryPaginationByOffset($req,$length){
        
        
        $amount = $req->query('amount');
        $number = $req->query('number');
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('combo_recharge_vtu')->where('credited', 0)->where('amount', 'like', "%GB");
        
        

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }

        if(!empty($number)){
            $query = $query->where('number', 'like', $number.'%');

        }


        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }

        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;

    }

    public function getAirtimeComboHistoryPaginationByOffset($req,$length){
        
        

        $amount = $req->query('amount');
        $number = $req->query('number');
        $date = $req->query('date');
        $credited_date = $req->query('credited_date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');
        
        
        $query = DB::table('combo_recharge_vtu')->where('credited', 1)->where('amount', 'not like', "%GB");
        

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }

        if(!empty($number)){
            $query = $query->where('number', 'like', $number.'%');

        }


        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }

        if(!empty($credited_date)){
            
            if($credited_date != ""){
                $credited_date = date("j M Y", strtotime($credited_date));  
            }
            $query = $query->where('credited_date', 'like',  $credited_date.'%');
        }

        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;

         
    }

    public function getComboRecordParamById($param,$id){
        
        $query = DB::table('combo_recharge_vtu')->where('id', $id)->get();
        if($query->count() == 1){
            return $query[0]->$param;
        }
    }

    public function markComboRecordAsRecharged($form_array,$id,$date,$time){

        try{
            DB::table('combo_recharge_vtu')->where('id', $id)->update($form_array);

            $user_id = $this->getComboRecordParamById("user_id",$id);
            $user_name = $this->getUserParamById("user_name",$user_id);

            $combo_date = $this->getComboRecordParamById("date",$id) . " " . $this->getComboRecordParamById("time",$id);

            $amount = $this->getComboRecordParamById("amount",$id);
            $number = $this->getComboRecordParamById("number",$id);

            $title = "VTU Combo Recharged Successfully";
            $message = "This Is To Alert You That The Combo Recharge You Requested On <em class='text-primary'>" . $date . "</em> With Mobile Number <em class='text-primary'>".$number."</em> For <em class='text-primary'>".$amount."</em> Worth Of Airtime Has Been Recharged By The Admin. ";
            

            $form_array = array(
                'sender' => "System",
                'receiver' => $user_id,
                'title' => $title,
                'message' => $message,
                'date_sent' => $date,
                'time_sent' => $time,
                'type' => 'misc'
            );

            if($this->sendMessage($form_array)){
                return true;
            }
        }catch(Exception $e){
           return false;
        }
    }

    public function getAirtimeComboRequestsHistoryPaginationByOffset($req,$length){
        
        

        $amount = $req->query('amount');
        $number = $req->query('number');
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('combo_recharge_vtu')->where('credited', 0)->where('amount', 'not like', "%GB");
        

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }

        if(!empty($number)){
            $query = $query->where('number', 'like', $number.'%');

        }


        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;

    }

    public function getUserFullNameByUserId($user_id){
        
        $query = DB::table('users')->where('id', $user_id)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $full_name = $row->full_name;
            }
            // echo $user_name;
            return $full_name;
        }else{
            return false;
        }
    }

    public function getTotalBalanceAdvanceLoanForAllUsers(){
        $total_balance = 0;
        // $query_str = "SELECT amount,amount_paid FROM loan_advances";
        
        $query = DB::table('loan_advances')->get(['amount', 'amount_paid']);
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $amount_paid = $row->amount_paid;
                $balance = $amount - $amount_paid;
                $total_balance += $balance;
            }
        }
        return $total_balance;
    }

    public function getTotalAmountPaidBackAdvanceLoanForAllUsers(){
        $total_amount_paid = 0;
        // $query_str = "SELECT amount_paid FROM loan_advances";
        
        $query = DB::table('loan_advances')->get('amount_paid');
        if($query->count() > 0){
            foreach($query as $row){
                $amount_paid = $row->amount_paid;
                $total_amount_paid += $amount_paid;
            }
        }
        return $total_amount_paid;
    }

    public function getTotalAmountRequestedAdvanceLoanForAllUsers(){
        $total_amount = 0;
        $query_str = "SELECT amount FROM loan_advances";
        $query = DB::table('loan_advances')->get('amount');
        
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $total_amount += $amount;
            }
        }
        return $total_amount;
    }

    public function getProductAdvanceHistoryForAllUsersByPagination($req,$length){
        
        

        $amount = $req->query('amount');
        $amount_paid = $req->query('amount_paid');
        $status = $req->query('status');
        $date = $req->query('date');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('loan_advances')->where('id','!=', 0);

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }

        if(!empty($amount_paid)){
            $query = $query->where('amount_paid', 'like', '%' . $amount_paid.'%');

        }

        

        if(!empty($status)){
            if($status == "pending"){
                // $query_str .= " AND amount != amount_paid ";
                $query = $query->whereRaw('amount != amount_paid');
            }else if($status == "cleared"){
                // $query_str .= " AND amount = amount_paid ";
                $query = $query->whereRaw('amount = amount_paid');
            }
        }

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date_time', 'like',  $date.'%');
        }


        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('datetime', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);
        return $query;
    }

    public function addWithrawalHistory($user_id,$amount){
        $date = date("j M Y");
        $time = date("h:i:sa");

        return DB::table('withdrawal_history')->insert(array('user_id' => $user_id,'amount' => $amount,'date' => $date,'time' => $time));
    }

    public function updateWithdrawalRequest($form_array,$id){
        
        try{
            DB::table('withdrawal_request')->where('id', $id)->update($form_array);
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function getWithdrawalRequestParamById($param,$id){
        $query = DB::table('withdrawal_request')->where('id', $id)->get();
        if($query->count() == 1){
            return $query[0]->$param;
        }else{
            return false;
        }
    }

    public function getAccountWithdrawalRequestsForAllUsersByPagination($req,$length){
        
        

        $amount = $req->query('amount');
        $user_name = $req->query('user_name');
        $status = $req->query('status');
        $date = $req->query('date');
        $debited_date_time = $req->query('debited_date_time');
        $dismissed_date_time = $req->query('dismissed_date_time');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('withdrawal_request')->where('id','!=', 0);

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }

        if(!empty($user_name)){
            $query = $query->where('user_name', 'like', '%' . $user_name.'%');
        }

        if(!empty($status)){
            
            if($status == "dismissed"){
                
                $query = $query->where('debited', 0)->where('dismissed', 1);
            }else if($status == "debited"){
                $query = $query->where('debited', 1)->where('dismissed', 0);
            }else if($status == "all"){
                
            }else if($status == "pending"){                
                $query = $query->where('debited', 0)->where('dismissed', 0);
            }else{
                $query = $query->where('debited', 0)->where('dismissed', 0);
            }
        }else{
            $query = $query->where('debited', 0)->where('dismissed', 0);
        }

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }


        if(!empty($debited_date_time)){
            if($debited_date_time != ""){
                $debited_date_time = date("j M Y", strtotime($debited_date_time));  
            }
            $query = $query->where('debited_date_time', 'like',  $debited_date_time.'%');
        }


        if(!empty($dismissed_date_time)){
            if($dismissed_date_time != ""){
                $dismissed_date_time = date("j M Y", strtotime($dismissed_date_time));  
            }
            $query = $query->where('dismissed_date_time', 'like',  $dismissed_date_time.'%');
        }

        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);


        
        return $query;
       
    }

    public function creditUsersRegistrationAmount($user_id,$amount){
        $query = DB::table('users')->where('id', $user_id)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $registration_amt_paid = $row->registration_amt_paid;
            }
            $new_total_income = $registration_amt_paid + $amount;
            try{
                DB::table('users')->where('id', $user_id)->update(array('registration_amt_paid' => $new_total_income));
                return true;
            }catch(Exception $e){
                return false;
            }
        }
    }

    public function addAdminCreditHistory($user_id,$amount,$date,$time){
        return DB::table('admin_fund_users_history')->insert(array('user_id' => $user_id,'amount' => $amount,'date' => $date,'time' => $time));
    }

    public function updateCreditAccountPaymentProofsRecord($form_array,$id){
        try{
            DB::table('credit_account_payment_proofs')->where('id', $id)->update($form_array);
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function creditUser($user_id,$amount,$summary = ""){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $date_time = $date . " " . $time;
        
        $query = DB::table('users')->where('id', $user_id)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $total_income = $row->total_income;
                $withdrawn = $row->withdrawn;
                $created = $row->created;
            }
            if($created == 1){
                $wallet_balance = $total_income - $withdrawn;
                $new_total_income = $total_income + $amount;
                $amount_after = $wallet_balance + $amount;
                
                try{
                    DB::table('users')->where('id', $user_id)->update(array('total_income' => $new_total_income));

                    if(DB::table('account_statement')->insert(array('user_id' => $user_id,'amount' => $amount,'amount_before' => $wallet_balance,'amount_after' => $amount_after,'summary' => $summary,'date' => $date,'time' => $time))){

                        // $this->runTheMainCheckAndDebitingMnthlyServiceCharge($user_id);
                        $query = DB::table('loan_advances')->where("user_id",$user_id)->orderBy("id","DESC")->limit(1)->get();

                        if($query->count() == 1){
                            foreach($query as $row){
                                $id = $row->id;
                                $amount_due = $row->amount;
                                $amount_paid = $row->amount_paid;
                                $amount_currently_due = $amount_due - $amount_paid;
                                $date_time_requested = $row->date_time;

                                if($amount_currently_due > 0){
                                    if($amount > $amount_currently_due){
                                        $amount_to_debit = $amount_currently_due;
                                    }else if($amount < $amount_currently_due){
                                        $amount_to_debit = $amount;
                                    }else if($amount == $amount_currently_due){
                                        $amount_to_debit = $amount_currently_due;
                                    }


                                    $start_date = $date_time_requested;
                                    $start_date = date("j M Y",strtotime($start_date));
                                    $start_date = strtotime($start_date); 
                                    $end_date = strtotime(date("j M Y")); 
                                      
                                    
                                    
                                    $date_diff = ($end_date - $start_date)/60/60/24; 

                                    if($date_diff >= 30){

                                        $amount_paid += $amount_to_debit;

                                        $form_array = array(
                                            'amount_paid' => $amount_paid,
                                            'last_date_time_paid' => $date_time
                                        );

                                        $summary = "Product Advance Repayment. Balance: " .number_format(($amount_due - $amount_paid),2);

                                        if($this->debitUser($user_id,$amount_to_debit,$summary)){
                                            try{
                                                DB::table('loan_advances')->where('id', $id)->update($form_array);
                                                return true;
                                            }catch(Exception $e){
                                                return false;
                                            }
                                        }
                                    }else{
                                        return true;
                                    }
                                }else{
                                    return true;
                                }
                            }
                        }else{
                            return true;
                        }
                    }
                }catch(Exception $e){
                    return false;
                }
                
            }else{
                if($this->creditUsersRegistrationAmount($user_id,$amount)){
                    return true;
                }
            }
        }
    }

    public function getAccountCreditRequestsForAllUsersByPagination($req,$length){
        
        

        $amount = $req->query('amount');
        $user_name = $req->query('user_name');
        $depositors_name = $req->query('depositors_name');
        $credited_amount = $req->query('credited_amount');
        $status = $req->query('status');
        $date_of_payment = $req->query('date_of_payment');
        $date = $req->query('date');
        $credited_date_time = $req->query('credited_date_time');
        $dismissed_date_time = $req->query('dismissed_date_time');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');


        
        $query = DB::table('credit_account_payment_proofs')->where('id','!=', 0);

        if(!empty($amount)){
            $query = $query->where('amount', 'like', '%' . $amount.'%');

        }

        if(!empty($user_name)){
            $query = $query->where('user_name', 'like', '%' . $user_name.'%');
        }

        if(!empty($depositors_name)){
            $query = $query->where('depositors_name', 'like', '%' . $depositors_name.'%');
        }

        if(!empty($credited_amount)){
            $query = $query->where('credited_amount', 'like', '%' . $credited_amount.'%');
        }

        if(!empty($status)){
            // $query = $query->appends(['status' => $status]);
            if($status == "dismissed"){
                
                $query = $query->where('credited', 0)->where('dismissed', 1);
            }else if($status == "credited"){
                
                $query = $query->where('credited', 1)->where('dismissed', 0);
            }else if($status == "all"){
                
            }else if($status == "pending"){
                
                $query = $query->where('credited', 0)->where('dismissed', 0);
            }else{
                
                $query = $query->where('credited', 0)->where('dismissed', 0);
            }
        }else{
            $query = $query->where('credited', 0)->where('dismissed', 0);
        }

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date', 'like',  $date.'%');
        }

        if(!empty($date_of_payment)){
            
            $query = $query->where('date_of_payment', 'like',  $date_of_payment.'%');
        }

        if(!empty($credited_date_time)){
            if($credited_date_time != ""){
                $credited_date_time = date("j M Y", strtotime($credited_date_time));  
            }
            $query = $query->where('credited_date_time', 'like',  $credited_date_time.'%');
        }


        if(!empty($dismissed_date_time)){
            if($dismissed_date_time != ""){
                $dismissed_date_time = date("j M Y", strtotime($dismissed_date_time));  
            }
            $query = $query->where('dismissed_date_time', 'like',  $dismissed_date_time.'%');
        }

        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('date_time', [$start_date, $end_date]);
        }


        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);


        
        return $query;


       
    }

    public function updateComplaintRecord($form_array,$id){
        try{
            DB::table('complaints')->where('id', $id)->update($form_array);
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function convertLocalNumberToInter($number)
        {
        //make sure the number is actually a number
        if(is_numeric($number)){

            // //if number doesn't start with a 0 or a 4 add a 0 to the start.
            // if($number[0] != 0 && $number[0] != 4){
            //     $number = "0".$number;
            // }

            //if number starts with a 0 replace with 4
            if($number[0] == 0){
                $number = substr($number, 1); 
                $number = "+234".$number;
            }

            //remove any spaces in the number
            $number = str_replace(" ","",$number);

            //return the number
            return $number;

        //number is not a number
        } else {

            //return nothing
            return false;
        }
    }

    public function getComplaintInfoById($id){
        $query = DB::table('complaints')->where('id', $id)->get();
        if($query->count() == 1){
            return $query;
        }else{
            return false;
        }
    }


    public function getComplaintsForAllUsersByPagination($req,$length){
        
        $user_name = $req->query('user_name');
        
        $whatsapp_number = $req->query('whatsapp_number');
        $type = $req->query('type');
        $status = $req->query('status');
        $date_of_recharge = $req->query('date_of_recharge');
        $date = $req->query('date');
        $dismissed_date_time = $req->query('dismissed_date_time');
        $start_date = $req->query('start_date');
        $end_date = $req->query('end_date');

        $query = DB::table('complaints')->where('id','!=', 0);

        if(!empty($user_name)){
            $query = $query->where('user_name', 'like', '%' . $user_name.'%');
        }

        if(!empty($whatsapp_number)){
            $query = $query->where('whatsapp_number', 'like', '%' . $whatsapp_number.'%');
        }

        if(!empty($type)){
            if($type != "all"){
                $query = $query->where('type', $type);
            }
        }

        if(!empty($status)){
            
            if($status == "dismissed"){
                
                $query = $query->where('dismissed', 1);
            }else if($status == "all"){
                
            }else if($status == "pending"){
                $query = $query->where('dismissed', 0);
            }else{
                $query = $query->where('dismissed', 0);
            }
        }else{
            
            $query = $query->where('dismissed', 0);
        }

        if(!empty($date)){
            
            if($date != ""){
               $date = date("j M Y", strtotime($date));    
            }
            $query = $query->where('date_time', 'like',  $date.'%');
        }

        if(!empty($date_of_recharge)){
                       
            $query = $query->where('date_of_recharge', 'like',  $date_of_recharge.'%');
        }

        if(!empty($dismissed_date_time)){
            if($dismissed_date_time != ""){
                $dismissed_date_time = date("j M Y", strtotime($dismissed_date_time));  
            }        
            $query = $query->where('dismissed_date_time', 'like',  $dismissed_date_time.'%');
        }

        if(!empty($start_date) && !empty($end_date)){

            $start_date = date("Y-m-d", strtotime($start_date));  
            $end_date = date("Y-m-d", strtotime($end_date));  
            $query = $query->whereBetween('datetime', [$start_date, $end_date]);
        }

        $query = $query->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        // $query->withPath('/reviews?full_name='.$full_name."&date=".$date."&status=".$status);


        
        return $query;
       
    }

    public function addComplaint($form_array){
        
        return DB::table('complaints')->insert($form_array);
    }


    public function upgradeMlmAccountToBusiness($mlm_db_id,$user_id,$date,$time,$reference = NULL){

        $sponsor_id = $this->getMlmDbParamById("sponsor",$mlm_db_id);
        $sponsor_user_id = $this->getMlmDbParamById("user_id",$sponsor_id);
        $sponsor_income = $this->getSponsorChargeForBusinessPackage();
        $sponsor_income_vat = $this->getSponsorVatChargeForBusinessPackage();
        $sponsor_income_vat_perc = $sponsor_income_vat / 100;
        $sponsor_income_vat_val = ($sponsor_income_vat_perc * $sponsor_income);
        $real_sponsor_income = $sponsor_income - ($sponsor_income_vat_perc * $sponsor_income);

        $placement_income = $this->getPlacementChargeForBusinessPackage();
        $placement_income_vat = $this->getPlacementVatChargeForBusinessPackage();
        $placement_income_vat_perc = $placement_income_vat / 100;
        $placement_income_vat_val = ($placement_income_vat_perc * $placement_income);
        $real_placement_income = $placement_income - ($placement_income_vat_perc * $placement_income);

        $car_bonus_income = $this->getCarBonus();
        $car_bonus_income_vat = $this->getCarBonusVat();
        if($car_bonus_income_vat != 0){
            $car_bonus_income_vat_perc = $car_bonus_income_vat / 100;
            $car_bonus_income_vat_val = ($car_bonus_income_vat_perc * $car_bonus_income);
            $real_car_bonus_income = $car_bonus_income - ($car_bonus_income_vat_perc * $car_bonus_income);
        }else{
            $car_bonus_income_vat_perc = 0;
            $car_bonus_income_vat_val = 0;
            $real_car_bonus_income = $car_bonus_income;
        }
        
        
        
        
        $this->creditUserCarBonus($mlm_db_id,$car_bonus_income,$car_bonus_income_vat,$real_car_bonus_income,11,$date,$time,$car_bonus_income_vat_val);
        $this->creditUserPlacementIncome($mlm_db_id,$placement_income,$placement_income_vat,$real_placement_income,4,$date,$time,$placement_income_vat_val);
        // $sponsors_first_mlm_db_id = $this->getUsersFirstMlmDbId($sponsor_id);
        $this->creditUserSponsorIncome($user_id,$sponsor_user_id,$sponsor_income,$sponsor_income_vat,$real_sponsor_income,$sponsor_id,3,$date,$time,$sponsor_income_vat_val);
        $former_references = $this->getMlmDbParamById("reference",$mlm_db_id);
        if(!is_null($former_references)){
            $former_references_arr = explode(",",$former_references);
            $former_references_arr[] = $reference;
            $reference = implode(",",$former_references_arr);
        }

        
        $user_id = $this->getUserIdWhenLoggedIn();

        $created_date = $this->getUserParamById("created_date",$user_id);
        $registration_amt_paid = $this->getUserParamById("registration_amt_paid",$user_id);
        $created = $this->getUserParamById("created",$user_id);
        $rgwb_paid = $this->getUserParamById("rgwb_paid",$user_id);

        $start_date = "26 Sep 2020";
        $start_date = date("j M Y",strtotime($start_date));
        $start_date = strtotime($start_date); 
        $end_date = strtotime($created_date); 
          
        
        
        $date_diff = ($start_date - $end_date)/60/60/24; 
        // echo $date_diff . "<br>";



        if($date_diff <= 0){


            // if($created == 1 && $rgwb_paid == 0){
                // $amount = 650;
                // $summary = "Upgrade Bonus";
                $form_array1 = array(
                    'rgwb_paid' => 1
                );
                if($this->updateUserTable($form_array1,$user_id)){
                    // if($this->creditUser($user_id,$amount,$summary)){
                        // return true;
                    // }
                }
            // }
        }
        $form_array = array(
            'reference' => $reference,
            'package' => 2,
            'date_created' => $date,
            'time_created' => $time
        );
        if($this->updateMlmTable($form_array,$mlm_db_id)){
            return true;
        }
    }

    public function updateMlmTable($form_array,$mlm_db_id){
        try{
            DB::table('mlm_db')->where('id', $mlm_db_id)->update($form_array);
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function creditUserCarBonus($mlm_db_id,$car_bonus_income,$car_bonus_income_vat,$real_car_bonus_income,$charge_type,$date,$time,$car_bonus_income_vat_val){
        $creditors_user_id = $this->getMlmDbParamById("user_id",$mlm_db_id);
        $ids_to_credit = $this->getIdsToCreditPlacement($mlm_db_id);
        for($i = 0; $i < count($ids_to_credit); $i++){
            $user_id = $ids_to_credit[$i]['user_id'];
            $placements_mlm_db_id = $ids_to_credit[$i]['mlm_db_id'];

            
            if(DB::table('mlm_earnings')->insert(array('user_id' => $user_id,'mlm_db_id' => $placements_mlm_db_id,'charge_type' => $charge_type,'amount' => $car_bonus_income,'vat' => $car_bonus_income_vat,'date' => $date,'time' => $time))){


                $total_vat = $this->getUserParamById("admin_vat_total",$this->getAdminId());
                $new_vat_total = $total_vat + $car_bonus_income_vat_val;
                $form_array = array(
                    'admin_vat_total' => $new_vat_total
                );

                if($this->updateUserTable($form_array,$this->getAdminId())){
                    $form_array = array();
                }

                
            }
        }
    }

    public function getCarBonus(){
        
        $query = DB::table('mlm_charges')->where('id', 11)->get();
        if($query->count() == 1){
            return $query[0]->amount;
        }
    }

    public function getCarBonusVat(){
        
        $query = DB::table('mlm_charges')->where('id', 11)->get();
        if($query->count() == 1){
            return $query[0]->vat;
        }
    }

    public function checkIfMlmDbIdBelongsToUser($mlm_db_id,$user_id){
        
        $query = DB::table('mlm_db')->where('user_id', $user_id)->where('id', $mlm_db_id)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function getUserHashedById($user_id){
        $query = DB::table('users')->where('id', $user_id)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $hashed = $row->hashed;
            }
            return $hashed;
        }else{
            return "";
        }
    }

    public function getLastTwentyUsersRegisteredUsers(){
        $date = date("j M Y");

        $query_str = "SELECT * FROM users WHERE date = '". $date . "' ORDER BY id DESC LIMIT 20";
        $query = DB::select($query_str);
        if(count($query) > 0){
            return $query;
        }else{
            return false;
        }
    }

    public function getChartistNumberForRegistrationsByMonth($month){
        $year = date("Y");
        $month_year = $month . " " . $year;

        $query_str = "SELECT id FROM users WHERE RIGHT(created_date, 8) = '".$month_year."'";
        $query = DB::select($query_str);
        return count($query);
    }

    public function getTotalProfitMadeForAllUsers(){
        $total_profit_made = 0;
        $query_str = "SELECT charged_num,service_charge FROM loan_advances WHERE amount = amount_paid";
        $query = DB::select($query_str);
        if(count($query) > 0){
            foreach($query as $row){
                $charged_num = $row->charged_num;
                $service_charge = $row->service_charge;
                $profit_made = ($charged_num * $service_charge);
                $total_profit_made += $profit_made;
            }
        }
        return $total_profit_made;
    }

    public function getTotalPendingAmountForProductAdvance(){
        $num = 0;
        $query_str = "SELECT amount,amount_paid FROM loan_advances WHERE amount != amount_paid";
        $query = DB::select($query_str);
        if(count($query) > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $amount_paid = $row->amount_paid;

                $balance = $amount - $amount_paid;
                $num += $balance;
            }
        }

        return $num;
    }

    public function getTotalAmountWithdrawnToday(){
        $total_amount = 0;
        $date = date("j M Y");

        $query_str = "SELECT amount FROM withdrawal_history WHERE date = '". $date . "'";
        $query = DB::select($query_str);
        if(count($query) > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $total_amount += $amount;
            }
        }

        return $total_amount;
    }

    public function getTotalAmountForOnlinePaymentMadeToday(){
        $total_amount = 0;
        $date = date("j M Y");

        $query_str = "SELECT amount FROM account_credit_history WHERE date = '". $date . "' AND (payment_option = 'monnify' OR payment_option = 'paystack')";
        $query = DB::select($query_str);
        if(count($query) > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $total_amount += $amount;
            }
        }

        return $total_amount;
    }


    public function getTotalNumberOfRegisteredUsers(){
        $query = DB::table('users')->get('id');
        return $query->count();
    }

    public function getTotalNumberOfUsersRegisteredToday(){
        $date = date("j M Y");
        $query = DB::table('users')->where("date",$date)->get('id');
        return $query->count();
    }

    public function getDownlineArrForUserReferralsForThisMonth($mlm_db_id){
        $ret_arr = array();
        $year = date("Y");
        $month = date("M");

        // $year = "2020";
        // $month = "Jul";

        $month_year = $month . " " . $year;
        // echo $parentID;
        
        // Create the query
        
        $query_str = "SELECT date_created FROM mlm_db WHERE RIGHT(date_created, 8) = '".$month_year."' AND sponsor = ".$mlm_db_id;
        
        // echo $query_str;
        // Execute the query and go through the results.
        
        $query = DB::select($query_str);
        if(count($query) > 0)
        {
            return $query;
        }else{
            return false;
        }
        
    }

    public function getChartistArrayForUserDownlineForTheMonth($user_id){
        $year = date("Y");
        $month = date("M");

        // $year = "2020";
        // $month = "Jul";

        $month_year = $month . " " . $year;

        $mlm_db_id = $this->getUsersFirstMlmDbId($user_id);
        $downline_arr = $this->getDownlineArrForUserReferralsForThisMonth($mlm_db_id);



        $first_week_count = 0;
        $second_week_count = 0;
        $third_week_count = 0;
        $fourth_week_count = 0;
        // $year = "2019";

        if(is_array($downline_arr)){
            // Create the query
            foreach($downline_arr as $row){
                // echo "string";
            
                $date_created = $row->date_created;
                $year_str = date('Y', strtotime($date_created));
                $month_str = date('M', strtotime($date_created));
                $day_str = date('j', strtotime($date_created));

                // echo $month_str . "<br>";

                
                if($day_str <= 7){
                    $first_week_count++;
                }else if($day_str <= 14){
                    $second_week_count++;
                }else if($day_str <= 21){
                    $third_week_count++;
                }else{
                    $fourth_week_count++;
                }
                
            }
        }

        $ret_arr = array($first_week_count,$second_week_count,$third_week_count,$fourth_week_count);
        
        return $ret_arr;
    }


    public function getTopTenMlmEarnersForTheMonth(){
        $year = date("Y");
        $month = date("M");

        $month_year = $month . " " . $year;

        $query_str = "SELECT id,user_name,slug,full_name,".strtolower($month . "_earnings")." FROM users WHERE is_admin = 0 ORDER BY ". strtolower($month . "_earnings") . " DESC LIMIT 10";
        $query = DB::select($query_str);
        return $query;
    }


    public function getDownlineArr1($parentID,$ret_arr = array()){

            
            // Create the query
            
        $query_str = "SELECT id,date_created,package FROM mlm_db WHERE ";
        if($parentID == null) {
            $query_str .= "under IS NULL";
        }
        else {
            $query_str .= "`under`=" . intval($parentID);
        }

        $query_str .= " ORDER BY positioning ASC";
        // Execute the query and go through the results.
        
        $query = DB::select($query_str);
        
        if(count($query) > 0)
        {
            
            foreach($query as $row)
            {
                $currentID = $row->id;
                $date_created = $row->date_created;
                $package = $row->package;
                // echo $currentID;
                $ret_arr[] = array($currentID,$date_created,$package);
                
                $ret_arr = $this->getDownlineArr1($currentID,$ret_arr);
            }
            
        }
        
        return $ret_arr;
    }

    public function getUsersTradeDeliveryIncome($user_id){
        $total = 0;
        
        $query = DB::table('mlm_earnings')->where("charge_type",13)->where("user_id",$user_id)->select('amount','vat')->get();
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $vat = $row->vat;
                $vat_perc = $vat / 100;

                $sub_total = ($amount - ($amount * $vat_perc));
                $total += $sub_total;
            }
        }
        return $total;
    }

    public function getUsersVtuTradeIncome($user_id){
        $total = 0;
        
        $query = DB::table('mlm_earnings')->where("charge_type",14)->where("user_id",$user_id)->select('amount','vat')->get();
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $vat = $row->vat;
                $vat_perc = $vat / 100;

                $sub_total = ($amount - ($amount * $vat_perc));
                $total += $sub_total;
            }
        }
        return $total;
    }

    public function getUsersSGPSIncome($user_id){
        $total = 0;
        $query = DB::table('mlm_earnings')->where("charge_type",15)->where("user_id",$user_id)->select('amount','vat')->get();
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $vat = $row->vat;
                $vat_perc = $vat / 100;

                $sub_total = ($amount - ($amount * $vat_perc));
                $total += $sub_total;
            }
        }
        return $total;
    }

    public function getUsersMlmBasicPlacementEarnings($user_id){
        $total = 0;
        $query = DB::table('mlm_earnings')->where("charge_type",2)->where("user_id",$user_id)->select('amount','vat')->get();
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $vat = $row->vat;
                $vat_perc = $vat / 100;

                $sub_total = ($amount - ($amount * $vat_perc));
                $total += $sub_total;
            }
        }
        return $total;
    }

    public function getUsersMlmBusinessPlacementEarnings($user_id){
        $total = 0;
        

        $query = DB::table('mlm_earnings')->where("charge_type",4)->where("user_id",$user_id)->select('amount','vat')->get();
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $vat = $row->vat;
                $vat_perc = $vat / 100;

                $sub_total = ($amount - ($amount * $vat_perc));
                $total += $sub_total;
            }
        }
        return $total;
    }

    public function getUsersCenterLeaderSponsorBonus($user_id){
        $total = 0;
        $query = DB::table('mlm_earnings')->where("charge_type",7)->where("user_id",$user_id)->select('amount','vat')->get();
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $vat = $row->vat;
                $vat_perc = $vat / 100;

                $sub_total = ($amount - ($amount * $vat_perc));
                $total += $sub_total;
            }
        }
        return $total;
    }


    public function getUsersCenterConnectorSponsorBonus($user_id){
        $total = 0;

        $query = DB::table('mlm_earnings')->where("charge_type",18)->where("user_id",$user_id)->select('amount','vat')->get();
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $vat = $row->vat;
                $vat_perc = $vat / 100;

                $sub_total = ($amount - ($amount * $vat_perc));
                $total += $sub_total;
            }
        }
        return $total;
    }

    public function getUsersVendorSponsorBonus($user_id){
        $total = 0;

        $query = DB::table('mlm_earnings')->where("charge_type",16)->where("user_id",$user_id)->select('amount','vat')->get();
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $vat = $row->vat;
                $vat_perc = $vat / 100;

                $sub_total = ($amount - ($amount * $vat_perc));
                $total += $sub_total;
            }
        }
        return $total;
    }

    public function getUsersCenterLeaderPlacementBonus($user_id){
        $total = 0;
        
        $query = DB::table('mlm_earnings')->where("charge_type",12)->where("user_id",$user_id)->select('amount','vat')->get();
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $vat = $row->vat;
                $vat_perc = $vat / 100;

                $sub_total = ($amount - ($amount * $vat_perc));
                $total += $sub_total;
            }
        }
        return $total;
    }

    public function getUsersCenterConnectorPlacementBonus($user_id){
        $total = 0;
        
        $query = DB::table('mlm_earnings')->where("charge_type",19)->where("user_id",$user_id)->select('amount','vat')->get();
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $vat = $row->vat;
                $vat_perc = $vat / 100;

                $sub_total = ($amount - ($amount * $vat_perc));
                $total += $sub_total;
            }
        }
        return $total;
    }

    public function getUsersVendorPlacementBonus($user_id){
        $total = 0;

        $query = DB::table('mlm_earnings')->where("charge_type",17)->where("user_id",$user_id)->select('amount','vat')->get();
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $vat = $row->vat;
                $vat_perc = $vat / 100;

                $sub_total = ($amount - ($amount * $vat_perc));
                $total += $sub_total;
            }
        }
        return $total;
    }



    public function getUsersCarAwardEarnings($user_id){
        $total = 0;

        $query = DB::table('mlm_earnings')->where("charge_type",11)->where("user_id",$user_id)->select('amount','vat')->get();
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $vat = $row->vat;
                $vat_perc = $vat / 100;

                $sub_total = ($amount - ($amount * $vat_perc));
                $total += $sub_total;
            }
        }
        return $total;
    }

    public function getUsersMlmBusinessSponsorEarnings($user_id){
        $total = 0;
        $query = DB::table('mlm_earnings')->where("charge_type",3)->where("user_id",$user_id)->select('amount','vat')->get();
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $vat = $row->vat;
                $vat_perc = $vat / 100;

                $sub_total = ($amount - ($amount * $vat_perc));
                $total += $sub_total;
            }
        }
        return $total;
    }

    public function getUsersMlmBasicSponsorEarnings($user_id){
        $total = 0;
        

        $query = DB::table('mlm_earnings')->where("charge_type",1)->where("user_id",$user_id)->select('amount','vat')->get();
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $vat = $row->vat;
                $vat_perc = $vat / 100;

                $sub_total = ($amount - ($amount * $vat_perc));
                $total += $sub_total;
            }
        }
        return $total;
    }

    public function getTotalAmountWithdrawnByUser($user_id){
        $total_withdrawn = 0;
        $query = DB::table('withdrawal_history')->where("user_id",$user_id)->get("amount");
        if($query->count() > 0){
            foreach($query as $row){
                $amount = $row->amount;
                $total_withdrawn += $amount;
            }
        }

        return $total_withdrawn;
    }

    public function getUsersWalletBalance($user_id){
        
        $query = DB::table('users')->where('id', $user_id)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $total_income = $row->total_income;
                $withdrawn = $row->withdrawn;

                $wallet_balance = $total_income - $withdrawn;

                return $wallet_balance;
            }
        }
    }

    public function verifyUserSignedInIsAdmin(){
        $user_id = $this->getUserIdWhenLoggedIn();
        $query = DB::table('users')->where('is_admin', 1)->where('id', $user_id)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function getNotifsNum($user_id){
        $query = DB::table('notif')->where('receiver',$user_id)->get();
        return $query->count();
    }

    public function getPostSlugById($post_id){
        
        $query = DB::table('posts')->where('id', $post_id)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $slug = $row->slug;
            }
            return $slug;
        }else{
            return false;
        }
    }

    public function getNotifs($user_id){
        $query = DB::table('notif')->where('receiver',$user_id)->orderBy('id','DESC')->limit(15)->get();
        if($query->count() > 0){
            return $query;
        }else{
            return false;
        }
    }


    public function getNotifCount($user_id){
        $query = DB::table('notif')->where('receiver', $user_id)->where('received', 0)->get();
        return $query->count();
    }

    public function getConversationsNum($user_id){
       
        $query = DB::table('messages')->where('receiver',$user_id)->get();
        if($query->count() > 0){
            // $ret_arr = array('sender' => )
            $rows = array();
            $new_rows = array();
            foreach($query as $row){
                $sender = $row->sender;
                $id = $row->id;
                $date = $row->date;
                $time = $row->time;
                $received = $row->received;
                $date_time = $date . " " . $time;
                $message = $row->message;
                $rows[] = array(
                    'sender' => $sender,
                    'id' => $id,
                    'date_time' => $date_time,
                    'received' => $received,
                    'message' => $message
                );
            }
            
            // $rows = array_unique($rows,SORT_REGULAR);
            $rows1 = array_unique($this->array_column_manual($rows, 'sender'));
            // print_r(array_intersect_key($array, $tempArr));
            $rows = array_intersect_key($rows,$rows1);
            $rows = array_values($rows);
            // $rows = array_slice($rows, 0,20);
            // var_dump($rows); 
            $rows = count($rows);           
        }else{
            $rows = 0;
        }
        return $rows;
    }


    public function getNumberOfNewMessagesFromSender($user_id,$sender){
        
        $query = DB::table('messages')->where('sender', $sender)->where('receiver', $user_id)->where('received', 0)->get();
        if($query->count() > 0 ){
            return "(". $query->count() .")";
        }else{
            return "";
        }
    }

    public function getUserLogoById($sender){
        
        $query = DB::table('users')->where('id', $sender)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $logo = $row->logo;
            }
            if(is_null($logo)){
                $logo = '/images/avatar.jpg';
            }else{
                $logo = '/storage/images/'.$logo;
            }
            return $logo;
        }else{
            return "";
        }
    }

    public function getSocialMediaTime($post_date,$post_time){
        $social_formated_time = "";
        if($post_date !== "" && $post_time !== ""){
            $post_date = strtotime($post_date);
            $post_date = date("j M Y",$post_date);
            $post_time = strtotime($post_time);
            $post_time = date("H:i:s",$post_time);

            $post_date1 = $post_date;
            $post_time1 = $post_time;

            $curr_date = date("j M Y");
            $curr_time = date("h:i:sa");
            $curr_date = date("j M Y",strtotime($curr_date));
            $curr_time = date("H:i:s",strtotime($curr_time));
            
            $curr_date = $curr_date . " " . $curr_time;
            // echo $curr_date;
            $curr_date = new DateTime($curr_date);
            $post_date = $post_date . " " .$post_time;
            $post_date = new DateTime($post_date);

            $time_diff = $curr_date->getTimestamp() - $post_date->getTimestamp();
            // echo $time_diff;
            if($time_diff >= 0){
                //First Check If Time Is Greater Equal
                if($time_diff == 0){
                    $social_formated_time = "Just Now";
                }else if($time_diff <= 60){
                    $social_formated_time = $time_diff . " secs ago";
                }else if(($time_diff > 60) && ($time_diff < 3600)){
                    $social_formated_time = floor($time_diff / 60);
                    $social_formated_time = $social_formated_time . " mins ago";
                }else if(($time_diff >= 3600) && ($time_diff < 86400)){
                    $social_formated_time = floor($time_diff / 3600);
                    if($social_formated_time == 1){
                        $social_formated_time = $social_formated_time . " hour ago";
                    }else{
                        $social_formated_time = $social_formated_time . " hours ago";
                    }
                }else if(($time_diff >= 86400) && ($time_diff < 2628000)){
                    $social_formated_time = floor($time_diff / 86400);
                    if($social_formated_time == 1){
                        $social_formated_time = $social_formated_time . " day ago";
                    }else{
                        $social_formated_time = $social_formated_time . " days ago";
                    }
                }else if(($time_diff >= 2628000) && (date("Y") == date("Y",strtotime($post_date1)))){
                    $social_formated_time = date("j M",strtotime($post_date1));
                }else if ((date("Y") !== date("Y",strtotime($post_date1)))) {
                    $social_formated_time = date("j M Y",strtotime($post_date1));
                }
            }
        }
        return $social_formated_time;
    }

    public function getConversations($user_id){
        // $query = $this->db->get_where('messages',array('receiver' => $user_id,'received' => 0));
        

        $query = DB::table('messages')->where('receiver',$user_id)->orWhere('sender', $user_id)->orderBy("id","DESC")->get();
        if($query->count() > 0){
            // $ret_arr = array('sender' => )
            $rows = array();
            $new_rows = array();
            foreach($query as $row){
                $sender = $row->sender;
                $id = $row->id;
                $date = $row->date;
                $time = $row->time;
                $received = $row->received;
                $date_time = $date . " " . $time;
                $message = $row->message;
                $receiver = $row->receiver;
                $rows[] = array(
                    'sender' => $sender,
                    'id' => $id,
                    'date_time' => $date_time,
                    'received' => $received,
                    'message' => $message,
                    'receiver' => $receiver
                );
            }
            
            // $rows = array_unique($rows,SORT_REGULAR);
            $rows1 = array_unique($this->array_column_manual($rows, 'sender'));
            // var_dump($rows1);
            // print_r(array_intersect_key($array, $tempArr));
            $rows = array_intersect_key($rows,$rows1);
            $rows = array_values($rows);
            $rows = array_slice($rows, 0,20);
            // var_dump($rows);             
        }else{
            $rows = false;
        }
        return $rows;
    }

    public function array_column_manual($array, $column)
    {
        $newarr = array();
        foreach ($array as $row) $newarr[] = $row[$column];
        return $newarr;
    }


    public function custom_echo($x, $length)
    {
      if(strlen($x)<=$length)
      {
        return $x;
      }
      else
      {
        $y=substr($x,0,$length) . '...';
        return $y;
      }
    }

    public function getNewNotifsCount(){
        $user_id = $this->getUserIdWhenLoggedIn();
        
        $query = DB::table('notif')->where("receiver",$user_id)->where("received",0)->get();
        $num_rows = $query->count();
        if($num_rows > 0){
            return "(" . $num_rows . ")";
        }else{
            return "";
        }
    }

    public function getNewMessagesCount($user_id){
        
        $query = DB::table('messages')->where('receiver',$user_id)->where('received',0)->get();
        if($query->count() > 0){
            $rows = array();
            foreach($query as $row){
                $sender = $row->sender;
                $rows[] .= $sender;
            }
            
            $rows = array_unique($rows);
            $rows = count($rows);
        }else{
            $rows = 0;
        }
        return $rows;
    }

    public function getNoOfAccountsOwnedByUser($user_id){
        
        $query = DB::table('mlm_db')->where('user_id', $user_id)->get();
        return $query->count();
    }


    public function changeUserPassword($user_id,$token,$new_password){
        $hashed = sha1($new_password);
        
        try{
            DB::table('users')->where('id', $user_id)->update(array('hashed' => $hashed,'token' => $token));
            return true;
        }catch(Exception $e){
            return false;
        }
         
    }

    public function getUserPhoneCodeByUserId($user_id){
        
        $query = DB::table('users')->where('id', $user_id)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $phone_code = $row->phone_code;
            }
            return $phone_code;
        }else{
            return false;
        }
    }

    public function getUserPhoneNumberByUserId($user_id){
        
        $query = DB::table('users')->where('id', $user_id)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $phone = $row->phone;
            }
            return $phone;
        }else{
            return false;
        }
    }


    public function getUserFullNameById($user_id){
        
        $query = DB::table('users')->where('id', $user_id)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $full_name = $row->full_name;
            }
            // echo $user_name;
            return $full_name;
        }else{
            return false;
        }
    }


    public function getFullMobileNoByUserName($user_name){
        
        $query = DB::table('users')->where('user_name', $user_name)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $phone_code = $row->phone_code;
                $phone = $row->phone;
            }
            $full_num = "+" . $phone_code . "" . $phone;
        }
        if(isset($full_num)){
            return $full_num;
        }else{
            return "";
        }
    }

    //Check If User Exists
    public function userExists($user_name){
        
        $query = DB::table('users')->where('user_name', $user_name)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    //Check If User Exists Phone
    public function userExistsPhone($phone){
        
        $query = DB::table('users')->get();
        $ret = false;
        if($query->count() > 0){
            foreach($query as $row){
                $user_phone = "+" . $row->phone_code . "" .$row->phone;
                // echo $user_phone;
                if($user_phone == $phone){
                    $ret = true;
                    break;
                }
            }
        }else{
            return false;
        }
        return $ret;
    }

    public function getUserNameByFullPhone($phone){
        
        $query = DB::table('users')->get();
        $ret = false;
        if($query->count() > 0){
            foreach($query as $row){
                $user_name = $row->user_name;
                $user_phone = "+" . $row->phone_code . "" .$row->phone;
                // echo $user_phone;
                if($user_phone == $phone){
                    $ret = $user_name;
                    break;
                }
            }
        }else{
            return false;
        }
        return $ret;
    }

    //Verify Password
    public function password_verify($user_name,$hashed){
        
        $query = DB::table('users')->where('user_name', $user_name)->where('hashed', $hashed)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function checkIfUserExists($user_name,$user_id){
        
        $query = DB::table('users')->where('user_name', $user_name)->where('id', $user_id)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function performMlmRegistrationForFirstTimeUsersWithOutPlacement($user_id,$sponsor_user_name,$date,$time,$package = NULL){

        $sponsor_id = $this->getUserIdByName($sponsor_user_name);
        $sponsor_id = $this->getUsersFirstMlmDbId($sponsor_id);
        
        if(is_null($package) || $package == 1){
            $package = 1;
            $package_str = "Basic";
        }else if($package == 2){
            $package = 2;
            $package_str = "Business";
        }
        
        if($this->checkIfMlmDbIdIsValid($sponsor_id) ){
            
            
            if($this->registerUserInMlm3($user_id,$sponsor_id,$date,$time,$package,0)){
                return true;
            }
        }
            
    }

    public function getAvailablePositionUnderMlmDbId($mlm_db_id){
        if($this->checkIfMlmDbIdIsValid($mlm_db_id)){
            
            $query = DB::table('mlm_db')->where('under', $mlm_db_id)->get();
            if($query->count() == 0){
                return "both";
            }else if($query->count() == 1){
                $taken_id = $this->getChildrenOfParent($mlm_db_id)[0]->id;
                $taken_position = $this->getMlmDbParamById("positioning",$taken_id);
                if($taken_position == "left"){
                    return "right";
                }else{
                    return "left";
                }
            }else{
                return false;
            }
        }   
    }

    public function getChildrenOfParent($mlm_db_id){
       
        $query = DB::table('mlm_db')->where("under",$mlm_db_id)->orderBy("id","ASC")->get();
        if($query->count() > 0){
            return $query;
        }else{
            return false;
        }
    }

    public function checkIfMlmDbIdHasNoAvailablePositionUnderHim($mlm_db_id){
        
        $query = DB::table('mlm_db')->where('under', $mlm_db_id)->get();
        if($query->count() == 2){
            return true;
        }else{
            return false;
        }
    }


    public function getAllUsersMlmDbIds($user_id){
        $ret_arr = array();
        // $query = $this->db->get_where("mlm_db",array('user_id' => $user_id));
        // $this->db->select("id");
        // $this->db->from("mlm_db");
        // $this->db->where("user_id",$user_id);
        // $this->db->order_by("id","ASC");
        // $query = $this->db->get();
        $query = DB::table('mlm_db')->where("user_id",$user_id)->orderBy("id","ASC")->get("id");
        if($query->count() > 0){
            foreach($query as $row){
                $id = $row->id;
                $ret_arr[] = $id;
            }
        }
        return $ret_arr;
    }

    public function performMlmRegistrationForFirstTimeUsersWithPlacement($user_id,$sponsor_user_name,$placement_mlm_db_id,$placement_position,$date,$time,$package = NULL){

        $sponsor_id = $this->getUserIdByName($sponsor_user_name);
        $sponsor_id = $this->getUsersFirstMlmDbId($sponsor_id);
        
        if(is_null($package) || $package == 1){
            $package = 1;
            $package_str = "Basic";
        }else if($package == 2){
            $package = 2;
            $package_str = "Business";
        }
        
        if($placement_position == "left" || $placement_position == "right"){
            if($this->checkIfMlmDbIdIsValid($sponsor_id) && $this->checkIfMlmDbIdIsValid($placement_mlm_db_id)){
                
                if($placement_position == "left"){
                    $next_available_position = "right";
                }else{
                    $next_available_position = "left";
                }

                

                if($this->checkIfThisPlacementPositionIsAvailable($placement_mlm_db_id,$placement_position)){
                    // echo "string";
                
                    if($this->registerUserInMlm2($user_id,$sponsor_id,$placement_mlm_db_id,$placement_position,$date,$time,$package,0)){
                        
                        return true;
                    }
                }else if($this->checkIfThisPlacementPositionIsAvailable($placement_mlm_db_id,$next_available_position)){
                    if($this->registerUserInMlm2($user_id,$sponsor_id,$placement_mlm_db_id,$next_available_position,$date,$time,$package,0)){
                        // echo "string";
                        return true;
                    }
                }else{
                    if($this->registerUserInMlm3($user_id,$sponsor_id,$date,$time,$package,0)){
                        return true;
                    }
                }
                
            }
        }
            
    }

    public function registerUserInMlm3($user_id,$sponsor_id,$date,$time,$package,$type,$reference = NULL){
        if($this->fixUserInNextAvailableSpaceForMlm($package,1,$sponsor_id,$user_id,$date,$time,$reference)){
            if($package == 1){
                $sponsor_income = $this->getSponsorChargeForBasicPackage();
                $sponsor_income_vat = $this->getSponsorVatChargeForBasicPackage();
                $sponsor_income_vat_perc = $sponsor_income_vat / 100;
                $sponsor_income_vat_val = ($sponsor_income_vat_perc * $sponsor_income);
                $real_sponsor_income = $sponsor_income - ($sponsor_income_vat_perc * $sponsor_income);
                $sponsor_user_id = $this->getMlmDbParamById("user_id",$sponsor_id);
                $this->creditUserSponsorIncome($user_id,$sponsor_user_id,$sponsor_income,$sponsor_income_vat,$real_sponsor_income,$sponsor_id,1,$date,$time,$sponsor_income_vat_val);

                return true;
                
            }else if($package == 2){
                $sponsor_income = $this->getSponsorChargeForBusinessPackage();
                $sponsor_income_vat = $this->getSponsorVatChargeForBusinessPackage();
                $sponsor_income_vat_perc = $sponsor_income_vat / 100;
                $sponsor_income_vat_val = ($sponsor_income_vat_perc * $sponsor_income);
                $real_sponsor_income = $sponsor_income - ($sponsor_income_vat_perc * $sponsor_income);
                $sponsor_user_id = $this->getMlmDbParamById("user_id",$sponsor_id);
                $this->creditUserSponsorIncome($user_id,$sponsor_user_id,$sponsor_income,$sponsor_income_vat,$real_sponsor_income,$sponsor_id,3,$date,$time,$sponsor_income_vat_val);


                return true;
            }
        }
    }

    public function registerUserInMlm2($user_id,$sponsor_id,$placement_id,$positioning,$date,$time,$package,$type,$reference = NULL){
        if($this->fixUserInPositionMlm($package,$type,$sponsor_id,$placement_id,$positioning,$user_id,$date,$time,$reference)){
            if($package == 1){
                $sponsor_income = $this->getSponsorChargeForBasicPackage();
                $sponsor_income_vat = $this->getSponsorVatChargeForBasicPackage();
                $sponsor_income_vat_perc = $sponsor_income_vat / 100;
                $sponsor_income_vat_val = ($sponsor_income_vat_perc * $sponsor_income);
                $real_sponsor_income = $sponsor_income - ($sponsor_income_vat_perc * $sponsor_income);
                $sponsor_user_id = $this->getMlmDbParamById("user_id",$sponsor_id);
                $this->creditUserSponsorIncome($user_id,$sponsor_user_id,$sponsor_income,$sponsor_income_vat,$real_sponsor_income,$sponsor_id,1,$date,$time,$sponsor_income_vat_val);

                return true;
                
            }else if($package == 2){
                $sponsor_income = $this->getSponsorChargeForBusinessPackage();
                $sponsor_income_vat = $this->getSponsorVatChargeForBusinessPackage();
                $sponsor_income_vat_perc = $sponsor_income_vat / 100;
                $sponsor_income_vat_val = ($sponsor_income_vat_perc * $sponsor_income);
                $real_sponsor_income = $sponsor_income - ($sponsor_income_vat_perc * $sponsor_income);
                $sponsor_user_id = $this->getMlmDbParamById("user_id",$sponsor_id);
                $this->creditUserSponsorIncome($user_id,$sponsor_user_id,$sponsor_income,$sponsor_income_vat,$real_sponsor_income,$sponsor_id,3,$date,$time,$sponsor_income_vat_val);


                return true;
            }
        }
    }

    public function creditUserSponsorIncome($user_id,$sponsor_id,$sponsor_income,$sponsor_income_vat,$real_sponsor_income,$mlm_db_id,$charge_type,$date,$time,$sponsor_income_vat_val){
        
        if(DB::table('mlm_earnings')->insert(array('user_id' => $sponsor_id,'mlm_db_id' => $mlm_db_id,'charge_type' => $charge_type,'amount' => $sponsor_income,'vat' => $sponsor_income_vat,'date' => $date,'time' => $time))){
            $total_vat = $this->getUserParamById("admin_vat_total",$this->getAdminId());
            $new_vat_total = $total_vat + $sponsor_income_vat_val;
            $form_array = array(
                'admin_vat_total' => $new_vat_total
            );

            if($this->updateUserTable($form_array,$this->getAdminId())){
                $form_array = array();

                if($charge_type == 1){
                    $admin_sponsor_bonus = $this->getAdminSponsorChargeForBasicPackage();
                    // echo $admin_sponsor_bonus;
                    
                    if(DB::table('mlm_earnings')->insert(array('user_id' => $this->getAdminId(),'mlm_db_id' => 1,'charge_type' => 5,'amount' => $admin_sponsor_bonus,'vat' => 0,'date' => $date,'time' => $time))){
                        $total_basic_income = $this->getUserParamById("total_basic_income",$this->getAdminId());
                        $new_total_basic_income = $total_basic_income + $admin_sponsor_bonus;

                        $form_array = array(
                            'total_basic_income' => $new_total_basic_income
                        );
                        if($this->updateUserTable($form_array,$this->getAdminId())){

                        }
                    }

                    $total_basic_income = $this->getUserParamById("total_basic_income",$sponsor_id);
                    $new_total_basic_income = $total_basic_income + $real_sponsor_income;

                    $form_array = array(
                        'total_basic_income' => $new_total_basic_income
                    );
                    $this->updateUsersBusinessIncomeForTheMonth($sponsor_id,$real_sponsor_income);

                }else if($charge_type == 3){
                    $admin_sponsor_bonus = $this->getAdminSponsorChargeForBusinessPackage();
                    // echo $admin_sponsor_bonus;
                    if(DB::table('mlm_earnings')->insert(array('user_id' => $this->getAdminId(),'mlm_db_id' => 1,'charge_type' => 6,'amount' => $admin_sponsor_bonus,'vat' => 0,'date' => $date,'time' => $time))){


                        $total_business_income = $this->getUserParamById("total_business_income",$this->getAdminId());
                        $new_total_business_income = $total_business_income + $admin_sponsor_bonus;

                        $form_array = array(
                            'total_business_income' => $new_total_business_income
                        );
                        if($this->updateUserTable($form_array,$this->getAdminId())){

                        }

                    }

                    $this->updateUsersBusinessIncomeForTheMonth($sponsor_id,$real_sponsor_income);

                    $total_business_income = $this->getUserParamById("total_business_income",$sponsor_id);
                    $new_total_business_income = $total_business_income + $real_sponsor_income;

                    $form_array = array(
                        'total_business_income' => $new_total_business_income
                    );
                }

                if($this->updateUserTable($form_array,$sponsor_id)){

                }

                $sponsored_business_partner_id = $user_id;
                $sponsored_business_partner_username = $this->getUserNameById($sponsored_business_partner_id);
                $sponsored_business_partner_slug = $this->getUserParamById("slug",$sponsored_business_partner_id);
                $sponsored_business_partner_full_name = $this->getUserParamById("full_name",$sponsored_business_partner_id);
                $sponsored_business_partner_phone_code = $this->getUserParamById("phone_code",$sponsored_business_partner_id);
                $sponsored_business_partner_phone_num = $this->getUserParamById("phone",$sponsored_business_partner_id);
                $sponsored_business_partner_phone_num =  "+". $sponsored_business_partner_phone_code . "" . $sponsored_business_partner_phone_num;

                $title = "Credit Alert";
                $message = "This Is To Alert You That You Were Credited With Sponsor Income. View Details Below.";
                $message .= "<div class='container' style='margin-top: 30px;'>";
                $message .= "<p>Sponsor Income Amount: <em class='text-primary'>".number_format($sponsor_income,2)."</em><p>";
                $message .= "<p>Sponsor Income Vat: <em class='text-primary'>".number_format($sponsor_income_vat,2)."%</em><p>";
                $message .= "<p>Withdrawable Sponsor Balance: <em class='text-primary'>".number_format($real_sponsor_income,2)."</em><p>";

                if($charge_type == 1){
                    $message .= "<h4 class='text-center' style='margin-top: 20px;'>Newly Sponsored Basic Partner Details<h4>";
                }else if($charge_type == 3){
                    $message .= "<h4 class='text-center' style='margin-top: 20px;'>Newly Sponsored Business Partner Details<h4>";
                }

                $message .= "<p>Username: <a target='_blank' href='/user/".$sponsored_business_partner_slug."'>".$sponsored_business_partner_username."</a><p>";

                $message .= "<p>Full Name: <em class='text-primary'>".$sponsored_business_partner_full_name."</em><p>";
                // $message .= "<p>Phone Number: <em class='text-primary'>".$sponsored_business_partner_phone_num."</em><p>";

                $message .= "</div>";
                

                $form_array = array(
                    'sender' => "System",
                    'receiver' => $sponsor_id,
                    'title' => $title,
                    'message' => $message,
                    'date_sent' => $date,
                    'time_sent' => $time,
                    'type' => 'misc'
                );

                $history_array = array(
                    'user_id' => $sponsor_id,
                    'income_type' => 'sponsor',
                    'creditors_id' => $sponsored_business_partner_id,
                    'amount' => $sponsor_income,
                    'vat' => $sponsor_income_vat,
                    'date' => $date,
                    'time' => $time
                );

                if($charge_type == 1){
                    $history_array['package'] = 1;
                }elseif($charge_type == 3){
                    $history_array['package'] = 2;
                }


                if($this->sendMessage($form_array) && $this->addMlmIncomeHistory($history_array)){
                    return true;
                }
            }
        }
    }

    public function getAdminSponsorChargeForBusinessPackage(){
        
        $query = DB::table('mlm_charges')->where('id', 6)->get();
        if($query->count() == 1){
            return $query[0]->amount;
        }
    }


    public function getAdminSponsorChargeForBasicPackage(){
        
        $query = DB::table('mlm_charges')->where('id', 5)->get();
        if($query->count() == 1){
            return $query[0]->amount;
        }
    }

    public function getSponsorVatChargeForBusinessPackage(){
        
        $query = DB::table('mlm_charges')->where('id', 3)->get();
        if($query->count() == 1){
            return $query[0]->vat;
        }
    }


    public function getSponsorChargeForBusinessPackage(){
        
        $query = DB::table('mlm_charges')->where('id', 3)->get();
        if($query->count() == 1){
            return $query[0]->amount;
        }
    }

    public function getSponsorVatChargeForBasicPackage(){
        
        $query = DB::table('mlm_charges')->where('id', 1)->get();
        if($query->count() == 1){
            return $query[0]->vat;
        }
    }

    public function getSponsorChargeForBasicPackage(){
        
        $query = DB::table('mlm_charges')->where('id', 1)->get();
        if($query->count() == 1){
            return $query[0]->amount;
        }
    }

    public function fixUserInPositionMlm($package,$type,$sponsor_id,$placement_id,$positioning,$user_id,$date,$time,$reference = NULL){
        if($this->checkIfThisPlacementPositionIsAvailable($placement_id,$positioning)){
            $placement_stage =  $this->getMlmDbParamById("stage",$placement_id);
            $new_stage = $placement_stage + 1;
            $form_array = array(
                'user_id' => $user_id,
                'package' => $package,
                'sponsor' => $sponsor_id,
                'under' => $placement_id,
                'stage' => $new_stage,
                'positioning' => $positioning,
                'date_created' => $date,
                'time_created' => $time,
                'reference' => $reference
            );
            
            $mlm_db_id = DB::table('mlm_db')->insertGetId($form_array);
            
            if($package == 1){
                $placement_income = $this->getPlacementChargeForBasicPackage();
                $placement_income_vat = $this->getPlacementVatChargeForBasicPackage();
                $charge_type = 2;
            }else{
                $placement_income = $this->getPlacementChargeForBusinessPackage();
                $placement_income_vat = $this->getPlacementVatChargeForBusinessPackage();
                $charge_type = 4;
            }
            $placement_income_vat_perc = $placement_income_vat / 100;
            $placement_income_vat_val = ($placement_income_vat_perc * $placement_income);
            $real_placement_income = $placement_income - ($placement_income_vat_perc * $placement_income);
            
            $this->creditUserPlacementIncome($mlm_db_id,$placement_income,$placement_income_vat,$real_placement_income,$charge_type,$date,$time,$placement_income_vat_val);
            return true;
            
        }else{
            if($this->fixUserInNextAvailableSpaceForMlm($package,1,$sponsor_id,$user_id,$date,$time,$reference)){
                return true;
            }
        }   
    }

    public function fixUserInNextAvailableSpaceForMlm($package,$type,$sponsor_id,$user_id,$date,$time,$reference = NULL){
        //If Type Is Next Available space
        
        // echo $sponsor_id;
        //Get Sponsors First Mlm Db Id
        if($type == 0){
            $sponsors_first_mlm_db_id = $this->getUsersFirstMlmDbId($sponsor_id);
        }else{
            $sponsors_first_mlm_db_id = $sponsor_id;
        }
        // echo $sponsors_first_mlm_db_id . "<br>";
        //Get The Stage Here
        $sponsors_first_mlm_db_stage = $this->getMlmDbParamById("stage",$sponsors_first_mlm_db_id);
        // echo $sponsors_first_mlm_db_stage;
        // echo $sponsors_first_mlm_db_stage;
        //Get The First Generation Under Sponsor Thats Empty
        
        $query = DB::table('mlm_db')->where('under', $sponsors_first_mlm_db_id)->get();
        $number_under_him = $query->count();
        //If First Level Under Him Is Full
        if($number_under_him == 2){
            $i = 1;
            $current_array = array();
            while (true) {
                $i++;
                // echo $i;

                $previous_stage = $i - 1;
                $stage_to_check_for = $sponsors_first_mlm_db_stage + $previous_stage;
                $current_stage = $sponsors_first_mlm_db_stage + $i;


                
                if($i == 2){
                    $parents_ids = $this->getChildrenIdsOfParent($sponsors_first_mlm_db_id);
                    // print_r($parents_ids);
                }else{
                    $parents_ids = $this->getIdsOfChildren($current_array);
                }
                // var_dump($parents_ids);
                if(is_array($parents_ids)){
                    $current_array = $parents_ids;
                    for($j = 0; $j < count($parents_ids); $j++){
                        $parent_id = $parents_ids[$j];
                        if(!$this->checkIfThisUserHasHisNextLevelFull($parent_id)){
                            
                            $parents_children_num = $this->getNumberOfImmediateChildrenOfThisUser($parent_id);
                            // echo $parents_children_num;
                            if($parents_children_num == 0){
                                $positioning = "left";
                            }else if($parents_children_num == 1){
                                $other_users_positioning = $this->getPositioningOfImmediateChildOfThisUser($parent_id);
                                if($other_users_positioning == "right"){
                                    $positioning = "left";
                                }else{
                                    $positioning = "right";
                                }
                            }
                            $form_array = array(
                                'user_id' => $user_id,
                                'package' => $package,
                                'sponsor' => $sponsors_first_mlm_db_id,
                                'under' => $parent_id,
                                'stage' => $current_stage,
                                'positioning' => $positioning,
                                'date_created' => $date,
                                'time_created' => $time,
                                'reference' => $reference
                            );
                            
                            $mlm_db_id = DB::table('mlm_db')->insertGetId($form_array);
                            
                                
                                
                            if($package == 1){
                                $placement_income = $this->getPlacementChargeForBasicPackage();
                                $placement_income_vat = $this->getPlacementVatChargeForBasicPackage();
                                $charge_type = 2;
                            }else{
                                $placement_income = $this->getPlacementChargeForBusinessPackage();
                                $placement_income_vat = $this->getPlacementVatChargeForBusinessPackage();
                                $charge_type = 4;
                            }
                            $placement_income_vat_perc = $placement_income_vat / 100;
                            $placement_income_vat_val = ($placement_income_vat_perc * $placement_income);
                            $real_placement_income = $placement_income - ($placement_income_vat_perc * $placement_income);
                            
                            $this->creditUserPlacementIncome($mlm_db_id,$placement_income,$placement_income_vat,$real_placement_income,$charge_type,$date,$time,$placement_income_vat_val);
                            return true;
                            
                            break 2;
                        }
                    }
                }
                
            }
        }else{ //If Not
            $new_stage = $sponsors_first_mlm_db_stage + 1;
            if($number_under_him == 1){
                $other_users_positioning = $this->getPositioningOfMlmUserDirect($new_stage,$sponsors_first_mlm_db_id);
                if($other_users_positioning == "right"){
                    $positioning = "left";
                }else{
                    $positioning = "right";
                }
            }else{
                $positioning = "left";
            }
            
            $form_array = array(
                'user_id' => $user_id,
                'package' => $package,
                'sponsor' => $sponsors_first_mlm_db_id,
                'under' => $sponsors_first_mlm_db_id,
                'stage' => $new_stage,
                'positioning' => $positioning,
                'date_created' => $date,
                'time_created' => $time,
                'reference' => $reference
            );
        
            $mlm_db_id = DB::table('mlm_db')->insertGetId($form_array);
                                

            if($package == 1){
                $placement_income = $this->getPlacementChargeForBasicPackage();
                $placement_income_vat = $this->getPlacementVatChargeForBasicPackage();
                $charge_type = 2;
            }else{
                $placement_income = $this->getPlacementChargeForBusinessPackage();
                $placement_income_vat = $this->getPlacementVatChargeForBusinessPackage();
                $charge_type = 4;
            }
            $placement_income_vat_perc = $placement_income_vat / 100;
            $placement_income_vat_val = ($placement_income_vat_perc * $placement_income);
            $real_placement_income = $placement_income - ($placement_income_vat_perc * $placement_income);
            
            
            $this->creditUserPlacementIncome($mlm_db_id,$placement_income,$placement_income_vat,$real_placement_income,$charge_type,$date,$time,$placement_income_vat_val);
            return true;
        }

        
    }

    public function getPositioningOfMlmUserDirect($stage,$sponsor_id){
        
        $query = DB::table('mlm_db')->where('stage', $stage)->where('under', $sponsor_id)->get();
        if($query->count() == 1){
            $query[0]->positioning;
        }else{
            return false;
        }
    }

    public function getNumberOfImmediateChildrenOfThisUser($parent_id){
        
        $query = DB::table('mlm_db')->where('under',$parent_id)->get();
        return $query->count();
    }

    public function getPositioningOfImmediateChildOfThisUser($parent_id){
        $query = DB::table('mlm_db')->where('under',$parent_id)->get();
        if($query->count() == 1){
            return $query[0]->positioning;
        }
    }

    public function checkIfThisUserHasHisNextLevelFull($parent_id){
        // $query = $this->db->get_where('mlm_db',array('under' => $parent_id));
        // $this->db->select("id");
        // $this->db->from("mlm_db");
        // $this->db->where("under",$parent_id);
        // $this->db->order_by("id","ASC");
        // $query = $this->db->get();
        $query = DB::table('mlm_db')->where("under",$parent_id)->orderBy("id","ASC")->get("id");
        if($query->count() >= 2){
            return true;
        }else{
            return false;
        }
    }

    public function getIdsOfChildren($current_array){
        $ret_arr = array();
        if(is_array($current_array)){
            for($i = 0; $i < count($current_array); $i++){
                $id = $current_array[$i];
                
                $query = DB::table('mlm_db')->where('under', $id)->get();
                if($query->count() > 0){
                    foreach($query as $row){
                        $id1 = $row->id;
                        $ret_arr[] = $id1;
                    }
                }
            }
        }
        return $ret_arr;
    }

    public function getChildrenIdsOfParent($sponsors_first_mlm_db_id){
        $ret_arr = array();
        // $query = $this->db->get_where('mlm_db',array('under' => $sponsors_first_mlm_db_id));
        // $this->db->select("id");
        // $this->db->from("mlm_db");
        // $this->db->where("under",$sponsors_first_mlm_db_id);
        // $this->db->order_by("id","ASC");
        // $query = $this->db->get();
        $query = DB::table('mlm_db')->where("under",$sponsors_first_mlm_db_id)->orderBy("id","ASC")->get('id');
        if($query->count() > 0){
            foreach($query as $row){
                $id = $row->id;
                $ret_arr[] = $id;
            }
        }
        return $ret_arr;
    }

    public function testSmt(){
        // return DB::table('users')->where('email', 'ikechukwunwogo@gmail.com')->get('user_name');
        $ret_arr = array();
        $current_month_year = strtotime("Sept 2021");
        
        for($i = 0; $i <= 193; $i++){

            $next_month = date("M Y", strtotime("+1 month", $current_month_year));
            $current_month_year = strtotime($next_month);
            $ret_arr[] = $next_month;
            // DB::table('admin_vtu_earnings')->insert(array('month_year' => $next_month));
        }
        return $ret_arr;
    }

    public function creditUserPlacementIncome($mlm_db_id,$placement_income,$placement_income_vat,$real_placement_income,$charge_type,$date,$time,$placement_income_vat_val){

        $creditors_user_id = $this->getMlmDbParamById("user_id",$mlm_db_id);
        $ids_to_credit = $this->getIdsToCreditPlacement($mlm_db_id);
        $ids_to_credit_num = count($ids_to_credit);
        for($i = 0; $i < count($ids_to_credit); $i++){
            $user_id = $ids_to_credit[$i]['user_id'];
            $placements_mlm_db_id = $ids_to_credit[$i]['mlm_db_id'];

            

            if(DB::table('mlm_earnings')->insert(array('user_id' => $user_id,'mlm_db_id' => $placements_mlm_db_id,'charge_type' => $charge_type,'amount' => $placement_income,'vat' => $placement_income_vat,'date' => $date,'time' => $time))){


                $total_vat = $this->getUserParamById("admin_vat_total",$this->getAdminId());
                $new_vat_total = $total_vat + $placement_income_vat_val;
                $form_array = array(
                    'admin_vat_total' => $new_vat_total
                );

                if($this->updateUserTable($form_array,$this->getAdminId())){
                    $form_array = array();

                    if($charge_type == 2){
                        $this->updateUsersBusinessIncomeForTheMonth($user_id,$real_placement_income);
                        $total_basic_income = $this->getUserParamById("total_basic_income",$user_id);
                        $new_total_basic_income = $total_basic_income + $real_placement_income;

                        $form_array = array(
                            'total_basic_income' => $new_total_basic_income
                        );
                    }else if($charge_type == 4){
                        $this->updateUsersBusinessIncomeForTheMonth($user_id,$real_placement_income);

                        $total_business_income = $this->getUserParamById("total_business_income",$user_id);
                        $new_total_business_income = $total_business_income + $real_placement_income;

                        $form_array = array(
                            'total_business_income' => $new_total_business_income
                        );
                    }

                    if($this->updateUserTable($form_array,$user_id)){

                    }

                    $sponsored_business_partner_id = $creditors_user_id;
                    $sponsored_business_partner_username = $this->getUserNameById($sponsored_business_partner_id);
                    $sponsored_business_partner_slug = $this->getUserParamById("slug",$sponsored_business_partner_id);
                    $sponsored_business_partner_full_name = $this->getUserParamById("full_name",$sponsored_business_partner_id);
                    $sponsored_business_partner_phone_code = $this->getUserParamById("phone_code",$sponsored_business_partner_id);
                    $sponsored_business_partner_phone_num = $this->getUserParamById("phone",$sponsored_business_partner_id);
                    $sponsored_business_partner_phone_num =  "+". $sponsored_business_partner_phone_code . "" . $sponsored_business_partner_phone_num;

                    $title = "Credit Alert";
                    $message = "This Is To Alert You That You Were Credited With Placement Income. View Details Below.";
                    $message .= "<div class='container' style='margin-top: 30px;'>";
                    $message .= "<p>Placement Income Amount: <em class='text-primary'>".number_format($placement_income,2)."</em><p>";
                    $message .= "<p>Placement Income Vat: <em class='text-primary'>".number_format($placement_income_vat,2)."%</em><p>";
                    // $message .= "<p>Withdrawable Placement Balance: <em class='text-primary'>".number_format($real_placement_income,2)."</em><p>";

                    if($charge_type == 2){
                        $message .= "<h4 class='text-center' style='margin-top: 20px;'>Newly Placement Basic Partner Details<h4>";
                    }else if($charge_type == 4){
                        $message .= "<h4 class='text-center' style='margin-top: 20px;'>Newly Placement Business Partner Details<h4>";
                    }

                    $message .= "<p>Username: <a target='_blank' href='/user/".$sponsored_business_partner_slug."'>".$sponsored_business_partner_username."</a><p>";

                    $message .= "<p>Full Name: <em class='text-primary'>".$sponsored_business_partner_full_name."</em><p>";
                    // $message .= "<p>Phone Number: <em class='text-primary'>".$sponsored_business_partner_phone_num."</em><p>";

                    $message .= "</div>";
                    

                    $form_array = array(
                        'sender' => "System",
                        'receiver' => $user_id,
                        'title' => $title,
                        'message' => $message,
                        'date_sent' => $date,
                        'time_sent' => $time,
                        'type' => 'misc'
                    );

                    $history_array = array(
                        'user_id' => $user_id,
                        'income_type' => 'placement',
                        'creditors_id' => $sponsored_business_partner_id,
                        'amount' => $placement_income,
                        'vat' => $placement_income_vat,
                        'date' => $date,
                        'time' => $time
                    );

                    if($charge_type == 2){
                        $history_array['package'] = 1;
                    }elseif($charge_type == 4){
                        $history_array['package'] = 2;
                    }

                    if(($this->sendMessage($form_array) && $this->addMlmIncomeHistory($history_array)) && $i == ($ids_to_credit_num - 1)){
                        return true;
                    }
                }
            }
        }
    }

    public function addMlmIncomeHistory($history_array){
        return DB::table('mlm_income_history')->insert($history_array);
    }

    public function sendMessage($form_array){
        return DB::table('notif')->insert($form_array);
    }

    public function updateUsersBusinessIncomeForTheMonth($user_id,$amount){
        $year = date("Y");
        $month = date("M");

        $month_year = $month . " " . $year;
        $month_amount = $this->getUserParamById(strtolower($month . "_earnings"),$user_id);
        $new_month_amount = $month_amount += $amount;
        try{
            DB::table('users')->where('id', $user_id)->update(array(strtolower($month . "_earnings") => $new_month_amount));
            return true;
        }catch(Exception $e){
            return false;
        }

    }

    public function updateUserTable($user_array,$id){
        try{
            DB::table('users')->where('id', $id)->update($user_array);
            return true;
        }catch(Exception $e){
            return false;
        }
        
    }

    public function getIdsToCreditPlacement($mlm_db_id){
        $ret_arr = array();
        for($i = 1; $i <= 16; $i++){
            
            $query = DB::table('mlm_db')->where('id', $mlm_db_id)->get();
            if($query->count() == 1){
                foreach($query as $row){
                    $under = $row->under;
                    if(!is_null($under)){
                        $user_id = $this->getMlmDbParamById("user_id",$under);
                        // $this->getIdsToCreditPlacement($under);
                        $mlm_db_id = $under;
                        $ret_arr[] = array(
                            'mlm_db_id' => $under,
                            'user_id' => $user_id
                        );
                        
                    }else{
                        $ret_arr[] =  array(
                            'mlm_db_id' => 1,
                            'user_id' => $this->getAdminId()
                        );
                    }
                }
            }

        }
        return $ret_arr;
    }

    public function getAdminId(){
        
        $query = DB::table('users')->where('is_admin', 1)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $id = $row->id;
            }
            return $id;
        }
    }

    public function getPlacementVatChargeForBusinessPackage(){
        
        $query = DB::table('mlm_charges')->where('id', 4)->get();
        if($query->count() == 1){
            return $query[0]->vat;
        }
    }

    public function getPlacementChargeForBusinessPackage(){
        
        $query = DB::table('mlm_charges')->where('id', 4)->get();
        if($query->count() == 1){
            return $query[0]->amount;
        }
    }

    public function getPlacementVatChargeForBasicPackage(){
        
        $query = DB::table('mlm_charges')->where('id', 2)->get();
        if($query->count() == 1){
            return $query[0]->vat;
        }
    }

    public function getPlacementChargeForBasicPackage(){
        
        $query = DB::table('mlm_charges')->where('id', 2)->get();
        if($query->count() == 1){
            return $query[0]->amount;
        }
    }

    public function getMlmDbParamById($param,$mlm_db_id){
        
        $query = DB::table('mlm_db')->where('id', $mlm_db_id)->select($param)->get();
        if($query->count() == 1){
            return $query[0]->$param;
        }
    }


    public function checkIfThisPlacementPositionIsAvailable($placement_id,$positioning){
        $query = DB::table('mlm_db')->where('under', $placement_id)->where('positioning', $positioning)->get();
        if($query->count() == 1){
            return false;
        }else{
            return true;
        }
    }

    public function getUsersFirstMlmDbId($user_id){
        $query = DB::table('mlm_db')->where("user_id",$user_id)->orderBy("id","ASC")->limit(1)->get();
        if($query->count() == 1){
            return $query[0]->id;
        }
    }

    public function checkIfUserAlreadyHasAnMlmAccountInTheSystem($user_id){
        
        $query = DB::table('mlm_db')->where('user_id', $user_id)->get();
        if($query->count() > 0){
            return true;
        }else{
            return false;
        }
    }
    public function checkIfMlmDbIdIsValid($mlm_db_id){
        
        $query = DB::table('mlm_db')->where('id', $mlm_db_id)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function getUserNameById($slug){
        $query = DB::table('users')->where('id', $slug)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $user_name = $row->user_name;
            }
            // echo $user_name;
            return $user_name;
        }else{
            return "";
        }
    }

    public function addPaymentProofRequest($form_array){
        return DB::table('credit_account_payment_proofs')->insert($form_array);
    }

    public function checkIfUserHasHalfRegisteredBySlug($slug){
        
        $query = DB::table('users')->where('slug', $slug)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $created = $row->created;
                // var_dump($created);
                if($created == '0'){
                    return true;
                }else{
                    return false;
                }
            }
        }
    }


    public function getUserInfoByUserId($user_id){
        $query = DB::table('users')->where('id', $user_id)->get();
        if($query->count() == 1){
            return $query;
        }else{
            return false;
        }
    }

    public function getUserNameBySlug($slug){
        $query = DB::table('users')->where('slug', $slug)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $user_name = $row->user_name;
            }
            // echo $user_name;
            return $user_name;
        }else{
            return false;
        }
    }

    public function getUserIdByToken($token){
        
        $query = DB::table('users')->where('token', $token)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $id = $row->id;
            }
            return $id;
        }
    }

    public function createUser($user_array){
        return DB::table('users')->insert($user_array);
    }

    public function getCountryIdByCountryCode($country_code){
        
        $query = DB::table('countries')->where('code', $country_code)->get();
        if($query->count() > 0){
            foreach($query as $row){
                $country_id = $row->id;
            }
            return $country_id;
        }else{
            return 0;
        }
    }

     public function phone_unique($phone_code,$phone){
        
        $query = DB::table('users')->where('phone',$phone)->where('phone_code', $phone_code)->get();
        if($query->count() > 0){
            return false;
        }else{
            return true;
        }
    }

    public function sendEmail($recepient_arr,$subject,$message,$otp = FALSE){
        if($otp){


            //Then Filter It To Those Who Have Email Notifications Enabled
            // $recepient_arr = $this->filterSubEmails($recepient_arr);
            if(count($recepient_arr) > 0){

                if($message !== ""){
                    $message = "<h3 style='text-transform:capitalize;'>" . $message . "</h3>";
                    $year = date("Y");
                    $message .= "<h5><a href='meetglobalresources.com'>MeetGlobal Resources</a> &copy; " . $year . ". All Rights Reserved</h5>";
                }
                if($_SERVER['SERVER_NAME'] !== "127.0.0.1"){

                    if(is_array($recepient_arr)){

                        require base_path('vendor/autoload.php'); ; // load Composer's autoloader

                        $mail = new PHPMailer(true); // Passing `true` enables exceptions


                        // SMTP configuration
                        $mail->isSMTP();
    
                        // SMTP configuration
                        

                        // $mail->Host     = 'smtp.gmail.com';
                        // $mail->SMTPAuth = true;
                        // $mail->Username = 'easybizcoop@gmail.com';
                        // $mail->Password = 'Thegoodnews@2@';
                        // $mail->SMTPSecure = 'tsl';
                        // $mail->Port     = 587;
                        
                        // $mail->setFrom('easybizcoop@gmail.com', 'Meet Global Resources');

                        // $mail->Host     = 'smtp.gmail.com';
                        // $mail->SMTPAuth = true;
                        // $mail->Username = 'meetglobalresources@gmail.com';
                        // $mail->Password = 'ogidifx@@123...';
                        // $mail->SMTPSecure = 'tsl';
                        // $mail->Port     = 587;
                        
                        // $mail->setFrom('meetglobalresources@gmail.com', 'Meet Global Resources');

                        // $mail->Host     = 'smtp.gmail.com';
                        // $mail->SMTPAuth = true;
                        // $mail->Username = 'Ogididavis02@gmail.com';
                        // $mail->Password = 'treasure16';
                        // $mail->SMTPSecure = 'tsl';
                        // $mail->Port     = 587;
                        
                        // $mail->setFrom('Ogididavis02@gmail.com', 'Meet Global Resources');


                        // $mail->Host     = 'smtp.gmail.com';
                        // $mail->SMTPAuth = true;
                        // $mail->Username = 'ikechukwunwogo@gmail.com';
                        // $mail->Password = 'programmer';
                        // $mail->SMTPSecure = 'tsl';
                        // $mail->Port     = 587;
                        
                        // $mail->setFrom('ikechukwunwogo@gmail.com', 'Meet Global Resources');

                        // $mail->Host     = 'smtp.gmail.com';
                        // $mail->SMTPAuth = true;
                        // $mail->Username = 'meetgresources@gmail.com';
                        // $mail->Password = 'dave1614.';
                        // $mail->SMTPSecure = 'tsl';
                        // $mail->Port     = 587;
                        
                        // $mail->setFrom('meetgresources@gmail.com', 'Meet Global Resources');

                        // $email_address = $this->getDefaultEmailAdress();
                        // $password = $this->getDefaultEmailAdressPassword();

                        // $mail->SMTPDebug  = 1;  
                        // $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
                        // $mail->SMTPAuth = true;                               // Enable SMTP authentication
                        // $mail->Username = $email_address;                 // SMTP username
                        // $mail->Password = $password;                           // SMTP password
                        // $mail->SMTPSecure = 'tsl';                            // Enable TLS encryption, `ssl` also accepted
                        // $mail->Port = 587;                                    // TCP port to connect to
                        
                        // $mail->setFrom($email_address, 'Meet Global Resources');

                        $email_address = $this->getDefaultEmailAdress();
                        $password = $this->getDefaultEmailAdressPassword();

                        // $mail->SMTPDebug  = 2;  
                        // $mail->Host = 'smtp.mail.yahoo.com';  // Specify main and backup SMTP servers
                        // $mail->SMTPAuth = true;                               // Enable SMTP authentication
                        // $mail->Username = $email_address;                 // SMTP username
                        // $mail->Password = $password;                           // SMTP password
                        // $mail->SMTPSecure = 'tsl';                            // Enable TLS encryption, `ssl` also accepted
                        // $mail->Port = 465;                                    // TCP port to connect to
                        
                        // $mail->setFrom($email_address, 'Meet Global Resources');



                        // $mail->SMTPDebug  = 2;  
                        $mail->Host = 'localhost';  // Specify main and backup SMTP servers
                        $mail->SMTPAuth = true;                               // Enable SMTP authentication
                        $mail->Username = 'admin@meetglobalresources.com';                 // SMTP username
                        $mail->Password = '6(Sq]Ok[(Kq9';                           // SMTP password
                        $mail->SMTPSecure = 'pop3';                            // Enable TLS encryption, `ssl` also accepted
                        $mail->Port = 25;                                    // TCP port to connect to
                        
                        $mail->setFrom('admin@meetglobalresources.com', 'Meet Global Resources');
                        // $mail->addReplyTo('info@example.com', 'CodexWorld');
                        
                        // Add a recipient
                        for($i = 0; $i < count($recepient_arr); $i++){
                            $to_email = $recepient_arr[$i];
                            // if($this->checkIfEmailHasNotifEnabled($to_email)  && $this->checkIfEmailNotifIsEnabled()){
                                $mail->addAddress($to_email);     // Add a recipient
                            // }
                        }
                        
                      
                        // Email subject
                        $mail->Subject = $subject;
                        
                        // Set email format to HTML
                        $mail->isHTML(true);
                        
                        // Email body content
                        // $mailContent = "<h1>Send HTML Email using SMTP in CodeIgniter</h1>
                        //     <p>This is a test email sending using SMTP mail server with PHPMailer.</p>";
                        $mail->Body = $message;
                        
                        // Send email
                        if(!$mail->send()){
                            // echo 'Message could not be sent.';
                            // echo 'Mailer Error: ' . $mail->ErrorInfo;
                            return false;
                        }else{
                            return true;
                        }
                    }
                }else{
                    return true;
                }
            }else{
                return true;
            }
        }else{
            return true;
        }   
    }

    public function getDefaultEmailAdressPassword(){
        
        $query = DB::table('default_email_address')->where('id', 1)->get();
        if($query->count() == 1){
            return $query[0]->password;
        }else{
            return false;
        }
    }


    public function getDefaultEmailAdress(){
        
        $query = DB::table('default_email_address')->where('id', 1)->get();
        if($query->count() == 1){
            return $query[0]->email_address;
        }else{
            return false;
        }
    }

    public function sendOtpEmail($email,$otp){
        if(is_array($email)){
            $subject = "OTP For MeetGlobal Resources";
            $message = "Your Otp Is: ".$otp.". Thanks For Using MeetGlobal Resources.";
            if($this->sendEmail($email,$subject,$message,true)){
                return true;
            }
        }
    }

    public function checkIfRandomBytesIsUniqueUsersTable($random_bytes){
        $query = DB::table('users')->where('token', $random_bytes)->get();
        if($query->count() > 0){
            return false;
        }else{
            return true;
        }
    }

    public function checkIfEmailIsUniqueUsersTable($email){
        $query = DB::table('users')->where('email', $email)->get();
        if($query->count() > 0){
            return false;
        }else{
            return true;
        }
    }

    public function checkIfThisNumberHasBeenUsed($phone,$calling_code){
        $user_id = $this->getUserIdWhenLoggedIn();
        // $query = $this->db->get_where('users',array('phone' => $phone,'phone_code' => $calling_code));

        $query = DB::table('users')->where("phone",$phone)->where("phone_code",$calling_code)->where("id",'!=',$user_id)->get();
        
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function getUserParamById($param,$user_id){
        
        $query = DB::table('users')->where('id', $user_id)->select($param)->get();
        if($query->count() == 1){
            return $query[0]->$param;
        }else{
            return false;
        }
    }

    public function getUserIdByName($user_name){
        $query = DB::table('users')->where('user_name', $user_name)->select("id")->get();
        if($query->count() == 1){
            foreach($query as $row){
                $user_id = $row->id;
            }
            return $user_id;
        }else{
            return "";
        }
    }

    public function checkIfUserNameExists($user_name){
        $query = DB::table('users')->where('user_name', $user_name)->select("id")->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function uploadFrontPageMessage($form_array){
        return DB::table('front_page_messages')->insert($form_array);
    }

    public function outPutBladeFileToUse($req){
        // if($this->confirmLoggedIn($req)){
        //     return "admin";
        // }else{
        //     return "app";
        // }
        return "app";
    }

    public function sendMeterTokenForPrepaidToUserByNotif($user_id,$email,$date,$time,$orderid,$disco,$meter_no,$amount,$meter_token){

        $title = "Successful Prepaid Electricity Recharge";
        $message = "Your Prepaid Electricity Recharge Was Successful With The Following Details: <br>";
        $message .= "Order Id: <em class='text-primary'>".$orderid."</em><br>";
        $message .= "Meter Token: <em class='text-primary'>".$meter_token."</em><br>";
        $message .= "Disco: <em class='text-primary'>".$disco."</em><br>";
        $message .= "Meter No.: <em class='text-primary'>".$meter_no."</em><br>";
        $message .= "Amount: <em class='text-primary'>".$amount."</em><br>";

        $form_array = array(
            'sender' => "System",
            'receiver' => $user_id,
            'title' => $title,
            'message' => $message,
            'date_sent' => $date,
            'time_sent' => $time,
            'type' => 'misc'
        );

        
        $email = array($email);

        $this->sendMessage($form_array);
        $this->sendEmail($email,$title,$message,true);
    }

    public function sendMeterTokenForPrepaidToUserByNotifAfterApproval($id,$date_time,$user_id,$date,$time,$orderid,$disco,$meter_no,$amount,$phone_number,$email,$meter_token){

        $title = "Meter Token For Your Prepaid Electricity Bill";
        $message = "Your Meter Token For Your Prepaid Electricity Transaction With The Following Details Has Arrived: <br>";
        $message .= "Meter Token: <em class='text-primary'>".$meter_token."</em><br>";
        $message .= "Order Id: <em class='text-primary'>".$orderid."</em><br>";
        $message .= "Disco: <em class='text-primary'>".$disco."</em><br>";
        $message .= "Meter No.: <em class='text-primary'>".$meter_no."</em><br>";
        $message .= "Amount: <em class='text-primary'>".$amount."</em><br>";
        $message .= "Date / Time: <em class='text-primary'>".$date_time."</em><br>";

        $form_array = array(
            'sender' => "System",
            'receiver' => $user_id,
            'title' => $title,
            'message' => $message,
            'date_sent' => $date,
            'time_sent' => $time,
            'type' => 'misc'
        );

        
        $email = array($email);

        // $this->sendMessage($form_array);
        // $this->sendEmail($email,$title,$message,true);
        // $sms_message = "Your Meter Token For Meter No. ".$meter_no.", ". $disco ." Is: " . $meter_token;
        $sms_message = "Your " . $disco . " Payment Was Successful. Amount:N".$amount." Date:" . $date_time . " Token:".$meter_token;
        $this->sendSmsMessage($phone_number,$sms_message);

        $form_array = array(
            'meter_token' => $meter_token,
            'notif_sent' => 1,
            'date_time' => $date . " " . $time
        );
        try{
            DB::table('prepaid_electricity_requests')->where('id', $id)->update($form_array);
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function sendSmsMessage($mobile_no,$message){
        $sender = "MGR";
        
        $use_post = true;
        $api_token = "uLeQszF9qDyUbfjZVGEsZZ5c6rNl2BRc6Cqw2KGyDTmtt8WZGQWUpdWhv2gA";
        $post_data = [
            "api_token" => $api_token,
            "from" => $sender,
            "body" => $message,
            "to" => $mobile_no
        ];
        

        $url = "https://www.bulksmsnigeria.com/api/v1/sms/create";
        // $url = site_url("onehealth/testing123");
        // $url .= "api_token=". $api_token."&from=".$from."&body=".$body."&to=".$to;
        
        // echo $url;
        $response = $this->curl($url, $use_post, $post_data);
        // var_dump($response);
        if($this->isJson($response)){
            return true;
        }
    }

    public function curl($url, $use_post, $post_data=[]){
        if(!$use_post){
            $response = Http::withOptions([
                'verify' => false,
            ])->get($url);
        }else{
            $response = Http::withOptions([
                'verify' => false,
            ])->post($url, $post_data);
        }

        return $response;
    }

    public function buyPowerVtuCurl($url, $use_post, $post_data=[]){
        $headers = [
            'Authorization' => 'Bearer df3f813b8dc7d9488535e7ea415d46e552f6e23f2da736e24eb9dae2700c5393',
            'Content-Type' => 'application/json'
        ];
        if(!$use_post){
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders($headers)->get($url);
        }else{
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders($headers)->post($url, $post_data);
        }
        return $response;
    
    }

    public function eminenceVtuCurl($url, $use_post, $post_data=[]){
        // return $post_data;
        $bearer_token = $this->getEminenceSubBearerToken();
        // return $bearer_token;
        $headers = [
            'Authorization' => 'Bearer '.$bearer_token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        if(!$use_post){
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders($headers)->get($url);
        }else{
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders($headers)->post($url, $post_data);
        }
        return $response;
    
    }

    public function getEminenceSubBearerToken(){
        $bearer_token = "";
        $query = DB::table('eminence_sub_token')->get();
        if($query->count() == 1){
            foreach($query as $row){
                $token = $row->bearer_token;
                $expires_in = date("Y-m-d",strtotime($row->expires_in));
            }

            $date_time = date("Y-m-d");

            $curr_date = strtotime($date_time);

            $expires_in = strtotime($expires_in);

            $date_diff = ($expires_in - $curr_date) / 86400;
            if($date_diff <= 0){
                $token_details = json_decode($this->getFreshEminenceSubToken());
                if(isset($token_details->access_token) && isset($token_details->expires_in)){
                    $bearer_token = $token_details->access_token;
                    $expires_in = $token_details->expires_in;

                    $form_array = array(
                        'bearer_token' => $bearer_token,
                        'expires_in' => $expires_in
                    );
                    DB::table('eminence_sub_token')->where('id', 1)->update($form_array);
                }
            }else{
                $bearer_token = $token;
            }

        }else if($query->count() == 0){
            $token_details = json_decode($this->getFreshEminenceSubToken());
            if(isset($token_details->access_token) && isset($token_details->expires_in)){
                $bearer_token = $token_details->access_token;
                $expires_in = $token_details->expires_in;

                $form_array = array(
                    'bearer_token' => $bearer_token,
                    'expires_in' => $expires_in
                );
                DB::table('eminence_sub_token')->insert($form_array);
            }
        }

        return $bearer_token;
    }

    public function getFreshEminenceSubToken(){
        $url = "https://app.eminencesub.com/api/login";
        $post_data = [
            'identity' => 'Meetglobal',
            'password' => 'GEEDAVIS'
        ];
        
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        
        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders($headers)->post($url, $post_data);
        
        return $response;
    }

    public function get9mobileComboDataBundles(){
        $query = DB::table('9mobile_combo_data_plans')->orderBy("amount","ASC")->get();
        if($query->count() > 0){
            return $query;
        }else{
            return false;
        }
    }

    public function addComboRequest($form_array){
        
        return DB::table('combo_recharge_vtu')->insert($form_array);
    }

    public function get9mobileComboDataAmountByProductId($product_id){
        $query = DB::table('9mobile_combo_data_plans')->where('id', $product_id)->get();
        if($query->count() == 1){
            return $query[0]->data_amount;
        }
    }

    public function get9mobileComboCostByProductId($product_id){
        
        $query = DB::table('9mobile_combo_data_plans')->where('id', $product_id)->get();
        if($query->count() == 1){
            return $query[0]->amount;
        }
    }

    public function getPayscribeVtuDataBundleCostByProductId($type,$product_id){
        $url = "https://www.payscribe.ng/api/v1/data/lookup";

        
        $use_post = true;
        $amount = 0;

        $response = $this->payscribeVtuCurl($url,$use_post,$post_data=[]);
        
        if($this->isJson($response)){
            $response = json_decode($response);
            if(is_object($response)){
                if($response->status == true){

                    $out = array_column($response->message->details, null, "network_name");

                    // echo json_encode($out['glo']);
                    if($type == "mtn"){
                        $network = "Mtn";
                        $plans = $out['mtn']->plans;
                    }elseif($type == "glo"){
                        $network = "Glo";
                        // $plans = $response->message->details[1]->plans;
                        $plans = $out['glo']->plans;
                    }else if($type == "airtel"){
                        $network = "Airtel";
                        // $plans = $response->message->details[2]->plans;
                        $plans = $out['airtel']->plans;
                    }else if($type == "9mobile"){
                        $network = "9mobile";
                        // $plans = $response->message->details[3]->plans;
                        $plans = $out['9mobile']->plans;
                    }

                    for($i = 0; $i < count($plans); $i++){
                        $PRODUCT_ID = $plans[$i]->plan_code;
                        if($PRODUCT_ID == $product_id){
                            $amount = $plans[$i]->amount;
                            break;
                        }
                    }
                }
            }
        }

        return $amount;
    }

    public function getVtuDataBundleCostByProductId($type,$product_id){
        $url = "https://www.nellobytesystems.com/APIQueryV1.asp?UserID=CK10153218&APIKey=UYV68HTPI15IFS0T8C94HC55PP7UCK44E11O033OAAEW7604BM3S7N50EE483649&OrderID=6322187656";
        $url = "https://www.nellobytesystems.com/APIDatabundlePlansV1.asp";
        $use_post = true;
        $amount = 0;

        

        $response = $this->vtu_curl($url,$use_post,$post_data=[]);
        
        if($this->isJson($response)){
            $response = json_decode($response);
            if(is_object($response)){
                if($type == "mtn"){
                    $network = "MTN";
                    $bundles = $response->MOBILE_NETWORK->MTN[0]->PRODUCT;
                }else if($type == "glo"){
                    $network = "Glo";
                    $bundles = $response->MOBILE_NETWORK->Glo[0]->PRODUCT;
                }else if($type == "airtel"){
                    $network = "Airtel";
                    $bundles = $response->MOBILE_NETWORK->Airtel[0]->PRODUCT;
                }else if($type == "9mobile"){
                    $network = "9mobile";
                    $bundles = $response->MOBILE_NETWORK->{'9mobile'}[0]->PRODUCT;
                }

                for($i = 0; $i < count($bundles); $i++){
                    $PRODUCT_ID = $bundles[$i]->PRODUCT_ID;
                    if($PRODUCT_ID == $product_id){
                        $amount = $bundles[$i]->PRODUCT_AMOUNT;
                        break;
                    }
                }
            }
        }

        return $amount;
    }

    public function payscribeVtuCurl($url, $use_post, $post_data=[]){
        $headers = [
            'Authorization' => 'Bearer ps_live_55a641ef18c94d14b220d05ae73fdef69cf6fe1137087ecdd0780bebaf0dc1df',
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache'
        ];
        if(!$use_post){
            $response = Http::withOptions([
                'http_errors' => false,
                'verify' => false,
            ])->withHeaders($headers)->get($url);
        }else{
            $response = Http::withOptions([
                'http_errors' => false,
                'verify' => false,
            ])->withHeaders($headers)->post($url, $post_data);
        }
        return $response;
    }

    public function debitUser($user_id,$amount,$summary = ""){
        $date = date("j M Y");
        $time = date("h:i:sa");
        
        $query = DB::table('users')->where('id', $user_id)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $withdrawn = $row->withdrawn;
                $total_income = $row->total_income;
            }
            $wallet_balance = $total_income - $withdrawn;
            $new_withdrawn = $withdrawn + $amount;
            $amount_after = $wallet_balance - $amount;
            // $query = $this->db->update('users',array('withdrawn' => $new_withdrawn),array('id' => $user_id));
            try {
                
                DB::table('users')->where('id', $user_id)->update(array('withdrawn'=> $new_withdrawn));
                
                if(DB::table('account_statement')->insert(array('user_id' => $user_id,'amount' => $amount,'amount_before' => $wallet_balance,'amount_after' => $amount_after,'summary' => $summary,'date' => $date,'time' => $time))){
                    // $this->runTheMainCheckAndDebitingMnthlyServiceCharge($user_id);
                    return true;
                }
                
            } catch (Exception $e) {
                return false;
            }
        }
    }

    public function getIdsToCreditEducationalVoucherVtu($mlm_db_id){
        $ret_arr = array();
        $ret_arr[] = array(
            'mlm_db_id' => $mlm_db_id,
            'user_id' => $this->getMlmDbParamById("user_id",$mlm_db_id)
        );
        

        return $ret_arr;
    }

    public function addVTUTransactionStatusEducationalVoucher($form_array){ 
        $date = date("j M Y");
        $time = date("h:i:sa");     
        if(DB::table('vtu_transactions')->insert($form_array)){

            $user_id = $form_array['user_id'];
            $amount = $form_array['amount'];
            $type = $form_array['type'];
            $sub_type = $form_array['sub_type'];
            
            $perc_amount = 100;
            

            $charge_type = 14;

            $mlm_db_id = $this->getUsersFirstMlmDbId($user_id);
            $ids_to_credit = $this->getIdsToCreditEducationalVoucherVtu($mlm_db_id);

            $vtu_income_vat = $this->getVtuTradeVatCharge();
            $vtu_income_vat_val = round(($vtu_income_vat / 100) * $perc_amount,2);

            $real_vtu_income = 100;


            

            $ids_to_credit_num = count($ids_to_credit);
            for($i = 0; $i < count($ids_to_credit); $i++){
                $user_id = $ids_to_credit[$i]['user_id'];
                $placements_mlm_db_id = $ids_to_credit[$i]['mlm_db_id'];


                if(DB::table('mlm_earnings')->insert(array('user_id' => $user_id,'mlm_db_id' => $placements_mlm_db_id,'charge_type' => $charge_type,'amount' => $perc_amount,'vat' => $vtu_income_vat,'date' => $date,'time' => $time))){

                    
                        $form_array = array();

                        $this->updateUsersBusinessIncomeForTheMonth($user_id,$real_vtu_income);
                        $total_business_income = $this->getUserParamById("total_business_income",$user_id);
                        $new_total_business_income = $total_business_income + $real_vtu_income;

                        $form_array = array(
                            'total_business_income' => $new_total_business_income
                        );
                        

                        if($this->updateUserTable($form_array,$user_id)){

                        }

                        if($i == ($ids_to_credit_num - 1)){
                            return true;
                        }
                    
                }
            }
            
        }
    }

    public function addTransactionStatus($form_array,$payscribe_data = false){  
        $date = date("j M Y");
        $time = date("h:i:sa");     
        
        if(DB::table('vtu_transactions')->insert($form_array)){
            $user_id = $form_array['user_id'];
            $amount = $form_array['amount'];
            $type = $form_array['type'];
            $sub_type = $form_array['sub_type'];
            if($type == "airtime" || $type == "data"){
                if($type == "data"){
                    $amount = $amount - 2;
                }
                $perc_amount = round((0.1 / 100) * $amount,2);
                $perc_amount_2 = round((0.08 / 100) * $amount,2);
                $purchaser_amount = round((2 / 100) * $amount,2);

            }else if($type == "electricity"){
                if($payscribe_data){
                    $perc_amount = round((0.1 / 100) * $amount,2);
                    $perc_amount_2 = round((0.08 / 100) * $amount,2);
                    $purchaser_amount = round((0.50 / 100) * $amount,2);
                }else{
                    $perc_amount = round((0.1 / 100) * $amount,2);
                    $perc_amount_2 = round((0.08 / 100) * $amount,2);
                    $purchaser_amount = round((0.105 / 100) * $amount,2);
                }
            }else if($type == "cable"){
                $perc_amount = round((0.1 / 100) * $amount,2);
                $perc_amount_2 = round((0.08 / 100) * $amount,2);
                $purchaser_amount = round((0.105 / 100) * $amount,2);
            }else if($type == "router"){
                $perc_amount = round((0.1 / 100) * $amount,2);
                $perc_amount_2 = round((0.08 / 100) * $amount,2);
                $purchaser_amount = round((1 / 100) * $amount,2);
            }
            $vtu_income_vat = $this->getVtuTradeVatCharge();
            $vtu_income_vat_val = round(($vtu_income_vat / 100) * $perc_amount,2);
            $vtu_income_vat_val_2 = round(($vtu_income_vat / 100) * $perc_amount_2,2);

            $real_vtu_income = round(($perc_amount - $vtu_income_vat_val),2);
            $real_vtu_income_2 = round(($perc_amount_2 - $vtu_income_vat_val_2),2);

            $charge_type = 14;

            $mlm_db_id = $this->getUsersFirstMlmDbId($user_id);
            $ids_to_credit = $this->getIdsToCreditVtu($mlm_db_id);


            

            if(DB::table('mlm_earnings')->insert(array('user_id' => $user_id,'mlm_db_id' => $mlm_db_id,'charge_type' => $charge_type,'amount' => $purchaser_amount,'vat' => 0,'date' => $date,'time' => $time))){

                $this->updateUsersBusinessIncomeForTheMonth($user_id,$real_vtu_income);

                $total_business_income = $this->getUserParamById("total_business_income",$user_id);
                $new_total_business_income = $total_business_income + $real_vtu_income;

                $form_array = array(
                    'total_business_income' => $new_total_business_income
                );
                
                $this->updateUserTable($form_array,$user_id);
            }

            $ids_to_credit_num = count($ids_to_credit);
            for($i = 0; $i < count($ids_to_credit); $i++){
                $user_id = $ids_to_credit[$i]['user_id'];
                $placements_mlm_db_id = $ids_to_credit[$i]['mlm_db_id'];

                
                if($i > 0 && $i <= 4){
                    $amount_to_credit_placement = $perc_amount;
                    $real_amount_to_credit_placement = $real_vtu_income;
                    $vtu_vat_val = $vtu_income_vat_val;
                }else{
                    $amount_to_credit_placement = $perc_amount_2;
                    $real_amount_to_credit_placement = $real_vtu_income_2;
                    $vtu_vat_val = $vtu_income_vat_val_2;
                }

                if($user_id == $this->getAdminId()){
                    $total_admin_earning_for_month_partic_vtu = $this->getCurrentAdminEarningsForParticMonthVtu($type,$sub_type);
                    $new_admin_month_earning_amt = $total_admin_earning_for_month_partic_vtu + $amount_to_credit_placement;
                    $this->updateAdminEarningsForParticMonthVtu($new_admin_month_earning_amt,$type,$sub_type);
                }

                

                if(DB::table('mlm_earnings')->insert(array('user_id' => $user_id,'mlm_db_id' => $placements_mlm_db_id,'charge_type' => $charge_type,'amount' => $amount_to_credit_placement,'vat' => $vtu_vat_val,'date' => $date,'time' => $time))){

                    $total_vat = $this->getUserParamById("admin_vat_total",$this->getAdminId());
                    $new_vat_total = $total_vat + $vtu_vat_val;
                    $form_array = array(
                        'admin_vat_total' => $new_vat_total
                    );



                    if($this->updateUserTable($form_array,$this->getAdminId())){
                        $total_admin_earning_for_month_partic_vtu = $this->getCurrentAdminEarningsForParticMonthVtu($type,$sub_type);
                        $new_admin_month_earning_amt = $total_admin_earning_for_month_partic_vtu + $vtu_vat_val;
                        $this->updateAdminEarningsForParticMonthVtu($new_admin_month_earning_amt,$type,$sub_type);

                        $form_array = array();

                        $this->updateUsersBusinessIncomeForTheMonth($user_id,$real_amount_to_credit_placement);
                        $total_business_income = $this->getUserParamById("total_business_income",$user_id);
                        $new_total_business_income = $total_business_income + $real_amount_to_credit_placement;

                        $form_array = array(
                            'total_business_income' => $new_total_business_income
                        );
                        

                        if($this->updateUserTable($form_array,$user_id)){

                        }

                        if($i == ($ids_to_credit_num - 1)){
                            return true;
                        }
                    }
                }
            }
            
        }
    }

    public function getCurrentAdminVtuEarningForMonthByParam($param){
        $current_month_year = date("M Y");
        $query = DB::table('admin_vtu_earnings')->where('month_year', $current_month_year)->select($param)->get();
        if($query->count() == 1){
            return $query[0]->$param;
        }
    }

    public function updateAdminEarningsForParticMonthVtu($new_admin_month_earning_amt,$type,$sub_type){
        $current_month_year = date("M Y");
        $earning_str = $sub_type . '_' .$type.'_earnings';
        try{
            DB::table('admin_vtu_earnings')->where('month_year', $current_month_year)->update(array($earning_str => $new_admin_month_earning_amt));
        }catch(Exception $e){
            return false;
        }
    }

    public function getCurrentAdminEarningsForParticMonthVtu($type,$sub_type){
        $current_month_year = date("M Y");
        $earning_str = $sub_type . '_' .$type.'_earnings';
        $query = DB::table('admin_vtu_earnings')->where('month_year', $current_month_year)->get($earning_str);
        if($query->count() == 1){
            return $query[0]->$earning_str;
        }
    }

    public function getIdsToCreditVtu($mlm_db_id){
        $ret_arr = array();
        $ret_arr[] = array(
            'mlm_db_id' => $mlm_db_id,
            'user_id' => $this->getMlmDbParamById("user_id",$mlm_db_id)
        );
        for($i = 1; $i <= 16; $i++){
            
            $query = DB::table('mlm_db')->where('id', $mlm_db_id)->get('under');
            if($query->count() == 1){
                foreach($query as $row){
                    $under = $row->under;
                    if(!is_null($under)){
                        $user_id = $this->getMlmDbParamById("user_id",$under);
                        // $this->getIdsToCreditPlacement($under);
                        $mlm_db_id = $under;
                        $ret_arr[] = array(
                            'mlm_db_id' => $under,
                            'user_id' => $user_id
                        );
                        
                    }else{
                        $ret_arr[] =  array(
                            'mlm_db_id' => 1,
                            'user_id' => $this->getAdminId()
                        );
                    }
                }
                
            }

        }

        return $ret_arr;
    }

    public function getVtuTradeVatCharge(){
        
        $query = DB::table('mlm_charges')->where('id', 14)->get('vat');
        if($query->count() == 1){
            return $query[0]->vat;
        }
    }

    public function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function vtu_curl($url,$use_post,$post_data=[]){
        if(!$use_post){
            $response = Http::withOptions([
                'verify' => false,
            ])->get($url);
        }else{
            $response = Http::withOptions([
                'verify' => false,
            ])->post($url, $post_data);
        }

        return $response;
    }


    public function getUserIdWhenLoggedIn(){
        if(isset($_COOKIE['meetrem'])){
            $cookie = $_COOKIE['meetrem'];
            list($user_id,$token,$mac) = explode(':', $cookie);
            return $user_id;
        }else{
            if(isset($_POST['meetrem'])){
                $user_id = $_POST['meetrem'];
                return $user_id;
            }
            return false;
        }   
    }

    public function getUserTotalAmountByUse($user_id){
        
        $query = DB::table('users')->where('id', $user_id)->get();
        if($query->count() == 1){
            foreach($query as $row){
                $total_income = $row->total_income;
                $withdrawn = $row->withdrawn;
            }
            return $total_income - $withdrawn;
        }else{
            return false;
        }
    }

    public function getAllUserInfo($user_id){
        
        $query = DB::table('users')->where('id' ,$user_id)->get();
        if($query->count() == 1){
            foreach($query as $row){
                // $followers = $row->followers;
                // $following = $row->following;
                // // $posts_num = $this->getUserTotalPostsNum($user_id);
                // // $following_num = $this->getUserTotalFollowingNum($user_id);
                // // $followers_num = $this->getUserTotalFollowersNum($user_id);
                // unset($row->followers);
                // unset($row->following);
                // $row->following_num = $following_num;
                // $row->followers_num = $followers_num;
                // $row->posts_num = $posts_num;
            }
            return $query;
        }
    }

    public function updateUserLastActivity($user_id){
        // $query_str = "UPDATE users SET last_activity = NOW() WHERE id=".$user_id;
        try {
            DB::table('users')->where('id' ,$user_id)->update(['last_activity' => Carbon::now()]);
            return true;
        } catch (Exception $e) {
            return false;
        }
        
        // if($this->db->query($query_str)){
        //     return true;
        // }else{
        //     return false;
        // }
    }

    public function onRegister($user_id,$token){
        $user_id = strtolower($user_id);
        $token = strtolower($token);
        $cookie = $user_id . ':' .$token;
        $mac = Crypt::encryptString($cookie);
        $cookie .= ':' .$mac;
        // if($user_id == 10 || $user_id == 177){
        //     $seconds = 31536000;
        // }else{
        //     $seconds = 1800;
        // }
        $seconds = 31536000;
        
        // if(setcookie('meetrem',$cookie,time() + 31536000,'/')) {
        if(setcookie('meetrem',$cookie,time() + $seconds,'/')) {

            return true;
        }
    }

    public function getUserInfoByUserName($user_name){
        
        $query = DB::table('users')->where('user_name',$user_name)->get();
        if($query->count() == 1){
            return $query;
        }else{
            return false;
        }
    }


    public function passwordVerify($user_name,$hashed){

        $query = DB::table('users')->where('user_name',$user_name)->where('hashed',$hashed)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function userExistsAdmin($user_name){
        
        $query = DB::table('users')->where('user_name',$user_name)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function updateUserTableById($form_array,$user_id){
        try{
            DB::table('users')->where('id',$user_id)->update($form_array);
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function getAllUsers(){
        $query = DB::table('users')->get();
        if($query->count() > 0){
            return $query;
        }else{
            return false;
        }
    }

    // public function confirmLoggedIn($request,$check_if_admin = FALSE){
    //     // $response = new Illuminate\Http\Response('Hello World');
    //     // $minutes = 3;

    //     // $cookie_name = "name";
    //     // $cookie_value = "John Doe";
    //     // setcookie($cookie_name, $cookie_value, time() + ($minutes * 30 * 30), "/"); // 86400 = 1 day
    //     // $encryptedValue = Crypt::encryptString("testing");
        
    //     // echo $encryptedValue . "<br>";

    //     // $decryptedValue = Crypt::decryptString($encryptedValue);

    //     // echo $decryptedValue;

    //     // $query = DB::table('users')->select('user_name','last_activity')->where('id',10)->get();
    //     // var_dump($query);

    //     if(isset($_COOKIE['meetrem'])){
    //         $cookie = $_COOKIE['meetrem'];
    //         list($user_id,$token,$mac) = explode(':', $cookie);
    //         if(!isset($user_id) || !isset($token) || !isset($mac) || is_null($user_id) || is_null($mac) || is_null($token) || $user_id == "" || $token == "" || $mac == ""){
    //             return false;
    //         }
    //         $cookie0 = $user_id . ':' .$token;
            
    //         try {
    //             $decrypt_mac = Crypt::decryptString($mac);
    //             if($decrypt_mac == false){
    //                 return false;
    //             }
    //         } catch (DecryptException $e) {
    //             return false;
    //         }
            
    //         if(!hash_equals($cookie0,$decrypt_mac)){
    //             return false;
    //         }

    //         $query = DB::table('users')->where('id',$user_id)->limit(1)->get();
    //         if($query->count() > 0){
    //             $usertoken_arr = $query;
    //             if(is_object($usertoken_arr)){
    //                 foreach($usertoken_arr as $user_token){
    //                     $user_token = $user_token->token;
    //                     // $user_name1 = $user_token->user_name;
    //                 }
                    
    //                 if(hash_equals($user_token,$token)){
    //                     $query1 = DB::table('users')->where('id',$user_id)->get();
                        
    //                     if($query1->count() == 1){
    //                         foreach($query1 as $row){
    //                             $created = $row->created;
    //                             $is_admin = $row->is_admin;
    //                             $active = $row->active;
    //                             if($check_if_admin){
    //                                 if($created == 1 && $is_admin == 1){
    //                                     return true;
    //                                 }
    //                             }else{
    //                                 // echo "string";
    //                                 if($created == 1){
    //                                     if($active == 1){
    //                                         return true;
    //                                     }else{
    //                                         setcookie('meetrem', time() - 3600);
    //                                     }
    //                                 }
    //                             }
    //                         }
                            
    //                     }
    //                 }else{
    //                     return false;
    //                 }
    //             }else{
    //                 return false;
    //             }
    //         }

    //     }else{
    //         if(isset($_POST['meetrem'])){
    //             $user_id = $_POST['meetrem'];
    //             if($this->checkIfUserExists1($user_id)){
    //                 return true;
    //             }
    //         }
    //         return false;
    //     }
    // }

    public function confirmLoggedIn(){
        if(isset($_COOKIE['meetrem'])){
            $cookie = $_COOKIE['meetrem'];
            list($user_id,$token,$mac) = explode(':', $cookie);
            if(!isset($user_id) || !isset($token) || !isset($mac) || is_null($user_id) || is_null($mac) || is_null($token) || $user_id == "" || $token == "" || $mac == ""){
                return false;
            }
            $cookie0 = $user_id . ':' .$token;
            
            
            try {
                $decrypt_mac = Crypt::decryptString($mac);
                if($decrypt_mac == false){
                    return false;
                }
            } catch (DecryptException $e) {
                return false;
            }

            
            
            if(!hash_equals($cookie0,$decrypt_mac)){
                return false;
            }
            $usertoken_arr = DB::table('users')->where('id', $user_id)->limit(1)->get();
            
            if(is_object($usertoken_arr)){
                foreach($usertoken_arr as $user_token){
                    $user_token = $user_token->token;
                    // $user_name1 = $user_token->user_name;
                }
                
                if(hash_equals($user_token,$token)){
                    
                    $query1 = DB::table('users')->where('id', $user_id)->get();
                    if($query1->count() == 1){
                        foreach($query1 as $row){
                            $created = $row->created;
                            if($created == 1){
                                return true;
                            }
                        }
                        
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            if(isset($_POST['meetrem'])){
                $user_id = $_POST['meetrem'];
                if($this->checkIfUserExists1($user_id)){
                    return true;
                }
            }
            return false;
        }
    }

    public function checkIfUserExists1($user_id){
        
        $query = DB::table('users')->where('id',$user_id)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }
}
