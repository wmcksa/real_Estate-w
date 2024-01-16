@extends('admin.layouts.app')
@section('title',__($title))
@section('content')
    <div class="page-header card card-primary m-0 m-md-4 my-4 m-md-0 p-5 shadow">
        <form action="{{ route('admin.ticket') }}" method="get">
            <div class="row justify-content-between align-items-center">

                <div class="col-md-4 col-lg-4 col-12">
                    <div class="form-group">
                        <label for="name">@lang('Filter By User')</label>
                        <input type="text" name="name" value="{{@request()->name}}" class="form-control"
                               placeholder="@lang('User name')">
                    </div>
                </div>

                <div class="col-md-4 col-lg-4 col-12">
                    <div class="form-group">
                        <label for="user">@lang('Ticket Id')</label>
                        <input type="text" name="ticket_id" value="{{@request()->ticket_id}}" class="form-control"
                               placeholder="@lang('Ticket no')">
                    </div>
                </div>


                <div class="col-md-4 col-lg-4">
                    <div class="form-group">
                        <label>@lang('Ticket Status')</label>
                        <select name="status" class="form-control">
                            <option value="">@lang('All Ticket')</option>
                            <option value="0"
                                    @if(@request()->status == '0') selected @endif>@lang('Open Ticket')</option>
                            <option value="1"
                                    @if(@request()->status == '1') selected @endif>@lang('Answered Ticket')</option>
                            <option value="2"
                                    @if(@request()->status == '2') selected @endif>@lang('Replied Ticket')</option>
                            <option value="3"
                                    @if(@request()->status == '3') selected @endif>@lang('Closed Ticket')</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4 col-lg-4 col-12">
                    <label for="from_date">@lang('From Date')</label>
                    <input type="date" class="form-control from_date" name="from_date" value="{{ old('from_date',request()->from_date) }}" placeholder="@lang('From date')" autocomplete="off"/>
                </div>

                <div class="col-lg-4 col-md-4 col-12">
                    <label for="to_date" class="to_date_margin">@lang('To Date')</label>
                    <input type="date" class="form-control to_date" name="to_date" value="{{ old('to_date',request()->to_date) }}" placeholder="@lang('To date')" autocomplete="off" disabled="true"/>
                </div>


                <div class="col-lg-4 col-md-4 col-12">
                    <label class="opacity-0">@lang('...')</label>
                    <button type="submit" class="btn waves-effect w-100 waves-light btn-primary btn-rounded"><i
                            class="fas fa-search"></i> @lang('Search')</button>
                </div>
            </div>
        </form>

    </div>


    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">

            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">@lang('Ticket Id')</th>
                        <th scope="col">@lang('User')</th>
                        <th scope="col">@lang('Subject')</th>
                        <th scope="col">@lang('Status')</th>
                        <th scope="col">@lang('Last Reply')</th>
                        @if(adminAccessRoute(config('role.support_ticket.access.view')) || adminAccessRoute(config('role.support_ticket.access.delete')))
                        <th scope="col">@lang('Action')</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($tickets as $key => $ticket)
                        <tr>
                            <td data-label="Ticket Id">
                                <a href="{{ route('admin.ticket.view', $ticket->id) }}" class="font-weight-bold"
                                   target="_blank">
                                    {{ trans('#').$ticket->ticket }} </a>
                            </td>

                            <td data-label="Submitted By">
                                @if($ticket->user_id)
                                    <a href="{{route('admin.user-edit',[$ticket->user_id])}}" target="_blank">
                                        <div class="d-flex no-block align-items-center">
                                            <div class="mr-3"><img src="{{getFile(config('location.user.path').optional($ticket->user)->image) }}" alt="user" class="rounded-circle" width="45" height="45">
                                            </div>
                                            <div class="">
                                                <h5 class="text-dark mb-0 font-16 font-weight-medium">
                                                    @lang(optional($ticket->user)->firstname) @lang(optional($ticket->user)->lastname)
                                                </h5>
                                                <span class="text-muted font-14"><span>@</span>@lang(optional($ticket->user)->username)</span>
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    <p class="font-weight-bold"> {{$ticket->name}}</p>
                                @endif
                            </td>

                            <td data-label="Subject">
                                <a href="{{ route('admin.ticket.view', $ticket->id) }}" class="font-weight-bold"
                                   target="_blank">
                                    @lang($ticket->subject)</a>
                            </td>


                            <td data-label="@lang('Status')">
                                @if($ticket->status == 0)
                                    <span class="custom-badge badge-pill bg-success">@lang('Open')</span>
                                @elseif($ticket->status == 1)
                                    <span class="custom-badge badge-pill bg-primary">@lang('Answered')</span>
                                @elseif($ticket->status == 2)
                                    <span
                                        class="custom-badge badge-pill bg-warning">@lang('Customer Reply')</span>
                                @elseif($ticket->status == 3)
                                    <span class="custom-badge badge-pill badge-dark">@lang('Closed')</span>
                                @endif
                            </td>

                            <td data-label="@lang('Last Reply')">
                                {{diffForHumans($ticket->last_reply) }}
                            </td>

                            @if(adminAccessRoute(config('role.support_ticket.access.view')) || adminAccessRoute(config('role.support_ticket.access.delete')))
                            <td data-label="Action">
                                @if(adminAccessRoute(config('role.support_ticket.access.view')))
                                <a href="{{ route('admin.ticket.view', $ticket->id) }}"
                                   class="btn btn-sm btn-rounded btn-outline-info"
                                   data-toggle="tooltip" title="" data-original-title="Details">
                                    <i class="fa fa-eye"></i>
                                </a>
                                @endif
                                @if(adminAccessRoute(config('role.support_ticket.access.delete')))
                                <button type="button" class="btn btn-sm btn-rounded btn-outline-danger notiflix-confirm"
                                        data-toggle="modal" data-target="#delete-modal"
                                        data-route="{{ route('admin.ticket.delete', $ticket->id) }}">
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                                @endif
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%">
                                <p class="text-center text-na">@lang($empty_message)</p>
                            </td>
                        </tr>

                    @endforelse
                    </tbody>
                </table>
                {{ $tickets->appends($_GET)->links('partials.pagination') }}
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
                    <button type="button" class="btn btn-danger btn-rounded"
                            data-dismiss="modal">@lang('Close')</button>
                    <form action="" method="post" class="deleteRoute">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-primary btn-rounded">@lang('Yes')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        'use strict'
        $('.from_date').on('change', function (){
            $('.to_date').removeAttr('disabled');
        });

        $('.notiflix-confirm').on('click', function () {
            var route = $(this).data('route');
            $('.deleteRoute').attr('action', route)
        })
    </script>
@endpush
