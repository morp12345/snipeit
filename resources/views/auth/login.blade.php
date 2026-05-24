@extends('layouts/basic')


{{-- Page content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">

                <div class="box login-box">
                    <div class="box-header with-border">
                        <h1 class="box-title">{{ trans('auth/general.login_prompt') }}</h1>
                    </div>

                    <div class="login-box-body">
                        <div class="row">

                            @if ($snipeSettings->login_note)
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        {!! Helper::parseEscapedMarkedown($snipeSettings->login_note) !!}
                                    </div>
                                </div>
                            @endif

                            <!-- Notifications -->
                            @include('notifications')

                            <div class="col-md-12">
                                {!! $errors->first('username', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                            </div>
                        </div> <!-- end row -->
                    </div>

                    <div class="box-footer">
                        <a href="{{ route('orangehrm.redirect') }}" class="btn btn-primary btn-block btn-lg">
                            {{ trans('auth/general.orangehrm_login') }}
                        </a>
                    </div>

                </div> <!-- end login box -->

            </div> <!-- col-md-4 -->
        </div> <!-- end row -->
    </div> <!-- end container -->

@stop
