loadTodos();

document.getElementById('todoForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const input = document.getElementById('todoInput');
    const title = input.value.trim();
    const update = document.getElementById('adicionar');

    if (update.textContent === 'Atualizar') {
        // Atualizar tarefa existente
        const id = update.getAttribute('data-id');
        fetch('dashboard.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=update&id=${id}&title=${encodeURIComponent(title)}`
        }).then(() => {
            update.textContent = 'Adicionar';
            update.removeAttribute('data-id');
            input.value = '';
            loadTodos();
        }).catch(error => console.error('Erro ao atualizar todo:', error));
    } else {
        // Adicionar nova tarefa
        fetch('dashboard.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=add&title=${encodeURIComponent(title)}`
        }).then(() => {
            input.value = '';
            loadTodos();
        }).catch(error => console.error('Erro ao adicionar todo:', error));
    }

});

function addTodo(title) {

    fetch('dashboard.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add&title=${encodeURIComponent(title)}`
    }).catch(error => console.error('Erro ao adicionar todo:', error));
}

function loadTodos() {
    fetch('dashboard.php?action=list')
    .then(response => response.json())
    .then(todos => {
        const todoList = document.getElementById('todoList');
        todoList.innerHTML = '';

        todos.forEach(todo => {
            const todoItem = createTodoElement(todo);
            todoList.appendChild(todoItem);
        });
    })
    .catch(error => console.error('Erro ao carregar todos:', error));
}

function createTodoElement(todo) {
    const div = document.createElement('div');
    div.className = `todo-item ${todo.completed ? 'completed' : ''}`;
    
    div.innerHTML = `
        <input type="checkbox" ${todo.completed ? 'checked' : ''} 
            onchange="toggleTodo(${todo.id}, this.checked)">
        <span id=${todo.id}>${todo.title}</span>
        <div class="actions">
            <button onclick="updateTodo(${todo.id})" style="background-color:#f0ad4e">Editar</button>
            <button onclick="deleteTodo(${todo.id})">Excluir</button>
        </div>
    `;


    return div;
}

async function updateTodo(id) {

    const update = document.getElementById('adicionar');
    const input = document.getElementById('todoInput');

    update.textContent = 'Atualizar';
    update.setAttribute('data-id', id);
    input.value = document.getElementById(id).textContent;
};

async function toggleTodo(id, completed) {
    try {
        await fetch('dashboard.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=toggle&id=${id}&completed=${completed ? 1 : 0}`
        });
        loadTodos();
    } catch (error) {
        console.error('Erro ao atualizar todo:', error);
    }
}

async function deleteTodo(id) {
    if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
        try {
            await fetch('dashboard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&id=${id}`
            });
            loadTodos();
        } catch (error) {
            console.error('Erro ao deletar todo:', error);
        }
    }
}

function logout() {
    fetch('logout.php').then(() => {
        window.location.href = 'index.html';
    });
}
