@if(count($mySharedProperties) > 0)
    <div class="col-lg-12">
        <div class="row g-4 mb-5">
            @foreach($mySharedProperties as $key => $shareProperty)
                <div class="col-md-4 col-lg-4">
                    <div class="property-box">
                        <div class="img-box">
                            <img class="img-fluid"
                                 src="{{ getFile(config('location.propertyThumbnail.path').optional($shareProperty->property)->thumbnail) }}"
                                 alt="@lang('property thumbnail')"/>
                            <div class="content">
                                <div class="tag">@lang('Sell')</div>
                                <h4 class="price">{{ config('basic.currency_symbol') }}{{ (int)$shareProperty->amount }}</h4>
                            </div>
                        </div>

                        <div class="text-box">
                            <div class="review">
                                <span>
                                    @php
                                        $isCheck = 0;
                                        $j = 0;
                                    @endphp

                                    @if(optional($shareProperty->property)->avgRating() != intval(optional($shareProperty->property)->avgRating()))
                                        @php
                                            $isCheck = 1;
                                        @endphp
                                    @endif
                                    @for($i = optional($shareProperty->property)->avgRating(); $i > $isCheck; $i--)
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        @php
                                            $j = $j + 1;
                                        @endphp
                                    @endfor
                                    @if(optional($shareProperty->property)->avgRating() != intval(optional($shareProperty->property)->avgRating()))
                                        <i class="fas fa-star-half-alt"></i>
                                        @php
                                            $j = $j + 1;
                                        @endphp
                                    @endif

                                    @if(optional($shareProperty->property)->avgRating() == 0 || optional($shareProperty->property)->avgRating() != null)
                                        @for($j; $j < 5; $j++)
                                            <i class="far fa-star"></i>
                                        @endfor
                                    @endif
                                </span>

                                <span>({{ count($shareProperty->property->getReviews) <= 1 ? (count($shareProperty->property->getReviews). trans(' review')) : (count($shareProperty->property->getReviews). trans(' reviews')) }})</span>
                            </div>

                            <a class="title"
                               href="{{ route('propertyDetails',[@slug(optional($shareProperty->property->details)->property_title), optional($shareProperty->property)->id]) }}">{{ \Illuminate\Support\Str::limit(optional($shareProperty->property->details)->property_title, 30)  }}</a>
                            <p class="address">
                                <i class="fas fa-map-marker-alt"></i>
                                @lang(optional($shareProperty->property->getAddress->details)->title)
                            </p>

                            <div class="aminities">
                                @foreach(optional($shareProperty->property)->limitamenity as $key => $amenity)
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
                                        <li>
                                            <a class="dropdown-item btn updateShare"
                                               data-route="{{route('user.propertyShareUpdate', $shareProperty->id) }}"
                                               data-amount="{{ $shareProperty->amount}}"
                                               data-property="{{ (optional($shareProperty->property->details)->property_title) }}">
                                                <i class="fal fa-share-alt"></i> @lang('Update Share')
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item btn notiflix-confirm"
                                               data-bs-toggle="modal" data-bs-target="#delete-modal"
                                               data-route="{{route('user.propertyShareRemove', $shareProperty->id)}}">
                                                <i class="fal fa-trash-alt"></i> @lang('Remove')
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <a href="{{ route('contact') }}">
                                    @lang('Contact Us')
                                </a>
                            </div>

                            <div class="plan d-flex justify-content-between">
                                <div>
                                    @if(optional($shareProperty->property)->profit_type == 1)
                                        <h5>{{ (int)optional($shareProperty->property)->profit }}% (@lang('Fixed'))</h5>
                                    @else
                                        <h5>{{ config('basic.currency_symbol') }}{{ (int)optional($shareProperty->property)->profit }}
                                            (@lang('Fixed'))
                                        </h5>
                                    @endif
                                    <span>@lang('Profit Range')</span>
                                </div>
                                <div>
                                    @if(optional($shareProperty->property)->is_return_type == 1)
                                        <h5>@lang('Lifetime')</h5>
                                    @else
                                        <h5>{{ optional($shareProperty->property->managetime)->time }} @lang(optional($shareProperty->property->managetime)->time_type)</h5>
                                    @endif
                                    <span>@lang('Return Interval')</span>
                                </div>
                                <div>
                                    <h5>{{ optional($shareProperty->property)->is_capital_back == 1 ? 'Yes' : 'No' }}</h5>
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
                {{ $sharedProperties->appends($_GET)->links() }}
            </ul>
        </nav>
    </div>
@else
    <div class="custom-not-found mt-5">
        <img src="{{ asset($themeTrue.'img/no_data_found.png') }}" alt="..." class="img-fluid">
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
