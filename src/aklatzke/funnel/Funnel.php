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
    // The check for instantiated ENVs will need to happen twice
    // throughout this instances lifetime.
    $this->load();

    $this->outputType = $outputType;

    if( $dryRun === 1 )
      $this->isDry = true;
  }
  /**
   * Used as an alternate way to load the storagePath members in
   * case this needs to be instantiated before .env is defined.
   *
   * Instead, it will be loaded on any attempt to bundle the assets.
   */
  private function load(  )
  {
    $envExists = isset($_ENV['FUNNEL_STORAGE_PATH']) || getenv('FUNNEL_STORAGE_PATH');

    if( ! $this->storagePath && $envExists )
      $this->storagePath = isset($_ENV['FUNNEL_STORAGE_PATH']) ? $_ENV['FUNNEL_STORAGE_PATH'] : getenv('FUNNEL_STORAGE_PATH');
  }

  public function addExternal( $uri )
  {
    $this->currentHash = $this->currentHash . $uri;

    $this->registered []= [ $uri, 'external' ];
  }

  public function add( $path )
  {
    $this->currentHash = $this->currentHash . $path;
    // deal with query parameters. this will be cached the same way as the others
    // due to the query parameter being appended to the hash
    if( strpos($path, "?") !== -1 )
    {
      $temp = explode("?", $path);
      // get everything before the query param
      $path = $temp[0];
    }


    $this->registered []= [ $path, 'local' ];
  }

  public function string( $str )
  {
    $this->currentHash = $this->currentHash . md5($str);

    $this->registered []= [ $str, 'string' ];
  }

  public function bundle( $basePath = '' )
  {
    // it's possible the ENV vars were loaded to the global array
    // after our initial creation of this instance (through, for example,
    // a dotenv loader).
    $this->load();

    $this->currentHash = md5($this->currentHash);

    $fullPath = $basePath . $this->storagePath . $this->currentHash . $this->outputType;
    $this->fullPath = "http://" . $_SERVER['HTTP_HOST'] . $this->storagePath . $this->currentHash . $this->outputType;

    if( file_exists( $fullPath ) && ! $this->isDry )
      return file_get_contents( $fullPath );

    $contents = '';

    foreach ($this->registered as $index => $script)
      if( $script[1] === 'string' )
        $contents .= $contents;
      else
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
