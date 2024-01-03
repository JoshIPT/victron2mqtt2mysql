<?php
const VE_TYPE_TXT_FLOAT = "float";
const VE_TYPE_TXT_INT = "int";
const VE_TYPE_TXT_BOOL = "bool";
const VE_TYPE_TXT_TEXT = "text";

class VictronDefinitions {
	public $veLookup = array(
		array("main_voltage",           "V",        VE_TYPE_TXT_FLOAT,  0.001,  "V"     ), // Main of channel 1 (battery) voltage
		array("current_fine",           "I",        VE_TYPE_TXT_FLOAT,  0.001,  "A"     ), // Main of channel 1 (battery) current
		array("power",                  "P",        VE_TYPE_TXT_INT,    1.0,    "W"     ), // Instantaneous power
		array("load_current",           "IL",       VE_TYPE_TXT_FLOAT,  0.001,  "A"     ), // Load current
    		array("pv_voltage",             "VPV",      VE_TYPE_TXT_FLOAT,  0.001,  "V"     ), // PV voltage
    		array("pv_power",               "PPV",      VE_TYPE_TXT_INT,    1.0,    "W"     ), // PV power
    		array("error",                  "ERR",      VE_TYPE_TXT_INT,    1.0,    ""      ), // Error code
    		array("charge_state",           "CS",       VE_TYPE_TXT_INT,    1.0,    ""      ), // Charge state code
    		array("consumed_ah",            "CE",       VE_TYPE_TXT_FLOAT,  0.001,  "Ah"    ), // Consumed Ah
    		array("soc",                    "SOC",      VE_TYPE_TXT_FLOAT,  0.1,    "%"     ), // State-of-charge
    		array("ttg",                    "TTG",      VE_TYPE_TXT_INT,    1.0,    "Min"   ), // Time-to-go
    		array("alarm_state",            "Alarm",    VE_TYPE_TXT_BOOL,   1.0,    ""      ), // Alarm condition active
    		array("relay_state",            "Relay",    VE_TYPE_TXT_BOOL,   1.0,    ""      ), // Relay state
    		array("alarm_reason",           "AR",       VE_TYPE_TXT_INT,    1.0,    ""      ), // Alarm reason
    		array("sw_version",             "FW",       VE_TYPE_TXT_INT,    1.0,    ""      ), // Firmware version
    		array("max_discharge",          "H1",       VE_TYPE_TXT_FLOAT,  0.001,  "Ah"    ), // Depth of deepest discharge
    		array("last_discharge",         "H2",       VE_TYPE_TXT_FLOAT,  0.001,  "Ah"    ), // Depth of last discharge
    		array("average_discharge",      "H3",       VE_TYPE_TXT_FLOAT,  0.001,  "Ah"    ), // Depth of average discharge
    		array("num_cycles",             "H4",       VE_TYPE_TXT_INT,    1.0,    ""      ), // Number of charge cycles
    		array("num_full_discharge",     "H5",       VE_TYPE_TXT_INT,    1.0,    ""      ), // Number of full discharges
    		array("cumulative_ah",          "H6",       VE_TYPE_TXT_FLOAT,  0.001,  "Ah"    ), // Cumulative Ah drawn
    		array("min_voltage",            "H7",       VE_TYPE_TXT_FLOAT,  0.001,  "V"     ), // Minimum main (battery) voltage
    		array("max_voltage",            "H8",       VE_TYPE_TXT_FLOAT,  0.001,  "V"     ), // Maximum main (battery) voltage
    		array("time_since_full_charge", "H9",       VE_TYPE_TXT_INT,    1.0,    "Sec"   ), // Number of seconds since last full charge
    		array("num_auto_sync",          "H10",      VE_TYPE_TXT_INT,    1.0,    ""      ), // Number of automatic synchronizations
    		array("num_low_volt_alarm",     "H11",      VE_TYPE_TXT_INT,    1.0,    ""      ), // Number of low main voltage alarms
    		array("num_high_volt_alarm",    "H12",      VE_TYPE_TXT_INT,    1.0,    ""      ), // Number of high main voltage alarms
    		array("energy_discharged",      "H17",      VE_TYPE_TXT_FLOAT,  0.01,   "kWh"   ), // Amount of discharged energy
    		array("energy_charged",         "H18",      VE_TYPE_TXT_FLOAT,  0.01,   "kWh"   ), // Amount of charged energy
    		array("energy_total",           "H19",      VE_TYPE_TXT_FLOAT,  0.01,   "kWh"   ), // Energy total
    		array("energy_today",           "H20",      VE_TYPE_TXT_FLOAT,  0.01,   "kWh"   ), // Energy today
    		array("max_power_today",        "H21",      VE_TYPE_TXT_FLOAT,  1.0,    "W"     ), // Max power today
    		array("energy_yesterday",       "H22",      VE_TYPE_TXT_FLOAT,  0.01,   "kWh"   ), // Energy yesterday
    		array("max_power_yesterday",    "H23",      VE_TYPE_TXT_FLOAT,  1.0,    "W"     ), // Max power yesterday
    		array("mppt_state",             "MPPT",     VE_TYPE_TXT_INT,    1.0,    ""      ), // MPPT State
    		array("load_state",             "LOAD",     VE_TYPE_TXT_TEXT,   1.0,    ""      ), // Load State
    		array("off_reason",             "OR",       VE_TYPE_TXT_TEXT,   1.0,    ""      ), // Off Reason
    		array("id",                     "PID",      VE_TYPE_TXT_INT,    1.0,    ""      ), // Product ID
    		array("serial",                 "SER#",     VE_TYPE_TXT_TEXT,   1.0,    ""      ), // Serial Number
	);

