# Study Root

Sistema gerenciador de anotações em PHP, originalmente desenvolvido como Projeto Integrador no IFSC. Esta versão foi modernizada para servir como portfólio, rodando gratuitamente em nuvem.

> Documentação acadêmica: [Google Docs](https://docs.google.com/document/d/1Jh700Q1HGRhJQvnK_KCYZsnnCxnK0gDuvxz54Lvc4ZM/edit?usp=sharing) · Protótipo: [Figma](https://www.figma.com/proto/AiGFEHJlkqDcDRAocHzbB7/Projeto-Integrador---Study-Root?node-id=45-7)

## Stack

- PHP 8.2 + Apache (imagem `php:8.2-apache`)
- PostgreSQL (Neon em produção, Postgres 16 local via Docker)
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

Smoke test end-to-end (em outra janela, com o compose já rodando):

```bash
make smoke        # ou: bash scripts/smoke-test.sh
```

Para resetar o banco:

```bash
make down         # ou: docker compose down -v
make up
```

Outros atalhos úteis (`make help` lista todos): `make test` (PHPUnit no container), `make logs`, `make shell`, `make migrate`, `make cleanup-legacy` (apaga arquivos deprecated).

## Como hospedar grátis na nuvem (Render + Neon)

Esta é a forma mais barata e simples de manter o projeto vivo como portfólio.

### 1. Criar o banco no Neon

1. Crie uma conta em <https://neon.tech> (não pede cartão de crédito).
2. Crie um projeto. O Neon gera um banco `neondb` por padrão — pode usar.
3. Na tela do projeto, copie a **connection string** (formato `postgres://user:pass@ep-xxxx.neon.tech/dbname?sslmode=require`). Você vai colar isso no Render.

### 2. Criar o web service no Render

1. Crie uma conta em <https://render.com>.
2. New → Blueprint → conecte o repositório do GitHub.
3. O Render detecta o `render.yaml` e propõe criar o serviço `study-root`.
4. Quando pedir a variável `DATABASE_URL`, cole a connection string do Neon.
5. Confirme o deploy. O Apache vai escutar na porta que o Render injetar via `$PORT`; o `entrypoint.sh` aplica o `schema.sql` antes de subir.

Pronto. Em ~3 minutos o app está no ar em `https://study-root.onrender.com` (URL exata aparece no painel).

> Plano free do Render hiberna depois de 15 minutos sem tráfego. A primeira request depois disso demora ~30s (cold start). Pra portfólio isso costuma ser aceitável — basta avisar no link.

### Alternativas equivalentes

- **Fly.io**: também roda Docker, free tier generoso. Use `fly launch` apontando para o `Dockerfile`. Configure `DATABASE_URL` com `fly secrets set`.
- **Railway**: $5 USD de crédito mensal. Conecte o repo, ele detecta o Dockerfile.

Em qualquer caso a única variável obrigatória é `DATABASE_URL`.

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
