#!/bin/bash

# This script takes care of starting our WebSockets
#

scriptDirectory=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "${scriptDirectory}/../"

function backgroundTask() {
	if ps aux | grep -v grep | grep "$1" > /dev/null; then
		echo " Running: $1"
		return 0
	fi

	echo "Starting: $1"
	$1 &
}

# Starting the WebSockets
backgroundTask "php bin/CudiBundle/queue.php --run"
backgroundTask "php bin/SportBundle/run.php --run"
backgroundTask "php bin/SyllabusBundle/update.php --run"
