# Estrutura do projeto

O RotaFlow utiliza uma arquitetura MVC simplificada, adequada ao PHP 8 e ao XAMPP.

| Diretório | Responsabilidade |
| --- | --- |
| `app/Controllers` | Recebe a requisição, valida fluxo e escolhe a view. |
| `app/Models` | Regras de validação dos dados de domínio. |
| `app/Repositories` | Consultas PDO parametrizadas e persistência. |
| `app/Services` | Regras que atravessam mais de uma entidade, como a transação de frete e exportação. |
| `app/Views` | Layout, componentes reutilizáveis e telas em português brasileiro. |
| `config` | Configuração da aplicação e conexão local. |
| `database` | Scripts de criação e carga inicial do MySQL/MariaDB. |
| `public` | Único ponto de entrada web e assets CSS/JavaScript. |
| `tests` | Smoke test executável sem banco. |
| `docs` | Referências acadêmicas, checklist e documentação de apoio. |

O fluxo de uma gravação de frete é: `public/index.php` → `FreightController` → `FreightService` → `FreightRepository`. O repositório abre uma transação, grava o cabeçalho em `frete`, substitui os itens em `frete_produto` e confirma tudo somente se todas as inserções forem concluídas.

## Segurança aplicada

- PDO com `ATTR_EMULATE_PREPARES = false` e consultas parametrizadas.
- Token CSRF em todas as operações POST.
- `htmlspecialchars` centralizado na função `e()` para saída HTML.
- Validação de dados no navegador e novamente no servidor.
- Chaves estrangeiras, `CHECK`, `UNIQUE` e transações no banco.
- Nenhum login, perfil ou funcionalidade fora do escopo acadêmico.

