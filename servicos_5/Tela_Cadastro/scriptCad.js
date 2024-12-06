document.getElementById("cargo").addEventListener("change", function() {
    var cargo = this.value;
    var rmContainer = document.getElementById("rm-container");
    var numeroIdentificacaoContainer = document.getElementById("numero-identificacao-container");

    if (cargo === "aluno") {
        rmContainer.style.display = "block";
        numeroIdentificacaoContainer.style.display = "none";
    } else if (cargo === "adm" || cargo === "prof") {
        rmContainer.style.display = "none";
        numeroIdentificacaoContainer.style.display = "block";
    } else {
        rmContainer.style.display = "none";
        numeroIdentificacaoContainer.style.display = "none";
    }
});

document.getElementById("cadastrar-btn").addEventListener("click", function() {
    document.getElementById("notification").style.display = "block";
});

document.getElementById("accept-checkbox").addEventListener("change", function() {
    document.getElementById("accept-button").disabled = !this.checked;
});

document.getElementById("accept-button").addEventListener("click", function() {
    document.getElementById("cadastro-form").submit();
});

document.addEventListener("DOMContentLoaded", function() {
    const cargoSelect = document.getElementById("cargo");
    const rmContainer = document.getElementById("rm-container");
    const numeroIdentificacaoContainer = document.getElementById("numero-identificacao-container");
    const professorContainer = document.getElementById("professor-container");

    cargoSelect.addEventListener("change", function() {
        const cargo = cargoSelect.value;
        if (cargo === "aluno") {
            rmContainer.style.display = "block";
            numeroIdentificacaoContainer.style.display = "none";
            professorContainer.style.display = "block";
        } else if (cargo === "adm" || cargo === "prof") {
            rmContainer.style.display = "none";
            numeroIdentificacaoContainer.style.display = "block";
            professorContainer.style.display = "none";
        } else {
            rmContainer.style.display = "none";
            numeroIdentificacaoContainer.style.display = "none";
            professorContainer.style.display = "none";
        }
    });
});




document.getElementById("cadastrar-btn").addEventListener("click", function(event) {
    event.preventDefault();
    document.getElementById("notification").style.display = "block";
});

document.getElementById("accept-checkbox").addEventListener("change", function() {
    document.getElementById("accept-button").disabled = !this.checked;
});

document.getElementById("accept-button").addEventListener("click", function() {
    document.getElementById("notification").style.display = "none";
    document.getElementById("cadastro-form").submit();
});

function closeNotification() {
    document.getElementById("notification").style.display = "none";
}

document.addEventListener("DOMContentLoaded", function() {
    const cargoSelect = document.getElementById("cargo");
    const rmContainer = document.getElementById("rm-container");
    const numeroIdentificacaoContainer = document.getElementById("numero-identificacao-container");
    const professorContainer = document.getElementById("professor-container");

    cargoSelect.addEventListener("change", function() {
        const cargo = cargoSelect.value;
        if (cargo === "aluno") {
            rmContainer.style.display = "block";
            numeroIdentificacaoContainer.style.display = "none";
            professorContainer.style.display = "block";
        } else if (cargo === "adm" || cargo === "prof") {
            rmContainer.style.display = "none";
            numeroIdentificacaoContainer.style.display = "block";
            professorContainer.style.display = "none";
        } else {
            rmContainer.style.display = "none";
            numeroIdentificacaoContainer.style.display = "none";
            professorContainer.style.display = "none";
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const cargoSelect = document.getElementById('cargo');
    const cursoContainer = document.getElementById('curso-container');
    const laboratorioContainer = document.getElementById('laboratorio-container');
    const rmContainer = document.getElementById('rm-container');
    const numeroIdentificacaoContainer = document.getElementById('numero-identificacao-container');

    cargoSelect.addEventListener('change', function() {
        const cargo = this.value;

        // Mostrar/ocultar campos com base no cargo selecionado
        if (cargo === 'aluno') {
            rmContainer.style.display = 'block';
            numeroIdentificacaoContainer.style.display = 'none';
            cursoContainer.style.display = 'block';
            laboratorioContainer.style.display = 'none';
        } else if (cargo === 'adm') {
            rmContainer.style.display = 'none';
            numeroIdentificacaoContainer.style.display = 'block';
            cursoContainer.style.display = 'none';
            laboratorioContainer.style.display = 'block';
        } else if (cargo === 'prof') {
            rmContainer.style.display = 'none';
            numeroIdentificacaoContainer.style.display = 'block';
            cursoContainer.style.display = 'none';
            laboratorioContainer.style.display = 'none';
        } else {
            rmContainer.style.display = 'none';
            numeroIdentificacaoContainer.style.display = 'none';
            cursoContainer.style.display = 'none';
            laboratorioContainer.style.display = 'none';
        }
    });
});