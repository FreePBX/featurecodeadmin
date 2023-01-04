<form autocomplete="off "class="fpbx-submit" name="frmAdmin" action="config.php?display=featurecodeadmin" method="post">
    <input type="hidden" name="action" value="save">
    <div class="display no-border">
        <div class="container-fluid">

            <!-- Conflict error may display here if there is one-->
            <?php if (! empty($conflict['conflicterror']) && is_array($conflict['conflicterror'])): ?>
                <div>
                    <script>javascript:alert('<?php echo _("You have feature code conflicts with extension numbers in other modules. This will result in unexpected and broken behavior."); ?>')</script>
			        <div class='alert alert-danger'><?php echo _("Feature Code Conflicts with other Extensions"); ?></div>
                    <?php echo implode('<br />', $conflict['conflicterror']); ?>
                </div>
            <?php endif ?>
            <!--End of error zone-->
            
            <!--Generated-->
            <?php foreach($modules as $rawname => $data): ?>
                <div class="section-title" data-for="<?php echo $rawname?>">
                    <h2><i class="fa fa-minus"></i> <?php echo $data['title']?></h2>
                </div>
                <div class="section" data-id="<?php echo $rawname?>">
                    <div class="element-container hidden-xs">
                        <div class="row">
                            <div class="form-group">
                                <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                    <h4><?php echo _("Description")?></h4>
                                </div>
                                <div class="col-lg-4 col-md-3 col-sm-7 col-xs-6">
                                    <h4><?php echo _("Code")?></h4>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-5 col-xs-6 col-actions">
                                    <h4><?php echo _("Actions")?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php foreach($data['items'] as $item): ?>
                        <div class="element-container <?php echo !empty($conflict['exten_conflict_arr'][$item['code']]) ? 'has-error' : ''?>">
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                        <label class="control-label" for="<?php echo $item['feature']?>"><?php echo $item['title']?></label>
                                        <?php if(!empty($item['help'])): ?>
                                            <i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $item['feature']?>"></i>
                                        <?php endif ?>
                                    </div>
                                    <div class="col-lg-4 col-md-3 col-sm-7 col-xs-12">
                                        <input type="text" name="fc[<?php echo $item['module']?>][<?php echo $item['feature']?>][code]" value="<?php echo $item['code']?>" id="custom_<?php echo $item['id']?>" data-default="<?php echo $item['default']?>" placeholder="<?php echo $item['default']?>" data-custom="<?php echo $item['custom']?>" class="form-control extdisplay" <?php echo (!$item['iscustom']) ? 'readonly' : ''?> required pattern="[0-9A-D\*#]*">
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12 col-actions">
                                        <span class="radioset">
                                            <input type="checkbox" data-for="custom_<?php echo $item['id']?>" name="fc[<?php echo $item['module']?>][<?php echo $item['feature']?>][customize]" class="custom" id="usedefault_<?php echo $item['id']?>" <?php echo ($item['iscustom']) ? 'checked' : ''?>>
                                            <label for="usedefault_<?php echo $item['id']?>"><?php echo _("Customize")?></label>
                                        </span>
                                        <span class="radioset">
                                            <input type="checkbox" class="enabled" name="fc[<?php echo $item['module']?>][<?php echo $item['feature']?>][enable]" id="ena_<?php echo $item['id']?>" <?php echo ($item['isenabled']) ? 'checked' : ''?>>
                                            <label for="ena_<?php echo $item['id']?>"><?php echo _("Enabled")?></label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <span id="<?php echo $item['feature']?>-help" class="help-block fpbx-help-block"><?php echo $item['help']?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <br/>
            <?php endforeach; ?>
            <!--END Generated-->

            <!-- Custom feature codes -->
            <?php if(isset($moduleCustomFeaturecodes) && !empty($moduleCustomFeaturecodes['customCodes'])): ?>
                <div class="section-title" data-for="<?php echo 'Custom feature codes'; ?>">
                    <h2><i class="fa fa-minus"></i> <?php echo _('Custom feature codes'); ?></h2>
                </div>
                <div class="section" data-id="<?php echo 'Custom feature codes'; ?>">
                <div class="section-title" data-for="<?php echo $moduleCustomFeaturecodes['moduleName']; ?>">
                    <h2><i class="fa fa-minus"></i> <?php echo $moduleCustomFeaturecodes['moduleName']; ?> </h2>
                </div>
                <div class="section" data-id="<?php echo $moduleCustomFeaturecodes['moduleName']; ?>">
                    <div class="element-container hidden-xs">
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-8">
                                    <h4><?php echo _("Description")?></h4>
                                </div>
                                <div class="col-md-4">
                                    <h4><?php echo _("Code")?></h4>
                                </div>
                            </div>
                        </div>
                    </div>	
                    <?php
                        foreach($moduleCustomFeaturecodes['customCodes'] as $code):
                        isset($moduleCustomFeaturecodes['featureCode']) ? $featurecode = $moduleCustomFeaturecodes['featureCode'] : '';
                        ?>
                        <div class="element-container">
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-8">
                                        <label class="control-label"> <?php echo $code['reason']; ?> </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" disabled value="<?php echo $featurecode .  $code['code']; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                </div>
                <br/>
            <?php endif ?>
            <!-- End custom feature codes -->
        </div>
    </div>
</form>