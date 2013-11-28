<?php 
/*
Plugin Name: EWP
Plugin URI: http://ecomwebpro.com
Description: Quản lý đặt thuê sản phẩm
Version: 1.0.0
Author: EWP company
Author URI: http://ecomwebpro.com
License: GPL2
*/
require_once('ewp_admin.php');

/**
 * install plugin
 */

global $ewp_db_version;
$ewp_db_version = '1.0.1';

function ewp_install() {
    global $wpdb;
    global $ewp_db_version;

    $contact_table = $wpdb->prefix . "ewp_contact";
    
    $sql = "CREATE TABLE $contact_table (
    		id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        	name VARCHAR(100),
        	email VARCHAR(100),
        	phone VARCHAR(25),
			message TEXT,
			product_name varchar(100),
			booking_date DATE,
			status VARCHAR(50)
			
        );
		";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    add_option("ewp_db_version", $ewp_db_version);
    update_option("ewp_db_version", $ewp_db_version);
}
register_activation_hook( __FILE__, 'ewp_install' );

function ewp_update_db_check() {
    global $ewp_db_version;
    if (get_site_option( 'ewp_db_version' ) != $ewp_db_version) {
        ewp_install();
    }
}
add_action( 'plugins_loaded', 'ewp_update_db_check' );


add_action( 'wp_enqueue_scripts', 'prefix_add_my_stylesheet' );

/**
 * Enqueue plugin style-file
 */
function prefix_add_my_stylesheet() {
	//add script
	wp_enqueue_script('ewp', plugins_url('js/ewp.js', __FILE__), array('jquery'));
//	wp_enqueue_script('ewp', plugins_url('fancybox/jquery.fancybox-1.3.4.pack.js', __FILE__), array('jquery'));
//	wp_enqueue_script('ewp1', plugins_url('js/jquery.colorbox-min.js', __FILE__), array('jquery'));
	
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'prefix-style', plugins_url('css/style.css', __FILE__) );
    //wp_register_style( 'prefix-style', plugins_url('fancybox/jquery.fancybox-1.3.4.css', __FILE__) );
//    wp_register_style( 'prefix-style', plugins_url('css/colorbox.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}

function buy_product(){
	$string = '
		<input type="hidden" id="ajax-url" value="' . admin_url('admin-ajax.php') .'">
		<div class="product-form">
			<a href="#info-form" id="buy-product-btn">Đặt thuê</a>
			<div style="display:none" id="info-form-wrapper">
				<div id="info-form">
					<table>
						<tr>
							<td>Tên của bạn <span class="required">*</span></td>
							<td><input name="name" /></td>
						</tr>
						<tr>
							<td>Địa chỉ email </td>
							<td><input name="custom-email" /></td>
						</tr>
						<tr>
							<td>Số điện thoại <span class="required">*</span></td>
							<td><input name="phone" /></td>
						</tr>
						<tr>
							<td>Thông điệp </td>
							<td><textarea name="message"></textarea></td>
						</tr>
						<tr>
							<td></td>
							<td class="ticket-submit">
								<button>
                                    <span>Gửi</span>
                                    <img src="' . plugins_url('ewp/images/loading.gif') .'" style="display:none;"/>
                                </button>
							</td>
						</tr>
					</table>
				</div>				
			</div>
		</div>
	
	';
	return $string;
}

