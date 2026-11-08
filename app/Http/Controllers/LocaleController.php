<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocaleController extends Controller
{
    protected array $allowedLocales = ['en', 'ur'];

    public function switch(string $locale, Request $request): RedirectResponse
    {
        abort_unless(in_array($locale, $this->allowedLocales, true), 404);

        if (Auth::guard('member')->check()) {
            Auth::guard('member')->user()->forceFill(['locale' => $locale])->save();
        } elseif (Auth::guard('account')->check()) {
            Auth::guard('account')->user()->forceFill(['locale' => $locale])->save();
        }

        return redirect(url()->previous() ?: url('/'))
            ->cookie('app_locale', $locale, 60 * 24 * 365);
    }
}
