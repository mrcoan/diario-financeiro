# 💰 Diário Financeiro

Sistema web de controle financeiro pessoal, desenvolvido como projeto de faculdade (disciplina de back-end) para praticar PHP, MySQL e boas práticas de segurança em aplicações web.

Permite que o usuário cadastre entradas, saídas e investimentos, acompanhe o saldo mensal e consulte um extrato completo com filtros por período e tipo de transação.

## ✨ Funcionalidades

- Cadastro e login de usuários (com senha criptografada)
- Painel com resumo mensal: entradas, saídas, investimentos e saldo
- Lançamento de novas transações (entrada, saída ou investimento)
- Extrato completo com filtro por tipo de transação e intervalo de datas
- Edição e exclusão de transações
- Atualização de dados da conta (nome e senha)

## 🛠️ Tecnologias

- PHP (sem frameworks)
- MySQL com PDO (prepared statements)
- HTML, CSS puros (JS ainda não utilizado)

## 🚀 Como rodar localmente

1. Clone o repositório e coloque a pasta em um servidor local com PHP e MySQL (ex: XAMPP, Laragon, WAMP).
2. Crie o banco de dados executando o script [`schema.sql`](./database/schema.sql) no MySQL.
3. Configure a conexão em `config/conexao.php` com os dados do seu banco local:
   ```php
   $host = 'localhost';
   $user = 'root';
   $db = 'diario_de_financas';
   $pass = '';
   ```
4. Acesse `index.html` pelo navegador (ex: `http://localhost/diario_de_financas/`).
5. Crie uma conta pela tela de cadastro e comece a usar.

## 🗄️ Estrutura do banco de dados

O banco tem duas tabelas principais:

- **usuarios**: dados de login (nome, e-mail, senha com hash)
- **transacoes**: cada lançamento, com tipo (`entrada`, `saida` ou `investimento`), valor, data e vínculo com o usuário (`ON DELETE CASCADE`)

Veja os detalhes completos em [`schema.sql`](./schema.sql).

## 🔒 Segurança já implementada

- Senhas com `password_hash()` / `password_verify()`
- Consultas via PDO com prepared statements (proteção contra SQL Injection)
- Controle de sessão em todas as páginas protegidas
- Verificação de propriedade do dado (um usuário não acessa/edita transação de outro)

## 🧭 Melhorias futuras (roadmap)

- [ ] Categorias de transações (tabela própria, já planejada no banco)
- [ ] Proteção CSRF nos formulários
- [ ] Trocar exclusão de transação de GET para POST
- [ ] Exigir senha atual para confirmar troca de senha
- [ ] Limite de tentativas de login (proteção contra força bruta)
- [ ] Gráficos de evolução mensal

## 📌 Status

Projeto em desenvolvimento contínuo como parte dos estudos de back-end.
