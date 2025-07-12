# Cinebox 🎬

O **Cinebox** é um projeto pessoal de aprendizado e prática com **PHP puro**, iniciado com o objetivo de construir uma aplicação de filmes simples, mas que evoluiu para um projeto backend mais profissional, com foco em **boas práticas de arquitetura e autenticação moderna**.

---

## 📌 Evolução do Projeto

### 🟢 Fase 1 – Estrutura Inicial com PHP + HTML
- Estrutura organizada por camadas (`Controllers`, `Models`, `Core`, `Views`, etc).
- Uso de **PHP misturado com HTML** para construção das páginas.
- Proposta de utilizar **mensagens flash** para feedback visual.
- Backend e frontend estavam acoplados.
- Ainda sem namespaces, sem tipagem e sem uso de Composer.

> ✅ Essa etapa foi **concluída com sucesso**, garantindo uma base funcional. Após isso, me motivei a transformar o Cinebox em uma **API desacoplada**, focando exclusivamente no backend em PHP puro.

---

### 🟡 Fase 2 – Refatoração para API + Boas Práticas
- Estrutura convertida em uma **REST API**.
- Separação clara por camadas com `Controllers`, `Services`, `Models`, `Core`, `Utils`.
- Aplicação de **tipagem forte** (type hinting).
- Uso de **namespaces com padrão PSR-4**.
- Tratamento centralizado de exceções (`safe()`).
- Respostas padronizadas em **JSON**, prontas para consumo por ferramentas como Postman ou Insomnia.

> ✅ Essa etapa trouxe mais organização e profissionalismo ao projeto. Foi a base para adicionar autenticação e modularizar as responsabilidades da aplicação.

---

### 🟠 Fase 3 – Autenticação com JWT e Composer (em andamento)
- Substituição do `$_SESSION['auth']` por **autenticação JWT**.
- Implementação de **validação de token via headers Authorization**.
- Criação do serviço `JwtService` com geração e validação de tokens.
- Introdução do **Composer** no projeto:
  - Para facilitar a instalação da biblioteca `firebase/php-jwt`.
  - Para configurar o autoload padrão PSR-4.
  - Como primeira experiência prática com gerenciamento de dependências.

> ⚠️ O uso do Composer neste projeto foi **intencionalmente simples**, apenas para facilitar o uso do JWT. A base do projeto permanece em **PHP puro**, com foco no entendimento dos fundamentos.

---

## 📚 Estrutura Atual do Projeto

```text
cinebox/
├── config/ # Configurações do banco e do sistema
│ └── config.php # Arquivo de configuração do banco de dados
├── public/ # Ponto de entrada da aplicação
│ └── index.php # Front controller
├── src/ # Código-fonte principal
│ ├── Controllers/ # Controllers da aplicação
│ ├── Core/ # Classes base, router, helpers
│ ├── Models/ # Modelos 
│ ├── Services/ # Serviços de negócio (regras)
│ └── Utils/ # Utilitários (ex: validação)
├── vendor/ # Dependências gerenciadas pelo Composer
├── composer.json # Arquivo de configuração do Composer
└── composer.lock # Arquivo de bloqueio (gerado pelo Composer)
```

---

## ✨ Funcionalidades já implementadas

- ✅ Cadastro e login de usuários
- ✅ Autenticação via **JWT Token**
- ✅ Listagem e criação de filmes
- ✅ Favoritar e desfavoritar filmes
- ✅ Avaliação de filmes (nota e comentário)
- ✅ Validação de dados reutilizável e modular
- ✅ Respostas em JSON com status HTTP adequados
- ✅ Estrutura MVC com serviços separados
- ✅ Tipagem forte
- ✅ Uso de namespaces e autoload com Composer

---

## 🎯 Próximos passos

- [ ] Finalizar autenticação JWT (ex: refresh token)
- [ ] Criar testes automatizados (PHPUnit)
- [ ] Melhorar o roteador manual ou substituí-lo por estrutura mais escalável
- [ ] Incluir controle de permissões (níveis de acesso)
- [ ] Desenvolver ou conectar um frontend para consumir a API

---

## 🙋‍♂️ Sobre o Projeto

Este projeto não utiliza frameworks como Laravel por escolha consciente.  
O objetivo é praticar **PHP puro**, com foco em:

- Entendimento da arquitetura MVC
- Manipulação manual de rotas
- Controle de autenticação sem abstrações
- Evolução natural do código com aprendizado contínuo

---

## 📌 Observações

O projeto pode ser facilmente testado com ferramentas como **Insomnia** ou **Postman**, pois todas as respostas seguem o padrão **REST + JSON**.

A estrutura e as decisões refletem o crescimento do desenvolvedor durante a execução do projeto.

---

> _Projeto criado e mantido por **Bruno Ismael** – explorando, testando e evoluindo no universo do backend em PHP. 🚀_
