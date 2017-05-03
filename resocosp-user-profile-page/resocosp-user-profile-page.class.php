<?php 

class UserProfilePage
{
	public function __construct(){
		add_action( 'wp_enqueue_scripts', array( $this, 'user_profile_page_add_scripts'), 30);
		add_action( 'wp_enqueue_scripts', array( $this, 'user_profile_page_add_styles') );
		add_action( 'wp_ajax_resocosp_get_user_page_params', array( $this, 'get_user_page_params'));
		add_action( 'wp_ajax_nopriv_resocosp_get_user_page_params', array( $this, 'get_user_page_params'));
		add_action( 'wp_ajax_resocosp_get_user_data_by_id', array( $this, 'get_user_data_by_id'));
		add_action( 'wp_ajax_nopriv_resocosp_get_user_data_by_id', array( $this, 'get_user_data_by_id'));
		add_action( 'wp_ajax_resocosp_user_page_update_password', array( $this, 'user_page_update_password'));
		add_action( 'wp_ajax_nopriv_resocosp_user_page_update_password', array( $this, 'user_page_update_password'));
		add_action( 'wp_ajax_resocosp_update_is_public', array( $this, 'update_is_public'));
		add_action( 'wp_ajax_nopriv_resocosp_update_is_public', array($this, 'update_is_public'));
		add_action( 'wp_ajax_resocosp_update_website', array( $this, 'update_website'));
		add_action( 'wp_ajax_nopriv_resocosp_update_website', array( $this, 'update_website'));
		add_action( 'wp_ajax_resocosp_add_social_account', array( $this, 'add_social_account'));
		add_action( 'wp_ajax_nopriv_resocosp_add_social_account', array( $this, 'add_social_account'));
		add_action( 'wp_ajax_resocosp_update_social_account', array( $this, 'update_social_account'));
		add_action( 'wp_ajax_nopriv_resocosp_update_social_account', array( $this, 'update_social_account'));
		add_action( 'wp_ajax_resocosp_delete_social_account', array( $this, 'delete_social_account'));
		add_action( 'wp_ajax_nopriv_resocosp_delete_social_account', array( $this, 'delete_social_account'));
		add_action('wp_ajax_resocosp_update_address', array($this, 'update_address'));
		add_action('wp_ajax_nopriv_resocosp_update_address', array($this, 'update_address'));
		add_action('wp_ajax_resocosp_add_new_post', array($this, 'add_new_post'));
		add_action('wp_ajax_nopriv_resocosp_add_new_post', array($this, 'add_new_post'));
		add_action('wp_ajax_resocosp_get_user_posts', array($this, 'get_user_posts'));
		add_action('wp_ajax_nopriv_resocosp_get_user_posts', array($this, 'get_user_posts'));
		add_action('wp_ajax_resocosp_delete_user_post', array($this, 'delete_user_post'));
		add_action('wp_ajax_nopriv_resocosp_delete_user_post', array($this, 'delete_user_post'));
		add_action('wp_ajax_resocosp_modify_user_post', array($this, 'modify_user_post'));
		add_action('wp_ajax_nopriv_resocosp_modify_user_post', array($this, 'modify_user_post'));
	}

	public static function activation(){
		UserProfilePage::set_grandparent_user_post();
	}

	public static function deactivation(){}

	public function user_profile_page_add_styles()
	{
		wp_enqueue_style( 'user_profile_page_style', plugin_dir_url(__FILE__). 'static/css/main.ae24d43a.css', array('bootstrap') );
	}

	public function user_profile_page_add_scripts()
	{
		wp_enqueue_script( 'user_profile_page_script', plugin_dir_url(__FILE__). 'static/js/main.cf9ef75b.js', array(), '0.0.1', true );
	}

	public static function resocosp_user_profile_page(){
		echo '<div id="root"></div>';
	}

