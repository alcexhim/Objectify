<?php
	namespace Objectify\Objects;
	
	use Phast\System;
	use Phast\Data\DataSystem;
	use PDO;
	
	use Phast\RandomStringGenerator;
	use Phast\RandomStringGeneratorCharacterSets;
			
	class UserLogin
	{
		use \Phast\Data\Traits\DataObjectTrait;
		
		public $User;
		
		public static function GetByToken($token)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "UserLogins WHERE login_Token = :login_Token";
			$statement = $pdo->prepare($query);
			$statement->execute(array
			(
				":login_Token" => $token
			));
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return self::GetByAssoc($values);
			// return self::GetByParameters(array(new DataObjectParameter("Token", DataObjectParameterComparison::Equal, $token)));
		}
		
		protected function BindDataColumn($columnName, $value)
		{
			if ($columnName == "UserID")
			{
				$this->User = User::GetByID($value);
				return true;
			}
			return false;
		}
	}
?>