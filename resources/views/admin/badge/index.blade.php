@extends('admin.layouts.app')
@section('title')
    @lang($page_title)
@endsection

@section('content')

    <div class="bd-callout bd-callout-warning m-0 m-md-4 my-4 m-md-0 ">
        <i class="fas fa-info-circle mr-2"></i> @lang("N.B: Pull up or down the rows to sort the ranking list order that how do you want to display the ranking in admin and user panel.")
    </div>

    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="media mb-4 justify-content-end">
                @if(adminAccessRoute(config('role.manage_badge.access.add')))
                    <a href="{{route('admin.badgeCreate')}}" class="btn btn-sm  btn-primary btn-rounded mr-2">
                        <span><i class="fas fa-plus"></i> @lang('Create New')</span>
                    </a>
                @endif
            </div>

            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">@lang('Rank Name')</th>
                        <th scope="col">@lang('Rank Lavel')</th>
                        <th scope="col">@lang('Badge Icon')</th>
                        <th scope="col">@lang('Min Invest')</th>
                        <th scope="col">@lang('Min Deposit')</th>
                        <th scope="col">@lang('Min Earning')</th>
                        <th scope="col">@lang('Bonus')</th>
                        <th scope="col">@lang('Status')</th>
                        @if(adminAccessRoute(config('role.manage_badge.access.edit')) == true)
                            <th scope="col">@lang('Action')</th>
                        @endif
                    </tr>
                    </thead>

                    <tbody id="sortable">
                    @forelse($allBadges as $item)
                        <tr data-id="{{ $item->id }}">
                            <td data-label="@lang('Rank Name')">
                                @lang(optional($item->details)->rank_name)
                            </td>
                            <td data-label="@lang('Rank Level')">
                                <p class="font-weight-bold">{{optional($item->details)->rank_level}}</p>
                            </td>

                            <td data-label="@lang('Rank icon')">
                                <img src="{{ getFile(config('location.badge.path').$item->badge_icon)}}"
                                     alt="@lang('not found')" width="60">
                            </td>

                            <td data-label="@lang('Minimum Earning')">
                                <p class="font-weight-bold">{{$item->min_invest}} {{ config('basic.currency') }}</p>
                            </td>

                            <td data-label="@lang('Minimum Earning')">
                                <p class="font-weight-bold">{{$item->min_deposit}} {{ config('basic.currency') }}</p>
                            </td>

                            <td data-label="@lang('Minimum Earning')">
                                <p class="font-weight-bold">{{$item->min_earning}} {{ config('basic.currency') }}</p>
                            </td>

                            <td data-label="@lang('Bonus')">
                                <p class="font-weight-bold">@lang($item->bonus) {{ config('basic.currency') }}</p>
                            </td>

                            <td data-label="@lang('Status')">
                                <span
                                    class="custom-badge {{ $item->status == 1 ? 'bg-success' : 'bg-danger' }}">@lang($item->status == 1 ? 'Active' : 'Deactive')</span>
                            </td>

                            @if(adminAccessRoute(config('role.manage_badge.access.edit')) == true)
                                <td data-label="@lang('Action')">
                                    @if(adminAccessRoute(config('role.manage_badge.access.edit')))
                                        <a href="{{ route('admin.badgeEdit',$item->id) }}"
                                           class="btn btn-sm btn-outline-primary btn-rounded edit-button">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                </td>
                            @endif
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
    <link rel="stylesheet" href="{{ asset('assets/admin/css/jquery-ui.min.css') }}">
    <link href="{{asset('assets/admin/css/dataTables.bootstrap4.css')}}" rel="stylesheet">
@endpush
@push('js')
    <script src="{{ asset('assets/global/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery.dataTables.min.js') }}"></script>
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
        "use strict";
        $(document).ready(function () {
            $("#sortable").sortable({
                update: function (event, ui) {
                    var methods = [];
                    $('#sortable tr').each(function (key, val) {
                        let methodId = $(val).data('id');
                        methods.push(methodId);
                    });

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        'url': "{{ route('admin.sort.badges') }}",
                        'method': "POST",
                        'data': {
                            sort: methods
                        },
                        success: function (data) {
                            console.log(data);
                        }

                    });

                }
            });
            $("#sortable").disableSelection();
        });


    </script>
@endpush
