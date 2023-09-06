@extends('vendor.layout.main')
@section('content')

 <div class="i_funds_body py-5 d-flex justify-content-center">
    <div class="container_withdraw">
        <div class="inner-container">
            <div class="mx-auto">
                <div class="main_section p-4 mb-4">
                <div class="d-flex justify-content-center w-100">
                  <img class='tick_pic' src="https://static.vecteezy.com/system/resources/previews/025/139/940/original/green-tick-icon-free-png.png" alt="image">
                </div>
                <h1 class="ty_heading text-center">Thank You!</h1>
                <div class="wrapper-2 text-center">
                    <p>Thanks for buying products.</p>
                    <p>you will receive email soon</p>
                    <button class="go-home">
                        <a href="{{ route('vendor.marketplace-page') }}">
                        go back
                        </a>
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

@endsection
