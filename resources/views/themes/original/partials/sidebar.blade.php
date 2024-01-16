<!-- sidebar -->
@php
    $user = \Illuminate\Support\Facades\Auth::user();
    $user_badge = \App\Models\Badge::with('details')->where('id', @$user->last_level)->first();
@endphp
<div id="sidebar" class="">
    <div class="sidebar-top">
        <a class="navbar-brand d-none d-lg-block" href="{{url('/')}}"> <img src="{{getFile(config('location.logoIcon.path').'logo.png')}}" alt="{{config('basic.site_title')}}" /></a>
        <div class="mobile-user-area d-lg-none">
            <div class="thumb">
                <img class="img-fluid user-img" src="{{getFile(config('location.user.path').auth()->user()->image)}}" alt="...">
                @if(optional($user->userBadge)->badge_icon)
                    <img src="{{ getFile(config('location.badge.path').optional($user->userBadge)->badge_icon) }}" alt="" class="rank-badge">
                @endif
            </div>
            <div class="content">
                <h5 class="mt-1 mb-1">{{ __(auth()->user()->fullname) }}</h5>
                <span class="">{{ __(auth()->user()->username) }}</span>
                @if(@$user->last_level != null && $user_badge)
                    <p class="text-small mb-0">@lang(optional($user->userBadge->details)->rank_name) - (@lang((optional($user->userBadge->details)->rank_level)))</p>
                @endif
            </div>
        </div>
        <button class="sidebar-toggler d-lg-none" onclick="toggleSideMenu()">
            <i class="fal fa-times"></i>
        </button>
    </div>

    <ul class="main">
        <li>
            <a class="{{menuActive(['user.home'])}}" href="{{ route('user.home') }}"><i class="fal fa-house-flood"></i>@lang('Dashboard')</a>
        </li>

        @php
            $segments = request()->segments();
            $last  = end($segments);
            $propertyMarketSegments = ['investment-properties', 'property-share-market', 'my-investment-properties', 'my-shared-properties', 'my-offered-properties', 'receive-offered-properties', 'offer-conversation'];
        @endphp

        <li>
            <a
                class="dropdown-toggle {{ in_array($last, $propertyMarketSegments) || in_array($segments[1], $propertyMarketSegments) ? 'propertyMarketActive' : '' }}"
                data-bs-toggle="collapse"
                href="#dropdownCollapsible"
                role="button"
                aria-expanded="false"
                aria-controls="collapseExample">
                <i class="fal fa-car-building"></i>@lang('Property Market')
            </a>
            <div class="collapse {{menuActive(['user.propertyMarket','user.offerList', 'user.offerConversation'],4)}} dropdownCollapsible" id="dropdownCollapsible">
                <ul class="">
                    <li>
                        <a class="{{($last == 'investment-properties') ? 'active' : '' }}" href="{{ route('user.propertyMarket', 'investment-properties') }}"><i class="fal fa-sack-dollar"></i>@lang('Investment Properties')</a>
                    </li>
                    @if(config('basic.is_share_investment') == 1)
                        <li>
                            <a class="{{($last == 'property-share-market') ? 'active' : '' }}"  href="{{ route('user.propertyMarket', 'property-share-market') }}"><i class="fal fa-house-return"></i>@lang('Share Market')</a>
                        </li>
                    @endif
                    <li>
                        <a  class="{{($last == 'my-investment-properties') ? 'active' : '' }}" href="{{ route('user.propertyMarket', 'my-investment-properties') }}"><i class="fal fa-building"></i>@lang('My Properties')</a>
                    </li>
                    @if(config('basic.is_share_investment') == 1)
                        <li>
                            <a  class="{{($last == 'my-shared-properties') ? 'active' : '' }}" href="{{ route('user.propertyMarket', 'my-shared-properties') }}"><i class="fal fa-share-alt"></i>@lang('My Shared Properties')</a>
                        </li>
                        <li>
                            <a  class="{{($last == 'my-offered-properties') ? 'active' : '' }}" href="{{ route('user.propertyMarket', 'my-offered-properties') }}"><i class="fal fa-paper-plane"></i>@lang('Send Offer')</a>
                        </li>
                        <li>
                            <a  class="{{($last == 'receive-offered-properties' || request()->routeIs('user.offerList')) ? 'active' : '' }}" href="{{ route('user.propertyMarket', 'receive-offered-properties') }}"><i class="fal fa-bell-on"></i>@lang('Receive Offer')</a>
                        </li>
                    @endif
                </ul>
            </div>
        </li>

        <li>
            <a class="{{menuActive(['user.invest-history'])}}" href="{{route('user.invest-history')}}"><i class="fal fa-history"></i>@lang('Invest History')</a>
        </li>

        <li>
            <a class="{{menuActive(['user.wishListProperty'])}}" href="{{ route('user.wishListProperty') }}"><i class="fal fa-heart"></i>@lang('WishList')</a>
        </li>

        <li>
            <a class="{{menuActive(['user.addFund'])}}" href="{{route('user.addFund')}}"><i class="fal fa-funnel-dollar"></i>@lang('Add Fund')</a>
        </li>

        <li>
            <a class="{{menuActive(['user.fund-history', 'user.fund-history.search'])}}" href="{{route('user.fund-history')}}"><i class="fal fa-file-invoice-dollar"></i>@lang('Fund History')</a>
        </li>


        <li>
            <a class="{{menuActive(['user.money-transfer'])}}" href="{{route('user.money-transfer')}}"><i class="fal fa-exchange-alt"></i>@lang('Money Transfer')</a>
        </li>

        <li>
            <a class="{{menuActive(['user.transaction', 'user.transaction.search'])}}" href="{{route('user.transaction')}}"><i class="fal fa-money-check-alt"></i>@lang('Transaction')</a>
        </li>

        <li>
            <a class="{{menuActive(['user.payout.money','user.payout.preview'])}}" href="{{route('user.payout.money')}}"><i class="fal fa-credit-card"></i>@lang('Payout')</a>
        </li>
        <li>
            <a class="{{menuActive(['user.payout.history','user.payout.history.search'])}}" href="{{route('user.payout.history')}}"><i class="fal fa-usd-square"></i>@lang('Payout History')</a>
        </li>

        <li>
            <a class="{{menuActive(['user.referral'])}}" href="{{route('user.referral')}}"><i class="fal fa-sync"></i>@lang('My Referral')</a>
        </li>
        <li>
            <a class="{{menuActive(['user.referral.bonus', 'user.referral.bonus.search'])}}" href="{{route('user.referral.bonus')}}"><i class="fal fa-hand-holding-usd"></i>@lang('Referral Bonus')</a>
        </li>

        <li>
            <a class="{{menuActive(['user.badges'])}}" href="{{route('user.badges')}}"><i class="fal fa-badge-check"></i>@lang('Badges')</a>
        </li>

        <li>
            <a class="{{menuActive(['user.ticket.list', 'user.ticket.create', 'user.ticket.view'])}}" href="{{route('user.ticket.list')}}"><i class="fal fa-ticket"></i>@lang('Support ticket')</a>
        </li>

        <li class="d-lg-none">
            <a href="{{route('user.twostep.security')}}">
                <i class="fal fa-lock"></i> @lang('2FA Security')
            </a>
        </li>

        <li class="d-lg-none">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fal fa-sign-out-alt"></i> @lang('Logout')
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</div>
