<div class="container-fluid">
	<div class="row">
		<div class='col-md-8'>
			<div class='fpbx-container'>
				<form autocomplete="off "class="fpbx-submit" name="frmAdmin" action="config.php?display=featurecodeadmin" method="post">
					<input type="hidden" name="action" value="save">
					<div class="display no-border">
						<div class="container-fluid">
							<h1><?php echo _("Feature Code Admin"); ?></h1>
							<div>
								<!-- Conflict error may display here if there is one-->
								<?php echo $conflicterror ?>
								<!--End of error zone-->
							</div>
							<!--Generated-->
							<?php //echo $modlines ?>
							<?php foreach($modules as $rawname => $data) { ?>
								<div class="section-title" data-for="<?php echo $rawname?>">
									<h2><i class="fa fa-minus"></i> <?php echo $data['title']?></h2>
								</div>
								<div class="section" data-id="<?php echo $rawname?>">
									<div class="element-container hidden-xs">
										<div class="row">
											<div class="form-group">
												<div class="col-md-6">
													<h4>Description</h4>
												</div>
												<div class="col-md-2">
													<h4>Code</h4>
												</div>
												<div class="col-md-4">
													<h4>Actions</h4>
												</div>
											</div>
										</div>
									</div>
									<?php foreach($data['items'] as $item) {?>
										<div class="element-container">
											<div class="row">
												<div class="form-group">
													<div class="col-md-6 control-label">
														<?php echo $item['title']?>
													</div>
													<div class="col-md-2">
														<input type="text" name="fc[<?php echo $item['module']?>][<?php echo $item['feature']?>][code]" value="<?php echo $item['code']?>" id="custom_<?php echo $item['id']?>" data-default="<?php echo $item['default']?>" data-custom="<?php echo $item['custom']?>" class="form-control code" <?php echo (!$item['iscustom']) ? 'readonly' : ''?> required pattern="\*{0,2}[0-9]{0,5}">
													</div>
													<div class="col-md-4">
														<span class="radioset">
															<input type="checkbox" data-for="custom_<?php echo $item['id']?>" name="fc[<?php echo $item['module']?>][<?php echo $item['feature']?>][customize]" class="custom" id="usedefault_<?php echo $item['id']?>" <?php echo ($item['iscustom']) ? 'checked' : ''?>>
															<label for="usedefault_<?php echo $item['id']?>">Customize</label>
														</span>
														<span class="radioset">
															<input type="checkbox" name="fc[<?php echo $item['module']?>][<?php echo $item['feature']?>][enable]" id="ena_<?php echo $item['id']?>" <?php echo ($item['isenabled']) ? 'checked' : ''?>>
															<label for="ena_<?php echo $item['id']?>">Enabled</label>
														</span>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
								</div>
								<br/>
							<?php } ?>
							<!--END Generated-->
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>