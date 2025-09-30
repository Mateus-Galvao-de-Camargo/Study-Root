#!/bin/bash

# Script de inicialização para produção
echo "Iniciando aplicação Study Root..."

# Aguarda o banco de dados ficar disponível
echo "Aguardando banco de dados..."
while ! mysqladmin ping -h"$MYSQL_HOST" -u"$MYSQL_USERNAME" -p"$MYSQL_PASSWORD" --silent; do
    echo "Aguardando MySQL..."
    sleep 2
done

echo "Banco de dados disponível!"

# Executa o script SQL para criar as tabelas
echo "Configurando banco de dados..."
mysql -h"$MYSQL_HOST" -u"$MYSQL_USERNAME" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" < /var/www/html/db/study_root_DB.sql

echo "Banco de dados configurado!"

# Inicia o Apache
echo "Iniciando Apache..."
apache2-foreground
