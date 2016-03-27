<?php 

require_once('database.php');

class User {

	private $id;
	private $username;
	private $first_name;
	private $last_name;
	private $phone;
	private $email;
	private $active;
	private $deletable;
	private $roles;
	private $qualifications;
	private $event_whitelist;

	/**
	 * Create a new user object.
	 *
	 * @param $user_id
	 * @param $user_roles
	 * @param $user_data
	 * @param $user_qualifications
	 */
	public function __construct($user_id, $user_roles, $user_data, $user_qualifications) {

		$this->id = $user_id;

		if(!$user_roles)
			$user_roles = $this->getRolesForUser($user_id);

		if($user_roles != null)
			$this->roles = $user_roles;

		if(!$user_data)
			$user_data = $this->getUserData($user_id);

		if($user_data != null) {
			$this->username = $user_data['username'];
			$this->first_name = $user_data['first_name'];
			$this->last_name = $user_data['last_name'];
			$this->phone = $user_data['phone'];
			$this->email = $user_data['email'];
			$this->active = $user_data['active'];
			$this->deletable = $user_data['deletable'];

			if(is_string($user_data['event_whitelist']))
				$this->event_whitelist = split(',', $user_data['event_whitelist']);
			else
				$this->event_whitelist = $user_data['event_whitelist'];
		}

		if(!$user_qualifications)
			$user_qualifications = User::getQualifications($user_id);

		if($user_qualifications != null)
			$this->qualifications = $user_qualifications;
	}

	/**
	 *
	 *
	 * @param $user_id
	 * @return User
	 */
	public static function withUserId($user_id) {
		return new self($user_id, null, null, null);
	}

	/**
	 *
	 *
	 * @param $user_array
	 * @return User
	 */
	public static function withUserObjectData($user_array) {

		$user_data = array(
			'username' => $user_array['username'],
			'first_name' => $user_array['first_name'],
			'last_name' => $user_array['last_name'],
			'phone' => $user_array['phone'],
			'email' => $user_array['email'],
			'active' => $user_array['active'],
			'deletable' => $user_array['deletable'],
			'event_whitelist' => $user_array['event_whitelist']
		);

		return new self($user_array['id'], $user_array['roles'], $user_data, $user_array['qualifications']);
	}

	/**
	 * Retrieve user data from database.
	 *
	 * @param $user_id
	 * @return null
	 */
	private function getUserData($user_id) {

		$db = Database::createConnection();

		$result = $db->query("SELECT username, first_name, last_name, phone, email, active, deletable, event_whitelist
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
	 * Retrieve user qualifications from database.
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
	 * Retrieve user roles from database.
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
	 * Checks the given roles_array against the roles the user has.
	 *
	 * @param $roles_array
	 * @return bool
	 */
	public function hasPermission($roles_array) {

		foreach($this->roles as $role) {

			if (in_array($role['title'], $roles_array)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Serialize the user object to store it to the session variable.
	 *
	 */
	public function serialize() {

		return array(
			'id' => $this->id,
			'username' => $this->username,
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'phone' => $this->phone,
			'email' => $this->email,
			'active' => $this->active,
			'deletable' => $this->deletable,
			'roles' => $this->roles,
			'qualifications' => $this->qualifications,
			'event_whitelist' => join(',', $this->event_whitelist)
		);
	}

	/**
	 *
	 *
	 * @param $user_data_array
	 * @param $user_id
	 * @return bool
	 */
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

	/**
	 *
	 *
	 * @param $qualifications_array
	 * @param $user_id
	 * @return bool
	 */
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