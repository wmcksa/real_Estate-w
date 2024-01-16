@extends($theme.'layouts.app')
@section('title', trans('Investor Details'))

@section('content')
    <!-- Investor profile section -->
    <section class="agent-profile-section">
        <div class="overlay">
            <div class="container">
                <div class="row g-lg-5 g-4">
                    <div class="col-lg-8">
                        <div class="agent-box">
                            <div class="img-box">
                                <img src="{{ asset(getFile(config('location.user.path').$investorInfo->image)) }}"
                                     class="img-fluid profile" alt="@lang('not found')"/>


                                <div
                                    class="property-count"> @lang('Total') {{ optional(optional($properties[0]->getInvestment[0])->user)->countTotalInvestment() == 1 ? trans('Property') : trans('Properties') }} ({{ optional(optional($properties[0]->getInvestment[0])->user)->countTotalInvestment() }}) </div>
                                </div>
                            <div class="text-box">
                                <h4 class="agent-name">@lang($investorInfo->fullname)</h4>
                                <span
                                    class="title"><span>@lang('Member since') {{ $investorInfo->created_at->format('M Y') }}</span></span>
                                <ul>

                                    @if($investorInfo->address)
                                        <li>
                                            <i class="fal fa-map-marker-alt" aria-hidden="true"></i>
                                            <span>@lang($investorInfo->address)</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <!-- agent description -->
                        <div class="agent-description">
                            <div class="navigator">
                                <button tab-id="tab1" class="tab active">@lang('Description')</button>
                                <button tab-id="tab2" class="tab">@lang('Running Properties')
                                    ({{ count($properties) }})
                                </button>
                            </div>
                            <!-- description -->
                            <div id="tab1" class="content active">
                                <p>
                                    @lang(optional(optional($properties[0]->getInvestment[0])->user)->bio)
                                </p>
                            </div>

                            <div id="tab2" class="content">
                                <h4>@lang('Properties')</h4>
                                <div class="row g-4">
                                    @foreach($properties as $key => $property)
                                        <div class="col-12">
                                                @include($theme.'partials.propertyBox')
                                        </div>
                                    @endforeach

                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination justify-content-center">
                                            {{ $properties->appends($_GET)->links() }}
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="side-bar">
                            <div class="side-box">
                                <h4>@lang('Send a Message')</h4>
                                <form action="{{ route('user.sendMessageToPropertyInvestor') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="investor_id" value="{{ optional(optional($properties[0]->getInvestment[0])->user)->id }}">
                                    <div class="row g-3">
                                        <div class="input-box col-12">
                                            <input class="form-control @error('name') is-invalid @enderror" name="name"
                                                   type="text"
                                                   @if(\Auth::check() == true)
                                                       @if(\Auth::user()->id == optional(optional($properties[0]->getInvestment[0])->user)->id)
                                                           placeholder="@lang('Full name')"
                                                   @else
                                                       value="@lang(\Illuminate\Support\Facades\Auth::user()->fullname)"
                                                   @endif
                                                   @else
                                                       placeholder="@lang('Full name')"
                                                @endif/>
                                            <div class="invalid-feedback">
                                                @error('name') @lang($message) @enderror
                                            </div>
                                        </div>

                                        <div class="input-box col-12">
                                            <textarea class="form-control @error('message') is-invalid @enderror"
                                                      cols="30" rows="3" name="message"
                                                      placeholder="@lang('Your message')"></textarea>
                                            <div class="invalid-feedback">
                                                @error('message') @lang($message) @enderror
                                            </div>
                                        </div>

                                        <div class="input-box col-12">
                                            <button class="btn-custom w-100" type="submit">@lang('submit')</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    @push('frontendModal')
        @include($theme.'partials.investNowModal')
    @endpush
@endsection

@push('script')
    <script src="{{ asset($themeTrue.'js/investNow.js') }}"></script>
    <script>
        'use strict'
        var isAuthenticate = '{{ Auth::check() }}';

        $(document).ready(function () {
            $('.wishList').on('click', function () {
                var _this = this.id;
                let property_id = $(this).data('property');
                if (isAuthenticate == 1) {
                    wishList(property_id, _this);
                } else {
                    window.location.href = '{{route('login')}}';
                }
            });
        });

        function wishList(property_id = null, id = null) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('user.wishList') }}",
                type: "POST",
                data: {
                    property_id: property_id,
                },
                success: function (data) {
                    if (data.data == 'added') {
                        $(`.save${id}`).removeClass("fal fa-heart");
                        $(`.save${id}`).addClass("fas fa-heart");
                        Notiflix.Notify.Success("Wishlist added");
                    }
                    if (data.data == 'remove') {
                        $(`.save${id}`).removeClass("fas fa-heart");
                        $(`.save${id}`).addClass("fal fa-heart");
                        Notiflix.Notify.Success("Wishlist removed");
                    }
                },
            });
        }
    </script>
@endpush
