#!/bin/bash


sleep 3
echo "Start mongo container"
	mongoimport --db cms --collection schemas --drop --file /data/db_import/schemas.json
    mongoimport --db cms --collection schemas --drop --file /data/db_import/schemas.json
echo "End mongo container"



sleep 2
echo "Start mongo container"
    mongoimport --db cms --collection contents --drop --file /data/db_import/contents.json
echo "End mongo container"
