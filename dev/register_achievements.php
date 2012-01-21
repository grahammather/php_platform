<?

ini_set('display_errors', '1');
error_reporting(E_ALL);

define("BASEDIR", __DIR__ . '/../');

require '../fatfree-2.0.3/lib/base.php';
require '../lib/rb.php';

F3::set('AUTOLOAD', '../lib/; ../controllers/; ../models/; ../routes/; ../src/');

// REDBEANPHP
R::setup(Cfg::instance()->db_dsn, Cfg::instance()->db_username, Cfg::instance()->db_password);
R::debug(Cfg::instance()->debug_sql);

$ch = curl_init();

$app_id = Cfg::instance()->fb_app_id;
$access_token = $app_id . '|' . Cfg::instance()->fb_app_secret;
$url = "https://graph.facebook.com/$app_id/achievements";

$ac = new AchievementsController();
$achievements = $ac->getData();
foreach ($achievements as $achievement) {

    $post_fields = array('access_token' => $access_token, 'achievement' => Cfg::instance()->master_url . "rest/achievements/" .$achievement->id);
    $curl_opts = array(CURLOPT_POSTFIELDS => $post_fields);
    $curl_opts[CURLOPT_URL] = $url;
    $curl_opts[CURLOPT_HTTPHEADER] = array('Expect:');
    
    curl_setopt_array($ch, $curl_opts);
    $result = curl_exec($ch);
    
    if ($result === FALSE)
        break;
}
