CREATE DATABASE IF NOT EXISTS bd_mundo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bd_mundo;

CREATE TABLE continentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    populacao BIGINT,
    area_km2 DECIMAL(12,2)
);

CREATE TABLE governantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    partido_politico VARCHAR(100),
    data_nascimento DATE,
    data_inicio_mandato DATE,
    data_fim_mandato DATE
);

CREATE TABLE paises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    id_continente INT,
    populacao BIGINT,
    area_km2 DECIMAL(12,2),
    idioma VARCHAR(100),
    id_governante INT,
    clima VARCHAR(50),
    regime_politico VARCHAR(100),
    moeda VARCHAR(50),
    FOREIGN KEY (id_continente) REFERENCES continentes(id) ON DELETE RESTRICT,
    FOREIGN KEY (id_governante) REFERENCES governantes(id) ON DELETE SET NULL
);

CREATE TABLE cidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    id_pais INT NOT NULL,
    populacao BIGINT,
    area_km2 DECIMAL(12,2),
    clima VARCHAR(50),
    id_governante INT,
    data_fundacao DATE,
    FOREIGN KEY (id_pais) REFERENCES paises(id) ON DELETE CASCADE,
    FOREIGN KEY (id_governante) REFERENCES governantes(id) ON DELETE SET NULL
);

INSERT INTO continentes (nome, populacao, area_km2)
VALUES
('América do Sul', 430000000, 17840000.00),
('Europa', 746000000, 10180000.00),
('América do Norte', 592000000, 24709000.00),
('Ásia', 4700000000, 44579000.00),
('África', 1400000000, 30370000.00);

INSERT INTO governantes (nome, partido_politico, data_nascimento, data_inicio_mandato, data_fim_mandato)
VALUES
('Luiz Inácio Lula da Silva', 'Partido dos Trabalhadores', '1945-10-27', '2023-01-01', '2026-12-31'),
('Emmanuel Macron', 'Renaissance', '1977-12-21', '2017-05-14', '2027-05-14'),
('Joe Biden', 'Partido Democrata', '1942-11-20', '2021-01-20', '2025-01-20'),
('Fumio Kishida', 'Partido Liberal Democrata', '1957-07-29', '2021-10-04', NULL),
('Eduardo Paes', 'Partido Social Democrático', '1969-11-14', '2021-01-01', '2024-12-31');

INSERT INTO paises (nome, id_continente, populacao, area_km2, idioma, id_governante, clima, regime_politico, moeda)
VALUES
('Brasil', 1, 214000000, 8515767.00, 'Português', 1, 'Tropical', 'República Presidencialista', 'Real'),
('França', 2, 68000000, 551695.00, 'Francês', 2, 'Temperado', 'República Semipresidencialista', 'Euro'),
('Estados Unidos', 3, 333000000, 9833517.00, 'Inglês', 3, 'Variado', 'República Federal Presidencialista', 'Dólar Americano'),
('Japão', 4, 125000000, 377975.00, 'Japonês', 4, 'Temperado', 'Monarquia Constitucional Parlamentar', 'Iene'),
('Egito', 5, 109000000, 1002450.00, 'Árabe', NULL, 'Desértico', 'República Semipresidencialista', 'Libra Egípcia');

INSERT INTO cidades (nome, id_pais, populacao, area_km2, clima, id_governante, data_fundacao) 
VALUES
('Rio de Janeiro', 1, 6748000, 1200.25, 'Tropical', 5, '1565-03-01'),
('Paris', 2, 2148000, 105.40, 'Temperado', NULL, '0250-01-01'),
('Nova York', 3, 8336000, 783.80, 'Temperado', NULL, '1624-02-02'),
('Tóquio', 4, 13960000, 2194.00, 'Temperado', NULL, '1457-05-01'),
('Cairo', 5, 9600000, 3085.12, 'Desértico', NULL, '0969-07-06');

USE bd_mundo;

CREATE TABLE IF NOT EXISTS USUARIOS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('usuario', 'administrador') NOT NULL DEFAULT 'usuario',
    tentativas_login TINYINT UNSIGNED NOT NULL DEFAULT 0,
    bloqueado_ate DATETIME NULL,
    primeiro_acesso TINYINT(1) NOT NULL DEFAULT 1,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS LOGS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    acao VARCHAR(100) NOT NULL,
    descricao VARCHAR(255) NULL,
    ip VARCHAR(45) NULL,
    data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_logs_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES USUARIOS(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/*
|--------------------------------------------------------------------------
| Administrador inicial
|--------------------------------------------------------------------------
|
| E-mail:
| admin@admin.com
|
| Senha temporária:
| Admin@123
|
*/

INSERT IGNORE INTO USUARIOS
(
    nome,
    email,
    senha,
    tipo,
    primeiro_acesso
)

VALUES
(
    'Administrador',
    'admin@admin.com',
    '$2y$12$/R5mO/ZrcI/IjlLsoERWlOVYrwSe7LFgMsWwKoNUlvIBdK2/Dtj.m',
    'administrador',
    1
);

