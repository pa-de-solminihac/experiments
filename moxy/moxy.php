<?php
// TODO : fake urls, sending parameters by other ways... cookies, referer, useragent, post...
// TODO : use SSL whenever possible (against DPI)

define ('__DEBUG__', 0);
define ('__CHUNKSIZE__', 8192);

// errors control : any error should be fatal. if __DEBUG__ errors will be output
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
function myerrhandler($errno, $errstr, $errfile, $errline) {
    if (__DEBUG__) {
        echo "\nerror [$errno] $errstr in\n$errfile:$errline";
    }
    die();
}
set_error_handler('myerrhandler');

// time control
ini_set('max_execution_time', 0);
set_time_limit(0);

// gets url if possible
$url = '';
if (isset($_GET['u'])) {
    $url = base64_decode($_GET['u']);
}
// print form if no url
if (!isset($_GET['u']) || !$url) {
    if (version_compare(PHP_VERSION, '5.3.4', '<')) {
        echo 'Warning : a bug in PHP versions prior to 5.3.4 will make downloads fail to resume.';
    }
?>
    <form method="post" action="">
        <input type="text" name="url" />
        <input type="submit" />
    </form>
<?php
    // prints command to execute
    $base_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?u=';
    if (isset($_POST['url'])) {
        $realurl = $_POST['url'];
        echo 'wget -c ' . $base_url . base64_encode($_POST['url']) . ' -O ' . basename($realurl);
    }
    die();
} else {

    // get remote file size
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // not necessary unless the file redirects
    $data = curl_exec($ch);
    curl_close($ch);
    if ($data === false) {
        die();
    }
    $contentLength = 'unknown';
    $status = 'unknown';
    $matches = array();
    if (preg_match('/^HTTP\/1\.[01] (\d\d\d)/', $data, $matches)) {
        $status = (int)$matches[1];
    }
    if (preg_match('/Content-Length: (\d+)/', $data, $matches)) {
        $contentLength = (int)$matches[1];
    }

    // resumable download ?
    $size = $contentLength;
    if (isset($_SERVER['HTTP_RANGE'])) {
        list($size_unit, $range_orig) = explode('=', $_SERVER['HTTP_RANGE'], 2);

        if ($size_unit == 'bytes') {
            //multiple ranges could be specified at the same time, but for simplicity only serve the first range
            //http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt
            list($range, $extra_ranges) = explode(',', $range_orig, 2);
        } else {
            $range = '';
        }
    } else {
        $range = '';
    }

    //figure out download piece from range (if set)
    if (is_array($range)) {
        list($seek_start, $seek_end) = explode('-', $range, 2);
    } else {
        $seek_start = 0;
        $seek_end = 0;
    }

    //set start and end based on range (if set), else set defaults
    //also check for invalid ranges.
    $seek_end = (empty($seek_end)) ? ($size - 1) : min(abs(intval($seek_end)),($size - 1));
    $seek_start = (empty($seek_start) || $seek_end < abs(intval($seek_start))) ? 0 : max(abs(intval($seek_start)),0);

    //add headers if resumable
    //Only send partial content header if downloading a piece of the file (IE workaround)
    if ($seek_start > 0 || $seek_end < ($size - 1))
    {
        header('HTTP/1.1 206 Partial Content');
    } else {
        header('HTTP/1.1 200 OK');
    }

    header('Accept-Ranges: bytes');
    header('Content-Range: bytes '.$seek_start.'-'.$seek_end.'/'.$size);

    //headers for IE Bugs (is this necessary?)
    //header("Cache-Control: cache, must-revalidate");  
    //header("Pragma: public");

    // header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: '.($seek_end - $seek_start + 1));

    // begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Type: application/force-download");

    // force the download
    header("Content-Disposition: attachment; filename=" . basename($url) . ";");
    header("Content-Transfer-Encoding: binary");
    // header("Content-Length: " . $contentLength);

    // buffering control
    ob_start();

    // gets the url
    $handle = fopen($url, 'rb');

    // seek to start of missing part : stream_get_contents can seek in streams, where fseek cant
    if ($seek_start) {
        stream_get_contents($handle, 1, ($seek_start - 1));
    }

    while (!feof($handle)) {
        // time control
        set_time_limit(0);

        echo fread($handle, __CHUNKSIZE__);

        // buffering control
        ob_flush();
        flush();
    }
    fclose($handle);

    // buffering control
    ob_end_flush();
}
?>
