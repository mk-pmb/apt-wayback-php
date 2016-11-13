<?php # -*- coding: utf-8, tab-width: 2 -*-

# Default config.
# use config.local.php to override settings with your preferred values.

$cfg = array();

# default repo
# ============
#
# All repos inherit from the "*" repo.

$cfg['*'] = array(
  'baseurl' => NULL,
  # Only use "*" as a repo ID if you wear sunglasses on the back of your head.

  # To be considered fresh, files must be younger than max_age
  # AND must be newer than min_uts (unix timestamp).
  'max-age' => '5 days',      # strtotime("+$max_age")
  'min-uts' => 0,

  'filename-chars' => "A-Za-z0-9_\\-\\.",

  # Debug mode
  # ==========
  #   0 = normal operation
  #   1 = debug curl script
  #   2 = debug PATH_INFO and config, skip curl
  'debug' => 0,

  # Proxy Settings
  # ==============
  #
  # All of these env vars will be set to a custom proxy setting in order to
  # defend against injected client-chosen values sent as HTTP headers:
  'proxy_vars' => array(
    'http_proxy',
    'HTTP_PROXY',
    'https_proxy',
    'HTTPS_PROXY',
  ),
  # Which value to set for the proxy_vars. Empty = no proxy.
  'proxy_spec' => '',
);


# example repo
# ============
#
$cfg['example'] = array(
  'baseurl' => 'https://example.net/deb/ubuntu/',
  # makes http://apt-wayback.local/taster.php/example/dists/xenial/Release.gz
  # check https://example.net/deb/ubuntu/dists/xenial/Release.gz
  # Usually you'll want the last character to be a slash.
);


require BASEPATH . 'config.local.php';
