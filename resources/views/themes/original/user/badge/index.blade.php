@extends($theme.'layouts.user')
@section('title', 'badges')

@section('content')
    <section class="payment-gateway">
        <div class="container-fluid">
            <div class="row mt-4 mb-2">
                <div class="col ms-2">
                    <div class="header-text-full">
                        <h3 class="dashboard_breadcurmb_heading mb-1">@lang('All Badges')</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('user.home') }}">@lang('Dashboard')</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">@lang('Badges')</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            @if(count($allBadges) > 0)
                <div class="badge-box-wrapper">
                    <div class="row g-4 mb-4">
                        @foreach($allBadges as $key => $badge)
                            <div class="col-xl-4 col-md-4 col-12 box">
                                <div class="badge-box {{ Auth::user()->ranking($badge->id, $badge->min_deposit) == 'true' ? '' : 'locked' }}">
                                    <img src="{{ getFile(config('location.badge.path').$badge->badge_icon) }}" alt="" />
                                    <h3>@lang(optional($badge->details)->rank_level)</h3>
                                    <p class="mb-3">@lang(optional($badge->details)->rank_name)</p>
                                    <div class="text-start">
                                        <h5>@lang('Minimum Invest'): <span>{{ $basic->currency_symbol }}{{ $badge->min_invest }}</span></h5>
                                        <h5>@lang('Minimum Deposit'): <span>{{ $basic->currency_symbol }}{{ $badge->min_deposit }}</span></h5>
                                        <h5>@lang('Minimum Earning'): <span>{{ $basic->currency_symbol }}{{ $badge->min_earning }}</span></h5>
                                        <h5>@lang('Bonus'): <span>{{ $basic->currency_symbol }}{{ $badge->bonus }}</span></h5>
                                    </div>
                                    <div class="lock-icon">
                                        <i class="far fa-lock-alt"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </section>
@endsection