	private static function set_grandparent_user_post(){
		$post_title = 'user0_post0';

		$post = get_page_by_title( $post_title, OBJECT, 'post' );

		if($post == NULL){
			$post_name = crypt($post_name, 'up');
			$post_content = 'Questo è il post padre di tutti i post padri dei singoli post inseriti dagli utenti';
		
			$user_post_parent = array(
				'post_title' 	=> $post_title,
				'post_name'		=> $post_name,
				'post_content'	=> $post_content,
				'post_status'	=> 'publish'
			);

			wp_insert_post( $user_post_parent, false );	
		}
	}

	private static function get_grandparent_user_post(){
		$grandparent_post = get_page_by_title( 'user0_post0', OBJECT, 'post' );
		return $grandparent_post->ID;
	}
	public function get_user_page_params(){

		$user_page = get_page_by_path($_GET["user_page"]);

		if($user_page) $id_pagina = $user_page->ID;
		else $id_pagina = $_GET["user_page"];

	    $response['id_pagina'] = $id_pagina; 
	    $pagina = get_post($id_pagina);
	    $response["pagina"] = $pagina;
	    $args = array(
	        'meta_key'  => 'id_pagina',
	        'meta_value'=> $id_pagina 
	    );
	    $user = get_users($args);
	    $response['utente'] = $user[0];
	    $response['utente_metadati'] = get_user_meta( $user[0]->ID );
	    $image_ID = get_post_thumbnail_id( $id_pagina );
	    $immagine = wp_get_attachment_image_src( $image_ID, 'thumbnail', false );
	    $response['immagine_url'] = $immagine[0];
	    $response['social_account'] = get_user_meta( $user[0]->ID, 'social_account' );
	    $id_comune = intval($response['utente_metadati']['comune'][0]);
	    $localita = LocalitaItaliane::get_row_by_id($id_comune);
	    $response["localita"] = $localita;
	    
	    echo json_encode($response);
	    wp_die();

	}

	public function get_user_data_by_id(){
	    $utente = get_userdata($_GET["idUtente"]);
	    $response["utente"] = $utente;
	    $utente_metadati = get_user_meta( $utente->ID );
	    $response["utente_metadati"] = $utente_metadati;
	    echo json_encode($response, JSON_NUMERIC_CHECK);
	    wp_die();
	}

	public function user_page_update_password(){
	    $user = get_userdata( $_GET['id_utente'] );
	    if(wp_check_password( $_GET['old_password'],  $user->data->user_pass, $user->ID )){
	        wp_set_password( $_GET['new_password'], $user->ID );
	        $response["passwordReset"] = true;
	    }
	    else $response['passwordReset'] = false;
	    echo json_encode($response, JSON_NUMERIC_CHECK);
	    wp_die();
	}

	public function update_is_public(){

	    $response['response'] = update_user_meta( $_GET['idUtente'], 'is_public', $_GET['statoPubblico']);
	    echo json_encode($response, JSON_NUMERIC_CHECK);
	    wp_die();
	}

	public function update_website(){
		$user_id = wp_update_user( array( 
	        'ID' => $_POST["user_ID"], 
	        'user_url' => $_POST["website"] ) 
	    );

	    if ( is_wp_error( $user_id ) ) {
	        $response["response"] = "error";
	    }
	    else {
	        $response["response"] = $user_id;
	    }
	    echo json_encode($response);
	    wp_die();
	}

	public function add_social_account(){
	    $id_utente = $_POST["user_ID"];
	    $social_code = $_POST["social_code"];
	    $social_username = $_POST["social_username"];
	    $social_account = get_user_meta( $id_utente, 'social_account', true);

	    if(empty($social_account)){
	        // crea il primo record 'social_account'
	        $meta_value = array( $social_code => $social_username);
	        $newSocialMeta = add_user_meta( $id_utente, 'social_account', $meta_value);
	        $response["response"] = $newSocialMeta;
	    }
	    else{
	        //verifica se esiste quindi aggiungi nuovo 
	        $is_social_present = false;
	        foreach($social_account as $key => $value) {
	            $social_list[$key] = $value;
	            if(array_key_exists($social_code, $social_account[$key])) $is_social_present = true;
	        }
	        $response["response"] = $social_list;
	        if(!$is_social_present){
	            $social_list[$social_code] = $social_username;
	            $new_social_list = update_user_meta( $id_utente, 'social_account', $social_list );
	            $response["response"] = $social_list;
	        }
	        else $response["response"] = false;
	    }

	    echo json_encode($response);
	    wp_die();
	}

