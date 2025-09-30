#!/bin/bash

# Script de inicialização para produção
echo "Iniciando aplicação Study Root..."

# Instala PostgreSQL client
apt-get update && apt-get install -y postgresql-client

# Aguarda o banco de dados ficar disponível
echo "Aguardando banco de dados..."
while ! pg_isready -h"$MYSQL_HOST" -p"$MYSQL_PORT" -U"$MYSQL_USERNAME"; do
    echo "Aguardando PostgreSQL..."
    sleep 2
done

echo "Banco de dados disponível!"

# Executa o script SQL para criar as tabelas
echo "Configurando banco de dados..."
PGPASSWORD="$MYSQL_PASSWORD" psql -h"$MYSQL_HOST" -p"$MYSQL_PORT" -U"$MYSQL_USERNAME" -d"$MYSQL_DATABASE" -f /var/www/html/db/study_root_DB_postgresql.sql

echo "Banco de dados configurado!"

# Inicia o Apache
echo "Iniciando Apache..."
apache2-foreground
