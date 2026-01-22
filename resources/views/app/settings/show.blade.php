@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="{{ route('settings.index') }}" class="mr-4"
                    ><i class="icon ion-md-arrow-back"></i
                ></a>
                @lang('crud.settings.show_title')
            </h4>

            <div class="mt-4">
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.system_name')</h5>
                    <span>{{ $setting->system_name ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.system_logo')</h5>
                    <span>{{ $setting->system_logo ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.allow_dark_sidebar')</h5>
                    <span>{{ $setting->allow_dark_sidebar ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.allow_message')</h5>
                    <span>{{ $setting->allow_message ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.allow_email')</h5>
                    <span>{{ $setting->allow_email ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.enable_upload_receipt')</h5>
                    <span>{{ $setting->enable_upload_receipt ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.mail_mailer')</h5>
                    <span>{{ $setting->mail_mailer ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.mail_host')</h5>
                    <span>{{ $setting->mail_host ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.mail_port')</h5>
                    <span>{{ $setting->mail_port ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.mail_username')</h5>
                    <span>{{ $setting->mail_username ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.mail_password')</h5>
                    <span>{{ $setting->mail_password ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.mail_encryption')</h5>
                    <span>{{ $setting->mail_encryption ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.mail_from_address')</h5>
                    <span>{{ $setting->mail_from_address ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.site_description')</h5>
                    <span>{{ $setting->site_description ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.site_contact_email')</h5>
                    <span>{{ $setting->site_contact_email ?? '-' }}</span>
                </div>
                <div class="mb-4">
                    <h5>@lang('crud.settings.inputs.site_contact_number')</h5>
                    <span>{{ $setting->site_contact_number ?? '-' }}</span>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('settings.index') }}" class="btn btn-light">
                    <i class="icon ion-md-return-left"></i>
                    @lang('crud.common.back')
                </a>

                @check('Update settings')
                <a href="{{ route('settings.create') }}" class="btn btn-light">
                    <i class="icon ion-md-add"></i> @lang('crud.common.create')
                </a>
                @endcheck
            </div>
        </div>
    </div>
</div>
@endsection
