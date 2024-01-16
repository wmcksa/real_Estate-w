@if(isset($templates['hero'][0]) && $hero = $templates['hero'][0])
    <!-- home section -->
    <section class="home-section">
        <div class="overlay h-100">
            <div class="container h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-xl-7">
                        <div class="text-box">
                            <h4> @lang($hero['description']->title) </h4>
                            <h2>@lang($hero['description']->sub_title) </h2>
                            <h2 class="primary_color">@lang($hero['description']->another_sub_title)</h2>
                            <p>@lang($hero['description']->short_description)</p>
                            <a class="btn-custom mt-4 btn text-white" href="{{ route('property') }}">@lang($hero['description']->button_name)</a>
                        </div>
                    </div>
                    <div class="col-xl-5 d-none d-xl-block">
                        <div class="img-main-wrapper">
                            <div class="img-wrapper">
                                <div class="img-box img-1">
                                    <img src="{{getFile(config('location.content.path').$hero->templateMedia()->image1)}}" alt="" />
                                </div>
                                <div class="img-box img-2">
                                    <img src="{{getFile(config('location.content.path').$hero->templateMedia()->image2)}}" alt="" />
                                </div>
                                <div class="img-box img-3">
                                    <img src="{{getFile(config('location.content.path').$hero->templateMedia()->image3)}}" alt="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
