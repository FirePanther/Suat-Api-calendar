<?php
/**
 * @author           Suat Secmen (http://suat.be)
 * @copyright        2015 Suat Secmen
 * @license          WTFPL <http://www.wtfpl.net/>
 * @description      add a calendar entry
 */

header('content-type: text/plain');

if (!count($_POST)) {
	// display help
	die('sum: summary (title)
uid: unique id (also necessary for filter)

day: {YYYYMMDD} (whole day)
- OR -
dfrom, dto: {YYYYMMDD} (period)
- OR -
tfrom, tto: {YYYYMMDD}T{HH}[{MM}[{SS}]]
');
}

if (!isset($_POST['sum'])) err('No (required) sum param');
if (!isset($_POST['uid'])) err('No (required) uid param');

$sum = fixnl($_POST['sum']);
$uid = fixnl($_POST['uid']);

if (isset($_POST['day'])) {
	// add whole day entry
	$day = $_POST['day'];
	if (!preg_match('~^\d{8}$~', $day)) err('day doesn\'t match \d{8}');
	$dt = 'DTSTAMP;VALUE=DATE:'.$day.PHP_EOL.
		'DTSTART;VALUE=DATE:'.$day.PHP_EOL.
		'DTEND;VALUE=DATE:'.$day;
} elseif (isset($_POST['dfrom'], $_POST['dto'])) {
	// add entry from day to day
	$dfrom = $_POST['dfrom'];
	$dto = $_POST['dto'];
	if (!preg_match('~^\d{8}$~', $dfrom)) err('dfrom doesn\'t match \d{8}');
	if (!preg_match('~^\d{8}$~', $dto)) err('dto doesn\'t match \d{8}');
	$dt = 'DTSTAMP;VALUE=DATE:'.$dfrom.PHP_EOL.
		'DTSTART;VALUE=DATE:'.$dfrom.PHP_EOL.
		'DTEND;VALUE=DATE:'.$dto;
} elseif (isset($_POST['tfrom'], $_POST['tto'])) {
	// add entry from time to time
	if (!preg_match('~^(\d{8})T(\d{2,6})$~', $_POST['tfrom'], $m))
		err('tfrom doesn\'t match \d{8}T\d{2,6}');
	$dfrom = $m[1];
	$tfrom = $m[2] . str_repeat('0', strlen(6 - $m[2]));
	$from = $dfrom . 'T' . $tfrom;
	if (!preg_match('~^(\d{8})T(\d{2,6})$~', $_POST['tto'], $m))
		err('tto doesn\'t match \d{8}T\d{2,6}');
	$dto = $m[1];
	$tto = $m[2] . str_repeat('0', strlen(6 - $m[2]));
	$to = $dto . 'T' . $tto;
	$dt = 'DTSTAMP:'.$from.PHP_EOL.
		'DTSTART:'.$from.PHP_EOL.
		'DTEND:'.$to;
} else
	err('One time param is required (day OR dfrom & dto OR tfrom & tto)');

$desc = null;
if (isset($_POST['desc'])) {
	$desc = 'DESCRIPTION:'.fixnl($_POST['desc']);
}

$data = 'UID:'.$uid.'@cal.api.suat.de'.PHP_EOL.
	$dt.PHP_EOL.
	'SUMMARY:'.$sum.PHP_EOL.
	$desc;

// save calendar data
file_put_contents('data/cal-'.$uid, $data);

// log and display error message
function err($msg) {
	file_put_contents('last-err', $msg);
	die($msg);
}

// replace new lines with \\n (for calendar)
function fixnl($s) {
	return str_replace(["\r\n", "\r", "\n"], "\\n", $s);
}