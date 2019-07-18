<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$data['instansi']	= $this->_get_instansi();
		$data['tahun']		= $this->_get_two_year_before();
		$data['logged_in']  = $this->session->userdata('logged_in');
		$this->load->view('welcome_message',$data);
		
	}
	
	public function doLogin()
	{
		
		$name  = $this->_remove_space($this->input->post('username'));
		$pwd   = $this->_remove_space($this->input->post('password'));
		
		$sql   = "SELECT id_dd_user,nama FROM dd_user WHERE username='$name' AND password =to_base64('$pwd')";	
		$query = $this->db->query($sql);	 
		if($query->num_rows() > 0)
		{			
			$row   = $query->row();			
			$data  = array('nama'  		      => $row->nama,			   
						   'logged_in'        => TRUE,
						   'id_dd_user'	      => $row->id_dd_user,
				  
				    );              
			$this->session->set_userdata($data);
			
			echo json_encode($data);			   
		}
		else 
		{
			
			echo json_encode( array('logged_in'        => FALSE,'msg'  => 'Wrong username or password'));	
				
        }
		
	}
	
	public function Logout()
	{
	    $this->session->sess_destroy();           		
	    redirect('welcome');
	}
	
    function _get_instansi()
	{
		$sql	="SELECT id_instansi,nama_instansi FROM instansi";
		$query 		= $this->db->query($sql);
		return $query;
	}	
	
	function _get_two_year_before()
	{
	    $year = date("Y");
		$yearDoubleBack = date("Y", strtotime($year . " - 1 year"));
		$years = range($yearDoubleBack,$year);
		return  $years;
	}	
	
	function _remove_space($string)
	{
         $text = $str=preg_replace('/\s+/', '', $string);
		 return $text;		 
	}
	
	
}
