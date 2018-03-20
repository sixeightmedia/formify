<?php defined('C5_EXECUTE') or die(_("Access Denied.")) ?>

<?php if(is_object($f)) { ?>
  <form class="formify-form <?php if(($disableDefaultCSS != '1') && (Config::get('concrete.formify.disable_default_css') != true)) { ?>with-style<?php } ?>" id="formify-form-<?php echo $f->fID; ?>-<?php echo intval($bID); ?>" data-bid="<?php echo intval($bID); ?>" data-fid="<?php echo $f->fID; ?>" data-rid="<?php echo intval($rID); ?>" data-context="<?php echo $context; ?>" enctype="multipart/form-data" method="post" action="<?php echo DIR_REL; ?>/index.php/formify/go/<?php echo $f->fID; ?>">
  	
  	<input type="hidden" name="rID" value="<?php echo $rID; ?>" />
  	<input type="hidden" name="token" value="<?php echo $token; ?>" />
  	<input type="hidden" name="source" value="<?php echo htmlentities(URL::to(Page::getCurrentPage())); ?>" />
  	<input type="hidden" name="referrer" value="<?php echo htmlentities($_SERVER['HTTP_REFERER']); ?>" />
  	
  	<?php echo Core::make('token')->output('formify_submit'); ?>
  	
  	<?php if(count($f->getSections()) > 0) { ?>
    	<?php foreach($f->getSections() as $s) { ?>
    		<div class="formify-section" data-formify-section-index="<?php echo $s->index; ?>">
    			
    			<?php if(count($s->getFields()) > 0) { ?>
      			<?php foreach($s->getFields() as $ff) { ?>
      				<div
      					class="formify-field-container <?php echo $ff->containerClass; ?>"
      					id="formify-field-container-<?php echo $ff->ffID; ?>"
      					data-ffid="<?php echo $ff->ffID; ?>"
      					data-field-type="<?php echo $ff->getType()->handle; ?>"
      					data-rule-count="<?php echo count($ff->getRules()); ?>"
      					data-unmet-rule-count="<?php echo count($ff->getRules()); ?>"
      					data-rule-action="<?php echo $ff->ruleAction; ?>"
      					data-rule-requirement="<?php echo $ff->ruleRequirement; ?>"
      				>
      					<?php $ff->render(); ?>
      				</div><!--/.formify-field-container-->
      			<?php } ?>
      		<?php } ?>
    			
    			<?php if($s->index == count($f->getSections())) { //Append additional info to last section ?>
    			
    				<?php foreach($f->getActiveIntegrations() as $i) { ?>
    					
    					<?php foreach($i->getFields() as $ff) { ?>
    						<div class="formify-field-container">
    							<?php $ff->render(); ?>
    						</div>
    					<?php } ?>
    					
    				<?php } ?>
    			
    				<?php if($captcha) { ?>
    				<div class="formify-field-container">
    					<div class="formify-field-input captcha">
    						<?php 
    						$captchaLabel = $captcha->label();
    						if (!empty($captchaLabel)) {
    							?>
    							<label class="control-label"><?php echo $captchaLabel; ?></label>
    							<?php 
    						}
    						?>
    						<div><?php $captcha->display(); ?></div>
    						<div><?php $captcha->showInput(); ?></div>
    					</div>
    				</div>
    				<?php } ?>
    				
    			<?php } //End check for last section ?>
    			
    			<?php if($f->commerceShowTotal) { ?>
    			  <hr />
    			  <div class="formify-field-container">
    					<p>Total: <?php echo $f->commerceCurrencySymbol; ?><span class="formify-commerce-total"></span></p>
    			  </div>
    			<?php } ?>
    			
    			<div class="formify-field-container">
    				<div class="formify-field-input">
    					<?php if($s->index != 1) { ?>
    					<button class="formify-nav-button" data-formify-section-index="<?php echo $s->index; ?>" data-formify-section-index-target="<?php echo $s->index - 1; ?>"><?php echo t('Previous'); ?></button>
    					<?php } ?>
    					<?php if($s->index < count($f->getSections())) { ?>
    					<button class="formify-nav-button" data-formify-section-index="<?php echo $s->index; ?>" data-formify-section-index-target="<?php echo $s->index + 1; ?>"><?php echo t('Next'); ?> <i style="display:none" class="fa fa-spinner fa-spin"></i></button>
    					<?php } ?>
    					<?php if($s->index == count($f->getSections())) { ?>
    					<input type="submit" value="<?php echo htmlentities($f->submitLabel); ?>" />
    					<?php } ?>
    				</div>
    			</div>
    		
    		</div><!--/.formify-section-->
    	<?php } ?>
    <?php } ?>
  	
  </form>
  
  <?php if($_GET['nojs'] != 1) { ?>
    <script type="text/javascript">	
    $(document).ready(function() {
    	
    	<?php foreach($f->getRules() as $r) { ?>
    	$('#formify-field-container-<?php echo $r['comparisonFieldID']; ?>').rulify(<?php echo json_encode($r); ?>);
    	<?php } ?>
    	
    	$('#formify-form-<?php echo $f->fID; ?>-<?php echo intval($bID); ?>').formify();
    });
    </script>
  <?php } ?>
  
  <script type="text/javascript">
	//Handle file uploads
	var formifyUploadsInProgress = 0;
	console.log('uploads: ' + formifyUploadsInProgress);
	$(document).ready(function() {
  	if(typeof $.fn.fileupload !== 'undefined') {
  		$('.formify-file','#formify-form-<?php echo $f->fID; ?>-<?php echo intval($bID); ?>').fileupload({
  			pasteZone: null,
  			dataType: 'json',
  			add: function(e,data) {
    			var $fieldContainer = $('#formify-field-container-' + $(this).attr('data-ffid'));
  				$('#' + $(this).attr('data-trigger')).hide();
  				$('#' + $(this).attr('data-progress')).show();
  				$('#' + $(this).attr('data-cancel')).show();
  				formifyUploadsInProgress++;
          console.log('uploads: ' + formifyUploadsInProgress);
          var $submit = $('#formify-form-<?php echo $f->fID; ?>-<?php echo intval($bID); ?> input[type="submit"]');
          $submit.attr('data-original-value',$submit.val());
          $submit.val('Uploads in progress...');
  				$submit.attr('disabled','disabled');
  				$('.formify-file-name',$fieldContainer).html(data.files[0].name);
  				data.submit();
  			},
  			progressall: function(e,data) {
    			var $fieldContainer = $('#formify-field-container-' + $(this).attr('data-ffid'));
  				var $percent = $('.formify-file-percent',$fieldContainer);
  				var $cancel =  $('#' + $(this).attr('data-cancel'));
  				var progress = parseInt(data.loaded / data.total * 100, 10);
  				if(progress == 100) {
  					$cancel.hide(); 
  					$percent.html('Upload complete.');
  				} else {
  					$percent.html('Uploading ' + progress + '%');
  				}
  			},
  			done: function(e,data) {
  				var $fieldContainer = $('#formify-field-container-' + $(this).attr('data-ffid'));
  				formifyUploadsInProgress--;
          console.log('uploads: ' + formifyUploadsInProgress);
  				if(formifyUploadsInProgress == 0) {
    				var $submit = $('#formify-form-<?php echo $f->fID; ?>-<?php echo intval($bID); ?> input[type="submit"]');
    				$submit.val($submit.attr('data-original-value'));
  					$submit.removeAttr('disabled');
  				}
  				console.log(data.result);
  				if(data.result.status == 'success') {
    				$('input[type="hidden"]',$fieldContainer).val(data.result.fileID);
          } else {
            $('.formify-file-progress',$fieldContainer).html('Error: ' + data.result.error);
          }
  			}
  		});
    }
	});
</script>

<?php } ?>

