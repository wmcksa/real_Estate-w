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
                        <th scope="col">@lang('Last Return Date')</th>
                        <th scope="col">@lang('Payment Status')</th>
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
                                <a href="{{ route('admin.seeInvestedUser',['property_id' => optional($invest->property)->id, 'type' => 'completed']) }}"><span class="custom-badge bg-success badge-pill">{{ optional($invest->property)->totalCompletedInvestUserAndAmount()['totalInvestedUser'] }}</span></a>
                            </td>

                            <td data-label="@lang('Total Invested Amount')">
                                {{ config('basic.currency_symbol') }}{{ optional($invest->property)->totalCompletedInvestUserAndAmount()['totalInvestedAmount'] }}
                            </td>


                            <td data-label="@lang('Last Return Date')">
                                {{ customDate($invest->last_return_date) }}
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

                            <td data-label="@lang('Action')">
                                <a href="{{ route('admin.seeInvestedUser',['property_id' => optional($invest->property)->id, 'type' => 'completed']) }}" class="btn btn-sm btn-outline-primary btn-rounded btn-rounded edit-button">
                                    <i class="far fa-eye"></i>
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
