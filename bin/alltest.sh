#!/bin/bash

set -e

composer lint
composer static
composer test
npm run cypress-run

