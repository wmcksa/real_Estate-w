@extends($theme.'layouts.user')
@section('title',trans('Fund History'))
@section('content')
    @push('style')
        <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}" />
    @endpush
<!-- Fund history -->
<section class="transaction-history">
    <div class="container-fluid">
        <div class="row mt-4 mb-2">
            <div class="col ms-2">
                <div class="header-text-full">
                    <h3 class="dashboard_breadcurmb_heading mb-1">@lang('Fund History')</h3>
                    <nav aria-label="breadcrumb" class="ms-2">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('user.home') }}">@lang('Dashboard')</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Fund History')</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- search area -->
        <div class="search-bar p-0">
            <form action="{{ route('user.fund-history.search') }}" method="get">
                <div class="row g-3 align-items-end">
                    <div class="input-box col-lg-3">
                        <input
                            type="text"
                            name="name"
                            value="{{@request()->name}}"
                            class="form-control"
                            placeholder="@lang('Transaction ID')"
                        />
                    </div>

                    <div class="input-box col-lg-3">
                        <select name="status"
                                class="form-select"
                                id="salutation"
                                aria-label="Default select example">
                            <option value="">@lang('All Payment')</option>
                            <option value="1"
                                    @if(@request()->status == '1') selected @endif>@lang('Complete Payment')</option>
                            <option value="2"
                                    @if(@request()->status == '2') selected @endif>@lang('Pending Payment')</option>
                            <option value="3"
                                    @if(@request()->status == '3') selected @endif>@lang('Cancel Payment')</option>
                        </select>
                    </div>

                    <div class="input-box col-lg-2">
                        <input
                            type="text" class="form-control datepicker from_date" name="from_date" value="{{ old('from_date',request()->from_date) }}" placeholder="@lang('From date')" autocomplete="off" readonly/>
                    </div>

                    <div class="input-box col-lg-2">
                        <input
                            type="text" class="form-control datepicker to_date" name="to_date" value="{{ old('to_date',request()->to_date) }}" placeholder="@lang('To date')" autocomplete="off" readonly disabled="true"/>
                    </div>

                    <div class="input-box col-lg-2">
                        <button class="btn-custom w-100" type="submit"><i class="fal fa-search"></i> @lang('Search') </button>
                    </div>
                </div>
            </form>
        </div>



       <div class="row mt-4">
          <div class="col">
             <div class="table-parent table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">@lang('Transaction ID')</th>
                            <th scope="col">@lang('Gateway')</th>
                            <th scope="col">@lang('Amount')</th>
                            <th scope="col">@lang('Charge')</th>
                            <th scope="col">@lang('Status')</th>
                            <th scope="col">@lang('Time')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($funds as $data)
                            <tr>
                                <td>{{$data->transaction}}</td>
                                <td>@lang(optional($data->gateway)->name)</td>
                                <td>{{getAmount($data->amount)}} @lang($basic->currency)</td>
                                <td>{{getAmount($data->charge)}} @lang($basic->currency)</td>
                                <td>
                                    @if($data->status == 1)
                                        <span class="badge bg-success">@lang('Complete')</span>
                                    @elseif($data->status == 2)
                                        <span class="badge bg-warning">@lang('Pending')</span>
                                    @elseif($data->status == 3)
                                        <span class="badge bg-danger">@lang('Cancel')</span>
                                    @endif
                                </td>
                                <td>{{ dateTime($data->created_at, 'd M Y h:i A') }}</td>
                            </tr>

                        @empty
                            <tr class="text-center">
                                <td colspan="100%">{{__('No Data Found!')}}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $funds->appends($_GET)->links($theme.'partials.pagination') }}

             </div>
          </div>
       </div>
    </div>
</section>

@endsection

@push('script')
    <script src="{{ asset('assets/global/js/bootstrap-datepicker.js') }}"></script>

    <script>
        'use strict'
        $(document).ready(function () {
            $( ".datepicker" ).datepicker({
            });

            $('.from_date').on('change', function (){
                $('.to_date').removeAttr('disabled');
            });
        });
    </script>
@endpush
