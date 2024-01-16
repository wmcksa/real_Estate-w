@extends('admin.layouts.app')
@section('title')
    @lang('Address List')
@endsection

@section('content')
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            @if(adminAccessRoute(config('role.manage_property.access.add')))
                <div class="media mb-4 float-right">
                    <a href="{{route('admin.addressCreate')}}" class="btn btn-sm btn-primary btn-rounded mr-2">
                        <span><i class="fa fa-plus-circle"></i> @lang('Create New')</span>
                    </a>
                </div>
            @endif
            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered" id="zero_config">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">@lang('SL No.')</th>
                        <th scope="col">@lang('address')</th>
                        <th scope="col">@lang('Status')</th>
                        @if(adminAccessRoute(config('role.manage_property.access.edit')) == true)
                            <th scope="col">@lang('Action')</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($addresses as $item)
                        <tr>
                            <td data-label="@lang('SL No.')">{{$loop->index+1}}</td>

                            <td data-label="@lang('Title')">
                                @lang(optional(@$item->details)->title)
                            </td>
                            <td data-label="@lang('Status')">
                                <span
                                    class="custom-badge {{ @$item->status == 1 ? 'bg-success' : 'bg-danger' }}">@lang(@$item->status == 1 ? 'Active' : 'Deactive')</span>
                            </td>

                            @if(adminAccessRoute(config('role.manage_property.access.edit')) == true || adminAccessRoute(config('role.manage_property.access.delete')) == true)
                                <td data-label="@lang('Action')">
                                    @if(adminAccessRoute(config('role.manage_property.access.edit')))
                                        <a href="{{ route('admin.addressEdit',$item->id) }}"
                                           class="btn btn-sm btn-outline-primary btn-rounded edit-button">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                </td>
                            @endif
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

    <!-- Delete Modal -->
    <div id="delete-modal" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="primary-header-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-primary">
                    <h4 class="modal-title" id="primary-header-modalLabel">@lang('Delete Confirmation')
                    </h4>
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">×
                    </button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you sure to delete this?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light"
                            data-dismiss="modal">@lang('Close')</button>
                    <form action="" method="post" class="deleteRoute">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-primary">@lang('Yes')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('style-lib')
    <link href="{{asset('assets/admin/css/dataTables.bootstrap4.css')}}" rel="stylesheet">
@endpush
@push('js')
    <script src="{{ asset('assets/admin/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/datatable-basic.init.js') }}"></script>

    <script>
        'use strict'
        $('.notiflix-confirm').on('click', function () {
            var route = $(this).data('route');
            $('.deleteRoute').attr('action', route)
        })
    </script>
@endpush
