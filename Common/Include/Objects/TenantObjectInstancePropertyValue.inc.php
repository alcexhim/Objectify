<?php
	namespace Objectify\Objects;
	
	class TenantObjectInstancePropertyValue
	{
		/**
		 * The property or name of the property of this TenantObjectInstancePropertyValue.
		 * @var TenantObjectInstanceProperty|string
		 */
		public $Property;
		/**
		 * The value of this TenantObjectInstancePropertyValue.
		 * @var string|number|MultipleInstanceProperty|SingleInstanceProperty
		 */
		public $Value;
		
		/**
		 * Creates a new instance of TenantObjectInstancePropertyValue.
		 * @param TenantObjectInstanceProperty|string $property
		 * @param string|number|MultipleInstanceProperty|SingleInstanceProperty $value
		 */
		public function __construct($property, $value = null)
		{
			$this->Property = $property;
			$this->Value = $value;
		}
	}
?>