<?php 
namespace Concrete\Package\Formify\Controller\Api;

use \Concrete\Package\Formify\Src\FormifyForm;
use \Concrete\Package\Formify\Src\FormifyField;
use Concrete\Core\Application\Service\Dashboard;
use Controller;
use FileImporter;
use Loader;
use Log;
	
class File extends Controller {
	
	public function upload($ffID) {
		
		$uh = Loader::helper('concrete/urls');
		
		$field = FormifyField::getByID($ffID);
		
		//Upload file
		Loader::library("file/importer");
		$fi = new FileImporter();
		$fi->setRescanThumbnailsOnImport(false);
		Log::addEntry(print_r($_FILES['file'],true));
		$resp = $fi->import($_FILES['file']['tmp_name'], $_FILES['file']['name']);
		if (($resp instanceof \Concrete\Core\Entity\File\Version) || ($resp instanceof \Concrete\Core\File\Version)) {
  		
  		if(intval($field->fsID) != 0) {
    		$fs = \Concrete\Core\File\Set\Set::getByID($field->fsID);
    		$fs->addFileToSet($resp);
  		}
  		
  		$response = array();
			$response['status'] = 'success';
			$response['fileID'] = $resp->getFileID();
			$js = Loader::helper('json');
			$r = $js->encode($response);
			echo $r;
			
		} else {
  		$response = array();
			$response['status'] = 'error';
  		switch($resp) {
				case FileImporter::E_FILE_INVALID_EXTENSION:
					$response['error'] = t('Invalid file extension.');
					break;
				case FileImporter::E_FILE_INVALID:
					$response['error'] = t('File exceeds max size of ' . min(ini_get('post_max_size'),ini_get('upload_max_filesize')));
					break;
			}
			$js = Loader::helper('json');
			$r = $js->encode($response);
			echo $r;
		}
			
	}
	
}