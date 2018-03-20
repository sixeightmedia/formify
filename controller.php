<?php  
namespace Concrete\Package\Formify;

use Package;
use BlockType;
use SinglePage;
use Route;
use Page;
use Area;
use Stack;
use BlockTypeSet;
use \Concrete\Package\Formify\Src\FormifyForm;
use \Concrete\Package\Formify\Src\FormifyOptionGroup;

/**
*
* Formify Package.
* @author Justin Garcia <justin@sixeightmedia.net>
*
*/

class Controller extends Package {

	protected $pkgHandle = 'formify';
	protected $appVersionRequired = '5.7.3.1';
	protected $pkgVersion = '3.1';

	public function getPackageDescription() {
		return t('Super-charge your forms and your website.');
	}

	public function getPackageName() {
		return t('Formify');
	}
	
	public function on_start() {
		
		//Load Packages
		require $this->getPackagePath() . '/vendor/autoload.php';
		
		//Register API Routes		
		Route::register('/formify/go/{id}','\Concrete\Package\Formify\Controller\Api\Records::process');
		Route::register('/formify/go/validate/{fID}/{sectionIndex}','\Concrete\Package\Formify\Controller\Api\Records::validate');
		
		Route::register('/formify/api/config/set/{property}/{value}','\Concrete\Package\Formify\Controller\Api\Formify::setConfig');
		Route::register('/formify/api/config/get/{property}','\Concrete\Package\Formify\Controller\Api\Formify::getConfig');
		Route::register('/formify/api/config/get/{property}/{format}','\Concrete\Package\Formify\Controller\Api\Formify::getConfig');
		
		Route::register('/formify/api/form/all','\Concrete\Package\Formify\Controller\Api\Form::all');
		Route::register('/formify/api/form/create','\Concrete\Package\Formify\Controller\Api\Form::create');
		Route::register('/formify/api/form/get/{fID}','\Concrete\Package\Formify\Controller\Api\Form::one');
		Route::register('/formify/api/form/update/{fID}','\Concrete\Package\Formify\Controller\Api\Form::update');
		Route::register('/formify/api/form/delete/{fID}','\Concrete\Package\Formify\Controller\Api\Form::delete');
		Route::register('/formify/api/form/permission/{fID}/{type}/{gID}','\Concrete\Package\Formify\Controller\Api\Form::permission');
		Route::register('/formify/api/form/integration/{fID}/{handle}','\Concrete\Package\Formify\Controller\Api\Form::integration');
		
		Route::register('/formify/api/fields/all/{fID}','\Concrete\Package\Formify\Controller\Api\Fields::all');
		Route::register('/formify/api/fields/create/{fID}','\Concrete\Package\Formify\Controller\Api\Fields::create');
		Route::register('/formify/api/fields/import/{fID}','\Concrete\Package\Formify\Controller\Api\Fields::import');
		Route::register('/formify/api/fields/get/{ffID}','\Concrete\Package\Formify\Controller\Api\Fields::one');
		Route::register('/formify/api/fields/update/{ffID}','\Concrete\Package\Formify\Controller\Api\Fields::update');
		Route::register('/formify/api/fields/delete/{ffID}','\Concrete\Package\Formify\Controller\Api\Fields::delete');
		Route::register('/formify/api/fields/sort','\Concrete\Package\Formify\Controller\Api\Fields::resort');
		Route::register('/formify/api/fields/types','\Concrete\Package\Formify\Controller\Api\Fields::types');
		
		Route::register('/formify/api/records/all/{fID}/{page}/{pageSize}','\Concrete\Package\Formify\Controller\Api\Records::all');
		Route::register('/formify/api/records/search/{fID}/{page}/{pageSize}/{query}','\Concrete\Package\Formify\Controller\Api\Records::all');
		Route::register('/formify/api/records/get/{rID}','\Concrete\Package\Formify\Controller\Api\Records::one');
		Route::register('/formify/api/records/rebuild/{rID}','\Concrete\Package\Formify\Controller\Api\Records::rebuild');
		Route::register('/formify/api/records/update/{rID}','\Concrete\Package\Formify\Controller\Api\Records::update');
		Route::register('/formify/api/records/delete/{rID}','\Concrete\Package\Formify\Controller\Api\Records::delete');
		Route::register('/formify/api/records/sort/{rID}/{adjacentRecordID}','\Concrete\Package\Formify\Controller\Api\Records::resort');
		Route::register('/formify/api/records/approve/{rID}','\Concrete\Package\Formify\Controller\Api\Records::approve');
		Route::register('/formify/api/records/reject/{rID}','\Concrete\Package\Formify\Controller\Api\Records::reject');
		Route::register('/formify/api/records/pend/{rID}','\Concrete\Package\Formify\Controller\Api\Records::pend');
		Route::register('/formify/api/records/migrate/{rID}','\Concrete\Package\Formify\Controller\Api\Records::migrate');
		Route::register('/formify/api/records/index/{fID}','\Concrete\Package\Formify\Controller\Api\Records::index');
		
		Route::register('/formify/api/notifications/all/{fID}','\Concrete\Package\Formify\Controller\Api\Notification::all');
		Route::register('/formify/api/notifications/create/{fID}/{type}','\Concrete\Package\Formify\Controller\Api\Notification::create');
		Route::register('/formify/api/notifications/get/{nID}','\Concrete\Package\Formify\Controller\Api\Notification::one');
		Route::register('/formify/api/notifications/update/{nID}','\Concrete\Package\Formify\Controller\Api\Notification::update');
		Route::register('/formify/api/notifications/delete/{nID}','\Concrete\Package\Formify\Controller\Api\Notification::delete');
		
		Route::register('/formify/api/options/all','\Concrete\Package\Formify\Controller\Api\Options::all');
		Route::register('/formify/api/options/create','\Concrete\Package\Formify\Controller\Api\Options::create');
		Route::register('/formify/api/options/get/{ogID}','\Concrete\Package\Formify\Controller\Api\Options::one');
		Route::register('/formify/api/options/update/{ogID}','\Concrete\Package\Formify\Controller\Api\Options::update');
		Route::register('/formify/api/options/delete/{ogID}','\Concrete\Package\Formify\Controller\Api\Options::delete');
		
		Route::register('/formify/api/rules/all/{fID}','\Concrete\Package\Formify\Controller\Api\Rules::all');
		Route::register('/formify/api/rules/create/{fID}','\Concrete\Package\Formify\Controller\Api\Rules::create');
		Route::register('/formify/api/rules/get/{rID}','\Concrete\Package\Formify\Controller\Api\Rules::one');
		Route::register('/formify/api/rules/update/{rID}','\Concrete\Package\Formify\Controller\Api\Rules::update');
		Route::register('/formify/api/rules/delete/{rID}','\Concrete\Package\Formify\Controller\Api\Rules::delete');
		
		Route::register('/formify/api/templates/all','\Concrete\Package\Formify\Controller\Api\Templates::all');
		Route::register('/formify/api/templates/create/{name}','\Concrete\Package\Formify\Controller\Api\Templates::create');
		Route::register('/formify/api/templates/get/{tID}','\Concrete\Package\Formify\Controller\Api\Templates::one');
		Route::register('/formify/api/templates/update/{tID}','\Concrete\Package\Formify\Controller\Api\Templates::update');
		Route::register('/formify/api/templates/delete/{tID}','\Concrete\Package\Formify\Controller\Api\Templates::delete');
		
		Route::register('/formify/api/groups/all','\Concrete\Package\Formify\Controller\Api\Groups::all');
		Route::register('/formify/api/groups/create','\Concrete\Package\Formify\Controller\Api\Groups::create');
		Route::register('/formify/api/groups/get/{gID}','\Concrete\Package\Formify\Controller\Api\Groups::one');
		Route::register('/formify/api/groups/update/{gID}','\Concrete\Package\Formify\Controller\Api\Groups::update');
		Route::register('/formify/api/groups/delete/{gID}','\Concrete\Package\Formify\Controller\Api\Groups::delete');
		
		Route::register('/formify/api/file/upload/{ffID}','\Concrete\Package\Formify\Controller\Api\File::upload');
		Route::register('/formify/api/export','\Concrete\Package\Formify\Controller\Api\Export::run');
		
	}

