<?php

namespace Whole\Core\Http\Controllers\Admin;


use Whole\Core\Http\Controllers\Admin\MainController;
use Whole\Core\Logs\Facade\Logs;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;

class AuthController extends MainController
{


    public function login()
    {
        return view('backend::login.login');
    }

    public function loginAction(Request $request)
    {
        $this->userClearCache();
        $remember = $request->get('remember');
        if (Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')], $remember))
        {
            Logs::add('login',$request->get('email')." ".trans('whole::http/controllers.auth_log_1'));
            return redirect()->route('admin.index');
        }else
        {
            Logs::add('login',trans('whole::http/controllers.auth_log_2',['email'=>$request->get('email'),'password'=>$request->get('password')]));
            Flash::error(trans('whole::http/controllers.auth_not_admin'));
            return redirect()->route('admin.login');
        }
    }


    public function logout()
    {
        $this->userClearCache();
        Logs::add('process',trans('whole::http/controllers.auth_log_3'));
        Auth::logout();
        return redirect()->route('master_page');
    }

}
