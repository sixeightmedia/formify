$(document).ready(function() {
	
	//Add active CSS class on focus
	$('.formify-form input, .formify-form select').on('focus',function() {
		$(this).addClass('active');
	});
	
	//Remove active CSS class on blur
	$('.formify-form input, .formify-form select').on('blur',function() {
		$(this).removeClass('active');
	});
	
	//Setup checkbox functionality
	$('.formify-form.with-style .formify-checkbox-label').unbind('click').on('click',function(e) {
		e.preventDefault();
		
		var ffID = $(this).attr('data-formify-ffid');
		var $i = $('i.fa',this);
		var $input = $('input',this);
		
		if($i.hasClass('fa-check')) {
			$i.removeClass('fa-check');
			$input.prop('checked',false);
		} else {
			$i.addClass('fa-check');
			$input.prop('checked',true);
		}
		
		$input.trigger('change');
	});
	
	//Setup radio button functionality
	$('.formify-form.with-style .formify-radio-label').unbind('click').on('click',function(e) {
		e.preventDefault();
		
		var ffID = $(this).attr('data-formify-ffid');
		var $i = $('i.fa',this);
		var $input = $('input',this);
		
		$('input[data-formify-ffid="' + ffID + '"]').prop('checked',false);
		$('i[data-formify-ffid="' + ffID + '"]').removeClass('fa-check');
		
		$i.addClass('fa-check');
		$input.prop('checked',true);
		
		$input.trigger('change');
	});
	
});

(function($) {
	
	$.fn.rulify = function(r) {
		
		return this.each(function() {
			
			var $container = $(this);
			var $field = $('[name]',$container);
			
			var $targetContainer = $('#formify-field-container-' + r.ffID);
			var $targetField = $('[name]',$targetContainer);
			
			function getValue() {
				var v;
				switch($container.attr('data-field-type')) {
					case 'checkboxes':
						v = $('[name]:checked',$container).val();
						break;
					case 'radio':
						v = $('[name]:checked',$container).val();
						break;
					default:
						v = $('[name]',$container).val();
				}
				
				return v;
			}
			
			function criteriaIsMet() {
				switch(r.comparison) {
					case '=':
						if(r.value == getValue()) {
							return true;
						} else {
							return false;
						}
						break;
					case '!=':
						if(r.value != getValue()) {
							return true;
						} else {
							return false;
						}
						break;
					case '~':
						if(getValue().indexOf(r.value) > -1) {
							return true;
						} else {
							return false;
						}
						break;
					case '!~':
						if(getValue().indexOf(r.value) == -1) {
							return true;
						} else {
							return false;
						}
						break;
				}
			}
			
			function ruleIsSatisfied() {
				if($container.data('rules-' + r.rID + '-satisfied') == 'true') {
					return true;
				} else {
					return false;
				}
			}
			
			function increment() {
				//Set rule-X-satisfied to false
				$container.data('rules-' + r.rID + '-satisfied','false');
				
				//Increase unmet rule count
				$targetContainer.attr('data-unmet-rule-count',parseInt($targetContainer.attr('data-unmet-rule-count')) + 1);
			}
			
			function decrement() {
				//Set rule-X-satisfied to true
				$container.data('rules-' + r.rID + '-satisfied','true');
				
				//Decrease unmet rule count
				$targetContainer.attr('data-unmet-rule-count',parseInt($targetContainer.attr('data-unmet-rule-count')) - 1);
			}
			
			function triggerRuleAction() {
				if($targetContainer.attr('data-rule-action') == 'show') {
					$targetContainer.show();
				} else {
					$targetContainer.hide();
				}
			}
			
			function reverseRuleAction() {
				if($targetContainer.attr('data-rule-action') == 'show') {
					$targetContainer.hide();
				} else {
					$targetContainer.show();
				}
			}
			
			function checkRules() {
				if((criteriaIsMet()) && (!ruleIsSatisfied())) {
					decrement();
				}
				
				if((!criteriaIsMet()) && (ruleIsSatisfied())) {
					increment();
				}
				
				if($targetContainer.attr('data-rule-requirement') == 'any') {
					if($targetContainer.attr('data-unmet-rule-count') < $targetContainer.attr('data-rule-count')) {
						triggerRuleAction();
					} else {
						reverseRuleAction();
					}
				}
				
				if($targetContainer.attr('data-rule-requirement') == 'all') {
					if($targetContainer.attr('data-unmet-rule-count') == 0) {
						triggerRuleAction();
					} else {
						reverseRuleAction();
					}
				}
			}
			
			checkRules();
			
			//Bind Change Event
			$field.on('change',function() {
				checkRules();
			});
		
		});
	
	}
	
}(jQuery));

