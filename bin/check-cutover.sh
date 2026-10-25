#!/bin/bash

# Shows where each of the site's domains currently points, during the move from One.com to Laravel Cloud.
#
#   bin/check-cutover.sh                      # check once
#   bin/check-cutover.sh --watch 60           # repeat every 60 seconds
#   bin/check-cutover.sh example.se ...       # check other domains than the default ones
#
# For each name (apex and www) it shows the A/AAAA records from One.com's own nameserver (the truth, with TTL)
# and from a few public resolvers (what visitors get, with the remaining cache time). It then requests the page
# over HTTPS and prints the status code and the X-Served-By header (SERVED_BY in .env on each server).
#
# Requires dig and curl. See docs/laravel-cloud-migration.md.

set -uo pipefail

DOMAINS=(goteborgkk.se gkk-styrkelyft.se goteborgkraftsportklubb.se)
RESOLVERS=(ns01.one.com 1.1.1.1 8.8.8.8 9.9.9.9)
WATCH=0

while [ $# -gt 0 ]; do
    case "$1" in
        --watch) WATCH="${2:?--watch needs a number of seconds}"; shift 2 ;;
        -h|--help) sed -n '3,13p' "$0"; exit 0 ;;
        *) [ "${CUSTOM:-}" ] || { DOMAINS=(); CUSTOM=1; }; DOMAINS+=("$1"); shift ;;
    esac
done

if [ -t 1 ]; then
    OLD=$'\e[33m'; NEW=$'\e[32m'; DIM=$'\e[2m'; BOLD=$'\e[1m'; RESET=$'\e[0m'
else
    OLD=''; NEW=''; DIM=''; BOLD=''; RESET=''
fi

# One.com's addresses are in 46.30.0.0/16 and 2a02:2350::/32
label() {
    case "$1" in
        46.30.*|2a02:2350:*) echo "${OLD}$1 (One.com)${RESET}" ;;
        *) echo "${NEW}$1${RESET}" ;;
    esac
}

lookup() {
    local resolver="$1" name="$2" type="$3" records
    records=$(dig +noall +answer +time=3 +tries=1 "@$resolver" "$name" "$type" 2>/dev/null | awk -v t="$type" '$4 == t { print $2, $5 }')

    if [ -z "$records" ]; then
        echo "${DIM}none${RESET}"
        return
    fi

    while read -r ttl value; do
        printf '%s %sttl %ss%s  ' "$(label "$value")" "$DIM" "$ttl" "$RESET"
    done <<< "$records"
    echo
}

check() {
    echo "${BOLD}$(date '+%Y-%m-%d %H:%M:%S')${RESET}"

    for domain in "${DOMAINS[@]}"; do
        for name in "$domain" "www.$domain"; do
            echo
            echo "${BOLD}$name${RESET}"

            for resolver in "${RESOLVERS[@]}"; do
                printf '  %-13s A     %s\n' "$resolver" "$(lookup "$resolver" "$name" A)"
                printf '  %-13s AAAA  %s\n' "" "$(lookup "$resolver" "$name" AAAA)"
            done

            local result
            result=$(curl -s -o /dev/null -D - --max-time 10 "https://$name/" 2>/dev/null \
                | awk 'NR == 1 { status = $2 } tolower($1) == "x-served-by:" { server = $2 } tolower($1) == "location:" { location = $2 }
                       END { gsub(/\r/, "", server); gsub(/\r/, "", location); printf "%s served-by=%s%s", status ? status : "failed", server ? server : "-", location ? " -> " location : "" }')
            printf '  %-13s %s\n' "https" "$result"
        done
    done
}

while true; do
    check

    [ "$WATCH" -gt 0 ] || break
    sleep "$WATCH"
    echo
    echo "────────────────────────────────────────────────────────────"
done
