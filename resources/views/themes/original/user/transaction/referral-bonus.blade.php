@extends($theme.'layouts.user')
@section('title',trans($title))
@section('content')

    @push('style')
        <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}" />
    @endpush

<section class="transaction-history">
    <div class="container-fluid">
        <div class="row mt-4 mb-2">
            <div class="col ms-2">
                <div class="header-text-full">
                    <h3 class="dashboard_breadcurmb_heading mb-1">@lang('Referral Bonus')</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('user.home') }}">@lang('Dashboard')</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ trans($title) }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 box">
                <div class="dashboard-box">
                    <h5>@lang('Total Bonus')</h5>
                    <h3><small><sup>{{trans(config('basic.currency_symbol'))}}</sup></small><span>{{ $totalReferralTransaction['total_referral_bonous'] }}</span></h3>
                    <i class="far fa-funnel-dollar text-success " aria-hidden="true"></i>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 box">
                <div class="dashboard-box">
                    <h5>@lang('Joining Bonus')</h5>
                    <h3><small><sup>{{trans(config('basic.currency_symbol'))}}</sup></small><span>{{  array_key_exists("joining_bonus",$totalReferralTransaction) ? $totalReferralTransaction['joining_bonus'] : 0 }}</span></h3>
                    <i class="far fa-sack-dollar text-info" aria-hidden="true"></i>
                </div>
            </div>

            <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 box">
                <div class="dashboard-box">
                    <h5>@lang('Deposit Bonus')</h5>
                    <h3><small><sup>{{trans(config('basic.currency_symbol'))}}</sup></small><span>{{ array_key_exists("deposit",$totalReferralTransaction) ? $totalReferralTransaction['deposit'] : 0 }}</span></h3>
                    <i class="fal fa-usd-circle text-warning" aria-hidden="true"></i>
                </div>
            </div>

            <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 box">
                <div class="dashboard-box">
                    <h5>@lang('Invest Bonus')</h5>
                    <h3><small><sup>{{trans(config('basic.currency_symbol'))}}</sup></small><span>{{ array_key_exists("invest",$totalReferralTransaction) ? $totalReferralTransaction['invest'] : 0 }}</span></h3>
                    <i class="far fa-badge-dollar text-orange" aria-hidden="true"></i>
                </div>
            </div>
            <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 box">
                <div class="dashboard-box">
                    <h5>@lang('Profit Bonus')</h5>
                    <h3><small><sup>{{trans(config('basic.currency_symbol'))}}</sup></small><span>{{ array_key_exists("profit_commission",$totalReferralTransaction) ? $totalReferralTransaction['profit_commission'] : 0 }}</span></h3>
                    <i class="far fa-sack-dollar text-info" aria-hidden="true"></i>
                </div>
            </div>

            <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 box">
                <div class="dashboard-box">
                    <h5>@lang('Badge Bonus')</h5>
                    <h3><small><sup>{{trans(config('basic.currency_symbol'))}}</sup></small><span>{{ array_key_exists("badge_commission",$totalReferralTransaction) ? $totalReferralTransaction['badge_commission'] : 0 }}</span></h3>
                    <i class="far fa-sack-dollar text-info" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <!-- search area -->
        <div class="search-bar mt-4 p-0">
            <form action="{{ route('user.referral.bonus.search') }}" method="get">
                <div class="row g-3 align-items-end">
                    <div class="input-box col-lg-2">
                        <input
                            type="text"
                            name="search_user"
                            value="{{@request()->search_user}}"
                            class="form-control"
                            placeholder="@lang('Search User')"/>
                    </div>

                    <div class="input-box col-lg-2">
                        <input
                            type="text"
                            name="type"
                            value="{{@request()->type}}"
                            class="form-control"
                            placeholder="@lang('Bonus Type')"/>
                    </div>

                    <div class="input-box col-lg-2">
                        <input
                            type="text"
                            name="remark"
                            value="{{@request()->remark}}"
                            class="form-control"
                            placeholder="@lang('Remark')"/>
                    </div>

                    <div class="input-box col-lg-2">
                        <input
                            type="text" class="form-control datepicker from_date" name="from_date" value="{{ old('from_date',request()->from_date) }}" placeholder="@lang('From date')" autocomplete="off" readonly/>
                    </div>

                    <div class="input-box col-lg-2">
                        <input
                            type="text" class="form-control datepicker to_date" name="to_date" value="{{ old('to_date',request()->to_date) }}" placeholder="@lang('To date')" autocomplete="off" readonly disabled="true"/>
                    </div>

                    <div class="input-box col-lg-2">
                        <button class="btn-custom w-100" type="submit"><i class="fal fa-search"></i> @lang('Search') </button>
                    </div>
                </div>
            </form>
        </div>

       <div class="row mt-4">
          <div class="col">
             <div class="table-parent table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">@lang('SL No.')</th>
                            <th scope="col">@lang('Bonus From')</th>
                            <th scope="col">@lang('Amount')</th>
                            <th scope="col">@lang('Remarks')</th>
                            <th scope="col">@lang('Bonus Type')</th>
                            <th scope="col">@lang('Time')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td data-label="@lang('SL')">{{loopIndex($transactions) + $loop->index}}</td>
                                <td data-label="@lang('Bonus From')">{{optional(@$transaction->bonusBy)->fullname}}</td>
                                <td data-label="@lang('Amount')">
                                    <span class="font-weight-bold text-success">{{getAmount($transaction->amount, config('basic.fraction_number')). ' ' . trans(config('basic.currency'))}}</span>
                                </td>
                                <td data-label="@lang('Remarks')"><span>@lang($transaction->remarks)</span></td>
                                <td data-label="@lang('Bonus Type')"><span>@lang($transaction->type)</span></td>
                                <td data-label="@lang('Time')">{{ dateTime($transaction->created_at, 'd M Y h:i A') }}</td>
                            </tr>

                        @empty
                            <tr class="text-center">
                                <td colspan="100%">{{__('No Data Found!')}}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $transactions->appends($_GET)->links($theme.'partials.pagination') }}

             </div>
          </div>
       </div>
    </div>
 </section>

@endsection

@push('script')
    <script src="{{ asset('assets/global/js/bootstrap-datepicker.js') }}"></script>

    <script>
        'use strict'
        $(document).ready(function () {
            $( ".datepicker" ).datepicker({
                autoclose: true,
                clearBtn: true
            });

            $('.from_date').on('change', function (){
                $('.to_date').removeAttr('disabled');
            });
        });
    </script>
@endpush
