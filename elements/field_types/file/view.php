<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php  $al = Loader::helper('concrete/asset_library'); ?>

<?php 
	if(intval($field->defaultValue) > 0) { 
		$fileID = File::getByID($field->defaultValue);
	}
?>

<div class="formify-field-label">
	<label>
	  <?php  echo $field->label; ?> <?php  echo $field->requiredIndicator; ?>
	  <div class="formify-field-description"><?php echo $field->description; ?></div>
  </label>
</div>
<div class="formify-field-input formify-file-manager <?php  echo $field->fieldClass; ?>">
	<?php  echo $al->file('formify-file-selector-' . $field->ffID, $field->ffID, t('Choose File'), $fileID);?>
</div>