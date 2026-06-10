#!/bin/bash
set -e

# Disable conflicting MPMs and ensure mpm_prefork is enabled for mod_php compatibility
a2dismod mpm_event || true
a2dismod mpm_worker || true
a2enmod mpm_prefork

# Execute the default container command
exec "$@"
