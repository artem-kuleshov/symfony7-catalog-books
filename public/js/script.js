(function($) {
    "use strict"

    $(".select2").select2();

    $('#subscribe').on('click', function (e) {
        $(this).prop("disabled", true);

        $('#errors').html('');

        $.ajax({
            url: '/author/subscribe',
            type: "POST",
            data: {
                author_id: $('#author_id').val(),
                phone: $('#phone').val(),
            },
            dataType: 'json',
            complete: function () {
                $('#subscribe').prop("disabled", false);
            },
            success: function (res) {
                if (res.errors) {
                    $('#errors').html(res.errors);
                } else {
                    location.reload();
                }
            }
        });
    });
})(jQuery);