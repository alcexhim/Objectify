<?php
	use Phast\Data\Table;
	use Phast\Data\Column;
	use Phast\Data\ColumnValue;
	use Phast\Data\Record;
	use Phast\Data\RecordColumn;
	use Phast\Data\TableKey;
	use Phast\Data\TableKeyColumn;
	use Phast\Data\TableForeignKey;
	use Phast\Data\TableForeignKeyColumn;
	use Phast\Data\TableForeignKeyReferenceOption;
	
	$tblLanguages = new Table("Languages", "language_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("Name", "VARCHAR", 64, null, false),
		new Column("Title", "VARCHAR", 128, null, false)
	),
	array
	(
		new Record(array
		(
			new RecordColumn("Name", "en-US"),
			new RecordColumn("Title", "English (United States)")
		))
	));
	$tables[] = $tblLanguages;
?>