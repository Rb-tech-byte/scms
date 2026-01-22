@extends('layouts.app')
@section('title', 'Register Student')
@section('header')
{!! $generatePageHeader('Add Student', 'applicants.index', 'user', 'Manage Student', 'users') !!}
@endsection

@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form method="POST" has-files action="{{ route('applicants.store') }}" class="mt-4">
                    @include('app.applicants.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('applicants.index') }}" class="btn btn-light">
                            <i class="icon ion-md-return-left text-primary"></i>
                            @lang('crud.common.back')
                        </a>

                        <button type="submit" class="btn btn-primary float-right">
                            <i class="icon ion-md-save"></i>
                            @lang('crud.common.create')
                        </button>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
    {{-- @push('js')
    <script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            ajax: {
                url: 'url_to_fetch_options', /
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 1 // Set a minimum number of characters before triggering the AJAX request
        });
    });
</script>
@endpush --}}
@endsection
