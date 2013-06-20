<?

    $page['template'] = '';

    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    
    ini_set('display_errors', 1);

	ini_set('error_log', '/tmp/phperrors.log');
	ini_set('log_errors',true);


    $url = $_GET['url'];


    $sendheaders = array();
    $sendheaders[] = 'Pragma: ';
    $sendheaders[] = 'X-Forwarded-For: ' . $_SERVER['REMOTE_ADDR'];
	$sendheaders[] = 'HTTP_ACCEPT: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5';
	$sendheaders[] = 'HTTP_ACCEPT_ENCODING: gzip';    
    
    $ch = curl_init();    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $sendheaders);
    curl_setopt($ch, CURLOPT_USERAGENT, $site['user-agent']);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
   if($_SERVER['HTTP_REFERER']){
	    curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
    }
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10 );

    $data = curl_exec($ch);
    $error = curl_error($ch);

    $origUrl = $url;

    $headers = curl_getinfo($ch);
    $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $contenttype = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $ct = explode(';', strtolower($contenttype));
    $type = $ct[0];

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

	$imagetype = array('image/jpeg','image/jpg','image/pjpeg','image/gif','image/png','image/x-icon','image/vnd.microsoft.icon');

    if(!$data || $http_code == '404'){
	
        header("HTTP/1.0 404 Not Found");
        echo '404 Not Found' . "\n";

        echo 'Error: ' . $http_code;

        exit();
	
    }
   
    
   
    $icon = false;
    if(stristr($type, 'ico') || stristr($type, 'text')){
        $icon = true;
    }

    $max_width= 75;
    $max_height = 75;

    if($icon){

        header('Content-type: image/png');
        echo ico2png($data);
        exit();
    
    } else {

        $img = imagecreatefromstring($data);

    }

    if($img===false) {
	    header("HTTP/1.0 404 Not Found");
	    echo '404 Not Found';
       exit();
    }

    $width = imagesx($img);
    $height = imagesy($img);
    
    if($height < 20){
        header('Location: /images/transparent.gif');
        exit();
        
    }

    
    $scale = min($max_width/$width, $max_height/$height);

    if ($scale < 1) {

        $new_width = floor($scale*$width);
        $new_height = floor($scale*$height); 

        $colorTransparent = imagecolortransparent($img);

        if($colorTransparent != -1){
            $new_img = imagecreate($new_width, $new_height);
            imagepalettecopy($new_img,$img);
            imagefill($new_img,0,0,$colorTransparent);
            imagecolortransparent($new_img, $colorTransparent);
        } else {
        	$new_img = imagecreatetruecolor($new_width, $new_height);
        }

        imagecopyresampled($new_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    } else {

	    $new_img = $img;

    }


    header('Cache-Control: max-age=300, must-revalidate');
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");


    if(stristr($type, 'image/jpg') || stristr($type, 'image/jpeg') || stristr($type, 'image/pjpeg')) {

	    header('Content-type: image/jpeg');
	    imagejpeg($new_img);

    } else if(stristr($type, 'image/png')) {

	    header('Content-type: image/png');
	    imagepng($new_img);

    } else if (stristr($type, 'image/gif')) {

	    header('Content-type: image/gif');
        imagegif($new_img);

    } else {
	    header('Content-type: image/jpeg');
	    imagejpeg($new_img);
    }
                                                                                         
    imagedestroy($new_img);
    
    
    
    function ico2png($ico)
    {
        $res = '';

        while(!isset($tmp))
        {
            $tmp = '';

            # get ICONDIR struct & check that it is correct ico format
            $icondir = unpack('sidReserved/sidType/sidCount', substr($ico, 0, 6));
            if ($icondir['idReserved']!=0 || $icondir['idType']!=1 || $icondir['idCount']<1) break;
            $icondir['idEntries'] = array();
            $entry = array();
            for($i=0; $i<$icondir['idCount']; $i++)
            {
                $entry = unpack('CbWidth/CbHeight/CbColorCount/CbReserved/swPlanes/swBitCount/LdwBytesInRes/LdwImageOffset', substr($ico, 6 + $i*16, 16));
                $icondir['idEntries'][] = $entry;
            }
            
       // print_r($icondir);
        //exit();             
            # select need icon & get it raw data
            $iconres = '';
           // $bpx = 1; # bits per pixel
            $idx = 0; # index of need icon
            foreach($icondir['idEntries'] as $k=>$entry)
            {
                //if ($entry['bWidth']==16 && isset($entry['swBitCount']) && $entry['swBitCount']>$bpx && $entry['swBitCount']<33)
                if ($entry['bWidth']==16)
                {
                    $idx = $k;
                    //$bpx = $entry['swBitCount'];
                }
            }            
            $iconres = substr($ico, $icondir['idEntries'][$idx]['dwImageOffset'], $icondir['idEntries'][$idx]['dwBytesInRes']);
            unset($ico);
            unset($icondir);

            # getting bitmap info
            $bitmap_info = array();
            $bitmap_info['header'] = unpack('LbiSize/LbiWidth/LbiHeight/SbiPlanes/SbiBitCount/LbiCompression/LbiSizeImage/LbiXPelsPerMeter/LbiYPelsPerMeter/LbiClrUsed/LbiClrImportant', substr($iconres, 0, 40));

            $bitmap_info['header']['biHeight'] = $bitmap_info['header']['biHeight'] / 2;            
            $number_color = 0;



            if ($bitmap_info['header']['biBitCount'] > 16) 
            {
                $number_color = 0;
                $sizecolor = $bitmap_info['header']['biWidth']*$bitmap_info['header']['biBitCount'] * $bitmap_info['header']['biHeight'] / 8;  
            }
            elseif ( $bitmap_info['header']['biBitCount'] < 16) 
            {
                $number_color = (int) pow(2, $bitmap_info['header']['biBitCount']);
                $sizecolor = $bitmap_info['header']['biWidth']*$bitmap_info['header']['biBitCount'] * $bitmap_info['header']['biHeight'] / 8;  
                if ($bitmap_info['header']['biBitCount']=='1') $sizecolor = $sizecolor * 2;
            }
            else return $res;

            $rgb_table_size =  4 * $number_color;        
            for($i=0; $i<$number_color; $i++)
            {
                $bitmap_info['colors'][] = unpack('CrgbBlue/CrgbGreen/CrgbRed/CrgbReserved', substr($iconres, 40 + $i*4, 4));
            }
            $current_offset = 40 + $number_color * 4;

            $arraycolor = array();

            for($i=0; $i<$sizecolor; $i++) 
            {
                $value = unpack('Cvalue', substr($iconres, $current_offset, 1));
                $arraycolor[] = $value['value'];
                $current_offset++;
            }

            # background alpha is disabled because IE 5.5 + have bug with alpha-channels
            # by default background color is white
            # imagealphablending($im, false);
            # imagefilledrectangle($im, 0, 0, 16, 16, $color);
            # imagealphablending($im, true);
            $im = imagecreatetruecolor(16, 16);
            $color = imagecolorallocate($im, 255, 255, 255);
            imagefill($im, 1, 1, $color);

            # getting mask
            $alpha = '';
            for($i=0; $i<16; $i++)
            {
                $z = unpack('Cx/Cy', substr($iconres, $current_offset, 2));
                $z = str_pad(decbin($z['x']), 8, '0', STR_PAD_RIGHT)  . str_pad(decbin($z['y']), 8, '0', STR_PAD_LEFT);
                $alpha .= $z;
                $current_offset = $current_offset + 4;
            }

            # drawing image
            $ico_size = 16;    
            $off = 0; # range (0-255)




            # cases for different color depth
            switch ($bitmap_info['header']['biBitCount'])    
            {        

                ###################### for 32 bit icons ######################
                case 32:
                    for($y=0; $y<$ico_size; $y++)
                    {
                        for($x=0; $x<$ico_size; $x++)
                        {
                            $a = round((255-$arraycolor[$off*4+3])/2);
                            $a = ($a<0) ? 0 : $a;
                            $a = ($a>127) ? 127 : $a;
                            $color = imagecolorallocatealpha($im, $arraycolor[$off*4+2], $arraycolor[$off*4+1], $arraycolor[$off*4], $a);
                            imagesetpixel($im, $x, $ico_size-1-$y, $color);
                            $off++;
                        }
                    }
                break;

                ###################### for 24 bit icons ######################
                case 24:
                    for($y=0; $y<$ico_size; $y++)
                    {
                        for($x=0; $x<$ico_size; $x++)
                        {
                            $valpha = ($alpha[$off]=='1') ? 127 : 0;
                            $color = imagecolorallocatealpha($im, $arraycolor[$off*3+2], $arraycolor[$off*3+1], $arraycolor[$off*3], $valpha);
                            imagesetpixel ($im, $x, $ico_size-1-$y, $color);
                            $off++;
                        }
                    }
                break;

                ###################### for 08 bit icons ######################
                case 8:
                    for($y=0; $y<$ico_size; $y++)
                    {
                        for($x=0; $x<$ico_size; $x++)
                        {
                            $valpha = ($alpha[$off]=='1') ? 127 : 0;
                            $c = $arraycolor[$off];
                            $c = $bitmap_info['colors'][$c];
                            $color = imagecolorallocatealpha($im, $c['rgbRed'], $c['rgbGreen'], $c['rgbBlue'], $valpha);
                            imagesetpixel ($im, $x, $ico_size-1-$y, $color);
                            $off++;
                        }
                    }
                break;

                ###################### for 04 bit icons ######################
                # 318 = 22 (header) + 40 (bitmap_info) + 16 * 4 (colors) + 128 (pixels) + 64 (mask)
                case 4:
                    for($y=0; $y<$ico_size; $y++)
                    {
                        for($x=0; $x<$ico_size; $x++)
                        {
                            $valpha = ($alpha[$off]=='1') ? 127 : 0;
                            $c = ($arraycolor[floor($off/2)]);
                            $c = str_pad(decbin($c), 8, '0', STR_PAD_LEFT);
                            $m =  (fmod($off+1, 2)==0) ? 1 : 0;
                            $c = bindec(substr($c, $m*4, 4));
                            $c = $bitmap_info['colors'][$c];
                            $color = imagecolorallocatealpha($im, $c['rgbRed'], $c['rgbGreen'], $c['rgbBlue'], $valpha);
                            imagesetpixel ($im, $x, $ico_size-1-$y, $color);
                            $off++;
                        }
                    }
                break;

                ###################### for 01 bit icons ######################
                # 198 = 22 (header) + 40 (bitmap_info) + 2 * 4 (colors) + 64 (pixels, but real 32 needed?) + 64 (mask)
                case 1:
                    for($y=0; $y<$ico_size; $y++)
                    {
                        for($x=0; $x<$ico_size; $x++)
                        {
                            $valpha = ($alpha[$off]=='1') ? 127 : 0;
                            $c = ($arraycolor[floor($off/8)]); # меняем байт каждые 8 пикселей
                            $c = str_pad(decbin($c), 8, '0', STR_PAD_LEFT);
                            $m = fmod($off+8, 8) + 1; # bit number
                            $c = (int) substr($c, $m-1, 1);
                            $c = $bitmap_info['colors'][$c];
                            $color = imagecolorallocatealpha($im, $c['rgbRed'], $c['rgbGreen'], $c['rgbBlue'], $valpha);
                            imagesetpixel ($im, $x, $ico_size-1-$y, $color);
                            $off++;
                        }
                        $off = $off + 16;
                    }            
                break;

                ##############################################################

                default:
                return '';
            }

            # output png
            ob_start();
            # imagesavealpha($im, true);
            imagepng($im);
            imagedestroy($im);
            $res = ob_get_clean();
        }
        return $res;
    }
