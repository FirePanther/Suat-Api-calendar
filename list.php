<?php
/**
 * @author           Suat Secmen (http://suat.be)
 * @copyright        2015 Suat Secmen
 * @license          WTFPL <http://www.wtfpl.net/>
 * @description      list all calendar entries (implement this into the calendar)
 */

header('content-type: text/plain');
echo 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:TCHost.de
CALSCALE:GREGORIAN
METHOD:PUBLISH
X-WR-CALNAME:[A] Suat-API
X-WR-TIMEZONE:Europe/Berlin
X-WR-CALDESC:
';

$filter = '';
if (isset($_GET['filter']) && preg_match('~^[\w-]+$~', $_GET['filter'])) {
	$filter = $_GET['filter'];
}

$data = glob('data/cal-'.$filter.'*');
foreach ($data as $d) {
	echo 'BEGIN:VEVENT'.PHP_EOL.file_get_contents($d).PHP_EOL.'END:VEVENT'.PHP_EOL;
}

echo 'END:VCALENDAR';