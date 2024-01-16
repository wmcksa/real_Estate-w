@if(count($receivedOfferedList) > 0)
    <div class="col-lg-12">
        <div class="row g-4 mb-5">
            @foreach($receivedOfferedList as $key => $offerList)
                <div class="col-md-4 col-lg-4">
                    <div class="property-box">
                        <div class="img-box">
                            <img class="img-fluid"
                                 src="{{ getFile(config('location.propertyThumbnail.path').optional($offerList->property)->thumbnail) }}"
                                 alt="@lang('property thumbnail')"/>
                            <div class="content">
                                <div class="tag">@lang('Sell')</div>
                                <h4 class="price">{{ config('basic.currency_symbol') }}{{ (int)optional($offerList->propertyShare)->amount }}</h4>
                            </div>
                        </div>

                        <div class="text-box">
                            <div class="review">
                                <span>
                                    @php
                                        $isCheck = 0;
                                        $j = 0;
                                    @endphp

                                    @if(optional($offerList->property)->avgRating() != intval(optional($offerList->property)->avgRating()))
                                        @php
                                            $isCheck = 1;
                                        @endphp
                                    @endif
                                    @for($i = optional($offerList->property)->avgRating(); $i > $isCheck; $i--)
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        @php
                                            $j = $j + 1;
                                        @endphp
                                    @endfor
                                    @if(optional($offerList->property)->avgRating() != intval(optional($offerList->property)->avgRating()))
                                        <i class="fas fa-star-half-alt"></i>
                                        @php
                                            $j = $j + 1;
                                        @endphp
                                    @endif

                                    @if(optional($offerList->property)->avgRating() == 0 || optional($offerList->property)->avgRating() != null)
                                        @for($j; $j < 5; $j++)
                                            <i class="far fa-star"></i>
                                        @endfor
                                    @endif
                                </span>
                                <span>({{ count(optional($offerList->property)->getReviews) <= 1 ? (count(optional($offerList->property)->getReviews). trans(' review')) : (count(optional($offerList->property)->getReviews). trans(' reviews')) }})</span>
                            </div>

                            <a class="title"
                               href="{{ route('propertyDetails',[slug(optional($offerList->property->details)->property_title), optional($offerList->property)->id]) }}">{{ Str::limit(optional($offerList->property->details)->property_title, 30)  }}</a>
                            <p class="address">
                                <i class="fas fa-map-marker-alt"></i>
                                @lang(optional($offerList->property->getAddress->details)->title)
                            </p>

                            <div class="aminities">
                                @foreach(optional($offerList->property)->limitamenity as $key => $amenity)
                                    <span><i class="{{ $amenity->icon }}"></i>{{ optional($amenity->details)->title  }}</span>
                                @endforeach
                            </div>



                            <div class="invest-btns d-flex justify-content-between">
                                <a class="btn" href="{{ route('user.offerList', $offerList->property_share_id) }}">
                                    @lang('Offer List') <span class="badge bg-secondary">{{ $offerList->totalOfferList($offerList->property_share_id) }}</span>
                                </a>

                                <a href="{{ route('contact') }}">
                                    @lang('Contact Us')
                                </a>
                            </div>

                            <div class="plan d-flex justify-content-between">
                                <div>
                                    @if(optional($offerList->property)->profit_type == 1)
                                        <h5>{{ (int)optional($offerList->property)->profit }}% (@lang('Fixed'))</h5>
                                    @else
                                        <h5>{{ config('basic.currency_symbol') }}{{ (int)optional($offerList->property)->profit }}
                                            (@lang('Fixed'))
                                        </h5>
                                    @endif
                                    <span>@lang('Profit Range')</span>
                                </div>
                                <div>
                                    @if(optional($offerList->property)->is_return_type == 1)
                                        <h5>@lang('Lifetime')</h5>
                                    @else
                                        <h5>{{ optional($offerList->property->managetime)->time }} @lang(optional($offerList->property->managetime)->time_type)</h5>
                                    @endif
                                    <span>@lang('Return Interval')</span>
                                </div>
                                <div>
                                    <h5>{{ optional($offerList->property)->is_capital_back == 1 ? 'Yes' : 'No' }}</h5>
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
                {{ $receivedOfferedList->appends($_GET)->links() }}
            </ul>
        </nav>
    </div>
@else
    <div class="custom-not-found mt-5">
        <img src="{{ asset($themeTrue.'img/no_data_found.png') }}" alt="..." class="img-fluid">
    </div>
@endif
