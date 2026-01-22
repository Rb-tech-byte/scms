@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="searchbar mt-0 mb-4">
            <div class="row">
                <div class="col-md-6">
                    <form>
                        <div class="input-group">
                            <input id="indexSearch" type="text" name="search" placeholder="{{ __('crud.common.search') }}"
                                value="{{ $search ?? '' }}" class="form-control" autocomplete="off" />
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon ion-md-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-right">
                    @check('update settings')
                        <a href="{{ route('settings.create') }}" class="btn btn-primary">
                            <i class="icon ion-md-add"></i> @lang('crud.common.create')
                        </a>
                    @endcheck
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div style="display: flex; justify-content: space-between;">
                    <h4 class="card-title">@lang('crud.settings.index_title')</h4>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless table-hover">
                        <thead>
                            <tr>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.system_name')
                                </th>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.system_logo')
                                </th>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.allow_dark_sidebar')
                                </th>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.allow_message')
                                </th>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.allow_email')
                                </th>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.enable_upload_receipt')
                                </th>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.mail_mailer')
                                </th>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.mail_host')
                                </th>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.mail_port')
                                </th>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.mail_username')
                                </th>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.mail_password')
                                </th>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.mail_encryption')
                                </th>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.mail_from_address')
                                </th>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.site_description')
                                </th>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.site_contact_email')
                                </th>
                                <th class="text-left">
                                    @lang('crud.settings.inputs.site_contact_number')
                                </th>
                                <th class="text-center">
                                    @lang('crud.common.actions')
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($settings as $setting)
                                <tr>
                                    <td>{{ $setting->system_name ?? '-' }}</td>
                                    <td>{{ $setting->system_logo ?? '-' }}</td>
                                    <td>{{ $setting->allow_dark_sidebar ?? '-' }}</td>
                                    <td>{{ $setting->allow_message ?? '-' }}</td>
                                    <td>{{ $setting->allow_email ?? '-' }}</td>
                                    <td>
                                        {{ $setting->enable_upload_receipt ?? '-' }}
                                    </td>
                                    <td>{{ $setting->mail_mailer ?? '-' }}</td>
                                    <td>{{ $setting->mail_host ?? '-' }}</td>
                                    <td>{{ $setting->mail_port ?? '-' }}</td>
                                    <td>{{ $setting->mail_username ?? '-' }}</td>
                                    <td>{{ $setting->mail_password ?? '-' }}</td>
                                    <td>{{ $setting->mail_encryption ?? '-' }}</td>
                                    <td>{{ $setting->mail_from_address ?? '-' }}</td>
                                    <td>{{ $setting->site_description ?? '-' }}</td>
                                    <td>{{ $setting->site_contact_email ?? '-' }}</td>
                                    <td>{{ $setting->site_contact_number ?? '-' }}</td>
                                    <td class="text-center" style="width: 134px;">
                                        <div role="group" aria-label="Row Actions" class="btn-group">
                                            @check('Update settings')
                                                <a href="{{ route('settings.edit', $setting) }}">
                                                    <button type="button" class="btn btn-light">
                                                        <i class="icon ion-md-create"></i>
                                                    </button>
                                                </a>
                                            @endcheck
                                            @check('View settings')
                                                <a href="{{ route('settings.show', $setting) }}">
                                                    <button type="button" class="btn btn-light">
                                                        <i class="icon ion-md-eye"></i>
                                                    </button>
                                                </a>
                                                @endcheck
                                                @check('Update settings')
                                                   
                                                        <button type="submit" class="btn btn-light text-danger deletebtn" link="{{ route('settings.destroy', $setting) }}">
                                                            <i class="icon ion-md-trash"></i>
                                                        </button>
                                                    @endcheck
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="17">
                                                    @lang('crud.common.no_items_found')
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="17">{!! $settings->render() !!}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endsection
