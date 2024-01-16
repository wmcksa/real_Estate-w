@extends('admin.layouts.app')
@section('title')
    @lang($title)
@endsection

@section('content')
    @php
        $base_currency = config('basic.currency_symbol');
    @endphp

    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped" id="zero_config">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">@lang('User')</th>
                        <th scope="col">@lang('Invested Amount')</th>
                        <th scope="col">@lang('Profit')</th>
                        <th scope="col">@lang('Return Date')</th>
                        <th scope="col">@lang('Payment Status')</th>
                        <th scope="col">@lang('Action')</th>
                    </tr>
                    </thead>
                    <tbody>

                    @forelse($investedUser as $invest)
                        <tr>
                            <td data-label="@lang('User')">
                                <a href="{{route('admin.user-edit',$invest->user_id)}}" target="_blank">
                                    <div class="d-flex no-block align-items-center">
                                        <div class="mr-3"><img
                                                src="{{getFile(config('location.user.path').optional($invest->user)->image) }}"
                                                alt="user" class="rounded-circle" width="45" height="45">
                                        </div>
                                        <div class="">
                                            <h5 class="text-dark mb-0 font-16 font-weight-medium">
                                                @lang(optional($invest->user)->firstname) @lang(optional($invest->user)->lastname)
                                            </h5>
                                            <span class="text-muted font-14"><span>@</span>@lang(optional($invest->user)->username)</span>
                                        </div>
                                    </div>
                                </a>
                            </td>

                            <td data-label="@lang('Invested Amount')">
                                {{ config('basic.currency_symbol') }}{{ $invest->amount }}
                            </td>

                            <td data-label="@lang('Profit')">
                                @if($invest->how_many_times != 0 && $invest->how_many_times != null && $invest->return_date != null)
                                    {{ config('basic.currency_symbol') }}{{  $invest->profit_type == 0 ? $invest->profit : $invest->net_profit }}
                                @elseif($invest->how_many_times != null && $invest->return_date == null)
                                    {{ config('basic.currency_symbol') }}@lang('0.00')
                                @elseif($invest->how_many_times == null && $invest->return_date != null)
                                    {{ config('basic.currency_symbol') }}{{ $invest->net_profit }}
                                @endif

                            </td>

                            <td data-label="@lang('Return Date')">
                                @if($invest->how_many_times != 0 && $invest->how_many_times != null && $invest->return_date != null)
                                    {{ customDate($invest->return_date) }}
                                @elseif($invest->how_many_times != null && $invest->return_date == null)
                                    <span class="custom-badge badge-pill bg-warning }}">@lang('After Installments Clear')</span>
                                @elseif($invest->how_many_times == null && $invest->return_date != null)
                                    {{ customDate($invest->return_date) }}
                                @endif
                            </td>

                            <td data-label="@lang('Payment Status')">
                                @if($invest->how_many_times == 0 && $invest->status == 1)
                                    <span class="custom-badge bg-success badge-pill">@lang('Completed')</span>
                                @elseif($invest->how_many_times == null && $invest->status == 0)
                                    <span class="custom-badge bg-success badge-pill">@lang('Life Time')</span>
                                @else
                                    <span class="custom-badge bg-success badge-pill">{{ $invest->how_many_times }}@lang(' times')</span>
                                @endif
                            </td>


                            <td data-label="@lang('Action')">
                                @if(($invest->how_many_times == null || $invest->how_many_times != 0) && $invest->status == 0 )
                                    @php
                                        $todayDate = \Carbon\Carbon::now()->format('Y-m-d');
                                        $expireDate = \Carbon\Carbon::parse(optional($invest->property)->expire_date)->format('Y-m-d');
                                        $returnDate = \Carbon\Carbon::parse($invest->return_date)->format('Y-m-d');
                                    @endphp

                                    @if($todayDate > $expireDate && optional($invest->property)->is_payment == 0)
                                        <button class="btn btn-sm  btn-outline-primary btn-rounded btn-sm investPaymentSingleUser {{ $todayDate != $returnDate ? 'disabled' : '' }}" {{ $todayDate != $returnDate ? 'disabled' : '' }}
                                                type="button"
                                                data-route="{{ route('admin.investPaymentSingleUser', $invest->id) }}"
                                                data-amount="{{ $invest->amount }}"
                                                data-toggle="modal"
                                                data-target="#investPaymentSingleUserModal">
                                            <span>
                                                <i class="fa fa-credit-card"></i> @lang(' Pay')
                                            </span>
                                        </button>
                                    @else
                                        --
                                    @endif
                                @else
                                    --
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%" class="text-center text-na">@lang('No Data Found')</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(!$investedUser->isEmpty())
        <div class="modal fade" id="investPaymentSingleUserModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">@lang('Payment now')</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="" method="post" id="investPaymentSingleUserForm">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                @if($invest->profit_type != 0)
                                    <label>@lang('Profit')</label>
                                    <div class="input-group mb-3">
                                        <input type="hidden" value="" name="invest_amont" class="invest_amount">
                                        <input type="text" name="get_profit" id="actualGetProfit" class="form-control edit_time" value="{{ count($investedUser) > 0 ? old('get_profit', (int)optional($invest->property)->profit) : old('get_profit') }}" placeholder="@lang('0')">
                                        <div class="input-group-append">
                                            <select name="time_type" id="edit_time_type" class="form-control edit_time_type">
                                                <option value="1" >%</option>
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <label>@lang('Pay Amount') @if($invest->profit_type == 0) (@lang('Fixed')) @endif</label>
                                <div class="input-group mb-3">
                                    <input type="text" name="amount" class="form-control edit_time pay_amount"
                                           value="{{ count($investedUser) > 0 ? old('amount', $invest->net_profit) : old('amount') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">@lang(config('basic.currency_symbol'))</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-rounded" data-dismiss="modal">
                                <span>@lang('Cancel')</span>
                            </button>
                            <button type="submit" class="btn btn-primary btn-rounded">
                                <span><i class="fas fa-save"></i> @lang('Submit')</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif


@endsection

@push('style-lib')
    <link href="{{asset('assets/admin/css/dataTables.bootstrap4.css')}}" rel="stylesheet">
@endpush

@push('js')
    <script src="{{ asset('assets/admin/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/datatable-basic.init.js') }}"></script>

    @if ($errors->any())
        @php
            $collection = collect($errors->all());
            $errors = $collection->unique();
        @endphp
        <script>
            "use strict";
            @foreach ($errors as $error)
                Notiflix.Notify.Failure("{{trans($error)}}");
            @endforeach
        </script>
    @endif

    <script>
        'use strict'
        $(document).ready(function () {
            $(document).on('click', '.investPaymentSingleUser', function () {
                let investAmount = $(this).data('amount');
                $('.invest_amount').val(investAmount);
                $('#investPaymentSingleUserForm').attr('action', $(this).data('route'))
            })

            $(document).on('input', '#actualGetProfit', function () {
                let actual_profit = $('#actualGetProfit').val();
                let investAmount = $('.invest_amount').val();
                let actualAmount = investAmount * actual_profit / 100;
                let netActualAmount = actualAmount.toFixed(2);
                $('.pay_amount').val(netActualAmount);
            });
        });
    </script>
@endpush
