<?php
	use DataFX\DataFX;
	use DataFX\Table;
	use DataFX\Column;
	use DataFX\ColumnValue;
	use DataFX\Record;
	use DataFX\RecordColumn;
	
	$tables[] = new Table("Languages", "language_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("Name", "VARCHAR", 5, null, false),
		new Column("Title", "VARCHAR", 50, null, false)
	),
	array
	(
		new Record(array
		(
			new RecordColumn("Name", "en-US"),
			new RecordColumn("Title", "English (United States)")
		))
	));
	
	$tables[] = new Table("LanguageStrings", "languagestring_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("TenantID", "INT", null, null, false),
		new Column("LanguageID", "INT", null, null, false),
		new Column("StringName", "VARCHAR", 50, null, false),
		new Column("StringValue", "LONGTEXT", null, null, false)
	));
?>