@if(isset($templates['testimonial'][0]) && $testimonial = $templates['testimonial'][0])
    <!-- testimonial section -->
    <section class="testimonial-section">
        <div class="container">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-6">
                    <div class="testimonial-wrapper">
                        <div class="header-text">
                            <h5>@lang(optional($testimonial->description)->title)</h5>
                            <h3>@lang(optional($testimonial->description)->sub_title)</h3>
                            <div class="quote">
                                <img src="{{ asset($themeTrue.'img/quote-2.png') }} " alt=""/>
                            </div>
                        </div>
                        @if(isset($contentDetails['testimonial']))
                            <div class="{{(session()->get('rtl') == 1) ? 'testimonials-rtl': 'testimonials'}} owl-carousel">
                                @foreach($contentDetails['testimonial'] as $key => $data)
                                    <div class="review-box">
                                    <div class="text">
                                        <p>
                                            @lang(optional($data->description)->description)
                                        </p>

                                        <div class="top">
                                            <h4>@lang(optional($data->description)->name)</h4>
                                            <span class="title"
                                            ><a class="organization" href="">@lang(optional($data->description)->designation)</a></span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="client-img">
                        <img src="{{getFile(config('location.content.path').$testimonial->templateMedia()->image)}}" alt="" class="img-fluid"/>

                        <img class="shape" src="{{ asset($themeTrue.'img/dot-square.png') }}" alt="@lang('not found')"/>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

@push('css-lib')
    <link rel="stylesheet" href="{{ asset($themeTrue.'css/owl.carousel.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset($themeTrue.'css/owl.theme.default.min.css') }}"/>
@endpush

@push('extra-js')
    <!-- fancybox slider -->
    <script src="{{ asset($themeTrue.'js/fancybox.umd.js') }}"></script>
@endpush

@push('script')
    <script src="{{ asset($themeTrue.'js/carousel.js') }}"></script>
    <script>
        'use strict'
        $(document).ready(function () {
            // Customize Fancybox
            Fancybox.bind('[data-fancybox="gallery"]', {
                Carousel: {
                    on: {
                        change: (that) => {
                            mainCarousel.slideTo(mainCarousel.findPageForSlide(that.page), {
                                friction: 0,
                            });
                        },
                    },
                },
            });
        });
    </script>
@endpush
