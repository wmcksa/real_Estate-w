@extends($theme.'layouts.app')
@section('title')
    @lang($title)
@endsection
@section('content')
    <!-- POLICY -->
    <section class="privacy-policy">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="header-text text-center">
                        <h5>@lang('Welcome To Our')</h5>
                        <h2>@lang(@$title)</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col mb-4 getLink-details">
                    <p>@lang(@$description)</p>
                </div>
            </div>
        </div>
        <div class="shapes">
            <img src="{{ asset($themeTrue.'img/dot-square.png') }}" alt="" class="shape-1"/>
            <img src="{{ asset($themeTrue.'img/dot-square.png') }}" alt="" class="shape-2"/>
        </div>
    </section>
    <!-- /POLICY -->
@endsection
