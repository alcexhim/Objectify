<?php
	namespace Objectify\Objects;
	
	class KnownRelationships
	{
		const Relationship___Class__instance_labeled_by__String = "{F52FC851-D655-48A9-B526-C5FE0D7A29D2}";
		const Relationship___Translatable_Text_Constant__has__Translatable_Text_Constant_Value = "{F9B60C00-FF1D-438F-AC74-6EDFA8DD7324}";
		const Relationship___Class__has__Object_Source = "{B62F9B81-799B-4ABE-A4AF-29B45347DE54}";
		const Relationship___Object_Source__for__Class = "{FBB9391D-C4A2-4326-9F85-7801F377253C}";
		const Relationship___Tenant__has_sidebar__Menu_Item = "{D62DFB9F-48D5-4697-AAAD-1CAD0EA7ECFA}";
		const Relationship___Menu_Item__sidebar_for__Tenant = "{4E0A8C3D-5D10-44E5-A6D7-31F262711E01}";
		

		const Relationship___Class__has_title__Translatable_Text_Constant = "{B8BDB905-69DD-49CD-B557-0781F7EF2C50}";
		public static function get___Class__has_title__Translatable_Text_Constant()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Class__has_title__Translatable_Text_Constant);
			return $inst;
		}
		
		public static function get___Class__instance_labeled_by__String()
		{
			$instRelationship__Class__instance_labeled_by_String = Instance::GetByGlobalIdentifier(self::Relationship___Class__instance_labeled_by__String);
			return $instRelationship__Class__instance_labeled_by_String;
		}
		public static function get___Translatable_Text_Constant__has__Translatable_Text_Constant_Value()
		{
			$instRelationship__Translatable_Text_Constant__has__Translatable_Text_Constant_Value = Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__has__Translatable_Text_Constant_Value);
			return $instRelationship__Translatable_Text_Constant__has__Translatable_Text_Constant_Value;
		}
		public static function get___Class__has__Object_Source()
		{
			$instRelationship__Class__has__Object_Source = Instance::GetByGlobalIdentifier(self::Relationship___Class__has__Object_Source);
			return $instRelationship__Class__has__Object_Source;
		}
		public static function get___Object_Source__for__Class()
		{
			$instRelationship__Object_Source__for__Class = Instance::GetByGlobalIdentifier(self::Relationship___Object_Source__for__Class);
			return $instRelationship__Object_Source__for__Class;
		}
		public static function get___Tenant__has_sidebar__Menu_Item()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Tenant__has_sidebar__Menu_Item);
			return $inst;
		}
		public static function get___Menu_Item__sidebar_for__Tenant()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Menu_Item__sidebar_for__Tenant);
			return $inst;
		}

		const Relationship___User__has_display_name__Translatable_Text_Constant = "{6C29856C-3B10-4F5B-A291-DD3CA4C04A2F}";
		const Relationship___Translatable_Text_Constant__display_name_for__User = "{C0B4140B-92C1-4D52-B6DA-8E818A83F3FA}";
		public static function get___User__has_display_name__Translatable_Text_Constant()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___User__has_display_name__Translatable_Text_Constant);
			return $inst;
		}
		public static function get___Translatable_Text_Constant__display_name_for__User()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__display_name_for__User);
			return $inst;
		}

		const Relationship___User__has__Security_Group = "{1921D642-84EC-4E16-B568-9C333909C018}";
		const Relationship___Security_Group__for__User = "{EDC79A23-3324-472D-B2F8-8CB9D2CF9C5D}";
		public static function get___User__has__Security_Group()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___User__has__Security_Group);
			return $inst;
		}
		public static function get___Security_Group__for__User()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Security_Group__for__User);
			return $inst;
		}

		const Relationship___Security_Group__has__Security_Permission = "{5E305133-1380-4736-93E4-6B280B35FCE3}";
		const Relationship___Security_Permission__for__Security_Group = "{52DAC6FC-7E3A-4B09-B3FC-205FF38FE952}";
		public static function get___Security_Group__has__Security_Permission()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Security_Group__has__Security_Permission);
			return $inst;
		}
		public static function get___Security_Permission__for__Security_Group()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Security_Permission__for__Security_Group);
			return $inst;
		}
		
		const Relationship___Class__has_owner__User = "{D1A25625-C90F-4A73-A6F2-AFB530687705}";
		const Relationship___User__owner_for__Class = "{04DD2E6B-EA57-4840-8DD5-F0AA943C37BB}";
		public static function get___Class__has_owner__User()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Class__has_owner__User);
		}
		public static function get___User__owner_for__Class()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___User__owner_for__Class);
		}
		
		const Relationship___Extract_Single_Instance_String_Component__has__Relationship = "{5E499753-F50F-4A9E-BF53-DC013820499C}";
		const Relationship___Relationship__for__Extract_Single_Instance_String_Component = "{B0111132-8721-405C-967B-0BEFA92CFE9A}";
		public static function get___Extract_Single_Instance_String_Component__has__Relationship()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Extract_Single_Instance_String_Component__has__Relationship);
		}
		public static function get___Relationship__for__Extract_Single_Instance_String_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Relationship__for__Extract_Single_Instance_String_Component);
		}
		
		const Relationship___Menu_Item_Command__has_title__Translatable_Text_Constant = "{65E3C87E-A2F7-4A33-9FA7-781EFA801E02}";
		const Relationship___Translatable_Text_Constant__title_for__Menu_Item_Command = "{901A5427-6344-40F4-B81C-D905EA152EB9}";
		public static function get___Menu_Item_Command__has_title__Translatable_Text_Constant()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Menu_Item_Command__has_title__Translatable_Text_Constant);
		}
		public static function get___Translatable_Text_Constant__title_for__Menu_Item_Command()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__title_for__Menu_Item_Command);
		}
		
		const Relationship___Relationship__has_source__Class = "{7FB5D234-042E-45CB-B11D-AD72F8D45BD3}";
		const Relationship___Class__source_for__Relationship = "{20FFFDE8-11A5-48D6-894B-21C6B234B811}";
		public static function get___Relationship__has_source__Class()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Relationship__has_source__Class);
		}
		public static function get___Class__source_for__Relationship()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Class__source_for__Relationship);
		}
		const Relationship___Relationship__has_destination__Class = "{F220F1C2-0499-4E87-A32E-BDBF80C1F8A4}";
		const Relationship___Class__destination_for__Relationship = "{A66CD08C-A155-42AF-8995-A1D96C5A0C06}";
		public static function get___Relationship__has_destination__Class()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Relationship__has_destination__Class);
		}
		public static function get___Class__destination_for__Relationship()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Class__destination_for__Relationship);
		}
		
		const Relationship___String__has__String_Component = "{3B6C4C25-B7BC-4242-8ED1-BA6D01B834BA}";
		const Relationship___String_Component__for__String = "{40E17597-0AEB-4C56-BBAB-FC600E1196DD}";
		public static function get___String__has__String_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___String__has__String_Component);
		}
		public static function get___String_Component__for__String()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___String_Component__for__String);
		}
		
		const Relationship___Translatable_Text_Constant_Value__has__Language = "{3655AEC2-E2C9-4DDE-8D98-0C4D3CE1E569}";
		const Relationship___Language__for__Translatable_Text_Constant_Value = "{032C3549-C2FC-4512-B98A-C2D0BBCF78D0}";
		public static function get___Translatable_Text_Constant_Value__has__Language()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant_Value__has__Language);
		}
		public static function get___Language__for__Translatable_Text_Constant_Value()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Language__for__Translatable_Text_Constant_Value);
		}
		
		const Relationship___Class__has__Task = "{4D8670E1-2AF1-4E7C-9C87-C910BD7B319B}";
		const Relationship___Task__for__Class = "{F6D05235-AAA8-4DC0-8D3A-A0F336B39F01}";
		public static function get___Class__has__Task()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Class__has__Task);
		}
		public static function get___Task__for__Class()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Task__for__Class);
		}
		
		const Relationship___User_Login__has__User = "{85B40E4B-849B-4006-A9C0-4E201B25975F}";
		const Relationship___User__for__User_Login = "{C79A6041-FC94-41A5-9860-D443C60FA7DE}";
		public static function get___User_Login__has__User()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___User_Login__has__User);
		}
		public static function get___User__for__User_Login()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___User__for__User_Login);
		}
		
		const Relationship___Sequential_Container_Page_Component__has__Sequential_Container_Orientation = "{DD55F506-8718-4240-A894-21346656E804}";
		const Relationship___Sequential_Container_Orientation__for__Sequential_Container_Page_Component = "{F8F4EBFE-605C-4F68-99F7-83AEF3FF1AF2}";
		public static function get___Sequential_Container_Page_Component__has__Sequential_Container_Orientation()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Sequential_Container_Page_Component__has__Sequential_Container_Orientation);
		}
		public static function get___Sequential_Container_Orientation__for__Sequential_Container_Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Sequential_Container_Orientation__for__Sequential_Container_Page_Component);
		}
		
		
		
		

		const Relationship___Panel_Page_Component__has_header__Page_Component = "{223B4073-F417-49CD-BCA1-0E0749144B9D}";
		const Relationship___Page_Component__header_for__Panel_Page_Component = "{38E70546-EB2F-4A2E-A2FA-9E290C1835A8}";
		public static function get___Panel_Page_Component__has_header__Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Panel_Page_Component__has_header__Page_Component);
		}
		public static function get___Page_Component__header_for__Panel_Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page_Component__header_for__Panel_Page_Component);
		}

		const Relationship___Panel_Page_Component__has_content__Page_Component = "{AD8C5FAE-2444-4700-896E-C5F968C0F85B}";
		const Relationship___Page_Component__content_for__Panel_Page_Component = "{1DD64A89-9947-4BA3-85AA-12D2EAED80DC}";
		public static function get___Panel_Page_Component__has_content__Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Panel_Page_Component__has_content__Page_Component);
		}
		public static function get___Page_Component__content_for__Panel_Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page_Component__content_for__Panel_Page_Component);
		}
		
		const Relationship___Panel_Page_Component__has_footer__Page_Component = "{56E339BD-6189-4BAC-AB83-999543FB8060}";
		const Relationship___Page_Component__footer_for__Panel_Page_Component = "{9DC77CE3-869D-4E6C-A649-7A662FB5026D}";
		public static function get___Panel_Page_Component__has_footer__Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Panel_Page_Component__has_footer__Page_Component);
		}
		public static function get___Page_Component__footer_for__Panel_Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page_Component__footer_for__Panel_Page_Component);
		}
		
		


		const Relationship___Sequential_Container_Page_Component__has__Page_Component = "{CB7B8162-1C9E-4E72-BBB8-C1C37CA69CD5}";
		const Relationship___Page_Component__for__Sequential_Container_Page_Component = "{E33F11EE-4417-4890-AD89-7BB3DB739918}";
		public static function get___Sequential_Container_Page_Component__has__Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Sequential_Container_Page_Component__has__Page_Component);
		}
		public static function get___Page_Component__for__Sequential_Container_Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page_Component__for__Sequential_Container_Page_Component);
		}
		
		const Relationship___Page__has__Style = "{6E6E1A85-3EA9-4939-B13E-CBF645CB8B59}";
		const Relationship___Style__for__Page = "{A608FC55-4D41-47F6-B021-38DFBAF29521}";
		public static function get___Page__has__Style()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page__has__Style);
		}
		public static function get___Style__for__Page()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Style__for__Page);
		}
		
		const Relationship___Page__has__Page_Component = "{24F6C596-D77D-4754-B023-00321DEBA924}";
		const Relationship___Page_Component__for__Page = "{2519A689-1184-4E24-8006-22FE3F7DB229}";
		public static function get___Page__has__Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page__has__Page_Component);
		}
		public static function get___Page_Component__for__Page()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page_Component__for__Page);
		}

		const Relationship___Page_Component__has__Style = "{818CFF50-7D42-43B2-B6A7-92C3C54D450D}";
		const Relationship___Style__has__Page_Component = "{007563E7-7277-4436-8C82-06D5F156D8E1}";
		public static function get___Page_Component__has__Style()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Page_Component__has__Style);
		}
		public static function get___Style__has__Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Style__has__Page_Component);
		}
		
		const Relationship___Heading_Page_Component__has_text__Translatable_Text_Constant = "{C5027DC2-53EE-4FC0-9BA6-F2B883F7DAD8}";
		const Relationship___Translatable_Text_Constant__text_for__Heading_Page_Component = "{29C02384-57B0-45F5-9C15-747F9DFD2C69}";
		public static function get___Heading_Page_Component__has_text__Translatable_Text_Constant()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Heading_Page_Component__has_text__Translatable_Text_Constant);
		}
		public static function get___Translatable_Text_Constant__text_for__Heading_Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__text_for__Heading_Page_Component);
		}
		
		const Relationship___Paragraph_Page_Component__has_text__Translatable_Text_Constant = "{0E002E6F-AA79-457C-93B8-2CCE1AEF5F7E}";
		const Relationship___Translatable_Text_Constant__text_for__Paragraph_Page_Component = "{5E75000D-2421-4AD4-9E5F-B9FDD9CB4744}";
		public static function get___Paragraph_Page_Component__has_text__Translatable_Text_Constant()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Paragraph_Page_Component__has_text__Translatable_Text_Constant);
		}
		public static function get___Translatable_Text_Constant__text_for__Paragraph_Page_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__text_for__Paragraph_Page_Component);
		}
		
		const Relationship___Command_Menu_Item__has__Icon = "{8859DAEF-01F7-46FA-8F3E-7B2F28E0A520}";
		const Relationship___Icon__for__Command_Menu_Item = "{6D3D6F9E-CDAE-4D0E-B5B2-BC2BA333F746}";
		public static function get___Command_Menu_Item__has__Icon()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Command_Menu_Item__has__Icon);
		}
		public static function get___Icon__for__Command_Menu_Item()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Icon__for__Command_Menu_Item);
		}
		
		const Relationship___Style__has__Style_Rule = "{4CC8A654-B2DF-4B17-A956-24939530790E}";
		const Relationship___Style_Rule__for__Style = "{32276525-126A-412F-A10F-1368312D2EAB}";
		public static function get___Style__has__Style_Rule()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Style__has__Style_Rule);
		}
		public static function get___Style_Rule__for__Style()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Style_Rule__for__Style);
		}
		
		const Relationship___Style_Rule__has__Style_Property = "{B69C2708-E78D-413A-B491-ABB6F1D2A6E0}";
		const Relationship___Style_Property__for__Style_Rule = "{DF43F7A5-8175-4E23-9801-8299EA55B356}";
		public static function get___Style_Rule__has__Style_Property()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Style_Rule__has__Style_Property);
		}
		public static function get___Style_Property__for__Style_Rule()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Style_Property__for__Style_Rule);
		}
		
	}
?>