# JPTechCell — Sistema de Gestão para Assistência Técnica

Sistema web de gestão de uma assistência técnica de celulares, desenvolvido em **PHP**, com interface administrativa, organização de atendimentos e recursos para acompanhar a operação da loja.

> **Status do repositório:** projeto de estudo/desenvolvimento. O código foi separado de uma instalação local do XAMPP para fins de portfólio e documentação. Não há demonstração pública hospedada nem auditoria de segurança concluída.

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
| **XAMPP** | Ambiente local original de desenvolvimento |
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

O JPTechCell reúne diferentes processos de uma assistência técnica em uma única aplicação, servindo como demonstração prática de desenvolvimento web com PHP, integração com banco de dados e organização de um sistema de gestão.

## Portfólio

Projeto compartilhado no portfólio de **Carlos Arthur Abe de Almeida** para demonstrar desenvolvimento de aplicações web em PHP e organização de rotinas de gestão.
