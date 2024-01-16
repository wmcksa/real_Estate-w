@extends('admin.layouts.app')
@section('title')
    @lang('Create a Property')
@endsection
@section('content')

    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="media mb-4 justify-content-end">
                <a href="{{route('admin.propertyList', ['all'])}}" class="btn btn-sm btn-rounded btn-primary mr-2">
                    <span><i class="fas fa-arrow-left"></i> @lang('Back')</span>
                </a>
            </div>


            <div class="tab-content mt-4" id="myTabContent">
                @foreach($languages as $key => $language)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="lang-tab-{{ $key }}"
                         role="tabpanel">
                        <form method="post" action="{{route('admin.propertyStore', $language->id)}}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="card">
                                <div class="card-header text-primary">
                                    <li><span class="propertyDetailsLabel">@lang('Add Property Details')</span></li>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label>@lang('Title') <span class="text-danger">*</span></label>
                                                <input type="text" name="property_title[{{ $language->id }}]"
                                                       value="{{ old('property_title'.'.'.$language->id) }}"
                                                       placeholder="@lang('Property title')"
                                                       class="form-control @error('property_title'.'.'.$language->id) is-invalid @enderror">
                                                @error('property_title'.'.'.$language->id)
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        @if ($loop->index == 0)
                                            <div class="col-md-3 col-lg-3">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">@lang('Address') <span
                                                            class="text-danger">*</span></label>
                                                    <select name="address_id" class="form-control  type addressList">
                                                        <option value="" selected disabled></option>
                                                        @foreach($allAddress as $address)
                                                            <option
                                                                value="{{ $address->id }}" {{ old('address_id') == $address->id ? 'selected' : '' }}>@lang(optional($address->details)->title)</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('address_id')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-3 col-lg-3">
                                                <div class="form-group">
                                                    <label>@lang('Location')</label>
                                                    <input type="text" name="location" value="{{old('location')}}"
                                                           placeholder="@lang('only embed url accepted')"
                                                           class="form-control @error('location') is-invalid @enderror">
                                                    @error('location')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-xl-3">
                                                <label for="before_expiry_date"> @lang('Amenities')</label>
                                                <select name="amenity_id[]"
                                                        class="form-control propertyAmenities @error('amenity_id') is-invalid @enderror"
                                                        multiple>
                                                    <option disabled>@lang('Choose items')</option>
                                                    @foreach($allAmenities as $amenity)
                                                        <option
                                                            value="{{ $amenity->id }}" {{ in_array($amenity->id, (array) old('amenity_id')) ? 'selected' : '' }}>@lang(optional($amenity->details)->title)</option>
                                                    @endforeach
                                                </select>
                                                @error('amenity_id')
                                                <span class="text-danger">@lang($message)</span>
                                                @enderror
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-12 col-xl-12 property__details col-12">
                                            <div class="form-group">
                                                <label for="details"> @lang('Details') </label>
                                                <textarea
                                                    class="form-control summernote @error('details'.'.'.$language->id) is-invalid @enderror"
                                                    name="details[{{ $language->id }}]" id="summernote" rows="15"
                                                    value="{{ old('details'.'.'.$language->id) }}">{{old('details'.'.'.$language->id)}}</textarea>
                                                @error('details'.'.'.$language->id)
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        @if ($loop->index == 0)
                                            <div class="col-md-5 col-xl-5 col-12">
                                                <div class="form-group">
                                                    <label for="thumbnail">{{ __('Thumbnail') }}</label>
                                                    <div class="image-input property_image_input">
                                                        <label for="image-upload" id="image-label"><i
                                                                class="fas fa-upload"></i></label>
                                                        <input type="file" name="thumbnail"
                                                               placeholder="@lang('Choose image')"
                                                               id="image"
                                                               class="form-control @error('thumbnail') is-invalid @enderror">
                                                        <img id="image_preview_container" class="preview-image"
                                                             src="{{ getFile(config('location.category.path')) }}"
                                                             alt="@lang('preview image')">
                                                    </div>
                                                    @error('thumbnail')
                                                    <span class="text-danger">@lang($message)</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-7 col-xl-7 col-12">
                                                <div class="form-group" id="tab3">
                                                    <label for="details"> @lang('Property Galary Images') </label>
                                                    <div class="property-image"></div>
                                                    @error('property_image.*')
                                                        <span class="text-danger">@lang($message)</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-xl-3">
                                                <div class="form-group">
                                                    <label>@lang('Featured Property')</label>
                                                    <div class="custom-switch-btn">
                                                        <input type='hidden' value='1'
                                                               name="is_featured" {{ old('is_featured') == "1" ? 'checked' : ''}}>
                                                        <input type="checkbox" name="is_featured" id="is_featured"
                                                               class="custom-switch-checkbox"
                                                               value="0" {{ old('is_featured') == "0" ? 'checked' : '' }}>
                                                        <label class="custom-switch-checkbox-label" for="is_featured">
                                                            <span class="custom-switch-checkbox-for-installments"></span>
                                                            <span class="custom-switch-checkbox-switch"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-xl-3">
                                                <div class="form-group">
                                                    <label>@lang('Can investors see available funds?')</label>
                                                    <div class="custom-switch-btn">
                                                        <input type='hidden' value='1'
                                                               name="is_available_funding" {{ old('is_available_funding') == "1" ? 'checked' : ''}}>
                                                        <input type="checkbox" name="is_available_funding" id="is_available_funding"
                                                               class="custom-switch-checkbox"
                                                               value="0" {{ old('is_available_funding') == "0" ? 'checked' : '' }}>
                                                        <label class="custom-switch-checkbox-label" for="is_available_funding">
                                                            <span class="custom-switch-checkbox-for-installments"></span>
                                                            <span class="custom-switch-checkbox-switch"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        <div class="col-md-2 col-xl-2 mt-4 mb-3 pl-2 pr-2 pt-1 pb-1">
                                            <a href="javascript:void(0)" class="btn btn-primary btn-rounded generate"
                                               data-lang="{{$language->id}}"><i
                                                    class="fa fa-plus-circle"></i> @lang('Add FAQ')</a>
                                        </div>
                                    </div>

                                    @php
                                        $maxNum = old('faq_title') && old('faq_details') ? max(count(old('faq_title')), count(old('faq_details'))) : (old('faq_title') && !old('faq_details') ? count(old('faq_title')) : (!old('faq_title') && old('faq_details') ? count(old('faq_title')) : 0));
                                    @endphp

                                    <div class="row addedField{{$language->id}}">
                                        @for($i = 0; $i < $maxNum; $i++)
                                            <div class="col-md-12 col-log-12 col-12">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <input name="faq_title[]" class="form-control" type="text"
                                                               value="{{ old('faq_title.'.$i) }}"
                                                               placeholder="{{trans('question')}}">
                                                        <textarea class="form-control" name="faq_details[]"
                                                                  id="summernote" rows="1"
                                                                  placeholder="@lang('Answer')">{{ old('faq_details.'.$i) }}</textarea>
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-danger delete_desc" type="button">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            @if ($loop->index == 0)
                            <div class="card">
                                <div class="card-header text-primary">
                                    <li><span class="propertyDetailsLabel"> @lang('Add Investment Details')</span></li>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 col-xl-3">
                                            <div class="form-group">
                                                <label>@lang('Invest Type')</label>
                                                <div class="custom-switch-btn">
                                                    <input type='hidden' value='1'
                                                           name="is_invest_type" {{ old('is_invest_type') == "1" ? 'checked' : '' }}>
                                                    <input type="checkbox" name="is_invest_type" id="is_invest_type"
                                                           class="custom-switch-checkbox"
                                                           value="0" {{ old('is_invest_type') == "0" ? 'checked' : '' }}>
                                                    <label class="custom-switch-checkbox-label" for="is_invest_type">
                                                        <span class="custom-switch-checkbox-for-investType"></span>
                                                        <span class="custom-switch-checkbox-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xl-3 fixedAmount d-none {{ old('is_invest_type') == "0" ? 'd-block' : 'd-none' }}">
                                            <div class="form-group">
                                                <label>@lang('Fixed Amount')</label>
                                                <div class="input-group">
                                                    <input type="text" name="fixed_amount"
                                                           class="form-control @error('fixed_amount') is-invalid @enderror"
                                                           placeholder="0.00" value="{{ old('fixed_amount') }}" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')"
                                                           id="fixedAmount"
                                                           autocomplete="off">
                                                    <div class="input-group-append">
                                                        <span
                                                            class="input-group-text">@lang(config('basic.currency_symbol'))</span>
                                                    </div>
                                                </div>
                                                @error('fixed_amount')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3 rangeAmount {{ old('is_invest_type') == "0" ? 'd-none' : '' }}">
                                            <div class="form-group">
                                                <label>@lang('Minimum Amount')</label>
                                                <div class="input-group">
                                                    <input type="text" name="minimum_amount"
                                                           class="form-control @error('minimum_amount') is-invalid @enderror"
                                                           placeholder="0.00" value="{{ old('minimum_amount') }}"
                                                           onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')">
                                                    <div class="input-group-append">
                                                <span
                                                    class="input-group-text">@lang(config('basic.currency_symbol'))</span>
                                                    </div>
                                                </div>
                                                @error('minimum_amount')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3 rangeAmount {{ old('is_invest_type') == "0" ? 'd-none' : '' }}">
                                            <div class="form-group">
                                                <label>@lang('Maximum Amount')</label>
                                                <div class="input-group">
                                                    <input type="text" name="maximum_amount"
                                                           class="form-control @error('maximum_amount') is-invalid @enderror"
                                                           placeholder="0.00" value="{{ old('maximum_amount') }}"
                                                           onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')">
                                                    <div class="input-group-append">
                                                <span
                                                    class="input-group-text">@lang(config('basic.currency_symbol'))</span>
                                                    </div>
                                                </div>
                                                @error('maximum_amount')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3">
                                            <div class="form-group">
                                                <label>@lang('Total Investment Amount')</label>
                                                <div class="input-group">
                                                    <input type="text" name="total_investment_amount"
                                                           class="form-control @error('total_investment_amount') is-invalid @enderror"
                                                           placeholder="0.00"
                                                           value="{{ old('total_investment_amount') }}"
                                                           onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')">
                                                    <div class="input-group-append">
                                                        <span
                                                            class="input-group-text">@lang(config('basic.currency_symbol'))</span>
                                                    </div>
                                                </div>
                                                @error('total_investment_amount')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3">
                                            <div class="form-group">
                                                <label>@lang('Profit')</label>
                                                <div class="input-group">
                                                    <input type="text" name="profit"
                                                           class="form-control @error('profit') is-invalid @enderror"
                                                           placeholder="0.00" value="{{ old('profit') }}"
                                                           onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')">
                                                    <div class="input-group-append">
                                                        <select name="profit_type" id="profit_type"
                                                                class="form-control">
                                                            <option value="1">%</option>
                                                            <option
                                                                value="0">@lang(config('basic.currency_symbol'))</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                @error('profit')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3 acceptInstallments {{ old('is_invest_type') == "0" ? '' : 'd-none' }}">
                                            <div class="form-group">
                                                <label>@lang('Accept Installments')</label>
                                                <div class="custom-switch-btn">
                                                    <input type='hidden' value='1'
                                                           name="is_installment" {{ old('is_installment') == "1" ? 'checked' : ''}}>
                                                    <input type="checkbox" name="is_installment" id="is_installment"
                                                           class="custom-switch-checkbox"
                                                           value="0" {{ old('is_installment') == "0" ? 'checked' : '' }}>
                                                    <label class="custom-switch-checkbox-label" for="is_installment">
                                                        <span class="custom-switch-checkbox-for-installments"></span>
                                                        <span class="custom-switch-checkbox-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3 installmentField {{ old('is_invest_type') == "0" && old('is_installment') == "1" ? '' : 'd-none' }}">
                                            <div class="form-group">
                                                <label>@lang('Total Installments')</label>
                                                <div class="input-group">
                                                    <input type="text" name="total_installments"
                                                           class="form-control @error('total_installments') is-invalid @enderror"
                                                           id="totalInstallments"
                                                           placeholder="min 1" value="{{ old('total_installments') }}"
                                                           onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')">
                                                </div>
                                                @error('total_installments')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3 installmentField {{ old('is_invest_type') == "0" && old('is_installment') == "1" ? '' : 'd-none' }}">
                                            <div class="form-group">
                                                <label>@lang('Installment Amount')</label>
                                                <div class="input-group">
                                                    <input type="text" name="installment_amount"
                                                           class="form-control @error('installment_amount') is-invalid @enderror"
                                                           placeholder="0.00" value="{{ old('installment_amount') }}"
                                                           id="installmentAmount"
                                                           onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" readonly autocomplete="off">
                                                    <div class="input-group-append">
                                                        <span
                                                            class="input-group-text">@lang(config('basic.currency_symbol'))</span>
                                                    </div>
                                                </div>
                                                @error('installment_amount')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3 installmentField {{ old('is_invest_type') == "0" && old('is_installment') == "1" ? '' : 'd-none' }}">
                                            <div class="form-group">
                                                <label>@lang('Installment Duration')</label>
                                                <div class="input-group">
                                                    <input type="text" name="installment_duration"
                                                           class="form-control expiry_time @error('installment_duration') is-invalid @enderror"
                                                           value="{{ old('installment_duration') }}"
                                                           placeholder="min 1" min="1" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')">
                                                    <div class="input-group-append">
                                                        <select class="form-control installment_duration_type"
                                                                id="installment_duration_type"
                                                                name="installment_duration_type">
                                                            <option value="Days" {{ old('installment_duration_type') == 'Days' ? 'selected' : '' }}>@lang('Day(s)')</option>
                                                            <option value="Months" {{ old('installment_duration_type') == 'Months' ? 'selected' : '' }}>@lang('Month(s)')</option>
                                                            <option value="Years" {{ old('installment_duration_type') == 'Years' ? 'selected' : '' }}>@lang('Year(s)')</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                @error('installment_duration')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3 installmentField {{ old('is_invest_type') == "0" && old('is_installment') == "1" ? '' : 'd-none' }}">
                                            <div class="form-group">
                                                <label>@lang('Installment Late Fee')</label>
                                                <div class="input-group">
                                                    <input type="text" name="installment_late_fee"
                                                           class="form-control @error('installment_late_fee') is-invalid @enderror"
                                                           placeholder="0.00" value="{{ old('installment_late_fee') }}"
                                                           onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')">
                                                    <div class="input-group-append">
                                                        <span
                                                            class="input-group-text">@lang(config('basic.currency_symbol'))</span>
                                                    </div>
                                                </div>
                                                @error('installment_late_fee')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3">
                                            <div class="form-group">
                                                <label>@lang('Return Type')</label>
                                                <div class="custom-switch-btn">
                                                    <input type='hidden' value='1'
                                                           name="is_return_type" {{ old('is_return_type') == "1" ? 'checked' : '' }}>
                                                    <input type="checkbox" name="is_return_type" id="is_return_type"
                                                           class="custom-switch-checkbox"
                                                           value="0" {{ old('is_return_type') == "0" ? 'checked' : '' }}>
                                                    <label class="custom-switch-checkbox-label" for="is_return_type">
                                                        <span class="custom-switch-checkbox-for-returnType"></span>
                                                        <span class="custom-switch-checkbox-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3 howManyTimes {{ old('is_return_type') == "0" ? '' : 'd-none' }}">
                                            <div class="form-group">
                                                <label>@lang('How many times?')</label>
                                                <div class="input-group">
                                                    <input type="number" name="how_many_times"
                                                           class="form-control @error('how_many_times') is-invalid @enderror"
                                                           placeholder="min 1" value="{{ old('how_many_times') }}"
                                                           onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')">
                                                </div>
                                                @error('how_many_times')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3">
                                            <div class="form-group">
                                                <label>@lang('After how many days?')</label>
                                                <select name="how_many_days" id="how_many_days"
                                                        class="form-control @error('how_many_days') is-invalid @enderror">
                                                    <option value="" disabled>@lang('Select a Period')</option>
                                                    @foreach($allSchedule as $schedule)
                                                        <option
                                                            value="{{ $schedule->id }}" {{ old('how_many_days')  == $schedule->id ? 'selected' : ''}}>@lang($schedule->time) @lang($schedule->time_type)</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                            @error('how_many_days')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 col-xl-3">
                                            <div class="form-group">
                                                <label>@lang('Can Share Investment?')</label>
                                                <div class="custom-switch-btn">
                                                    <input type='hidden' value='1'
                                                           name="is_investor" {{ old('is_investor') == "1" ? 'checked' : ''}}>
                                                    <input type="checkbox" name="is_investor" id="is_investor"
                                                           class="custom-switch-checkbox"
                                                           value="0" {{ old('is_investor') == "0" ? 'checked' : '' }}>
                                                    <label class="custom-switch-checkbox-label" for="is_investor">
                                                        <span class="custom-switch-checkbox-for-installments"></span>
                                                        <span class="custom-switch-checkbox-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3">
                                            <div class="form-group">
                                                <label>@lang('Capital Back')</label>
                                                <div class="custom-switch-btn">
                                                    <input type='hidden' value='1'
                                                           name="is_capital_back" {{ old('is_capital_back') == "1" ? 'checked' : ''}}>
                                                    <input type="checkbox" name="is_capital_back" id="is_capital_back"
                                                           class="custom-switch-checkbox"
                                                           value="0" {{ old('is_capital_back') == "0" ? 'checked' : '' }}>
                                                    <label class="custom-switch-checkbox-label" for="is_capital_back">
                                                        <span class="custom-switch-checkbox-for-installments"></span>
                                                        <span class="custom-switch-checkbox-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3">
                                            <div class="form-group">
                                                <label>@lang('Start Date')</label>
                                                <input type="datetime-local" class="form-control start_date" name="start_date" value="{{ old('start_date',request()->start_date) }}" placeholder="@lang('Start date')" autocomplete="off"/>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3">
                                            <div class="form-group">
                                                <label>@lang('Expire Date')</label>
                                                <input type="datetime-local" class="form-control expire_date" name="expire_date" value="{{ old('expire_date',request()->expire_date) }}" placeholder="@lang('Expire date')" autocomplete="off"/>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-xl-3">
                                            <div class="form-group ">
                                                <label>@lang('Status')</label>
                                                <div class="custom-switch-btn">
                                                    <input type='hidden' value='1'
                                                           name='status' {{ old('status') == "1" ? 'checked' : '' }}>
                                                    <input type="checkbox" name="status" class="custom-switch-checkbox"
                                                           id="status"
                                                           value="0" {{ old('status') == "0" ? 'checked' : '' }}>
                                                    <label class="custom-switch-checkbox-label" for="status">
                                                        <span class="custom-switch-checkbox-propertyStatus"></span>
                                                        <span class="custom-switch-checkbox-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-12">
                                <button type="submit"
                                        class="btn waves-effect waves-light btn-rounded btn-primary btn-block mt-3">
                                    <span><i class="fas fa-save pr-2"></i> @lang('Save Changes')</span></button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/summernote.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/image-uploader.css') }}"/>
