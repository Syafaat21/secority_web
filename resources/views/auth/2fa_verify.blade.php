<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Verify 2FA</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="{{asset('adminlte/plugins/fontawesome-free/css/all.min.css')}}">
  <link rel="stylesheet" href="{{asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{asset('adminlte/dist/css/adminlte.min.css')}}">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo"><b>Security Web</b> - 2FA Verify</div>
  <div class="card">
    <div class="card-body login-card-body">

      @if(session('failed'))
        <div class="alert alert-danger">{{ session('failed') }}</div>
      @endif

      <p class="login-box-msg">Enter the 6-digit code from your authenticator app.</p>

      <form action="/2fa" method="post">
        @csrf
        @if(session('failed'))
          <div class="alert alert-danger">{{ session('failed') }}</div>
        @endif
        @if($errors->has('code'))
          <div class="alert alert-danger">{{ $errors->first('code') }}</div>
        @endif
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="input-group mb-3">
          <input type="text" name="code" value="{{ old('code') }}" class="form-control" placeholder="123456">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-key"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8"></div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Verify</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="{{asset('adminlte/plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('adminlte/dist/js/adminlte.min.js')}}"></script>
</body>
</html>