	public function install() {
		$pkg = parent::install();
		
		BlockTypeSet::add('formify','Formify',$pkg);
		BlockType::installBlockTypeFromPackage('formify_form', $pkg);
		BlockType::installBlockTypeFromPackage('formify_view', $pkg);
		
		$this->createDashboardPages();
		$this->createOptionGroups();
		
	}
	
	public function upgrade() {
		parent::upgrade();
		
		$pkg = Package::getByHandle($this->pkgHandle);
		
		$this->createDashboardPages();
		$this->createOptionGroups();
		
		$p = Page::getByPath('/dashboard/formify/forms/rules');
		if(is_object($p)) {
			$p->delete();
		}
		
		$this->migrate();
	}
    
  public function createDashboardPages() {
    $pkg = Package::getByHandle($this->pkgHandle);
	
  	if($page = SinglePage::add('/dashboard/formify', $pkg)) {
  		$page->update(array('cName' => 'Formify'));
  	}
  	
  	if($page = SinglePage::add('/dashboard/formify/forms', $pkg)) {
  		$page->update(array('cName' => 'Forms'));
  	}
  	
  	if($page = SinglePage::add('/dashboard/formify/forms/settings', $pkg)) {
  		$page->update(array('cName' => 'Settings'));
  	}
  	
  	if($page = SinglePage::add('/dashboard/formify/forms/records', $pkg)) {
  		$page->update(array('cName' => 'Records'));
  	}
  	
  	if($page = SinglePage::add('/dashboard/formify/forms/fields', $pkg)) {
  		$page->update(array('cName' => 'Fields'));
  	}
  	
  	if($page = SinglePage::add('/dashboard/formify/forms/notifications', $pkg)) {
  		$page->update(array('cName' => 'Notifications'));
  	}
  	
  	if($page = SinglePage::add('/dashboard/formify/forms/export', $pkg)) {
  		$page->update(array('cName' => 'Export'));
  	}
  	
  	if($page = SinglePage::add('/dashboard/formify/forms/attributes', $pkg)) {
  		$page->update(array('cName' => 'Attributes'));
  	}
  	
  	if($page = SinglePage::add('/dashboard/formify/forms/integrations', $pkg)) {
  		$page->update(array('cName' => 'Integrations'));
  	}
  	
  	if($page = SinglePage::add('/dashboard/formify/templates', $pkg)) {
  		$page->update(array('cName' => 'Templates'));
  	}
  	
  	if($page = SinglePage::add('/dashboard/formify/import', $pkg)) {
  		$page->update(array('cName' => 'Import'));
  	}
  	
  	if($page = SinglePage::add('/dashboard/formify/attributes', $pkg)) {
  		$page->update(array('cName' => 'Attributes'));
  	}
  	
  	if($page = SinglePage::add('/dashboard/formify/options', $pkg)) {
  		$page->update(array('cName' => 'Options'));
  	}
  	
  	if($page = SinglePage::add('/dashboard/formify/defaults', $pkg)) {
  		$page->update(array('cName' => 'Defaults'));
  	}
  	
  }
  
