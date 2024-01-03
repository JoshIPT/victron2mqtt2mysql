#!/usr/bin/php -q
<?php
require_once("vendor/autoload.php");
require_once(__DIR__."/definitions.php");
use lepiaf\SerialPort\SerialPort;
use lepiaf\SerialPort\Parser\SeparatorParser;
use lepiaf\SerialPort\Configure\TTYConfigure;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

/** Configuration **/
$server = "localhost";
$port = 1883;
$clientId = "victron";
$username = "victron";
$password = "victron";
$serialPort = "/dev/ttyUSB0";
$baudRate = "19200";
/** End Configuration **/

$settings = (new ConnectionSettings)
	->setUsername($username)
	->setPassword($password)
	->setReconnectAutomatically(true)
	->setDelayBetweenReconnectAttempts(1000)
	->setUseTls(false);

$mqtt = new MqttClient($server, $port, $clientId);

$mqtt->connect($settings, false);

$tty = new TTYConfigure();
$tty->setOption($baudRate);

$serial = new SerialPort(new SeparatorParser("\n"), $tty);
try {
	$serial->open($serialPort);
} catch (Exception $ex) { die("Failed to open {$serialPort}: ".$ex->getMessage()); }

$buffer = array();

const START_BIT = "Checksum";
const END_BIT = "HSDS";

while (true) {
	while ($data = $serial->read()) {
		if (substr($data, 0, 8) === START_BIT) {
			//print "Found start bit\n";
			$buffer = array();
		}
		else if (substr($data, 0, 4) == END_BIT) {
			//print "Found end bit\n";
			processMessages($mqtt, $buffer);
		}
		else {
			$buffer[] = $data;
		}
	}
}

function processMessages($mqtt, $messages) {
	$definitions = new VictronDefinitions();

	foreach ($messages as $message) {
		if (!strstr($message, ":")) {
			list($key, $val) = explode("\t", $message);

			$key = $definitions->mapVeLookup($key);
			if (!is_array($key)) {
				$type = VE_TYPE_TXT_TEXT;
				$multiplier = 1.0;
				$suffix = "";
			}
			else {
				$type = $key[2];
				$multiplier = $key[3];
				$suffix = $key[4];
			}

			if ($type == VE_TYPE_TXT_FLOAT) {
				$val = number_format(floatval($val) * $multiplier, 3);
			}
			else if ($type == VE_TYPE_TXT_INT) {
				$val = intval($val);
			}
			else if ($type == VE_TYPE_TXT_BOOL) {
				if ($val == "1") { $val = "true"; }
				else { $val = "false"; }
			}

			if ($key[0] == "charge_state") {
				$val = $definitions->chargeState[intval($val)];
			}
			else if ($key[0] == "mppt_state") {
				$val = $definitions->mpptCodes[intval($val)];
			}
			else if ($key[0] == "off_reason") {
				$val = $definitions->offCodes[$val];
			}
			else if ($key[0] == "error") {
				$val = $definitions->errorCodes[intval($val)];
			}

			$mqtt->publish("victron/{$key[0]}", "{$val}{$suffix}", 0);
			//print "{$key[0]}: {$val}{$suffix}\n";
		}
	}
}

$mqtt->disconnect();
?>
