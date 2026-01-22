<footer class="footer-admin mt-auto footer-light">
    <div class="container-xl px-4">
        <div class="row">
            <div class="col-md-6 small">Copyright {{ date('Y') }} &copy; Institute of Education and Management
                Technologies (IEMT)</div>
            <!--div class="col-md-6 text-md-end small">
                &middot;
                Developed By :
                <a href="https://wolinet.com">WOLINET LTD</a>
            </div> -->
        </div>
    </div>
</footer>

@push('js')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
        });
        $(document).ready(function() {
            $('label').each(function() {
                if ($(this).text().includes('*')) {
                    var labelText = $(this).html().replace('*',
                        '<span style="color: red;font-size: 20px;">*</span>');
                    $(this).html(labelText);
                }
            });

            $(".deletebtn").click(function() {
                var link = $(this).attr('link');

                swal.fire({
                    title: "{{ __('crud.common.are_you_sure') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            type: 'POST',
                            url: link,
                            data: {
                                _method: "DELETE"
                            },
                            dataType: 'json',
                            success: function(response) {
                                if(response ==1){
                                swal.fire({
                                    title: "Deleted!",
                                    text: "The record has been deleted.",
                                    icon: "success",
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                                window.setTimeout(function() {
                                    window.location.reload();
                                }, 4000); 
                            }else{
                                swal.fire({
                                    title: "Opps!",
                                    text: response,
                                    icon: "danger",
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                            },
                            error: function(xhr) {
                                swal.fire({
                                    title: "Opps!",
                                    text: "Failed to delete.",
                                    icon: "danger",
                                    timer: 2000,
                                    showConfirmButton: false
                                });;
                            }
                        })
                    }
                });
            });
        });
        $("#applicant_id").change(function() {
            var id = $(this).val();
            if (id > 0) {

                $.ajax({
                    type: 'POST',
                    url: ' {{ route('payment.filter_course') }} ',
                    data: {
                        id: id
                    },
                    dataType: 'html',
                    success: function(data) {
                        $('#course_id').html(data);
                    }
                });
            }
        });

        $("#course_id").change(function() {
            var course_id = $(this).val();
            var student = $("#applicant_id").val();

            if (course_id < 1) {
                $('#amount').attr('value', '');
                $("#add_payment").show();
                $("#pay_check").show();
                $('#complete_status').hide();
            } else {

                $.ajax({
                    url: '{{ route('payment.course_amount') }}',
                    type: 'POST',
                    data: {
                        course: course_id,
                        student_id: student
                    },
                    dataType: 'html',
                    success: function(response) {
                        $('#amount').attr('value', response);
                        if (response < 1) {
                            $("#add_payment").css('display', 'none');
                            $("#pay_check").hide();
                            $('#complete_status').show();
                        } else {
                            $("#add_payment").show();
                            $('#complete_status').hide();

                        }
                    }
                });
            }
        });

        $("#role_id").change(function() {
            var role = $(this).val();
            if (role != 0) {
                $.ajax({
                    url: "{{ route('encrypt.role') }}", // New route that encrypts role
                    type: "POST",
                    data: {
                        role_id: role,
                        _token: "{{ csrf_token() }}" // CSRF token for Laravel
                    },
                    success: function(response) {
                        var link = "{{ url('permission/manage') }}/" + response;
                        window.location.href = link;
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }

        });
        $(".role").change(function() {
            var role_id = $("#role_id").val();
            var checked = $(this).prop('checked');
            var permission = $(this).attr('id');

            if (checked) {
                var method = "create";
            } else {
                var method = "destroy";
            }
            if (role_id > 0) {

                $.ajax({
                    url: "{{ route('permission.assign_role_permissions') }}",
                    type: "POST",
                    data: {
                        method: method,
                        role_id: role_id,
                        permission_id: permission
                    },
                    dataType: "html",
                    success: function(response) {
                        $('#fs_message').text(response);
                        $('#flash-message').show();
                        setTimeout(function() {
                            $('#flash-message').fadeOut();
                        }, 3000);
                    }
                });
            }
        });

        $("#global_search").keyup(function() {
            var query = $(this).val();
            if (query.length < 3) {
                return;
            } else {
                $.ajax({
                    url: "{{ route('global.search') }}",
                    type: 'POST',
                    data: {
                        search: query
                    },
                    success: function(data) {
                        $('#search-results').html(data).show();
                    },
                    error: function() {
                        $('#search-results').html('<p>No results found</p>').show();
                    }
                });
            }

        });

        $("#global_m_search").on('input', function() {
            var query = $(this).val();
            if (query.length < 3) {
                return;
            } else {
                $.ajax({
                    url: "{{ route('global.search') }}",
                    type: 'POST',
                    data: {
                        search: query
                    },
                    success: function(data) {
                        $('#search-m-results').html(data).show();
                    },
                    error: function() {
                        $('#search-m-results').html('<p>No results found</p>').show();
                    }
                });
            }

        });
        $(document).click(function(event) {
            if (!$(event.target).closest('.search-container').length) {
                $('#search-results').hide();
                $('#search-m-results').hide();

            }
        });
    </script>
@endpush
