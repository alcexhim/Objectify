<?php
	namespace Objectify\Objects;
	
	class KnownAttributes
	{
		const GUID___Attribute___Text___Value = "{041DD7FD-2D9C-412B-8B9D-D7125C166FE0}";
		
		private static $Attribute___Text___Value;
		public static function get___Text___Value()
		{
			if (KnownAttributes::$Attribute___Text___Value == null)
			{
				KnownAttributes::$Attribute___Text___Value = Instance::GetByGlobalIdentifier(KnownAttributes::GUID___Attribute___Text___Value);
			}
			return KnownAttributes::$Attribute___Text___Value;
		}

		const GUID___Attribute___Text___CSSValue = "{C0DD4A42-F503-4EB3-8034-7C428B1B8803}";
		
		private static $Attribute___Text___CSSValue;
		public static function get___Text___CSSValue()
		{
			if (KnownAttributes::$Attribute___Text___CSSValue == null)
			{
				KnownAttributes::$Attribute___Text___CSSValue = Instance::GetByGlobalIdentifier(KnownAttributes::GUID___Attribute___Text___CSSValue);
			}
			return KnownAttributes::$Attribute___Text___CSSValue;
		}
	}
?>