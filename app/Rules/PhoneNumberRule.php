<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\MainModel;

class PhoneNumberRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->main_model = new MainModel();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $phone = $value;
        $ip_address = $_SERVER['REMOTE_ADDR'];
        if($ip_address == "::1"){
            $ip_address = "197.211.60.81";
        }

        
       
        $calling_code = "234";
            
        
        $phone = substr($phone,1);
        
        if($this->main_model->checkIfThisNumberHasBeenUsed($phone,$calling_code)){
            return FALSE;
        }else{

            return TRUE;
        }
           
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Sorry This Phone Number Has Been Used Before';
    }
}
