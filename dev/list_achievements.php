<?
define("BASEDIR", __DIR__ . '/../');
require '../lib/rb.php';
require '../lib/Singleton.php';
require '../lib/Cfg.php';

$ch = curl_init();

$app_id = Cfg::instance()->fb_app_id;
$access_token = $app_id . '|' . Cfg::instance()->fb_app_secret;
$url = "https://graph.facebook.com/$app_id/achievements?access_token=$access_token";
$curl_opts[CURLOPT_URL] = $url;

curl_setopt_array($ch, $curl_opts);
$result = curl_exec($ch);

echo "\n\n\n";
echo $result;

