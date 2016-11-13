<?php # -*- coding: utf-8, tab-width: 2 -*-

require BASEPATH . 'default-config.php';

$cfg['*']['proxy_spec'] = 'http://localhost:3128/';
$cfg['*']['min-uts'] = strtotime('2016-11-10 21:15');

$cfg['gitlab-ce'] = array(
  'baseurl' => 'https://packages.gitlab.com/gitlab/gitlab-ce/ubuntu/',
  # won't help as long as their amazon serves empty package lists like
  # http://web.archive.org/web/20161113220501/https://packages.gitlab.com/gitlab/gitlab-ce/ubuntu/dists/trusty/main/binary-i386/Packages
);

$cfg['passenger'] = array(
  'baseurl' => 'https://oss-binaries.phusionpassenger.com/apt/passenger/',
  # won't help as long as they just ban all robots.
);







# no closing PHP tag: prevent accidential whitespace.
