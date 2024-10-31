<?php
/**
* Plugin Name: Push Нотификации
* Plugin URI: https://pushem.ru
* Description: Push Нотификации для вашего сайта на Wordpress. Требуется подключение сервиса pushem.ru, есть БЕСПЛАТНЫЙ тарифный план
* Version: 1.0
* Author: Pushem.ru
* Author URI: https://pushem.ru
* License: GPL
*/

/* Plugin Initiate section */
/* End of Plugin Initiate section */

/* Plugin setting section */

function pushem_notifications_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=pushem_notifications_options">'.__("Настройки","pushem_notifications").'</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'pushem_notifications_settings_link' );

function pushem_notifications_add_options_submenu_page() {
     add_submenu_page(
          'options-general.php',
          __( 'Push Нотификации настройки', 'pushem_notifications' ),
          __( 'Push Нотификации', 'pushem_notifications' ),
          'manage_options',
          'pushem_notifications_options',
          'pushem_notifications_options_page'
     );
     add_menu_page(
        __( 'Отправить Push-Нотификацию', 'pushem_notifications' ),
        __( 'Отправить Push-Нотификацию', 'pushem_notifications' ),
        'manage_options',
        'https://pushem.ru/user/notification',
        '',
        'dashicons-email',
        6
    );
}
add_action( 'admin_menu', 'pushem_notifications_add_options_submenu_page' );

function pushem_notifications_register_settings() {
     register_setting(
          'pushem_notifications_options',
          'pushem_notifications_pushem_id'
     );
    register_setting(
          'pushem_notifications_options',
          'pushem_notifications_pushem_key'
    );
    register_setting(
          'pushem_notifications_options',
          'pushem_notifications_pushem_segments'
    );

    //Getting options
    register_setting( 'pushem_notifications_options', 'pushem_notifications_pushem_token');
    register_setting( 'pushem_notifications_options', 'pushem_notifications_pushem_https');
}
add_action( 'admin_init', 'pushem_notifications_register_settings' );

function pushem_notifications_options_page() {
    if ( ! isset( $_REQUEST['settings-updated'] ) ) $_REQUEST['settings-updated'] = false;
    ?>
 
     <div class="wrap">
 
          <?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
               <div class="updated fade"><p><strong><?php _e( 'Свойства сохранены!', 'pushem_notifications' ); ?></strong></p></div>
          <?php endif; ?>
           
          <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
          <div id="poststuff">
               <div id="post-body">
                    <div id="post-body-content">

                         <form method="post" action="options.php">
                              <?php settings_fields( 'pushem_notifications_options' ); ?>
                              <?php $pushem_id = get_option( 'pushem_notifications_pushem_id' ); ?>
                              <?php $pushem_key = get_option( 'pushem_notifications_pushem_key' ); ?>
                              <?php $pushem_segments = get_option( 'pushem_notifications_pushem_segments' ); ?>

                              <table class="form-table">
                                  <tr valign="top">
                                    <p><h3><a href="https://pushem.ru/">Если вы еще не создали бесплатный аккаунт на pushem.ru - создайте! Он обязателен для работы плагина. Данные для полей ниже находятся на странице "Данные для CMS плагинов" в вашем аккаунте на pushem.ru</a></h3></p>
                                  </tr>
                                   <tr valign="top">
                                   <p><h3><?php _e('Pushem.ru аккаунт', 'pushem_notifications'); ?></h3></p>
                                        <td>
                                            <p>
                                            <label class="label" for="pushem_notifications_pushem_id[pushem_id]"><?php _e( 'Pushem.ru ID пользователя', 'pushem_notifications' ); ?></label>
                                            <input type="text" name="pushem_notifications_pushem_id[pushem_id]" value="<?php echo $pushem_id['pushem_id']; ?>">
                                            </p>
                                            
                                            <p>
                                            <label class="label" for="pushem_notifications_pushem_key[pushem_key]"><?php _e( 'Pushem.ru ключ API', 'pushem_notifications' ); ?></label>
                                            <input type="text" name="pushem_notifications_pushem_key[pushem_key]" value="<?php echo $pushem_key['pushem_key']; ?>">
                                            </p>

                                            <p>
                                            <label class="label" for="pushem_notifications_pushem_segments[pushem_segments]"><?php _e( 'Сегменты', 'pushem_notifications' ); ?></label>
                                            <input type="text" name="pushem_notifications_pushem_segments[pushem_segments]" value="<?php echo $pushem_key['pushem_segments']; ?>">
                                            <br />
                                            <p>Названия сегментов ТОЛЬКО НА ЛАТИНИЦЕ, ЧЕРЕЗ ЗАПЯТУЮ, БЕЗ ПРОБЕЛОВ</p>
                                            </p>

                                        </td>
                                    <td>
                                    <p><br /></p>

                                    </td>
                                    </tr>
                              </table>
                              <input type="submit" class="button-primary" value="<?php _e('Сохранить настройки') ?>" />
                              
                              <table class="form-table">
                                   <tr valign="top">
                                        <td>
                                            <!--<p><h3><?php _e('Данные полученные от pushem.ru', 'pushem_notifications'); ?></h3></p>-->
                                            <p>
                                            <?php
                                                if (!empty($pushem_id['pushem_id']) && !empty($pushem_key['pushem_key']) ) {
                                                    $url = 'https://pushem.ru/api/account/getdata/?id='.$pushem_id['pushem_id'].'&api='.$pushem_key['pushem_key'];
                                                    $result = file_get_contents($url, false);
                                                    
                                                    if (!empty($result)) {
                                                        $result = json_decode($result);
                                                        
                                                        if (!empty($result->error)) {
                                                          print '<span style="color:#FA0000;">'.$result->error.'</span>';
                                                        } else {
                                                          $token = $result->token;
                                                          $https = $result->https;
                                                          update_option( 'pushem_notifications_pushem_token', $token );
                                                          update_option( 'pushem_notifications_pushem_https', $https );
                                                        }

                                                        ?>
                                                        <?php                                                        
                                                    }
                                                }
                                            ?>
                                            </p>
                                        </td>
                                    </tr>

                              </table>

                    </div>
               </div>
          </div>
     </div>
<?php
}

