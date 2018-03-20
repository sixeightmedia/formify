<?php  
namespace Concrete\Package\Formify\Src;

define('TABLE_FORMIFY_FIELDS','FormifyFields');
define('TABLE_FORMIFY_OPTIONS','FormifyOptions');
define('TABLE_FORMIFY_RECORDS','FormifyRecords');
define('TABLE_FORMIFY_ANSWERS','FormifyAnswers');
define('TABLE_FORMIFY_TEMPLATES','FormifyTemplates');

use \Concrete\Package\Formify\Src\FormifyForm;	
use \Concrete\Package\Formify\Src\FormifyField;	
use \Concrete\Package\Formify\Src\FormifyFieldType;
use \Concrete\Package\Formify\Src\FormifyTemplateObject;
use \Concrete\Package\Formify\Src\FormifyFilters;
use \Liquid\Liquid;
use \Liquid\Template as LiquidTemplate;
use User;
use UserInfo;
use Loader;
use Package;
use Page;
use Log;
use File;

class FormifyTemplate {
	
	private $assignableProperties = array(
		'tID',
		'fID',
		'name',
		'type',
		'header',
		'content',
		'footer',
		'empty'
	);
	
	public function get($id) {
		if(is_numeric($id)) {
			$t = self::getByID($id);
		} else {
			$t = self::getByHandle($id);
		}
		
		return $t;
	}
	
	public function getByID($tID) {
		$db = Loader::db();
		$t = new self;
		
		$tData = $db->getRow("SELECT * FROM " . TABLE_FORMIFY_TEMPLATES . " WHERE tID = ?",array($tID));
		
		if (($tData['tID'] == $tID) && ($tID != 0)) {
			foreach($tData as $col => $val) {
				$t->$col = $val;
			}
			
			$t->placeholders = $t->getAvailablePlaceholders();
			
			return $t;
		} else {
			return false;
		}
	}
	
	public function getByHandle($handle) {
		$path = DIR_PACKAGES . '/formify/elements/templates/' . $handle;
		if(file_exists($path)) {
			$txt = Loader::helper('text');
			$t = new self;
			$t->tID = $handle;
			$t->type = 'list';
			$t->handle = $handle;
			$t->name = $txt->unhandle($handle);
			$t->header = @file_get_contents($path . '/header.html');
			$t->header = ($t->header ? $t->header : '');
			$t->content = @file_get_contents($path . '/content.html');
			$t->content = ($t->content ? $t->content : '');
			$t->footer = @file_get_contents($path . '/footer.html');
			$t->footer = ($t->footer ? $t->footer : '');
			$t->empty = @file_get_contents($path . '/empty.html');
			$t->empty = ($t->empty ? $t->empty : '');
			$t->isFile = true;
			return $t;
		} else {
			return false;
		}
	}
	
	public function all() {
		$db = Loader::db();
		$tData = $db->getAll("SELECT tID FROM " . TABLE_FORMIFY_TEMPLATES . "");
		$templates = array();
		if(count($tData) > 0) {
			foreach($tData as $tRow) {
				$templates[] = self::get($tRow['tID']);
			}
		}
		
		$fh = Loader::helper('file');
		$files = array();
		$path = DIR_PACKAGES . '/formify/elements/templates/';
		if (file_exists($path)) {
			$templateDirs = $fh->getDirectoryContents($path);
			if(count($templateDirs) > 0) {
				foreach($templateDirs as $td) {
					$templates[] = self::get($td);
				}
			}
		}
		
		return $templates;
	}
	
	public function create($name) {
		$db = Loader::db();
		$db->execute("INSERT INTO " . TABLE_FORMIFY_TEMPLATES . " (tID) VALUES (0)");
		$tID = $db->Insert_ID();
		$t = self::get($tID);
		$t->set('name',$name);
		return $t;
	}
	
	public function delete() {
		$db = Loader::db();
		$db->execute("DELETE FROM " . TABLE_FORMIFY_TEMPLATES . " WHERE tID=?",array($this->tID));
	}
	
	public function propertyIsAssignable($property) {
		foreach($this->assignableProperties as $ap) {
			if($ap == $property) {
				return true;
			}	
		}
		return false;
	}
	
	public function set($property,$value) {
		$db = Loader::db();
		if($this->propertyIsAssignable($property)) {
			$db->replace(TABLE_FORMIFY_TEMPLATES,array('tID'=>$this->tID,$property=>$value),'tID');
		}
		$this->$property = $value;
	}
	
	public function setRecords($records) {
		$this->records = $records;
	}
	
	public function setBlockID($bID) {
		$this->bID = $bID;
	}
	
	public function setDetailCollectionID($cID) {
		$this->detailCollectionID = $cID;
	}
	
