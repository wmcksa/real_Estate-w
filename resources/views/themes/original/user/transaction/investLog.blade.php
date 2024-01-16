@extends($theme.'layouts.user')
@section('title', trans('Investment History'))
@section('content')
    @push('style')
        <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}" />
    @endpush
    <!-- Invest history -->
    <section class="transaction-history">
        <div class="container-fluid">
            <div class="row mt-4 mb-2">
                <div class="col ms-2">
                    <div class="header-text-full">
                        <h3 class="dashboard_breadcurmb_heading mb-1">@lang('Investment History')</h3>
                        <nav aria-label="breadcrumb" class="ms-2">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('user.home') }}">@lang('Dashboard')</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">@lang('Invest History')</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- search area -->
            <div class="search-bar mt-3 me-2 ms-2 p-0">
                <form action="" method="get" enctype="multipart/form-data">
                    <div class="row g-3 align-items-end">
                        <div class="input-box col-lg-2">
                            <label for="">@lang('Property')</label>
                            <input
                                type="text"
                                name="property"
                                value="{{@request()->property}}"
                                class="form-control"
                                placeholder="@lang('Search property')"
                            />
                        </div>

                        <div class="input-box col-lg-2">
                            <label for="">@lang('Invest Status')</label>
                            <select class="form-select" name="invest_status" aria-label="Default select example">
                                <option value="">@lang('All Invest')</option>
                                <option value="1" @if(@request()->invest_status == '1') selected @endif>@lang('Complete')</option>
                                <option value="0" @if(@request()->invest_status == '0') selected @endif>@lang('Due')</option>
                            </select>
                        </div>

                        <div class="input-box col-lg-2">
                            <label for="">@lang('Return Status')</label>
                            <select class="form-select" name="return_status" aria-label="Default select example">
                                <option value="">@lang('All')</option>
                                <option value="0" @if(@request()->return_status == '0') selected @endif>@lang('Running')</option>
                                <option value="1" @if(@request()->return_status == '1') selected @endif>@lang('Completed')</option>
                            </select>
                        </div>

                        <div class="input-box col-lg-2">
                            <label for="from_date">@lang('From Date')</label>
                            <input
                                type="text" class="form-control datepicker from_date" name="from_date" value="{{ old('from_date',request()->from_date) }}" placeholder="@lang('From date')" autocomplete="off" readonly/>
                        </div>
                        <div class="input-box col-lg-2">
                            <label for="to_date">@lang('To Date')</label>
                            <input
                                type="text" class="form-control datepicker to_date" name="to_date" value="{{ old('to_date',request()->to_date) }}" placeholder="@lang('To date')" autocomplete="off" readonly disabled="true"/>
                        </div>
                        <div class="input-box col-lg-2">
                            <button class="btn-custom w-100" type="submit"><i class="fal fa-search"></i>@lang('Search')</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-parent table-responsive me-2 ms-2 mt-4">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">@lang('SL')</th>
                            <th scope="col">@lang('Property')</th>
                            <th scope="col">@lang('Investment Amount')</th>
                            <th scope="col">@lang('Profit')</th>
                            <th scope="col">@lang('Upcoming Payment')</th>
                            <th scope="col">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($investments as $key => $invest)
                        <tr>
                            <td data-label="@lang('SL')">{{loopIndex($investments) + $key}}</td>

                            <td class="company-logo" data-label="@lang('Property')">
                                <div>
                                    <a href="{{ route('propertyDetails',[@slug(optional($invest->property->details)->property_title), optional($invest->property)->id]) }}" target="_blank">
                                        <img src="{{ getFile(config('location.propertyThumbnail.path').optional($invest->property)->thumbnail) }}">
                                    </a>
                                </div>
                                <div>
                                    <a href="{{ route('propertyDetails',[@slug(optional($invest->property->details)->property_title), optional($invest->property)->id]) }}" target="_blank">@lang(\Illuminate\Support\Str::limit(optional($invest->property->details)->property_title, 30))</a> <br>
                                    <span class="text-muted font-14"><span class="text-danger">@lang('Invested: ')</span><span>{{ config('basic.currency_symbol') }}</span>{{ (int)$invest->amount }}</span>
                                </div>
                            </td>

                            <td data-label="@lang('Invest Amount')">{{ optional($invest->property)->investmentAmount }}</td>

                            <td data-label="@lang('Profit')">
                                @if($invest->invest_status == 1)
                                    {{  $invest->profit_type == 0 ? config('basic.currency_symbol').$invest->profit : config('basic.currency_symbol').$invest->net_profit }}
                                @else
                                    @lang('N/A')
                                @endif
                            </td>

                            <td data-label="@lang('Upcoming Payment')">
                                @if($invest->invest_status == 0)
                                    <span class="badge bg-danger">@lang('After Installments complete')</span>
                                @elseif($invest->invest_status == 1 && $invest->return_date == null && $invest->status == 1)
                                    <span class="badge bg-success">@lang('All completed')</span>
                                @else
                                    {{ customDate($invest->return_date) }}
                                @endif
                            </td>

                            <td data-label="Action">
                                <div class="sidebar-dropdown-items">
                                    <button
                                        type="button"
                                        class="dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        <i class="fal fa-cog"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="{{ route('user.invest-history-details', $invest->id) }}" class="dropdown-item"> <i class="fal fa-eye"></i> @lang('Details') </a>
                                        </li>
                                        @if($invest->invest_status == 0)
                                            <li>
                                                <a class="dropdown-item btn duePayment"
                                                   data-route="{{route('user.completeDuePayment', $invest->id)}}"
                                                   data-property="{{ optional($invest->property->details)->property_title }}"
                                                   data-fixedamount="{{ optional($invest->property)->fixed_amount }}"
                                                   data-previouspay="{{ $invest->amount }}"
                                                   data-investtype="{{ optional($invest->property)->is_invest_type }}"
                                                   data-isinstallment="{{ $invest->is_installment }}"
                                                   data-totalinstallments="{{ $invest->total_installments }}"
                                                   data-dueinstallments="{{ $invest->due_installments }}"
                                                   data-installmentamount="{{ optional($invest->property)->installment_amount }}"
                                                   data-installmentduration="{{ optional($invest->property)->installment_duration }}"
                                                   data-installmentlastdate="{{ customDate($invest->next_installment_date_end) }}"
                                                   data-getinstallmentdate="{{ $invest->getInstallmentDate() }}"
                                                   data-installmentlatefee="{{ optional($invest->property)->installment_late_fee }}"
                                                   data-installmentdurationtype="{{ optional($invest->property)->installment_duration_type }}"> <i class="far fa-sack-dollar"></i> @lang('Due Make Payment') </a>
                                            </li>
                                        @endif

                                        @if($invest->invest_status == 1 && $invest->status == 0)
                                            @if($invest->propertyShare)
                                                <li>
                                                    <a class="dropdown-item btn updateOffer"
                                                       data-route="{{route('user.propertyShareUpdate', optional($invest->propertyShare)->id)}}"
                                                       data-amount="{{ optional($invest->propertyShare)->amount }}"
                                                       data-property="{{ optional($invest->property->details)->property_title }}">
                                                        <i class="far fa-sack-dollar"></i> @lang('Update Share')
                                                    </a>
                                                </li>
                                            @else
                                                @if(optional($invest->property)->is_investor == 1 && config('basic.is_share_investment') == 1)
                                                    <li>
                                                        <a class="dropdown-item btn sendOffer"
                                                           data-route="{{route('user.propertyShareStore', $invest->id)}}"
                                                           data-property="{{ optional(optional($invest->property)->details)->property_title }}">
                                                            <i class="far fa-sack-dollar"></i> @lang('Sell Share')
                                                        </a>
                                                    </li>
                                                @endif
                                            @endif
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr class="text-center">
                                <td colspan="100%" class="text-danger text-center">{{trans('No Data Found!')}}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @push('loadModal')
        <!-- Modal -->
        {{--  due make payment modal --}}
        <div class="modal fade" id="duePaymentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <form action="" method="post" id="invest-form" class="login-form due_payment_modal">
                    @csrf
                    @method('put')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">@lang('Due Make Payment')</h5>
                            <button type="button" class="close-btn close_invest_modal" data-bs-dismiss="modal"
                                    aria-label="Close">
                                <i class="fal fa-times"></i>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="card">
                                <div class="m-3 mb-0 payment-method-details property-title font-weight-bold">
                                </div>

                                <div class="card-body">
                                    <div class="estimation-box">
                                        <div class="details_list">
                                            <ul>
                                                <li class="d-flex justify-content-between"><span>@lang('Fixed Invest')</span>
                                                    <span class="fixed_invest"></span></li>
                                                <li class="d-flex justify-content-between totalInstallment">
                                                    <span>@lang('Total Installment')</span><span
                                                        class="total_installments"></span></li>

                                                <li class="d-flex justify-content-between dueInstallment">
                                                    <span>@lang('Due Installment')</span><span
                                                        class="due_installments"></span></li>

                                                <li class="d-flex justify-content-between dueInstallment">
                                                    <span>@lang('Installment Amount')</span><span
                                                        class="installment_amount"></span></li>

                                                <li class="d-flex justify-content-between installmentDuration">
                                                    <span>@lang('Installment Duration')</span> <span
                                                        class="installment_duration"></span></li>

                                                <li class="d-flex justify-content-between installmentLastDate">
                                                    <span>@lang('Installment Last Date')</span> <span
                                                        class="installment_last_date"></span></li>

                                                <li class="d-flex justify-content-between installmentLateFee">
                                                    <span>@lang('Installment Late Fee')</span> <span
                                                        class="installment_late_fee"></span></li>

                                                <li class="d-flex justify-content-between previousTotalPay">
                                                    <span>@lang('Previous Total Pay')</span> <span
                                                        class="previous_total_pay"></span></li>

                                                <li class="d-flex justify-content-between previousTotalPay">
                                                    <span>@lang('Due Amount')</span> <span
                                                        class="total_due_amount"></span></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row g-3 investModalPaymentForm">
                                        <div class="input-box col-12">
                                            <label for="">@lang('Select Wallet')</label>
                                            <select class="form-control form-select" id="exampleFormControlSelect1"
                                                    name="balance_type">
                                                @auth
                                                    <option
                                                        value="balance">@lang('Deposit Balance - '.$basic->currency_symbol.getAmount(auth()->user()->balance))</option>
                                                    <option
                                                        value="interest_balance">@lang('Interest Balance -'.$basic->currency_symbol.getAmount(auth()->user()->interest_balance))</option>
                                                @endauth
                                            </select>
                                        </div>

                                        <div class="input-box col-12 payInstallment d-none">
                                            <div class="form-check">
                                                <input type="hidden" value="" class="set_installment_amount">
                                                <input type="hidden" value="" class="set_totalDue_amount">
                                                <input type="hidden" value="" class="set_get_installment_date">
                                                <input type="hidden" value="" class="set_total_due_amount">
                                                <input type="hidden" value="" class="set_installment_late_fee">
                                                <input class="form-check-input" type="checkbox" value="0"
                                                       name="pay_installment" id="pay_installment"/>
                                                <label class="form-check-label"
                                                       for="pay_installment">@lang('Pay Installment')</label>
                                            </div>
                                        </div>

                                        <div class="input-box col-12">
                                            <label for="">@lang('Amount')</label>
                                            <div class="input-group">
                                                <input
                                                    type="text" class="invest-amount form-control" name="amount" id="amount"
                                                    value="{{old('amount')}}"
                                                    onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')"
                                                    autocomplete="off"
                                                    placeholder="@lang('Enter amount')" required>
                                                <button class="show-currency"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn-custom btn2 btn-secondary close_invest_modal close__btn"
                                    data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn-custom investModalSubmitBtn">@lang('Submit')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{--  Send offer modal --}}
        <div class="modal fade" id="sendOfferModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <form action="" method="post" id="invest-form" class="login-form send_offer_modal">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">@lang('Set Share Amount')</h5>
                            <button type="button" class="close-btn close_invest_modal" data-bs-dismiss="modal"
                                    aria-label="Close">
                                <i class="fal fa-times"></i>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="card">
                                <div class="m-3 mb-0 payment-method-details property-title font-weight-bold">
                                </div>

                                <div class="card-body">
                                    <div class="row g-3 investModalPaymentForm">
                                        <div class="input-box col-12">
                                            <label for="">@lang('Share Amount')</label>
                                            <div class="input-group">
                                                <input
                                                    type="text" class="invest-amount form-control @error('amount') is-invalid @enderror" name="amount" id="amount"
                                                    value="{{old('amount')}}"
                                                    onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')"
                                                    autocomplete="off"
                                                    placeholder="@lang('Enter amount')" required>
                                                <button class="show-currency" type="button"></button>
                                            </div>
                                            @error('amount')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn-custom btn2 btn-secondary close_invest_modal close__btn"
                                    data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn-custom investModalSubmitBtn">@lang('Share')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{--  Update offer modal --}}
        <div class="modal fade" id="updateOfferModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <form action="" method="post" id="invest-form" class="login-form update_offer_modal">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">@lang('Update Share Amount')</h5>
                            <button type="button" class="close-btn close_invest_modal" data-bs-dismiss="modal"
                                    aria-label="Close">
                                <i class="fal fa-times"></i>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="card">
                                <div class="card-header payment-method-details property-title primary_color">
                                </div>

                                <div class="card-body">
                                    <div class="row g-3 investModalPaymentForm">
                                        <div class="input-box col-12">
                                            <label for="">@lang('Share Amount')</label>
                                            <div class="input-group">
                                                <input
                                                    type="text" class="invest-amount form-control amount @error('amount') is-invalid @enderror" name="amount" id="amount"
                                                    value="{{old('amount')}}"
                                                    onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')"
                                                    autocomplete="off"
                                                    placeholder="@lang('Enter amount')" required>
                                                <button class="show-currency" type="button"></button>
                                            </div>
                                            @error('amount')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn-custom btn2 btn-secondary close_invest_modal close__btn"
                                    data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn-custom investModalSubmitBtn">@lang('Share')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endpush
