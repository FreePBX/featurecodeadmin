<?php 
// This file is part of FreePBX.
//
//    FreePBX is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 2 of the License, or
//    (at your option) any later version.
//
//    FreePBX is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with FreePBX.  If not, see <http://www.gnu.org/licenses/>.
//
//    Copyright (C) 2006 Rob Thomas

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

function featurecodeadmin_get_config($engine) {
	global $ext;  // is this the best way to pass this?

  switch($engine) {
    case "asterisk":

      $featurecodes = featurecodes_getAllFeaturesDetailed();

      $contextname = 'ext-featurecodes';
      foreach ($featurecodes as $result) {
        // Ignore disabled codes, and modules, and ones not providing destinations
        //
        if ($result['featureenabled'] == 1 && $result['moduleenabled'] == 1 && $result['providedest'] == 1) {
          $thisexten = ($result['customcode'] != '')?$result['customcode']:$result['defaultcode'];
          $ext->add($contextname, $result['defaultcode'], '', new ext_goto('1',$thisexten,'from-internal'));
        }
      }
    break;
  }
}

function featurecodeadmin_destinations() {

  $featurecodes = featurecodes_getAllFeaturesDetailed();
	if (isset($featurecodes)) {
    $text_domain = Array();
    foreach ($featurecodes as $result) {
      // Ignore disabled codes, and modules, and ones not providing destinations
      //
      if ($result['featureenabled'] == 1 && $result['moduleenabled'] == 1 && $result['providedest'] == 1) {
        $modulename = $result['modulename'];

        // normally gettext is done by drawselects, but in this case we are getting strings from other modules as well
        // where the translations are. We will therefore try to do the translation here. Then drawselects will try to
        // translate against the already translated strings but should just fail and default to what we pass back
        //
        if (!isset($text_domain['modulename']) & $modulename != 'core') {
          if (extension_loaded('gettext') && is_dir("modules/".$modulename."/i18n")) {
            bindtextdomain($modulename,"modules/$modulename/i18n");
            bind_textdomain_codeset($modulename, 'utf8');
            $text_domain[$modulename] = true;
          } else {
            $text_domain[$modulename] = false;
          }
        }
        if ($modulename != 'core' && $text_domain[$modulename]) {
          $description = dgettext($modulename,$result['featuredescription']);
          if ($description == $result['featuredescription']) {
            $description = dgettext('amp',$description);
          }
        } else {
          $description = dgettext('amp',$result['featuredescription']);
        }
        if ($description == $result['featuredescription']) {
            $description = _($description);
        }
        $thisexten = ($result['customcode'] != '')?$result['customcode']:$result['defaultcode'];
				$extens[] = array('destination' => 'ext-featurecodes,'.$result['defaultcode'].',1', 'description' => $description.' <'.$thisexten.'>');
      }
    }
  }
  if (isset($extens)) 
    return $extens;
  else
    return null;
}
?>
