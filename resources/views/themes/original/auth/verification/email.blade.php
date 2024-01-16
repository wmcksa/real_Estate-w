@extends($theme.'layouts.app')
@section('title',$page_title)

@section('content')
    <!-- Email Verification Section -->
    <section class="login-section">
        <div class="container h-100">
            <div class="row h-100 justify-content-center">
                <div class="col-lg-7">
                    <div class="img-box">
                        <img src="{{ asset($themeTrue.'img/login.png') }}" alt="@lang('image')" class="img-fluid" />
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="form-wrapper d-flex align-items-center h-100">
                        <div class="form-box">
                            <form action="{{ route('user.mailVerify') }}" method="post">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-12">
                                        <h4>@lang('Enter Your Code')</h4>
                                    </div>
                                    <div class="input-box col-12">
                                        <input type="text"
                                               name="code"
                                               class="form-control"
                                               value="{{old('code')}}"
                                               placeholder="@lang('Enter Your Code')"
                                               autocomplete="off"/>
                                        @error('code')<span class="text-danger mt-1">{{ trans($message) }}</span>@enderror
                                        @error('error')<span class="text-danger mt-1">{{ trans($message) }}</span>@enderror
                                    </div>
                                    <div class="input-box col-12">
                                        <button class="btn-custom" type="submit">@lang('Submit')</button>
                                    </div>
                                    <div class="col-lg-12 text-center mt-4">
                                        <p class="text-center">@lang("Didn't get Code? Click to") <a href="{{route('user.resendCode')}}?type=email"  class="golden-text"> @lang('Resend code')</a></p>

                                        @error('resend')
                                        <p class="text-danger mt-1">{{ trans($message) }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
