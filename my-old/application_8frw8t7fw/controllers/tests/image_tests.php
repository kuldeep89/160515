<?php  


class Image_tests extends CI_Controller {
	
	
		public function __construct() {
			parent::__construct();
		}
		
		public function run_tests() {
			
			echo ('<h2>Image Model Tests</h2>
			The following tests have been ran on the Image Model through the controller method image_tests/run_tests <br /><br />');
			
		
			$this->load->library('unit_test');
			$this->load->model('image_model');		
			
			
			////////////////
			// * UNIT TEST: Add Image Test, This test tests whether an image array has been inserted into the image table. ** Had to comment out line 44 of image_model to run tests **
			///////////////
			$arr_image = array('file_name' => 'name', 'file_size' => 'size', 'image_height' => 'height', 'image_width' => 'width', 'image_type' => 'type');
			$test = $this->image_model->add_image( $arr_image, 'academy' );
			echo $this->unit->run($test, 'is_int', 'Unit Test add_image()', 'This test tests whether an image array has been inserted into the image table.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->image_model->add_image( $arr_image, 'academy' ) == true, 'is_true', 'Component Test add_image()', 'This test tests whether an image array has been inserted into the image table.');
			
			////////////////
			// * UNIT TEST: Get Image Test, This test tests whether the get image function has pulled the image related to the $namespace from the image table.
			///////////////
			$test = $this->image_model->get_images( 'academy' );
			echo $this->unit->run($test, 'is_array', 'Unit Test get_images()', 'This test tests whether the get image function has pulled the image related to the $namespace from the image table.');
			//////////////////
			// * COMPONENT TEST: 
			/////////////////
			echo $this->unit->run($this->image_model->get_images( 'academy' ) == true, 'is_true', 'Component Test get_images()', 'This test tests whether the get image function has pulled the image related to the $namespace from the image table.');
			
	}			
}
