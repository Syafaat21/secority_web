<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Security Web | Register</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('adminlte/dist/css/adminlte.min.css')}}">
</head>
<body class="hold-transition login-page">
<div class="login-box mb-3">
  <div class="login-logo">
    <b>Security Web</b> - Register
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('failed'))
            <div class="alert alert-danger">{{ session('failed') }}</div>
        @endif

      <p class="login-box-msg">Register here</p>

      <form action="/register" method="post">
        @csrf
        @error('name')
            <small class="text-danger" style="display: block; margin-bottom: 0.5rem;">{{ $message }}</small>
        @enderror
        <div class="input-group mb-3">
          <input type="text" name="name" class="form-control" placeholder="Name" value="{{ old('name') }}">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        @error('email')
            <small class="text-danger" style="display: block; margin-bottom: 0.5rem;">{{ $message }}</small>
        @enderror
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        @error('password')
            <small class="text-danger" style="display: block; margin-bottom: 0.5rem;">{{ $message }}</small>
        @enderror
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" id="password">
          <div class="input-group-append show-password" style="cursor: pointer;">
            <div class="input-group-text">
              <span class="fas fa-lock" id="password-lock"></span>
            </div>
          </div>
        </div>
        @error('confirm_password')
            <small class="text-danger" style="display: block; margin-bottom: 0.5rem;">{{ $message }}</small>
        @enderror
        <div class="input-group mb-3">
          <input type="password" name="confirm_password" class="form-control" placeholder="Password Confirmation" id="confirm-password">
          <div class="input-group-append show-confirm-password" style="cursor: pointer;">
            <div class="input-group-text">
              <span class="fas fa-lock" id="confirm-password-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">

          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Register</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <div class="social-auth-links text-center mb-3">
        <p>- OR -</p>
        <a href="#" class="btn btn-block btn-primary">
          <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
        </a>
        <a href="/auth-google-redirect" class="btn btn-block btn-danger">
          <i class="fab fa-google mr-2"></i> Sign in using Google
        </a>
      </div>
      <!-- /.social-auth-links -->

      <p class="mb-1">
        already have an account? <a href="/login" class="text-center">Login here</a>
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{asset('adminlte/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('adminlte/dist/js/adminlte.min.js')}}"></script>

<script>
    $('.show-password').on('click', function() {
        if($('#password').attr('type') == 'password') {
            $('#password').attr('type', 'text');
            $('#password-lock').attr('class', 'fas fa-unlock');
        } else {
            $('#password').attr('type', 'password');
            $('#password-lock').attr('class', 'fas fa-lock');
        }
    })
    $('.show-confirm-password').on('click', function() {
        if($('#confirm-password').attr('type') == 'password') {
            $('#confirm-password').attr('type', 'text');
            $('#confirm-password-lock').attr('class', 'fas fa-unlock');
        } else {
            $('#confirm-password').attr('type', 'password');
            $('#confirm-password-lock').attr('class', 'fas fa-lock');
        }
    })
</script>
</body>
</html>
