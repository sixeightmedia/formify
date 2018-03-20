<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php global $c; ?>

<?php if(($enableSearch) || ($enableSort)) { ?>
	<form method="get" action="<?php echo $this->action('search'); ?>">
    
    <?php if($enableSearch) { ?>
  	<div class="form-control">
    	 <label style="display:block"><?php echo t('Search'); ?></label>
       <input type="text" name="q" value="<?php echo htmlentities($query); ?>" />
  	</div>
		<?php } ?>
		
		<?php if(($enableUserSort) && (count($sortableFields) > 0)) { ?>
		<div class="form-control">
      <label style="display:block"><?php echo t('Sort by'); ?></label>
      <select name="sortBy">
        <option value=""></option>
        <?php if(count($sortableFields) > 0) { ?>
          <?php foreach($sortableFields as $ff) { ?>
            <option value="<?php echo $ff->ffID; ?>" <?php if($sortBy == $ff->ffID) { ?>selected="selected"<?php } ?>><?php echo htmlentities($ff->label); ?></option>
          <?php } ?>
        <?php } ?>
      </select>
  	</div>
		<?php } ?>
		
		<?php if($enableDateFilter) { ?>
		<!--
		<div class="form-control">
  		<label>Date filter</label>
  		<input type="text" /> - 
  		<input type="text" />
		</div>
		-->
		<?php } ?>
		
		<div class="form-control">
  		<input type="submit" value="<?php echo $sortButtonLabel; ?>" />
  		<?php if($enableSearchReset) { ?>
    		<!--<input type="submit" value="<?php echo t('Reset'); ?>" />-->
      <?php } ?>
		</div>
	</form>
	<hr />
<?php } ?>

<?php if($template) { ?>
	
	<?php $template->render($records); ?>
	
	<?php if($paginator) { ?>	 
		<div class="pagination" style="text-align:center">
			<div style="float:left"><?php echo $paginator->getPrevious()?></div>
			<div style="float:right"><?php echo $paginator->getNext()?></div>
			<?php echo $paginator->getPages()?>
		</div>		
	<?php } ?>
	
<?php } ?>
	