function show_home_banner($atts){
	extract(shortcode_atts(array(
		'src' => '',
		'alt' => '',
		'url' => '' 
	), $atts));
	if($src){
		$str = '<div class="home-banner">';
		if($url){
			$str .= 
				'<a href="' . $url . '">
					<img src="' . $src . '" alt="' . $alt . '" />
			</a>';
			} else {
				$str .= '<img src="' . $src . '" alt="' . $alt .'" />';
			}
		$str .= '</div>';		
	}
	return $str;
}
function show_home_ads($atts, $content = null){
	$str = '
		<div class="home-row">
			' . $content . '
		</div>
	';
	return $str;
}
function show_hotline($atts){
	extract(shortcode_atts(array(
		'hotline1' => '',
		'hotline2' => ''
	), $atts));
	$str = 
		'<div class="info-hotline home-block">
			<div id="info-reg">
				<div class="title">
					Đăng ký nhận bản tin khuyến mãi
				</div>
				<div class="form">
					<input name="receive-email" class="receive-email" />
					<button class="btn-blue">Đăng ký</button>
				</div>
			</div>
			<div class="hotline">
				<div class="col">
					<img src="' . get_bloginfo('url') . '/wp-content/uploads/2013/08/hotline1.png" />
				</div>
				<div class="col">
					<img alt="01288888618" src="' . get_bloginfo('url') . '/wp-content/uploads/2013/08/hotline_no.png" />
			';
	$str .= '
	
				</div>
			</div>
		</div>';
	return $str;
}
function booking_hint($atts, $content = null){
	$str = 
		'<div class="booking-hint home-cell">
			<h5>Hướng dẫn đặt vé</h5>
			<div class="text">' . $content . '</div>
		</div>';
	return $str;
}
function promo_news(){
	$str = 
		'<div class="promo-news home-cell">
			<h5>Tin khuyến mại</h5>';
	if(function_exists('get_vsrp')){
		$str .= get_vsrp();
	}
	$str .= 
		'</div>';
	return $str;	
}
function home_address($atts, $content = null){
	extract(shortcode_atts(array(
		'latitude' => '',
		'longitude' => ''
	), $atts));
	$str = 
		'<div class="home-cell home-address">
		';	
	if($content){
		$str .= '<div class="text">' . $content . '</div>';
	}
	if($latitude && $longitude){
		$str .= '<input type="hidden" id="lat" value="' . $latitude . '">';
		$str .= '<input type="hidden" id="lat" value="' . $longitude . '">';
		$str .= 
			'<div class="map">
				<iframe width="272" height="272" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=vi&amp;geocode=&amp;q=21.0301444,105.7845998&amp;aq=&amp;sll=15.984434,108.273697&amp;sspn=0.215192,0.407181&amp;ie=UTF8&amp;t=m&amp;ll=21.030113,105.784607&amp;spn=0.021791,0.02326&amp;z=14&amp;output=embed"></iframe>
			</div>';
	}
	$str .= '</div>';
	return $str;
}
function facebook_page($atts){
	extract(shortcode_atts(array(
		'url' => ''
	), $atts));
	$str .= '<div class="facebook-page home-cell">';
	if($url){
		$str .= '<iframe src="' . $url . '" scrolling="no" frameborder="0" style="border: none; overflow: hidden; width: 468px; height: 275px;" allowtransparency="true"></iframe>';	
	}
	$str .= '</div>';
	return $str;
}
function register_shortcode(){
//	add_shortcode('chon-ve', 'show_ticket_selector');
//	add_shortcode('home-banner', 'show_home_banner');
//	add_shortcode('hotline', 'show_hotline');
//	add_shortcode('booking-hint', 'booking_hint');
//	add_shortcode('promo-news', 'promo_news');
//	add_shortcode('home-address', 'home_address');
//	add_shortcode('facebook-page', 'facebook_page');
//	add_shortcode('home-ads', 'show_home_ads');
}

add_action('init', 'register_shortcode');



/**
 * save info to db and send mail
 * @author duythanhdao@live.com
 */
function save_custom_info (){
    $to = get_settings('admin_email');
    $subject = 'Đặt thuê máy của ' . $_POST['name'] . ($_POST['email'] ? '<' . $_POST['email'] . '>' : '') ;
    $message .= 'Tên khách hàng: ' . $_POST['name'] . "\n";
    $message .= 'Điện thoại: ' . $_POST['phone'] . "\n";
    $message .= 'Email: ' . $_POST['email'] . "\n";

    $message .= 'Tên sản phẩm: ' . $_POST['product_name'] . "\n";


    $message .= 'Nội dung: ' . $_POST['message'];
    //reg_email();
    
    try{
        $result = wp_mail($to,$subject,$message);
    }
    catch(phpmailerException $e){
        
        $exceptionmsg = $e->errorMessage();
        exit('Có lỗi xảy ra, quý khách vui lòng thử lại');
    }
    if(saveBooking()) {

        exit('Thông tin đã được gửi, cảm ơn qúy khách!');
    }

    exit('Có lỗi xảy ra, quý khách vui lòng thử lại');
}
add_action( 'wp_ajax_save_custom_info', 'save_custom_info');
add_action( 'wp_ajax_nopriv_save_custom_info', 'save_custom_info');

/**
 * save booking to database
 * @author duythanhdao@live.com
 */

function saveBooking(){
    global $wpdb;
    $table_name = $wpdb->prefix . "ewp_contact";
    $data = array();
    $fields = array(
        'name', 'email', 'phone', 'message', 'product_name'
    );
    foreach($fields as $field){
        if(isset($_POST[$field])) {
            $data[] = "'" . $_POST[$field] . "'";
        }
    }

    $data[] = "'" . date('Y-m-d', time()) . "'";
    $data[] = "'" . 'waitting' . "'";
    
    $query = "INSERT INTO $table_name (name, email, phone, message, product_name, booking_date, status) VALUES ";
    $query .= "(" . implode(',', $data) . ")";
 
    return $wpdb->query($query);
}

function reg_email(){
	if(isset($_POST['email'])){
		saveContact($_POST['email']);
	}	
}

add_action( 'wp_ajax_reg_email', 'reg_email');
add_action( 'wp_ajax_nopriv_reg_email', 'reg_email');

/**
 * save contact to database
 * @author duythanhdao@live.com
 */

function saveContact($email = null){
    global $wpdb;
    $table_name = $wpdb->prefix . "ewp_contact";
    
  		$query = "DELETE FROM $table_name WHERE email LIKE '$email'";
  		$wpdb->query($query);
	    $query = "INSERT INTO $table_name (email) VALUES ";
	    $query .= "('" . $email . "')";  
	if($email) {
		if($wpdb->query($query))
			exit("Thông tin đã được lưu lại, xin cảm ơn!");
		exit("Có lỗi xảy ra, vui lòng thử lại!");  	
    }
}

/*********Amin area*******/

?>

