<?php # -*- coding: utf-8, tab-width: 2 -*-

Header('Content-Type: text/plain');
define('BASEPATH', dirname(__FILE__) . '/');

$debuglv = 0;

function fail($msg, $status = 500) {
  http_response_code($status);
  echo "E: $msg\n";
  exit(0);
}

function expect($a, $b, $err) { if ($a !== $b) { fail($err); } }

expect(true, chdir(BASEPATH), 'failed to chdir');

$rqpath = (string)@$_SERVER['PATH_INFO'];
$rqpath = explode('/', ltrim($rqpath, '/'));
$rqrepo = (string)@array_shift($rqpath);

require BASEPATH . 'local-config.php';

function cfgv($key) {
  global $cfg, $rqrepo;
  if (isset($cfg[$rqrepo][$key])) { return $cfg[$rqrepo][$key]; }
  if (isset($cfg['*'][$key])) { return $cfg['*'][$key]; }
  fail($key . ' not configured for this repo');
}


expect(true, is_array(@$cfg[$rqrepo]), 'repo not configured');
$debuglv = cfgv('debug');
expect(true, is_int($debuglv), 'debug level must be int');
$baseurl = cfgv('baseurl');

$max_age = (int)strtotime('+' . cfgv('max-age'), 0);
expect(true, ($max_age >= 3600), 'max_age too low. use min-uts instead.');
$min_uts = time() - $max_age;
$min_uts = max($min_uts, (int)strtotime(cfgv('min-uts'), 0));

$curl_proxy = cfgv('proxy_spec');
foreach (cfgv('proxy_vars') as $proxy_var) {
  putenv("$proxy_var=$curl_proxy");
  expect((string)@getenv($proxy_var), $curl_proxy,
    'failed to set env var ' . $proxy_var);
}

$fnrgx = '!^[' . cfgv('filename-chars') . ']+$!';
$fileurl = '';
foreach ($rqpath as $pathstep) {
  if ($pathstep === '') { continue; }
  expect(false, (substr($pathstep, 0, 1) === '.'),
    'path component cannot start with dot');
  if (preg_match($fnrgx, $pathstep, $match)) {
    $pathstep = $match[0];
  } else {
    fail('unsupported path component after "' . $fileurl . '/"');
  }
  $fileurl .= '/' . $pathstep;
}
$fileurl = $baseurl . ltrim($fileurl, '/');

$uagent = (string)@$_SERVER['HTTP_USER_AGENT'];
if (!empty($uagent)) { putenv("CURL_USERAGENT=$uagent"); }

if ($debuglv >= 1) {
  print_r(array(
    'uagent'    => $uagent,
    'file url'  => $fileurl,
    'min_date'  => date('c = r', $min_uts),
    ));
}

putenv("WAYBACK_FILE_URL=$fileurl");
$wbm_head = array();
$retval = 'skip';
if ($debuglv <= 1) {
  exec('bash curl-util.sh datefmt_ini_env 2>&1', $wbm_head, $retval);
}
array_push($wbm_head, "CURL-RV=$retval");

if ($debuglv > 0) { print_r($wbm_head); }
$wbm_head = parse_ini_string(implode("\n", $wbm_head));
$wbm_status = (int)substr((string)@$wbm_head['HTTP-Status'], 0, 3);
$wbm_err = (string)@$wbm_head['X-Archive-Wayback-Runtime-Error'];
$wbm_uts = (int)@strtotime(@$wbm_head['Timestamp-Latest']);
$wbm_fresh = ($wbm_uts >= $min_uts);

$redir_url = '';
if ($wbm_status === 302) {
  $redir_url = (string)@$wbm_head['Location'];
  expect(substr($redir_url, 0, 5), '/web/', 'Unexpected redirect URL');
} elseif ($wbm_status === 404) {
  $redir_url = '/save/_embed/' . $fileurl;
  $wbm_err = '';
}

if ($redir_url !== '') {
  $redir_url = 'http://web.archive.org' . $redir_url;
}



if ($debuglv > 0) {
  $wbm_head['Wayback-UTS'] = $wbm_uts . ',' . ($wbm_fresh ? 'fresh' : 'stale');
  $wbm_head['Redir-URL'] = $redir_url;
  print_r($wbm_head);
}

if (($wbm_status !== 302) && ($wbm_status !== 404) && ($wbm_status >= 400)) {
  fail("HTTP $wbm_status, Wayback Runtime Error: $wbm_err", $wbm_status);
}

if ($wbm_fresh) {
  expect($wbm_status, 302, "Unexpected HTTP status (fresh): $wbm_status");
} else {
  expect($wbm_status, ($wbm_uts === 0 ? 404 : 302),
    "Unexpected HTTP status (stale): $wbm_status");
}

if ($debuglv !== 0) { exit(0); }
foreach (array('Date') as $hdr) {
  if (!empty($wbm_head[$hdr])) {
    Header($hdr . ': ' . $wbm_head[$hdr]);
  }
}
Header('Location: ' . $redir_url);



# np2
