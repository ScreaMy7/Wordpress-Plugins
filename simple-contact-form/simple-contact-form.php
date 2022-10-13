<?php
/**
 * Plugin Name
 *
 * @wordpress-plugin
 * Plugin Name:       Simple-Contact-form
 * Plugin URI:        https://example.com/plugin-name
 * Description:       A plugin for contact us form
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Scream
 * Author URI:        https://example.com
 * Text Domain:       Simple-Contact-form
 */

 if ( !defined('ABSPATH') ){
    echo 'Nice try';
    exit;
 }

class SimpleContactForm {

   
    public function __construct() {
         //create custom post type
        add_action('init', array($this , 'create_custom_post_type'));

        // Add assets 
        add_action('wp_enqueue_scripts', array($this, 'load_assets'));

        //add shortcode
        add_shortcode('contact-form', array($this ,'load_shortcode'));
        //add JS 
        add_action('wp_footer' , array($this , 'load_scripts'));

        add_action('rest_api_init', array($this , 'register_rest_api'));

    }

    public function create_custom_post_type(){
        $args  = array(
            'public' => true,
            'has_archive' => true ,
            'supports' => array('title'),
            'exclude_from_search' =>  true,
            'publicly_queryable' => false,
            'labels' => array(
                'name' => 'Contact Form',
                'singular_name' => 'Contact Form Entry'
            ),
            'menu_icon' =>'dashicons-forms',
        );

        register_post_type('simple_contact_form', $args);
    }

    public function load_assets(){
        wp_enqueue_style ( 'simple-contact-form' , 
                plugin_dir_url(__FILE__).'css/simple-contact-form.css',
                array(),
                null,
                'all'            
            );

    }

    public function load_shortcode()
    { ?>
        <div class="simple-contact-form" >
            <h1> Send us an email </h1>
            <p> Please fill the below form </p>
            <form id="simple-contact-form_form">
                <div class="mb-2 " >
                    <input  name="text" class="feedback-input" type="email" placeholder="Email"/>
                </div>
                <div class="mb-2" >
                    <input  name="text" class="feedback-input" type="text" placeholder="Name"/>
                </div>
                <div class="mb-2" >
                    <input  name="text"class="feedback-input" type="number" placeholder="Phone"/>
                </div>
                <div class="mb-2" >
                    <textarea name="text" class="feedback-input" placeholder="Your message"></textarea>
                </div>
                <div class="mb-2" >
                    <input type="submit" value="SUBMIT"/>
                </div>
            </form>
        </div>
    <?php }

    public function load_scripts()
    {?>
        
        <script>

        (function($){
            var nonce = '<?php echo wp_create_nonce('wp_rest'); ?>';

            $('#simple-contact-form_form').submit( function(event){

                event.preventDefault();
                var form = $(this).serialize();

                $.ajax({
                    method:'post',
                    url: '<?php echo get_rest_url(null,'simple-contact-form/v1/send-email');?>',
                    header: {'X-WP-Nonce': nonce },
                    data: form
                })
            });
        })(jQuery)

        </script>


    <?php }

    public function register_rest_api(){
        register_rest_route('simple-contact-form/v1', 'send-email', array('method'=>'POST','callback'=>array($this, 'handle-contact-form')));

    }

    public function handle_contact_form($data){
        $headers= $data->get_headers();
        $params = $data->get_params();
        $nonce = $headers['x_wp_nonce'][0];

        if (!wp_verify_nonce($nonce,'wp_rest'))
        {
            return new WP_REST_Response('Message not sent', 422);
        }

    $post_id = wp_insert_post([
        'post_type' => 'simple_contact_form',
        'post_title'=> 'Contact enquiry',
        'post_status' => 'publish'
    ]) ;

    if ($post_id)
    {
        return new WP_REST_Response('Thank you for email',200);
    }
    }
}

new SimpleContactForm;



 
