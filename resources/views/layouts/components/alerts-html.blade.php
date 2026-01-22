@if (session()->has('error'))
<div class="container-xl">
    <div class="alert alert-red alert-icon" role="alert">
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="alert-icon-aside">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="alert-icon-content">
            {{ session('error') }}
        </div>
    </div>
</div>
@endif

@if ($errors->any())
<div class="container-xl">
    <div class="alert alert-red alert-icon" role="alert">
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="alert-icon-aside">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="alert-icon-content">
            @foreach ($errors->all() as $error)
            {{ $error }}
            @endforeach
        </div>
    </div>
</div>
@endif

@if (session()->has('warning'))
<div class="container-xl">
    <div class="alert alert-yellow alert-icon" role="alert">
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="alert-icon-aside">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="alert-icon-content">
            {{ session('warning') }}
        </div>
    </div>
</div>
@endif

@if (session()->has('info'))
<div class="container-xl">
    <div class="alert alert-blue alert-icon" role="alert">
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="alert-icon-aside">
            <i class="fa-solid fa-info-circle"></i>
        </div>
        <div class="alert-icon-content">
            <h6 class="alert-heading">Info</h6>
            {{ session('info') }}
        </div>
    </div>
</div>
@endif

    <div class="container-xl" id="flash-message" style="display: none;" >
        <div class="alert alert-success alert-icon" role="alert">
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            <div class="alert-icon-aside">
                <i class="fa-solid fa-info-circle"></i>
            </div>
            <div class="alert-icon-content" id="fs_message">
            </div>
        </div>
    </div>

{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        var formsubmission =document.getElementById('#delete-form');
        if(formsubmission!==null){
        document.querySelector('#delete-form').addEventListener('submit', function(event) {
        event.preventDefault();
        Swal.fire({
            title: "{{ __('crud.common.are_you_sure') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        });
    });
}
    });
</script> --}}

