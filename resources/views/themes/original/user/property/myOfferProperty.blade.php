@if(count($myOfferedProperties) > 0)
    <div class="col-lg-12">
        <div class="row g-4 mb-5">
            @foreach($myOfferedProperties as $key => $offerProperty)
                <div class="col-md-4 col-lg-4">
                    <div class="property-box">
                        <div class="img-box">
                            <img class="img-fluid"
                                 src="{{ getFile(config('location.propertyThumbnail.path').optional($offerProperty->property)->thumbnail) }}"
                                 alt="@lang('property thumbnail')"/>
                            <div class="content">
                                <div class="tag">@lang('Buy')</div>
                                <div class="badges">
                                    @if($offerProperty->status == 0 && $offerProperty->payment_status == 0)
                                        <span class="warning">@lang('Offer Pending')</span>
                                    @elseif($offerProperty->status == 1 && $offerProperty->payment_status == 0)
                                        <span class="success">@lang('Offer Accepted')</span>
                                    @elseif($offerProperty->status == 2 && $offerProperty->payment_status == 0)
                                        <span class="danger">@lang('Offer Rejected')</span>
                                    @elseif($offerProperty->status == 1 && $offerProperty->payment_status == 1)
                                        <span class="featured">@lang('Offer Completed')</span>
                                    @endif
                                </div>
                                <h4 class="price">{{ config('basic.currency_symbol') }}{{ (int)optional($offerProperty->propertyShare)->amount }}</h4>
                            </div>
                        </div>

                        <div class="text-box">
                            <div class="review">
                                <span>
                                    @php
                                        $isCheck = 0;
                                        $j = 0;
                                    @endphp

                                    @if(optional($offerProperty->property)->avgRating() != intval(optional($offerProperty->property)->avgRating()))
                                        @php
                                            $isCheck = 1;
                                        @endphp
                                    @endif
                                    @for($i = optional($offerProperty->property)->avgRating(); $i > $isCheck; $i--)
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        @php
                                            $j = $j + 1;
                                        @endphp
                                    @endfor
                                    @if(optional($offerProperty->property)->avgRating() != intval(optional($offerProperty->property)->avgRating()))
                                        <i class="fas fa-star-half-alt"></i>
                                        @php
                                            $j = $j + 1;
                                        @endphp
                                    @endif

                                    @if(optional($offerProperty->property)->avgRating() == 0 || optional($offerProperty->property)->avgRating() != null)
                                        @for($j; $j < 5; $j++)
                                            <i class="far fa-star"></i>
                                        @endfor
                                    @endif
                                </span>

                                <span>({{ count($offerProperty->property->getReviews) <= 1 ? (count($offerProperty->property->getReviews). trans(' review')) : (count($offerProperty->property->getReviews). trans(' reviews')) }})</span>
                            </div>

                            <a class="title"
                               href="{{ route('propertyDetails',[slug(optional(optional($offerProperty->property)->details)->property_title), optional($offerProperty->property)->id]) }}">{{ Str::limit(optional(optional($offerProperty->property)->details)->property_title, 30)  }}</a>
                            <p class="address">
                                <i class="fas fa-map-marker-alt"></i>
                                @lang(optional(optional(optional($offerProperty->property)->getAddress)->details)->title)
                            </p>

                            <div class="aminities">
                                @foreach(optional($offerProperty->property)->limitamenity as $key => $amenity)
                                    <span><i class="{{ $amenity->icon }}"></i>{{ optional($amenity->details)->title  }}</span>
                                @endforeach
                            </div>


                            <div class="invest-btns d-flex justify-content-between">
                                <div class="sidebar-dropdown-items">
                                    <button
                                        type="button"
                                        class="dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fal fa-cog"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-right">
                                        @if($offerProperty->status == 0 && optional($offerProperty->propertyShare)->status == 1)
                                            <li>
                                                <a class="dropdown-item btn updateOffer"
                                                   data-route="{{route('user.propertyOfferUpdate', $offerProperty->id)}}"
                                                   data-owner="{{ optional($offerProperty->owner)->fullname }}"
                                                   data-amount="{{ $offerProperty->amount }}"
                                                   data-details="{{ $offerProperty->description }}"
                                                   data-property="{{ (optional(optional($offerProperty->property)->details)->property_title) }}">
                                                    <i class="fal fa-paper-plane"></i> @lang('Update Offer')
                                                </a>
                                            </li>
                                        @elseif(optional($offerProperty->propertyShare)->status == 0 && $offerProperty->payment_status == 0)
                                            <li>
                                                <a class="dropdown-item btn disabled">
                                                    <i class="fal fa-shopping-cart"></i> @lang('Sold out')
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item" href="{{ route('user.offerConversation', $offerProperty->id) }}">
                                                <i class="fal fa-envelope" aria-hidden="true"></i> @lang('Conversation')
                                            </a>
                                        </li>
                                        @if($offerProperty->status == 2 || (optional($offerProperty->propertyShare)->status == 0 && $offerProperty->payment_status == 0))
                                            <li>
                                                <a class="dropdown-item btn notiflix-confirm"
                                                   data-bs-toggle="modal" data-bs-target="#delete-modal"
                                                   data-route="{{route('user.propertyOfferRemove', $offerProperty->id)}}">
                                                    <i class="fal fa-trash-alt"></i> @lang('Remove')
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                <a href="{{ route('investorProfile', [slug(optional($offerProperty->owner)->username), optional($offerProperty->owner)->id]) }}" target="_blank">
                                    @lang('Contact Owner')
                                </a>
                            </div>

                            <div class="plan d-flex justify-content-between">
                                <div>
                                    @if(optional($offerProperty->property)->profit_type == 1)
                                        <h5>{{ (int)optional($offerProperty->property)->profit }}% (@lang('Fixed'))</h5>
                                    @else
                                        <h5>{{ config('basic.currency_symbol') }}{{ (int)optional($offerProperty->property)->profit }}
                                            (@lang('Fixed'))
                                        </h5>
                                    @endif
                                    <span>@lang('Profit Range')</span>
                                </div>
                                <div>
                                    @if(optional($offerProperty->property)->is_return_type == 1)
                                        <h5>@lang('Lifetime')</h5>
                                    @else
                                        <h5>{{ optional(optional($offerProperty->property)->managetime)->time }} @lang(optional(optional($offerProperty->property)->managetime)->time_type)</h5>
                                    @endif
                                    <span>@lang('Return Interval')</span>
                                </div>
                                <div>
                                    <h5>{{ optional($offerProperty->property)->is_capital_back == 1 ? 'Yes' : 'No' }}</h5>
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
                {{ $myOfferedProperties->appends($_GET)->links() }}
            </ul>
        </nav>
    </div>
@else
    <div class="custom-not-found mt-5">
        <img src="{{ asset($themeTrue.'img/no_data_found.png') }}" alt="@lang('not found')" class="img-fluid">
    </div>
@endif

@push('loadModal')
    {{--  Remove Share modal --}}
    <div class="modal fade" id="delete-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">@lang('Remove Confirmation')</h5>
                    <button type="button" class="close-btn close_invest_modal" data-bs-dismiss="modal"
                            aria-label="Close">
                        <i class="fal fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <p>@lang('Are you sure to remove this?')</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-custom btn2 btn-secondary close_invest_modal close__btn"
                            data-bs-dismiss="modal">@lang('Close')</button>
                    <form action="" method="post" class="deleteRoute">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn-custom">@lang('Remove')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('script')
    <script>
        'use strict'
        $('.notiflix-confirm').on('click', function () {
            var route = $(this).data('route');
            $('.deleteRoute').attr('action', route)
        })
    </script>
@endpush
