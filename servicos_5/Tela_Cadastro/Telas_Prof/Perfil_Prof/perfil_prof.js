/* Escuta o evento de carregamento do DOM */
document.addEventListener('DOMContentLoaded', function() {
    /* Seleciona o elemento dropdown */
    var dropdown = document.querySelector('.dropdown');
    
    /* Adiciona um evento de clique ao dropdown */
    dropdown.addEventListener('click', function(event) {
        event.stopPropagation(); // Impede que o evento de clique se propague para os elementos pai
        this.classList.toggle('active'); // Alterna a classe 'active' no dropdown
    });

    /* Adiciona um evento de clique à janela */
    window.addEventListener('click', function(event) {
        /* Verifica se o clique não foi dentro do dropdown */
        if (!event.target.closest('.dropdown')) {
            dropdown.classList.remove('active'); // Fecha o dropdown removendo a classe 'active'
        }
    });

    /* Adiciona um evento de clique para cada link dentro do dropdown */
    var dropdownLinks = document.querySelectorAll('.dropdown-content a');
    dropdownLinks.forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault(); // Impede o comportamento padrão do link, que é navegar para o URL especificado no atributo 'href'
            
            var destination = this.getAttribute('href'); // Obtém o destino do link (se refere aos elementos '<a>')
            window.location.href = destination; // Redireciona para a página correspondente
        });
    });
});






// perfil_Aluno.js

document.addEventListener('DOMContentLoaded', () => {
    const salvarButton = document.getElementById('salvar-button');

    salvarButton.addEventListener('click', () => {
        // Coletar os dados dos campos editáveis
        const nome = document.getElementById('nome').innerText.trim();
        const email = document.getElementById('email').innerText.trim();
        const senha = document.getElementById('senha').innerText.trim();

        // Validar os dados (opcional, mas recomendado)
        if (!nome || !email || !senha) {
            alert('Todos os campos devem ser preenchidos.');
            return;
        }

        // Opcional: Validar o e-mail
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Por favor, insira um e-mail válido.');
            return;
        }

        // Preparar os dados para envio
        const dados = { nome, email, senha };

        // Enviar os dados ao servidor via fetch
        fetch('save_profile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dados)
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Exibir a resposta do servidor
            // Opcional: Atualizar a página ou realizar outras ações
            // location.reload(); // Por exemplo, recarregar a página
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Ocorreu um erro ao salvar as modificações.');
        });
    });
});
