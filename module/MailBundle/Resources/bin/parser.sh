#!/bin/bash

# A very small wrapper around our e-mail parser
#

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "$SCRIPT_DIRECTORY/../../"

php public/index.php mail:parse
