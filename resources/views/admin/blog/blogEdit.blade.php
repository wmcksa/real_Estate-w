@extends('admin.layouts.app')

@section('title')
    @lang('Edit Blog')
@endsection

@section('content')
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <div class="media mb-4 justify-content-end">
                <a href="{{route('admin.blogList')}}" class="btn btn-sm  btn-primary btn-rounded mr-2">
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
                        <form method="post" action="{{ route('admin.blogUpdate',[$id, $language->id]) }}" class="mt-4" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            @if ($loop->index == 0)
                                <div class="row mb-3">
                                    <div class="col-sm-12 col-md-12 mb-3">
                                        <label for="blog_category_id"> @lang('Select Category') </label>
                                        <select name="blog_category_id" class="form-control @error('blog_category_id'.'.'.$language->id) is-invalid @enderror">
                                            @forelse ($blogCategory as $item)
                                                <option value="{{ $item->details->blog_category_id }}" {{ $item->details->blog_category_id == $blogDetails[$language->id][0]->blog->blog_category_id ? 'selected' : '' }}>@lang($item->details->name)</option>
                                            @empty
                                            @endforelse
                                        </select>

                                        <div class="invalid-feedback">
                                            @error('blog_category_id'.'.'.$language->id) @lang($message) @enderror
                                        </div>
                                        <div class="valid-feedback"></div>
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-sm-12 col-md-12 mb-3">
                                    <label for="author"> @lang('Author') </label>
                                    <input type="text" name="author[{{ $language->id }}]"
                                            class="form-control  @error('author'.'.'.$language->id) is-invalid @enderror"
                                            value="<?php echo old('author'.$language->id, isset($blogDetails[$language->id]) ? $blogDetails[$language->id][0]->author : '') ?>">
                                    <div class="invalid-feedback">
                                        @error('author'.'.'.$language->id) @lang($message) @enderror
                                    </div>
                                    <div class="valid-feedback"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 col-md-12 mb-3">
                                    <label for="name"> @lang('Title') </label>
                                    <input type="text" name="title[{{ $language->id }}]"
                                            class="form-control  @error('title'.'.'.$language->id) is-invalid @enderror"
                                            value="<?php echo old('author'.$language->id, isset($blogDetails[$language->id]) ? $blogDetails[$language->id][0]->title : '') ?>">
                                    <div class="invalid-feedback">
                                        @error('title'.'.'.$language->id) @lang($message) @enderror
                                    </div>
                                    <div class="valid-feedback"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 col-md-12 my-3">
                                    <div class="form-group ">
                                        <label for="details"> @lang('Details') </label>
                                        <textarea class="form-control summernote @error('details') is-invalid @enderror" name="details[{{ $language->id }}]" id="summernote" rows="15" value="<?php echo old('author'.$language->id, isset($blogDetails[$language->id]) ? @$blogDetails[$language->id][0]->details : '') ?>"><?php echo old('author'.$language->id, isset($blogDetails[$language->id]) ? @$blogDetails[$language->id][0]->details : '') ?></textarea>

                                        <div class="invalid-feedback">
                                            @error('details') @lang($message) @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($loop->index == 0)
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <label for="image">{{ ('Image') }}</label>
                                            <div class="image-input ">
                                                <label for="image-upload" id="image-label"><i class="fas fa-upload"></i></label>
                                                <input type="file" name="image" placeholder="@lang('Choose image')" id="image">
                                                <img id="image_preview_container" class="preview-image"
                                                src="{{getFile(config('location.blog.path').(isset($blogDetails[$language->id]) ? $blogDetails[$language->id][0]->blog->image : ''))}}"
                                                    alt="@lang('preview image')">
                                            </div>
                                            @error('image')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group ">
                                            <label>@lang('Status')</label>
                                            <div class="custom-switch-btn">
                                                <input type='hidden' value='1' name='status'>
                                                <input type="checkbox" name="status" class="custom-switch-checkbox"
                                                       id="status"
                                                       value="0" <?php if( optional($blogDetails[$language->id][0]->blog)->status == 0):echo 'checked'; endif ?>>
                                                <label class="custom-switch-checkbox-label" for="status">
                                                    <span class="custom-switch-checkbox-inner"></span>
                                                    <span class="custom-switch-checkbox-switch"></span>
                                                </label>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endif
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
        "use strict";
        $(document).ready(function (e) {
            $('#image').on("change",function () {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#image_preview_container').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
            $('.summernote').summernote({
                height: 250,
                callbacks: {
                    onBlurCodeview: function() {
                        let codeviewHtml = $(this).siblings('div.note-editor').find('.note-codable').val();
                        $(this).val(codeviewHtml);
                    }
                }
            });
        });
    </script>
@endpush
