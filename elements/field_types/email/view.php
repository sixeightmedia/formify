<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="formify-field-label">
	<label>
	  <?php  echo $field->label; ?> <?php  echo $field->requiredIndicator; ?>
	  <div class="formify-field-description"><?php echo $field->description; ?></div>
  </label>
</div>
<div class="formify-field-input formify-email <?php  echo $field->fieldClass; ?>">
	<input type="email" id="formify-field-<?php  echo $field->ffID; ?>" name="<?php  echo $field->ffID; ?>" value="<?php  echo htmlspecialchars($field->defaultValue); ?>" placeholder="<?php  echo htmlspecialchars($field->placeholder); ?>" maxlength="<?php  echo $field->maxLength; ?>" />
</div>