	public function update_social_account(){
	    $id_utente = $_POST["user_ID"];
	    $social_code = $_POST["social_code"];
	    $social_username = $_POST["social_username"];
	    $social_accounts = get_user_meta( $id_utente, 'social_account', true);

	    foreach($social_accounts as $key => $value){
	        if($key == $social_code){
	            $social_accounts[$key] = $social_username;
	            break;
	        }
	    }
	    $new_social_list = update_user_meta( $id_utente, 'social_account', $social_accounts );
	    $response["response"] = $new_social_list;
	    echo json_encode($response);
	    wp_die();
	}

	public function delete_social_account(){
	    $id_utente = $_POST["user_ID"];
	    $social_code = $_POST["social_code"];
	    $social_accounts = get_user_meta( $id_utente, 'social_account', true);

	    unset($social_accounts[$social_code]);
	    $new_social_list = update_user_meta( $id_utente, 'social_account', $social_accounts );
	    $response["response"] = $new_social_list;
	    echo json_encode($response);
	    wp_die();
	}

	public function update_address(){

		update_user_meta( $_POST["idUtente"], 'indirizzo', $_POST["indirizzo"]);
		update_user_meta( $_POST["idUtente"], 'cap', $_POST["cap"]);
		update_user_meta( $_POST["idUtente"], 'provincia', $_POST["provincia"]);
		update_user_meta( $_POST["idUtente"], 'comune', $_POST["comune"]);

		if($_POST["mode"] == 'update'){	
			$response["response"] = LocalitaItaliane::get_row_by_id($_POST["comune"]);			
		}
		else{
			$response["response"] = 0;
		}

		echo json_encode($response);
		die();
	}

	public function add_new_post(){
		$idUtente = $_POST["idUtente"];
		$post_content = $_POST["post_content"];
		$is_first_post = false;
		// controlla se è il primo post
		$parentpost = get_page_by_title( 'user'.$idUtente.'_post0', OBJECT, 'post' );
		if($parentpost == NULL){
			// aggiungi il parent post
			$parent_post_title = 'user'.$idUtente.'_post0';

			$parent_post_name = crypt($parent_post_title, 'up');
			$parent_post_content = "Questo è il post padre di tutti i post inseriti dall' utente";
			$grandparent_post_ID = UserProfilePage::get_grandparent_user_post();
			$user_post_parent = array(
				'post_title' 	=> $parent_post_title,
				'post_name'		=> $parent_post_name,
				'post_content'	=> $parent_post_content,
				'post_status'	=> 'publish',
				'post_parent'	=> $grandparent_post_ID,
				'post_author'	=> $idUtente
			);

			$parent_post_ID = wp_insert_post( $user_post_parent, false );	
			$is_first_post = true;
		}

		if($is_first_post){
			// inserisci il primo post
			$post_title = 'user'.$idUtente.'_post1';
			$post_name = crypt($post_title, 'up');
			$user_post = array(
				'post_title' 	=> $post_title,
				'post_name'		=> $post_name,
				'post_content'	=> $post_content,
				'post_status'	=> 'publish',
				'post_parent'	=> $parent_post_ID,
				'post_author'	=> $idUtente
			);

			$post_ID = wp_insert_post( $user_post, false );
			add_post_meta( $post_ID, 'preferenze', 0, true );
			add_post_meta( $post_ID, 'condivisioni', 0, true );
			if(isset($_FILES['immagine-post'])){
				$this->post_image_upload($_FILES['immagine-post'], $post_ID, true );
			}
			$response["response"] = $post_ID;
		}
		else{
			//prendi l'ultimo post inserito
			$args = array(
				'post_parent'	=> $parentpost->ID,
				'post_author'	=> $idUtente,
				'orderby'		=> 'post_date',
				'numberposts'	=> 1
			);

			$latest_post = get_posts($args);
			$post_number = ((int)substr($latest_post[0]->post_title, strlen('user'.$idUtente.'_post')))+1;
			$new_post_title = 'user'.$idUtente.'_post'.$post_number;
			$new_post_name = crypt($new_post_title, 'up');
			$user_post = array(
				'post_title' 	=> $new_post_title,
				'post_name'		=> $new_post_name,
				'post_content'	=> $post_content,
				'post_status'	=> 'publish',
				'post_parent'	=> $parentpost->ID,
				'post_author'	=> $idUtente
			);

			$post_ID = wp_insert_post( $user_post, false );
			add_post_meta( $post_ID, 'preferenze', 0, true );
			add_post_meta( $post_ID, 'condivisioni', 0, true );
			if(isset($_FILES['immagine-post'])){
				$this->post_image_upload($_FILES['immagine-post'], $post_ID, true );
			}
			$post_data = get_post($post_ID );
			$post_data->preferenze = "0";
			$post_data->condivisioni = "0";
			$response["response"] = $post_data;

		}

		echo json_encode($response);
		die();
	}

