<?php
	namespace Objectify\Objects;
	
	class User
	{
		/**
		 * Gets the Instance of the User currently logged in, or NULL if no user is currently logged in.
		 * @return TenantObjectInstance|null
		 */
		public static function GetCurrent()
		{
			$tenant = Tenant::GetCurrent();
			$loginToken = $_SESSION[$tenant->Name . ":Authentication.LoginToken"]; // Objectify::ExecuteMethod("GetLoginTokenForCurrentUser");
			
			if ($loginToken == null) return null;
			$instUser = self::GetByLoginToken($loginToken);
			return $instUser;
		}
		
		/**
		 * Gets the Instance of the User associated with the given Login Token.
		 * @param string $token The Login Token to search for.
		 * @return TenantObjectInstance The Instance of the User object associated with the specified Login Token.
		 */
		public static function GetByLoginToken($token)
		{
			$objUserLogin = KnownObjects::get___User_Login();
			if ($objUserLogin == null) return null;
			
			$instUserLogins = $objUserLogin->GetInstances();
			
			foreach ($instUserLogins as $instUserLogin)
			{
				$tokenCompare = $instUserLogin->GetAttributeValue("Token");
				if ($tokenCompare == $token)
				{
					$inst = $instUserLogin->GetRelatedInstance(KnownRelationships::get___User_Login__has__User());
					if ($inst == null) continue;
					
					return $inst;
				}
			}
			return null;
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
?>