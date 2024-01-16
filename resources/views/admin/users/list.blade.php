@extends('admin.layouts.app')
@section('title')
    @lang("User List")
@endsection

@section('content')
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <form action="{{ route('admin.users.search') }}" method="get">
                    <div class="row">
                        <div class="col-md-3 col-lg-3 col-12">
                            <div class="form-group">
                                <label>@lang('Search By User')</label>
                                <input type="text" name="search" value="{{@request()->search}}" class="form-control"
                                       placeholder="@lang('Type Here')">
                            </div>
                        </div>

                        <div class="col-md-3 col-lg-3 col-12">
                            <div class="form-group">
                                <label>@lang('Status')</label>
                                <select name="status" class="form-control">
                                    <option value="">@lang('All')</option>
                                    <option value="1"
                                            @if(@request()->status == '1') selected @endif>@lang('Active User')</option>
                                    <option value="0"
                                            @if(@request()->status == '0') selected @endif>@lang('Inactive User')</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 col-lg-3 col-12">
                            <label for="from_date">@lang('From Date')</label>
                            <input type="date" class="form-control from_date" name="from_date" value="{{ old('from_date',request()->from_date) }}" placeholder="@lang('From date')" autocomplete="off"/>
                        </div>

                        <div class="col-md-3 col-lg-3 col-12">
                            <label for="to_date">@lang('To Date')</label>
                            <input type="date" class="form-control to_date" name="to_date" value="{{ old('to_date',request()->to_date) }}" placeholder="@lang('To date')" autocomplete="off" disabled="true"/>
                        </div>


                        <div class="col-md-12 col-lg-12 col-12">
                            <div class="form-group">
                                <label for="" class="opacity-0">@lang('...')</label>
                                <button type="submit" class="btn w-100 d-block btn-primary btn-rounded"><i
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
            @if(adminAccessRoute(config('role.manage_user.access.edit')))
                <div class="dropdown mb-2 text-right">
                    <button class="btn btn-sm  btn-primary btn-rounded dropdown-toggle" type="button" id="dropdownMenuButton"
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

            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        @if(adminAccessRoute(config('role.manage_user.access.edit')))
                            <th scope="col" class="text-center">
                                <input type="checkbox" class="form-check-input check-all tic-check" name="check-all"
                                       id="check-all">
                                <label for="check-all"></label>
                            </th>
                        @endif
                        <th scope="col">@lang('No.')</th>
                        <th scope="col">@lang('Name')</th>
                        <th scope="col">@lang('Upline')</th>
                        <th scope="col">@lang('Main Balance')</th>
                        <th scope="col">@lang('Interest Balance')</th>
                        <th scope="col">@lang('Last Login')</th>
                        <th scope="col">@lang('Status')</th>
                        @if(adminAccessRoute(config('role.manage_user.access.edit')))
                            <th scope="col">@lang('Action')</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            @if(adminAccessRoute(config('role.manage_user.access.edit')))
                                <td class="text-center">
                                    <input type="checkbox" id="chk-{{ $user->id }}"
                                           class="form-check-input row-tic tic-check" name="check" value="{{$user->id}}"
                                           data-id="{{ $user->id }}">
                                    <label for="chk-{{ $user->id }}"></label>
                                </td>
                            @endif
                            <td data-label="@lang('No.')">{{loopIndex($users) + $loop->index}}</td>
                            <td data-label="@lang('Name')">
                                <a href="{{route('admin.user-edit',[$user->id])}}" target="_blank">
                                    <div class="d-flex no-block align-items-center">
                                        <div class="mr-3 thumb">
                                            <img src="{{getFile(config('location.user.path').$user->image) }}" alt="user" class="rounded-circle user-img" width="45" height="45">
                                            @if($user->last_level != null)
                                                <img src="{{ getFile(config('location.badge.path').optional($user->userBadge)->badge_icon) }}" alt="@lang('badge icon')" class="rank-badge" data-toggle="tooltip" data-placement="top" title="{{ optional($user->userBadge->details)->rank_level }} ({{ optional($user->userBadge->details)->rank_name }})">
                                            @endif
                                        </div>
                                        <div class="">
                                            <h5 class="text-dark mb-0 font-16 font-weight-medium">@lang($user->firstname) @lang($user->lastname) </h5>
                                            <span class="text-muted font-14"><span>@</span>@lang($user->username)</span>
                                        </div>
                                    </div>
                                </a>
                            </td>
                            <td data-label="@lang('Upline')">
                                @if(isset($user->referral_id))
                                    <a href="{{route('admin.user-edit',[$user->referral_id])}}" target="_blank">
                                        <div class="d-flex no-block align-items-center">
                                            <div class="mr-3 thumb">
                                                <img src="{{getFile(config('location.user.path').optional($user->referral)->image) }}" alt="user" class="rounded-circle user-img" width="45" height="45">
                                                @if($user->referral->last_level != null)
                                                    <img src="{{ getFile(config('location.badge.path').optional($user->referral->userBadge)->badge_icon) }}" alt="@lang('badge icon')" class="rank-badge" data-toggle="tooltip" data-placement="top" title="{{ optional($user->referral->userBadge->details)->rank_level }} ({{ optional($user->referral->userBadge->details)->rank_name }})">
                                                @endif
                                            </div>

                                            <div class="">
                                                <h5 class="text-dark mb-0 font-16 font-weight-medium">@lang(optional($user->referral)->firstname) @lang(optional($user->referral)->lastname)</h5>
                                                <span class="text-muted font-14"><span>@</span>@lang(optional($user->referral)->username)</span>
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    <span class="text-na">@lang('N/A')</span>
                                @endif
                            </td>

                            <td data-label="@lang('Main Balance')">{{trans(config('basic.currency_symbol'))}}{{getAmount($user->balance, config('basic.fraction_number'))}}</td>
                            <td data-label="@lang('Interest Balance')">{{trans(config('basic.currency_symbol'))}}{{getAmount($user->interest_balance, config('basic.fraction_number'))}}</td>
                            <td data-label="@lang('Last Login')">{{diffForHumans($user->last_login)}}</td>
                            <td data-label="@lang('Status')">
                                <span
                                    class="custom-badge badge-pill {{ $user->status == 0 ? 'bg-danger' : 'bg-success' }}">{{ $user->status == 0 ? 'Inactive' : 'Active' }}</span>
                            </td>
                            @if(adminAccessRoute(config('role.manage_user.access.edit')))
                                <td data-label="@lang('Action')">
                                    <div class="dropdown show">
                                        <a class="dropdown-toggle p-3" href="#" id="dropdownMenuLink" data-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <a class="dropdown-item" href="{{ route('admin.user-edit',$user->id) }}">
                                                <i class="fa fa-edit text-warning pr-2"
                                                   aria-hidden="true"></i> @lang('Edit')
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.send-email',$user->id) }}">
                                                <i class="fa fa-envelope text-success pr-2"
                                                   aria-hidden="true"></i> @lang('Send Email')
                                            </a>
                                            <a class="dropdown-item loginAccount" type="button"
                                               data-toggle="modal"
                                               data-target="#signIn"
                                               data-route="{{route('admin.login-as-user',$user->id)}}">
                                                <i class="fas fa-sign-in-alt text-success pr-2"
                                                   aria-hidden="true"></i> @lang('Login as User')
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center text-na" colspan="100%">@lang('No User Data')</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                {{$users->appends(@$search)->links('partials.pagination')}}

            </div>
        </div>
    </div>




    <div class="modal fade" id="all_active" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-primary">
                    <h5 class="modal-title">@lang('Active User Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
                </div>
                <div class="modal-body">
                    <p>@lang("Are you really want to active the User's")</p>
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
                    <h5 class="modal-title">@lang('DeActive User Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
                </div>
                <div class="modal-body">
                    <p>@lang("Are you really want to Inactive the User's")</p>
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

    <!-- Admin Login as a User Modal -->
    <div class="modal fade" id="signIn">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="" class="loginAccountAction" enctype="multipart/form-data">
                    @csrf
                    <!-- Modal Header -->
                    <div class="modal-header modal-colored-header bg-primary">
                        <h4 class="modal-title">@lang('Sing In Confirmation')</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body">
                        <p>@lang('Are you sure to sign in this account?')</p>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal"><span>@lang('Close')</span>
                        </button>
                        <button type="submit" class=" btn btn-primary "><span>@lang('Yes')</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection


@push('js')
    <script>
        "use strict";

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

        //dropdown menu is not working
        $(document).on('click', '.dropdown-menu', function (e) {
            e.stopPropagation();
        });

        //multiple active
        $(document).on('click', '.active-yes', function (e) {
            e.preventDefault();
            var allVals = [];
            $(".row-tic:checked").each(function () {
                allVals.push($(this).attr('data-id'));
            });

            var strIds = allVals;

            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
                url: "{{ route('admin.user-multiple-active') }}",
                data: {strIds: strIds},
                datatType: 'json',
                type: "post",
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
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
                url: "{{ route('admin.user-multiple-inactive') }}",
                data: {strIds: strIds},
                datatType: 'json',
                type: "post",
                success: function (data) {
                    location.reload();

                }
            });
        });

        $(document).on('click', '.loginAccount', function () {
            var route = $(this).data('route');
            $('.loginAccountAction').attr('action', route)
        });

        $('select').select2({
            selectOnClose: true,
            width: '100%'
        });

        $('.from_date').on('change', function (){
            $('.to_date').removeAttr('disabled');
        });

    </script>
@endpush
