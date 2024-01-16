@extends($theme.'layouts.user')
@section('title', trans($title))

@section('content')

<!-- investment plans -->
<section class="payment-gateway">
    <div class="container-fluid">
        <div class="row mt-4 mb-2">
            <div class="col ms-2">
                <div class="header-text-full">
                    <h3 class="dashboard_breadcurmb_heading mb-1">@lang('Payout Money')</h3>
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

       <div class="row">
            @foreach($gateways as $key => $gateway)
                <div class="col-lg-2 col-md-3 col-sm-6 mb-4">
                    <div class="gateway-box">
                        <img
                            class="img-fluid gateway"
                            src="{{ getFile(config('location.withdraw.path').$gateway->image)}}"
                            alt="{{$gateway->name}}"
                        >
                            <button type="button"
                                    data-id="{{$gateway->id}}"
                                    data-name="{{$gateway->name}}"
                                    data-min_amount="{{getAmount($gateway->minimum_amount, $basic->fraction_number)}}"
                                    data-max_amount="{{getAmount($gateway->maximum_amount,$basic->fraction_number)}}"
                                    data-percent_charge="{{getAmount($gateway->percent_charge,$basic->fraction_number)}}"
                                    data-fix_charge="{{getAmount($gateway->fixed_charge, $basic->fraction_number)}}"

                                    @if($payoutSettings->saturday == 0 && $today == 'saturday')
                                        data-bs-toggle="modal" data-bs-target="#payoutOffDayModal"
                                        data-offday="{{ $today }}"
                                        data-days="{{ $payoutSettings }}"
                                        class="btn-custom notifyOffDay addFundCustomButton"
                                    @elseif($payoutSettings->sunday == 0 && $today == 'sunday')
                                        data-bs-toggle="modal" data-bs-target="#payoutOffDayModal"
                                        data-offday="{{ $today }}"
                                        data-days="{{ $payoutSettings }}"
                                    class="btn-custom notifyOffDay addFundCustomButton"
                                    @elseif($payoutSettings->monday == 0 && $today == 'monday')
                                        data-bs-toggle="modal" data-bs-target="#payoutOffDayModal"
                                        data-offday="{{ $today }}"
                                        data-days="{{ $payoutSettings }}"
                                        class="btn-custom notifyOffDay addFundCustomButton"
                                    @elseif($payoutSettings->tuesday == 0 && $today == 'tuesday')
                                        data-bs-toggle="modal" data-bs-target="#payoutOffDayModal"
                                        data-offday="{{ $today }}"
                                        data-days="{{ $payoutSettings }}"
                                        class="btn-custom notifyOffDay addFundCustomButton"
                                    @elseif($payoutSettings->wednesday == 0 && $today == 'wednesday')
                                        data-bs-toggle="modal" data-bs-target="#payoutOffDayModal"
                                        data-offday="{{ $today }}"
                                        data-days="{{ $payoutSettings }}"
                                        class="btn-custom notifyOffDay addFundCustomButton"
                                    @elseif($payoutSettings->thursday == 0 && $today == 'thursday')
                                        data-bs-toggle="modal" data-bs-target="#payoutOffDayModal"
                                        data-offday="{{ $today }}"
                                        data-days="{{ $payoutSettings }}"
                                        class="btn-custom notifyOffDay addFundCustomButton"
                                    @elseif($payoutSettings->friday == 0 && $today == 'friday')
                                        data-bs-toggle="modal" data-bs-target="#payoutOffDayModal"
                                        data-offday="{{ $today }}"
                                        data-days="{{ $payoutSettings }}"
                                        class="btn-custom notifyOffDay addFundCustomButton"
                                    @else
                                        data-bs-toggle="modal" data-bs-target="#addFundModal"
                                        class="addFundCustomButton addFund"
                                    @endif>
                                @lang('PAYOUT NOW')
                            </button>
                    </div>
                </div>
            @endforeach
       </div>
    </div>
