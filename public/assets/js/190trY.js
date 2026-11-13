
$(function () {
    $('.itemSearch').select2({
        theme: 'bootstrap-5',
        ajax: {
            url: '{{ route("countries.search") }}',
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.name,
                            id: item.id
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });



    var courseSelect = $('#course_id');
    var feeInput = $('.fee');


    courseSelect.on('change', function () {
        var selectedOption = $('option:selected', this);
        var selectedCourseId = selectedOption.val();
        if (selectedCourseId) {
            var url = pageurl;
            url = url.replace(':id', selectedCourseId);
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    feeInput.val(data.fee);
                    //check duration with value of course duration
                    var duration = data.duration;
                    var durationRadios = document.querySelectorAll(
                        'input[name="duration"]');
                    for (var i = 0; i < durationRadios.length; i++) {
                        if (durationRadios[i].value === duration) {
                            durationRadios[i].checked = true;
                        }
                    }

                    //check if course has discount
                    if (data.has_discount) {
                        $('#show_only_if_course_selected_has_discount').show();

                        // check if has discount is checked then deduct discount amount from fee
                        var hasDiscountCheckbox = $('#has_discount_checkbox');
                        var discountAmount = data.discount;
                        $('#show_discount_amount').text(discountAmount + ' Tshs');


                    } else {
                        $('#show_only_if_course_selected_has_discount').hide();
                    }

                },
                error: function (xhr, textStatus, errorThrown) {
                    console.error('Error:', errorThrown);
                }
            });
        } else {
            feeInput.val('');
        }

    });


        var start_date = $('#start_date');
        var end_date = $('#end_date');
        start_date.on('change', function () {
            var selectedOption = $('option:selected', courseSelect);
            var selectedCourseId = selectedOption.val();
            if (selectedCourseId) {
                var url = pageurl;
                url = url.replace(':id', selectedCourseId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        var duration = data.duration;
                        var startDate = new Date(start_date.val());
                        var endDate = new Date(end_date.val());
                        var endDate = new Date(startDate);
                        endDate.setDate(endDate.getDate() + (duration * 7));
                        end_date.val(endDate.toISOString().slice(0, 10));
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.error('Error:', errorThrown);
                    }
                });
            }
        });




        // check if partial payment is checked then enable amount field
        var partialPayment = $('.payment_status_class');
        var message = $('#if_partial_payment_is_checked_text').text('');

        var defaultChecked = $('#full_payment').prop('checked', true);


        partialPayment.on('change', function () {
            if (partialPayment.filter(':checked').val() === '1') {
                $('#partialPayment').prop('checked', true);
                message.text('Amount will be deducted by 50%');
            } else {
                $('#full_payment').prop('checked', true);
                message.text('');
            }


        });



        updateDiscountFields();

        $('.apply_discount').change(function () {

            updateDiscountFields();
        });

        function updateDiscountFields() {
            const applyDiscountRadio = $('#discount_yes');
            const ifDiscountRow = $('#if_discount_checked_row');
            const discountReasonInputs = $('input[name="discount_reason"]');

            if (applyDiscountRadio.filter(':checked').val() === '1') {
                ifDiscountRow.show();
                discountReasonInputs.first().prop('checked', true);
            } else {
                ifDiscountRow.hide();
                discountReasonInputs.prop('checked', false);
            }
        }


    });



