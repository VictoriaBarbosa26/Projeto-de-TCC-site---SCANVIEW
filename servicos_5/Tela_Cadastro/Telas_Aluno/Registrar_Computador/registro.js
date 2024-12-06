// ESCUTA O EVENTO DE CARREGAMENTO DO DOM
document.addEventListener('DOMContentLoaded', function() {
  // SELECIONA O ELEMENTO DROPDOWN
  var dropdown = document.querySelector('.dropdown');
  // ADICIONA UM EVENTO DE CLIQUE AO DROPDOWN
  dropdown.addEventListener('click', function(event) {
      event.stopPropagation(); // IMPEDE QUE O EVENTO DE CLIQUE SE PROPAGUE PARA OS ELEMENTOS PAI
      this.classList.toggle('active'); // ALTERNA A CLASSE 'ACTIVE' NO DROPDOWN
  });

  // ADICIONA UM EVENTO DE CLIQUE À JANELA
  window.addEventListener('click', function(event) {
      // VERIFICA SE O CLIQUE NÃO FOI DENTRO DO DROPDOWN
      if (!event.target.closest('.dropdown')) {
          dropdown.classList.remove('active'); // FECHA O DROPDOWN REMOVENDO A CLASSE 'ACTIVE'
      }
  });

  // ADICIONA UM EVENTO DE CLIQUE PARA CADA LINK DENTRO DO DROPDOWN
  var dropdownLinks = document.querySelectorAll('.dropdown-content a');
  dropdownLinks.forEach(function(link) {
      link.addEventListener('click', function(event) {
          event.preventDefault(); // IMPEDE O COMPORTAMENTO PADRÃO DO LINK, QUE É NAVEGAR PARA O URL ESPECIFICADO NO ATRIBUTO 'HREF'

          var destination = this.getAttribute('href'); // OBTÉM O DESTINO DO LINK (SE REFERE AOS ELEMENTOS '<a>')
          window.location.href = destination; // REDIRECIONA PARA A PÁGINA CORRESPONDENTE
      });
  });
});

// Captura o botão de lixeira
var lixeiraBtn = document.querySelector('.lixeira-icon');

// Captura o modal
var modal = document.getElementById("myModal");

// Captura o botão de fechar do modal
var span = document.getElementsByClassName("close")[0];

// Captura os botões dentro do modal
var confirmBtn = document.getElementById("confirmClear");
var cancelBtn = document.getElementById("cancelClear");

// Adiciona um evento de clique ao botão de lixeira
lixeiraBtn.addEventListener('click', function() {
  // Exibe o modal
  modal.style.display = "block";
});

// Adiciona um evento de clique ao botão de fechar do modal
span.addEventListener('click', function() {
  // Fecha o modal ao clicar no botão de fechar (×)
  modal.style.display = "none";
});

// Adiciona um evento de clique ao botão "Cancelar"
cancelBtn.addEventListener('click', function(event) {
  // Fecha o modal ao clicar no botão "Cancelar"
  modal.style.display = "none";
  // Evita que o formulário seja enviado
  event.preventDefault();
});

// Adiciona um evento de clique ao botão "Sim, Limpar"
confirmBtn.addEventListener('click', function(event) {
  // Limpa o formulário
  document.querySelector('form').reset();
  // Fecha o modal após limpar o formulário
  modal.style.display = "none";
  // Evita que o formulário seja enviado
  event.preventDefault();
});

// Fecha o modal se o usuário clicar fora dele
window.addEventListener('click', function(event) {
  if (event.target == modal) {
      modal.style.display = "none";
  }
});