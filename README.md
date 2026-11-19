# RotaFlow

Gerenciador logístico acadêmico desenvolvido para a disciplina de Fundamentos de Programação para Internet da UNIPAR. A aplicação preserva a identidade visual e o fluxo do protótipo original, substituindo os dados simulados por persistência em MySQL/MariaDB.

## Tecnologias

- PHP 8+
- HTML5, CSS3 e JavaScript puro
- Bootstrap 5.3 e Bootstrap Icons distribuídos localmente
- MySQL/MariaDB com PDO e prepared statements
- XAMPP e Git

Não foram utilizados Laravel, React, Angular ou outros frameworks de aplicação.

## Funcionalidades

- Dashboard com indicadores reais e fretes recentes.
- Cadastro, edição, exclusão, listagem e pesquisa de produtos, endereços e transportadores.
- Cadastro, edição, listagem e visualização de fretes.
- Inclusão dinâmica de vários produtos, sem duplicidade, com quantidade por item e cálculo automático de peso e valor.
- Validação no navegador e no servidor, integridade referencial e transação para o frete e seus produtos.
- Relatórios por data ou intervalo, com indicadores consolidados e exportação em CSV e PDF.
- PDF gerado pelo PHP com quebra de linhas e paginação para evitar conteúdo cortado.
- Proteções contra SQL Injection, XSS, CSRF, métodos HTTP indevidos e carregamento de scripts externos.

## Instalação no XAMPP

1. Copie a pasta do projeto para `C:\xampp\htdocs\rotaflow`.
2. Inicie Apache e MySQL pelo painel do XAMPP.
3. Abra o phpMyAdmin em `http://localhost/phpmyadmin`.
4. Para uma instalação nova, importe `database/schema.sql` e depois `database/seed.sql` no banco `rotaflow`.
5. Copie `config/database.example.php` para `config/database.local.php` somente se precisar alterar host, porta, usuário ou senha. O padrão do XAMPP costuma ser usuário `root`, senha vazia e porta `3306`.
6. Acesse `http://localhost/rotaflow/public/`.

O `schema.sql` contém comandos de criação e limpeza das tabelas para instalação/reinstalação limpa. Não o execute novamente em um banco que contenha registros que devam ser preservados. O `seed.sql` insere apenas dados fictícios de demonstração.

Não é necessário instalar Composer ou Node.js para executar o sistema. Bootstrap, Bootstrap Icons e a fonte dos ícones já estão em `public/assets/vendor`.

## Configuração e estrutura

O arquivo de exemplo está em `config/database.example.php`. A configuração local, quando necessária, deve ficar em `config/database.local.php`, que está no `.gitignore` e não deve ser publicada.

O ponto de entrada público é `public/index.php`. A aplicação utiliza uma MVC simplificada, separando Controllers, Models, Repositories, Services e Views. Os scripts SQL ficam em `database`, os assets públicos em `public/assets`, os testes em `tests` e a documentação em `docs`.

## Relatórios

Na tela **Relatórios**, informe uma data ou um intervalo válido. Datas inexistentes e intervalos em que a data inicial é posterior à final são rejeitados. Os botões geram arquivos CSV e PDF filtrados pelo mesmo período.

Para verificar o PDF manualmente, abra o arquivo baixado com um leitor de PDF ou use `pdfinfo`/`pdftotext` quando disponíveis. Relatórios extensos são divididos em páginas automaticamente.

## Testes

Na raiz do projeto, execute o smoke test:

```text
php tests/smoke.php
```

Para verificar a sintaxe de todos os arquivos PHP no PowerShell:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

O checklist de fluxos manuais está em [docs/checklist-testes.md](docs/checklist-testes.md), incluindo cadastros, pesquisa, edição, fretes com múltiplos produtos, cálculos, persistência e exportações.

## Documentação acadêmica

- [Estrutura do projeto](docs/estrutura-projeto.md)
- [Checklist de testes](docs/checklist-testes.md)
- [Enunciado acadêmico](<docs/Atividade Prática Orientada - FPI.docx>)
- [PDF do protótipo original](<docs/Prototipo_Gerenciador_Logistico_UNIPAR.pdf>)
- [Protótipo original](prototipo)

O enunciado menciona “transformação dos dados/formatação do currículo”, embora o restante da atividade descreva um gerenciador logístico. Essa inconsistência foi registrada nos documentos acadêmicos; não foi criado módulo de currículo por estar fora do escopo do RotaFlow.

A publicação no GitHub e o vídeo de 5 a 10 minutos exigidos pela entrega acadêmica devem ser realizados pelo aluno conforme as orientações da disciplina.
