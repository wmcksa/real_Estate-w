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
            <div class="media mb-4 justify-content-end">
                @if(adminAccessRoute(config('role.manage_property.access.add')))
                    <a href="{{route('admin.propertyCreate')}}" class="btn btn-sm btn-primary btn-rounded mr-2">
                        <span><i class="fas fa-plus"></i> @lang('Create New')</span>
                    </a>
                @endif

                @if(adminAccessRoute(config('role.manage_property.access.edit')))
                    <div class="dropdown mb-2 text-right">
                        <button class="btn btn-sm btn-rounded btn-primary dropdown-toggle" type="button"
                                id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span><i class="fas fa-bars pr-2"></i> @lang('Action')</span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <button class="dropdown-item" type="button" data-toggle="modal"
                                    data-target="#all_active">@lang('Active')</button>
                            <button class="dropdown-item" type="button" data-toggle="modal"
                                    data-target="#all_inactive">@lang('Inactive')</button>
                        </div>
                    </div>
                @endif
            </div>


            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped" id="zero_config">
                    <thead class="thead-dark">
                    <tr>
                        @if(adminAccessRoute(config('role.manage_property.access.edit')))
                            <th scope="col" class="text-center">
                                <input type="checkbox" class="form-check-input check-all tic-check" name="check-all"
                                       id="check-all">
                                <label for="check-all"></label>
                            </th>
                        @endif

                        <th scope="col">@lang('Property')</th>
                        <th scope="col">@lang('Investment Amount')</th>
                        <th scope="col">@lang('Total Investment')</th>
                        <th scope="col">@lang('Profit')(@lang('% / fixed'))</th>
                        <th scope="col">@lang('Installment')</th>
                        <th scope="col">@lang('Status')</th>
                        @if(adminAccessRoute(config('role.manage_property.access.edit')) == true || adminAccessRoute(config('role.manage_property.access.delete')) == true)
                            <th scope="col">@lang('Action')</th>
                        @endif
                    </tr>

                    </thead>
                    <tbody>

                    @forelse($manageProperties as $item)
                        <tr>
                            @if(adminAccessRoute(config('role.manage_property.access.edit')))
                                <td class="text-center">
                                    <input type="checkbox" id="chk-{{ $item->id }}"
                                           class="form-check-input row-tic tic-check" name="check"
                                           value="{{ $item->id }}"
                                           data-id="{{ $item->id }}">
                                    <label for="chk-{{ $item->id }}"></label>
                                </td>
                            @endif

                            <td data-label="@lang('Property')">
                                @lang(optional($item->details)->property_title)
                            </td>
                            <td data-label="@lang('Investment Amount')">
                                <p class="font-weight-bold">{{$item->investmentAmount}}</p>
                            </td>

                            <td data-label="@lang('Total Invest')">
                                <p class="font-weight-bold">{{ $basic->currency_symbol }}{{$item->total_investment_amount}}</p>
                            </td>

                            <td data-label="@lang('Profit')">
                                <p class="font-weight-bold"> {{ $item->profit_type == 1 ? $item->profit . '%' : $base_currency . $item->profit }}</p>
                            </td>

                            <td data-label="@lang('Installment')">
                                <p class="font-weight-bold">
                                    @if($item->is_installment == 0)
                                        <span class="custom-badge bg-danger">@lang('No')</span>
                                    @else
                                        <span class="custom-badge bg-success">@lang('yes')</span>
                                    @endif
                                </p>
                            </td>

                            <td data-label="@lang('Status')">
                                @if($item->status == 0)
                                    <span class="custom-badge bg-danger badge-pill">@lang('Deactive')</span>
                                @else
                                    <span class="custom-badge bg-success badge-pill">@lang('Active')</span>
                                @endif
                            </td>

                            @if(adminAccessRoute(config('role.manage_property.access.edit')) == true || adminAccessRoute(config('role.manage_property.access.delete')) == true)
                                <td data-label="@lang('Action')">
                                    @if(adminAccessRoute(config('role.manage_property.access.edit')) == true)
                                        <a href="{{ route('admin.propertyEdit',$item->id) }}"
                                           class="btn btn-sm btn-outline-primary btn-rounded btn-rounded edit-button">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </a>
                                    @endif

                                    <button
                                        class="btn btn-sm btn-outline-primary btn-rounded btn-sm edit-button propertyInvestInfo"
                                        type="button"
                                        data-property="{{ optional($item->details)->property_title }}"
                                        data-totalinvestmentamount="{{ $item->total_investment_amount }}"
                                        data-expiredate="{{ dateTime($item->expire_date) }}"
                                        data-startdate="{{ dateTime($item->start_date) }}"
                                        data-investment="{{ json_encode($item->totalInvestUserAndAmount()) }}">
                                        <span><i class="fas fa-info"></i></span>
                                    </button>
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
                {{ $manageProperties->links('partials.pagination') }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="all_active" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-primary">
                    <h5 class="modal-title">@lang('Active Property Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
                </div>
                <div class="modal-body">
                    <p>@lang("Are you really want to active the properties")</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal"><span>@lang('No')</span></button>
                    <form action="" method="post">
                        @csrf
                        <a href="" class="btn btn-primary active-yes"><span>@lang('Yes')</span></a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="all_inactive" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-primary">
                    <h5 class="modal-title">@lang('DeActive Property Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
                </div>
                <div class="modal-body">
                    <p>@lang("Are you really want to Inactive the properties")</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal"><span>@lang('No')</span></button>
                    <form action="" method="post">
                        @csrf
                        <a href="" class="btn btn-primary inactive-yes"><span>@lang('Yes')</span></a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="delete-modal" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="primary-header-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="primary-header-modalLabel">@lang('Delete Confirmation')
                    </h4>
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">×
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you sure to delete this?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light"
                            data-dismiss="modal">@lang('Close')</button>
                    <form action="" method="post" class="deleteRoute">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-primary">@lang('Yes')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="propertyInvestInfoModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">@lang('Property Investment Info')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <ul class="list-group withdraw-detail">
                        <li class="list-group-item">
                            <span class="font-weight-bold"> @lang('Property'): </span>
                            <span class="font-weight-bold ml-3 propertyName"></span>
                        </li>

                        <li class="list-group-item">
                            <span class="font-weight-bold "> @lang('Total Invested User'): </span>
                            <span class="font-weight-bold ml-3 totalInvestedUser"></span>
                        </li>

                        <li class="list-group-item">
                            <span class="font-weight-bold"> @lang('Expected Amount'): </span>
                            <span class="font-weight-bold ml-3 requiredAmount"></span>
                        </li>

                        <li class="list-group-item">
                            <span class="font-weight-bold "> @lang('Received Amount'): </span>
                            <span class="font-weight-bold ml-3 receivedAmount"></span>
                        </li>

                        <li class="list-group-item">
                            <span class="font-weight-bold "> @lang('Due Amount'): </span>
                            <span class="font-weight-bold ml-3 dueAmount"></span>
                        </li>

                        <li class="list-group-item">
                            <span class="font-weight-bold "> @lang('Start Date'): </span>
                            <span class="font-weight-bold ml-3 startDate"></span>
                        </li>

                        <li class="list-group-item">
                            <span class="font-weight-bold "> @lang('Expire Date'): </span>
                            <span class="font-weight-bold ml-3 expireDate"></span>
                        </li>
                    </ul>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-rounded" data-dismiss="modal">
                        <span>@lang('Cancel')</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('style-lib')
{{--    <link href="{{asset('assets/admin/css/dataTables.bootstrap4.css')}}" rel="stylesheet">--}}
@endpush
@push('js')
{{--    <script src="{{ asset('assets/admin/js/jquery.dataTables.min.js') }}"></script>--}}
{{--    <script src="{{ asset('assets/admin/js/datatable-basic.init.js') }}"></script>--}}


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
        $('.notiflix-confirm').on('click', function () {
            var route = $(this).data('route');
            $('.deleteRoute').attr('action', route)
        })

        $(document).ready(function () {
            $(document).on('click', '#check-all', function () {
                $('input:checkbox').not(this).prop('checked', this.checked);
            });


            $(document).on('click', '.propertyInvestInfo', function () {
                var propertyInvestInfoModal = new bootstrap.Modal(document.getElementById('propertyInvestInfoModal'))
                propertyInvestInfoModal.show();

                let property = $(this).data('property');
                let investment = $(this).data('investment');

                let totalInvestmentAmount = $(this).data('totalinvestmentamount');
                let expireDate = $(this).data('expiredate');
                let startDate = $(this).data('startdate');
                let totalInvestedAmount = investment.totalInvestedAmount;
                let totalInvestedUser = investment.totalInvestedUser;
                let dueAmount = totalInvestmentAmount - totalInvestedAmount;
                let symbol = "{{trans($basic->currency_symbol)}}";


                $('.propertyName').text(property);
                $('.totalInvestedUser').text(totalInvestedUser);
                $('.requiredAmount').text(`${symbol}${totalInvestmentAmount}`);
                $('.receivedAmount').text(`${symbol}${totalInvestedAmount}`);
                $('.dueAmount').text(`${symbol}${dueAmount}`);
                $('.expireDate').text(expireDate);
                $('.startDate').text(startDate);
            });

            $(document).on('change', ".row-tic", function () {
                let length = $(".row-tic").length;
                let checkedLength = $(".row-tic:checked").length;
                if (length == checkedLength) {
                    $('#check-all').prop('checked', true);
                } else {
                    $('#check-all').prop('checked', false);
                }
            });


            //multiple active
            $(document).on('click', '.active-yes', function (e) {
                e.preventDefault();
                var allVals = [];
                $(".row-tic:checked").each(function () {
                    allVals.push($(this).attr('data-id'));
                });

                var strIds = allVals;

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.property-active') }}",
                    data: {strIds: strIds},
                    datatType: 'json',
                    success: function (data) {
                        location.reload();
                    },
                });
            });

            //multiple deactive
            $(document).on('click', '.inactive-yes', function (e) {
                e.preventDefault();
                var allVals = [];
                $(".row-tic:checked").each(function () {
                    allVals.push($(this).attr('data-id'));
                });

                var strIds = allVals;
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.property-inactive') }}",
                    data: {strIds: strIds},
                    datatType: 'json',
                    success: function (data) {
                        location.reload();
                    }
                });
            });
        });
    </script>
@endpush
