# Checklist de testes funcionais

## Preparação

- [ ] Apache e MySQL iniciados no XAMPP.
- [ ] `database/schema.sql` importado no phpMyAdmin.
- [ ] `database/seed.sql` importado para dados de demonstração.
- [ ] `config/database.local.php` revisado, se os dados de conexão forem diferentes do padrão do XAMPP.

## Cadastros

- [ ] Criar, pesquisar e editar um produto.
- [ ] Excluir um produto sem relacionamento.
- [ ] Tentar excluir produto já usado em frete e confirmar bloqueio por integridade.
- [ ] Criar, pesquisar e editar endereço com complemento.
- [ ] Criar, pesquisar e editar transportador.
- [ ] Validar campos obrigatórios e valores inválidos no navegador e no servidor.

## Fretes

- [ ] Criar frete com origem, destino, transportador e data.
- [ ] Adicionar pelo menos dois produtos usando o botão dinâmico.
- [ ] Alterar quantidades e conferir peso e valor total no resumo.
- [ ] Remover linha de produto sem permitir que o último item seja removido.
- [ ] Tentar usar a mesma origem e destino e confirmar rejeição.
- [ ] Editar situação, rota e produtos de um frete existente.
- [ ] Abrir a tela de detalhes e conferir os totais.

## Relatórios

- [ ] Filtrar por uma data.
- [ ] Filtrar por intervalo de datas.
- [ ] Conferir os indicadores consolidados.
- [ ] Baixar CSV e abrir no LibreOffice/Excel.
- [ ] Baixar PDF e conferir título, período, linhas e totais.

## Segurança e regressão

- [ ] Tentar submeter POST sem token CSRF.
- [ ] Pesquisar texto com caracteres `<script>` e confirmar que não há execução.
- [ ] Confirmar que registros continuam persistidos após recarregar a página.
- [ ] Executar `php tests/smoke.php` e `php -l` nos arquivos PHP.

