@extends('admin.layouts.app')
@section('title')
    {{ trans($page_title) }}
@endsection
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary shadow">
                    <div class="card-body">
                        @if(adminAccessRoute(config('role.payment_settings.access.add')))
                            <a href="{{route('admin.deposit.manual.create')}}" class="btn btn-primary btn-rounded btn-sm float-right mb-3"><i class="fa fa-plus-circle"></i> {{trans('Create New')}}</a>
                        @endif

                        <table class="table ">
                            <thead class="thead-dark">
                            <tr>
                                <th scope="col">@lang('Name')</th>
                                <th scope="col">@lang('Status')</th>
                                @if(adminAccessRoute(config('role.payment_settings.access.edit')))
                                    <th scope="col">@lang('Action')</th>
                                @endif
                            </tr>

                            </thead>
                            <tbody id="sortable">
                            @if(count($methods) > 0)
                                @foreach($methods as $method)
                                    <tr data-code="{{ $method->code }}">


                                        <td data-label="@lang('Name')"><div class="d-flex no-block align-items-center">
                                                <div class="mr-3">
                                                    <img src="{{ getFile(config('location.gateway.path').$method->image)}}" alt="{{$method->name}}" class="rounded-circle" width="45" height="45">
                                                </div>
                                                <div class="mr-3">
                                                    <h5 class="text-dark mb-0 font-16 font-weight-medium">{{ $method->name }}</h5>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="@lang('Status')">

                                            {!!  $method->status == 1 ? '<span class="custom-badge bg-success badge-pill">'.trans('Active').'</span>' : '<span class="custom-badge bg-danger badge-sm">'.trans('Inactive').'</span>' !!}
                                        </td>

                                        @if(adminAccessRoute(config('role.payment_settings.access.edit')))
                                        <td data-label="@lang('Action')">
                                            <a href="{{ route('admin.deposit.manual.edit', $method->id) }}"
                                               class="btn btn-outline-primary btn-rounded btn-sm"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               data-original-title="@lang('Edit this Payment Methods info')">
                                                <i class="fa fa-edit"></i></a>
                                                <button type="button"
                                                        data-code="{{$method->code}}"
                                                        data-status="{{$method->status}}"
                                                        data-message="{{($method->status == 0)?'Enable':'Disable'}}"
                                                        class="btn btn-sm btn-rounded btn-{{($method->status == 0)?'outline-success':'outline-danger'}} disableBtn"
                                                        data-toggle="modal" data-target="#disableModal" ><i class="fa fa-{{($method->status == 0)?'check':'ban'}}"></i>
                                                </button>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-center text-na" colspan="8">
                                        @lang('No Data Found')
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>



    <div id="disableModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header modal-colored-header bg-primary">
                    <h5 class="modal-title"><span class="messageShow"></span> @lang('Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.payment.methods.deactivate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="code">
                    <div class="modal-body">
                        <p class="font-weight-bold">@lang('Are you sure to') <span class="messageShow"></span> {{trans('this?')}}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn waves-effect waves-light btn-danger btn-rounded" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn waves-effect waves-light btn-primary btn-rounded messageShow"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@push('js')
    <script>
        "use strict";
        $('.disableBtn').on('click', function () {
            var status  = $(this).data('status');
            $('.messageShow').text($(this).data('message'));
            var modal = $('#disableModal');
            modal.find('input[name=code]').val($(this).data('code'));
        });
    </script>
@endpush
