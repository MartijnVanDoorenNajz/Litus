#!/bin/bash

# A very small wrapper around our test print script
#

scriptDirectory=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "${scriptDirectory}/../../"

APPLICATION_ENV="production" php public/index.php cudi:stock:recalculate "$@"
