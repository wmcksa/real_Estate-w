@extends($theme.$layout)
@section('title', trans($title))
@section('content')

    <!-- main -->
    <div class="container-fluid">
        <div class="main row">
            <div class="dashboard-heading">
                <h2 class="mb-0">@lang('Withdraw')</h2>
            </div>
            <div class="col-md-3">
                <div class="card card-type-1 text-center bgGateway">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                            <img
                                src="{{getFile(config('location.withdraw.path').optional($withdraw->method)->image)}}"
                                class="card-img-top w-50" alt="{{optional($withdraw->method)->name}}">
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between align-items-start">@lang('Request Amount') :
                            <span
                                class="float-right greenColorText">{{@$basic->currency_symbol}}{{getAmount($withdraw->amount)}} </span>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between align-items-start">@lang('Charge Amount') :
                            <span
                                class="float-right text-danger">{{@$basic->currency_symbol}}{{getAmount($withdraw->charge)}} </span>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between align-items-start">@lang('Total Payable') :
                            <span
                                class="float-right text-danger">{{@$basic->currency_symbol}}{{getAmount($withdraw->net_amount)}} </span>
                        </li>
                        @if($layout != 'layouts.payment')
                            <li class="list-group-item border-0 d-flex justify-content-between align-items-start">@lang('Available Balance') :
                                <span
                                    class="float-right greenColorText">{{@$basic->currency_symbol}}{{$remaining}} </span>
                            </li>
                        @endif
                    </ul>
                </div>

            </div>

            <div class="col-md-8">

                <div class="card card-type-1 bgGateway">
                    <div class="text-center">
                        <h3 class="card-title">@lang('Additional Information To Withdraw Confirm')</h5>
                    </div>

                    <div class="card-body">

                        <form @if($layout == 'layouts.payment') action="{{route('user.payout.submit',$billId)}}"
                        @else action="" @endif method="post" enctype="multipart/form-data" class="form-row form text-left preview-form">
                            @csrf
                            @if($payoutMethod->supported_currency)
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="form-group input-box search-currency-dropdown">
                                            <label for="from_wallet">@lang('Select Bank Currency')</label>
                                            <select id="from_wallet" name="currency_code"
                                                    class="form-control form-control-sm transfer-currency"
                                                    required>
                                                <option value="" disabled=""
                                                        selected="">@lang('Select Currency')</option>
                                                @foreach($payoutMethod->supported_currency as $singleCurrency)
                                                    <option
                                                        value="{{$singleCurrency}}"
                                                        @foreach($payoutMethod->convert_rate as $key => $rate)
                                                            @if($singleCurrency == $key) data-rate="{{$rate}}" @endif
                                                        @endforeach {{old('transfer_name') == $singleCurrency ?'selected':''}}>{{$singleCurrency}}</option>
                                                @endforeach
                                            </select>
                                            @error('currency_code')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($payoutMethod->code == 'paypal')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group input-box search-currency-dropdown">
                                            <label for="from_wallet">@lang('Select Recipient Type')</label>
                                            <select id="from_wallet" name="recipient_type"
                                                    class="form-control form-control-sm mb-3" required>
                                                <option value="" disabled=""
                                                        selected="">@lang('Select Recipient')</option>
                                                <option value="EMAIL">@lang('Email')</option>
                                                <option value="PHONE">@lang('phone')</option>
                                                <option value="PAYPAL_ID">@lang('Paypal Id')</option>
                                            </select>
                                            @error('recipient_type')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(optional($withdraw->method)->input_form)

                                <div class="row g-4">
                                    @foreach(optional($withdraw->method)->input_form as $k => $v)
                                        @if($v->type == "text")
                                            <div class="input-box col-md-12">
                                                <label>@lang($v->label?? $v->field_level) @if($v->validation == 'required')
                                                        <span class="text-danger">*</span>
                                                    @endif</label>
                                                <input type="text" name="{{$k}}"
                                                    class="form-control"
                                                    @if($v->validation == "required") required @endif>
                                                @if ($errors->has($k))
                                                    <span
                                                        class="text-danger">{{ trans($errors->first($k)) }}</span>
                                                @endif
                                            </div>
                                        @elseif($v->type == "textarea")
                                            <div class="input-box col-12">
                                                <div class="form-group">
                                                    <label>@lang($v->label?? $v->field_level) @if($v->validation == 'required')
                                                            <span class="text-danger">*</span>
                                                        @endif
                                                    </label>
                                                    <textarea name="{{$k}}" class="form-control"
                                                            cols="30"
                                                            rows="10"
                                                            @if($v->validation == "required") required @endif></textarea>
                                                    @if ($errors->has($k))
                                                        <span
                                                            class="text-danger">{{ trans($errors->first($k)) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($v->type == "file")
                                            <div class="input-box col-12">
                                                <label
                                                    for="">@lang($v->label?? $v->field_level) @if($v->validation == 'required')
                                                        <span class="text-danger">*</span>
                                                    @endif</label>
                                                <input class="form-control" name="{{$k}}" accept="image/*"
                                                    @if($v->validation == "required") required @endif type="file"
                                                    id="formFile"/>
                                                <span class="icon"> <i class="fal fa-paperclip"></i></span>
                                                @if ($errors->has($k))
                                                    <br>
                                                    <span
                                                        class="text-danger">{{ __($errors->first($k)) }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            <input type="text" name="wallet_type" value="{{ $wallet_type }}">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-custom w-100 mt-3">
                                        <span>@lang('Confirm Now')</span>
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection


@push('css-lib')
    <link rel="stylesheet" href="{{asset($themeTrue.'css/bootstrap-fileinput.css')}}">
@endpush
@push('extra-js')
    <script src="{{asset($themeTrue.'js/bootstrap-fileinput.js')}}"></script>
@endpush

@push('script')
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
