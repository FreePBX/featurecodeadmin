<?php 

function featurecodeadmin_update($req) {
	foreach ($req as $key => $item) {
		// Split up...
		// 0 - action
		// 1 - modulename
		// 2 - featurename
		$arr = explode("#", $key);
		if (count($arr) == 3) {
			$action = $arr[0];
			$modulename = $arr[1];
			$featurename = $arr[2];
			$fieldvalue = $item;
			
			// Is there a more efficient way of doing this?
			switch ($action)
			{
				case "ena":
					$fcc = new featurecode($modulename, $featurename);
					if ($fieldvalue == 1) {
						$fcc->setEnabled(true);
					} else {
						$fcc->setEnabled(false);
					}
					$fcc->update();
					break;
				case "custom":
					$fcc = new featurecode($modulename, $featurename);
					if ($fieldvalue == $fcc->getDefault()) {
						$fcc->setCode(''); // using default
					} else {
						$fcc->setCode($fieldvalue);
					}
					$fcc->update();
					break;
			}
		}
	}

	needreload();
}

function featurecodeadmin_check_extensions($exten=true) {
	$extenlist = array();
	if (is_array($exten) && empty($exten)) {
		return $extenlist;
	}
	$featurecodes = featurecodes_getAllFeaturesDetailed();

	foreach ($featurecodes as $result) {
		$thisexten = ($result['customcode'] != '')?$result['customcode']:$result['defaultcode'];

		// Ignore disabled codes, and modules, and any exten not being requested unless all (true)
		//
		if (($result['featureenabled'] == 1) && ($result['moduleenabled'] == 1) && ($exten === true || in_array($thisexten, $exten))) {
			$extenlist[$thisexten]['description'] = _("Featurecode: ").$result['featurename']." (".$result['modulename'].":".$result['featuredescription'].")";
			$extenlist[$thisexten]['status'] = 'INUSE';
			$extenlist[$thisexten]['edit_url'] = 'config.php?type=setup&display=featurecodeadmin';
		}
	}
	return $extenlist;
}
?>
