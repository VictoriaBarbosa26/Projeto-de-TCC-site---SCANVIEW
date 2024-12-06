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














document.getElementById("toggleFlexboxButton").addEventListener("click", function() {
    var flexbox = document.getElementById("flexbox");
    flexbox.style.display = flexbox.style.display === "none" || flexbox.style.display === "" ? "flex" : "none";
});

document.getElementById("cancelButton").addEventListener("click", function() {
    document.getElementById("flexbox").style.display = "none"; // Oculta o flexbox
});

document.getElementById("sendButton").addEventListener("click", function() {
    var problemas = Array.from(document.querySelectorAll('input[name="problemas[]"]:checked'))
                         .map(checkbox => checkbox.value);
    var description = document.getElementById("problemDescription").value;
    var dataResolucao = new Date().toISOString().slice(0, 10); // Captura a data atual

    // Verifica se existem problemas selecionados
    if (problemas.length === 0) {
        alert("Por favor, selecione pelo menos um problema.");
        return;
    }

    if (!description) {
        alert("Por favor, descreva o problema.");
        return;
    }

    // Envia os dados usando um fetch
    const formData = new FormData();
    formData.append('problemas', JSON.stringify(problemas));
    formData.append('descricao_solucao', description);
    formData.append('data_solucao', dataResolucao); // Adiciona a data

    fetch('resolver_problemas.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(idsResolvidos => {
        // Remove os problemas da tela
        idsResolvidos.forEach(id => {
            var checkbox = document.getElementById("problem" + id);
            if (checkbox) {
                checkbox.parentElement.remove(); // Remove o elemento do DOM
            }
        });
        document.getElementById("flexbox").style.display = "none"; // Oculta o flexbox após o envio
        
        // Redireciona para a tela de visualização de problemas
        window.location.replace("enviado/enviado.php");

    })
    .catch(error => console.error('Erro:', error));
});



