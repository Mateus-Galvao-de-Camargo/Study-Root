#!/usr/bin/env bash
# Entrypoint do container Study Root.
# - Ajusta Apache pra escutar na porta vinda da plataforma (Render usa $PORT)
# - Roda as migrações antes de subir o servidor
# - Repassa o comando original (apache2-foreground por padrão)

set -euo pipefail

PORT="${PORT:-8080}"

# Aponta DocumentRoot e Listen para a porta esperada.
sed -ri "s!Listen 80!Listen ${PORT}!g" /etc/apache2/ports.conf
sed -ri "s!:80>!:${PORT}>!g"          /etc/apache2/sites-available/000-default.conf

# Permite ".." dentro do projeto sem expor estrutura de pastas indevidamente.
# DocumentRoot fica em /var/www/html.
echo "ServerName 0.0.0.0" >> /etc/apache2/apache2.conf

# Roda o migrate; se falhar, aborta (melhor o deploy falhar do que ficar no ar quebrado).
if [ -n "${DATABASE_URL:-}${DB_HOST:-}" ]; then
    echo "[entrypoint] Rodando migrações..."
    php /var/www/html/back-end/migrate.php
else
    echo "[entrypoint] Sem DATABASE_URL ou DB_HOST definidos — pulando migrações."
fi

exec "$@"
