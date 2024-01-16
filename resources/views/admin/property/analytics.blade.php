@extends('admin.layouts.app')
@section('title')
    @lang("Property Analytics")
@endsection

@section('content')
    <style>
        .fa-ellipsis-v:before {
            content: "\f142";
        }
    </style>
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <form action="" method="get" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-3 col-xl-3 col-sm-12">
                            <div class="form-group">
                                <label for="title"> @lang('Property')</label>
                                <input
                                    type="text"
                                    name="property"
                                    value="{{ old('property',request()->property) }}"
                                    class="form-control"
                                    placeholder="@lang('Search property...')"
                                />
                            </div>
                        </div>

                        <div class="col-md-3 col-xl-3 col-sm-12">
                            <div class="form-group">
                                <label for="title"> @lang('From Date')</label>
                                <input type="date" class="form-control from_date" name="from_date" id="datepicker" placeholder="@lang('From date')" value="{{ old('from_date', request()->from_date) }}"/>
                            </div>
                        </div>

                        <div class="col-md-3 col-xl-3 col-sm-12">
                            <div class="form-group">
                                <label for="title"> @lang('To Date')</label>
                                <input type="date" class="form-control to_date" name="to_date" id="datepicker" placeholder="@lang('To date')" value="{{ old('to_date', request()->to_date) }}" disabled="true"/>
                            </div>
                        </div>

                        <div class="col-md-3 col-xl-3 col-sm-12">
                            <div class="form-group">
                                <label></label>
                                <button type="submit" class="btn w-100 btn-primary listing-btn-search-custom mt-2"><i
                                        class="fas fa-search"></i> @lang('Search')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                    <th>@lang('#')</th>
                    <th>@lang('Property')</th>
                    <th>@lang('Country')</th>
                    <th>@lang('Total Visit')</th>
                    <th>@lang('Last Visited At')</th>
                    <th class="text-end">@lang('Action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($allAnalytics as $key => $analytics)
                        <tr>
                            <td data-label="@lang('No.')">{{loopIndex($allAnalytics) + $key}}</td>
                            <td data-label="@lang('property')">
                                <a href="{{ route('propertyDetails',[slug(optional(optional($analytics->getProperty)->details)->property_title), optional($analytics->getProperty)->id]) }}" class="color-change-listing" target="_blank">{{ \Illuminate\Support\Str::limit(optional(optional($analytics->getProperty)->details)->property_title, 20)  }}</a>
                            </td>

                            <td data-label="@lang('Country')">
                                @lang($analytics->country)
                            </td>

                            <td data-label="@lang('Total Visit')">
                                {{$analytics->total_count_count}}
                            </td>
                            <td data-label="@lang('Last Visited At')">
                                {{ dateTime(optional($analytics->lastVisited)->created_at) }}
                            </td>

                            <td>
                                <a class="btn btn-outline-primary btn-sm rounded btn-icon edit_button" href="{{ route('admin.showPropertyAnalytics', $analytics->manage_property_id) }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center text-danger" colspan="100%">@lang('No Data Found')</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                {{$allAnalytics->appends($_GET)->links('partials.pagination')}}
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        'use strict'
        $(document).ready(function () {
            $('.from_date').on('change', function (){
                $('.to_date').removeAttr('disabled')
            });
        });
    </script>
@endpush
