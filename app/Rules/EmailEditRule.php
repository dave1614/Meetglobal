<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\MainModel;

class EmailEditRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($user_id)
    {
        //
        $this->main_model = new MainModel();
        $this->user_id = $user_id;
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
        //
        $email = $value;
        $user_id = $this->user_id;
        if($this->main_model->checkIfEmailIsInId($email,$user_id)){
            return TRUE;
        }else{
            if($this->main_model->checkIfEmailExists($email)){
                
                return FALSE;
            }else{
                return TRUE;
            }
        }   

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Email Used By Another User';
    }
}
