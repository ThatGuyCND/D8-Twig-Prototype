<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

class Request {
	
	public $uri;
	
	protected $page;
	
	protected $notes;
	
	protected $response = '';
	
    public function __construct()
    {
		$this->view = new View();
		$this->uri = new URI();
		$this->view->add_global( 'url', $this->uri );
    }
    
    public function execute()
    {
        try
        {	
            $this->get_page();
			$this->load_data();
			$this->handle_session();
			$this->render();
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
		$session = new Session();
		$session->set_state();
		$this->view->add_global( 'session', $session );
		$this->view->add_global( 'user', $session->userdata() );
	}
	
	protected function render()
	{
		$this->view->set_path( $this->page->get_path() );	
		$this->response = $this->view->render();
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