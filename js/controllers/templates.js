FormifyApp.controller('TemplatesController',function($scope,$http,$filter) {
  
  formifyTemplateEditorHeader = CodeMirror.fromTextArea(document.getElementById("formify-template-editor-header"), {
    mode: "text/html",
    matchBrackets: true
  });
	
	formifyTemplateEditorContent = CodeMirror.fromTextArea(document.getElementById("formify-template-editor-content"), {
    mode: "text/html",
    matchBrackets: true
  });
	
	formifyTemplateEditorFooter = CodeMirror.fromTextArea(document.getElementById("formify-template-editor-footer"), {
    mode: "text/html",
    matchBrackets: true
  });
	
	formifyTemplateEditorEmpty = CodeMirror.fromTextArea(document.getElementById("formify-template-editor-empty"), {
    mode: "text/html",
    matchBrackets: true
  });
	
	Concrete.event.bind('ConcreteSitemap', function(e, instance) {
	
	    Concrete.event.bind('SitemapSelectPage', function(e, data) {
	        if (data.instance == instance) {
	            Concrete.event.unbind(e);
	            $scope.activeTemplate.appendText($scope.activeTemplate.activeSection,'{{ "' + data.cID + '" | page_url }}');
	            $.fn.dialog.closeTop();
	        }

	    });	
	});
	
	Concrete.event.bind('FileManagerSelectFile', function(e, data) {
		$scope.activeTemplate.appendText($scope.activeTemplate.activeSection,'{{ "' + data.fID + '" | file_url }}');
	    $.fn.dialog.closeTop();
	    
	    //Bug workaround
	    $('#ccm-menu-click-proxy').remove();
		
	});
	
	$scope.formifyTemplate = function(tData) {
		for(var p in tData) {
			this[p] = tData[p];
		}
	}	
	
	$scope.formifyTemplate.prototype.update = function() {
  	this.header = formifyTemplateEditorHeader.getValue();
  	this.content = formifyTemplateEditorContent.getValue();
  	this.footer = formifyTemplateEditorFooter.getValue();
  	this.empty = formifyTemplateEditorEmpty.getValue();
  	$http.post(CCM_DISPATCHER_FILENAME + '/formify/api/templates/update/' + this.tID,this);
		this.notSaved = false;
	}
	
	$scope.formifyTemplate.prototype.delete = function() {
		var t = this;
		if(confirm('Are you sure you want to delete this template?')) {
			$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/templates/delete/' + t.tID).success(function() {
				delete $scope.activeTemplate;
				$scope.templates.splice($scope.templates.indexOf(t),1);
			});
		}
	}
	
	$scope.formifyTemplate.prototype.appendPlaceholder = function(section) {
		this.appendText(section,this.activePlaceholder);
	}
	
	$scope.formifyTemplate.prototype.appendText = function(section,text) {
  	
  	switch(section) {
    	case 'header':
    	  formifyTemplateEditorHeader.replaceSelection(text);
    	  break;
      case 'content':
    	  formifyTemplateEditorContent.replaceSelection(text);
    	  break;
      case 'footer':
    	  formifyTemplateEditorFooter.replaceSelection(text);
    	  break;
      case 'empty':
    	  formifyTemplateEditorEmpty.replaceSelection(text);
    	  break;
  	}
  	
		//$scope.$apply();
	}
	
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/templates/all/').success(function(tData) {
		$scope.templates = [];
		for(var i = 0; i < tData.length; i++) {
			var t = new $scope.formifyTemplate(tData[i]);
			$scope.templates.push(t);
		}
	});
	
	$scope.add = function(name) {
		$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/templates/create/' + name).success(function(tData) {
			var newTemplate = new $scope.formifyTemplate(tData);
			$scope.templates.unshift(newTemplate);
		});
	}
	
	$scope.save = function(t) {
		$scope.templates[$scope.templates.indexOf(t)] = $scope.activeTemplate;
		delete $scope.activeTemplate;
		t.update();
	}
	
	$scope.detail = function(t) {
		$scope.activeTemplate = t;
		
		formifyTemplateEditorHeader.setValue('');
		formifyTemplateEditorHeader.setValue(t.header);
		
  	formifyTemplateEditorContent.setValue('');
  	formifyTemplateEditorContent.setValue(t.content);
  	
  	formifyTemplateEditorFooter.setValue('');
  	formifyTemplateEditorFooter.setValue(t.footer);
  	
  	formifyTemplateEditorEmpty.setValue('');
  	formifyTemplateEditorEmpty.setValue(t.empty);
	}
	
	$scope.formifyForm = function(fData) {
		for(var p in fData) {
			this[p] = fData[p];
		}
	};
	
	$http.get(CCM_DISPATCHER_FILENAME + '/formify/api/form/all').success(function(fData) {
		$scope.forms = [];
		for(var i = 0; i < fData.length; i++) {
			var f = new $scope.formifyForm(fData[i]);
			$scope.forms.push(f);
		}
	});
	
	$scope.openSitemap = function(section) {
		$scope.activeTemplate.activeSection = section;
		$.fn.dialog.open({
			title: 'Select a page',
			href: CCM_TOOLS_PATH + '/sitemap_search_selector',
			width: '80%',
			modal: true,
			height: 550
		});
	}
	
	$scope.openFileManager = function(section) {
		$scope.activeTemplate.activeSection = section;
		$.fn.dialog.open({
			title: 'Select a file',
			href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/file/search',
			width: '80%',
			modal: true,
			height: 550
		});
	}
	
});


