@extends('vendor.layout.auth')
@section('content')
<style type="text/css">
    .field-icon {
        float: right;
        margin-right: 20px;
        margin-top: -26px;
        z-index: 2;
    }
</style>
<div class="card-body">
    <div class="card-content p-2">
        <div class="text-center">
            <img src="{{asset('public/wb/img/logo/logo2.png')}}" alt="logo icon">
        </div>
        <div class="card-title text-uppercase text-center py-3">Customer Sign In</div>

        <form class="form-horizontal" method="POST" action="{{ url('/w2bcustomer/login') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="email" class="sr-only">E-Mail Address</label>
                <div class="position-relative has-icon-right">
                    <input type="email" id="email" class="form-control input-shadow {{ $errors->has('email') ? 'error' : '' }}" name="email" value="{{ old('email') }}" placeholder="E-Mail Address" autofocus>
                    <div class="form-control-position">
                        <i class="icon-user"></i>
                    </div>
                    @if ($errors->has('email'))
                        <label class="error">{{ $errors->first('email') }}</label>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="sr-only">Password</label>
                <div class="position-relative has-icon-right">
                    <input type="password" id="password" class="form-control input-shadow {{ $errors->has('password') ? 'error' : '' }}" name="password" placeholder="Password">
                    <span toggle="#password" class="fa fa-eye field-icon toggle-password"></span>
                    <!-- <div class="form-control-position">
                        <i class="icon-lock"></i>
                    </div> -->
                    @if ($errors->has('password'))
                        <label class="error">{{ $errors->first('password') }}</label>
                    @endif
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-6">
                    <div class="icheck-material-primary">
                        <input type="checkbox" id="user-checkbox" name="remember" checked="" />
                        <label for="user-checkbox">Remember me</label>
                    </div>
                </div>
                <div class="form-group col-6 text-right">
                    <a href="{{ url('/vendor/password/reset') }}">Reset Password</a>
                </div>
            </div>
        <button type="submit" class="btn btn-primary btn-block w-100 m-auto">Sign In</button>

        </form>
        <div class='w-100 my-2 mx-auto d-flex '>
            <button type="button" onclick="window.location='{{ url("w2bcustomer/auth/google") }}'" style="background-color:#ee7322;color:#fff !important;" class="btn btn-block w-50">Login with Google</button>
            <button type="button" onclick="window.location='{{ url("w2bcustomer/auth/fb") }}'" class="btn btn-primary ml-1 w-50">Login with Facebook</button>
        </div>
        <hr>
        <a href="{{url('/')}}" style="background-color:#ee7322;color:#fff !important;" class="btn btn-block">Home</a>
        <div class="form-row mt-2">
            <div class="form-group col-12" style="text-align: center;">
               Don't have an account? <br>
               <a href="{{ url('/w2bcustomer/register') }}">Sign up as Customer  | </a>
               <a class='ml-2' href="{{ url("/vendor-signup") }}">Sign up as Seller  | </a>
               <a class='ml-2' href="{{ url("/supplier/signup") }}">Sign up as Supplier</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $(".toggle-password").click(function() {

            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    });
</script>
@endsection
