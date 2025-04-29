#!/bin/bash

DB_NAME="petamicr"
DB_USER="admin"
DB_PASSWORD="121010"
SQL_DIR="/var/www/html/petamicr.loc/db_backup/loc_full__2025_03_10_12_02_26"

for sql_file in "$SQL_DIR"/*.sql; do
    if [ -f "$sql_file" ]; then
        echo "Выполнение $sql_file..."
        mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < "$sql_file"
        if [ $? -eq 0 ]; then
            echo "$sql_file выполнен успешно."
        else
            echo "Ошибка при выполнении $sql_file."
        fi
    fi
done

echo "Выполнение всех SQL-файлов завершено."
