<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

class View {
	
	protected $twig;
	
	protected $path = '';
	
	protected $globals = array();
	
	protected $data = array();
  
   	public function __construct()
	{
		$loader = new Twig_Loader_Filesystem( array( SITE_PATH, PT_COMPONENTS_PATH, PT_VIEWS_PATH ) );
		$this->twig = new Twig_Environment($loader, array(
           	'debug'       	=> Config::get('debug'),
           	'auto_reload' 	=> Config::get('auto_reload'),
           	'charset'     	=> Config::get('charset'),
			'cache'			=> TEMPLATE_CACHE,
		));
		
		$this->twig->addExtension(new Twig_Extensions_Extension_Text());
		
		if ( Config::get('debug') )
		{
			$this->twig->addExtension(new Twig_Extensions_Extension_Debug());	
		}
	}
	
	public function set_path( $path )
	{
		$this->path = $path;
	}
	
	public function add_global( $name, $global )
	{
		$this->twig->addGlobal( $name, $global );
	}
	
	public function add_data( $name, $data )
	{
		$this->data[$name] = $data;
	}
    
	public function render()
	{	
		try
		{
			$template = $this->twig->loadTemplate($this->path);
			return $template->render( $this->data );			
		}
		catch( Exception $e )
		{
			if ( Config::get('debug') )
			{
				throw $e;
			}
			else
			{
				throw new Exception( 'Error rendering template ' . $this->path );	
			}
		}	
	}

}

/* End of file classes/view.php */