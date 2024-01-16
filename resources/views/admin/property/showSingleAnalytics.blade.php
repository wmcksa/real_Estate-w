@extends('admin.layouts.app')
@section('title')
    @lang("Analytics Details")
@endsection

@section('content')
    <style>
        .fa-ellipsis-v:before {
            content: "\f142";
        }
    </style>

    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th>@lang('#')</th>
                        <th>@lang('Country')</th>
                        <th>@lang('City')</th>
                        <th>@lang('Visitor Ip')</th>
                        <th>@lang('Browser')</th>
                        <th>@lang('Operating System')</th>
                        <th>@lang('Visited At')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($allSinglePropertyAnalytics as $key => $analytic)

                        <tr>
                            <td data-label="@lang('No.')">{{loopIndex($allSinglePropertyAnalytics) + $key}}</td>
                            <td data-label="@lang('Country')">
                                {{ ($analytic->country) ? __($analytic->country) : __('N/A') }}
                            </td>

                            <td data-label="@lang('City')">
                                {{ ($analytic->city) ? __($analytic->city) : __('N/A') }}
                            </td>

                            <td data-label="@lang('Visitor Ip')">
                                @lang($analytic->visitor_ip)
                            </td>

                            <td data-label="@lang('Browser')">
                                @lang($analytic->browser)
                            </td>

                            <td data-label="@lang('Operating System')">
                                @lang($analytic->os_platform)
                            </td>

                            <td data-label="@lang('Visited At')">
                                {{ dateTime($analytic->created_at) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center text-danger" colspan="100%">@lang('No Data Found')</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                {{$allSinglePropertyAnalytics->appends($_GET)->links('partials.pagination')}}
            </div>
        </div>
    </div>
@endsection

