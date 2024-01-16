@extends($theme.'layouts.user')
@section('title',trans($title))
@section('content')

    @push('style')
        <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}"/>
    @endpush

    <section class="transaction-history">
        <div class="container-fluid">
            <div class="row mt-4 mb-2">
                <div class="col ms-2">
                    <div class="header-text-full">
                        <h3 class="dashboard_breadcurmb_heading mb-1">@lang('Payout History')</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('user.home') }}">@lang('Dashboard')</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">@lang('Payout History')</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- search area -->
            <div class="search-bar p-0">
                <form action="{{ route('user.payout.history.search') }}" method="get">
                    <div class="row g-3 align-items-end">
                        <div class="input-box col-lg-3">
                            <input
                                type="text"
                                name="name"
                                value="{{@request()->name}}"
                                class="form-control"
                                placeholder="@lang('Transaction ID')"
                            />
                        </div>

                        <div class="input-box col-lg-3">
                            <select name="status"
                                    class="form-select"
                                    id="salutation"
                                    aria-label="Default select example">
                                <option value="">@lang('All Payment')</option>
                                <option value="1"
                                        @if(@request()->status == '1') selected @endif>@lang('Pending Payment')</option>
                                <option value="2"
                                        @if(@request()->status == '2') selected @endif>@lang('Complete Payment')</option>
                                <option value="3"
                                        @if(@request()->status == '3') selected @endif>@lang('Cancel Payment')</option>
                            </select>
                        </div>

                        <div class="input-box col-lg-2">
                            <input
                                type="text" class="form-control datepicker from_date" name="from_date"
                                value="{{ old('from_date',request()->from_date) }}" placeholder="@lang('From date')"
                                autocomplete="off" readonly/>
                        </div>

                        <div class="input-box col-lg-2">
                            <input
                                type="text" class="form-control datepicker to_date" name="to_date"
                                value="{{ old('to_date',request()->to_date) }}" placeholder="@lang('To date')"
                                autocomplete="off" readonly disabled="true"/>
                        </div>

                        <div class="input-box col-lg-2">
                            <button class="btn-custom w-100" type="submit"><i class="fal fa-search"></i> @lang('Search')
                            </button>
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
                                <th scope="col">@lang('Transaction ID')</th>
                                <th scope="col">@lang('Gateway')</th>
                                <th scope="col">@lang('Amount')</th>
                                <th scope="col">@lang('Charge')</th>
                                <th scope="col">@lang('Status')</th>
                                <th scope="col">@lang('Time')</th>
                                <th scope="col">@lang('Details')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($payoutLog as $item)
                                <tr>
                                    <td>{{$item->trx_id}}</td>
                                    <td>@lang(optional($item->method)->name)</td>
                                    <td>{{getAmount($item->amount)}} @lang($basic->currency)</td>
                                    <td>{{getAmount($item->charge)}} @lang($basic->currency)</td>
                                    <td>
                                        @if($item->status == 1)
                                            <span class="badge bg-warning">@lang('Pending')</span>
                                        @elseif($item->status == 2)
                                            <span class="badge bg-success">@lang('Complete')</span>
                                        @elseif($item->status == 3)
                                            <span class="badge bg-danger">@lang('Cancel')</span>
                                        @endif
                                    </td>
                                    <td>{{ dateTime($item->created_at, 'd M Y h:i A') }}</td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn bgPrimary btn-sm infoButton text-white"
                                            data-information="{{json_encode($item->information)}}"
                                            data-feedback="{{$item->feedback}}"
                                            data-trx_id="{{ $item->trx_id }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#infoModal"
                                        >
                                            <i class="fa fa-info-circle"></i>
                                        </button>
                                    </td>
                                </tr>

                            @empty
                                <tr class="text-center">
                                    <td colspan="100%">{{__('No Data Found!')}}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                        {{ $payoutLog->appends($_GET)->links($theme.'partials.pagination') }}

                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Modal -->
    @push('loadModal')
        <div
            class="modal fade"
            id="infoModal"
            tabindex="-1"
            data-bs-backdrop="static"
            aria-labelledby="infoModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title golden-text" id="infoModalLabel">
                            @lang('Details')
                        </h4>
                        <button type="button" class="close-btn close_invest_modal" data-bs-dismiss="modal"
                                aria-label="Close">
                            <i class="fal fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group">
                            <li class="list-group-item list-group-item-primary bg-transparent lebelFont">@lang('Transactions')
                                : <span class="trx"></span>
                            </li>
                            <li class="list-group-item list-group-item-primary bg-transparent lebelFont">@lang('Admin Feedback')
                                : <span
                                    class="feedback"></span></li>
                        </ul>
                        <div class="payout-detail">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-custom text-white"
                            data-bs-dismiss="modal"
                        >
                            @lang('Close')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endpush

@endsection

@push('script')

    @push('script')
        <script src="{{ asset('assets/global/js/bootstrap-datepicker.js') }}"></script>

        <script>
            'use strict'
            $(document).ready(function () {
                $(".datepicker").datepicker({
                    autoclose: true,
                    clearBtn: true
                });
                $('.from_date').on('change', function () {
                    $('.to_date').removeAttr('disabled');
                });
            });
        </script>
    @endpush

    <script>
        "use strict";

        $(document).ready(function () {
            $('.infoButton').on('click', function () {
                var infoModal = $('#infoModal');
                infoModal.find('.trx').text($(this).data('trx_id'));
                infoModal.find('.feedback').text($(this).data('feedback'));
                var list = [];
                var information = Object.entries($(this).data('information'));

                var ImgPath = "{{asset(config('location.withdrawLog.path'))}}/";
                var result = ``;
                for (var i = 0; i < information.length; i++) {
                    if (information[i][1].type == 'file') {
                        result += `<li class="list-group-item bg-transparent customborder lebelFont">
                                            <span class="font-weight-bold "> ${information[i][0].replaceAll('_', " ")} </span> : <img src="${ImgPath}/${information[i][1].field_name}" alt="..." class="w-100">
                                        </li>`;
                    } else {
                        result += `<li class="list-group-item bg-transparent customborder lebelFont text-dark"
                                            <span class="font-weight-bold "> ${information[i][0].replaceAll('_', " ")} </span> : <span class="font-weight-bold ml-3">${information[i][1].field_name}</span>
                                        </li>`;
                    }
                }

                if (result) {
                    infoModal.find('.payout-detail').html(`<br><h4 class="my-3 golden-text">@lang('Payment Information')</h4>  ${result}`);
                } else {
                    infoModal.find('.payout-detail').html(`${result}`);
                }
                infoModal.modal('show');
            });


            $('.closeModal').on('click', function (e) {
                $("#infoModal").modal("hide");
            });
        });

    </script>
@endpush