@endsection

@push('script')
    <script src="{{ asset('assets/global/js/bootstrap-datepicker.js') }}"></script>
    <script>
        'use strict'
        $(document).ready(function (){
            $( ".datepicker" ).datepicker({
                autoclose: true,
                clearBtn: true
            });

            $('.from_date').on('change', function (){
                $('.to_date').removeAttr('disabled');
            });

            $(document).on('click', '.duePayment', function () {
                var propertyInvestModal = new bootstrap.Modal(document.getElementById('duePaymentModal'))
                propertyInvestModal.show();

                let symbol = "{{trans($basic->currency_symbol)}}";
                let dataRoute = $(this).data('route');
                let dataProperty = $(this).data('property');
                let dataFixedAmount = $(this).data('fixedamount');
                let dataPreviousPay = $(this).data('previouspay');
                let isInstallment = $(this).data('isinstallment');
                let totalInstallments = $(this).data('totalinstallments');
                let totalDueInstallments = $(this).data('dueinstallments');
                let installmentAmount = $(this).data('installmentamount');
                let installmentDuration = $(this).data('installmentduration') + ' ' + $(this).data('installmentdurationtype');
                let installmentLastDate = $(this).data('installmentlastdate');
                let getInstallmentDate = $(this).data('getinstallmentdate');
                let installmentLateFee = $(this).data('installmentlatefee');
                $('.show-currency').text("{{config('basic.currency')}}");

                $('.due_payment_modal').attr('action', dataRoute);
                $('.property-title').text( `Property: ${dataProperty}`);
                $('.fixed_invest').text(`${symbol}${dataFixedAmount}`);
                $('.total_installments').text(totalInstallments);
                $('.due_installments').text(totalDueInstallments);
                $('.installment_duration').text(installmentDuration);
                $('.installment_last_date').text(installmentLastDate);
                $('.installment_last_date').text(installmentLastDate);
                $('.installment_late_fee').text(`${symbol}${installmentLateFee}`);
                $('.previous_total_pay').text(`${symbol}${parseInt(dataPreviousPay)}`);
                let totalDueAmount = parseInt(dataFixedAmount) - parseInt(dataPreviousPay);
                $('.total_due_amount').text(`${symbol}${totalDueAmount}`);
                $('.installment_amount').text(`${symbol}${installmentAmount}`);



                if (isInstallment == 1 && totalDueInstallments > 1){
                    $('.set_installment_amount').val(installmentAmount);
                    $('.set_totalDue_amount').val(totalDueAmount);
                    $('.set_get_installment_date').val(getInstallmentDate);
                    $('.set_total_due_amount').val(totalDueAmount);
                    $('.set_installment_late_fee').val(installmentLateFee);
                    $('.payInstallment').removeClass('d-none');
                }

                if (getInstallmentDate == 0){
                    let dueAmountWithLateFee = parseInt(totalDueAmount) + parseInt(installmentLateFee);
                    $('.invest-amount').val(dueAmountWithLateFee);
                    $('#amount').attr('readonly', true);
                }else{
                    $('.invest-amount').val(totalDueAmount);
                    $('#amount').attr('readonly', true);
                }
            });


            $(document).on('click', '#pay_installment', function () {
                let getInstallmentDate = $('.set_get_installment_date').val();
                let installmentAmount = $('.set_installment_amount').val();
                let installmentLateFee = $('.set_installment_late_fee').val();
                let totalDueAmount = $('.set_total_due_amount').val();

                if ($(this).prop("checked") == true) {
                    $(this).val(1);
                    if (getInstallmentDate == 0){
                        let dueAmountWithLateFee = parseInt(installmentAmount) + parseInt(installmentLateFee);
                        $('.invest-amount').val(dueAmountWithLateFee);
                        $('#amount').attr('readonly', true);
                    }else{
                        $('.invest-amount').val(installmentAmount);
                        $('#amount').attr('readonly', true);
                    }

                } else {
                    $(this).val(0);
                    if (getInstallmentDate == 0){
                        let dueAmountWithLateFee = parseInt(totalDueAmount) + parseInt(installmentLateFee);
                        $('.invest-amount').val(dueAmountWithLateFee);
                        $('#amount').attr('readonly', true);
                    }else{
                        $('.invest-amount').val(totalDueAmount);
                        $('#amount').attr('readonly', true);
                    }
                }
            });

            $(document).on('click', '.close_invest_modal', function () {
                if ($('#pay_installment').prop("checked") == true) {
                    $("#pay_installment").prop("checked", false);
                }
            });

            $(document).on('click', '.sendOffer', function () {
                var propertysendOfferModal = new bootstrap.Modal(document.getElementById('sendOfferModal'))
                propertysendOfferModal.show();

                let dataRoute = $(this).data('route');
                let dataProperty = $(this).data('property');

                $('.send_offer_modal').attr('action', dataRoute);
                $('.property-title').text(`Property: ${dataProperty}`);
                $('.show-currency').text("{{config('basic.currency')}}");

            });

            $(document).on('click', '.updateOffer', function () {
                var propertyupdateOfferModal = new bootstrap.Modal(document.getElementById('updateOfferModal'))
                propertyupdateOfferModal.show();

                let dataRoute = $(this).data('route');
                let dataProperty = $(this).data('property');
                let amount = $(this).data('amount');
                $('.show-currency').text("{{config('basic.currency')}}");

                $('.update_offer_modal').attr('action', dataRoute);
                $('.property-title').text(`Property: ${dataProperty}`);
                $('.amount').val(amount);
            });
        });
    </script>
@endpush
