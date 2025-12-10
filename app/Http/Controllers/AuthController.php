<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\verification;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email|max:50',
            'password' => 'required|max:50',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !Auth::attempt($request->only('email', 'password'), $request->remember)){
            $this->trackFailedLogin($user);
            return back()->with('failed', 'Email atau password salah');
        }

        if($user->status == 'banned'){
            return back()->with('failed', 'Akun Anda telah dibekukan.');
        }

        $user->failed_login_attempts = 0; // Reset setelah berhasil login
        $user->save();

        // Keep user id in session for 2FA flow then logout so routes protected by `auth` can't be accessed yet
        Session::put('2fa:user:id', $user->id);
        Session::put('2fa:user:remember', (bool)$request->remember);
        Auth::logout();

        if(!$user->two_factor_secret){
            return redirect()->route('2fa.setup');
        }
        return redirect()->route('2fa.verify');
    }

    private function trackFailedLogin($user){
        if($user){
            $user->failed_login_attempts++;
            if($user->failed_login_attempts >= 3){
                $user->status = 'banned';
            }
            $user->save();
        }
    }

    // Show QR and secret for initial 2FA setup
    public function show2faSetup(Request $request){
        $userId = Session::get('2fa:user:id');
        if(!$userId) return redirect('/login')->with('failed', 'Sesi 2FA tidak ditemukan. Silakan login ulang.');

        $user = User::find($userId);
        $google2fa = new Google2FA();

        // Generate a temporary secret and save in session until user confirms
        if(!Session::has('2fa:secret_temp')){
            $secret = $google2fa->generateSecretKey();
            Session::put('2fa:secret_temp', $secret);
        } else {
            $secret = Session::get('2fa:secret_temp');
        }

        $company = config('app.name') ?: 'MyApp';
        $otpAuthUrl = $google2fa->getQRCodeUrl($company, $user->email, $secret);
        // Use qrserver.com to generate QR image (more reliable than deprecated Google Chart API)
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($otpAuthUrl);

        return view('auth.2fa_setup', compact('qrUrl', 'secret', 'user'));
    }

    // Confirm setup by verifying TOTP code and saving secret to user
    public function post2faSetup(Request $request){
        $messages = [
            'code.required' => 'Kode 2FA wajib diisi.',
            'code.digits_between' => 'Kode 2FA harus berupa angka 6 digit.',
        ];

        $request->validate([
            'code' => 'required|digits_between:6,8'
        ], $messages);

        $userId = Session::get('2fa:user:id');
        $secret = Session::get('2fa:secret_temp');
        if(!$userId || !$secret) return redirect('/login')->with('failed', 'Sesi 2FA tidak ditemukan.');

        try{
            $google2fa = new Google2FA();
            $valid = $google2fa->verifyKey($secret, $request->code);
        } catch (\Exception $e) {
            return back()->with('failed', 'Terjadi kesalahan saat memverifikasi kode. Silakan coba lagi.');
        }

        if(!$valid){
            return back()->with('failed', 'Kode 2FA tidak valid.');
        }

        $user = User::find($userId);
        $user->two_factor_secret = $secret;
        $user->save();

        // Cleanup and login
        Session::forget('2fa:secret_temp');
        Session::forget('2fa:user:id');
        $remember = Session::pull('2fa:user:remember', false);
        Auth::loginUsingId($user->id, $remember);

        if($user->role == 'customer') return redirect('/customer')->with('success', '2FA berhasil diaktifkan.');
        return redirect('/dashboard')->with('success', '2FA berhasil diaktifkan.');
    }

    // Show form for entering 2FA code
    public function show2faVerify(Request $request){
        $userId = Session::get('2fa:user:id');
        if(!$userId) return redirect('/login')->with('failed', 'Sesi 2FA tidak ditemukan. Silakan login ulang.');
        return view('auth.2fa_verify');
    }

    // Verify submitted 2FA code and login the user
    public function post2faVerify(Request $request){
        $messages = [
            'code.required' => 'Kode 2FA wajib diisi.',
            'code.digits_between' => 'Kode 2FA harus berupa angka 6 digit.',
        ];

        $request->validate([
            'code' => 'required|digits_between:6,8'
        ], $messages);

        $userId = Session::get('2fa:user:id');
        if(!$userId) return redirect('/login')->with('failed', 'Sesi 2FA tidak ditemukan.');

        $user = User::find($userId);
        if(!$user || !$user->two_factor_secret){
            return redirect('/login')->with('failed', 'Pengguna atau secret 2FA tidak ditemukan.');
        }

        try{
            $google2fa = new Google2FA();
            $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);
        } catch (\Exception $e) {
            return back()->with('failed', 'Terjadi kesalahan saat memverifikasi kode. Silakan coba lagi.');
        }

        if(!$valid){
            return back()->with('failed', 'Kode 2FA tidak valid.');
        }

        // Passed 2FA: cleanup and login
        Session::forget('2fa:user:id');
        $remember = Session::pull('2fa:user:remember', false);
        Auth::loginUsingId($user->id, $remember);

        if($user->role == 'customer') return redirect('/customer');
        return redirect('/dashboard');
    }

    // Reset stored 2FA secret so user can re-enroll (accessed during 2FA flow)
    public function reset2fa(Request $request){
        $userId = Session::get('2fa:user:id');
        if(!$userId) return redirect('/login')->with('failed', 'Sesi 2FA tidak ditemukan.');

        $user = User::find($userId);
        if(!$user) return redirect('/login')->with('failed', 'Pengguna tidak ditemukan.');

        // Clear existing secret so show2faSetup will present a new secret/QR
        $user->two_factor_secret = null;
        $user->save();

        // Remove any previous temp secret so a new one is generated
        Session::forget('2fa:secret_temp');

        return redirect()->route('2fa.setup')->with('success', '2FA di-reset. Silakan scan QR baru atau masukkan kunci secret.');
    }

    function register(Request $request){
        $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|max:50',
            'password' => 'required|max:50|min:8',
            'confirm_password' => 'required|max:50|min:8|same:password',
        ]);

        $request['status'] = 'verify';
        $user = User::create($request->all());

        // Redirect to login page with success message
        return redirect('/login')->with('success', 'Registration successful. Please log in to continue and complete 2FA setup.');
    }

    public function forgot_password(Request $request){
        $request->validate([
            'email' => 'required|email|max:50',
        ]);
        $user = User::whereEmail($request->email)->first();
        if(!$user){
            return back()->with('failed', 'Email tidak terdaftar.');
        }

        // Hapus token reset_password lama yang masih aktif
        verification::where('user_id', $user->id)
            ->where('type', 'reset_password')
            ->where('status', 'active')
            ->delete();

        // Generate token unik
        $token = Str::random(32);

        // Simpan token baru ke database verifications
        verification::create([
            'user_id' => $user->id,
            'unique_id' => $token,
            'otp' => '',
            'type' => 'reset_password',
            'send_via' => 'email',
            'status' => 'active'
        ]);

        // TODO: Kirim email dengan link reset password
        // $resetLink = url('/reset_password/' . $token);
        // Mail::send(...) atau menggunakan OtpEmail mailable

        return back()->with('success', 'Link reset password telah dikirim ke email anda.')->with('reset_token', $token);
    }

    public function reset_password_view($token){
        return view('auth.reset_password', compact('token'));
    }

    public function reset_password(Request $request){
        $request->validate([
            'token' => 'required',
            'password' => 'required|max:50|min:8',
            'confirm_password' => 'required|max:50|min:8|same:password',
        ]);

        // Cari verifikasi dengan token yang cocok
        $verification = verification::where('unique_id', $request->token)
            ->where('type', 'reset_password')
            ->where('status', 'active')
            ->first();

        if(!$verification){
            return back()->with('failed', 'Token tidak valid atau telah kadaluarsa.');
        }

        // Update password user
        $user = User::find($verification->user_id);
        $user->password = $request->password;
        $user->save();

        // Update status verifikasi menjadi valid
        $verification->status = 'valid';
        $verification->save();

        return redirect('/login')->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    public function google_redirect(){
        return Socialite::driver('google')->redirect();
    }

    public function google_callback(){
        $googleUser = Socialite::driver('google')->user();
        $user = User::whereEmail($googleUser->email)->first();
        if(!$user){
            $user = User::create(['name' => $googleUser->name, 'email' => $googleUser->email, 'status' => 'active']);
        }
        if($user && $user->status == 'banned'){
            return redirect('/login')->with('failed', 'Akun anda telah di bekukan');
        }
        if($user && $user->status == 'verify'){
            $user->update(['status' => 'active']);
        }
        Auth::login($user);
        if($user->role == 'customer') return redirect('/customer');
        return redirect('/admin');
    }

    public function logout(){
        Auth::logout();
        return redirect('/login');
    }

    public function ban_user($userId){
        $user = User::find($userId);
        if(!$user) return back()->with('failed', 'Pengguna tidak ditemukan.');

        if($user->status == 'banned'){
            return back()->with('failed', 'Pengguna sudah dibekukan.');
        }

        $user->status = 'banned';
        $user->save();

        return back()->with('success', 'Pengguna berhasil dibekukan.');
    }

    public function auto_ban_inactive_users(){
        $inactiveUsers = User::where('status', 'active')
            ->where('updated_at', '<', now()->subMinute())
            ->get();

        foreach ($inactiveUsers as $user) {
            $user->status = 'banned';
            $user->save();
        }

        return response()->json(['message' => 'Pengguna tidak aktif telah dibekukan.']);
    }

    public function auto_logout_inactive_users(){
        $inactiveUsers = User::where('status', 'active')
            ->where('updated_at', '<', now()->subMinutes(3))
            ->get();

        foreach ($inactiveUsers as $user) {
            Auth::logout($user);
        }

        return response()->json(['message' => 'Pengguna tidak aktif telah logout otomatis.']);
    }
}
