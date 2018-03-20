<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php  $al = Loader::helper('concrete/asset_library'); ?>
<div class="formify-field-label">
	<label>
	  <?php  echo $field->label; ?> <?php  echo $field->requiredIndicator; ?>
	  <div class="formify-field-description"><?php echo $field->description; ?></div>
  </label>
</div>

<?php if(intval($field->defaultValue) > 0) { ?>
	<?php $fileID = File::getByID($field->defaultValue); ?>
	<div class="formify-field-input formify-file-manager <?php  echo $field->fieldClass; ?>">
		<?php echo $al->file('formify-file-selector-' . $field->ffID, $field->ffID, t('Choose File'), $fileID);?>
	</div>
<?php } else { ?>
<div class="formify-field-input formify-attachment">
	<span class="formify-file-button">
		<span class="formify-file-button-text">
			<a href="javascript:void(0)" class="<?php  echo $field->fieldClass; ?>"><?php  echo t('Select File'); ?></a>
		</span>
		<input type="file"
			class="formify-field formify-file"
			id="formify-file-field-<?php  echo $field->ffID; ?>"
			name="file"
			placeholder="<?php  echo htmlspecialchars($field->placeholder); ?>"
			maxlength="<?php  echo $field->maxLength; ?>"
			data-trigger="formify-file-trigger-<?php  echo $field->ffID; ?>"
			data-progress="formify-file-progress-<?php  echo $field->ffID; ?>"
			data-cancel="formify-file-cancel-<?php  echo $field->ffID; ?>"
			data-url="<?php  echo DIR_REL . '/' . DISPATCHER_FILENAME; ?>/formify/api/file/upload/<?php echo $field->ffID; ?>"
			data-ffid="<?php echo $field->ffID; ?>"
		/>
	</span>
	
	<div class="formify-file-progress" id="formify-file-progress-<?php  echo $field->ffID; ?>">
		<span class="formify-file-name" id="formify-file-name-<?php  echo $field->ffID; ?>"></span>:
		<span class="formify-file-percent" id="formify-file-percent-<?php  echo $field->ffID; ?>"></span>
	</div>
	
	<a href="#" class="formify-file-cancel" id="formify-file-cancel-<?php  echo $field->ffID; ?>">Cancel</a>
	<input type="hidden" id="formify-field-<?php  echo $field->ffID; ?>" name="<?php  echo $field->ffID; ?>" />
	
</div>
<?php } ?>