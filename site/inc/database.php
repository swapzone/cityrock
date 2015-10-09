<?php 

class Database {

	/**
	 * Creates a database connection and returns the database handle.
	 *
	 * @return object database handle
	 */
	public static function createConnection() {

		$servername = "localhost";
		$username = "root";
		$password = "";
		$database = "cityrock";

		$db = new mysqli($servername, $username, $password, $database);

		if ($db->connect_error) {
			die("Connection failed: " . $db->connect_error);
		} 

		$db->set_charset("utf8");

		return $db;
	}
}
?>
