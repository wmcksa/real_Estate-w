@extends('admin.layouts.app')
@section('title',trans('investment details'))
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('Property Investment Information')}}
                            <a href="{{ route('admin.investments') }}" class="btn btn-primary btn-rounded btn-sm float-right mb-2"><i class="fa fa-check-circle"></i> {{trans('Back')}}</a>
                        </h4>
                        <hr>

                        <div class="p-4 border shadow-sm rounded">
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="list-style-none">
                                        <li class="my-2 pb-3">
                                            <span class="font-weight-medium text-dark"><i class="far fa-calendar-check mr-2 text-primary" aria-hidden="true"></i> {{trans('Investment Date')}}: <small class="float-right">{{ dateTime($singleInvestDetails->created_at) }}</small></span>
                                        </li>

                                        <li class="my-2 border-bottom pb-3">
                                                <span class="font-weight-medium text-dark"><i class="far fa-building text-success mr-2" aria-hidden="true"></i> {{trans('Property')}}: <small class="float-right"><a href="{{ route('propertyDetails',[slug(optional($singleInvestDetails->property->details)->property_title), optional($singleInvestDetails->property)->id]) }}">
                                                                @lang(optional($singleInvestDetails->property->details)->property_title)
                                                            </a></small></span>

                                        </li>


                                        <li class="my-3">
                                                <span><i class="icon-check mr-2 text--site text-warning"></i> {{trans('Transaction Id')}} : <span
                                                        class="font-weight-medium text-dark">{{ $singleInvestDetails->trx }}</span></span>
                                        </li>

                                        <li class="my-3">
                                            <span><i class="icon-check mr-2 text--site text-primary"></i> {{trans('Invest')}} : <span class="font-weight-medium text-primary">{{ config('basic.currency_symbol') }}{{ $singleInvestDetails->amount }}</span></span>
                                        </li>

                                        <li class="my-3">
                                            <span><i class="icon-check far fa-times-circle mr-2 text--site text-primary"></i> {{trans('Profit')}} : <span class="font-weight-medium text-primary">{{ config('basic.currency_symbol') }}{{ $singleInvestDetails->net_profit }}</span></span>
                                        </li>

                                        @if($singleInvestDetails->is_installment == 1)
                                            <li class="my-3">
                                                <span class="font-weight-medium text-dark"><i
                                                        class="icon-check mr-2 text--site text-success"></i> @lang('Total Installment') : <span
                                                        class="font-weight-medium text-success"> {{ $singleInvestDetails->total_installments }} </span></span>
                                            </li>

                                            <li class="my-3">
                                                <span class="font-weight-medium text-dark"><i
                                                        class="icon-check mr-2 text--site text-success"></i> @lang('Due Installment') : <span
                                                        class="font-weight-medium text-success"> {{ $singleInvestDetails->due_installments }} </span></span>
                                            </li>
                                            <li class="my-3">
                                                <span class="font-weight-medium text-dark"><i
                                                        class="icon-check mr-2 text--site text-purple"></i> @lang('Next Installment Date Start') : <span
                                                        class="font-weight-medium text-purple"> {{ dateTime($singleInvestDetails->next_installment_date_start) }} </span></span>
                                            </li>
                                            <li class="my-3">
                                                <span class="font-weight-medium text-dark"><i
                                                        class="icon-check mr-2 text--site text-purple"></i> @lang('Next Installment Date End') : <span
                                                        class="font-weight-medium text-purple"> {{ dateTime($singleInvestDetails->next_installment_date_end) }} </span></span>
                                            </li>
                                        @endif


                                        <li class="my-3">
                                            <span><i class="icon-check mr-2 text--site text-primary"></i> @lang('Return Interval') : <span
                                                    class="font-weight-bold text-primary">{{ $singleInvestDetails->return_time }} {{ $singleInvestDetails->return_time_type }}</span></span>
                                        </li>

                                        <li class="my-3">
                                            <span><i class="icon-check mr-2 text--site text-success {{ $singleInvestDetails->how_many_times == 0 ? 'text-danger' : 'text-success' }}"></i> @lang('Return How Many Times') : <span
                                                    class="font-weight-bold text-success">{{ $singleInvestDetails->how_many_times == null ? 'Lifetime' : $singleInvestDetails->how_many_times }}</span></span>
                                        </li>

                                        <li class="my-3">
                                            <span>
                                                <i class="icon-check mr-2 text--site text-success"></i> {{trans('Next Return Date')}} :
                                                @if($singleInvestDetails->invest_status == 0)
                                                    <span class="custom-badge bg-danger badge-pill">@lang('After All Installment completed')</span>
                                                @elseif($singleInvestDetails->invest_status == 1 && $singleInvestDetails->return_date == null && $singleInvestDetails->status == 1)
                                                    <span class="custom-badge bg-success badge-pill">@lang('completed')</span>
                                                @else
                                                    <span class="font-weight-bold text-dark">{{ customDate($singleInvestDetails->return_date) }}</span>
                                                @endif
                                            </span>
                                        </li>

                                        <li class="my-3">
                                            <span>
                                                <i class="mr-2 text--site  {{ $singleInvestDetails->last_return_date != null ? 'far fa-check-circle text-success' : 'far fa-times-circle text-danger' }}"></i> {{trans('Last Return Date')}} :
                                                <span class="font-weight-bold text-warning {{ $singleInvestDetails->last_return_date != null ? 'text-dark' : 'text-danger' }}">{{ $singleInvestDetails->last_return_date != null ? customDate($singleInvestDetails->last_return_date) : 'N/A' }}</span>
                                            </span>
                                        </li>

                                        <li class="my-3">
                                            <span><i class="icon-check mr-2 text--site text-warning"></i> {{trans('Investment Payment Status')}} :

                                                <span class="custom-badge badge-pill {{ $singleInvestDetails->invest_status == 1 ? 'bg-success': 'bg-warning' }}">{{ $singleInvestDetails->invest_status == 1 ? trans('Complete'): trans('Due') }}</span></span>
                                        </li>

                                        <li class="my-3">
                                            <span><i class="icon-check mr-2 text--site text-warning"></i> {{trans('Profit Return Status')}} :

                                                <span class="custom-badge badge-pill {{ $singleInvestDetails->status == 1 ? 'bg-success' : 'bg-primary' }}">{{ $singleInvestDetails->status == 1 ? trans('Completed') : trans('Running') }}</span></span>
                                        </li>

                                        <li class="my-3">
                                            <span><i class="icon-check mr-2 text--site text-warning"></i> {{trans('Investment Status')}} :
                                            <span class="custom-badge badge-pill {{ $singleInvestDetails->is_active == 1 ? 'bg-success' : 'bg-danger' }}">{{ $singleInvestDetails->is_active == 1 ? trans('Active') : trans('Deactive') }}</span></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

