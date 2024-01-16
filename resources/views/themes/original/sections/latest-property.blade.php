@if(isset($templates['latest-property'][0]) && $latestProperty = $templates['latest-property'][0])
    <!-- latest property -->
    @if(count($latestProperties) > 0)
        <section class="latest-property">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="header-text text-center">
                            <h5>@lang(optional($latestProperty->description)->title)</h5>
                            <h2>@lang(optional($latestProperty->description)->sub_title)</h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @foreach($latestProperties as $key => $property)
                            <div class="col-lg-6 mb-4">
                                @include($theme.'partials.propertyBox')
                            </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endif
