@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('general.create') }} {{ trans('general.purchase_request') }}
    @parent
@stop

{{-- Page content --}}
@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">

        @include('partials.procurement-pipeline', ['pipelineStep' => 1])

        <form class="form-horizontal"
              method="POST"
              action="{{ route('purchase-requests.store') }}"
              autocomplete="off">
            @csrf

            <div class="box box-default">

                <div class="box-header with-border">
                    <h2 class="box-title">{{ trans('general.purchase_request') }}</h2>
                </div>

                <div class="box-body">

                    {{-- Global validation summary --}}
                    @if ($errors->any())
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-danger">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                    {{ trans('general.form_has_errors') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Title --}}
                    <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                        <label for="title" class="col-md-3 control-label">
                            {{ trans('general.title') }}
                            <span class="required" aria-hidden="true">*</span>
                        </label>
                        <div class="col-md-7">
                            <input type="text"
                                   class="form-control"
                                   name="title"
                                   id="title"
                                   value="{{ old('title') }}"
                                   placeholder="{{ trans('general.title') }}"
                                   aria-required="true"
                                   aria-label="{{ trans('general.title') }}"
                                   required>
                            {!! $errors->first('title', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- Department (select2 AJAX, consistent with user edit form) --}}
                    <div class="form-group {{ $errors->has('department') ? 'has-error' : '' }}">
                        <label for="department_select" class="col-md-3 control-label">
                            {{ trans('general.department') }}
                        </label>
                        <div class="col-md-7">
                            <select class="js-data-ajax"
                                    data-endpoint="departments"
                                    data-placeholder="{{ trans('general.select_department') }}"
                                    name="department"
                                    id="department_select"
                                    style="width: 100%"
                                    aria-label="{{ trans('general.department') }}">
                                @if (old('department'))
                                    <option value="{{ old('department') }}" selected="selected">
                                        {{ \App\Models\Department::find(old('department'))?->name ?? old('department') }}
                                    </option>
                                @endif
                            </select>
                            {!! $errors->first('department', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- Justification --}}
                    <div class="form-group {{ $errors->has('justification') ? 'has-error' : '' }}">
                        <label for="justification" class="col-md-3 control-label">
                            {{ trans('general.justification') }}
                        </label>
                        <div class="col-md-7">
                            <textarea class="form-control"
                                      name="justification"
                                      id="justification"
                                      rows="5"
                                      placeholder="{{ trans('general.justification') }}"
                                      aria-label="{{ trans('general.justification') }}"
                            >{{ old('justification') }}</textarea>
                            {!! $errors->first('justification', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                        <label for="notes" class="col-md-3 control-label">
                            {{ trans('general.notes') }}
                        </label>
                        <div class="col-md-7">
                            <textarea class="form-control"
                                      name="notes"
                                      id="notes"
                                      rows="3"
                                      placeholder="{{ trans('general.notes') }}"
                                      aria-label="{{ trans('general.notes') }}"
                            >{{ old('notes') }}</textarea>
                            {!! $errors->first('notes', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                </div>{{-- /.box-body --}}

                <div class="box-footer">
                    <div class="col-md-7 col-md-offset-3">

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane" aria-hidden="true"></i>
                            {{ trans('general.submit_purchase_request') }}
                        </button>

                        <a href="{{ url()->previous() }}" class="btn btn-link btn-sm">
                            {{ trans('button.cancel') }}
                        </a>

                    </div>
                </div>{{-- /.box-footer --}}

            </div>{{-- /.box --}}

        </form>

    </div>{{-- /.col-md-8 --}}
</div>{{-- /.row --}}

@stop
