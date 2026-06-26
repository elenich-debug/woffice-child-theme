jQuery(document).ready(function($) {
    // Бесплатное голосование
    $('.btn-vote-free').on('click', function() {
        const form = $(this).closest('form');
        const data = {
            action: 'handle_vote',
            post_id: form.find('input[name="post_id"]').val(),
            nonce: form.find('input[name="nonce"]').val()
        };

        sendVoteRequest(data);
    });

    // Платное голосование
    $('.btn-vote-paid').on('click', function() {
        const form = $(this).closest('form');
        const data = {
            action: 'handle_paid_vote',
            post_id: form.find('input[name="post_id"]').val(),
            nonce: form.find('input[name="paid_nonce"]').val()
        };

        sendVoteRequest(data);
    });

    function sendVoteRequest(data) {
        $.ajax({
            url: voting_ajax.ajax_url,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    $('.vote-count').text(response.data.votes);
                    showSuccessAlert(response.data.message);
                } else {
                    showErrorAlert(response.data.message);
                }
            },
            error: function() {
                showErrorAlert('Connection error');
            }
        });
    }

    function showSuccessAlert(message) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 6000
        });
    }

    function showErrorAlert(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 6000
        });
    }
});