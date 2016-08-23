<?php
	namespace Objectify\Objects;
	
	class KnownInstances
	{
		const GUID___Boolean_Attribute___True = "{9F6F4296-2FCD-4B7E-AB09-D25F47D0C54C}";
		/**
		 * Represents the Instance for "True" [Boolean Attribute], which represents a literal Boolean value
		 * of "true".
		 * 
		 * To get the opposite ("false"), build an Evaluate Boolean Expression Method that returns "not"
		 * this instance (Not True = False).
		 */
		public static function get___Boolean_Attribute___True()
		{
			return Instance::GetByGlobalIdentifier(KnownInstances::GUID___Boolean_Attribute___True);
		}

		const GUID___Return_Instance_Set_Method_Binding___This_Instance = "{712D1D13-78B4-457C-8C32-BCA5723EB0E1}";
		/**
		 * Represents the Instance for "This Instance" [Return Instance Set Method Binding], which returns
		 * the current instance.
		 * 
		 * For example, during Report execution, this is the Instance being referred to by the Primary
		 * Report Object for the current row.
		 */
		public static function get___Return_Instance_Set_Method_Binding___This_Instance()
		{
			return Instance::GetByGlobalIdentifier(KnownInstances::GUID___Return_Instance_Set_Method_Binding___This_Instance);
		}
	}
	
?>