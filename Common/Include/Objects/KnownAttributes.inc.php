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
		
		const GUID___Attribute___Boolean___Render_as_Text = "{15DD9E49-AB6D-44F0-9039-27AF81736406}";
		
		private static $Attribute___Boolean___Render_as_Text;
		public static function get___Boolean___Render_as_Text()
		{
			if (KnownAttributes::$Attribute___Boolean___Render_as_Text == null)
			{
				KnownAttributes::$Attribute___Boolean___Render_as_Text = Instance::GetByGlobalIdentifier(KnownAttributes::GUID___Attribute___Boolean___Render_as_Text);
			}
			return KnownAttributes::$Attribute___Boolean___Render_as_Text;
		}
		
		const GUID___Attribute___Text___Target_URL = "{970F79A0-9EFE-4E7D-9286-9908C6F06A67}";
		
		private static $Attribute___Text___Target_URL;
		public static function get___Text___Target_URL()
		{
			if (KnownAttributes::$Attribute___Text___Target_URL == null)
			{
				KnownAttributes::$Attribute___Text___Target_URL = Instance::GetByGlobalIdentifier(KnownAttributes::GUID___Attribute___Text___Target_URL);
			}
			return KnownAttributes::$Attribute___Text___Target_URL;
		}
	}
?>