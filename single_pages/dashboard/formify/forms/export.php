<?php  defined('C5_EXECUTE') or die('Access Denied'); ?>
<div class="ccm-ui ccm-dashboard-content-full formify" ng-app="FormifyApp">
	
	<?php Loader::packageElement('dashboard/form_nav','formify',array('f'=>$f,'forms'=>$forms)); ?>
	
	<section id="formify-export" ng-controller="ExportController" ng-cloak>
		
		<form method="post" action="<?php  echo View::url('/formify/api/export')?>">
			
			<input type="hidden" name="fID" value="<?php  echo $f->fID; ?>" />
			
			<!--
			<h3><?php  echo t('Export Type'); ?></h3>
			
			<div>
				<label><input type="radio" name="type" value="full" ng-model="exportType" /> <?php  echo t('Full'); ?></label>
				<label><input type="radio" name="type" value="summary" ng-model="exportType" /> <?php  echo t('Summary'); ?></label>
			</div>
			-->

			<h3><?php  echo t('Which records?'); ?></h3>
			
			<div>
				<label><input type="radio" name="records" value="all" ng-model="records" /> <?php  echo t('All'); ?></label>
				<label><input type="radio" name="records" value="range" ng-model="records" /> <?php  echo t('A specific date range'); ?></label>
			</div>
			
			<div id="records-range-dates" ng-show="records == 'range'">
				
				<hr />
				
				<div class="row">
					<div class="col-md-3">
						Start Date
						<?php  echo Core::make('helper/form/date_time')->date('start',''); ?>
					</div>
					<div class="col-md-3">
						End Date
						<?php  echo Core::make('helper/form/date_time')->date('end',''); ?>
					</div>
				</div>
				
			</div>
				
			<hr />

      <h3><?php echo t('Filter Records'); ?></h3>
      <script type="text/javascript">
        var filterIndex = 0;

        var strFilterOptionHtml = "";

        <?php foreach($f->getFields() as $ff) { ?>
            strFilterOptionHtml += "<option value=\"<?php  echo $ff->ffID; ?>\"><?php  echo $ff->label; ?></option>";
        <?php } ?>

        function addFilter() {
            var strFilterHtml;

            strFilterHtml = "<div class='row' style=\"margin-top:4px\" id=\"lFilter" + filterIndex + "\">";
            strFilterHtml += "<div class=\"col-md-3\">";
            strFilterHtml += "<select class=\"form-control\" name=\"fieldValueFilter" + filterIndex + "\" id=\"fieldValueFilter" + filterIndex + "\">";
            strFilterHtml += "<option value=\"\"></option>";
            strFilterHtml += strFilterOptionHtml;
            strFilterHtml += "</select>";
            strFilterHtml += "</div>";
            strFilterHtml += "<div class=\"col-md-1\"><div style=\"margin-top:8px\"><b>matches</b></div></div>";
            strFilterHtml += "<div class=\"col-md-6\">";
            strFilterHtml += "<input type=\"text\" class=\"form-control\" value=\"\" id=\"fieldValueFilterValue\"" + filterIndex + "\" name=\"fieldValueFilterValue" + filterIndex + "\" />";
            strFilterHtml += "</div>";

            strFilterHtml += "<div class=\"col-md-2\">";
            strFilterHtml += "<a onclick=\"removeFilter(this);return(false)\" data-item-remove=\"" + filterIndex + "\" class=\"removeFilter btn btn-danger\">remove</a>";
            strFilterHtml += "</div>";

            strFilterHtml += "</div>";

            $('#dFilters').append(strFilterHtml);
            filterIndex++;
        }

        function removeFilter(t) {
            var t = $(t);
            var filterId = t.attr('data-item-remove');
            var strFilterKey = 'lFilter' + filterId;
            jQuery('#' + strFilterKey).remove();
        }

        $(document).ready(function() {
           $('#btnAddFilter').on('click',addFilter);
        });
      </script>
      
      <a class="btn btn-primary" id="btnAddFilter">Add Filter</a>
      <br/><br/>
      
      <div id="dFilters"></div>
			
			<h3><?php  echo t('Which fields?'); ?></h3>
			
			<div>
				<label><input type="radio" name="columns" value="all" ng-model="columns" /> <?php  echo t('All'); ?></label>
				<label><input type="radio" name="columns" value="certain" ng-model="columns" /> <?php  echo t('Only certain fields'); ?></label>
			</div>
			
			<hr />
			
			<table class="ccm-search-results-table" ng-show="columns == 'certain'">
				
				<thead>
					<tr>
						<th class="false"><span>&nbsp;</span></th>
						<th class="false"><span>Field</span></th>
						<th class="false"><span>Type</span></th>
					</tr>
				</thead>
				
				<tbody>
					
					<tr>
						<td><input type="checkbox" name="includeRecordID" value="true" ng-checked="columns == 'all'" /></td>
						<td>Record ID</td>
						<td>Static</td>
					</tr>
					
					<tr>
						<td><input type="checkbox" name="includeDateSubmitted" value="true" ng-checked="columns == 'all'" /></td>
						<td>Date Submitted</td>
						<td>Static</td>
					</tr>
					
					<tr>
						<td><input type="checkbox" name="includeIPAddress" value="true" ng-checked="columns == 'all'" /></td>
						<td>IP Address</td>
						<td>Static</td>
					</tr>
					
					<tr>
						<td><input type="checkbox" name="includeOwner" value="true" ng-checked="columns == 'all'" /></td>
						<td>Owner</td>
						<td>Static</td>
					</tr>
			
					<?php  foreach($f->getFields() as $ff) { ?>
						<tr>
							<td><input type="checkbox" name="fields[<?php  echo $ff->ffID; ?>]" value="true" checked="checked" ng-checked="columns == 'all'" /></td>
							<td><?php  echo $ff->label; ?></td>
							<td><?php  echo $ff->getType()->name; ?></td>
						</tr>
					<?php  } ?>
					
				</tbody>
			</table>
			
			<input type="submit" class="btn btn-success" value="Run Export" />
			
			<br /><br /><br /><br /><br /><br />
			
		</form>
		
	</section>
	
</div>

<?php  Loader::packageElement('dashboard/formify_nav','formify'); ?>

