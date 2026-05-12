# Pré-requisito: `docker compose up` rodando em outra janela.
#
# Esse script faz um fluxo end-to-end por curl:
#   1. GET / -> 200, contém form de login
#   2. POST cadastro -> 302 para home
#   3. GET /telas/home.php (com cookie) -> 200
#   4. POST cadastro_assunto -> 302
#   5. GET home contém o assunto criado
#   6. POST logout -> 302 para /index.php
#   7. GET /telas/home.php sem cookie -> 302 para /index.php (auth)
#
# Falha rápido na primeira asserção que quebra.

set -euo pipefail

BASE_URL="${BASE_URL:-http://localhost:8080}"
COOKIE_JAR="$(mktemp)"
EMAIL="smoke-$(date +%s)@example.com"
SENHA="hunter22"
ASSUNTO="Assunto Teste $$"

trap 'rm -f "$COOKIE_JAR"' EXIT

red()   { printf "\033[31m%s\033[0m\n" "$*"; }
green() { printf "\033[32m%s\033[0m\n" "$*"; }
say()   { printf "→ %s\n" "$*"; }

assert_status() {
    local actual="$1" expected="$2" label="$3"
    if [ "$actual" = "$expected" ]; then
        green "  ✔ $label (HTTP $actual)"
    else
        red "  ✘ $label esperava HTTP $expected, veio $actual"
        exit 1
    fi
}

assert_contains() {
    local body="$1" needle="$2" label="$3"
    if echo "$body" | grep -q -- "$needle"; then
        green "  ✔ $label"
    else
        red "  ✘ $label — body não contém '$needle'"
        echo "--- body (primeiros 200 chars) ---"
        echo "$body" | head -c 200
        echo
        exit 1
    fi
}

# Helper: extrai o token CSRF de um body HTML
extract_csrf() {
    grep -oE 'name="_csrf" value="[a-f0-9]+"' | head -1 | sed -E 's/.*value="([a-f0-9]+)"/\1/'
}

say "1. GET / (página de login)"
body=$(curl -s -c "$COOKIE_JAR" -w "\n%{http_code}" "$BASE_URL/")
code=$(echo "$body" | tail -1)
body=$(echo "$body" | sed '$d')
assert_status "$code" "200" "GET /"
assert_contains "$body" 'name="email"' "página tem campo email"
assert_contains "$body" '_csrf' "página tem token CSRF"

say "2. POST /telas/cadastro.php (pega CSRF de lá)"
body=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" "$BASE_URL/telas/cadastro.php")
csrf=$(echo "$body" | extract_csrf)
[ -n "$csrf" ] || { red "  ✘ não conseguiu extrair CSRF de cadastro.php"; exit 1; }
green "  ✔ CSRF extraído: ${csrf:0:12}..."

say "3. POST /back-end/cadastrar.php (cria conta nova)"
code=$(curl -s -o /dev/null -w "%{http_code}" \
    -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
    -d "_csrf=$csrf" \
    -d "cadastrar=1" \
    -d "usuario=smoke" \
    --data-urlencode "email=$EMAIL" \
    --data-urlencode "senha=$SENHA" \
    "$BASE_URL/back-end/cadastrar.php")
# 302 esperado para /telas/home.php
case "$code" in
    302|303) green "  ✔ cadastrar redirecionou (HTTP $code)" ;;
    *) red "  ✘ cadastrar veio com HTTP $code (esperava 302/303)"; exit 1 ;;
esac

say "4. GET /telas/home.php (deve estar logado)"
body=$(curl -sL -b "$COOKIE_JAR" -c "$COOKIE_JAR" -w "\n%{http_code}" "$BASE_URL/telas/home.php")
code=$(echo "$body" | tail -1)
body=$(echo "$body" | sed '$d')
assert_status "$code" "200" "home autenticado"
assert_contains "$body" 'Adicionar Assunto' "home tem botão de adicionar assunto"
csrf=$(echo "$body" | extract_csrf)
[ -n "$csrf" ] || { red "  ✘ CSRF não encontrado em home"; exit 1; }

say "5. POST /back-end/cadastro_assunto.php"
code=$(curl -s -o /dev/null -w "%{http_code}" \
    -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
    -d "_csrf=$csrf" \
    -d "cadastrar=1" \
    --data-urlencode "titulo=$ASSUNTO" \
    --data-urlencode "resumo=resumo do teste de smoke" \
    -d "pagina=home.php" \
    "$BASE_URL/back-end/cadastro_assunto.php")
case "$code" in
    302|303) green "  ✔ cadastro_assunto redirecionou (HTTP $code)" ;;
    *) red "  ✘ cadastro_assunto veio com HTTP $code"; exit 1 ;;
esac

say "6. GET /telas/home.php (assunto deve aparecer)"
body=$(curl -sL -b "$COOKIE_JAR" "$BASE_URL/telas/home.php")
assert_contains "$body" "$ASSUNTO" "assunto criado aparece em home"

say "7. POST /back-end/logout.php"
csrf=$(echo "$body" | extract_csrf)
code=$(curl -s -o /dev/null -w "%{http_code}" \
    -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
    -d "_csrf=$csrf" \
    "$BASE_URL/back-end/logout.php")
case "$code" in
    302|303) green "  ✔ logout redirecionou (HTTP $code)" ;;
    *) red "  ✘ logout veio com HTTP $code"; exit 1 ;;
esac

say "8. GET /telas/home.php sem cookie (deve redirecionar para login)"
code=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/telas/home.php")
case "$code" in
    302|303) green "  ✔ home redireciona quando não autenticado (HTTP $code)" ;;
    *) red "  ✘ home não redirecionou — HTTP $code (vazamento de tela autenticada)"; exit 1 ;;
esac

echo
green "✅ Todos os checks passaram. Stack está funcional."
echo
echo "Conta criada para o teste: $EMAIL / $SENHA"
echo "(Apague depois com o painel do Postgres ou rode docker compose down -v)"
