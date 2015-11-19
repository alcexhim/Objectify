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
	
	$tblTenants = new Table("Tenants", "tenant_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("URL", "VARCHAR", 30, null, false),
		new Column("Description", "LONGTEXT", null, null, true),
		new Column("Status", "INT", null, null, true),
		new Column("TypeID", "INT", null, null, true),
		new Column("BeginTimestamp", "DATETIME", null, null, true),
		new Column("EndTimestamp", "DATETIME", null, null, true)
	),
	array
	(
	));
	
	$tblTenants->UniqueKeys = array
	(
		new TableKey(array
		(
			new TableKeyColumn("URL")
		))
	);
	$tables[] = $tblTenants;
?>