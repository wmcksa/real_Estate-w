@if(count($myProperties) > 0)
    <div class="col-lg-12">
        <div class="row g-4 mb-5">
            @foreach($myProperties as $key => $property)
                <div class="col-md-4 col-lg-4">
                    <div class="property-box">
                        <div class="img-box">
                            <img class="img-fluid"
                                 src="{{ getFile(config('location.propertyThumbnail.path').optional($property->property)->thumbnail) }}"
                                 alt="@lang('property thumbnail')"/>
                            <div class="content">
                                @if(optional($property->property)->is_invest_type == 0)
                                    <div class="tag">@lang('Fixed Invest')</div>
                                @else
                                    <div class="tag">@lang('Invest Range')</div>
                                @endif

                                <div class="badges">
                                    @if(optional($property->property)->is_installment == 1)
                                        <span class="featured">@lang('Installment facility')</span>
                                    @else
                                        <span class="featured">@lang('No installment')</span>
                                    @endif
                                </div>
                                <h4 class="price">{{ config('basic.currency_symbol') }}{{ (int)$property->amount }}</h4>
                                @if($property->status == 0 && $property->invest_status == 1)
                                    <span class="invest-completed"><i class="fad fa-check-circle"></i> @lang('Running')</span>
                                    @elseif($property->status == 1 && $property->invest_status == 1)
                                    <span class="invest-completed"><i class="fad fa-check-circle text-success"></i> @lang('Completed')</span>
                                @else
                                    <span class="invest-completed"><i class="fad fa-times-circle text-danger"></i> @lang('Due')</span>
                                @endif

                            </div>
                        </div>

                        <div class="text-box">
                            <div class="review">
                                <span>
                                    @php
                                        $isCheck = 0;
                                        $j = 0;
                                    @endphp

                                    @if(optional($property->property)->avgRating() != intval(optional($property->property)->avgRating()))
                                        @php
                                            $isCheck = 1;
                                        @endphp
                                    @endif
                                    @for($i = optional($property->property)->avgRating(); $i > $isCheck; $i--)
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        @php
                                            $j = $j + 1;
                                        @endphp
                                    @endfor
                                    @if(optional($property->property)->avgRating() != intval(optional($property->property)->avgRating()))
                                        <i class="fas fa-star-half-alt"></i>
                                        @php
                                            $j = $j + 1;
                                        @endphp
                                    @endif

                                    @if(optional($property->property)->avgRating() == 0 || optional($property->property)->avgRating() != null)
                                        @for($j; $j < 5; $j++)
                                            <i class="far fa-star"></i>
                                        @endfor
                                    @endif
                                </span>
                                <span>({{ count($property->property->getReviews) <= 1 ? (count($property->property->getReviews). trans(' review')) : (count($property->property->getReviews). trans(' reviews')) }})</span>
                            </div>
                            <a class="title"
                               href="{{ route('propertyDetails',[@slug(optional($property->property->details)->property_title), optional($property->property)->id]) }}">{{ \Illuminate\Support\Str::limit(optional($property->property->details)->property_title, 30)  }}</a>
                            <p class="address">
                                <i class="fas fa-map-marker-alt"></i>
                                @lang(optional($property->property->getAddress->details)->title)
                            </p>

                            <div class="aminities">
                                @foreach(optional($property->property)->limitamenity as $key => $amenity)
                                    <span><i class="{{ $amenity->icon }}"></i>{{ optional($amenity->details)->title  }}</span>
                                @endforeach
                            </div>

                            <div class="invest-btns d-flex justify-content-between">

                                @if($property->propertyShare)
                                    <button type="button" class="btn text-danger">
                                        @lang('Already Shared')
                                    </button>
                                @else
                                    @if(optional($property->property)->is_investor == 1 && config('basic.is_share_investment') == 1)
                                        <button type="button" class="sendOffer btn {{ ($property->invest_status == 1 && $property->status == 1) || ($property->invest_status == 0) || (optional($property->property)->is_investor == 0) ? 'disabled' : '' }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="custom-tooltip"
                                                data-bs-title="@lang('You can sell this investment')"
                                                data-route="{{route('user.propertyShareStore', $property->id)}}"
                                                data-property="{{ optional($property->property->details)->property_title }}">
                                            @lang('Sell Share')
                                        </button>
                                    @else
                                        <button class="opacity-0"></button>
                                    @endif
                                @endif

                                <a href="{{ route('contact') }}">
                                    @lang('Contact Us')
                                </a>
                            </div>

                            <div class="plan d-flex justify-content-between">
                                <div>
                                    @if(optional($property->property)->profit_type == 1)
                                        <h5>{{ (int)optional($property->property)->profit }}% (@lang('Fixed'))</h5>
                                    @else
                                        <h5>{{ config('basic.currency_symbol') }}{{ (int)optional($property->property)->profit }}
                                            (@lang('Fixed'))</h5>
                                    @endif
                                    <span>@lang('Profit Range')</span>
                                </div>
                                <div>
                                    @if(optional($property->property)->is_return_type == 1)
                                        <h5>@lang('Lifetime')</h5>
                                    @else
                                        <h5>{{ optional($property->property->managetime)->time }} @lang(optional($property->property->managetime)->time_type)</h5>
                                    @endif
                                    <span>@lang('Return Interval')</span>
                                </div>
                                <div>
                                    <h5>{{ optional($property->property)->is_capital_back == 1 ? 'Yes' : 'No' }}</h5>
                                    <span>@lang('Capital back')</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                {{ $myProperties->appends($_GET)->links() }}
            </ul>
        </nav>
    </div>
@else
    <div class="custom-not-found mt-5">
        <img src="{{ asset($themeTrue.'img/no_data_found.png') }}" alt="@lang('not found')" class="img-fluid">
    </div>
@endif
