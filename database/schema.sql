CREATE DATABASE IF NOT EXISTS rotaflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rotaflow;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS frete_produto;
DROP TABLE IF EXISTS frete;
DROP TABLE IF EXISTS produto;
DROP TABLE IF EXISTS endereco;
DROP TABLE IF EXISTS transportador;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE produto (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    descricao VARCHAR(255) NULL,
    peso DECIMAL(10,3) NOT NULL,
    valor DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT chk_produto_peso CHECK (peso > 0),
    CONSTRAINT chk_produto_valor CHECK (valor >= 0)
) ENGINE=InnoDB;

CREATE TABLE endereco (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    identificacao VARCHAR(150) NOT NULL,
    cep CHAR(8) NOT NULL,
    logradouro VARCHAR(180) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    complemento VARCHAR(100) NULL,
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado CHAR(2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_endereco_cidade (cidade),
    CONSTRAINT chk_endereco_estado CHECK (CHAR_LENGTH(estado) = 2)
) ENGINE=InnoDB;

CREATE TABLE transportador (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    documento VARCHAR(20) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(150) NULL,
    placa_veiculo VARCHAR(8) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_transportador_documento (documento)
) ENGINE=InnoDB;

CREATE TABLE frete (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    origem_id INT UNSIGNED NOT NULL,
    destino_id INT UNSIGNED NOT NULL,
    transportador_id INT UNSIGNED NOT NULL,
    data_frete DATE NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'Em aberto',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_frete_data (data_frete),
    CONSTRAINT fk_frete_origem FOREIGN KEY (origem_id) REFERENCES endereco (id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_frete_destino FOREIGN KEY (destino_id) REFERENCES endereco (id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_frete_transportador FOREIGN KEY (transportador_id) REFERENCES transportador (id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_frete_status CHECK (status IN ('Em aberto', 'Em trânsito', 'Concluído', 'Cancelado'))
) ENGINE=InnoDB;

CREATE TABLE frete_produto (
    frete_id INT UNSIGNED NOT NULL,
    produto_id INT UNSIGNED NOT NULL,
    quantidade INT UNSIGNED NOT NULL,
    PRIMARY KEY (frete_id, produto_id),
    CONSTRAINT fk_frete_produto_frete FOREIGN KEY (frete_id) REFERENCES frete (id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_frete_produto_produto FOREIGN KEY (produto_id) REFERENCES produto (id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_frete_produto_quantidade CHECK (quantidade >= 1)
) ENGINE=InnoDB;

DELIMITER //
CREATE TRIGGER trg_frete_rota_insert
BEFORE INSERT ON frete
FOR EACH ROW
BEGIN
    IF NEW.origem_id = NEW.destino_id THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'A origem e o destino do frete devem ser diferentes';
    END IF;
END//

CREATE TRIGGER trg_frete_rota_update
BEFORE UPDATE ON frete
FOR EACH ROW
BEGIN
    IF NEW.origem_id = NEW.destino_id THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'A origem e o destino do frete devem ser diferentes';
    END IF;
END//
DELIMITER ;