</section>


    @push('loadModal')
        <div class="modal fade addFundModal" id="addFundModal" tabindex="-1" aria-labelledby="planModalLabel" data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title method-name" id="planModalLabel"></h4>
                        <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fal fa-times"></i>
                        </button>
                    </div>
                    <form action="{{route('user.payout.moneyRequest')}}" method="post">
                        @csrf
                    <div class="modal-body">
                        <div class="payment-form">
                            <p class="depositLimit"></p>
                            <p class="depositCharge"></p>


                                <div class="form-group mb-30 mt-3">
                                    <div class="box">
                                        <h5 class="text-dark">@lang('Select Wallet')</h5>

                                        <div class="input-group input-box">
                                            <select class="form-select" aria-label="Default select example" name="wallet_type">
                                                <option
                                                    value="balance" class="text-dark">@lang('Deposit Balance - '.$basic->currency_symbol.getAmount(auth()->user()->balance))</option>
                                                <option value="interest_balance" class="text-dark">@lang('Interest Balance -'.$basic->currency_symbol.getAmount(auth()->user()->interest_balance))</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-30 mt-3">
                                    <div class="box">
                                        <h5 class="text-dark">@lang('Amount')</h5>
                                        <div class="input-group input-box">
                                            <input
                                                type="text" class="amount form-control" name="amount"/>
                                            <button class="show-currency btn-custom"></button>
                                        </div>
                                    </div>
                                    @error('amount')
                                    <p class="text-danger">{{$message}}</p>
                                    @enderror
                                </div>
                                <input type="hidden" class="gateway" name="gateway" value="">

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn-custom">@lang('Next')</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="payoutOffDayModal" class="modal fade addFundModal" tabindex="-1" role="dialog" data-bs-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content form-block">
                    <div class="modal-header">
                        <h4 class="modal-title method-name golden-text">@lang('Payout Information')</h4>
                        <button type="button" class="close-btn close_invest_modal" data-bs-dismiss="modal"
                                aria-label="Close">
                            <i class="fal fa-times"></i>
                        </button>
                    </div>

                    <form action="{{route('user.payout.moneyRequest')}}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="payment-form ">
                                <span class="withdrawClose"></span> <span class="toDay text-danger"></span>
                                <p class="openDays mt-3"> @lang('Opening Days :')
                                    <span class="saturday badge bg-primary custom-size"></span>
                                    <span class="sunday badge bg-success custom-size"></span>
                                    <span class="monday badge bg-info custom-size"></span>
                                    <span class="tuesday badge bg-warning custom-size"></span>
                                    <span class="wednesday badge bg-success custom-size"></span>
                                    <span class="thursday badge bg-primary custom-size"></span>
                                    <span class="friday badge bg-info custom-size"></span>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endpush

@endsection



@push('script')

    @if(count($errors) > 0 )
        <script>
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
            @foreach($errors->all() as $key => $error)
            Notiflix.Notify.Failure("@lang($error)");
            @endforeach
        </script>
    @endif

    <script>
        "use strict";
        var id, minAmount, maxAmount, baseSymbol, fixCharge, percentCharge, currency, gateway;

        $('.notifyOffDay').on('click', function (){
            let today = $(this).data('offday');
            let days = $(this).data('days');
            if (days.saturday != 0){
                $('.saturday').text('Saturday');
            }
            if (days.sunday != 0){
                $('.sunday').text('Sunday');
            }
            if (days.monday != 0){
                $('.monday').text('Monday');
            }
            if (days.tuesday != 0){
                $('.tuesday').text('Tuesday');
            }
            if (days.wednesday != 0){
                $('.wednesday').text('Wednesday');
            }
            if (days.thursday != 0){
                $('.thursday').text('Thursday');
            }
            if (days.friday != 0){
                $('.friday').text('Friday');
            }


            $('.withdrawClose').text(`@lang('Payment withdrawal closes on ')`);
            $('.toDay').text(`${today}`);

        })

        $('.addFund').on('click', function () {
            id = $(this).data('id');
            gateway = $(this).data('gateway');
            minAmount = $(this).data('min_amount');
            maxAmount = $(this).data('max_amount');
            baseSymbol = "{{config('basic.currency_symbol')}}";
            fixCharge = $(this).data('fix_charge');
            percentCharge = $(this).data('percent_charge');
            currency = $(this).data('currency');
            $('.depositLimit').text(`@lang('Transaction Limit:') ${minAmount} - ${maxAmount}  ${baseSymbol}`);

            var depositCharge = `@lang('Charge:') ${fixCharge} ${baseSymbol}  ${(0 < percentCharge) ? ' + ' + percentCharge + ' % ' : ''}`;
            $('.depositCharge').text(depositCharge);
            $('.method-name').text(`@lang('Payout By') ${$(this).data('name')}`);
            $('.show-currency').text("{{config('basic.currency')}}");
            $('.gateway').val(id);
        });
        $('.close').on('click', function (e) {
            $('#loading').hide();
            $('.amount').val(``);
            $("#addFundModal").modal("hide");
        });
    </script>
@endpush