@endpush
@push('js-lib')
    <script src="{{ asset('assets/admin/js/summernote.min.js')}}"></script>
    <script src="{{ asset('assets/global/js/image-uploader.js') }}"></script>
@endpush

@push('js')

    <script>
        'use strict'
        $('.summernote').summernote({
            height: 250,
            callbacks: {
                onBlurCodeview: function () {
                    let codeviewHtml = $(this).siblings('div.note-editor').find('.note-codable').val();
                    $(this).val(codeviewHtml);
                }
            }
        });

        $('#image').on("change",function () {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#image_preview_container').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });


        $(document).ready(function (e) {
            $(".generate").on('click', function () {
                var lang = $(this).data('lang');
                var form = `<div class="col-md-12 col-log-12 col-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input name="faq_title[]" class="form-control" type="text"
                                        placeholder="{{trans('question')}}">
                                        <textarea class="form-control summernote " name="faq_details[]" rows="1" placeholder="@lang('Answer')"></textarea>
                                        <span class="input-group-btn">
                                            <button class="btn btn-danger delete_desc" type="button">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div> `;

                $(`.addedField${lang}`).append(form)
            });

            $(document).on('click', '.delete_desc', function () {
                $(this).closest('.input-group').parent().remove();
            });


            let propertyImageOptions = {
                imagesInputName: 'property_image',
                label: 'Drag & Drop files here or click to browse images',
                extensions: ['.jpg', '.jpeg', '.png'],
                mimes: ['image/jpeg', 'image/png'],
                maxSize: 5242880
            };

            $('.property-image').imageUploader(propertyImageOptions);


            $(document).on('input', '#totalInstallments', function (){
                let total_installments = $('#totalInstallments').val();
                let fixed_amount = $('#fixedAmount').val();
                let installment_amount = parseInt(fixed_amount) / parseInt(total_installments);
                let final_installment_amount = installment_amount.toFixed(2);
                $('#installmentAmount').val(final_installment_amount);
            });


            $(document).on('change', '#is_invest_type', function () {
                var isCheck = $(this).prop('checked');
                if (isCheck == false) {
                    $('.rangeAmount').addClass('d-block');
                    $('.rangeAmount').removeClass('d-none');
                    $('.fixedAmount').removeClass('d-block');
                    $('.fixedAmount').addClass('d-none');
                    $('.acceptInstallments').addClass('d-none')
                    $('.installmentField').addClass('d-none');
                } else {
                    $('.rangeAmount').addClass('d-none');
                    $('.rangeAmount').removeClass('d-block');
                    $('.fixedAmount').removeClass('d-none');
                    $('.acceptInstallments').removeClass('d-none');
                    $('.installmentField').removeClass('d-none');
                    $('#is_installment').prop('checked', false);
                }
            });

            $(document).on('change', '#is_return_type', function () {
                var isCheck = $(this).prop('checked');

                if (isCheck == false) {
                    $('.howManyTimes').removeClass('d-block');
                    $('.howManyTimes').addClass('d-none');
                } else {
                    $('.howManyTimes').removeClass('d-none');
                    $('.howManyTimes').addClass('d-block');
                }
            });

            $(document).on('change', '#is_installment', function () {
                var isCheck = $(this).prop('checked');
                if (isCheck == false) {
                    $('.installmentField').removeClass('d-none');
                } else {
                    $('.installmentField').addClass('d-none');
                }
            });

            $('.propertyAmenities').select2({
                width: '100%',
                placeholder: '@lang("Select Amenities")',
            });

            $("#how_many_days").select2({
                selectOnClose: true,
                width: '100%'
            })

            $('.addressList').select2({
                width: '100%',
                placeholder: '@lang("Select Address")',
            });

            $('select[name=period_duration]').select2({
                selectOnClose: true
            });


        });

    </script>

@endpush
