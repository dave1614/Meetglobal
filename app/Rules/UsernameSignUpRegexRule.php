<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\MainModel;

class UsernameSignUpRegexRule implements Rule
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
        $userName = $value;
        if (preg_match('/^\S*$/', $userName ) ) 
          {
            return TRUE;
          } 
          else 
          {
            return FALSE;
          }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Sorry the username you entered contains disallowed characters';
    }
}
