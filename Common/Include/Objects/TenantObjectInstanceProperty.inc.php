<?php
	namespace Objectify\Objects;
	
	class TenantObjectInstanceProperty
	{
		public $ID;
		public $ParentObject;
		public $Name;
		public $DataType;
		public $DefaultValue;
		public $Required;
	
		/// <summary>
		/// Determines whether this TenantObjectInstanceProperty is visible when rendered as a column in a ListView.
		/// </summary>
		public $ColumnVisible;

		public function Encode($value)
		{
			if ($this->DataType == null) return $value;
			return $this->DataType->Encode($value);
		}
		public function Decode($value)
		{
			if ($this->DataType == null) return $value;
			return $this->DataType->Decode($value);
		}
		
		public function __construct($name = null, $dataType = null, $defaultValue = null, $required = false)
		{
			$this->Name = $name;
			$this->DataType = $dataType;
			$this->DefaultValue = $defaultValue;
			$this->Required = $required;
		}
		
		public static function GetByAssoc($values)
		{
			$item = new TenantObjectInstanceProperty();
			$item->ID = $values["property_ID"];
			$item->ParentObject = TenantObject::GetByID($values["property_ObjectID"]);
			$item->Name = $values["property_Name"];
			$item->Description = $values["property_Description"];
			$item->DataType = DataType::GetByID($values["property_DataTypeID"]);
			if ($item->DataType != null)
			{
				$item->DefaultValue = $item->DataType->Decode($values["property_DefaultValue"]);
			}
			$item->Required = ($values["property_IsRequired"] == 1);
			$item->ColumnVisible = ($values["property_ColumnVisible"] == 1);
			return $item;
		}
	}
	class TenantObjectInstancePropertyValue
	{
		public $Property;
		public $Value;
		
		public function __construct($property, $value = null)
		{
			$this->Property = $property;
			$this->Value = $value;
		}
	}
?>