-- Criação do banco de dados
CREATE DATABASE ScanView;
USE ScanView;


-- Criação da tabela Alunos
CREATE TABLE Alunos (
    ID_Aluno INT AUTO_INCREMENT PRIMARY KEY,
    Nome_Completo VARCHAR(200) NOT NULL,
    Email VARCHAR(50) UNIQUE NOT NULL,
    Senha VARCHAR(255) NOT NULL,
    Data_Nascimento DATE,
    Serie VARCHAR(10),
    Curso VARCHAR(20),
    Celular VARCHAR(15),
    RM INT UNIQUE
);


-- Criação da tabela Administradores
CREATE TABLE Administradores (
    ID_Adm INT AUTO_INCREMENT PRIMARY KEY,
    Nome_Completo VARCHAR(200) NOT NULL,
    Email VARCHAR(50) UNIQUE NOT NULL,
    Senha VARCHAR(255) NOT NULL,
    Numero_Identificacao INT UNIQUE NOT NULL
);


-- Criação da tabela Professor
CREATE TABLE Professor (
    ID_Prof INT AUTO_INCREMENT PRIMARY KEY,
    Nome_Completo VARCHAR(200) NOT NULL,
    Email VARCHAR(50) UNIQUE NOT NULL,
    Senha VARCHAR(255) NOT NULL,
    Disciplina VARCHAR(255),
    Numero_Identificacao INT UNIQUE NOT NULL
);


-- Criação da tabela Identificacao_Pendentes
CREATE TABLE Identificacao_Pendentes (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Numero_Identificacao INT UNIQUE NOT NULL,
    Cargo ENUM('adm', 'prof') NOT NULL
);


-- Inserção de dados na tabela Identificacao_Pendentes
INSERT INTO Identificacao_Pendentes (Numero_Identificacao, Cargo) VALUES
(123456, 'adm'),
(252561, 'adm'),
(654321, 'prof'),
(789012, 'prof');


-- Criação da tabela Computadores
CREATE TABLE Computadores (
    ID_Computador INT AUTO_INCREMENT PRIMARY KEY,
    Laboratorio VARCHAR(50),
    Disciplina VARCHAR(255),
    Computador VARCHAR(100) NOT NULL,
    ID_Aluno INT,
    ID_Professor INT,
    Data_Hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Aluno) REFERENCES Alunos(ID_Aluno) ON DELETE SET NULL,
    FOREIGN KEY (ID_Professor) REFERENCES Professor(ID_Prof) ON DELETE SET NULL
);


-- Criação da tabela Computadores_Professores
CREATE TABLE Computadores_Professores (
    ID_Computador INT AUTO_INCREMENT PRIMARY KEY,
    Laboratorio VARCHAR(50),
    Disciplina VARCHAR(255),
    Computador VARCHAR(100) NOT NULL,
    ID_Aluno INT,
    ID_Professor INT,
    Data_Hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Aluno) REFERENCES Alunos(ID_Aluno) ON DELETE SET NULL,
    FOREIGN KEY (ID_Professor) REFERENCES Professor(ID_Prof) ON DELETE SET NULL
);


-- Criação da tabela horarios_adm
CREATE TABLE horarios_adm (
    id INT AUTO_INCREMENT PRIMARY KEY,
    adm_id INT NOT NULL,
    data DATE NOT NULL,
    horario_inicio TIME NOT NULL,
    horario_fim TIME NOT NULL,
    FOREIGN KEY (adm_id) REFERENCES Administradores(ID_Adm) ON DELETE CASCADE
);


-- Criação da tabela Problemas
CREATE TABLE Problemas (
    ID_Problema INT AUTO_INCREMENT PRIMARY KEY,
    ID_Computador INT,
    Descricao VARCHAR(255) NOT NULL,
    Data_Registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Resolvido BOOLEAN DEFAULT FALSE,
    Nome_Aluno VARCHAR(255),
    FOREIGN KEY (ID_Computador) REFERENCES Computadores(ID_Computador) ON DELETE CASCADE
);


-- Criação da tabela Registro_Aulas
CREATE TABLE Registro_Aulas (
    ID_Aula INT AUTO_INCREMENT PRIMARY KEY,
    Disciplina VARCHAR(100) NOT NULL,
    Data DATE NOT NULL,
    Lab VARCHAR(50) NOT NULL,
    Horario TIME NOT NULL,
    Curso VARCHAR(30) NOT NULL
);


-- Criação da tabela Solução
CREATE TABLE Solucoes (
    ID_Solucao INT AUTO_INCREMENT PRIMARY KEY,
    ID_Problema INT NOT NULL,
    Descricao TEXT NOT NULL,
    DataResolucao DATE NOT NULL,
    FOREIGN KEY (ID_Problema) REFERENCES Problemas(ID_Problema)
);


-- Criação da tabela mensagens
CREATE TABLE mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    remetente VARCHAR(255) NOT NULL,  
    destinatario VARCHAR(255) NOT NULL,  
    assunto VARCHAR(255) NOT NULL,      
    conteudo TEXT NOT NULL,              
    status ENUM('lida', 'não lida') DEFAULT 'não lida',  
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_resposta INT DEFAULT NULL
);


-- Criação da tabela notificações
CREATE TABLE notificacao (
    ID_Noti INT AUTO_INCREMENT PRIMARY KEY, 
    remetente VARCHAR(255) NOT NULL,  
    destinatario VARCHAR(255) NOT NULL,  
    assunto VARCHAR(255) NOT NULL,    
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criação da tabela agendamentos
CREATE TABLE agendamentos (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    laboratorio VARCHAR(255) NOT NULL,
    data DATE NOT NULL,
    hora TIME NOT NULL,
    id_administrador INT NOT NULL, 
    ID_Computador INT, 
    status ENUM('pendente', 'enviado') DEFAULT 'pendente', 
    FOREIGN KEY (id_administrador) REFERENCES Administradores(ID_Adm) ON DELETE CASCADE, 
    FOREIGN KEY (ID_Computador) REFERENCES Computadores(ID_Computador) ON DELETE CASCADE
);

-- Criação da tabela respostas
CREATE TABLE respostas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mensagem_id INT NOT NULL,
    aluno_id INT,
	adm_id INT,
	prof_id INT,
    resposta TEXT NOT NULL,
    data_resposta DATETIME NOT NULL,
    FOREIGN KEY (mensagem_id) REFERENCES mensagens(id),
    FOREIGN KEY (aluno_id) REFERENCES Alunos(ID_Aluno),
	FOREIGN KEY (adm_id) REFERENCES Administradores(ID_Adm),
	FOREIGN KEY (prof_id) REFERENCES Professor (ID_Prof)
);