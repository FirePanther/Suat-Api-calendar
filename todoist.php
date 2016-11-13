<?php
/**
 * @author           Suat Secmen (http://suat.be)
 * @copyright        2015 Suat Secmen
 * @license          WTFPL <http://www.wtfpl.net/>
 * @description      modify and then list todoist calendar entries (implement this into the calendar)
 */
require 'conf.php';
header('content-type: text/plain');
$source = file_get_contents($todoistIcalFeed);

// replace urls with url schemes (for iPhone)
$source = preg_replace('~http:\/\/todoist\.com\/\#project\/(\d+)~', 'todoist://project?id=$1', $source);

echo $source;
