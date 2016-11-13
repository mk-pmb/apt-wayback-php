#!/bin/bash
# -*- coding: utf-8, tab-width: 2 -*-


function curl_datefmt () {
  local URLS=( "$@" )
  [ "$1" == --env ] && URLS=( "$WAYBACK_FILE_URL" )
  local UA_ACNG='Debian Apt-Cacher-NG/0.7.2'
  local CURL_OPTS=(
    --head
    --silent
    --max-time 10
    --retry 2
    --user-agent "${CURL_USERAGENT:-$UA_ACNG}"
    )
  local URL_PRFX='http://web.archive.org/web/99991239246060/'
  local ORIG_URL=
  for ORIG_URL in "${URLS[@]}"; do
    echo "Orig-URL: $ORIG_URL"
    case "$ORIG_URL" in
      'http://'* | 'https://'* )
        curl "${CURL_OPTS[@]}" -- "$URL_PRFX$ORIG_URL" || return $?;;
      * ) echo 'HTTP/1.0 400 Bad Request';;
    esac
  done | sed -re '
    s~\s+$~~
    /^$/d
    s~^(Server|Content-Length|(Proxy-|)Connection|Set-Cookie):\s~\r~i
    /^\r/d

    s~^(HTTP)/(\S+)\s+~\1-Version: \2\n\1-Status=~
    s~^Location: /web/([0-9]{4})([0-9]{2})([0-9]{2}|<<$date\
      )([0-9]{2})([0-9]{2})([0-9]{2}|<<$time\
      )/.*$~&\nTimestamp-Latest=\1-\2-\3T\4:\5:\6Z~i
    s~((^Link|\bResourceNotInArchiveException): <?\S{20}|$\
      )\S*(\S{20}>?;?\s)~\1[...]\3~
    s~:\s*~=~
    '
  return 0
}
















[ "$1" == --lib ] && return 0; curl_datefmt "$@"; exit $?
