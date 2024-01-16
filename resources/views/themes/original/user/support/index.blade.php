@extends($theme.'layouts.user')
@section('title',__($page_title))

@section('content')

<section class="transaction-history">
    <div class="container-fluid">
        <div class="row mt-4 mb-2">
            <div class="col ms-2">
                <div class="header-text-full">
                    <h3 class="dashboard_breadcurmb_heading mb-1">@lang($page_title)</h3>
                    <nav aria-label="breadcrumb ms-1">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('user.home') }}">@lang('Dashboard')</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __($page_title) }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

       <div class="row">
          <div class="col">
             <div class="d-flex justify-content-end mb-4">
                 <a href="{{route('user.ticket.create')}}" class="btn btn-custom text-white create__ticket"> <i class="fa fa-plus-circle"></i> @lang('Create Ticket')</a>
             </div>
            <div class="card bg-light">

                <div class="card-body p-0">
                    <div class="table-parent table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">@lang('Subject')</th>
                                    <th scope="col">@lang('Status')</th>
                                    <th scope="col">@lang('Last Reply')</th>
                                    <th scope="col">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $key => $ticket)
                                    <tr>
                                        <td data-label="@lang('Ticket Id')">
                                            <span class="font-weight-bold"> [{{ trans('Ticket#').$ticket->ticket }}
                                            ] {{ $ticket->subject }}
                                            </span>
                                        </td>
                                        <td data-label="@lang('status')">
                                            @if($ticket->status == 0)
                                              <span class="badge rounded-pill bg-success">@lang('Open')</span>
                                            @elseif($ticket->status == 1)
                                                <span class="badge rounded-pill bg-primary">@lang('Answered')</span>
                                            @elseif($ticket->status == 2)
                                                <span class="badge rounded-pill bg-warning">@lang('Replied')</span>
                                            @elseif($ticket->status == 3)
                                                <span class="badge rounded-pill bg-danger">@lang('Closed')</span>
                                            @endif
                                        </td>
                                        <td>{{diffForHumans($ticket->last_reply) }}</td>
                                        <td data-label="Action">
                                            <a href="{{ route('user.ticket.view', $ticket->ticket) }}" class="action-btn">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>

                                    </tr>

                                @empty
                                    <tr class="text-center">
                                        <td colspan="100%">{{__('No Data Found!')}}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{ $tickets->appends($_GET)->links($theme.'partials.pagination') }}

                     </div>
                </div>
            </div>

          </div>
       </div>
    </div>
</section>


@endsection
