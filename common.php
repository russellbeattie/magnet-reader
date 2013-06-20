<?
// ---------------------------------------------
// global vars

    $site['title'] = 'Magnet Reader';
    $site['domain'] = '.magnetdomain.com';
    $site['url'] = 'http://magnetdomain.com/app';
    $site['base'] = '/app';
    $site['logo'] = '/images/icon_small.png';

	$site['dbuser'] = 'DBUSER';
	$site['dbpass'] = 'DBPASSWORD';
    $site['dbhost'] = '127.0.0.1';
    $site['dbname'] = 'myfeeds';


	$site['updates'] = false;

	$site['user-agent'] = 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0; magnetreader)';

	$site['username'] = 'russ';
	$site['password'] = '5d9f71b71b207b9e665820c0dce67bdb';

	define('APP_PATH', realpath(dirname(__FILE__)));

    $debug = false;

    //debug(print_r($_SERVER,true));
    //debug(print_r($_COOKIE,true));

// ---------------------------------------------
// error and logging

	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
	ini_set('display_errors', 1);
	ini_set('error_log', '/tmp/reader_errors.log');
	ini_set('log_errors',true);



// ---------------------------------------------
// definitions and datetime

    date_default_timezone_set('America/Los_Angeles');

// ---------------------------------------------
// database

	$db = new PDO('mysql:host=' . $site['dbhost'] . ';dbname=' . $site['dbname'], $site['dbuser'], $site['dbpass']);
    $db->setAttribute(PDO::ATTR_PERSISTENT, true);
  //  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

// ---------------------------------------------
// utility functions

function checkToken(){

	global $site;

	if($_REQUEST['token'] == getToken()){

	    return true;

	}


	if(!isset($_COOKIE['username']) || !isset($_COOKIE['token'])){

		return false;

	} else {

		$tokencookie = $_COOKIE['token'];

		$token = getToken();

		if($token == $tokencookie){

			return true;

		} else {

			return false;

		}

	}

}

function doLogout(){

    //header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
    setcookie('token', '', time() - 3600, '/');

}

function doLogin($username, $password){

	global $site;

	if($username == $site['username'] && md5($password) == $site['password']){

        $token = getToken();
        if(stripos($_SERVER['HTTP_USER_AGENT'], 'msie') !== false){
            $expires = null;
        } else {
            $expires = time() + 60*60*24*30;
        }

        //header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
        setcookie('username', $site['username'], $expires, '/');
        setcookie('token', $token, $expires, '/');

		return true;

	} else {

		return false;

	}

}


function getToken(){

    global $site;

    $token = md5($site['username'] . $site['password']);

    return $token;

}

function getSaveToLinksScript(){

    global $site;

    $script = "javascript:window.open('" . $site['url'] . "/addLink?close=true&url='+encodeURIComponent(location.href)+'&title='+encodeURIComponent(document.title),'linkwindow','resizable=1,width=800,height=600').moveTo(200,200)";

    return $script;
}


// ----------------------------------------------------


function getSummary($text){

	global $site;

    $text = formatContent($text);

    $doc = new DOMDocument();
    @$doc->loadHTML($text);

    $nodes = $doc->getElementsByTagName('img');

    $src = '';
    $out = '';

    foreach ($nodes as $node) {

        if($node->nodeName == 'img' && $src == ''){
           $w = $node->getAttribute('width');
		   $h = $node->getAttribute('height');
		  if($w == '1' || $h == '1'){

			} else {

			$src = $node->getAttribute('src');
           $p = parse_url($src, PHP_URL_PATH);
           $i = strtolower(pathinfo($p, PATHINFO_EXTENSION));
           if($i == 'jpg' || $i == 'jpeg' || $i == 'gif' || $i == 'png'){

           } else {

               $src = '';
           }
		  }
        }
   /*
        if($node->nodeName == 'div' || $node->nodeName == 'p' ||  $node->nodeName == 'a' || $node->nodeName == 'td' || $node->nodeName == 'li'){
           $out = $out . ' ' . @mb_convert_encoding(htmlspecialchars(trim($node->nodeValue)),'utf-8');
           //$out = $out . ' ' . $node->nodeValue;

            if(strlen($out) > 500){
                break;
            }
        }

	*/

    }

	$out = $text;

    $out = trim(strip_tags($out));

    if(strlen($out) > 500){
        $out = substr($out, 0, 500);
        $last = strrpos($out, '. ');
        if($last){
            $out = substr($out, 0, $last + 1);
        }
    }

    if(strlen($out) < 1){
        $out = '<p>&nbsp;</p>';
    } else {
        $out = '<p>' . $out . '</p>';
    }

    if($src){
        $out = '<img class="thumb" src="' . $site['url'] . '/thumb?url=' . urlencode($src) . '"/> ' . $out;
    }

    //$out = $out . '<pre>' . htmlentities($text) . '</pre>';



    return $out;

}


