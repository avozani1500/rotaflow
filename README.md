# RotaFlow

Gerenciador logístico acadêmico desenvolvido para a disciplina de Fundamentos de Programação para Internet da UNIPAR. A aplicação preserva a identidade visual do protótipo fornecido, mas substitui os dados em memória por persistência real em MySQL/MariaDB.

## Tecnologias

- PHP 8+
- HTML5, CSS3 e JavaScript puro
- Bootstrap 5.3 via CDN e Bootstrap Icons
- MySQL/MariaDB com PDO
- XAMPP e Git

Não foram utilizados Laravel, React, Angular ou outros frameworks de aplicação.

## Funcionalidades

- Dashboard com indicadores e fretes recentes carregados do banco.
- Cadastro, edição, pesquisa e exclusão de produtos, endereços e transportadores.
- Cadastro e edição de fretes com origem, destino, transportador, data, situação e múltiplos produtos.
- Cálculo dinâmico do peso, quantidade e valor total dos itens.
- Integridade referencial e transação para cabeçalho do frete e seus produtos.
- Relatório filtrável por data ou intervalo, com download em CSV e PDF gerados pelo PHP.
- Proteções contra SQL Injection, XSS e CSRF.

## Instalação no XAMPP

1. Copie a pasta do projeto para `C:\xampp\htdocs\rotaflow`.
2. Inicie Apache e MySQL no painel do XAMPP.
3. Abra `http://localhost/phpmyadmin`.
4. Importe `database/schema.sql`.
5. Importe `database/seed.sql` no banco `rotaflow`.
6. Se necessário, copie `config/database.example.php` para `config/database.local.php` e ajuste host, porta, usuário e senha. O padrão funciona com o XAMPP sem senha para `root`.
7. Acesse `http://localhost/rotaflow/public/`.

O ponto de entrada público é `public/index.php`. Não é necessário instalar Composer ou Node para executar a aplicação.

## Testes locais

O teste estático pode ser executado na raiz:

```text
php tests/smoke.php
```

Para verificar sintaxe em todos os PHP:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

O roteiro de teste manual está em [docs/checklist-testes.md](docs/checklist-testes.md).

## Estrutura

Veja [docs/estrutura-projeto.md](docs/estrutura-projeto.md) para a descrição da arquitetura MVC simplificada.

O diretório `prototipo` foi mantido intacto como referência da Etapa 1. A aplicação real está na raiz, organizada em `app`, `config`, `database`, `public` e `tests`.

## Observações acadêmicas

O enunciado menciona “transformação dos dados/formatação do currículo”, embora todo o restante da atividade descreva um gerenciador logístico. Essa inconsistência foi registrada e não foi implementado um módulo de currículo, pois ele está fora do escopo do RotaFlow.

A entrega acadêmica ainda requer que o aluno publique o repositório no GitHub e grave o vídeo de 5 a 10 minutos conforme o enunciado. Essas ações externas não são executadas automaticamente.

