#!/bin/bash

set -e

composer lint
composer static
composer test
npm run test:unit
npm run test:e2e
