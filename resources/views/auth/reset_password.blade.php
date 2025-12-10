<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Security Web | Reset Password</title>

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
<div class="login-box">
  <div class="login-logo">
    <b>Security Password</b>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">You are only one step a way from your new password, recover your password now.</p>

      <form action="/reset_password" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" id="password">
          <div class="input-group-append show-password" style="cursor: pointer;">
            <div class="input-group-text">
              <span class="fas fa-lock" id="password-lock"></span>
            </div>
          </div>
          @error('password')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="input-group mb-3">
          <input type="password" name="confirm_password" class="form-control @error('confirm_password') is-invalid @enderror" placeholder="Confirm Password" id="confirm-password">
          <div class="input-group-append show-confirm-password" style="cursor: pointer;">
            <div class="input-group-text">
              <span class="fas fa-lock" id="confirm-password-lock"></span>
            </div>
          </div>
          @error('confirm_password')
            <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Change password</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      @if($errors->any())
        <div class="alert alert-danger fade show mt-3" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <p class="mt-3 mb-1">
        <a href="/login">Login</a>
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
