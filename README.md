# Study Root

Sistema gerenciador de anotações em PHP, originalmente desenvolvido como Projeto Integrador(TCC) no IFSC. Esta versão foi modernizada para servir como portfólio, rodando gratuitamente em nuvem.

**AVISO** O site está hospedado gratuitamente no Render, após ficar inativo por um tempo ele precisa de 30 segundos para voltar a ativa, este foi meu primeiro projeto da vida e ele renderiza quase completamente tudo no lado do servidor então a maioria das ações demoram um pouco para serem efetuadas.

**AVISO 2** Este sistema foi criado exclusivamente para fins educacionais e usado como portfólio, todas informações, contas e dados estão sujeitos a serem apagados a qualquer momento sem aviso prévio e eu não recomendo que insira dados sensíveis ou sequer reais em qualquer uso dele, você pode se cadastrar até mesmo com um email fictício pois não há validação se ele existe ou pertence a você.

[Site do Study Root](https://study-root-qxr4.onrender.com/)

> Documentação acadêmica: [Google Docs](https://docs.google.com/document/d/1Jh700Q1HGRhJQvnK_KCYZsnnCxnK0gDuvxz54Lvc4ZM/edit?usp=sharing) · Protótipo: [Figma](https://www.figma.com/proto/AiGFEHJlkqDcDRAocHzbB7/Projeto-Integrador---Study-Root?node-id=45-7)

## Como usar o site?
1 - O primeiro acesso começa pela tela de login, acesse a tela de cadastro pelo link abaixo do formulário de login

2 - Após se cadastrar ele vai logar automáticamente e te enviar à tela principal, na esquerda você pode visualizar uma barra de pesquisa, um botão de configuração que abre um modal com as opções de alterar sua senha ou deslogar, e então você vê o botão branco com um sinal de mais (+) começe clicando nesse mais.

3 - No modal acessado você criará seu primeiro assunto, ela vai servir como uma pasta com nome e descrição

4 - Agora na sua esquerda está o seu primeiro assunto, clique nele e a tela principal mostrará o título, a descrição e o botão de adicionar anotações, clique para adicionar uma anotação

5 - Clicando sobre a sua anotação recém criada você verá o editor de textos, após digitar ele vai salvar sua escrita.

## Stack

- PHP 8.2 + Apache (imagem `php:8.2-apache`)
- PostgreSQL 16
- PDO_pgsql como driver único
- TinyMCE para edição rica
- Bootstrap 5 + FontAwesome 6

## Funcionalidades

- Cadastro/login de usuários (bcrypt nativo do PHP)
- CRUD de assuntos e anotações
- Editor rich text com persistência de HTML
- **Autosave**: o conteúdo é salvo automaticamente 1,5s após você parar de digitar (e no máximo a cada 15s mesmo digitando direto). O botão Salvar continua existindo para salvar imediatamente. Indicador "Salvo às HH:MM:SS" mostra o último sucesso.
- Busca client-side por título de assunto
- Troca de senha

## Como rodar localmente

Pré-requisitos: Docker e Docker Compose.

```bash
git clone https://github.com/<seu-usuario>/Study-Root.git
cd Study-Root
make up           # ou: docker compose up --build
```

Acesse <http://localhost:8080>. O schema é aplicado automaticamente pelo entrypoint na primeira subida.

## Arquitetura

```
src/
├── index.php                 # tela de login (DocumentRoot)
├── back-end/
│   ├── lib/
│   │   ├── db.php           # PDO singleton (parseia DATABASE_URL ou env vars)
│   │   ├── auth.php         # sessão segura, CSRF, requireAuth
│   │   └── helpers.php      # h() escape, safe_redirect, validações
│   ├── bcrypt.php           # wrapper de password_hash/verify
│   ├── login.php, logout.php, cadastrar.php
│   ├── cadastro_assunto.php, update_assunto.php, delete_assunto.php
│   ├── cadastro_anotacao.php, update_anotacao.php, delete_anotacao.php
│   ├── update_texto.php, troca_senha.php
│   └── migrate.php          # aplica db/schema.sql (idempotente)
├── telas/                   # home, assunto, anotacao, cadastro
├── css/, js/, img/          # assets estáticos
└── db/
    └── schema.sql           # única fonte da verdade do schema (Postgres)

docker/
└── entrypoint.sh            # ajusta porta + roda migrate + apache
```

## Segurança aplicada nesta versão

Comparada à versão original, esta refatoração corrige:

- **SQL Injection**: todas as queries agora usam prepared statements via PDO.
- **XSS**: toda saída para HTML passa por `h()` (`htmlspecialchars` com `ENT_QUOTES` + UTF-8). Templates não montam mais HTML via `printf` com `%s`.
- **CSRF**: cada formulário inclui um token gerado por request, verificado em todos os handlers POST.
- **Session fixation**: `session_regenerate_id(true)` no login e na troca de senha.
- **Cookies de sessão**: `HttpOnly`, `SameSite=Lax`, `Secure` em HTTPS (detectado via `X-Forwarded-Proto`).
- **Open redirect**: o parâmetro `pagina` agora passa por whitelist (`safe_redirect`).
- **Auth bypass**: telas privadas chamam `require_auth()` com `exit;` imediato — não mais `print "<script>...</script>"` seguido por renderização normal.
- **Senhas**: `password_hash`/`password_verify` nativos do PHP, com `password_needs_rehash` na verificação. Hashes antigos (`$2a$`) continuam funcionando.
- **Ownership checks**: todo UPDATE/DELETE/SELECT sensível filtra por `id_estudante_fk` ou faz JOIN garantindo que o registro pertence ao usuário logado.

## Variáveis de ambiente

| Nome | Obrigatório | Default | Notas |
|---|---|---|---|
| `DATABASE_URL` | sim em prod | — | URL única estilo Neon/Heroku. Tem precedência sobre as variáveis discretas. |
| `DB_DRIVER` | não | `pgsql` | `pgsql` ou `mysql`. |
| `DB_HOST` / `DB_PORT` / `DB_NAME` / `DB_USER` / `DB_PASS` | não | valores de dev | Usados se `DATABASE_URL` não estiver setada. |
| `PORT` | não | `8080` | Porta que o Apache escuta. Render injeta automaticamente. |
| `APP_ENV` | não | — | Apenas cosmético. |

## Testes

A suíte é PHPUnit 10 e cobre `helpers`, `bcrypt`, CSRF e o sanitizador HTML. Com o compose rodando:

```bash
make up           # sobe app + Postgres (numa shell separada)
make test         # roda phpunit --testdox dentro do container
```

Sem Make:

```bash
docker compose exec app bash -c "composer install && vendor/bin/phpunit --testdox"
```

Cobertura atual:

- `HelpersTest` — escape HTML, normalização de espaços, validação de string não-vazia.
- `BcryptTest` — hash/verify, detecção de rehash necessário, compatibilidade com hashes legados `$2a$`.
- `AuthTest` — comparação CSRF em tempo constante, rejeição de tokens vazios/nulos/parciais.
- `SafeRedirectTest` — whitelist contra URLs externas, `javascript:`, traversal, query strings malformadas.
- `HtmlPurifyTest` — strip de `<script>`, `onerror`, `javascript:`, `data:`, `<iframe>`, `<style>`, preservação de formatação básica, `rel=noopener` em `target=_blank`.

## Limitações conscientes

- Sem rate limiting no login. Para um portfólio sem tráfego adversarial, o `password_hash` com custo 10 já dá tempo suficiente. Se quiser endurecer, dá pra adicionar `fail2ban` no nível de plataforma ou um cache TTL de tentativas.
- Cache do HTMLPurifier vive em `/tmp` — não persiste entre restarts. Como o purifier é rápido, o impacto é desprezível para o tráfego de portfólio.

## Sobre as participações originais

- **Mateus Galvão de Camargo** — Líder de equipe, fullstack, modelagem
- **Gabriel Lopes** — Front-end, prototipagem
- **Bruno Karl** — Revisão de texto

A modernização para deploy em nuvem (este branch) foi feita pelo Mateus.

## Licença

MIT
