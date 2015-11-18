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
			foreach ($this->ValidObjects as $obj)
			{
				if ($obj->ID != $value->ParentObject->ID) return false;
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