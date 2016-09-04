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
	
	// Instances of the objects.
	$tblInstances = new Table("Instances", "instance_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("TenantID", "INT", null, null, false),
		new Column("ObjectID", "INT", null, null, false),
		new Column("ID", "INT", null, null, false),
		new Column("GlobalIdentifier", "CHAR", 32, null, true)
	));
	$tblInstances->PrimaryKey = new TableKey(array
	(
		new TableKeyColumn("TenantID"),
		new TableKeyColumn("ObjectID"),
		new TableKeyColumn("ID")
	));
	$tblInstances->ForeignKeys = array
	(
		new TableForeignKey("TenantID", new TableForeignKeyColumn($tblTenants, "ID")),
		new TableForeignKey("ObjectID", new TableForeignKeyColumn($tblTenantObjects, "ID"))
	);
	$tables[] = $tblInstances;
?>