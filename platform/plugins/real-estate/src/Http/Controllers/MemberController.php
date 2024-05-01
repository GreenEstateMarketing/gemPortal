<?php

namespace Botble\RealEstate\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use Theme;
use URL;

class MemberController extends Controller
{
    public function __construct()
{
    /* session(['url.intended' => URL::previous()]);
     $pages = [route('member.login'), /*route('public.single'),*//*route('member.account.register')];

        if (in_array(session()->get('url.intended'), $pages)) {
            $this->redirectTo = route('member-dashboard');
        } else {
            $this->redirectTo = session()->get('url.intended');
        }*/
}
    public function showLoginForm()
    {
        //print("exit");exit;
       // SeoHelper::setTitle(trans('plugins/real-estate::account.login'));

        if (view()->exists(Theme::getThemeNamespace() . '::views.real-estate.member.auth.login')) {

            return Theme::scope('real-estate.member.auth.login')->render();
        }

        return view('plugins/real-estate::member.auth.login');
    }
    public function login(Request $request)
    {
        print_r($request);exit;
    }
}
