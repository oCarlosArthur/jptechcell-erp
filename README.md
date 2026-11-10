# JPTechCell — Sistema de Gestão para Assistência Técnica

Sistema web de gestão para assistência técnica de celulares, desenvolvido em **PHP**, com integração a **MySQL/MariaDB**, interface administrativa e funcionalidades para organizar produtos, estoque e outros processos da loja.

## Aplicação em uma loja real

A **JPTechCell é uma loja real**, administrada por um **primo de Carlos Arthur Abe de Almeida**, responsável por este repositório. O sistema é utilizado internamente na loja em **ambiente local (`localhost`)**, com **XAMPP (Apache, PHP e MySQL/MariaDB)**, especialmente para **cadastro de produtos e controle de estoque**.

A aplicação também contém módulos de clientes, vendas, ordens de serviço, financeiro e relatórios. A presença desses módulos no código não significa que todos sejam utilizados diariamente na operação da loja.

> **Sobre esta versão pública:** o repositório documenta uma aplicação com uso real, mas disponibiliza apenas o código para portfólio e estudo. O ambiente operacional funciona localmente; não há demonstração pública hospedada, banco de dados real da loja nem auditoria de segurança concluída.

## Funcionalidades identificadas no código

- **Painel administrativo:** indicadores e informações resumidas da operação.
- **Clientes:** cadastro, edição, consulta e organização de informações.
- **Produtos e estoque:** cadastro de produtos, movimentações e acompanhamento de estoque.
- **Vendas:** registro e consulta de vendas e itens.
- **Ordens de serviço:** abertura, alteração de status, visualização e impressão de atendimentos.
- **Financeiro:** registro e acompanhamento de gastos e informações financeiras.
- **Relatórios:** visualizações e versões para impressão de clientes, produtos, estoque, vendas, finanças e ordens de serviço.
- **Configurações:** personalização de informações e identidade visual do sistema.
- **Acesso:** formulário de login com validação de senha usando `password_verify`.

A existência de telas e rotinas no código não equivale a uma validação funcional ponta a ponta.

## Tecnologias

| Tecnologia | Uso |
|---|---|
| **PHP 8+** | Lógica do servidor, controladores, modelos e páginas |
| **MySQL / MariaDB** | Persistência prevista pelo código via PDO |
| **HTML, CSS e JavaScript** | Interface e interações |
| **XAMPP** | Ambiente local de desenvolvimento e uso interno na loja (`localhost`) |
| **ViaCEP / JsBarcode** | Integrações presentes em funcionalidades específicas |

## Estrutura

```text
jptechcell/
├── app/
│   ├── config/            # Exemplo de configuração local de banco
│   ├── controllers/       # Ações da aplicação
│   ├── core/              # Conexão PDO e componentes principais
│   ├── models/            # Consultas e regras de dados
│   └── views/             # Telas da interface
├── database/              # Reservado para scripts do banco
├── public/
│   ├── assets/
│   │   ├── css/           # Estilos
│   │   └── img/           # Identidade visual
│   ├── uploads/           # Arquivos gerados localmente (ignorados pelo Git)
│   └── index.php          # Entrada e roteamento do sistema
├── storage/               # Dados temporários/locais (ignorados pelo Git)
├── .gitignore
└── README.md
```

## Execução local (XAMPP)

1. Instale o **XAMPP** com PHP e MySQL/MariaDB compatíveis.
2. Coloque a pasta `jptechcell` dentro de `htdocs`.
3. Crie um banco local para o projeto e as respectivas tabelas conforme a modelagem original.
4. **Configuração do banco:** copie `app/config/database.local.example.php` para `app/config/database.local.php` e informe seu host, nome do banco, usuário e senha locais.
5. Inicie o **Apache** e o **MySQL** no painel do XAMPP.
6. Abra `http://localhost/jptechcell/public/index.php`.

**Importante:** o backup recebido não continha um dump SQL nem scripts de criação de tabelas; portanto, o projeto **não estará totalmente executável somente com os arquivos deste repositório**. Será necessário recuperar/reconstruir o esquema do banco e configurar um usuário para acesso.

### Configurações sensíveis

O arquivo **`app/config/database.local.php` não é versionado**. Apenas o modelo `database.local.example.php` é incluído, com valores fictícios. Não publique senhas, dados de clientes, backups ou exportações de banco.

## Observações para uso em produção

Este projeto foi preparado para **compartilhamento do código**, não para disponibilização direta na internet. Antes de qualquer implantação pública é necessário revisar autenticação, autorização, validações, upload de arquivos, backup/restauração, proteção contra CSRF e armazenamento de dados pessoais.

A revisão de publicação removeu a configuração de acesso ao banco embutida no código. **As demais regras de negócio foram preservadas** e não passaram por testes completos com banco de dados.

## Contexto

O JPTechCell foi aplicado em um cenário comercial real: uma assistência técnica de celulares da família do responsável por este repositório. Seu uso interno em `localhost`, principalmente no controle de estoque, evidencia uma aplicação de tecnologia para apoiar as rotinas da loja. Os demais módulos foram mantidos na documentação conforme sua implementação no código.

## Portfólio

Projeto compartilhado no portfólio de **Carlos Arthur Abe de Almeida** como exemplo de aplicação web em PHP/MySQL utilizada no cotidiano de um negócio real, com foco no controle de estoque e na organização de processos de gestão.
