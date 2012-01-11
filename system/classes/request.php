<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

class Request {
	
	public $uri;
	
	public $page_path;

	public $view;
		
	public $store;
	
	public $pages;
	
	public $response = '';
	
	protected $extension_manager;
	
	protected $user_cookie_name = 'user';
	
	public function __construct()
	{
		$this->view = new View();
		$this->uri = new URI();
		$this->store = new Store();
		$this->pages = new Pages();
		$this->view->add_global( 'request', $this->get_request_data() );
		$this->view->add_global( 'url', $this->uri );
		$this->view->add_global( 'pages', $this->pages );
		$this->view->add_global( 'data', Data::instance() );
		$this->view->add_global( 'assets', new Assets() );
		$this->view->add_global( 'config', Config::get_all() );
		$this->view->add_global( 'utils', new Utils() );
		$this->extension_manager = Extension_manager::instance();
		$this->view->add_global( 'actions', $this->extension_manager->get_actions( $this ) );
	}
	
	public function execute()
	{
		try
		{
			if ( $this->uri->segment_1 == Config::get('json_data_trigger' ) )
			{
				// json data request
				$this->json_data();
			}
			elseif ( $this->uri->segment_1 == Config::get('short_url_trigger' ) && $this->uri->segment_2 )
			{
				// short url request
				$page = $this->pages->get_by_id( $this->uri->segment_2 );
				if ( $page )
				{
					$this->uri->redirect( $page->nice_url, 301 );
				}
				else
				{
					throw new Exception('404');
				}
			}
			else
			{
				// regular page request
				$this->get_page_path();
				$this->handle_session();
				$this->render();
			}
		}
		catch( Exception $e )
		{
			// TODO: use differnet Exception subclasses to distinguish different errors
			switch ( $e->getMessage() )
			{
				case '404':
					$this->show_404();
				break;
				
				default:
					$this->display_error( $e );
				break;
			}
		}
	}
	
	public function response()
	{
		$this->extension_manager->run_hook('before_display', array( $this ));
		return $this->response;
	}
	
	protected function get_request_data()
	{
	    // TODO: Do we want to sanitize this in any way?
	    return array(
	       'post' => $_POST,
	       'get' => $_GET,
	       'cookie' => $_COOKIE,
	       'request' => $_REQUEST
	    );
	}
	
	protected function get_page_path()
	{
		$page = $this->pages->get_by_path( $this->uri->string() );
				
		if ( ! $page )
		{
			// can't find anything, throw a 404 error.
			throw new Exception('404');
		}
		else
		{
			$this->page_path = $page->fs_path;
		}
	}
	
	protected function handle_session()
	{				
		$lit = Config::get('login_trigger');
		$lot = Config::get('logout_trigger');
		
		if ( isset( $_REQUEST[$lit] ) )
		{
			// login
			$user_details = $this->get_user_details( $_REQUEST[$lit] );
			if ( $user_details ) $this->store->set( $this->user_cookie_name, $user_details );
		}
		elseif ( isset( $_REQUEST[$lot] ) )
		{
			// logout
			$this->store->clear( $this->user_cookie_name );
		}
		
		$user = $this->store->get( $this->user_cookie_name ); // grab the user, if set.
		
		$this->view->add_global( 'store', $this->store );
		$this->view->add_global( 'user', $user );
	}
	
	protected function render()
	{
		$this->extension_manager->run_hook('before_render', array( $this ));
		$this->view->set_path( $this->page_path );	
		$this->response = $this->view->render();
	}
	
	// trys to find a user's details with a specific id. If not found it just returns the ID.
	protected function get_user_details( $user_id )
	{
		if ( ! empty($user_id) )
		{
			$data = Data::instance();
			$user = $data->find('users.'. $user_id);
			return $user ? $user : array( 'id' => $user_id );
		}
		return NULL;
	}
	
	protected function json_data()
	{
		if ( $this->uri->segment_2 )
		{
			$parts = $this->uri->segments();
			$file = $parts[1];
			$data = Data::instance();
			
			unset($parts[0]);
			unset($parts[1]);

			$result = $data->find($file . implode('.', array_values($parts)) );
			
			$this->response = $result === NULL ? '' : json_encode($result);
		}
	}
	
	protected function show_404()
	{
		header('HTTP/1.0 404 Not Found');
		
		// if there is a 404 content page, display that.

		$page = $this->pages->get_by_path( '404' );
		
		if ( $page )
		{
			try
			{
				$this->view->set_path( $page->fs_path );
				$this->response = $this->view->render();
				return;
			}
			catch( Exception $e )
			{
				// ...
			}
		}
		
		$this->view->set_path('PT/404.html');
		$this->response = $this->view->render();
	}

	protected function display_error( $e )
	{	
		$this->view->add_data('exception', $e);
		
		if ( Config::get('debug') )
		{	
			$this->view->add_data('debug_on', true);
			
			$this->view->add_data('error_code', Helpers::get_highlighted_source($e->getFile(), $e->getLine(), 10));
		
			$trace_code = array();
			foreach( $e->getTrace() as $trace )
			{
				$trace_code[] = Helpers::get_highlighted_source($trace['file'], $trace['line'], 6);
			}
		
			$this->view->add_data('trace_code', $trace_code);
		}
		
		$this->view->set_path('PT/error.html');
		$this->response = $this->view->render();
	}

}
