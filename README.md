# 💰 Diário Financeiro

Sistema web de controle financeiro pessoal, desenvolvido como projeto acadêmico para aplicar conhecimentos de desenvolvimento web, programação back-end, banco de dados e segurança da informação.

O Diário Financeiro permite que usuários organizem suas movimentações financeiras, acompanhem entradas, saídas e investimentos, consultem seu histórico e visualizem um resumo de sua situação financeira.

## ✨ Funcionalidades

### 👤 Usuários e autenticação
- Cadastro de usuários.
- Login com autenticação por e-mail e senha.
- Senhas armazenadas com criptografia por hash.
- Controle de sessões e proteção de páginas restritas.
- Atualização dos dados da conta.
- Alteração de senha.
- Exclusão de conta mediante confirmação de senha.

### 💳 Controle financeiro
- Cadastro de movimentações financeiras:
  - Entradas;
  - Saídas;
  - Investimentos.
- Associação de categorias pré-definidas às movimentações.
- Possibilidade de realizar lançamentos sem categoria.
- Edição e exclusão de movimentações.
- Histórico de movimentações financeiras.

### 📊 Painel e extrato
- Painel com resumo financeiro mensal.
- Visualização de saldo, entradas, saídas e investimentos.
- Exibição das últimas movimentações.
- Extrato financeiro com filtros por período e tipo de movimentação.
- Exportação do extrato em formato JSON.

### 🎨 Interface
- Layout responsivo, adaptado para computadores, tablets e celulares.
- Identidade visual própria.
- Modais para cadastro e edição de movimentações.
- Notificações do tipo Toast.
- Animações e elementos interativos.

## 🛠️ Tecnologias utilizadas

| Tecnologia | Aplicação |
|---|---|
| HTML5 | Estrutura das páginas |
| CSS3 | Estilização e responsividade |
| JavaScript | Interatividade e funcionalidades da interface |
| PHP | Processamento e regras de negócio |
| MySQL | Armazenamento e gerenciamento dos dados |
| PDO | Comunicação entre PHP e banco de dados |

### Bibliotecas e recursos externos
- **Font Awesome:** ícones da interface.
- **Swiper.js:** carrossel de destaques.
- **AOS (Animate On Scroll):** animações durante a navegação.
- **Google Fonts:** tipografia do projeto.

## 🗄️ Estrutura do banco de dados

O banco de dados utiliza três tabelas principais:

- **usuarios:** armazena os dados dos usuários, incluindo nome, e-mail e senha.
- **categorias:** armazena as categorias financeiras pré-definidas pelo sistema.
- **transacoes:** registra as movimentações financeiras, incluindo descrição, valor, tipo, data e associação ao usuário e, opcionalmente, a uma categoria.

### Relacionamentos

- Um usuário pode possuir diversas movimentações financeiras.
- Cada movimentação pertence a um usuário.
- Uma categoria pode estar associada a diversas movimentações.
- Uma movimentação pode não possuir categoria.

Os relacionamentos são implementados por meio de chaves estrangeiras, utilizando regras de integridade referencial.

O esquema completo do banco de dados está disponível em [`database/schema.sql`](database/schema.sql).

## 📁 Estrutura do projeto

```text
diario-financeiro/
│
├── app/
│   ├── painel.php
│   ├── extrato.php
│   ├── graficos.php
│   └── config.php
│
├── config/
│   ├── conexao.php
│   └── .htaccess
│
├── css/
│   └── style.css
│
├── database/
│   └── schema.sql
│
├── img/
│   ├── logo.png
│   ├── logo-preta.png
│   ├── logo-branca.png
│   └── ...
│
├── js/
│   └── script.js
│
├── process/
│   ├── processar_lancamento.php
│   ├── editar_transacao.php
│   ├── excluir_transacao.php
│   ├── excluir_conta.php
│   └── logout.php
│
├── templates/
│   ├── head.php
│   ├── header_publico.php
│   ├── header_logado.php
│   ├── footer.php
│   ├── toast.php
│   └── bibliotecas_js.php
│
├── index.php
├── login.php
├── cadastro.php
└── recuperar_senha.php
```

## 🚀 Como executar localmente

### Pré-requisitos

- Servidor local com suporte a PHP.
- MySQL ou MariaDB.
- Extensão PDO MySQL habilitada no PHP.
- Navegador atualizado.

Para facilitar a configuração, recomenda-se utilizar o [XAMPP](https://www.apachefriends.org/pt_br/index.html).

### 1. Clonar o repositório

```bash
git clone URL_DO_REPOSITORIO
```

Coloque a pasta do projeto dentro do diretório `htdocs` do XAMPP.

### 2. Criar o banco de dados

Inicie o Apache e o MySQL pelo painel do XAMPP.

Acesse o phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Importe o arquivo `database/schema.sql` para criar o banco de dados e suas tabelas, incluindo as categorias iniciais.

### 3. Configurar a conexão

Abra o arquivo `config/conexao.php` e ajuste as credenciais conforme a configuração do seu ambiente:

```php
$host = 'localhost';
$user = 'root';
$db = 'diario_de_financas';
$pass = '';
```

**Importante:** utilize as credenciais apropriadas para seu ambiente. Não publique senhas reais ou informações privadas no repositório.

### 4. Acessar o sistema

Com Apache e MySQL em execução, acesse:

```text
http://localhost/diario-financeiro/
```

Realize o cadastro de um usuário e faça login para começar a utilizar o sistema.

## 🔒 Segurança

O projeto utiliza algumas práticas de segurança no desenvolvimento:

- Armazenamento de senhas utilizando `password_hash()`.
- Verificação de senhas com `password_verify()`.
- Consultas preparadas com PDO (`prepared statements`).
- Controle de sessões para restringir o acesso às páginas internas.
- Verificação de propriedade das movimentações, impedindo que um usuário altere ou exclua registros de outro.
- Validação de dados recebidos pelos formulários.
- Confirmação de senha antes da exclusão de uma conta.
- Restrição de acesso direto aos arquivos de configuração.

## 🗺️ Próximas etapas

O projeto continua em desenvolvimento. Entre as melhorias planejadas estão:

- [ ] Filtros do extrato por período, tipo e categoria.
- [ ] Combinação de filtros nas consultas SQL.
- [ ] Melhorias visuais nos filtros.
- [ ] Reorganização e refatoração do código PHP.
- [ ] Implementação dos gráficos e indicadores financeiros.
- [ ] Aprimoramento da experiência de utilização e responsividade.

## 📌 Status do projeto

<em>Em desenvolvimento.</em>

Projeto acadêmico desenvolvido por **Marco Antonio Araujo de Almeida**, com o objetivo de consolidar conhecimentos em desenvolvimento web, programação PHP, JavaScript, banco de dados relacionais e engenharia de software.
