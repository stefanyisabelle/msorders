#!/bin/bash

# Script para gerenciar o worker de filas do Laravel
# Uso: ./queue-worker.sh [start|stop|restart|status]

QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
QUEUE_NAME=${QUEUE_NAME:-notifications,default}
WORKER_TRIES=${WORKER_TRIES:-3}
WORKER_TIMEOUT=${WORKER_TIMEOUT:-30}

case "$1" in
    start)
        echo "Iniciando worker de filas..."
        docker-compose exec -d app php artisan queue:work $QUEUE_CONNECTION \
            --queue=$QUEUE_NAME \
            --tries=$WORKER_TRIES \
            --timeout=$WORKER_TIMEOUT \
            --sleep=3 \
            --max-jobs=1000 \
            --daemon
        echo "Worker iniciado!"
        ;;
    
    stop)
        echo "Parando workers..."
        docker-compose exec app php artisan queue:restart
        echo "Workers parados!"
        ;;
    
    restart)
        echo "Reiniciando workers..."
        docker-compose exec app php artisan queue:restart
        sleep 2
        docker-compose exec -d app php artisan queue:work $QUEUE_CONNECTION \
            --queue=$QUEUE_NAME \
            --tries=$WORKER_TRIES \
            --timeout=$WORKER_TIMEOUT \
            --sleep=3 \
            --max-jobs=1000 \
            --daemon
        echo "Workers reiniciados!"
        ;;
    
    status)
        echo "Status das filas:"
        echo ""
        echo "Jobs pendentes:"
        docker-compose exec app php artisan queue:monitor $QUEUE_CONNECTION
        echo ""
        echo "Jobs falhados:"
        docker-compose exec app php artisan queue:failed
        ;;
    
    retry)
        if [ -z "$2" ]; then
            echo "Reprocessando todos os jobs falhados..."
            docker-compose exec app php artisan queue:retry all
        else
            echo "Reprocessando job #$2..."
            docker-compose exec app php artisan queue:retry $2
        fi
        ;;
    
    flush)
        echo "Limpando jobs falhados..."
        docker-compose exec app php artisan queue:flush
        echo "Jobs falhados removidos!"
        ;;
    
    listen)
        echo "Iniciando worker em modo listen (desenvolvimento)..."
        docker-compose exec app php artisan queue:listen $QUEUE_CONNECTION \
            --queue=$QUEUE_NAME \
            --tries=$WORKER_TRIES \
            --timeout=$WORKER_TIMEOUT
        ;;
    
    *)
        echo "Uso: $0 {start|stop|restart|status|retry|flush|listen}"
        echo ""
        echo "Comandos:"
        echo "  start   - Inicia o worker em background"
        echo "  stop    - Para todos os workers"
        echo "  restart - Reinicia todos os workers"
        echo "  status  - Mostra status das filas"
        echo "  retry   - Reprocessa jobs falhados (retry all ou retry <id>)"
        echo "  flush   - Remove todos os jobs falhados"
        echo "  listen  - Inicia worker em modo listen (desenvolvimento)"
        exit 1
        ;;
esac

exit 0