function formatContent($content){


//    $content = str_replace('<pre>','<div class="pre">',$content);
//    $content = str_replace('</pre>','</div>', $content);


	$config = array(
        'output-xhtml'  => true,
        'hide-comments' => true,
        'quote-nbsp' => true,
        'quote-ampersand' => true,
        'preserve-entities' => true,
        'literal-attributes' => true,
        'drop-font-tags' => true,
        'logical-emphasis' => true,
        'enclose-block-text' => true,
        'enclose-text' => true,
        'quote-marks' => true,
        'char-encoding' => 'utf8',
        'show-body-only' => true
        );

    $text = tidy_repair_string($content, $config);



    $doc = new DOMDocument();
    @$doc->loadHTML($text);

    $nodes = $doc->getElementsByTagName('*');

    foreach ($nodes as $node) {
        if($node->getAttribute('style')){
            $node->removeAttribute('style');
        }
    }

    $html = $doc->saveHTML();

    $html = tidy_repair_string($html, $config);

	return $html;


}

function cleanTitle($text){


    $text = preg_replace('/\s+/', ' ', $text);

    return $text;

}


function get_links($r){

	$r = check_short_urls($r);

	$regexURL = "((http|https|ftp|mailto):\/\/[A-Za-z0-9\.\:\@\?\&\~\%\=\+\-\/\_\;\#]+)";

	$r = preg_replace("/($regexURL\.(png|gif|jpg|PNG|GIF|JPG|jpeg|JPG))(?!\")/","<img src=\"\\1\" />",$r);

	$r = preg_replace("/(?<![\"\[])$regexURL(?!\")/","<a href=\"\\0\" target=\"_blank\" class=\"social-link\">\\0</a>",$r);

	//$r = preg_replace_callback("/(?<![\"\[])$regexURL(?!\")/", 'format_regex_links', $r);

	$r = preg_replace('/(^|\s+)@([A-Za-z0-9_-]{1,64})/e', "'\\1@<a href=\"http://twitter.com/\\2\" class=\"twitter\">\\2</a>'", $r);

	$r = preg_replace('/(^|\s+)#([A-Za-z0-9]{1,64})/e', "'\\1#<a href=\"http://search.twitter.com/search?q=%23\\2\" class=\"twitter\">\\2</a>'", $r);

    $r = ucfirst($r);


    return $r;

}


function check_short_urls($text) {

	$pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';

	return preg_replace_callback($pattern, 'check_short_urls_callback', $text);

}

function check_short_urls_callback($matches) {


	$url = $matches[0];

	$newurl = long_url($url);

	if($url == $newurl){

		debug('check_short_url: ' . $url);

	} else {

		debug('check_short_url: ' . $url . ' -> ' .  $newurl);
}

	return	$newurl;


}


function format_regex_links($links){

	$url = long_url($links[0]);

	$link = '<a href="' . trim($url) . '" target="_blank" class="social-link">' . $url . '</a>';

	return $link;

}


function long_url($url){

    $host = parse_url($url, PHP_URL_HOST);


    if( $host == 'bit.ly' ||
        $host == 'feedproxy.google.com' ||
        $host == 'j.mp' ||
        $host == 't.co'  ||
        $host == 'goo.gl' ||
        $host == 'qr.ae' ||
        $host == 'b.qr.ae' ||
        $host == 'nyti.ms' ||
        $host == 'aol.it' ||
        $host == 'some.ly' ||
        $host == 'tinyurl.com' ||
        $host == 'usat.ly' ||
        $host == 'ow.ly' ||
        $host == 'ht.ly' ||
        $host == 'is.gd' ||
        $host == 'cot.ag' ||
        $host == 'awe.sm' ||
        $host == 'dlvr.it' ||
        $host == 'icio.us' ||
        $host == 'yhoo.it' ||
        $host == '3.ly' ||
        $host == 'delivr.com' ||
        $host == 'shar.es' ) {

			debug('long_url - ' . $url);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)');
            curl_setopt($ch, CURLOPT_FILETIME, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
            //curl_setopt($ch, CURLOPT_VERBOSE, true);

            $response = curl_exec($ch);

            //echo "response: " . $response . PHP_EOL;

            curl_close($ch);

            if(strpos($response, 'Location:') !== false){
                preg_match_all('#Location:\s?([^\s]+)#i', $response, $matches);

                if (isset($matches[1])) {

                    $url = $matches[1][0];

					$url = long_url($url);

                }
             }

        }

        return de_utm($url);

}


