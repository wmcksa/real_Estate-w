@extends('admin.layouts.app')
@section('title')
    @lang("Wish List")
@endsection

@section('content')
    <style>
        .fa-ellipsis-v:before {
            content: "\f142";
        }
    </style>

    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <div class="row justify-content-between">
            <div class="col-md-12">
                <form action="" method="get" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-3 col-xl-3 col-sm-6 mb-3">
                            <div class="form-group">
                                <label for="name"> @lang('Property')</label>
                                <input type="text" name="name" value="{{ old('name',request()->name) }}" class="form-control"
                                       placeholder="@lang('search property..')">
                            </div>
                        </div>
                        <div class="col-md-3 col-xl-3 col-sm-12">
                            <div class="form-group">
                                <label for="title"> @lang('From Date')</label>
                                <input type="date" class="form-control from_date" name="from_date" id="datepicker" placeholder="@lang('From date')" value="{{ old('from_date', request()->from_date) }}"/>
                            </div>
                        </div>

                        <div class="col-md-3 col-xl-3 col-sm-12">
                            <div class="form-group">
                                <label for="title"> @lang('To Date')</label>
                                <input type="date" class="form-control to_date" name="to_date" id="datepicker" placeholder="@lang('To date')" value="{{ old('to_date', request()->to_date) }}" disabled="true"/>
                            </div>
                        </div>

                        <div class="col-md-3 col-xl-3 col-sm-12">
                            <div class="form-group">
                                <label></label>
                                <button type="submit" class="btn w-100  btn-primary listing-btn-search-custom mt-2"><i
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
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                    <th>@lang('#')</th>
                    <th>@lang('Property')</th>
                    <th>@lang('User')</th>
                    <th>@lang('Date-Time')</th>
                    <th class="text-end">@lang('Action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($wishLists as $key => $wish)
                        <tr>
                            <td data-label="@lang('No.')">{{++$key}}</td>

                            <td data-label="@lang('Property')">
                                <a href="{{ route('propertyDetails',[slug(optional(optional($wish->get_property)->details)->property_title), optional($wish->get_property)->id]) }}" target="_blank">
                                    <div class="d-flex no-block align-items-center">
                                        <div class="mr-3"><img src="{{getFile(config('location.propertyThumbnail.path').optional($wish->get_property)->thumbnail) }}" alt="@lang('property_thumbnail')" class="rounded-circle" width="45" height="45"></div>
                                        <div class="">
                                            <h5 class="text-dark mb-0 font-16 font-weight-medium">
                                                @lang(\Illuminate\Support\Str::limit(optional(optional($wish->get_property)->details)->property_title, 30))
                                            </h5>
                                        </div>
                                    </div>
                                </a>
                            </td>

                            <td data-label="@lang('User')">
                                <a href="{{route('admin.user-edit',optional($wish->get_user)->id)}}" target="_blank">
                                    <div class="d-flex no-block align-items-center">
                                        <div class="mr-3"><img src="{{getFile(config('location.user.path').optional($wish->get_user)->image) }}" alt="user" class="rounded-circle" width="45" height="45">
                                        </div>
                                        <div class="">
                                            <h5 class="text-dark mb-0 font-16 font-weight-medium">
                                                @lang(optional($wish->get_user)->firstname) @lang(optional($wish->get_user)->lastname)
                                            </h5>
                                            <span class="text-muted font-14"><span>@</span>@lang(optional($wish->get_user)->username)</span>
                                        </div>
                                    </div>
                                </a>
                            </td>

                            <td data-label="@lang('Date-Time')">
                                {{ customDate($wish->created_at) }}
                            </td>

                            <td data-label="@lang('Action')">
                                <a href="javascript:void(0)"
                                   data-route="{{route('admin.content.delete',$wish->id)}}"
                                   data-toggle="modal"
                                   data-target="#deleteModal"
                                   class="btn btn-outline-danger btn-rounded btn-sm notiflix-confirm"><i
                                        class="fas fa-trash-alt"></i></a>
                            </td>


                        </tr>
                    @empty
                        <tr>
                            <td class="text-center text-danger" colspan="9">@lang('No Data Found')</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                {{$wishLists->appends($_GET)->links('partials.pagination')}}
            </div>
        </div>
    </div>

    @push('adminModal')
        <div id="deleteModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-primary">
                        <h5 class="modal-title"><span class="messageShow"></span> @lang('Confirmation')</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="" method="POST" class="deleteRoute">
                        @csrf
                        @method('delete')
                        <div class="modal-body">
                            <p class="font-weight-bold">@lang('Are you sure delete wishList?') </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn waves-effect waves-light btn-dark" data-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn waves-effect waves-light btn-primary messageShow"> @lang('Delete')</button>
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
        $(document).ready(function () {
            $('.notiflix-confirm').on('click', function () {
                var route = $(this).data('route');
                $('.deleteRoute').attr('action', route)
            })
            $('.from_date').on('change', function (){
                $('.to_date').removeAttr('disabled')
            });
        });
    </script>
@endpush
