#!/bin/sh

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

echo $DIR

docker rm -f mic_cms_ms_api
docker-compose rm

docker-compose build mic_cms_ms_api
WEB_ID=$(docker-compose up -d mic_cms_ms_api)

sleep 3

docker exec -it mic_cms_ms_api sh /start_script.sh

sleep 3

docker exec -it mic_cms_ms_api sh /etc/init.d/apache2 start

# docker exec -it mic_cms_ms_api composer install

