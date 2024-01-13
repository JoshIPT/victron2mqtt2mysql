#include <wiringPi.h>
#include <mysql.h>
#include <stdio.h>
#include <stdlib.h>
#include <stdint.h>

/* Configurables */
#define DHTPIN		15
#define MYSQL_USER	"victron"
#define MYSQL_PASS	"victron"
#define MYSQL_HOST	"localhost"
#define MYSQL_DB	"victron"
#define CELCIUS		1
/* End configurables */

#define MAXTIMINGS	85

int dht11_dat[5] = { 0, 0, 0, 0, 0 };

void read_dht11_dat(MYSQL *db) {
	uint8_t laststate	= HIGH;
	uint8_t counter		= 0;
	uint8_t j		= 0, i;
	float	f;
	float	c;
	char query[255] = "";

	dht11_dat[0] = dht11_dat[1] = dht11_dat[2] = dht11_dat[3] = dht11_dat[4] = 0;

	pinMode(DHTPIN, OUTPUT);
	digitalWrite(DHTPIN, LOW);
	delay(18);
	digitalWrite(DHTPIN, HIGH);
	delayMicroseconds(40);
	pinMode(DHTPIN, INPUT);

	for (i = 0; i < MAXTIMINGS; i++) {
		counter = 0;
		while (digitalRead(DHTPIN) == laststate) {
			counter++;
			delayMicroseconds(1);
			if (counter == 255) {
				break;
			}
		}
		laststate = digitalRead(DHTPIN);

		if (counter == 255) { break; }

		if ((i >= 4) && (i % 2 == 0)) {
			dht11_dat[j / 8] <<= 1;
			if (counter > 16) { dht11_dat[j / 8] |= 1; }
			j++;
		}
	}

	if ((j >= 40) && (dht11_dat[4] == ((dht11_dat[0] + dht11_dat[1] + dht11_dat[2] + dht11_dat[3]) & 0xFF))) {
		c = dht11_dat[2];
		f = c * 9. / 5. + 32;
		if (CELCIUS == 1) {
			char full_c[32] = "";
			sprintf(full_c, "%d.%d", dht11_dat[2], dht11_dat[3]);
			sprintf(query, "INSERT INTO `sensors` (`sensor_name`, `value`, `unit`) VALUES ('temperature', '%s', 'C') ON DUPLICATE KEY UPDATE `value`='%s';", full_c, full_c);
			mysql_query(db, query);
		}
		else {
			sprintf(query, "INSERT INTO `sensors` (`sensor_name`, `value`, `unit`) VALUES ('temperature', '%d', 'F') ON DUPLICATE KEY UPDATE `value`='%d';", f, f);
			mysql_query(db, query);
		}

		sprintf(query, "INSERT INTO `sensors` (`sensor_name`, `value`, `unit`) VALUES ('humidity', '%d.%d', '%%') ON DUPLICATE KEY UPDATE `value`='%d.%d';", dht11_dat[0], dht11_dat[1], dht11_dat[0], dht11_dat[1]);
		mysql_query(db, query);

		//printf("{\"humidity\": \"%d.%d\",\"temperature\": \"%d.%d\"}\n", dht11_dat[0], dht11_dat[1], dht11_dat[2], dht11_dat[3], f );
	} else {
		//printf("{\"error\":\"Invalid data format\"}\n");
	}
}

int main(void) {
	if (wiringPiSetup() == -1) { exit(1); }
	MYSQL *db;
	MYSQL_RES *qid;

	db = mysql_init(NULL);
	if (!mysql_real_connect(db, MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB, 0, NULL, 0)) {
		fprintf(stderr, "%s\n", mysql_error(db));
		exit(1);
	}

	while (1) {
		read_dht11_dat(db);
		delay(1000);
	}
	return(0);
}
