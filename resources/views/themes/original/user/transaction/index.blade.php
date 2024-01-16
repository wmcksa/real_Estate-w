@extends($theme.'layouts.user')
@section('title')
    @lang('Transaction')
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}" />
@endpush

@section('content')
    <section class="transaction-history">
        <div class="container-fluid">
            <div class="row mt-4 mb-2">
                <div class="col ms-2">
                    <div class="header-text-full">
                        <h3 class="dashboard_breadcurmb_heading mb-1">@lang('Transaction History')</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('user.home') }}">@lang('Dashboard')</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">@lang('transaction')</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>


            <!-- search area -->
            <div class="search-bar p-0">
                <form action="{{ route('user.transaction.search') }}" method="get">
                    <div class="row g-3 align-items-end">
                        <div class="input-box col-lg-3">
                            <input
                                type="text"
                                name="transaction_id"
                                value="{{@request()->transaction_id}}"
                                class="form-control"
                                placeholder="@lang('Transaction ID')"/>
                        </div>

                        <div class="input-box col-lg-3">
                            <input type="text" name="remark" value="{{@request()->remark}}" class="form-control" placeholder="@lang('Remark')"/>
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
                                <th>@lang('SL No.')</th>
                                <th>@lang('Transaction ID')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Remarks')</th>
                                <th>@lang('Time')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{loopIndex($transactions) + $loop->index}}</td>
                                    <td>@lang($transaction->trx_id)</td>
                                    <td>
                                        <span
                                        class="fontBold text-{{($transaction->trx_type == "+") ? 'success': 'danger'}}">{{($transaction->trx_type == "+") ? '+': '-'}}{{getAmount($transaction->amount, config('basic.fraction_number')). ' ' . trans(config('basic.currency'))}}</span>
                                    </td>
                                    <td>@lang($transaction->remarks)</td>
                                    <td>{{ dateTime($transaction->created_at, 'd M Y h:i A') }}</td>
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
