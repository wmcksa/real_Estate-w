@extends($theme.'layouts.app')
@section('title','404')

@section('content')
    <section class="error-page wow fadeInUp mb-5 pb-5">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-md-12 text-center my-5 text-box">
                    <img src="{{ asset($themeTrue.'img/error-404.png') }}" alt="@lang('not found')" />
                    <h1 class="golden-text opps d-block">{{trans('Opps!')}}</h1>
                    <div class="sub_title golden-text mb-4 lead">{{trans("We can't seem to find the page you are looking for")}}</div>
                    <a class="gold-btn btn-custom" href="{{url('/')}}" >@lang('Back To Home')</a>
                </div>
            </div>
        </div>
    </section>
@endsection