/* End of Plugin setting section */

/* Plugin Initate pushem.org (head behaivor) */

add_action ('wp_head','pushem_notifications_set_header');

function pushem_notifications_set_header() {
    $type = get_option( 'pushem_notifications_pushem_https' );
    if ($type !== FALSE) {
        if ($type == 0) {
            $token = get_option( 'pushem_notifications_pushem_token' );
            if ($token !== FALSE) {
                $seg_list = get_option( 'pushem_notifications_pushem_segments' );
                if ($seg_list !== FALSE) {
                  $segments = '&segments='.$seg_list;
                } else {
                  $segments = '';
                }
                $user = wp_get_current_user();
                    if (isset($user->ID)) {
                          if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
                            foreach ( $user->roles as $role ) $roles[] = urlencode($role);
                            $s = implode(",",$roles);
                            $widget_file = 'https://pushem.ru/widget/loader.js?token='.$token.'&site_id='.$user->ID.$segments;
                            print '<script src="'.$widget_file.'"></script>';
                          } else {
                            $widget_file = 'https://pushem.org/widget/loader.js?token='.$token.'&site_id='.$user->ID.$segments;
                            print '<script src="'.$widget_file.'"></script>';
                          }
                    } else {
                        $widget_file = 'https://pushem.ru/widget/loader.js?token='.$token.$segments;
                        print '<script src="'.$widget_file.'"></script>';
                    }
            }
        }
        if ($type == 1) {
            $mainfest_file = plugins_url( 'js/sdk/manifest.json', __FILE__ );
            print '<link rel="manifest" href="'.$mainfest_file.'">';
            $token = get_option( 'pushem_notifications_pushem_token' );
            if ($token !== FALSE) {
                $seg_list = get_option( 'pushem_notifications_pushem_segments' );
                if ($seg_list !== FALSE) {
                  $segments = '&segments='.$seg_list;
                } else {
                  $segments = '';
                }
                $user = wp_get_current_user();
                    if (isset($user->ID)) {
                          if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
                            foreach ( $user->roles as $role ) $roles[] = urlencode($role);
                            $s = implode(",",$roles);
                            
                            $pushem_data_array = array( 'swpath' => plugins_url( 'js/sdk/sw.js', __FILE__ ) );
                            
                            $init_file = plugins_url( 'js/sdk/init.js', __FILE__ ).'?token='.$token.'&site_id='.$user->ID.$segments;
                            wp_enqueue_script( 'pushem_init', $init_file );
                            wp_localize_script( 'pushem_init', 'pushem_data', $pushem_data_array );
                            
                            //print '<script src="'.$init_file.'"></script>';
                          } else {
                            $pushem_data_array = array( 'swpath' => plugins_url( 'js/sdk/sw.js', __FILE__ ) );
                            
                            $init_file = plugins_url( 'js/sdk/init.js', __FILE__ ).'?token='.$token.'&site_id='.$user->ID.$segments;
                            wp_enqueue_script( 'pushem_init', $init_file );
                            wp_localize_script( 'pushem_init', 'pushem_data', $pushem_data_array );

                            //print '<script src="'.$init_file.'"></script>';
                          }
                    } else {
                        $init_file = plugins_url( 'js/sdk/init.js', __FILE__ ).'?token='.$token.$segments;
                        print '<script src="'.$init_file.'"></script>';
                    }
            }
        }
    }

}

/* End of Plugin */
