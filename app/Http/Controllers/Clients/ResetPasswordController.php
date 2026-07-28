<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function showResetForm($token)
    {
        // Hien thi form dat lai mat khau va gui token qua view.
        return view('clients.pages.reset-password', compact('token'));
    }

    public function resetPassword(Request $request)
    {
        // Kiá»ƒm tra email, máº­t kháº©u má»›i vÃ  token reset máº­t kháº©u.
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
            'token' => 'required',
        ], [
            'email.required' => 'Email lÃ  báº¯t buá»™c',
            'email.email' => 'Email khÃ´ng há»£p lá»‡',
            'email.exists' => 'Email nÃ y chÆ°a Ä‘Æ°á»£c Ä‘Äƒng kÃ½ trong há»‡ thá»‘ng',
            'password.required' => 'Vui lÃ²ng nháº­p máº­t kháº©u',
            'password.min' => 'Máº­t kháº©u pháº£i cÃ³ Ã­t nháº¥t 6 kÃ½ tá»±',
            'password.confirmed' => 'Máº­t kháº©u xÃ¡c nháº­n khÃ´ng khá»›p',
            'token.required' => 'MÃ£ token khÃ´ng há»£p láº¹ hoáº·c Ä‘Ã£ háº¿t háº¡n',
        ]);

        // Cáº­p nháº­t máº­t kháº©u má»›i náº¿u token há»£p lá»‡.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Äá»•i máº­t kháº©u thÃ nh cÃ´ng thÃ¬ chuyá»ƒn vá» trang Ä‘Äƒng nháº­p.
            toastr()->success('Máº­t kháº©u Ä‘Ã£ Ä‘Æ°á»£c Ä‘áº·t láº¡i thÃ nh cÃ´ng');
            return redirect()->route('login');
        }

        // Token hoáº·c email khÃ´ng há»£p lá»‡ thÃ¬ tráº£ lá»—i vá» form.
        toastr()->error('Äáº·t láº¡i máº­t kháº©u khÃ´ng thÃ nh cÃ´ng');

        return back()->withErrors(['email' => __($status)]);
    }
}

