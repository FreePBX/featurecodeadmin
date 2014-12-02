<?php 
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }
//	License for all code of this FreePBX module can be found in the license file inside the module directory
//	Copyright (C) 2006 Niklas Larsson
//	Copyright (C) 2014 Schmooze Com Inc.
//

$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$dispnum = "featurecodeadmin"; //used for switch on config.php

$tabindex = 0;

//if submitting form, update database
switch ($action) {
  case "save":
  	featurecodeadmin_update($_REQUEST);
  	needreload();
  break;
}

$featurecodes = featurecodes_getAllFeaturesDetailed();
$exten_conflict_arr = array();
$conflict_url = array();
$exten_arr = array();
foreach ($featurecodes as $result) {
	/* if the feature code starts with "In-Call Asterisk" then it is not conflicting with normal feature codes. This would be featuremap and future
	 * application map type codes. This is a real kludge and instead there should be a category associated with these codes when the feature code
	 * is created. However, the logic would be the same, thus my willingness to put in such a kludge for now. When the schema changes to add this
	 * then this can be updated to reflect that
	 */
	if (($result['featureenabled'] == 1) && ($result['moduleenabled'] == 1) && substr($result['featuredescription'],0,16) != 'In-Call Asterisk') {
		$exten_arr[] = ($result['customcode'] != '')?$result['customcode']:$result['defaultcode'];
	}
}
$usage_arr = framework_check_extension_usage($exten_arr);
unset($usage_arr['featurecodeadmin']);
if (!empty($usage_arr)) {
	$conflict_url = framework_display_extension_usage_alert($usage_arr,false,false);
}
$conflicterror = '';
if (!empty($conflict_url)) {
	$str = _("You have feature code conflicts with extension numbers in other modules. This will result in unexpected and broken behavior.");
	$conflicterror .= "<script>javascript:alert('$str')</script>";
 	$conflicterror .= "<h4>"._("Feature Code Conflicts with other Extensions")."</h4>";
	$conflicterror .=  implode('<br />',$conflict_url);
	// Create hash of conflicting extensions
	//
	foreach ($usage_arr as $module_name => $details) {
		foreach (array_keys($details) as $exten_conflict) {
			$exten_conflict_arr[$exten_conflict] = true;
		}
	}
	// Now check for conflicts within featurecodes page
	//
	$unique_exten_arr = array_unique($exten_arr);
	$feature_conflict_arr = array_diff_assoc($exten_arr, $unique_exten_arr);
	foreach ($feature_conflict_arr as $value) {
		$exten_conflict_arr[$value] = true;
	}
}
$currentmodule = "(none)";
$modlines = '';

foreach($featurecodes as $item) {
	$moduledesc = isset($item['moduledescription']) ? modgettext::_($item['moduledescription'], $item['modulename']) : null;
	// just in case the translator put the translation in featurcodes module:
	if (($moduledesc !== null) && ($moduledesc == $item['moduledescription'])) {
		$moduledesc = _($moduledesc);
	}
	$featuredesc = modgettext::_($item['featuredescription'], $item['modulename']);
	// just in case the translator put the translation in featurcodes module:
	if ($featuredesc == $item['featuredescription']) {
		$featuredesc = _($featuredesc);
	}
	$featurehelp = modgettext::_($item['featurehelptext'], $item['modulename']);
	if ($featurehelp == $item['featurehelptext']) {
		$featurehelp = _($featurehelp);
	}
	$moduleena = ($item['moduleenabled'] == 1 ? true : false);
	$featureid = $item['modulename'] . '#' . $item['featurename'];
	$featureena = ($item['featureenabled'] == 1 ? true : false);
	$featurecodedefault = (isset($item['defaultcode']) ? $item['defaultcode'] : '');
	$featurecodecustom = (isset($item['customcode']) ? $item['customcode'] : '');
	if($featurecodecustom != ''){
		$dychecked = '';
		$dnchecked = 'checked';
	}else{
		$dychecked = 'checked';
		$dnchecked = '';
		$disablein = 'readonly';		
	}
	if($featureena){
		$eychecked = 'checked';
		$enchecked = '';		
	}else{
		$eychecked = '';
		$enchecked = 'checked';	
	}
	$thiscode = ($featurecodecustom != '') ? $featurecodecustom : $featurecodedefault;
$modlines .= <<<HERE
<!--$modulename $featuredesc -->
<div class="element-container">
<div class="row">
<div class="col-md-12">
<div class="row">
<div class="form-group">
<div class="col-sm-5">
<b>$featuredesc</b>
</div>
<div class="col-sm-1">
<input type="hidden" name="default_$featureid" value="$featurecodedefault" id="default_$featureid">
<input type="hidden" name="origcustom_$featureid" value="$featurecodecustom"  id="origcustom_$featureid">
<input type="text" class="form-control" id="custom$featureid" name="custom#$featureid" value="$thiscode" $disablein required pattern="\*{0,2}[0-9]{0,5}">
</div>
<div class="col-sm-3">
<span class="radioset">
<input type="radio" name="usedefault_$featureid" onclick="usedefault_onclick(this);" class="form-control " id="usedefault_${featureid}1" tabindex="" value="1" $dychecked><label for="usedefault_${featureid}1">yes</label>
<input type="radio" name="usedefault_$featureid" onclick="usedefault_onclick(this);" class="form-control " id="usedefault_${featureid}0" tabindex="" value="0" $dnchecked ><label for="usedefault_${featureid}0">no</label>
</span>
</div>
<div class="col-sm-3">
<span class="radioset">
<input type="radio" name="ena#$featureid" onclick="" class="form-control " id="ena#${featureid}1" tabindex="" value="1" $eychecked><label for="ena#${featureid}1">yes</label>
<input type="radio" name="ena#$featureid" onclick="" class="form-control " id="ena#${featureid}0" tabindex="" value="0" $enchecked><label for="ena#${featureid}0">no</label>
</span>
</div>
</div>	
</div>	
</div>	
</div>
</div>
HERE;
}

?>
<div class="contianer-fluid">
	<div class="row">
		<div class='col-sm-12'>
			<div class='fpbx-container'>
				<div class='display full-border'>
					<div class="contianer-fluid">
						<div class="section-title" data-for="featurecodeadmin">
							<h3><i class="fa fa-asterisk"></i><?php echo _("Feature Code Admin"); ?></h3>
						</div>
						<div class="section-title" data-for="featurecodeadmin">
							<!-- Conflict error may display here if there is one-->
							<?php echo $conflicterror ?>
							<!--End of error zone-->
						</div>
						<div class="section" data-id="featurecodeadmin">
							<form autocomplete="off "class="fpbx-submit" name="frmAdmin" action="/admin/config.php?display=featurecodeadmin" method="post" onsubmit="return frmAdmin_onsubmit(this);">
							<input type="hidden" name="display" value="<?php echo $dispnum ?>">
							<input type="hidden" name="action" value="save">
							<div class="element-container">
								<div class="row">
									<div class="col-md-12">
										<div class="row">
											<div class="form-group">
												<div class="col-sm-5">
												<h4>Description</h4>
												</div>
												<div class="col-sm-1">
												<h4>Code</h4>
												</div>
												<div class="col-sm-3">
												<h4>Default</h4>
												</div>
												<div class="col-sm-3">
												<h4>Enabled</h4>
												</div>
											</div>	
										</div>	
									</div>	
								</div>	
							</div>	
							<!--Generated-->	
							<?php echo $modlines ?>
							<!--END Generated-->
							</form>		
							</div>	
						</div>	
					</div>	
				</div>	
			</div>	
		</div>
	</div>				
</div>				

