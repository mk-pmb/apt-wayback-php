#!/bin/bash
# -*- coding: utf-8, tab-width: 2 -*-


function curl_datefmt () {
  local CURL_OPTS=(
    --head
    --silent
    --max-time 10
    --retry 2
    )
  local URL_PRFX='http://web.archive.org/web/99991239246060/'
  local ORIG_URL=
  for ORIG_URL in "$@"; do
    echo "# $ORIG_URL"
    curl "${CURL_OPTS[@]}" -- "$URL_PRFX$ORIG_URL"
  done | sed -re '
    s~\s+$~~
    /^$/d
    s~^(Server|Content-Length|(Proxy-|)Connection|Set-Cookie):\s~\r~i
    s~^Location: /web/([0-9]{4})([0-9]{4})([0-9]{6})/.*$|$\
      ~&\nTimestamp-Latest: \1-\2-\3~i
    /^\r/d
    s~((^Link|\bResourceNotInArchiveException): <?\S{20}|$\
      )\S*(\S{20}>?;?\s)~\1…\3~
    '
  return 0
}
















[ "$1" == --lib ] && return 0; curl_datefmt "$@"; exit $?
