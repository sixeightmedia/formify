<?php  
namespace Concrete\Package\Formify\Src;

class FormifyTemplateObject {
  
  public function __toString () {
    return $this->defaultValue;
  }
  
  public function get($data) {
    $a = new self;
    if(count($data) > 0) {
      $i=0;
      foreach($data as $key => $value) {
        if($i == 0) {
          $a->defaultValue = $value;
        }
        $a->$key = $value;
        $i++;
      }
    }
    return $a;
  }
  
}