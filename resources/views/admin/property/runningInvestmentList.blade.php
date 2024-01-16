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
                        <th scope="col">@lang('Expire Time')</th>
                        <th scope="col">@lang('Invested User')</th>
                        <th scope="col">@lang('Invested Amount')</th>
                        <th scope="col">@lang('Disbursement Type')</th>
                        <th scope="col">@lang('Action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($investments as $invest)
                        <tr>
                            <td data-label="@lang('Property')">
                                <a href="{{ route('propertyDetails',[@slug(optional($invest->property->details)->property_title), optional($invest->property)->id]) }}"
                                   target="_blank">
                                    <div class="d-flex no-block align-items-center">
                                        <div class="mr-3"><img
                                                src="{{getFile(config('location.propertyThumbnail.path').optional($invest->property)->thumbnail) }}"
                                                alt="@lang('property_thumbnail')" class="rounded-circle" width="45"
                                                height="45">
                                        </div>
                                        <div class="">
                                            <h5 class="text-dark mb-0 font-16 font-weight-medium">
                                                @lang(\Illuminate\Support\Str::limit(optional($invest->property->details)->property_title, 30))
                                            </h5>
                                        </div>
                                    </div>
                                </a>
                            </td>

                            <td data-label="@lang('Expire time')">
                                @if(optional($invest->property)->expire_date)
                                    {{ dateTime(optional($invest->property)->expire_date) }}
                                @endif
                            </td>

                            <td data-label="@lang('Invested User')">
                                <a href="{{ route('admin.seeInvestedUser',['property_id' => optional($invest->property)->id, 'type' => 'running']) }}">
                                    <span
                                        class="custom-badge bg-success badge-pill">{{ optional($invest->property)->totalRunningInvestUserAndAmount()['totalInvestedUser'] }}</span></a>
                            </td>

                            <td data-label="@lang('Invested Amount')">
                                {{ config('basic.currency_symbol') }}{{ optional($invest->property)->totalRunningInvestUserAndAmount()['totalInvestedAmount'] }}
                            </td>

                            <td data-label="@lang('Disbursement Type')">
                                <input data-toggle="toggle" id="disbursement_type" class="disbursement_type"
                                       data-onstyle="success"
                                       data-offstyle="info" data-on="Manual" data-off="Automatic" data-width="70%"
                                       type="checkbox"
                                       {{ optional($invest->property)->is_payment == 0 ? 'checked' : '' }} name="disbursement_type"
                                       data-id="{{ optional($invest->property)->id }}">
                            </td>

                            <td data-label="@lang('Action')">
                                <a href="{{ route('admin.seeInvestedUser', ['property_id' => optional($invest->property)->id, 'type' => 'running']) }}"
                                   class="btn btn-sm btn-outline-primary btn-rounded btn-rounded edit-button">
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

@push('js')

    <script>
        "use strict";
        $(document).on('change', '#disbursement_type', function () {

            var isCheck = $(this).prop('checked');
            let dataId = $(this).data('id');
            let isVal = null;
            if (isCheck == true) {
                isVal = 'on'
            } else {
                isVal = 'off';
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('admin.disbursementType') }}",
                type: "POST",
                data: {
                    dataid: dataId,
                    isval: isVal,
                },
                success: function (data) {
                },
            });
        });

    </script>

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
