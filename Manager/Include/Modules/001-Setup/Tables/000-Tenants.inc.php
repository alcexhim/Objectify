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
		new Column("Name", "VARCHAR", 256, null, false),
		new Column("GlobalIdentifier", "CHAR", 32, null, true),
		new Column("ParentTenantID", "INT", null, null, true)
	));
	$tblTenants->ForeignKeys = array
	(
		new TableForeignKey("ParentTenantID", new TableForeignKeyColumn($tblTenants, "ID"))
	);
	$tables[] = $tblTenants;
	
?>