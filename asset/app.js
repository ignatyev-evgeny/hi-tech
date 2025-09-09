mask("input");

/** Обработка формы */
document.getElementById('validationForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const resultDiv = document.getElementById('result');
    const loadingDiv = document.getElementById('loading');
    const submitBtn = form.querySelector('.submit-btn');

    /** Скрываем предыдущий результат */
    resultDiv.style.display = 'none';
    loadingDiv.style.display = 'block';
    submitBtn.disabled = true;

    /** AJAX запрос */
    fetch('/action/validateForm.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.error?.message || `${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            loadingDiv.style.display = 'none';
            submitBtn.disabled = false;
            showResult(data);
        })
        .catch(error => {
            loadingDiv.style.display = 'none';
            submitBtn.disabled = false;
            showError(error.message);
        });
});

/** Показ результата */
function showResult(response) {
    const resultDiv = document.getElementById('result');

    if (response.status && response.data) {
        const data = response.data;
        const phoneData = data.libphonenumber;

        const isValid = phoneData.valid;
        const resultClass = isValid ? 'success' : 'error';
        const icon = isValid ? '✓' : '✕';
        const title = isValid ? 'Номер валиден' : 'Номер не валиден';

        resultDiv.innerHTML = `
                    <div class="result-header">
                        <div class="result-icon">${icon}</div>
                        <div class="result-title">${title}</div>
                    </div>
                    <div class="result-grid">
                        <div class="result-item">
                            <div class="result-item-label">Введённый номер</div>
                            <div class="result-item-value">${data.input}</div>
                        </div>
                        <div class="result-item">
                            <div class="result-item-label">Регион</div>
                            <div class="result-item-value">${phoneData.region}</div>
                        </div>
                    </div>
                `;

        resultDiv.className = `result-card ${resultClass}`;
        resultDiv.style.display = 'block';
    } else {
        showError('Неверный формат ответа сервера');
    }
}

/** Показ ошибки */
function showError(message) {
    const resultDiv = document.getElementById('result');

    resultDiv.innerHTML = `
                <div class="result-header">
                    <div class="result-icon">⚠</div>
                    <div class="result-title">Ошибка</div>
                </div>
                <p style="margin-top: 15px; color: #666;">${message}</p>
            `;

    resultDiv.className = 'result-card error';
    resultDiv.style.display = 'block';
}

