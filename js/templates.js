var formifyTemplateEditorHeader = '';
var formifyTemplateEditorContent = '';
var formifyTemplateEditorFooter = '';
var formifyTemplateEditorEmpty = '';

$(document).ready(function() {
	
	var activeTextarea = '';

	$('#formify-templates-tabs-nav a').click(function(e) {
		e.preventDefault();
		$('li.active').removeClass('active');
		$(this).parent().addClass('active');
		$('.formify-templates-tab').hide();
		$($(this).attr('href')).show();
		
	});
	
});


	
