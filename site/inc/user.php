<?php 

require_once('database.php');

class User {

	private $id;
	private $roles;

	public function __construct($user_id) {

		$this->id = $user_id;
		$this->roles = $this->getRolesForUser($user_id);
	}

	/**
	 *
	 *
	 */
	public function hasRole($role_name) {

		foreach ($this->roles as $role) {
			if($role['role_title'] == $role_name) return true;
		}

		return false;
	}

	/**
	 *
	 *
	 */
	private function getRolesForUser($user_id) {

		$db = Database::createConnection();

		$result = $db->query("SELECT role_id AS id, title AS title
							  FROM user_has_role 
							  LEFT JOIN role
							  ON user_has_role.role_id=role.id
							  WHERE user_id={$user_id};");

		$roles_array = array();
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
		   		$roles_array[] = $row;
			}		
		} 	

		$db->close();

		return $roles_array;
	}

	/**
	 *
	 *
	 */
	public function serialize() {

		return array(
			'id' => $this->id,
			'roles' => $this->roles
			);
	}
}

?>