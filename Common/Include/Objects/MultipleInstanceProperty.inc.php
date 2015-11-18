<?php
	namespace Objectify\Objects;
	
	class MultipleInstanceProperty
	{
		/**
		 * @var TenantObjectInstance[]
		 */
		private $mvarInstances;
		/**
		 * Gets the instances assigned to this MultipleInstanceProperty.
		 * @return TenantObjectInstance[]
		 */
		public function GetInstances()
		{
			return $this->mvarInstances;
		}
		public function AddInstance($value)
		{
			if ($value == null) return false;
			
			$objIDsAllowed = array();
			foreach ($this->ValidObjects as $obj)
			{
				if ($obj->ID != $value->ParentObject->ID)
				{
					$objIDsAllowed[] = $obj->ID;
				}
			}
			
			$count = count($objIDsAllowed);
			if ($count > 0)
			{
				$objIDsAllowedStr = "";
				for ($i = 0; $i < $count; $i++)
				{
					$objIDsAllowedStr .= $objIDsAllowed[$i];
					if ($i < $count - 1) $objIDsAllowedStr .= ", ";
				}
				Objectify::Log("Attempted to assign an instance to a property which doesn't accept instances of this object", array
				(
					"Instance ID" => $value->ID,
					"Instance Object ID" => $value->ParentObject->ID,
					"Allowed Object IDs" => $objIDsAllowedStr
				));
				return false;
			}
			
			$this->mvarInstances[] = $value;
			return true;
		}
		public function ClearInstances()
		{
			$this->mvarInstances = array();
		}
		public function CountInstances()
		{
			return count($this->mvarInstances);
		}
		
		public $ValidObjects;
		
		public function __construct($instances = null, $validObjects = null)
		{
			if ($instances == null) $instances = array();
			$this->mvarInstances = $instances;
			
			if ($validObjects == null) $validObjects = array();
			$this->ValidObjects = $validObjects;
		}
	}
?>