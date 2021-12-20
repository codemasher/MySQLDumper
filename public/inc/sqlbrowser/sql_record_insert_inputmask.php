<?php
// insert a new record
$tpl = new MSDTemplate();
$tpl->set_filenames([
	                    'show' => './tpl/sqlbrowser/sql_record_insert_inputmask.tpl',
                    ]);

$sqledit = "SHOW FIELDS FROM `$tablename`";
$res     = MSD_query($sqledit);
if($res){
	$num = mysqli_num_rows($res);

	$feldnamen = '';
	for($x = 0; $x < $num; $x++){
		$row       = mysqli_fetch_object($res);
		$feldnamen .= $row->Field.'|';
		$tpl->assign_block_vars('ROW', [
			'CLASS'      => ($x % 2) ? 1 : 2,
			'FIELD_NAME' => $row->Field,
			'FIELD_ID'   => correct_post_index($row->Field),
		]);

		$type = strtoupper($row->Type);

		if(strtoupper($row->Null) == 'YES'){
			//field is nullable
			$tpl->assign_block_vars('ROW.IS_NULLABLE', []);
		}

		if(in_array($type, [
			'BLOB',
			'TEXT',
		])){
			$tpl->assign_block_vars('ROW.IS_TEXTAREA', []);
		}
		else{
			$tpl->assign_block_vars('ROW.IS_TEXTINPUT', []);
		}
	}
}

$tpl->assign_vars([
	                  'HIDDEN_FIELDS' => FormHiddenParams(),
	                  'FIELDNAMES'    => substr($feldnamen, 0, strlen($feldnamen) - 1),
	                  'SQL_STATEMENT' => my_quotes($sql['sql_statement']),
                  ]);

$tpl->pparse('show');
