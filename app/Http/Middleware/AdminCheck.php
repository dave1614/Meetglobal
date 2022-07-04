<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\MainModel;
use App\Http\Controllers\MainController;

class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public $main_model = "";
    public $main_controller = "";
    
    public function __construct(){
        
        $this->main_model = new MainModel();
        $this->main_controller = new MainController();
    }


    public function handle(Request $request, Closure $next)
    {
        if($this->main_model->confirmLoggedIn(true)){
            if(!$this->main_model->verifyUserSignedInIsAdmin()){
                return response()->view('no_access');
            }
        }else{
            return response()->view('no_access');
        }
        return $next($request);
    
    }
}
