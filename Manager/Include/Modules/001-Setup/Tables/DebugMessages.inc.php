<?php
	use Phast\Data\Table;
	use Phast\Data\Column;
	use Phast\Data\ColumnValue;
	use Phast\Data\Record;
	use Phast\Data\RecordColumn;
	use Phast\Data\TableForeignKey;
	use Phast\Data\TableForeignKeyColumn;
	use Phast\Data\TableForeignKeyReferenceOption;
	
	$tblDebugMessages = new Table("DebugMessages", "message_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, true),
		new Column("Content", "LONGTEXT", null, null, false),
		new Column("SeverityID", "INT", null, 0, false),
		new Column("Timestamp", "DATETIME", null, null, false),
		new Column("IPAddress", "VARCHAR", 40, "", false)
	));
	$tables[] = $tblDebugMessages;
	
	$tblDebugMessageBacktraces = new Table("DebugMessageBacktraces", "bt_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("MessageID", "INT", null, null, false),
		new Column("FileName", "LONGTEXT", null, null, false),
		new Column("LineNumber", "INT", null, null, true)
	));
	$tblDebugMessageBacktraces->ForeignKeys = array
	(
		new TableForeignKey("MessageID", new TableForeignKeyColumn($tblDebugMessages, "ID"), TableForeignKeyReferenceOption::Cascade)
	);
	$tables[] = $tblDebugMessageBacktraces;
	
	$tblDebugMessageParameters = new Table("DebugMessageParameters", "mp_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("MessageID", "INT", null, null, false),
		new Column("Name", "LONGTEXT", null, null, false),
		new Column("Value", "LONGTEXT", null, null, true)
	));
	$tblDebugMessageParameters->ForeignKeys = array
	(
		new TableForeignKey("MessageID", new TableForeignKeyColumn($tblDebugMessages, "ID"), TableForeignKeyReferenceOption::Cascade)
	);
	$tables[] = $tblDebugMessageParameters;
?>