(function($) {

  $.fn.formify = function() {

    return this.each(function() {
            
      $f = $(this);
      
      $('.formify-product select',$f).on('change',function() {
      	calculateTotal();
    	});
    	
    	$('.formify-product input[type="text"]',$f).on('keyup',function() {
      	calculateTotal();
    	});
    	
    	$('.formify-product input[type="checkbox"]',$f).on('change',function() {
      	calculateTotal();
    	});
        
      $('.formify-nav-button',$f).on('click',function(e) {
  			e.preventDefault();
  			var sectionIndex = $(this).attr('data-formify-section-index');
  			var targetSectionIndex = $(this).attr('data-formify-section-index-target');
  			var $targetSection = $('.formify-section[data-formify-section-index="' + targetSectionIndex + '"]');
  			var fID = $f.attr('data-fid');
  			
  			$navButton = $(this);
  			
  			if(sectionIndex < targetSectionIndex) { // Do AJAX validation if going to next section
  				
  				$('i',$navButton).show();
  				
  				//Post form data via ajax
  				$.ajax({
  					type: 'POST',
  					url: CCM_REL + '/index.php/formify/go/validate/' + fID + '/' + sectionIndex,
  					data: $f.serialize(),
  					error: function(xhr, textStatus, errorThrown){
  						showError($f,'There was an error submitting this form. Please contact the administrator of this site.');
  						console.log(errorThrown);
  					},
  					success: function(r) {
  						var errors = JSON.parse(r);
  						if(errors.length > 0) {
  							/* Clear error fields and remove error message */
  							$('.formify-error-message',$f).remove();
  							$('.formify-field-container',$f).removeClass('formify-error');
  			
  							$.each(errors, function(i,error) {
  								 /* Highlight the error fields */
  								$('#formify-field-container-' + error.ffID).addClass('formify-error').each(function() {
  									var $container = $(this);
  									
  									var children = $container[0].getElementsByTagName('*');
  									
  									for(var i = 0;i < children.length;i++) {
  										$(children[i]).on('focus',function() {
  											$('#formify-field-container-' + error.ffID).removeClass('formify-error');
  										});
  									}
  								});
  								showError($f,error.message);
                  $('i',$navButton).hide();
  							});
  							
  						} else {
    						hideError();
  							$('.formify-section',$f).hide();
  							$('i',$navButton).hide();
  							$targetSection.show();
  						}
  					}
  				});
  				
  			} else {
    		  hideError();
  				$('.formify-section',$f).hide();
  				$targetSection.show();
  			}
  			
  			return false;
  		});
          
      $(this).on('submit',function() {
    			
    		$f = $(this);
        
  			//If form is already processing, don't do anything
  			if(formifyIsProcessing($f)) {
  				return false;
  			} else {
  				
  				var fID = $f.attr('data-fid');
  				var rID = $f.attr('data-rid');
  				var context = $f.attr('data-context');
  				
  				if(rID == '0') {
  					var mode = 'add';
  				} else {
  					var mode = 'edit';
  				}
  				
  				//Setup processing animations and prevent concurrent submissions
  				formifyStartProcessing($f);
  				
  				//Post form data via ajax
  				$.ajax({
  					type: 'POST',
  					url: $f.attr('action') + '?ajax=1',
  					data: $f.serialize(),
  					error: function(xhr, textStatus, errorThrown){
  						showError($f,'There was an error submitting this form. Please contact the administrator of this site.');
  						console.log(errorThrown);
  						formifyEndProcessing($f);
  					},
  					success: function(r) {
  						
  						try { // Verify that a proper response was received. If not, there was a server-side error
  							var response = JSON.parse(r);
  						} catch(e) {
  							showError($f,'There was an error submitting this form. Please contact the administrator of this site.');
  							formifyEndProcessing($f);
  						}
  						
  						if(response) { // Process the response from the server
  							
  							if(response.errors.length > 0) { // Process errors
  								
  								/* Clear error fields and remove error message */
  								$('.formify-error-message',$f).remove();
  								$('.formify-field-container',$f).removeClass('formify-error');
  				
  								
  								$.each(response.errors, function(i,error) {
  								
  									switch(error.type) {
  										case 'submissions':
  											showError($f,error.message);
  											break;
  										case 'permissions':
  											showError($f,error.message);
  											break;
  										case 'captcha':
  											$('.formify-captcha-image',$f).load(FORMIFY_TOOLS_PATH + 'block/refresh_captcha');
  											showError($f,error.message);
  											break;
  										case 'validation':
  											 /* Highlight the error fields */
  											$('#formify-field-container-' + error.ffID).addClass('formify-error').each(function() {
  												var $container = $(this);
  												
  												var children = $container[0].getElementsByTagName('*');
  												
  												for(var i = 0;i < children.length;i++) {
  													$(children[i]).on('focus',function() {
  														$('#formify-field-container-' + error.ffID).removeClass('formify-error');
  													});
  												}
  											});
  											showError($f,error.message);
  											break;
  										default:
  											showError($f,error.message);
  											break;
  									}
  								});
  								
  								formifyEndProcessing($f);
  								
  							} else { // No errors
  								
  								if(context == 'dashboard') {
  									if(mode == 'add') { // Adding a record
    									window.location = CCM_REL + '/index.php/dashboard/formify/forms/records/edit/' + fID + '?success=1';
  									} else { // Editing a record
  										window.location = CCM_REL + '/index.php/dashboard/formify/forms/records';
  								  }
  								} else {
  									switch(response.action) {
  										case 'redirect':
  											//Redirect
  											window.location = response.url;
  											break;
  										case 'post':
  											// Create new form
  											var $form = $('<form></form>');
  											$form.attr('action',response.url);
  											$form.attr('method','post');
  											
  											$.each(response.postData,function(key,val) {
  												var $input = $('<input></input>');
  												$input.attr('type','hidden');
  												$input.attr('name',key);
  												$input.attr('value',response.postData[key]);
  												$form.append($input);
  											});
  											
  											$form.insertAfter($f)
  											$form.submit();
  											break;
  										case 'message':
  											//Hide the form
  											$f.fadeOut(200,function() {
  												//Show the response message
  												$msg = $('<div></div>');
  												$msg.css('opacity',0);
  												$msg.addClass('formify-message');
  												$msg.attr('id','formify-message-' + fID);
  												$msg.html(response.message);
  												$msg.insertAfter($f);
  												$msg.fadeTo(200,1);
  											});
  											break;
  									}
  								}
  								
  							}
  							
  						}
  						
  					}
  				});
  				
  				return false;
  			}
  		});
  		
  		function showError($f,text) {
    		if($('.formify-error-message',$f).length > 0) {
    			$e = $('.formify-error-message',$f);
    		} else {
    			$e = $('<div></div>');
    			$e.addClass('formify-error-message');
    			$f.append($e);
    		}
    		$e.html(text);
    	}
  	
      function hideError() {
    		$('.formify-error-message',$f).remove();
  	  }
  	
      function formifyIsProcessing($f) {
    		if($f.hasClass('processing')) {
    			return true;
    		} else {
    			return false;
    		}
    	}
  	
      function formifyStartProcessing($f) {
    		$f.addClass('processing');
    		
    		//Get all submit buttons
    		var $element = $('input[type="submit"]',$f);
    		
    		$element.each(function() {
    			$(this).attr('data-text',$(this).val())
    			$(this).val('Processing');
    		});
    		
    		$element.addClass('animate');
    		
    		animateProcessing($element);
    	}
  	
      function formifyEndProcessing($f) {
    		$f.removeClass('processing');
    		
    		//Get all submit buttons
    		var $element = $('input[type="submit"]',$f);
    		
    		$element.removeClass('animate');
    		
    		$element.each(function() {
    			$(this).prop('disabled',false);
    			$(this).val($(this).attr('data-text'));
    		});
    	}
  	
      function animateProcessing($element) {
    		if($element.hasClass('animate')) {	
    			$element.animate({"opacity":0.5},800)
          .animate({"opacity":"1"},800,function() {
    				animateProcessing($element);
    		  });
    		}
      }
      
      function calculateTotal($f) {
      	var total = 0;
      	$('.formify-product select',$f).each(function() {
        	total += ($(this).val() * $(this).attr('data-formify-commerce-multiplier'));
        });
        
        $('.formify-product input[type="text"]',$f).each(function() {
          total += ($(this).val() * $(this).attr('data-formify-commerce-multiplier'));
      	});
        
        $('.formify-product input[type="checkbox"]',$f).each(function() {
          if($(this).is(':checked')) {
            total += ($(this).val() * $(this).attr('data-formify-commerce-multiplier'));
          }
      	});
        
        $('.formify-product input[type="hidden"]',$f).each(function() {
          total += ($(this).val() * $(this).attr('data-formify-commerce-multiplier'));
      	});
      	
      	$('.formify-commerce-total',$f).html(parseFloat(Math.round(total * 100) / 100).toFixed(2));
    	}
  		
    });
    
  }

}(jQuery));