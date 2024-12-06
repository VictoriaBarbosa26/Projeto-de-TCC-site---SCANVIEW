document.addEventListener('DOMContentLoaded', function() {
    // Event listener para o botão de enviar
    document.getElementById('enviar-btn').addEventListener('click', function() {
        // Aqui você pode adicionar a lógica para enviar os dados, por exemplo:
        // enviarDados();
        
        // Após enviar os dados com sucesso, chame a função para exibir o gráfico
        exibirGrafico();
    });
});

function exibirGrafico() {
    // Dados de exemplo para o gráfico
    var meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    var dados = {
        'Item 1': [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60],  // Dados para o Item 1 (exemplo)
        'Item 2': [10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65], // Dados para o Item 2 (exemplo)
        'Item 3': [15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70]  // Dados para o Item 3 (exemplo)
    };

    // Configuração dos dados para o gráfico
    var datasets = [];
    var cores = ['#FF6384', '#36A2EB', '#FFCE56']; // Cores para os top 3 itens (exemplo)

    Object.keys(dados).forEach(function(item, index) {
        var dataset = {
            label: item,
            data: dados[item],
            borderColor: cores[index],
            fill: false
        };
        datasets.push(dataset);
    });

    // Configuração do gráfico
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: meses,
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    // Exibir legenda com os top 3 itens
    var legenda = document.getElementById('legenda');
    legenda.innerHTML = '';
    Object.keys(dados).slice(0, 3).forEach(function(item, index) {
        var cor = cores[index];
        legenda.innerHTML += '<span style="color:' + cor + '">' + item + '</span>';
    });

    // Exibir o container do gráfico
    document.getElementById('chart-container').style.display = 'block';
}
