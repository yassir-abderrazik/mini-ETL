<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CheckDatabaseExistRole implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $tables = DB::select('SHOW DATABASES');
        $existDB = true;
        foreach($tables as $table){
            if($table->Database == $value){
                $existDB =  $existDB  *  false;
            }
        }
        return $existDB;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Database Exist';
    }
}
