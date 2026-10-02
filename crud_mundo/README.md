# CRUD Mundo

Sistema web desenvolvido para o gerenciamento de informações geográficas sobre países e cidades.

O projeto foi desenvolvido como atividade do curso de **Desenvolvimento de Sistemas da ETEC – Profª Ilza Nascimento Pintus em São José dos Campos**, utilizando HTML, CSS, JavaScript, PHP e MySQL.

## Sobre o projeto

O CRUD Mundo é uma aplicação web que permite o cadastro, consulta, edição e exclusão de informações relacionadas a países, cidades, continentes e governantes.

O sistema utiliza o conceito de **CRUD (Create, Read, Update e Delete)** para realizar o gerenciamento dos dados armazenados no banco de dados.

Além das funcionalidades de gerenciamento, o projeto possui um sistema de autenticação de usuários com diferentes níveis de acesso, permitindo controlar quais funcionalidades podem ser utilizadas por cada tipo de usuário.

## Funcionalidades

### Gerenciamento de países

* Cadastro de países;
* Consulta dos países cadastrados;
* Edição das informações;
* Exclusão de registros;
* Associação com continente e governante;
* Cadastro de informações como população, área, idioma, clima, regime político e moeda.

### Gerenciamento de cidades

* Cadastro de cidades;
* Consulta das cidades cadastradas;
* Edição das informações;
* Exclusão de registros;
* Associação da cidade a um país;
* Associação com governante;
* Cadastro de informações como população, área, clima e data de fundação.

### Gerenciamento de continentes

* Cadastro de continentes;
* Consulta dos continentes cadastrados;
* Edição das informações;
* Exclusão de registros.

### Gerenciamento de governantes

* Cadastro de governantes;
* Consulta dos governantes cadastrados;
* Edição das informações;
* Exclusão de registros;
* Associação dos governantes a países ou cidades.

### Sistema de autenticação

O projeto possui um sistema de login com controle de acesso de acordo com o tipo de usuário.

Entre as funcionalidades estão:

* Login e logout;
* Controle de sessão;
* Diferenciação entre usuário e administrador;
* Alteração de senha;
* Alteração obrigatória da senha no primeiro acesso;
* Bloqueio após tentativas consecutivas de login com senha incorreta;
* Registro de eventos de autenticação.

### Pesquisa

O sistema possui uma funcionalidade de pesquisa para facilitar a localização de informações cadastradas.

## Tecnologias utilizadas

### Front End

* HTML5
* CSS3
* JavaScript

### Back End

* PHP
* PDO

### Banco de dados

* MySQL

### Ferramentas

* XAMPP
* Git
* GitHub

## Banco de dados

O banco de dados utilizado pelo projeto é chamado `bd_mundo`.

As principais tabelas utilizadas são:

* `continentes`
* `paises`
* `cidades`
* `governantes`
* `USUARIOS`
* `LOGS`

As tabelas possuem relacionamentos por meio de chaves estrangeiras, permitindo associar, por exemplo, cidades aos seus respectivos países e países aos seus continentes.

## Controle de acesso

O sistema possui dois tipos principais de usuários:

**Usuário:** possui acesso às funcionalidades de consulta disponíveis no sistema.

**Administrador:** possui acesso às funcionalidades administrativas, incluindo o gerenciamento dos registros e dos usuários.

O acesso às páginas e operações administrativas é verificado pelo sistema de autenticação.

## Segurança

O sistema possui mecanismos de segurança para o controle de acesso e proteção das informações, incluindo:

* Armazenamento seguro de senhas utilizando hash;
* Verificação de senhas;
* Controle de sessões;
* Consultas preparadas com PDO;
* Controle de permissões;
* Bloqueio após três tentativas consecutivas de senha incorreta;
* Registro de eventos na tabela `LOGS`.

## Como executar o projeto

### 1. Instalar o XAMPP

Instale o XAMPP e inicie os serviços:

* Apache
* MySQL

### 2. Configurar o banco de dados

Abra o **phpMyAdmin** através do XAMPP e importe o arquivo:

```text
bd_mundo.sql
```

Esse arquivo contém a estrutura necessária para o funcionamento do banco de dados.

### 3. Verificar a conexão

A conexão com o banco de dados está configurada no arquivo:

```text
config/conexao.php
```

Verifique se as informações de conexão correspondem à configuração do seu ambiente MySQL.

### 4. Acessar o sistema

Com o Apache e o MySQL em funcionamento, abra o navegador e acesse:

```text
http://localhost/crud_mundo/
```

## Estrutura do projeto

```text
crud_mundo/
│
├── actions/
│   ├── cidade_process.php
│   ├── login_process.php
│   └── pais_process.php
│
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── main.js
│
├── autenticacao/
│   ├── login.php
│   └── logout.php
│
├── config/
│   ├── auth.php
│   └── conexao.php
│
├── includes/
│   ├── footer.html
│   └── header.html
│
├── paginas/
│   ├── alterar_senha.php
│   ├── cidade_form.php
│   ├── criar_usuario.php
│   └── pais_form.php
│
├── bd_mundo.sql
├── index.php
└── README.md
```

## Autor

**Nome:** Nicole Heloiza

**Curso:** Desenvolvimento de Sistemas

**Instituição:** ETEC – Profª Ilza Nascimento Pintus, São José dos Campos

**Projeto:** CRUD Mundo – Programação Web

## Projeto acadêmico

Projeto desenvolvido para fins acadêmicos no curso de Desenvolvimento de Sistemas, com o objetivo de aplicar conhecimentos de desenvolvimento web, banco de dados, PHP, JavaScript, HTML e CSS.
