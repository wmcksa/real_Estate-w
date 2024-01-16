@extends('admin.layouts.app')
@section('title', trans($page_title))
@section('content')

    <div class="row">
        <div class="col-md-8 col-xl-8">
            <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
                <div class="card-body">
                    <form method="post" action="{{route('admin.share.investment.action')}}" class="form-row align-items-center">
                        @csrf
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">@lang('Property Share Investment')</label>
                            <div class="custom-switch-btn">
                                <input type='hidden' value='1' name='is_share_investment'>
                                <input type="checkbox" name="is_share_investment"
                                       class="custom-switch-checkbox "
                                       id="is_share_investment" value="0" {{ $control->is_share_investment == 0 ? 'checked' : '' }}>
                                <label class="custom-switch-checkbox-label" for="is_share_investment">
                                    <span class="custom-switch-checkbox-for-installments"></span>
                                    <span class="custom-switch-checkbox-switch"></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-6 col-xl-6 col-12">
                            <button type="submit"
                                    class="btn btn-primary btn-block  btn-rounded mx-2 mt-4">
                                <span>@lang('Save Changes')</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

