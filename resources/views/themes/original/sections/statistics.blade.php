@if(isset($templates['statistics'][0]) && $statistic = $templates['statistics'][0])
    <!-- commission section -->
    <section class="commission-section">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="header-text text-center">
                        <h5>@lang(optional($statistic->description)->title)</h5>
                        <h2>@lang(optional($statistic->description)->sub_title)</h2>
                    </div>
                </div>
            </div>

            @if(isset($contentDetails['statistics']))
                @if(count($contentDetails['statistics']) > 0)
                    <div class="row g-4 g-lg-5">
                        @foreach($contentDetails['statistics'] as $k => $data)
                        <div class="col-md-6 col-lg-3 box">
                            <div
                                class="commission-box {{(session()->get('rtl') == 1) ? 'isRtl': 'noRtl'}}"
                                data-aos="zoom-in"
                                data-aos-duration="800"
                                data-aos-anchor-placement="center-bottom">
                                <h3>@lang(optional($data->description)->title)</h3>
                                <h5>@lang(optional($data->description)->description)</h5>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            @endif

        </div>
        <div class="shapes">
            <img src="{{ asset($themeTrue.'img/dot-square.png') }}" alt="" class="shape-1"/>
            <img src="{{ asset($themeTrue.'img/dot-square.png') }}" alt="" class="shape-2"/>
        </div>
    </section>
@endif