	public function get_user_posts(){

		$post_parent = get_page_by_title( 'user'.$_GET['user_id'].'_post0', OBJECT, 'post' );
		$args = array(
			'post_author'	=> $_GET['user_id'],
			'post_parent'	=> $post_parent->ID,
			'orderby'		=> 'post_date',
			'order'			=> 'DESC',
			'post_type'		=> 'post',
			'post_status'	=> 'publish'
		);
		$posts = get_posts( $args );
		$response["posts"] = $posts;
		// estrai meta dati dei post
		$posts_meta = array();
		foreach ($posts as $key => $value) {
			$post_meta = array(
				'postID' 		=> $posts[$key]->ID,
				'preferenze'	=> get_post_meta($posts[$key]->ID, 'preferenze', true),
				'condivisioni'	=> get_post_meta($posts[$key]->ID, 'condivisioni', true)
			);
			array_push($posts_meta, $post_meta);

		}
		$response["posts_meta"] = $posts_meta;

		// estrai immagini dei post
		$posts_immagini = array();
		foreach ($posts as $key => $value) {
			$image_ID = get_post_thumbnail_id( $posts[$key]->ID );
	    	$immagine = wp_get_attachment_image_src( $image_ID);
	    	array_push($posts_immagini, $immagine[0]);
		}
		$response["posts_immagini"] = $posts_immagini;

		echo json_encode($response);
		die();
	}

	public function modify_user_post(){
		$args = array(
			'ID'	=> $_POST["postID"],
			'post_content'	=> $_POST["post_content"]
		);

		$post = wp_update_post( $args, false );

		$response["response"] = $post;
		echo json_encode($response);
		die();
	}

	public function delete_user_post(){
		wp_delete_post( $_GET["post_id"], true );
		delete_post_meta( $_GET["post_id"], 'preferenze');
		delete_post_meta( $_GET["post_id"], 'condivisioni');

		$response["response"] = $_GET["post_id"];
		echo json_encode($response);
		die();

	}

	public function post_image_upload($file, $post_id = 0 , $set_as_featured = false){

	    $upload = wp_upload_bits( $file['name'], null, file_get_contents( $file['tmp_name'] ) );

	    $wp_filetype = wp_check_filetype( basename( $upload['file'] ), null );

	    $wp_upload_dir = wp_upload_dir();

	    $attachment = array(
	        'guid' => $wp_upload_dir['baseurl'] . _wp_relative_upload_path( $upload['file'] ),
	        'post_mime_type' => $wp_filetype['type'],
	        'post_title' => preg_replace('/\.[^.]+$/', '', basename( $upload['file'] )),
	        'post_content' => '',
	        'post_status' => 'inherit'
	    );
	    
	    $attach_id = wp_insert_attachment( $attachment, $upload['file'], $post_id );

	    require_once(ABSPATH . 'wp-admin/includes/image.php');

	    $attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
	    wp_update_attachment_metadata( $attach_id, $attach_data );

	    if( $set_as_featured == true ) {
	        update_post_meta( $post_id, '_thumbnail_id', $attach_id );
	    }
	}

}

?>