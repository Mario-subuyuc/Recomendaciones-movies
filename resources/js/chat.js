const form = document.getElementById('chat-form');
if (form) {
    const question = document.getElementById('chat-question');
    const category = document.getElementById('chat-category');
    const button = document.getElementById('chat-submit');
    const messages = document.getElementById('chat-messages');
    const error = document.getElementById('chat-error');
    const progress = document.getElementById('chat-progress');
    let pending = false;
    function addMessage(label, text, type) {
        document.getElementById('chat-empty')?.remove();
        const article = document.createElement('article');
        article.className = `chat-message chat-message-${type}`;
        const heading = document.createElement('strong');
        heading.textContent = label;
        const content = document.createElement('p');
        content.textContent = text; // Texto plano: nunca innerHTML ni Markdown sin sanitizar.
        article.append(heading, content);
        messages.append(article);
        messages.scrollTop = messages.scrollHeight;
    }
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (pending || !question.value.trim()) return;
        pending = true;
        button.disabled = question.disabled = category.disabled = true;
        button.textContent = 'Procesando…';
        progress.hidden = false;
        error.hidden = true;
        const text = question.value.trim();
        try {
            const response = await fetch(form.action, {
                method: 'POST', credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value },
                body: JSON.stringify({ pregunta: text, categoria: category.value }),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                const fallback = response.status === 419 || response.status === 401 ? 'Tu sesión venció. Recarga la página e inicia sesión.' : response.status === 403 ? 'No tienes permiso para consultar esta categoría.' : 'No pudimos completar la consulta. Inténtalo de nuevo.';
                throw new Error(Object.values(data.errors || {}).flat()[0] || (response.status < 500 || response.status === 503 ? data.message : null) || fallback);
            }
            if (typeof data.respuesta !== 'string') throw new Error('La respuesta no es válida. Inténtalo de nuevo.');
            addMessage(`Tú · ${category.options[category.selectedIndex].text}`, text, 'user');
            addMessage('Asistente', data.respuesta, 'assistant');
            question.value = '';
        } catch (failure) {
            error.textContent = failure instanceof TypeError ? 'No pudimos conectarnos. Comprueba tu conexión antes de intentar otra consulta.' : failure.message;
            error.hidden = false;
        } finally {
            pending = false;
            button.disabled = question.disabled = category.disabled = false;
            button.textContent = 'Enviar pregunta';
            progress.hidden = true;
            question.focus();
        }
    });
}
