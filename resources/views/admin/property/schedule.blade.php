@extends('admin.layouts.app')
@section('title')
    @lang('Manage Schedule')
@endsection

@section('content')

    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">

        <div class="card-body">
            @if(adminAccessRoute(config('role.manage_property.access.add')))
            <button class="btn btn-sm  btn-primary btn-rounded float-right mb-2" type="button"
                    data-toggle="modal"
                    data-target="#addModal">
                <span><i class="fas fa-plus"></i> @lang('Create New')</span>
            </button>
            @endif

            <div class="table-responsive">
                <table class="categories-show-table table table-hover table-striped table-bordered" id="zero_config">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">@lang('Duration')</th>
                        <th scope="col">@lang('Duration Type')</th>
                        <th scope="col">@lang('Status')</th>
                        @if(adminAccessRoute(config('role.manage_property.access.edit')))
                            <th scope="col">@lang('Action')</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($manageTimes as $item)
                        <tr>
                            <td data-label="@lang('Duration')">
                                @lang($item->time)
                            </td>

                            <td data-label="@lang('Duration Type')">
                                @if($item->time_type == 'days' && $item->time == 1)
                                    @lang('Day')
                                @elseif($item->time_type == 'months' && $item->time == 1)
                                    @lang('Month')
                                @elseif($item->time_type == 'years' && $item->time == 1)
                                    @lang('Year')
                                @else
                                    @lang($item->time_type)
                                @endif
                            </td>
                            <td data-label="@lang('Status')">
                                <span
                                    class="custom-badge badge-pill {{ $item->status == 0 ? 'bg-danger' : 'bg-success' }}">{{ $item->status == 0 ? 'Inactive' : 'Active' }}</span>
                            </td>
                            @if(adminAccessRoute(config('role.manage_property.access.edit')))
                            <td data-label="@lang('Action')">
                                <button class="btn btn-sm  btn-outline-primary btn-rounded btn-sm edit-button" type="button"
                                        data-toggle="modal"
                                        data-target="#editModal"
                                        data-timetype="{{$item->time_type}}"
                                        data-time="{{$item->time}}"
                                        data-status="{{$item->status}}"
                                        data-route="{{route('admin.update.schedule',['id'=>$item->id])}}">
                                    <span><i class="fas fa-edit"></i></span>
                                </button>
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

    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">@lang('Create New Schedule')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.store.schedule')}}" method="post">
                    @csrf
                    <div class="modal-body">

                        <div class="form-group">
                            <label>@lang('Time')</label>
                            <div class="input-group mb-3">
                                <input type="number" name="time" class="form-control" value="{{old('time')}}" placeholder="@lang('schedule time')">
                                <div class="input-group-append">
                                    <select name="time_type" id="time_type" class="form-control">
                                        <option value="days">@lang('Day(s)')</option>
                                        <option value="months">@lang('Months(s)')</option>
                                        <option value="years">@lang('Year(s)')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>@lang('Status')</label>
                            <div class="input-group mb-3">
                                <select class="form-control  w-100 edit_status"
                                        data-live-search="true" name="status"
                                        required="">
                                    <option value="1">{{trans('Active')}}</option>
                                    <option value="0">{{trans('Deactive')}}</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ trans($message) }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-rounded" data-dismiss="modal">
                            <span>@lang('Cancel')</span>
                        </button>
                        <button type="submit" class="btn btn-primary btn-rounded">
                            <span><i class="fas fa-save"></i> @lang('Save Changes')</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">@lang('Edit Schedule')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" id="editForm">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Time')</label>
                            <div class="input-group mb-3">
                                <input type="number" name="time" class="form-control edit_time" value="{{old('time')}}" placeholder="@lang('schedule time')">
                                <div class="input-group-append">
                                    <select name="time_type" id="edit_time_type" class="form-control edit_time_type">
                                        <option value="days" >@lang('Day(s)')</option>
                                        <option value="months" >@lang('Months(s)')</option>
                                        <option value="years">@lang('Year(s)')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>@lang('Status')</label>
                            <div class="input-group mb-3">
                                <select class="form-control w-100 edit_status"
                                        data-live-search="true" name="status"
                                        required="">
                                    <option value="1">{{trans('Active')}}</option>
                                    <option value="0">{{trans('Deactive')}}</option>
                                </select>
                                @error('status')
                                <span class="text-danger">{{ trans($message) }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-rounded" data-dismiss="modal">
                            <span>@lang('Cancel')</span>
                        </button>
                        <button type="submit" class="btn btn-primary btn-rounded">
                            <span><i class="fas fa-save"></i> @lang('Save Changes')</span></button>
                    </div>
                </form>
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

    <script>
        "use strict";
        $(document).ready(function () {
            $(document).on('click', '.edit-button', function () {
                $('#editForm').attr('action', $(this).data('route'))
                $('.edit_time').val($(this).data('time'))
                $('.edit_time_type').val($(this).data('timetype'));
                $('.edit_status').val($(this).data('status'));
            })

        });

    </script>
@endpush