	public function mapVeLookup($code) {
		foreach ($this->veLookup as $map) {
			if ($map[1] == $code) { return $map; }
		}
		return $code;
	}

	public $chargeState = array(
		0 => 'Not charging',
		2 => 'Fault',
		3 => 'Bulk',
		4 => 'Absorption',
		5 => 'Float'
	);

	public $errorCodes = array(
		0 => 'No error',
		2 => 'Battery voltage too high',
		3 => 'Remote temperature sensor failure',
		4 => 'Remote temperature sensor failure',
		5 => 'Remote temperature sensor failure (connection lost)',
		6 => 'Remote battery voltage sense failure',
		7 => 'Remote battery voltage sense failure',
		8 => 'Remote battery voltage sense failure (connection lost)',
		17 => 'Charger temperature too high',
		18 => 'Charger over current',
		19 => 'Charger current reversed',
		20 => 'Bulk time limit exceeded',
		21 => 'Current sensor issue (sensor bias/sensor broken)',
		26 => 'Terminals overheated',
		28 => 'Power stage issue',
		33 => 'Input voltage too high (solar panel)',
		34 => 'Input current too high (solar panel)',
		38 => 'Input shutdown (due to excessive battery voltage)',
		39 => 'Input shutdown',
		65 => '[Info] Communication warning',
		66 => '[Info] Incompatible device',
		67 => 'BMS Connection lost',
		114 => 'CPU temperature too high',
		116 => 'Factory calibration data lost',
		117 => 'Invalid/incompatible firmware',
		119 => 'User settings invalid'
	);

	public $mpptCodes = array(
		0 => 'Off',
		1 => 'Voltage or current limited',
		2 => 'MPP Tracker active'
	);

	public $offCodes = array(
		"0x00000000" => 'Currently on',
		"0x00000001" => 'No input power',
		"0x00000002" => 'Switched off (power switch)',
		"0x00000004" => 'Switched off (device mode register)',
		"0x00000008" => 'Remote input',
		"0x00000010" => 'Protection active',
		"0x00000020" => 'Paygo',
		"0x00000040" => 'BMS',
		"0x00000080" => 'Engine shutdown detection',
		"0x00000100" => 'Analysing input voltage'
	);
}
