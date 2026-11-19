# RotaFlow — instruções do projeto

## Objetivo
Implementar o gerenciador logístico acadêmico da UNIPAR em PHP, usando o protótipo existente como referência visual e funcional. Leia o enunciado na pasta `docs/` antes de implementar.

## Arquivos de referência
- `docs/Atividade Prática Orientada - FPI.docx`: enunciado oficial (fonte dos requisitos).
- `docs/Prototipo_Gerenciador_Logistico_UNIPAR.pdf`: referência visual para a entrega da Etapa 1.
- `prototipo/index.html`, `prototipo/styles.css`, `prototipo/app.js`: protótipo navegável. Preserve esses arquivos como referência; reutilize o visual e adapte os componentes para a aplicação real.

## Implementação
- PHP 8+, MySQL/MariaDB, PDO, HTML, CSS, JavaScript e Bootstrap 5. Execução local no XAMPP, sem Laravel ou Angular.
- Criar a aplicação real na raiz do projeto, mantendo o protótipo separado, e organizar diretórios conforme o enunciado e boas práticas. Começar inspecionando os arquivos existentes.
- Implementar dashboard com dados reais; cadastro, listagem e edição de produtos, endereços e transportadores; geração de fretes com origem, destino, transportador, data e múltiplos produtos com quantidade; adicionar/remover linhas dinamicamente via JavaScript; consulta e relatório de fretes por dia, com download gerado pelo PHP (preferência: PDF).
- Persistir dados em banco relacional com chaves estrangeiras e transação para frete/itens. Usar consultas parametrizadas, validação no servidor, escape de HTML e CSRF. Não permitir frete sem itens, quantidade inferior a 1 ou origem igual ao destino.
- Manter aparência do protótipo. Não adicionar autenticação ou funcionalidades fora do escopo. A menção a currículo no enunciado parece um erro de edição; sinalizar a dúvida, sem implementar módulo de currículo.
- Criar SQL de instalação, configuração de conexão local sem credenciais reais versionadas, `.gitignore` e README com passos exatos para XAMPP, importação SQL e execução. Se usar biblioteca para PDF, documentar sua instalação.
- Inicializar Git se ainda não houver repositório e registrar commits incrementais; não publicar no GitHub sem autorização. A entrega exige repositório público e vídeo, que serão providenciados pelo aluno.
- Testar cadastros, edição, frete com vários produtos, persistência e relatório. Não afirmar que testes foram executados se o ambiente não permitir; informar pendências e passos de validação manual.
