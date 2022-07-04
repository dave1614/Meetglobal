<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\MainModel;

class UsernameSignupUnique implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        $user_name = $value;
        if($this->main_model->checkIfUserNameExists($user_name)){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This user name has been used by another user.';
    }
}
