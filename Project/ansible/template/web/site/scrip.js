document.addEventListener("DOMContentLoaded", function () {
    console.log("Сайт успешно загружен!");

    // 1. Реакция на добавление строки
    const addForm = document.querySelector('form[method="POST"]');
    if (addForm) {
        addForm.addEventListener("submit", function (event) {
            alert("Новая запись успешно добавлена!");
        });
    }

    // 2. Реакция на удаление строки
    const deleteLinks = document.querySelectorAll('a[href^="?delete="]');
    deleteLinks.forEach(link => {
        link.addEventListener("click", function (event) {
            const confirmDelete = confirm("Вы уверены, что хотите удалить эту запись?");
            if (!confirmDelete) {
                event.preventDefault(); // Отменяем действие по умолчанию (удаление)
            } else {
                alert("Запись успешно удалена!");
            }
        });
    });

    // 3. Реакция на изменение строки (если есть форма редактирования)
    const editForms = document.querySelectorAll('form[method="POST"][action*="edit"]');
    editForms.forEach(form => {
        form.addEventListener("submit", function (event) {
            alert("Запись успешно изменена!");
        });
    });

    // 4. Динамическое обновление таблицы после AJAX-запросов (опционально)
    function refreshTable() {
        const table = document.querySelector("table");
        if (table) {
            console.log("Таблица обновлена!");
        }
    }

    // Пример вызова refreshTable после AJAX-запроса (если используется)
    // fetch('/update-table')
    //     .then(response => response.json())
    //     .then(data => {
    //         refreshTable();
    //     });
});
