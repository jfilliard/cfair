#!/bin/bash

until php /scripts/bin/console.php process --sleep=${PROCESS_SLEEP_DELAY:-5}
do
	echo oops
	sleep 5
done
