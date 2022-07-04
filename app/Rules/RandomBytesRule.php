<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\MainModel;


class RandomBytesRule implements Rule
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
        //
        $random_bytes = $value;
        if($this->main_model->checkIfRandomBytesIsUniqueUsersTable($random_bytes)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
