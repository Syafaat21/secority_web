<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Verification;
use App\Mail\OtpEmail;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    public function index(){
        return view('verification.index');
    }

    public function show($unique_id){
        $verify = Verification::whereUserId(Auth::user()->id)->whereUniqueId($unique_id)
            ->whereStatus('active')->count();
        if(!$verify) abort(404);
        return view('verification.show', compact('unique_id'));
    }

    public function update(Request $request, $unique_id){
        $verify = Verification::whereUserId(Auth::user()->id)->whereUniqueId($unique_id)
            ->whereStatus('active')->first();
        if(!$verify) abort(404);
        if(md5($request->otp) != $verify->otp){
            $verify->update(['status' => 'invalid']);
            return redirect('/verify')->with('failed', 'OTP yang anda masukkan salah.');
        }
        $verify->update(['status' => 'valid']);
        User::find($verify->user_id)->update(['status' => 'active']);
        return redirect('/customer');
    }

    public function store(Request $request){
        if($request->type == 'register'){
            $user = User::find($request->user()->id);
        } else {
            // $user = reset password
        }
        if(!$user) return back()->with('failed', 'User not found.');
        $otp = rand(100000, 999999);
        $verify = Verification::create([
            'user_id' => $user->id, 'unique_id' => uniqid(), 'otp' => md5($otp),
            'type' => $request->type, 'send_via' => 'email'
        ]);
        Mail::to($user->email)->queue(new OtpEmail($otp));
        if($request->type == 'register'){
            return redirect('/verify/'. $verify->unique_id);
        }
        // return redirect('/reset-password')
    }

    public function resend($unique_id){
        $verify = Verification::whereUserId(Auth::user()->id)->whereUniqueId($unique_id)
            ->whereStatus('active')->first();
        if(!$verify) abort(404);
        $otp = rand(100000, 999999);
        $verify->update(['otp' => md5($otp)]);
        Mail::to(Auth::user()->email)->queue(new OtpEmail($otp));
        return back()->with('success', 'OTP telah dikirim ulang.');
    }
}