function api_long_url($url){

	$host = parse_url($url, PHP_URL_HOST);


	if( $host == 'bit.ly' ||
	    $host == 'feedproxy.google.com' ||
		$host == 'j.mp' ||
		$host == 't.co'  ||
		$host == 'goo.gl' ||
		$host == 'qr.ae' ||
		$host == 'b.qr.ae' ||
		$host == 'nyti.ms' ||
		$host == 'aol.it' ||
		$host == 'some.ly' ||
		$host == 'tinyurl.com' ||
		$host == 'usat.ly' ||
		$host == 'ow.ly' ||
		$host == 'ht.ly' ||
		$host == 'is.gd' ||
		$host == 'cot.ag' ||
		$host == 'awe.sm' ||
		$host == 'dlvr.it' ||
		$host == 'icio.us' ||
		$host == 'yhoo.it' ||
		$host == '3.ly' ||
		$host == 'delivr.com' ||
		$host == 'shar.es' ) {


            $expandUrl = 'http://api.longurl.org/v2/expand?format=json&url=' . urlencode($url);

            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_URL, $expandUrl );
            curl_setopt($curl, CURLOPT_USERAGENT, "russellbeattie; http://www.russellbeattie.com");
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $curl, CURLOPT_TIMEOUT, 20 );

            $jsonString = curl_exec( $curl );

            $info = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close( $curl );

            if($info != '200'){

                //echo $id . ' - ' . $info . ' - ' . $url . PHP_EOL . $jsonString . PHP_EOL;

                return $url;

            }

            $urls = json_decode($jsonString);

            $longUrl = $urls->{'long-url'};

            if($longUrl){
                return de_utm($longUrl);
            } else {
                return de_utm($url);
            }

	} else {

        return de_utm($url);
    }

}

function de_utm($url){

    $query  = parse_url($url, PHP_URL_QUERY);

    if($query !== '' && stripos($query, 'utm_') !== false){
        $var  = html_entity_decode($query);
        $var  = explode('&', $var);
        $arr  = array();

        foreach($var as $val)
        {
            $x = explode('=', $val);
            if(stripos($x[0],'utm_') !== false){
            } else {
                $arr[$x[0]] = $x[1];
            }
        }
        unset($val, $x, $var);

        $q = http_build_query($arr);

        $url = str_replace($query, $q, $url);

        if(substr($url,-1) == '?'){
            $url = substr($url, 0, -1);
        }

    }

    return $url;
 }


function plural($num) {
	if ($num != 1)
		return 's';
}

function getRelativeTime($date) {
	$diff = time() - strtotime($date);
	if ($diff<60)
		return $diff . ' second' . plural($diff) . ' ago';
	$diff = round($diff/60);
	if ($diff<60)
		return $diff . ' minute' . plural($diff) . ' ago';
	$diff = round($diff/60);
	if ($diff<24)
		return $diff . ' hour' . plural($diff) . ' ago';
	$diff = round($diff/24);
	if ($diff<7)
		return $diff . ' day' . plural($diff) . ' ago';
	$diff = round($diff/7);
	if ($diff<4)
		return $diff . ' week' . plural($diff) . ' ago';
	return 'on ' . date('F j, Y', strtotime($date));

}

function debug($message){
  global $debug;

  if($debug == true){
    error_log($message . PHP_EOL, 3, "/tmp/debug.log");
  }

}

function rel2abs($rel, $base){
        /* return if already absolute URL */
        if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;

        /* queries and anchors */
        if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;

        /* parse base URL and convert to local variables:
           $scheme, $host, $path */
        extract(parse_url($base));

        /* remove non-directory element from path */
        $path = preg_replace('#/[^/]*$#', '', $path);

        /* destroy path if relative url points to root */
        if ($rel[0] == '/') $path = '';

        /* dirty absolute URL */
        $abs = "$host$path/$rel";

        /* replace '//' or '/./' or '/foo/../' with '/' */
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

        /* absolute URL is ready! */
        return $scheme.'://'.$abs;
    }


