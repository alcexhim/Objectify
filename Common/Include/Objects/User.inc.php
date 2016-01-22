<?php
	namespace Objectify\Objects;
	
	use Phast\System;
	use Phast\Data\DataSystem;
	use PDO;
	
	use Phast\RandomStringGenerator;
	use Phast\RandomStringGeneratorCharacterSets;
			
	class User
	{
		use \Phast\Data\Traits\DataObjectTrait;
		
		/**
		 * The unique identifier for this User in the database.
		 * @var int
		 */
		public $ID;
		
		/**
		 * The name used to identify the user during login.
		 * @var string
		 */
		public $LoginName;
		
		/**
		 * The hash of the user's password and salt.
		 * @var string
		 */
		public $PasswordHash;
		/**
		 * The salt of the user's password used for comparisons.
		 * @var string
		 */
		public $PasswordSalt;
		
		/**
		 * The Tenant that contains this User.
		 * @var Tenant
		 */
		public $ParentTenant;
		
		/**
		 * Gets the Instance of the User currently logged in, or NULL if no user is currently logged in.
		 * @return TenantObjectInstance|null
		 */
		public static function GetCurrent()
		{
			$loginToken = Objectify::ExecuteMethod("GetLoginTokenForCurrentUser");
			
			if ($loginToken == null) return null;
			$instUser = self::GetByLoginToken($loginToken);
			return $instUser;
		}
		
		public static function GetByCredentials($username, $password = null)
		{
			$pdo = DataSystem::GetPDO();
			
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users WHERE user_LoginID = :user_LoginID AND (:user_PasswordHash IS NULL OR (user_PasswordHash = :user_PasswordHash))";
			$statement = $pdo->prepare($query);
			
			$user_PasswordHash = null;
			$user_PasswordSalt = null;
			
			if ($password != null)
			{
				$query1 = "SELECT user_PasswordSalt FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users WHERE user_LoginID = :user_LoginID";
				$statement1 = $pdo->prepare($query1);
				$statement1->execute(array
				(
					":user_LoginID" => $username
				));
				
				$count = $statement1->rowCount();
				if ($count == 0) return null;
				
				$values = $statement1->fetch(PDO::FETCH_ASSOC);
				
				$user_PasswordSalt = $values["user_PasswordSalt"];
				$user_PasswordHash = hash("sha512", $password . $user_PasswordSalt);
			}
			
			$statement->execute(array
			(
				":user_LoginID" => $username,
				":user_PasswordHash" => $user_PasswordHash
			));
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return self::GetByAssoc($values);
		}
		
		/**
		 * Gets the Instance of the User associated with the given Login Token.
		 * @param string $token The Login Token to search for.
		 * @return TenantObjectInstance The Instance of the User object associated with the specified Login Token.
		 */
		public static function GetByLoginToken($token)
		{
			$objUserLogin = TenantObject::GetByName("UserLogin");
			if ($objUserLogin == null) return null;
			
			$instUserLogins = $objUserLogin->GetInstances();
			
			foreach ($instUserLogins as $instUserLogin)
			{
				$tokenCompare = $instUserLogin->GetPropertyValue("Token");
				if ($tokenCompare == $token) return $instUserLogin->GetPropertyValue("User")->GetInstance();
			}
			return null;
			
			$pdo = DataSystem::GetPDO();
			
			$query = "SELECT " . System::GetConfigurationValue("Database.TablePrefix") . "Users.* FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Users, " . System::GetConfigurationValue("Database.TablePrefix") . "UserLogins WHERE user_ID = login_UserID AND login_Token = :login_Token";
			$statement = $pdo->prepare($query);
			$statement->execute(array
			(
				":login_Token" => $token
			));
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return self::GetByAssoc($values);
		}
		
		/**
		 * Requests a login token from the authentication system.
		 * @return boolean True if a login token was successfully obtained; false otherwise.
		 */
		public function RequestLoginToken()
		{
			$pdo = DataSystem::GetPDO();
			$user_LoginToken = RandomStringGenerator::Generate(RandomStringGeneratorCharacterSets::AlphaNumericMixedCase, 32);
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "UserLogins (login_Token, login_UserID, login_CreationTimestamp, login_CreationIPAddress) VALUES (:login_Token, :login_UserID, NOW(), :login_CreationIPAddress)";
			$statement = $pdo->prepare($query);
			$retval = $statement->execute(array
			(
				":login_Token" => $user_LoginToken,
				":login_UserID" => $this->ID,
				":login_CreationIPAddress" => $_SERVER["REMOTE_ADDR"]
			));
			if ($retval)
			{
				$_SESSION["Authentication.LoginToken"] = $user_LoginToken;
				return true;
			}
			return false;
		}
		
		/**
		 * Releases the currently-obtained login token for this client.
		 * @return boolean True if the operation succeeded; false otherwise.
		 */
		public static function ReleaseLoginToken()
		{
			/*
			$pdo = DataSystem::GetPDO();
			$query = "DELETE FROM " . System::GetConfigurationValue("Database.TablePrefix") . "UserLogins WHERE login_Token = :login_Token";
			$statement = $pdo->prepare($query);
			$retval = $statement->execute(array
			(
				":login_Token" => $_SESSION["Authentication.LoginToken"]
			));
			
			if ($retval)
			{
			*/
				$_SESSION["Authentication.LoginToken"] = null;
				return true;
			// }
			//return false;
		}
		
		public function BindDataColumn($columnName, $value)
		{
			if ($columnName == "ParentTenantID")
			{
				$this->ParentTenant = Tenant::GetByID($value);
				return true;
			}
			return false;
		}
	}
	
	User::$DataObjectTableName = "Users";
	User::$DataObjectColumnPrefix = "user_";
?>