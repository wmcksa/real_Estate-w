@extends('admin.layouts.app')
@section('title')
    @lang($title)
@endsection
@section('content')
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <form action="{{route('admin.investments.search')}}" method="get">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="user">@lang('Search User')</label>
                                <input type="text" name="user" value="{{@request()->user}}" class="form-control" placeholder="@lang('Search by user')">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="property">@lang('Property')</label>
                                <input type="text" name="property" value="{{@request()->property}}" class="form-control" placeholder="@lang('Search property')">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group ">
                                <label for="invest_status">@lang('Invest payment Status')</label>
                                <select class="form-control type" name="invest_status">
                                    <option value="">@lang('All')</option>
                                    <option value="1" @if(@request()->invest_status == '1') selected @endif>@lang('Complete')</option>
                                    <option value="0" @if(@request()->invest_status == '0') selected @endif>@lang('Due')</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group ">
                                <label for="return_status">@lang('Profit Return Status')</label>
                                <select class="form-control type" name="return_status">
                                    <option value="">@lang('All')</option>
                                    <option value="0" @if(@request()->return_status == '0') selected @endif>@lang('Running')</option>
                                    <option value="1" @if(@request()->return_status == '1') selected @endif>@lang('Completed')</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group ">
                                <label for="status">@lang('Investment Status')</label>
                                <select name="status" class="form-control type">
                                    <option value="">@lang('All')</option>
                                    <option value="1" @if(@request()->status == '1') selected @endif>@lang('Active')</option>
                                    <option value="0" @if(@request()->status == '0') selected @endif>@lang('Deactive')</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="from_date">@lang('From Date')</label>
                                <input type="date" class="form-control from_date" id="datepicker" name="from_date" value="{{ old('from_date',request()->from_date) }}" placeholder="@lang('From date')"/>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="to_date">@lang('To Date')</label>
                                <input type="date" class="form-control to_date" id="datepicker" name="to_date" value="{{ old('to_date',request()->to_date) }}" placeholder="@lang('To date')" disabled="true"/>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="return_status" class="search__invest">@lang('submit')</label>
                                <button type="submit" class="btn btn-block btn-primary btn-rounded"><i class="fas fa-search"></i> @lang('Search')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div class="col-md-auto invest_check_box mt-2 p-0 d-none">
                    <div class="form-group">
                        @if(adminAccessRoute(config('role.investments.access.edit')) == true)
                            <button class="btn btn-outline-primary btn-rounded btn-rounded btn-sm d-inline btn-sm" data-toggle="modal" data-target="#active_selected_invests">{{ __('Active') }}</button>
                            <button class="btn btn-outline-warning btn-rounded btn-sm d-inline btn-sm" data-toggle="modal" data-target="#deactive_selected_invests">{{ __('Deactive') }}</button>
                        @endif
                    </div>
                </div>
            </div>
            <table class="categories-show-table table table-hover table-striped">
                <thead class="thead-dark">
                <tr>
                    @if(adminAccessRoute(config('role.investments.access.edit')) == true)
                        <th scope="col" class="text-center">
                            <input type="checkbox" class="form-check-input check-all tic-check check_all" value="1" name="check-all"
                                   id="check-all" data-status="all">
                            <label for="check-all"></label>
                        </th>
                    @endif
                    <th>@lang('SL')</th>
                    <th>@lang('User')</th>
                    <th>@lang('Property')</th>
                    <th>@lang('Invest Amount')</th>
                    <th>@lang('Profit')</th>
                    <th>@lang('Upcoming Payment')</th>
                    <th>@lang('Profit Status')</th>
                    <th>@lang('Payment Status')</th>
                    <th>@lang('Investment Status')</th>
                    <th scope="col">@lang('Action')</th>

                </tr>
                </thead>
                <tbody>
                @forelse($investments as $key => $invest)
                    <tr>
                        @if(adminAccessRoute(config('role.investments.access.edit')) == true || adminAccessRoute(config('role.investments.access.delete')) == true)
                            <td class="text-center">
                                <input type="checkbox" name="id[]"
                                       value="{{ $invest->id }}"
                                       class="form-check-input row-tic tic-check"
                                       id="customCheck{{ $invest->id }}"
                                       data-id="{{ $invest->id }}">
                                <label for="customCheck{{ $invest->id }}"></label>
                            </td>
                        @endif
                        <td data-label="@lang('SL')">
                            {{loopIndex($investments) + $key}}
                        </td>

                        <td data-label="@lang('User')">
                            <a href="{{route('admin.user-edit',$invest->user_id)}}" target="_blank">
                                <div class="d-flex no-block align-items-center">
                                    <div class="mr-3"><img src="{{getFile(config('location.user.path').optional($invest->user)->image) }}" alt="user" class="rounded-circle" width="45" height="45">
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

                        <td data-label="@lang('Property')">
                            <a href="{{ route('propertyDetails',[@slug(optional($invest->property->details)->property_title), optional($invest->property)->id]) }}" target="_blank">
                                <div class="d-flex no-block align-items-center">
                                    <div class="mr-3"><img src="{{getFile(config('location.propertyThumbnail.path').optional($invest->property)->thumbnail) }}" alt="@lang('property_thumbnail')" class="rounded-circle" width="45" height="45">
                                    </div>
                                    <div class="">
                                        <h5 class="text-dark mb-0 font-16 font-weight-medium">
                                            @lang(\Illuminate\Support\Str::limit(optional($invest->property->details)->property_title, 30))
                                        </h5>
                                        <span class="text-muted font-14">@lang('Invested: ')<span>{{ config('basic.currency_symbol') }}</span>{{ (int)$invest->amount }}</span>
                                    </div>
                                </div>
                            </a>
                        </td>

                        <td data-label="@lang('Invest Amount')">
                            {{ optional($invest->property)->investmentAmount }}
                        </td>

                        <td data-label="@lang('Profit')">
                            @if($invest->invest_status == 1)
                                {{ config('basic.currency_symbol') }}{{ $invest->net_profit }}
                            @else
                                {{ config('basic.currency_symbol') }}@lang('0.00')
                            @endif

                        </td>

                        <td data-label="@lang('Upcoming Payment')">
                            @if($invest->invest_status == 0)
                                <span class="custom-badge badge-pill bg-danger">@lang('N/A')</span>
                            @else
                                {{ customDate($invest->return_date) }}
                            @endif
                        </td>

                        <td data-label="@lang('Profit Status')">
                            <span class="custom-badge badge-pill {{ $invest->status == 1 ? 'bg-success' : 'bg-primary' }}">{{ $invest->status == 1 ? trans('Completed') : trans('Running') }}</span>
                        </td>

                        <td data-label="@lang('Payment Status')">
                            <span class="custom-badge badge-pill {{ $invest->invest_status == 1 ? 'bg-success' : 'bg-warning' }}">{{ $invest->invest_status == 1 ? trans('clear') : trans('Due') }}</span>
                        </td>


                        <td data-label="@lang('Status')">
                            <span class="custom-badge badge-pill {{ $invest->is_active == 1 ? 'bg-success' : 'bg-warning' }}">{{ $invest->is_active == 1 ? trans('Active') : trans('Deactive') }}</span>
                            @if($invest->is_active == 0)
                                <sup>
                                    <a href="javascript:void(0)"
                                       title="@lang('Deactive Reason')"
                                       data-investor="@lang(optional($invest->user)->firstname) @lang(optional($invest->user)->lastname)"
                                       data-title="{{ optional($invest->property->details)->property_title }}"
                                       data-deactivereason="{{ $invest->deactive_reason }}"
                                       class="info-listing-btn investDeactiveInfo" aria-labelledby="dropdownMenuLink">  <i class="fas fa-info"></i>
                                    </a>
                                </sup>
                            @endif
                        </td>

                        <td data-label="@lang('Action')">
                            <div class="dropdown show">
                                <a class="dropdown-toggle p-3" href="#" id="dropdownMenuLink" data-toggle="dropdown"
                                   aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <a class="dropdown-item" href="{{ route('admin.investmentDetails', $invest->id) }}">
                                        <i class="far fa-eye text-primary pr-2"
                                           aria-hidden="true"></i> @lang('Details')
                                    </a>
                                    @if(adminAccessRoute(config('role.investments.access.edit')) == true)
                                        <a
                                            @if($invest->is_active == 0)
                                                class="dropdown-item activeDeactiveInvest investActive cursor-pointer"
                                            data-id="{{ $invest->id }}"
                                            data-title="{{ optional($invest->property->details)->property_title }}"
                                            @else
                                                class="dropdown-item activeDeactiveInvest investDeactive cursor-pointer"
                                            data-id="{{ $invest->id }}"
                                            data-title="{{ optional($invest->property->details)->property_title }}"
                                            @endif>
                                            @if($invest->is_active == 0)
                                                <i class="fa fa-toggle-off pr-2 text-purple"></i> @lang('Active')
                                            @else
                                                <i class="fa fa-toggle-on pr-2 text-warning"></i> @lang('Deactive')
                                            @endif
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center text-danger" colspan="100%">@lang('No Investment Data')</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {{ $investments->links('partials.pagination') }}
        </div>
    </div>

    @push('adminModal')
        <!-- Single Active Invest Modal -->
        <div id="investActiveModal" class="modal fade" tabindex="-1" role="dialog"
             aria-labelledby="primary-header-modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-primary">
                        <h4 class="modal-title" id="primary-header-modalLabel">@lang('Active Confirmation')
                        </h4>
                        <button type="button" class="close" data-dismiss="modal"
                                aria-hidden="true">×
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="showPropertyTitle"> </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-rounded"
                                data-dismiss="modal">@lang('Close')</button>
                        <form action="{{ route('admin.investActive') }}" method="post">
                            @csrf
                            <input type="hidden" value="" class="investActiveId" name="invest_id">
                            <button type="submit" class="btn btn-primary btn-rounded">@lang('Yes')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Multiple Active Invest Modal -->
        <div id="active_selected_invests" class="modal fade" tabindex="-1" role="dialog"
             aria-labelledby="primary-header-modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-primary">
                        <h4 class="modal-title" id="primary-header-modalLabel">@lang('Active Confirmation')
                        </h4>
                        <button type="button" class="close" data-dismiss="modal"
                                aria-hidden="true">×
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>@lang('Are you sure investment active?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-rounded"
                                data-dismiss="modal">@lang('Close')</button>
                        <form action="{{ route('admin.multiple.invest.active') }}" method="post">
                            @csrf
                            <input type="hidden" name="investment_id" class="invest_id_checked">
                            <button type="submit" class="btn btn-primary btn-rounded">@lang('Yes')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Single Deactive Invest Modal -->
        <div class="modal fade" id="investDeactiveModal" role="dialog">
            <div class="modal-dialog">
                <form action="{{ route('admin.investDeactive') }}" method="post">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header modal-colored-header bg-primary">
                            <h5 class="modal-title">@lang('Deactive Investment Confirmation')</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
                        </div>

                        <div class="modal-body">
                            <p class="showPropertyTitle"></p>

                            <div class="form-group">
                                <label for="">@lang('Write you reason')</label> <span class="text-danger">*</span>
                                <input type="hidden" value="" name="invest_id" class="investDeactiveId">
                                <textarea name="deactive_reason" id="deactive_reason" required rows="4" class="form-control @error('deactive_reason') is-invalid @enderror" placeholder="@lang('type here...')"></textarea>
                                <div class="invalid-feedback">
                                    @error('deactive_reason') @lang($message) @enderror
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-rounded" data-dismiss="modal"><span>@lang('No')</span></button>

                            @csrf
                            <button type="submit" class="btn btn-primary btn-rounded">@lang('Yes')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Multiple Deactive Invest Modal -->
        <div class="modal fade" id="deactive_selected_invests" role="dialog">
            <div class="modal-dialog">
                <form action="{{ route('admin.multiple.invest.deactive') }}" method="post">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header modal-colored-header bg-primary">
                            <h5 class="modal-title">@lang('Are You Sure Deactive Investment?')</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <label for="">@lang('Write you reason')</label> <span class="text-danger">*</span>
                                <input type="hidden" value="" name="invest_id" class="investDeactiveId">
                                <textarea name="deactive_reason" id="deactive_reason" rows="4" class="form-control @error('deactive_reason') is-invalid @enderror" placeholder="@lang('type here...')"></textarea>
                                <div class="invalid-feedback">
                                    @error('deactive_reason') @lang($message) @enderror
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-dismiss="modal"><span>@lang('Close')</span></button>
                            <input type="hidden" name="investment_id" class="invest_id_checked">
                            <button type="submit" class="btn btn-primary">@lang('Yes')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Invest Deactive Info Modal -->
        <div class="modal fade" id="investDeactiveInfoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-primary">
                        <h4 class="modal-title" id="myModalLabel">@lang('Invest Deactive Information')</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <form role="form" method="POST" class="actionRoute" action="" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="modal-body">
                            <ul class="list-group">
                                <li class="list-group-item propertyInvestor"></li>
                                <li class="list-group-item propertyTitle"></li>
                                <li class="list-group-item deactiveReason"></li>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-rounded" data-dismiss="modal">@lang('Close')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endpush

@endsection

@push('js')
    <script>
        'use strict'
        $(document).ready(function (){
            var invest_array = [];
            $(document).on('click', '.check_all', function (){
                invest_array = [];
                if(this.checked){
                    console.log('checked');
                    $('.invest_check_box').removeClass('d-none');
                    $('.invest_check_box').removeClass('d-none');

                    $('.row-tic').each(function(){
                        $(this).prop('checked', true);
                        invest_array.push($(this).attr('data-id'));
                        $('.invest_id_checked').val(invest_array);
                        $('.selected_invest').val(invest_array);
                    });

                }else{

                    $('.invest_check_box').addClass('d-none');

                    $('.row-tic').each(function(){

                        $(this).prop('checked', false);
                        $('.invest_id_checked').val(invest_array);
                        $('.selected_invest').val(invest_array);

                    });
                }

                if(invest_array.length == 0)
                {
                    $('.invest_check_box').addClass('d-none');
                }else{
                    $('.invest_check_box').removeClass('d-none');
                }
            })


            $(document).on("click", ".row-tic", function(){
                var data_id = $(this).attr('data-id');
                $('.row-tic').each(function(){
                    if($(this).is(':checked')){
                        $('.check_all').prop('checked', true)
                    } else{
                        $('.check_all').prop('checked', false)
                        return false;
                    }
                });

                if(invest_array.indexOf(data_id)  != -1){
                    invest_array = invest_array.filter(item => item !== data_id)
                    $('.invest_id_checked').val(invest_array);
                    $('.selected_invest').val(invest_array);
                }
                else{
                    invest_array.push(data_id)
                    $('.invest_id_checked').val(invest_array);
                    $('.selected_invest').val(invest_array);
                }


                if(invest_array.length == 0)
                {
                    $('.invest_check_box').addClass('d-none');
                }else{
                    $('.invest_check_box').removeClass('d-none');
                }
            });
        })
    </script>































    <script>
        'use strict'
        $('.from_date').on('change', function (){
            $('.to_date').removeAttr('disabled');
        });

        $(document).on('click', '.investActive', function () {
            console.log('abc');
            var showInvestActiveModal = new bootstrap.Modal(document.getElementById('investActiveModal'))
            showInvestActiveModal.show();
            let InvestId = $(this).data('id');
            let propertyTitle = $(this).data('title');
            $('.showPropertyTitle').text(`@lang('Are you sure to active ') ${propertyTitle} @lang(' Property Investment?')`);
            $('.investActiveId').val(InvestId);
        });

        $(document).on('click', '.investDeactive', function () {
            var showPropertyDeactiveModal = new bootstrap.Modal(document.getElementById('investDeactiveModal'))
            showPropertyDeactiveModal.show();

            let InvestId = $(this).data('id');
            let propertyTitle = $(this).data('title');
            $('.showPropertyTitle').text(`@lang('Are you sure to deactive ') ${propertyTitle} @lang(' Property Investment?')`);
            $('.investDeactiveId').val(InvestId);
        });

        $(document).on('click', '.investDeactiveInfo', function () {
            var investDeactiveInfoModal = new bootstrap.Modal(document.getElementById('investDeactiveInfoModal'));
            investDeactiveInfoModal.show();

            let propertyInvestor = $(this).data('investor');
            let propertyTitle = $(this).data('title');
            let deactiveReason = $(this).data('deactivereason');

            $('.propertyInvestor').text(`@lang('Investor: ') ${propertyInvestor}`);
            $('.propertyTitle').text(`@lang('Property: ') ${propertyTitle}`);
            $('.deactiveReason').text(`@lang('Deactive Reason: ') ${deactiveReason}`);
        });
    </script>



    <script>
        "use strict";
        $(document).ready(function () {
            $(document).on('click', '#check-all', function () {
                $('input:checkbox').not(this).prop('checked', this.checked);
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

            $('.notiflix-confirm').on('click', function () {
                var route = $(this).data('route');
                $('.deleteRoute').attr('action', route)
            })

        });
    </script>
@endpush