	public function getAvailablePlaceholders() {
		if(count($this->placeholders) > 0) {
			return $this->placeholders;
		} else {
			$placeholders = array();
			
			$placeholders[] = array('label'=>t('Username'),'handle'=>'{{ user }}');
			$placeholders[] = array('label'=>t('User Email'),'handle'=>'{{ user.email }}');
			$placeholders[] = array('label'=>t('User ID'),'handle'=>'{{ user.id }}');
			$placeholders[] = array('label'=>t('Timestamp'),'handle'=>'{{ timestamp }}');
			$placeholders[] = array('label'=>t('IP Address'),'handle'=>'{{ ip }}');
			$placeholders[] = array('label'=>t('Record ID'),'handle'=>'{{ id }}');
			$placeholders[] = array('label'=>t('Detail URL'),'handle'=>'{{ detailurl }}');
			$placeholders[] = array('label'=>t('List URL'),'handle'=>'{{ listurl }}');
			$placeholders[] = array('label'=>t('Primary Field Value'),'handle'=>'{{ primary }}');
			
			if($f = \Concrete\Package\Formify\Src\FormifyForm::get($this->fID)) {
				foreach($f->getFields() as $ff) {
					$placeholders[] = array('label'=>$ff->label,'handle'=>'{{ ' . $ff->handle . ' }}');
				}
			}
			
			return $placeholders;
		}
	}
	
	public function render($records = '',$return = false) {
		
		$this->setRecords($records);
		
		if((count($this->records) == 0) || ($this->records == '')) {
			$content = $this->empty;
		} elseif(!is_array($this->records)) { // Detail view
  		$this->fID = $this->records->fID;
			$content = $this->getDetail();
		} else { // List View
  		$this->fID = $this->records[0]->fID;
		  $content = $this->header . $this->getList() . $this->footer;
		}
		
		if($return) {
			return $content;
		} else {
			echo $content;
		}
	}
	
	public function getDetail() {
  	return $this->parse($this->records,$this->content);
	}
	
	public function getList() {
  	$content = '';
		foreach($this->records as $r) {
			$content .= $this->parse($r,$this->content);
		}
		return $content;
	}
	
	public function parse($r,$content) {
  	
  	$template = new LiquidTemplate();
  	
  	$template->registerFilter('\Concrete\Package\Formify\Src\FormifyFilters');
  	
  	$template->parse($content);
  	
  	$data = $this->getPlaceholderValues($r);
  	
		return $template->render($data);
	}
	
	public function getPlaceholderValues($r) {
  	$data = array();
  	
  	$data['answers'] = [];
    $data['emailableanswers'] = [];

    //Field Values
  	if($f = \Concrete\Package\Formify\Src\FormifyForm::get($this->fID)) {
			foreach($f->getFields() as $ff) {
  			
  			if(($ff->getType()->handle == 'attachment') || ($ff->getType()->handle == 'file')) {
    			$fileID = intval($r->getAnswerValue($ff->handle));
  				if($fileID) {
  					$file = File::getByID($r->getAnswerValue($ff->handle));
  					if(is_object($file)) {
    					$url = $file->getApprovedVersion()->getURL();
    				}
  				}
  			}
  			
  			$answer = FormifyTemplateObject::get([
    			'default' => $r->getFriendlyAnswerValue($ff->handle),
  			  'label' => $ff->label,
  			  'value' => $r->getFriendlyAnswerValue($ff->handle),
  			  'url' => $url
  		  ]);
  			
  			$data[$ff->handle] = $answer;
  			$data['answers'][] = $answer;
  			
  			if($ff->includeInEmail) {
    			$data['emailableanswers'][] = $answer;
  			}
  			
			}
		}
		
		//User
  	$data['user'] = FormifyTemplateObject::get([
			'username' => $r->username,
		  'id' => $r->uID,
		  'email' => $r->email
	  ]);
  	
  	//Timestamp
  	$data['timestamp'] = $r->created / 1000;
  	
  	//Status
    $data['status'] = $r->status;

    //ID
    $data['id'] = $r->rID;
    
    //IP
    $data['ip'] = $r->ipAddress;
    
    //Amount Charged
    $data['amountcharged'] = $r->amountCharged;
    
    //Amount Paid
    $data['amountpaid'] = $r->amountPaid;
    
    //Primary Value
    $data['primary'] = $r->name;
    
    //Source
    $data['source'] = $r->source;
    
    //Referrer
    $data['referrer'] = $r->referrer;

    //Detail URL
    if(intval($this->detailCollectionID) == 0) {
			$detailPage = Page::getCurrentPage();
		} else {
			$detailPage = Page::getByID($this->detailCollectionID);
		}
		
		if($detailPage) {
			$data['detailurl'] = $detailPage->getCollectionLink() . '?rID[' . $this->bID . ']=' . $r->rID;
		}
    
    //List URL
    $listPage = Page::getCurrentPage();
    if(is_object($listPage)) {
  		$data['listurl'] = $listPage->getCollectionLink();
    }
		
		return $data;
  	
	}
  
}