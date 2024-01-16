@extends('admin.layouts.app')
@section('title')
    @lang('Edit Badge')
@endsection
@section('content')

    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="media mb-4 justify-content-end">
                <a href="{{route('admin.badgeList')}}" class="btn btn-sm  btn-primary btn-rounded mr-2">
                    <span><i class="fas fa-arrow-left"></i> @lang('Back')</span>
                </a>
            </div>

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                @foreach($languages as $key => $language)
                    <li class="nav-item">
                        <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" href="#lang-tab-{{ $key }}" role="tab" aria-controls="lang-tab-{{ $key }}"
                           aria-selected="{{ $loop->first ? 'true' : 'false' }}">@lang($language->name)</a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content mt-2" id="myTabContent">
                @foreach($languages as $key => $language)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="lang-tab-{{ $key }}" role="tabpanel">
                        <form method="post" action="{{route('admin.badgeUpdate', [$id, $language->id])}}" class="mt-4" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                                    <label>@lang('Rank Name')</label>
                                    <input type="text" name="rank_name[{{ $language->id }}]"
                                           class="form-control  @error('rank_name'.'.'.$language->id) is-invalid @enderror"
                                           value="{{ old('rank_name'.$language->id, isset($singleBadgeDetails[$language->id]) ? $singleBadgeDetails[$language->id][0]->rank_name : '') }}" placeholder="@lang('rank name')">
                                    @error('rank_name'.'.'.$language->id)
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                                    <label>@lang('Rank Level')</label>
                                    <input type="text" name="rank_level[{{ $language->id }}]"
                                           class="form-control @error('rank_level'.'.'.$language->id) is-invalid @enderror"
                                           value="{{ old('rank_level'.$language->id, isset($singleBadgeDetails[$language->id]) ? $singleBadgeDetails[$language->id][0]->rank_level : '') }}" placeholder="@lang('rank level')">
                                    @error('rank_level'.'.'.$language->id)
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if ($loop->index == 0)
                                    <div class="form-group col-md-4 col-lg-4 col-sm-12 fixedAmount d-block">
                                        <label>@lang('Minimum Invest')</label>
                                        <div class="input-group">
                                            <input type="text" name="min_invest" class="form-control" value="{{ old('min_invest', $singleBadgeDetails[$language->id][0]->badges->min_invest) }}" placeholder="0.00" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')">
                                            <div class="input-group-append">
                                                <span class="input-group-text">@lang(config('basic.currency_symbol'))</span>
                                            </div>
                                        </div>
                                        @error('min_invest')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-4 col-lg-4 col-12 fixedAmount d-block">
                                        <label>@lang('Minimum Deposit')</label>
                                        <div class="input-group">
                                            <input type="text" name="min_deposit" class="form-control" value="{{ old('min_deposit', $singleBadgeDetails[$language->id][0]->badges->min_deposit) }}" placeholder="0.00" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')">
                                            <div class="input-group-append">
                                                <span class="input-group-text">@lang(config('basic.currency_symbol'))</span>
                                            </div>
                                        </div>
                                        @error('min_deposit')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-5 fixedAmount d-block">
                                        <label>@lang('Minimum Earning')</label>
                                        <div class="input-group">
                                            <input type="text" name="min_earning" value="{{ old('min_earning', $singleBadgeDetails[$language->id][0]->badges->min_earning) }}" class="form-control" placeholder="0.00" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')">
                                            <div class="input-group-append">
                                                <span class="input-group-text">@lang(config('basic.currency_symbol'))</span>
                                            </div>
                                        </div>
                                        @error('min_earning')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-3 fixedAmount d-block">
                                        <label>@lang('Bonus')</label>
                                        <div class="input-group">
                                            <input type="text" name="bonus" value="{{ old('bonus', $singleBadgeDetails[$language->id][0]->badges->bonus) }}" class="form-control" placeholder="0.00">
                                            <div class="input-group-append" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')">
                                                <span class="input-group-text">@lang(config('basic.currency_symbol'))</span>
                                            </div>
                                        </div>
                                        @error('bonus')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif

                                <div class=" col-md-6">
                                    <div class="form-group">
                                        <label for="description">@lang('Description')</label>
                                        <textarea name="description[{{ $language->id }}]" rows="15" class="form-control summernote @error('description'.'.'.$language->id) is-invalid @enderror" id="summernote" placeholder="@lang('type here..')" value="{{ old('description'.$language->id, isset($singleBadgeDetails[$language->id]) ? $singleBadgeDetails[$language->id][0]->description : '') }}">{{ old('description'.$language->id, isset($singleBadgeDetails[$language->id]) ? $singleBadgeDetails[$language->id][0]->description : '') }}</textarea>
                                        @error('description'.'.'.$language->id)
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                @if ($loop->index == 0)
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label>@lang('Badge Icon')</label>

                                            <div class="image-input rank_icon_input">
                                                <label for="image-upload" id="image-label"><i class="fas fa-upload"></i></label>
                                                <input type="file" name="badge_icon" placeholder="@lang('Choose image')" id="image" class="form-control @error('badge_icon') is-invalid @enderror">
                                                <img id="image_preview_container" class="preview-image"
                                                     src="{{ asset(getFile(config('location.badge.path').(isset($singleBadgeDetails[$language->id]) ? $singleBadgeDetails[$language->id][0]->badges->badge_icon : ''))) }}"
                                                     alt="preview image">
                                            </div>
                                        </div>
                                        @error('badge_icon')
                                            <span class="text-danger">{{ trans($message) }}</span>
                                        @enderror
                                    </div>
                                @endif

                                @if($loop->index == 0)
                                    <div class="form-group col-sm-12 col-md-4 col-xl-4">
                                        <label>@lang('Status')</label>
                                        <div class="custom-switch-btn">
                                            <input type='hidden' value='1' name='status'>
                                            <input type="checkbox" name="status" class="custom-switch-checkbox"
                                                   id="status"
                                                   value="0" <?php if( $singleBadgeDetails[$language->id][0]->badges->status == 0):echo 'checked'; endif ?>>
                                            <label class="custom-switch-checkbox-label" for="status">
                                                <span class="custom-switch-checkbox-inner"></span>
                                                <span class="custom-switch-checkbox-switch"></span>
                                            </label>
                                        </div>
                                    </div>
                                @endif

                            </div>
                            <button type="submit" class="btn waves-effect waves-light btn-rounded btn-primary btn-block mt-3">@lang('Save')</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/summernote.min.css')}}">
@endpush
@push('js-lib')
    <script src="{{ asset('assets/admin/js/summernote.min.js')}}"></script>
@endpush

@push('js')
    <script>
        'use strict'
        $(document).ready(function (e) {
            "use strict";

            $('#image').on("change",function () {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#image_preview_container').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });


            $('select').select2({
                selectOnClose: true
            });

            $('.summernote').summernote({
                height: 200,
                callbacks: {
                    onBlurCodeview: function () {
                        let codeviewHtml = $(this).siblings('div.note-editor').find('.note-codable').val();
                        $(this).val(codeviewHtml);
                    }
                }
            });


        });
    </script>
@endpush
