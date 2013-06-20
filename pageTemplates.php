<?

	$page['template'] = '';
	
	$dir = dirname(__FILE__) . '/tpl';
	
    $files = new DirectoryIterator($dir);

    foreach($files as $fileinfo){

        if ($fileinfo->isFile()) {
        	$filename = $fileinfo->getFilename();
        	
        	$name = substr($filename,0, -4);
        	
        	echo '<script id="' . $name . 'Template" type="text/html">' . PHP_EOL;
        	
        	readfile($dir . '/' . $filename);
        	
        	echo '</script>' . PHP_EOL . PHP_EOL;
        	
        	
        	
        }
    }


