<?php
	namespace Objectify\Objects;
	
	class SingleInstanceProperty
	{
		private $mvarInstance;
		
		/**
		 * Gets the instance stored in this property.
		 * @return Instance
		 */
		public function GetInstance()
		{
			return $this->mvarInstance;
		}
		/**
		 * Stores the specified instance in this property. If another instance is already stored in this property, that instance is replaced.
		 * @param Instance $value
		 */
		public function SetInstance($value)
		{
			$objIDsAllowed = array();
			foreach ($this->ValidObjects as $obj)
			{
				if ($obj == null)
				{
					trigger_error("XquizIT: attempted to add instance " . $value->GlobalIdentifier . " to null valid object");
					continue;
				}
				if ($obj->ID != $value->ParentObject->ID)
				{
					// go through the hierarchy to see if it's really invalid
					$objParents = $obj->GetParentObjects();
					$ok = false;
					$count = count($objParents);
					if ($count > 0)
					{
						foreach ($objParents as $objParent)
						{
							if ($objParent->ID == $value->ParentObject->ID)
							{
								$ok = true;
								break;
							}
						}
						if ($ok) break;
					}
					
					if ($ok)
					{
						$objIDsAllowed[] = $obj->ID;
					}
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
			
			$this->mvarInstance = $value;
			return true;
		}
		
		/**
		 * The objects whose instances may be stored in this property.
		 * @var TenantObject[]
		 */
		public $ValidObjects;
		
		/**
		 * Creates a SingleInstanceProperty with the specified instances and valid objects.
		 * @param Instance $instance The instance to store in this property.
		 * @param TenantObject[] $validObjects The objects whose instances may be stored in this property.
		 */
		public function __construct($instance = null, $validObjects = null)
		{
			$this->mvarInstance = $instance;
			$this->ValidObjects = $validObjects;
		}
	}
?>