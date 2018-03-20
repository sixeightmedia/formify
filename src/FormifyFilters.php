<?php  
namespace Concrete\Package\Formify\Src;

use Page;
use File;
use View;

class FormifyFilters {
  
  function page_url($cID) {
  	$page = Page::getByID($cID);
  	if(is_object($page)) {
    	return View::URL($page->getCollectionPath());
  	}
  }
  
  function file_url($fID) {
  	$file = File::getByID($fID);
  	if(is_object($file)) {
    	return $file->getURL();
  	}
	}
  
}