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
		$dchecked = 'checked';
		$disablein = '';
	}else{
		$dchecked = '';
		$disablein = 'readonly';		
	}
	if($featureena){
		$echecked = 'checked';
	}else{
		$echecked = '';
	}
	$thiscode = ($featurecodecustom != '') ? $featurecodecustom : $featurecodedefault;
	$thismodule = $item['modulename'];
	$modlines .= '<form autocomplete="off "class="fpbx-submit" name="frmAdmin" action="/admin/config.php?display=featurecodeadmin" method="post" onsubmit="return frmAdmin_onsubmit(this);">';
	$modlines .= '<input type="hidden" name="display" value="' . $dispnum .'">';
	$modlines .= '<input type="hidden" name="action" value="save">';
if($thismodule != $currentmodule){
if($lastmodule != "(none)"){
if($currentmodule != $lastmodule){
$modlines .= <<<HERE
</div>
HERE;
	
}
}
$lastmodule = $currentmodule;
$currentmodule = $thismodule;
$title = ucfirst($thismodule);
$modlines .= <<<HERE
<div class="section-title" data-for="$thismodule">
<h2><i class="fa fa-minus"></i> $title</h2>	
</div>

<div class="section" data-id="$thismodule">

<div class="element-container">
<div class="row">
<div class="col-md-8">
<div class="row">
<div class="form-group">
<div class="col-md-3">
<h4>Description</h4>
</div>
<div class="col-md-2">
<h4>Code</h4>
</div>
<div class="col-md-3">
<h4>Actions</h4>
</div>
</div>	
</div>	
</div>	
</div>	
</div>		
HERE;

}
$modlines .= <<<HERE
<!--$modulename $featuredesc -->
<div class="element-container">
<div class="row">
<div class="col-md-8">
<div class="row">
<div class="form-group">
<div class="col-md-3">
<b>$featuredesc</b>
</div>
<div class="col-md-2">
<input type="hidden" name="default_$featureid" value="$featurecodedefault" id="default_$featureid">
<input type="hidden" name="origcustom_$featureid" value="$featurecodecustom"  id="origcustom_$featureid">

<input type="text" class="form-control" id="custom$featureid" name="custom#$featureid" value="$thiscode" $disablein required pattern="\*{0,2}[0-9]{0,5}">

</div>
<div class="col-md-3">
<span class="radioset">
<input type="checkbox" name="usedefault_$featureid" onclick="fcradioonclick(this);" id="usedefault_$featureid" $dchecked>
<label for="usedefault_$featureid">Customize</label>
</span>
<span class="radioset">
<input type="checkbox" name="ena#$featureid" onclick="" id="ena#$featureid" $echecked>
<label for="ena#$featureid">Enabled</label>
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
		<div class='col-md-12'>
			<div class='fpbx-container'>
				<div class="contianer">
					<h1><?php echo _("Feature Code Admin"); ?></h1>
					<div>
						<!-- Conflict error may display here if there is one-->
						<?php echo $conflicterror ?>
						<!--End of error zone-->
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


