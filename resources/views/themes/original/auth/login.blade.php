@extends($theme.'layouts.app')
@section('title',__('Login'))

@section('content')
    <!-- login section -->
    <section class="login-section">
        <div class="container h-100">
            <div class="row h-100 justify-content-center">
                <div class="col-lg-7">
                    <div class="img-box">
                        <img src="{{ asset($themeTrue.'img/login.png') }}" alt="@lang('login-image')" class="img-fluid" />
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="form-wrapper d-flex align-items-center h-100">
                        <div class="form-box">
                            <form action="{{ route('login') }}" method="post">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-12">
                                        <h4>@lang('Login To Your Account')</h4>
                                    </div>
                                    <div class="input-box col-12">
                                        <input type="text"
                                               name="username"
                                               class="form-control"
                                               placeholder="@lang('Email Or Username')"/>
                                        @error('username')<span class="text-danger float-left">@lang($message)</span>@enderror
                                        @error('email')<span class="text-danger float-left">@lang($message)</span>@enderror
                                    </div>

                                    <div class="input-box col-12">
                                        <input type="hidden"
                                               name="timezone"
                                               class="form-control timezone"
                                               placeholder="@lang('timezone')"/>
                                    </div>

                                    <div class="input-box col-12">
                                        <input type="password"
                                               name="password"
                                               class="form-control"
                                               placeholder="@lang('Password')"/>
                                        @error('password')
                                        <span class="text-danger mt-1">@lang($message)</span>
                                        @enderror
                                    </div>

                                    @if(basicControl()->reCaptcha_status_login)
                                        <div class="box mb-4 form-group">
                                            {!! NoCaptcha::renderJs(session()->get('trans')) !!}
                                            {!! NoCaptcha::display($basic->theme == 'original' ? ['data-theme' => 'dark'] : []) !!}
                                            @error('g-recaptcha-response')
                                            <span class="text-danger mt-1">@lang($message)</span>
                                            @enderror
                                        </div>
                                    @endif


                                    <div class="col-12">
                                        <div class="links">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       name="remember"
                                                       {{ old('remember') ? 'checked' : '' }}
                                                       id="flexCheckDefault"/>
                                                <label class="form-check-label" for="flexCheckDefault"> @lang('Remember me') </label>
                                            </div>
                                            <a href="{{ route('password.request') }}">@lang('Forget password?')</a>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn-custom" type="submit">@lang('Sign in')</button>
                                <div class="bottom">
                                    @lang("Don't have an account?")

                                    <a href="{{ route('register') }}">@lang('Create account')</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        'use strict'
        $(document).ready(function (){
            $('.timezone').val(Intl.DateTimeFormat().resolvedOptions().timeZone);
        });
    </script>
@endpush
