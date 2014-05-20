<?php

// Database Constants



if(SERVER_LIVE) {
	defined('DB_SERVER') ? null : define("DB_SERVER", "localhost");
	defined('DB_USER')   ? null : define("DB_USER", "root");
	defined('DB_PASS')   ? null : define("DB_PASS", "p@55w0rd");
	defined('DB_NAME')   ? null : define("DB_NAME", "memox");
} else {
	defined('DB_SERVER') ? null : define("DB_SERVER", "localhost");
	defined('DB_USER')   ? null : define("DB_USER", "root");
	defined('DB_PASS')   ? null : define("DB_PASS", "p@55w0rd");
	defined('DB_NAME')   ? null : define("DB_NAME", "memox");
}
?>