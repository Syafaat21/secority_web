<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Setup 2FA</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="{{asset('adminlte/plugins/fontawesome-free/css/all.min.css')}}">
  <link rel="stylesheet" href="{{asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{asset('adminlte/dist/css/adminlte.min.css')}}">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo"><b>Security Web</b> - 2FA Setup</div>
  <div class="card">
    <div class="card-body login-card-body">

      @if(session('failed'))
        <div class="alert alert-danger">{{ session('failed') }}</div>
      @endif

      <p class="login-box-msg">Scan the QR code with Google Authenticator, or enter the secret key manually.</p>

      <div class="text-center mb-3">
        <img src="{{ $qrUrl }}" alt="QR Code">
      </div>
      <p><strong>Secret key:</strong> <code>{{ $secret }}</code></p>

      <form action="/2fa/setup" method="post">
        @csrf
        @if($errors->has('code'))
            <div class="alert alert-danger">{{ $errors->first('code') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="input-group mb-3">
          <input type="text" name="code" value="{{ old('code') }}" class="form-control" placeholder="Enter code from app">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-key"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8"></div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Confirm</button>
          </div>
        </div>
      </form>

      <p class="mt-3">Need help? Install <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Google Authenticator</a> or similar app and add account using the secret or QR.</p>

    </div>
  </div>
</div>

<script src="{{asset('adminlte/plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('adminlte/dist/js/adminlte.min.js')}}"></script>
</body>
</html>
