<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 
 * Calling views
 */
class webrtc extends CI_Controller {
    var $webrtc_table = "webrtc_messages"; // signaling db table
    public function __construct() {
        parent::__construct();
         $this->load->helper('url');
         $this->load->database();
    }
    public function index(){
        echo "<h3>HTML5 geolocation:</h3>";
        echo anchor(array('webrtc','html5geo1',""),"html5 geolocation example (1)")."<br>";
        echo "<h3>WebRTC:</h3>";
        echo anchor(array('webrtc','polling_ajax',"empty_table"),"clear prev. chan conersation messages")."<br>";
        echo anchor(array('webrtc','webrtc_bob'),"Bob (callee) ANSWER")."<br>";
        echo anchor(array('webrtc','webrtc_alice'),"Alice (caller) REQUEST")."<br>";
        echo anchor(array('webrtc','pc_1_cp'),"local_pear2pear(2)")."<br>";
        echo anchor(array('webrtc','pc_1'),"local_pear2pear(1)")."<br>";
        echo anchor(array('webrtc','webrtc_camera1'),"camera demo(1)")."<br>";
        echo anchor(array('webrtc','webrtc_camera2'),"camera demo(2)")."<br>";
       // phpinfo();
    }
    public function html5geo1(){
        $this->load->view('html5geo1',array());
    }
     public function webrtc_alice(){
        $this->load->view('webrtc_alice',array());
    }
    public function webrtc_bob(){
        $this->load->view('webrtc_bob',array());
    }
    public function webrtc_camera1(){
        $this->load->view('webrtc_camera1',array());
    }
     public function webrtc_camera2(){
        $this->load->view('webrtc_camera2',array());
    }
    public function pc_1(){
        $this->load->view('pc1',array());
    }
     public function pc_1_cp() {
        $this->load->view('pc1cp', array());
    }
    
    /**
     * Client polling this URL. 
     * Each request client send request message: $_post['req'] with STRING!!! (this may be json)
     * And server answer with 'ans' with array were each value is a json
     * This method for conversation of two persons Alice "caller" (a) and Bob(b) "callee"
     * the name of person stored at the url: polling_ajax/a ore polling_ajax/b
     * If 'a' Alice make request  it store it message at the db_table with 'to' column == 'b'.
     * If 'b' Bob make request message stored with 'to' columnt == 'a'
     * Db table have next columns:
     * 'id', 'to', 'read_flag' {0 - not read, 1 read}, 'message'
     * CREATE TABLE `webrtc_messages` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`to` VARCHAR( 2 ) NOT NULL ,
`read_flag` INT( 1 ) NOT NULL DEFAULT '0',
`message` TEXT NOT NULL ,
INDEX ( `read_flag` )
) ENGINE = MYISAM ;

     * 3437609
     */
    public function polling_ajax(){
        $name  = $this->uri->segment(3);
        if('empty_table' == $name){
            $this->db->empty_table($this->webrtc_table);
            die("message table cleared");
        }
        $to = ('a' == $name)? 'b' : 'a'; // From Alice to Bob
        $req = $this->input->post('req'); // this is must be json
        if(!empty($req)){
            $this->_save_message($to, $req);
        }
        $answers = $this->_get_answers($name);
        $this->_set_read_flag($answers);
        
        $ans = array();
        foreach($answers as $row){
            $ans[] = $row['message'];
        }
        if(!empty($ans)){
        echo $ans[0]; // do not touch any JSON at all.
        }
        else{
            return "";
        }
    }
    private function _save_message($to, $message){
        $database_arr = array(
            'to' => $to,
            'message' => $message,
            'read_flag' => "0", // not read yet
        );
        $this->db->insert($this->webrtc_table, $database_arr);
        
    }
    private function _get_answers($name){
        $query = $this->db->get_where($this->webrtc_table,array("read_flag"=>0, "to"=>$name));
        return $query->result_array();
    }
    private function _set_read_flag($answers){
        foreach($answers as $row){
            $this->db->where(array('id'=>$row['id']));
            $this->db->update($this->webrtc_table, array('read_flag'=>1));
        }
    }
}