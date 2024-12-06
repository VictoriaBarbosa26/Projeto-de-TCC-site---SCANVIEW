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