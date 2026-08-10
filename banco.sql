CREATE DATABASE IF NOT EXISTS agendamento_veterinario
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE agendamento_veterinario;

-- =========================================
-- USUÁRIOS DO SISTEMA
-- =========================================

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- =========================================
-- CONFIGURAÇÃO DOS HORÁRIOS DE ATENDIMENTO
-- =========================================

CREATE TABLE horarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dia_semana TINYINT UNSIGNED NOT NULL,
    horario TIME NOT NULL,
    ativo TINYINT(1) DEFAULT 1,

    UNIQUE KEY unico_horario (dia_semana, horario)
) ENGINE=InnoDB;


-- =========================================
-- AGENDAMENTOS
-- =========================================

CREATE TABLE agendamentos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nome_tutor VARCHAR(150) NOT NULL,
    telefone VARCHAR(30) NOT NULL,
    email VARCHAR(150) NULL,

    nome_animal VARCHAR(100) NOT NULL,
    especie VARCHAR(50) NOT NULL,

    motivo TEXT NOT NULL,

    data_consulta DATE NOT NULL,
    horario_consulta TIME NOT NULL,

    status ENUM(
        'pendente',
        'aprovado',
        'recusado',
        'reagendamento',
        'cancelado'
    ) DEFAULT 'pendente',

    nova_data DATE NULL,
    novo_horario TIME NULL,

    observacao TEXT NULL,

    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_data_horario (data_consulta, horario_consulta),
    INDEX idx_status (status)
) ENGINE=InnoDB;


-- =========================================
-- BLOQUEIOS DA AGENDA
-- =========================================

CREATE TABLE bloqueios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    data_bloqueio DATE NOT NULL,

    horario_inicio TIME NULL,
    horario_fim TIME NULL,

    dia_inteiro TINYINT(1) DEFAULT 0,

    motivo VARCHAR(255) NULL,

    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_data_bloqueio (data_bloqueio)
) ENGINE=InnoDB;