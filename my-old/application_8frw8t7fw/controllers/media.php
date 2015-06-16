<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Media extends MY_Controller {

	/*******************************
	**	Academy Entry Image
	**
	**	Description:
	**	This method returns a json object
	**  of the featured image for an academy entry.
	**
	**	@param:		entry_id
	**	@return:	void
	**
	**/
	public function academy_entry_featured_image( $entry_id ) {
		
		////////////////
		// Check to see if it's numeric first.
		////////////////
		if( !is_numeric($entry_id) ) {
			die('Non-numeric entry id.');
		}
		
		$this->load->model('academy_model');
		
		$obj_entry	= $this->academy_model->get_entry($entry_id);
		
		$arr_fields	= array('thumb', 'name');
		$arr_image	= array();
		
		foreach( $arr_fields as $field ) {
			$arr_image[$field]	= $obj_entry->get($field);
		}
		
		////////////////
		// Build Page Array
		////////////////
		$arr_page['arr_images']	= array($arr_image);
		
		$this->load->view('backend/object-templates/media/json-images', $arr_page);
		
	}

	/*******************************
	**	File Uploader
	**
	**	Description:
	**	This method handles files that have been submitted to it.
	**
	**	@param:		void
	**	@return:	void
	**
	**/
	public function file_uploader( $module = 'academy' ) {
		////////////////
		//!Write File Uploader HERE
		////////////////
		$config['upload_path']		= dirname(dirname(dirname(__FILE__))).'/assets/tmp_images/';
		$config['allowed_types']	= 'pdf';
		$config['max_size']			= '5000';
		
		$this->load->library('upload', $config);
		
		if( !$this->upload->do_upload('upload') ) {
			
			////////////////
			// Something Went Wrong
			////////////////
			echo '<span style="color: red">'.$this->upload->display_errors().'</span>';
			return 1;
						
		} else {
			echo '<span style="color: green">File uploaded successfully.</span>';
		}
	}
	
	/*******************************
	**	Image Browser
	**
	**	Description:
	**	This method browsers images by namespace.
	**
	**	@param:		namespace
	**	@return:	void
	**
	**/
	public function image_browser( $namespace = FALSE ) {

		$this->load->helper('image_helper');

		$this->load->library('opencloud');

		switch( $namespace ) {
		
			case 'academy':
				$container = $this->opencloud->set_container('saltsha_academy');
			break;
			case 'profile':
				$container = $this->opencloud->set_container('saltsha_profile');
			break;
			default:
				$container = $this->opencloud->set_container('saltsha');
			break;
		}

		// Set CDN URL
		$cdn_url = $container->publicURL().'/';

		foreach ($this->opencloud->list_objects() as $cur_img) {
			// Filter out thumbnail images
			if (strpos($cur_img['cdn-url'], "_thumb.") === false) {
				$img_data['name'] = $cur_img['cdn-url'];
				$img_data['thumb'] = substr_replace($cur_img['cdn-url'],'_thumb.', strrpos($cur_img['cdn-url'], '.'), 1);
				$img_data['namespace'] = $namespace;
				$arr_images[] = $img_data;
			}
		}

		$arr_images	= array_reverse($arr_images);

		////////////////
		// Build Page Array
		////////////////
		$arr_page['arr_images']	= $arr_images;
		$arr_page['namespace']	= $namespace;
		$arr_page['image_path']	= "";

		$this->load->view('backend/object-templates/media/json-images', $arr_page);

	}

	/*******************************
	**	Image Uploader
	**
	**	Description:
	**	This method handles images that have been
	**  posted to it.
	**
	**	@param:		void
	**	@return:	void
	**
	**/
	public function image_uploader( $module = 'academy' ) {

		$this->load->library('opencloud');

		switch( $module ) {
			case 'academy':
				$namespace = 'academy';
				$container = $this->opencloud->set_container('saltsha_academy');
			break;
			case 'profile':
				$namespace = 'profile';
				$container = $this->opencloud->set_container('saltsha_profile');
			break;
			default:
				$namespace = '';
				$container = $this->opencloud->set_container('saltsha');
			break;
		}

		// Set CDN URL
		$cdn_url = $container->SSLURI().'/';

		// Upload Image
		$config['upload_path']		= dirname(dirname(dirname(__FILE__))).'/assets/tmp_images/';
		$config['allowed_types']	= 'gif|jpg|png|jpeg';
		$config['max_size']			= '5000';
		$config['max_width']		= '2800';
		$config['max_height']		= '2800';

		$this->load->library('upload', $config);

		if( !$this->upload->do_upload('upload') ) {
			// Upload failed
			if ($module != 'profile') {
				echo '<span style="color: red">'.$this->upload->display_errors().'</span>';
			}
			return 1;			
		}

		// Get uploaded image
		$arr_image				= $this->upload->data();
		$arr_image['namespace']	= $namespace;

		// Set thumbnail data
		$config['image_library']	= 'gd2';
		$config['source_image']		= $arr_image['full_path'];
		$config['create_thumb']		= TRUE;
		$config['maintain_ratio']	= FALSE;
		$config['width']			= 125;
		$config['height']			= 125;

		// Set file data
		$date = date("Ymd");
		$name = $arr_image['file_name'];
		$time = time (); 
		$user = $this->session->userdata('id');
		$path_regular = $arr_image['full_path']; 
		$img_type = ['image_type'];
		$file_name_regular = $time.'-'.$date.'-'.$user.'-'.$name;

		// Send image to CDN
		if (!$this->opencloud->add_object($file_name_regular, $path_regular ,$img_type)) {
			if ($module != 'profile') {
				echo '<span style="color: red">There was an error uploading your image.</span>';
			}
		}

		// Load image lib with config data
		$arr_thumb = $this->load->library('image_lib', $config);

		// Resize image
		if( !$this->image_lib->resize() ) {
			if ($module != 'profile') {
				echo '<span style="color: red">Failed to resize image! Please try again.</span>';
			}
		}

		// Get file info
		$file_info = pathinfo($arr_image['file_name']);

		// Define image data
		$name_thumb = $file_info['filename'].'_thumb.'.$file_info['extension'];
		$path_thumb =  $config['upload_path'].$file_info['filename'].'_thumb.'.$file_info['extension'];
		$file_name = $time.'-'.$date.'-'.$user.'-'.$name;
		$file_name_thumb = $time.'-'.$date.'-'.$user.'-'.$name_thumb;

		// Add thumbnail
		if ($this->opencloud->add_object($file_name_thumb, $path_thumb ,$img_type)) {
			if ($module != 'profile') {
				echo '<span id="success" style="color: green; font-family: Arial;">Image uploaded successfully.<br/><br/>Click the "Select Featured Image" tab to choose a featured image for this post.</span>';
			}
		} else {
			if ($module != 'profile') {
				echo '<span id="failure" style="color: red">There was an error uploading your image thumbnail.</span>';
			}
		}

		// Remove temporary images
		unlink($path_regular);
		unlink($path_thumb);

		// If profile image module
		if( $module == 'profile' ) {
			// Load users model
			$this->load->model('users_model');

			// Get current profile image data
			$img_url = $this->users_model->get_profile_image($this->input->post('id'));

			// Check if image exists
			if (trim($img_url) != '' && !is_null($img_url)) {
				// Get file data
				$file_data = pathinfo($img_url);
				$old_file_name = $file_data['basename'];
				$old_thumb_name = $file_data['filename'].'_thumb.'.$file_data['extension'];

				// Remove current profile image and thumbnail
				$this->opencloud->delete_object($old_file_name);
				$this->opencloud->delete_object($old_thumb_name);
			}

			// Set this user's profile image.
			$this->load->model('users_model');
			$this->users_model->set_profile_image($this->input->post('id'), $cdn_url.$file_name);

			// Reload user data before we send to a new page
			if( $this->input->post('id') == $this->current_user->get('id') ) {
				$this->users_lib->reload_current_user();
			}

			// Redirect to user profile page
			redirect('users/moderator/'.$this->input->post('id'));
		}

	}
	
	/*******************************
	**	Upload
	**
	**	Description:
	**	This method displays an upload form for images.
	**
	**	@param:		namespace
	**	@return:	void
	**
	**/
	public function upload( $namespace = "academy" ) {
		
		$this->load->view('backend/object-templates/media/upload-form');
		
	}
	
	/*******************************
	**	Delete image
	**
	**	Description:
	**	This method removes and image from the CDN.
	**
	**	@return:	void
	**
	**/
	public function delete_image( ) {

		// Load image helper
		$this->load->helper('image_helper');

		// Remove image and thumbnail
		if (remove_cdn_image($this->input->post('image_data'))) {
			$response = array('status' => 'success');
		} else {
			$response = array('status' => 'failure');
		}
		
		// Echo response in JSON
		echo json_encode($response);
	}
	
}