@extends($theme.'layouts.app')
@section('title',__('Reset Password'))


@section('content')
    <!-- Reset Password Section -->
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
                            <form action="{{route('password.update')}}" method="post">
                                @csrf
                                @error('token')
                                <div class="alert alert-danger alert-dismissible fade show w-100" role="alert">
                                    {{ trans($message) }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                @enderror

                                <input type="hidden" name="token" value="{{ $token }}">
                                <input type="hidden" name="email" value="{{ $email }}">

                                <div class="row g-4">
                                    <div class="col-12">
                                        <h4>@lang('Reset Password')</h4>
                                    </div>
                                    <div class="input-box col-12">
                                        <input type="password"
                                               name="password"
                                               class="form-control"
                                               placeholder="@lang('New Password')"
                                               autocomplete="off"/>
                                        @error('password')<span class="text-danger mt-1">{{ trans($message) }}</span>@enderror
                                    </div>
                                    <div class="input-box col-12">
                                        <input type="password"
                                               name="password_confirmation"
                                               class="form-control"
                                               placeholder="@lang('Confirm Password')"
                                               autocomplete="off"/>
                                    </div>
                                    <div class="input-box col-12">
                                        <button class="btn-custom" type="submit">@lang('Submit')</button>
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