  public function createOptionGroups() {
    
    if(!$og = \Concrete\Package\Formify\Src\FormifyOptionGroup::getByName('Countries')) { 
      $og = \Concrete\Package\Formify\Src\FormifyOptionGroup::create('Countries');
      $og->setOptions(array(
        'Afghanistan',
        'Albania',
        'Algeria',
        'American Samoa',
        'Andorra',
        'Angola',
        'Antigua And Barbuda',
        'Argentina',
        'Armenia',
        'Australia',
        'Austria',
        'Azerbaijan',
        'Bahamas',
        'Bahrain',
        'Bangladesh',
        'Barbados',
        'Belarus',
        'Belgium',
        'Belize',
        'Benin',
        'Bhutan',
        'Bolivia',
        'Bosnia And Herzegovina',
        'Botswana',
        'Brazil',
        'Brunei Darussalam',
        'Bulgaria',
        'Burkina Faso',
        'Burma',
        'Burundi',
        'Cambodia',
        'Cameroon',
        'Canada',
        'Cape Verde',
        'Cayman Islands',
        'Central African Republic',
        'Chad',
        'Chile',
        'China',
        'Colombia',
        'Comoros',
        'Congo',
        'Congo',
        'Cook Islands',
        'Costa Rica',
        'Croatia',
        'Cuba',
        'Cyprus',
        'Czech Republic',
        'Denmark',
        'Djibouti',
        'Dominica',
        'Dominican Republic',
        'East Timor',
        'Ecuador',
        'Egypt',
        'El Salvador',
        'Equatorial Guinea',
        'Eritrea',
        'Estonia',
        'Ethiopia',
        'Fiji',
        'Finland',
        'France',
        'Gabon',
        'Gambia',
        'Georgia',
        'Germany',
        'Ghana',
        'Greece',
        'Grenada',
        'Guam',
        'Guatemala',
        'Guinea',
        'Guinea-Bissau',
        'Guyana',
        'Haiti',
        'Honduras',
        'Hong Kong',
        'Hungary',
        'Iceland',
        'India',
        'Indonesia',
        'Iran',
        'Iraq',
        'Ireland',
        'Israel',
        'Italy',
        'Ivory Coast',
        'Jamaica',
        'Japan',
        'Jordan',
        'Kazakhstan',
        'Kenya',
        'Kiribati',
        'Kosovo',
        'Kuwait',
        'Kyrgyzstan',
        'Lao People\'s Democratic Republic',
        'Latvia',
        'Lebanon',
        'Lesotho',
        'Liberia',
        'Libya',
        'Liechtenstein',
        'Lithuania',
        'Luxembourg',
        'Macau',
        'Macedonia',
        'Madagascar',
        'Malawi',
        'Malaysia',
        'Maldives',
        'Mali',
        'Malta',
        'Marshall Islands',
        'Mauritania',
        'Mauritius',
        'Mexico',
        'Micronesia',
        'Moldova',
        'Monaco',
        'Mongolia',
        'Montserrat',
        'Montenegro',
        'Morocco',
        'Mozambique',
        'Namibia',
        'Nauru',
        'Nepal',
        'Netherlands',
        'New Zealand',
        'Nicaragua',
        'Niger',
        'Nigeria',
        'North Korea',
        'Norway',
        'Oman',
        'Pakistan',
        'Palau',
        'Palestinian Territory',
        'Panama',
        'Papua New Guinea',
        'Paraguay',
        'Peru',
        'Philippines',
        'Pitcairn',
        'Poland',
        'Portugal',
        'Qatar',
        'Romania',
        'Russia',
        'Rwanda',
        'Saint Kitts And Nevis',
        'Saint Lucia',
        'Saint Vincent And The Grenadines',
        'Samoa',
        'San Marino',
        'Sao Tome And Principe',
        'Saudi Arabia',
        'Senegal',
        'Serbia',
        'Seychelles',
        'Sierra Leone',
        'Singapore',
        'Slovakia',
        'Slovenia',
        'Solomon Islands',
        'Somalia',
        'South Africa',
        'South Korea',
        'Sandwich Islands',
        'Spain',
        'Sri Lanka',
        'Sudan',
        'Suriname',
        'Swaziland',
        'Sweden',
        'Switzerland',
        'Syria',
        'Taiwan',
        'Tajikistan',
        'Tanzania',
        'Thailand',
        'Togo',
        'Tonga',
        'Trinidad And Tobago',
        'Tunisia',
        'Turkey',
        'Turkmenistan',
        'Tuvalu',
        'Uganda',
        'Ukraine',
        'United Arab Emirates',
        'United Kingdom',
        'United States',
        'Uruguay',
        'Uzbekistan',
        'Vanuatu',
        'Vatican City',
        'Venezuela',
        'Vietnam',
        'Western Sahara',
        'Yemen',
        'Zambia',
        'Zimbabwe'
      ));
    }
    
    if(!$og = \Concrete\Package\Formify\Src\FormifyOptionGroup::getByName('U.S. States')) { 
      $og = \Concrete\Package\Formify\Src\FormifyOptionGroup::create('U.S. States');
      $og->setOptions(array(
        'Alabama',
        'Alaska',
        'American Samoa',
        'Arizona',
        'Arkansas',
        'California',
        'Colorado',
        'Connecticut',
        'Delaware',
        'Florida',
        'Georgia',
        'Guam',
        'Hawaii',
        'Idaho',
        'Illinois',
        'Indiana',
        'Iowa',
        'Kansas',
        'Kentucky',
        'Louisiana',
        'Maine',
        'Maryland',
        'Massachusetts',
        'Michigan',
        'Minnesota',
        'Mississippi',
        'Missouri',
        'Montana',
        'Nebraska',
        'Nevada',
        'New Hampshire',
        'New Jersey',
        'New Mexico',
        'New York',
        'North Carolina',
        'North Dakota',
        'Northern Mariana Islands',
        'Ohio',
        'Oklahoma',
        'Oregon',
        'Pennsylvania',
        'Puerto Rico',
        'Rhode Island',
        'South Carolina',
        'South Dakota',
        'Tennessee',
        'Texas',
        'U.S. Minor Outlying Islands',
        'Utah',
        'Vermont',
        'Virgin Islands',
        'Virginia',
        'Washington',
        'Washington, D.C.',
        'West Virginia',
        'Wisconsin',
        'Wyoming'
      ));
    }
    
    if(!$og = \Concrete\Package\Formify\Src\FormifyOptionGroup::getByName('Days of the Week')) { 
      $og = \Concrete\Package\Formify\Src\FormifyOptionGroup::create('Days of the Week');
      $og->setOptions(array(
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday'
      ));
    }
    
    if(!$og = \Concrete\Package\Formify\Src\FormifyOptionGroup::getByName('Months of the Year')) { 
      $og = \Concrete\Package\Formify\Src\FormifyOptionGroup::create('Months of the Year');
      $og->setOptions(array(
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
      ));
    }
    
  }
  
  public function migrate() {
    
    $forms = \Concrete\Package\Formify\Src\FormifyForm::getAll();
    
    if(count($forms) > 0) {
	    foreach($forms as $f) {
		    if(!$f->isMigrated()) {
		      $rs = $f->getRecordSet();
			    $rs->setPageSize(0);
			    $records = $rs->getRecords();
			    
			    if(count($records) > 0) {
				    foreach($records as $r) {
					    $r->migrate();
				    }
			    }
			    
			    $f->logMigration();
			  }
		  }
	  }
    
  }
}
