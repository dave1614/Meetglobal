<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function addNewUser($form_array){
        return DB::table('users')->insert($form_array);
    }

    public function deleteUser($user_id){
        try{
            DB::table('users')->where('id', $user_id)->delete();
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function checkIfUserIdIsValid($user_id){
        $query = DB::table('users')->where('id',$user_id)->get();
        if($query->count() == 1){
            return true;
        }else{
            return false;
        }
    }

    public function editUser($form_array,$id){
        // echo $id;
        try{
            DB::table('users')->where('id',$id)->update($form_array);
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
