@extends($theme.'layouts.user')
@section('title',trans('All WishList'))

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap-datepicker.css') }}"/>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="main row">
            <div class="col-12">
                <div
                    class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">@lang('My WishList')</h3>
                </div>
                <div class="search-bar my-search-bar">
                    <form action="" method="get" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <div class="input-box input-group">

                                    <input type="text" name="name" value="{{ old('name',request()->name) }}" class="form-control" placeholder="@lang('Search property...')"/>
                                </div>
                            </div>

                            <div class="input-box col-lg-3 col-md-3 col-sm-12">
                                <input type="text" class="form-control datepicker from_date" name="purchase_date" autofocus="off" readonly placeholder="@lang('From date')" value="{{ old('purchase_date',request()->purchase_date) }}">
                            </div>

                            <div class="input-box col-lg-3 col-md-3 col-sm-12">
                                <input type="text" class="form-control datepicker to_date" name="expire_date" autofocus="off" readonly placeholder="@lang('To date')" value="{{ old('expire_date',request()->expire_date) }}" disabled="true">
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <button class="btn-custom" type="submit">@lang('search')</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- table -->
                <div class="table-parent table-responsive wishlistTable">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">@lang('property')</th>
                            <th scope="col">@lang('Data-Time')</th>
                            <th scope="col" class="text-end">@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($favourite_properties as $key => $wishlist)


                            <tr>
                                <td class="company-logo" data-label="@lang('Property')">
                                    <div>
                                        <a href="{{ route('propertyDetails',[@slug(optional($wishlist->get_property->details)->property_title), optional($wishlist->get_property)->id]) }}" target="_blank">
                                            <img src="{{ getFile(config('location.propertyThumbnail.path').optional($wishlist->get_property)->thumbnail) }}">
                                        </a>
                                    </div>
                                    <div>
                                        <a href="{{ route('propertyDetails',[@slug(optional($wishlist->get_property->details)->property_title), optional($wishlist->get_property)->id]) }}" target="_blank">@lang(\Illuminate\Support\Str::limit(optional($wishlist->get_property->details)->property_title, 30))</a> <br>
                                    </div>
                                </td>


                                <td data-label="Date-Time">{{ dateTime($wishlist->created_at) }}</td>

                                <td data-label="Action" class="action d-flex justify-content-end">
                                    <button class="action-btn notiflix-confirm" data-bs-toggle="modal" data-bs-target="#delete-modal" data-route="{{ route('user.favouriteListingDelete', $wishlist->id) }}" >
                                        <i class="fa fa-trash font-14"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <td class="text-center" colspan="100%"> @lang('No Data Found')</td>
                        @endforelse
                        </tbody>
                    </table>
                    {{ $favourite_properties->appends($_GET)->links() }}
                </div>
        </div>
    </div>

    @push('loadModal')
        <div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top modal-md">
                <div class="modal-content">
                    <div class="modal-header modal-primary modal-header-custom">
                        <h4 class="modal-title" id="editModalLabel">@lang('Delete Confirmation')</h4>
                        <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fal fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        @lang('Are you sure delete?')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-custom btn2 btn-secondary close_invest_modal close__btn" data-bs-dismiss="modal">@lang('Close')</button>
                        <form action="" method="post" class="deleteRoute">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-sm btn-custom addCreateListingRoute text-white">@lang('Confirm')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endpush

@endsection

@push('script')
    <script src="{{ asset('assets/global/js/bootstrap-datepicker.js') }}"></script>
    <script>
        'use strict'
        $(document).ready(function () {
            $(".datepicker").datepicker({
                autoclose: true,
                clearBtn: true
            });

            $('.from_date').on('change', function () {
                $('.to_date').removeAttr('disabled');
            });

            $('.notiflix-confirm').on('click', function () {
                var route = $(this).data('route');
                $('.deleteRoute').attr('action', route)
            })
        });
    </script>
@endpush
