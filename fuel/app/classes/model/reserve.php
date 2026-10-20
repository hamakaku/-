<?php
/**
 * The Reserve Model.
 */
class Model_Reserve extends \Model_Crud
{
	protected static $_table_name      = 'reserve';
	protected static $_primary_key     = 'id';
	protected static $_mysql_timestamp = true;
	protected static $_created_at      = 'created_at';
}
