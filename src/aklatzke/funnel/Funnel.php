<?php namespace Aklatzke\Funnel;

use Exception;

class Funnel{

  public $storagePath;

  protected $outputType;
  protected $isDry;
  protected $fullPath;

  protected $registered = [];

  private $currentHash;

  const DRY_RUN = 1;

  public function __construct( $outputType, $dryRun = 0 )
  {
    $this->storagePath = $_ENV['FUNNEL_STORAGE_PATH'];
    $this->outputType = $outputType;

    if( $dryRun === 1 )
      $this->isDry = true;
  }

  public function addExternal( $uri )
  {
    $this->currentHash = $this->currentHash . $uri;

    $this->registered []= [ $uri, 'external' ];
  }

  public function add( $path )
  {
    $this->currentHash = $this->currentHash . $path;

    $this->registered []= [ $path, 'local' ];
  }

  public function bundle( $basePath = '' )
  {
    $this->currentHash = md5($this->currentHash);

    $fullPath = $basePath . $this->storagePath . $this->currentHash . $this->outputType;
    $this->fullPath = "http://" . $_SERVER['HTTP_HOST'] . $this->storagePath . $this->currentHash . $this->outputType;

    if( file_exists( $fullPath ) )
      return file_get_contents( $fullPath );

    $contents = '';

    foreach ($this->registered as $index => $script)
      $contents .= file_get_contents( $script[0] );

    if( $this->isDry )
      return $contents;

    file_put_contents( $fullPath , $contents );

    return $contents;
  }

  public function getPath(  )
  {
    if( ! $this->fullPath )
      throw new Exception( 'aklatzke\funnel: You must run bundle() before you can fetch the path.' );

    return $this->fullPath;
  }
}
