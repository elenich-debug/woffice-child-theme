document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.custom-update-btn');
    const spinner = document.getElementById('custom-loading-spinner');
    const overlay = document.getElementById('overlay');

    buttons.forEach(button => {
        button.addEventListener('click', async function (e) {
            e.preventDefault();
            const postId = this.dataset.postId;

            if (!postId) {
                console.error('Ошибка: отсутствует postId');
                alert('Ошибка: отсутствует идентификатор поста.');
                return;
            }

            // Показать затемнение, спиннер и увеличить кнопку
            if (overlay) overlay.style.display = 'block';
            if (spinner) spinner.style.display = 'inline-block';
            button.classList.add('processing');

            try {
                const response = await fetch('/wp-admin/admin-ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_post_content&post_id=${encodeURIComponent(postId)}`
                });

                if (!response.ok) {
                    throw new Error(`HTTP ошибка: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    location.reload();
                } else {
                    console.error('Ошибка:', data.data);
                    alert('Ошибка: ' + data.data);
                }
            } catch (error) {
                console.error('Ошибка запроса:', error);
                alert('Ошибка запроса: ' + error.message);
            } finally {
                // Скрыть затемнение, спиннер и вернуть кнопку в исходное состояние
                if (overlay) overlay.style.display = 'none';
                if (spinner) spinner.style.display = 'none';
                button.classList.remove('processing');
            }
        });
    });
});

