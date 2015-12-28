<?php namespace aklatzke\funnel;

class Funnel{

  public $storagePath;

  public function __construct( $fileTypeOrPattern )
  {
    $this->storagePath = getenv('funnel_storage_path');
    $this->pattern = $fileTypeOrPattern;
  }

  public function bundle( $flag )
  {
    
  }

  public function clear(){}
}
