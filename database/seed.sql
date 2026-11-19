USE rotaflow;

INSERT INTO produto (id, nome, descricao, peso, valor) VALUES
(1, 'Notebook corporativo 15 polegadas', 'Equipamento eletrônico', 2.200, 3490.00),
(2, 'Monitor LED 24 polegadas', 'Monitor Full HD', 3.600, 899.90),
(3, 'Impressora multifuncional', 'Equipamento de escritório', 6.300, 1250.00),
(4, 'Cadeira de escritório', 'Mobiliário', 12.500, 680.00),
(5, 'Caixa de periféricos', 'Acessórios variados', 4.800, 320.00);

INSERT INTO endereco (id, identificacao, cep, logradouro, numero, complemento, bairro, cidade, estado) VALUES
(1, 'Centro de distribuição', '80010000', 'Av. Central', '450', NULL, 'Centro', 'Curitiba', 'PR'),
(2, 'Depósito regional', '85601000', 'Rua das Indústrias', '120', NULL, 'Industrial', 'Francisco Beltrão', 'PR'),
(3, 'Unidade comercial', '85810000', 'Rua Comercial', '95', 'Sala 2', 'Centro', 'Cascavel', 'PR'),
(4, 'Ponto de coleta', '87010000', 'Av. das Flores', '210', NULL, 'Zona 01', 'Maringá', 'PR');

INSERT INTO transportador (id, nome, documento, telefone, email, placa_veiculo) VALUES
(1, 'Sul Express Logística', '12.345.678/0001-90', '(41) 99990-1000', 'contato@sulexpress.exemplo', 'ABC1D23'),
(2, 'TransVale Cargas', '98.765.432/0001-10', '(46) 99990-2000', 'contato@transvale.exemplo', 'DEF4G56'),
(3, 'Norte Sul Transportes', '55.444.333/0001-22', '(45) 99990-3000', 'contato@nortesul.exemplo', 'HIJ7K89');

INSERT INTO frete (id, origem_id, destino_id, transportador_id, data_frete, status) VALUES
(1039, 4, 3, 1, '2026-11-10', 'Concluído'),
(1040, 2, 1, 3, '2026-11-11', 'Em aberto'),
(1041, 3, 4, 2, '2026-11-12', 'Concluído'),
(1042, 1, 2, 1, '2026-11-12', 'Concluído');

INSERT INTO frete_produto (frete_id, produto_id, quantidade) VALUES
(1039, 5, 3),
(1040, 4, 4),
(1041, 3, 2),
(1041, 5, 2),
(1042, 1, 2),
(1042, 2, 1);

