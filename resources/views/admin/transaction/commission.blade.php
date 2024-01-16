
@extends('admin.layouts.app')
@section('title')
    @lang("Commissions")
@endsection
@section('content')
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <form action="{{route('admin.commissions.search')}}" method="get">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="from_date">@lang('Bonus From')</label>
                                <input type="text" name="from_name" value="{{@request()->from_name}}" class="form-control get-username"
                                       placeholder="@lang('from name')">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="from_date">@lang('Bonus To')</label>
                                <input type="text" name="to_name" value="{{@request()->to_name}}" class="form-control get-username"
                                       placeholder="@lang('to name')">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="from_date">@lang('Remark')</label>
                                <input type="text" name="remark" value="{{@request()->remark}}" class="form-control get-service"
                                       placeholder="@lang('Remark')">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>@lang('Bonus Type')</label>
                                <select name="type" class="form-control type">
                                    <option value="">@lang('All')</option>
                                    <option value="joining_bonus" {{ @request()->type == 'joining_bonus' ? 'selected' : '' }}>@lang('Joining Bonus')</option>
                                    <option value="deposit" {{ @request()->type == 'deposit' ? 'selected' : '' }}>@lang('Deposit Bonus')</option>
                                    <option value="invest" {{ @request()->type == 'invest' ? 'selected' : '' }}>@lang('Investment Bonus')</option>
                                    <option value="profit_commission" {{ @request()->type == 'profit_commission' ? 'selected' : '' }}>@lang('Profit Commission')</option>
                                    <option value="custom-badge_commission" {{ @request()->type == 'badge_commission' ? 'selected' : '' }}>@lang('Badge Commission')</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="from_date">@lang('From Date')</label>
                            <input type="date" class="form-control from_date" name="from_date" value="{{ old('from_date',request()->from_date) }}" placeholder="@lang('From date')" autocomplete="off"/>
                        </div>

                        <div class="input-box col-lg-4">
                            <label for="to_date">@lang('To Date')</label>
                            <input type="date" class="form-control to_date" name="to_date" value="{{ old('to_date',request()->to_date) }}" placeholder="@lang('To date')" autocomplete="off" disabled="true"/>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="opacity-0">@lang('...')</label>
                                <button type="submit" class="btn btn-block btn-primary"><i class="fas fa-search"></i> @lang('Search')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <table class="categories-show-table table table-hover table-striped table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th>@lang('SL No.')</th>
                    <th>@lang('Bonus From')</th>
                    <th>@lang('Bonus To')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Remarks')</th>
                    <th>@lang('Bonus Type')</th>
                    <th>@lang('Date-Time')</th>
                </tr>
                </thead>
                <tbody>
                @forelse($transactions as $k => $transaction)
                    <tr>
                        <td data-label="@lang('No.')">{{loopIndex($transactions) + $k}}</td>
                        <td data-label="@lang('Bonus From')">
                            <a href="{{route('admin.user-edit',optional($transaction->bonusBy)->id)}}" target="_blank">
                                <div class="d-flex no-block align-items-center">
                                    <div class="mr-3"><img src="{{getFile(config('location.user.path').optional($transaction->bonusBy)->image) }}" alt="user" class="rounded-circle" width="45" height="45">
                                    </div>
                                    <div class="">
                                        <h5 class="text-dark mb-0 font-16 font-weight-medium">
                                            @lang(optional($transaction->bonusBy)->fullname)
                                        </h5>
                                        <span class="text-muted font-14"><span>@</span>@lang(optional($transaction->bonusBy)->username)</span>
                                    </div>
                                </div>
                            </a>
                        </td>

                        <td data-label="@lang('Bonus To')">
                            <a href="{{route('admin.user-edit', optional($transaction->user)->id)}}" target="_blank">
                                <div class="d-flex no-block align-items-center">
                                    <div class="mr-3"><img src="{{getFile(config('location.user.path').optional($transaction->user)->image) }}" alt="user" class="rounded-circle" width="45" height="45">
                                    </div>
                                    <div class="">
                                        <h5 class="text-dark mb-0 font-16 font-weight-medium">
                                            @lang(optional($transaction->user)->fullname)
                                        </h5>
                                        <span class="text-muted font-14"><span>@</span> @lang(optional($transaction->user)->username)</span>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td data-label="@lang('Amount')">
                             <span class="font-weight-bold text-success">{{getAmount($transaction->amount, config('basic.fraction_number')). ' ' . trans(config('basic.currency'))}}</span>
                        </td>

                        <td data-label="@lang('Remarks')"> @lang($transaction->remarks)</td>
                        <td data-label="@lang('Bonus Type')"> @lang($transaction->type)</td>
                        <td data-label="@lang('Date-Time')">
                            {{ dateTime($transaction->created_at, 'd M Y h:i A') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center text-na" colspan="100%">@lang('No User Data')</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {{ $transactions->links('partials.pagination') }}
        </div>
    </div>
@endsection

@push('js')
    <script>
        'use strict'
        $('.from_date').on('change', function (){
            $('.to_date').removeAttr('disabled');
        });
    </script>
@endpush
