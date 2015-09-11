<?php 

require_once('database.php');

class User {

	private $id;
	private $username;
	private $first_name;
	private $last_name;
	private $phone;
	private $roles;
	private $qualifications;

	public function __construct($user_id) {

		$this->id = $user_id;
		$this->roles = $this->getRolesForUser($user_id);

		$user_object = $this->getUserData($user_id);

		if($user_object != null) {
			$this->username = $user_object['username'];
			$this->first_name = $user_object['first_name'];
			$this->last_name = $user_object['last_name'];
			$this->phone = $user_object['phone'];
		}

		$this->qualifications = User::getQualifications($user_id);
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
	 * @param $user_id
	 * @return null
	 */
	private function getUserData($user_id) {

		$db = Database::createConnection();

		$result = $db->query("SELECT username, first_name, last_name, phone
							  FROM user
							  WHERE id={$user_id};");

		$row = null;
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
		}

		$db->close();

		return $row;
	}

	/**
	 *
	 *
	 */
	static public function getQualifications($user_id) {

		$db = Database::createConnection();

		$result = $db->query("SELECT qualification.id, qualification.description, qualification.date_required, mergeTable.documents, mergeTable.date, mergeTable.user_id
							  FROM qualification
							  LEFT JOIN (
							  	SELECT id, documents, date, user_id
								FROM qualification AS a
								LEFT JOIN user_has_qualification AS b
								ON a.id = b.qualification_id
							    WHERE user_id={$user_id}) AS mergeTable
							  ON qualification.id = mergeTable.id;");

		$qualifications_array = array();
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
		   		$qualifications_array[] = $row;
			}		
		} 

		$db->close();

		return $qualifications_array;

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
			'username' => $this->username,
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'phone' => $this->phone,
			'roles' => $this->roles,
			'qualifications' => $this->qualifications
		);
	}

	static public function updateUserData($user_data_array, $user_id) {

		if(empty($user_data_array)) return true;

		$db = Database::createConnection();

		$update_list = '';

		foreach ($user_data_array as $key => $value) {
			$update_list .= ',' . $key . '=\'' . $value . '\'';
		}

		$update_list = substr($update_list, 1);
			
	
		$result = $db->query("UPDATE user  
							  SET $update_list
							  WHERE id=$user_id;");
		
		$db->close();

		return $result;
	}

	static public function updateUserQualifications($qualifications_array, $user_id) {

		if(empty($qualifications_array)) return true;

		$db = Database::createConnection();

		$result = $db->query("DELETE FROM user_has_qualification
							  WHERE user_id=$user_id;");

		if($result) {
			$value_list = '';
			foreach ($qualifications_array as $key => $value) {
				$value = $value ? $value : 'null';

				$value_list .= ',(' . $user_id . ', ' . $key . ', ' . $value . ')';
			}

			$value_list = substr($value_list, 1);

			$result = $db->query("INSERT INTO user_has_qualification  
									  (user_id, qualification_id, date)
								 	  VALUES {$value_list};");
		}
		
		$db->close();

		return $result;
	}
}

?>