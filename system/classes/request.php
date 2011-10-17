<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

class Request {
	
	public $uri;
	
	protected $page;

	protected $view;
		
	protected $store;
	
	protected $response = '';
	
	protected $user_cookie_name = 'user';
	
    public function __construct()
    {
		$this->view = new View();
		$this->uri = new URI();
		$this->store = new Store();
		$this->view->add_global( 'url', $this->uri );
    }
    
    public function execute()
    {
        try
        {
			if ( $this->uri->segment_1 == '__data' )
			{
				$this->json_data();
			}
			else
			{
	            $this->get_page();
				$this->load_data();
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
		return $this->response;
	}
	
    protected function get_page()
    {
        $this->page = new Page($this->uri);

        if ( ! $this->page->exists() )
        {
            // can't find anything, throw a 404 error.
            throw new Exception('404');
        }
    }

	protected function load_data()
	{
		// first load GET data etc
		$this->view->add_global( 'get', $_GET );
		
		// then grab any data from the data directory
		$data = Data::instance();
		$this->view->add_global( 'data', $data->load_all( DATA_PATH, DATA_CACHE ) );
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
		$this->view->set_path( $this->page->get_path() );	
		$this->response = $this->view->render();
	}
	
	// trys to find a user's details with a specific id. If not found it just returns the ID.
	protected function get_user_details( $user_id )
	{
		if ( ! empty($user_id) )
		{
			$data = Data::instance();
			$user = $data->find('users', $user_id);
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
			
			$result = $data->find($file, implode('.', array_values($parts)) );
			
			$this->response = $result === NULL ? '' : json_encode($result);
		}
	}
	
	protected function show_404()
    {
        header('HTTP/1.0 404 Not Found');
        
        // if there is a 404 content page, display that.

		$page = new Page( new URI('404') );

        if ( $page->exists() )
        {
			try
			{
				$this->view->set_path( $page->get_path() );	
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
		// TODO: Nicer error printing
		$this->view->add_data('message', $e->getMessage());
		$this->view->set_path('PT/error.html');
		$this->response = $this->view->render();
	}
		
}