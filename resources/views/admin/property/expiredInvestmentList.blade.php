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
                        <th scope="col">@lang('Property')</th>
                        <th scope="col">@lang('Expire Date')</th>
                        <th scope="col">@lang('Invested User')</th>
                        <th scope="col">@lang('Invested Amount')</th>
                        <th scope="col">@lang('Profit Date')</th>
                        <th scope="col">@lang('Payment Status')</th>
                        <th scope="col">@lang('Disbursement Type')</th>
                        <th scope="col">@lang('Action')</th>
                    </tr>
                    </thead>
                    <tbody>

                    @forelse($investments as $invest)
                        <tr>
                            <td data-label="@lang('Property')">
                                <a href="{{ route('propertyDetails',[@slug(optional($invest->property->details)->property_title), optional($invest->property)->id]) }}" target="_blank">
                                    <div class="d-flex no-block align-items-center">
                                        <div class="mr-3"><img src="{{getFile(config('location.propertyThumbnail.path').optional($invest->property)->thumbnail) }}" alt="@lang('property_thumbnail')" class="rounded-circle" width="45" height="45">
                                        </div>
                                        <div class="">
                                            <h5 class="text-dark mb-0 font-16 font-weight-medium">
                                                @lang(\Str::limit(optional($invest->property->details)->property_title, 30))
                                            </h5>
                                        </div>
                                    </div>
                                </a>
                            </td>

                            <td data-label="@lang('Expire Date')">
                                @if(optional($invest->property)->expire_date)
                                    {{ dateTime(optional($invest->property)->expire_date) }}
                                @endif
                            </td>

                            <td data-label="@lang('Invested User')">
                                <a href="{{ route('admin.seeInvestedUser', ['property_id' => optional($invest->property)->id, 'type' => 'expired']) }}"><span class="custom-badge bg-success badge-pill">{{ optional($invest->property)->totalRunningInvestUserAndAmount()['totalInvestedUser'] }}</span></a>
                            </td>

                            <td data-label="@lang('Total Invested Amount')">
                                {{ config('basic.currency_symbol') }}{{ optional($invest->property)->totalRunningInvestUserAndAmount()['totalInvestedAmount'] }}
                            </td>


                            <td data-label="@lang('Profit Date')">
                                {{ customDate($invest->return_date) }}
                            </td>

                            <td data-label="@lang('Payment Status')">
                                @if($invest->how_many_times == 0 && $invest->status == 1)
                                    <span class="custom-badge bg-success badge-pill">@lang('Completed')</span>
                                @elseif($invest->how_many_times == null && $invest->status == 0)
                                    <span class="custom-badge bg-success badge-pill">@lang('Unlimited')</span>
                                @else
                                    <span class="custom-badge bg-success badge-pill">{{ $invest->how_many_times }}@lang(' times')</span>
                                @endif
                            </td>

                            <td data-label="@lang('Disbursement Type')">
                                <input data-toggle="toggle" id="disbursement_type" class="disbursement_type" data-onstyle="success"
                                       data-offstyle="info" data-on="Manual" data-off="Automatic" data-width="100%"
                                       type="checkbox" {{ optional($invest->property)->is_payment == 0 ? 'checked' : '' }} name="disbursement_type" data-id="{{ optional($invest->property)->id }}">
                            </td>


                            <td data-label="@lang('Action')">
                                <a href="{{ route('admin.seeInvestedUser', ['property_id' => optional($invest->property)->id, 'type' => 'expired']) }}" class="btn btn-sm btn-outline-primary btn-rounded btn-rounded edit-button">
                                    <i class="far fa-eye"></i>
                                </a>

                                @php
                                    $todayDate = \Carbon\Carbon::now()->format('Y-m-d');
                                    $returnDate = \Carbon\Carbon::parse($invest->return_date)->format('Y-m-d');
                                @endphp


                                @if(($invest->how_many_times == null || $invest->how_many_times != 0) && $invest->status == 0 && optional($invest->property)->is_payment == 0)
                                    <button class="btn btn-sm btn-outline-primary btn-rounded btn-sm investPaymentAllUser {{ $todayDate != $returnDate ? 'disabled' : '' }}" {{ $todayDate != $returnDate ? 'disabled' : '' }}
                                    type="button"
                                            data-route="{{ route('admin.investPaymentAllUser', optional($invest->property)->id) }}"
                                            data-amount="{{ $invest->amount }}"
                                            data-toggle="modal"
                                            data-target="#investPaymentAllUserModal">
                                        <span>
                                            <i class="fa fa-credit-card"></i> @lang(' Pay')
                                        </span>
                                    </button>
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

    <div class="modal fade" id="investPaymentAllUserModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">@lang('Payment now')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                @if(!$investments->isEmpty())
                    <form action="" method="post" id="investPaymentAllUserForm">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                @if($invest->profit_type != 0)
                                    <label>@lang('Profit')</label>
                                    <div class="input-group mb-3">
                                        <input type="hidden" value="" name="invest_amont" class="invest_amount">
                                        <input type="text" name="get_profit" id="actualGetProfit" class="form-control edit_time" value="{{ count($investments) > 0 ? old('get_profit', (int)optional($invest->property)->profit) : old('get_profit') }}" placeholder="@lang('0')">
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
                                           value="{{ $invest->profit_type == 0 ? $invest->profit : $invest->net_profit }}">
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
                @endif

            </div>
        </div>
    </div>

@endsection

@push('js')


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
        "use strict";
        $(document).ready(function () {
            $(document).on('click', '.investPaymentAllUser', function () {
                let investAmount = $(this).data('amount');
                $('.invest_amount').val(investAmount);
                $('#investPaymentAllUserForm').attr('action', $(this).data('route'))
            })

            $(document).on('input', '#actualGetProfit', function () {
                let actual_profit = $('#actualGetProfit').val();
                let investAmount = $('.invest_amount').val();
                let actualAmount = investAmount * actual_profit / 100;
                let netActualAmount = actualAmount.toFixed(2);
                $('.pay_amount').val(netActualAmount);
            });

            $(document).on('change','#disbursement_type', function () {

                var isCheck = $(this).prop('checked');
                let dataId = $(this).data('id');
                let isVal = null;
                if (isCheck == true) {
                    isVal = 'on'
                } else {
                    isVal = 'off';
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('admin.disbursementType') }}",
                    type: "POST",
                    data: {
                        dataid: dataId,
                        isval: isVal,
                    },
                    success: function (data) {
                        window.location.reload();
                    },
                });
            });
        });
    </script>

@endpush
