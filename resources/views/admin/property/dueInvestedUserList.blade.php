@extends('admin.layouts.app')
@section('title')
    @lang('Due Installment Invested User List')
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
                        <th scope="col">@lang('Due Amount')</th>
                        <th scope="col">@lang('Due Installments')</th>
                        <th scope="col">@lang('Installment Last Date')</th>
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

                            <td data-label="@lang('Due Amount')">
                                {{ config('basic.currency_symbol') }}{{ optional(($invest)->property)->totalDueInvestUserAndAmount()['totalDueAmount'] }}
                            </td>

                            <td data-label="@lang('Due Installments')">
                                <span class="custom-badge badge-pill bg-warning">{{ $invest->due_installments }}</span>
                            </td>

                            <td data-label="@lang('Installment Last Date')">
                                {{ customDate($invest->next_installment_date_end) }}
                            </td>


                            <td data-label="@lang('Action')">
                                <a class="btn btn-sm  btn-outline-primary btn-rounded btn-sm investPaymentSingleUser"
                                   href="{{ route('admin.send-email',$invest->user_id) }}">
                                    <span>
                                        <i class="fas fa-envelope-open text-blue"></i> @lang(' Send Mail')
                                    </span>
                                </a>

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
@endpush
