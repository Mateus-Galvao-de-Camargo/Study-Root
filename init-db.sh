#!/bin/bash

# Script de inicialização do banco de dados
echo "Aguardando MySQL ficar disponível..."

# Cria arquivo de configuração para desabilitar SSL
cat > /tmp/mysql.cnf << EOF
[client]
ssl=0
EOF

# Aguarda o MySQL ficar disponível
while ! mysqladmin --defaults-file=/tmp/mysql.cnf ping -h"db" -u"root" -p"kodejifr" --silent; do
    echo "Aguardando MySQL..."
    sleep 2
done

echo "MySQL está disponível!"

# Executa o script SQL para criar as tabelas
echo "Criando tabelas do banco de dados..."
mysql --defaults-file=/tmp/mysql.cnf -h"db" -u"root" -p"kodejifr" study_root < /var/www/html/db/study_root_DB.sql

echo "Banco de dados configurado com sucesso!"
