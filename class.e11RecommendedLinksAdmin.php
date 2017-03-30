<?php

// e11 Recommended Links
// Copyright (C) 2017 Eric Adolfson
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License along
// with this program; if not, write to the Free Software Foundation, Inc.,
// 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

require_once 'class-recommended-links-list-table.php';

class e11RecommendedLinksAdmin {
  private static $initialized = false;
  private static $linksTableName;

  /**
   * Plugin initialization.
   * @static
   */
  public static function init() {

    global $wpdb;

    // Ensure function is called only once.

    if (self::$initialized) {
      return;
    }

    self::$initialized = true;

    // Load stylesheet for plugin.

    wp_register_style('e11-recommended-links-admin.css',
                        plugin_dir_url(__FILE__) . 'css/e11-recommended-links-admin.css',
                        array(),
                        E11_RECOMMENDED_LINKS_VERSION);

    wp_enqueue_style('e11-recommended-links-admin.css');

    // Set table name for recommended links.

    self::$linksTableName = $wpdb->prefix . 'e11_recommended_links';

    // Trigger update procedure on version change.

    if (get_option('e11_recommended_links_version')
                                      != E11_RECOMMENDED_LINKS_VERSION) {
      self::perform_update();
    }
  }

  /**
   * Create/update database table(s) if plugin was just installed or upgraded
   * to a new version.
   *
   * @static
   */
  private static function perform_update() {

    // Create recommended links table.

    // [TODO] Rename "name" to title, here and throughout the plugin.

    $sql = 'CREATE TABLE ' . self::$linksTableName . ' (
		    id integer NOT NULL AUTO_INCREMENT,
		    created datetime DEFAULT "0000-00-00 00:00:00" NOT NULL,
		    display_mode tinyint NOT NULL DEFAULT 1,
		    name varchar(512) NOT NULL,
		    url varchar(1024) NOT NULL,
		    description text NOT NULL DEFAULT "",
		    
		    PRIMARY KEY (id)
      );';

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Update plugin version.

    update_option('e11_recommended_links_version',
      E11_RECOMMENDED_LINKS_VERSION);
  }










  /**
   * Callback to register plugin settings and build its settings page.
   * @static
   */
  public static function settings_init() {

    // Register settings for plugin.

    register_setting('e11_recommended_links', 'e11_recommended_links_options');

    // Build settings page for plugin.

    // Section: Recommended Links

    add_settings_section(
      'e11_recommended_links_section_links',
      __('Recommended Links', 'e11_recommended_links'),
      array('e11RecommendedLinksAdmin', 'display_recommended_links_control'),
      'e11_recommended_links'
    );

//    add_settings_field(
//      'e11_recaptcha_field_site_key',
//      __('Site key', 'e11Recaptcha'),
//      array('e11RecaptchaAdmin', 'field_site_key_cb'),
//      'e11_recaptcha',
//      'e11_recaptcha_section_api',
//      [
//        'label_for' => 'e11_recaptcha_field_site_key',
//        'class' => 'e11-recaptcha-row'
//      ]
//    );
//
//    add_settings_field(
//      'e11_recaptcha_field_secret_key',
//      __('Secret key', 'e11Recaptcha'),
//      array('e11RecaptchaAdmin', 'field_secret_key_cb'),
//      'e11_recaptcha',
//      'e11_recaptcha_section_api',
//      [
//        'label_for' => 'e11_recaptcha_field_secret_key',
//        'class' => 'e11-recaptcha-row'
//      ]
//    );
//
//    // Section: Behavior
//
//    add_settings_section(
//      'e11_recaptcha_section_behavior',
//      __('Behavior', 'e11Recaptcha'),
//      array('e11RecaptchaAdmin', 'section_behavior_cb'),
//      'e11_recaptcha'
//    );
//
//    add_settings_field(
//      'e11_recaptcha_field_behavior_comments',
//      __('Comments', 'e11Recaptcha'),
//      array('e11RecaptchaAdmin', 'field_behavior_comments_cb'),
//      'e11_recaptcha',
//      'e11_recaptcha_section_behavior',
//      [
//        'label_for' => 'e11_recaptcha_field_behavior_comments',
//        'class' => 'e11-recaptcha-row'
//      ]
//    );
//
//    add_settings_field(
//      'e11_recaptcha_field_behavior_new_users',
//      __('New users', 'e11Recaptcha'),
//      array('e11RecaptchaAdmin', 'field_behavior_new_users_cb'),
//      'e11_recaptcha',
//      'e11_recaptcha_section_behavior',
//      [
//        'label_for' => 'e11_recaptcha_field_behavior_new_users',
//        'class' => 'e11-recaptcha-row'
//      ]
//    );
  }

  /**
   * Callback to build and display HTML for "Links" section.
   *
   * @static
   * @param array $args Associative array of field arguments
   */
  public static function section_links_cb() {
//    echo __(
//      '<p>Text.</p>
//      ', 'e11_recommended_links');
  }

  /**
   * Callback to build and display HTML for "Behavior" section.
   *
   * @param array $args Associative array of field arguments
   */
  public static function section_behavior_cb() {
    echo __(
      '<p>The following settings determine when users will be required to
          solve reCAPTCHAs.</p>
      ', 'e11Recaptcha');
  }

  /**
   * Callback to build and display HTML for API site key text input.
   *
   * @param array $args Associative array of field arguments
   */
  public static function field_site_key_cb($args) {
    $options = get_option('e11_recaptcha_options', array());

    $siteKey = '';

    if (isset($options[$args['label_for']])) {
      $siteKey = $options[$args['label_for']];
    }

    echo '
      <input id="'
      . esc_attr($args['label_for'])
      . '" type="text" name="e11_recaptcha_options['
      . esc_attr($args['label_for'])
      . ']" value="'
      . esc_attr($siteKey)
      . '" />
      <p class="description">'
      . esc_html_x('Site key as provided by Google for your reCAPTCHA account', 'e11Recaptcha')
      . '</p>
    ';
  }

  /**
   * Callback to build and display HTML for API secret key text input.
   *
   * @param array $args Associative array of field arguments
   */
  public static function field_secret_key_cb($args) {
    $options = get_option('e11_recaptcha_options', array());

    $secretKey = '';

    if (isset($options[$args['label_for']])) {
      $secretKey = $options[$args['label_for']];
    }

    echo '
      <input id="'
      . esc_attr($args['label_for'])
      . '" type="text" name="e11_recaptcha_options['
      . esc_attr($args['label_for'])
      . ']" value="'
      . esc_attr($secretKey)
      . '" />
      <p class="description">'
      . esc_html_x('Secret key as provided by Google for your reCAPTCHA account', 'e11Recaptcha')
      . '</p>
    ';
  }

  /**
   * Callback to build and display HTML for "behavior with comments" select
   * box.
   *
   * @param array $args Associative array of field arguments
   */
  public static function field_behavior_comments_cb($args) {
    $options = get_option('e11_recaptcha_options', array());

    // Comment behavior may be one of "all_comments", "not_logged_in",
    // or "disabled".  Default to "not_logged_in".

    $behavior = 'not_logged_in';

    if (isset($options[$args['label_for']])) {
      $behavior = $options[$args['label_for']];

      switch($behavior) {
        case 'all_comments':
        case 'not_logged_in':
        case 'disabled':
          break;

        default:
          $behavior = 'not_logged_in';
          break;
      }
    }

    echo '
      <select id="'
      . esc_attr($args['label_for'])
      . '" name="e11_recaptcha_options['
      . esc_attr($args['label_for'])
      . ']">
        <option value="all_comments" '
      . selected($behavior, 'all_comments', false)
      . '>'
      . esc_html_x('Enabled for all comments', 'e11Recaptcha')
      . '</option>
        <option value="not_logged_in" '
      . selected($behavior, 'not_logged_in', false)
      . '>'
      . esc_html_x('Enabled for comments by users not logged in', 'e11Recaptcha')
      . '</option>
        <option value="disabled" '
      . selected($behavior, 'disabled', false)
      . '>'
      . esc_html_x('Disabled', 'e11Recaptcha')
      . '</option>
      </select>
      <p class="description">'
      . esc_html_x('Require users to solve a reCAPTCHA to post a comment', 'e11Recaptcha')
      . '</p>
    ';
  }

  /**
   * Callback to build and display HTML for "behavior with new users" select
   * box.
   *
   * @param array $args Associative array of field arguments
   */
  public static function field_behavior_new_users_cb($args) {
    $options = get_option('e11_recaptcha_options', array());

    // New user behavior may be one of "enabled" or "disabled".
    // Defaults to "enabled".

    $behavior = 'enabled';

    if (isset($options[$args['label_for']])) {
      $behavior = $options[$args['label_for']];

      switch($behavior) {
        case 'enabled':
        case 'disabled':
          break;

        default:
          $behavior = 'enabled';
          break;
      }
    }

    echo '
      <select id="'
      . esc_attr($args['label_for'])
      . '" name="e11_recaptcha_options['
      . esc_attr($args['label_for'])
      . ']">
        <option value="enabled" '
      . selected($behavior, 'enabled', false)
      . '>'
      . esc_html_x('Enabled for user registrations', 'e11Recaptcha')
      . '</option>
        <option value="disabled" '
      . selected($behavior, 'disabled', false)
      . '>'
      . esc_html_x('Disabled', 'e11Recaptcha')
      . '</option>
      </select>
      <p class="description">'
      . esc_html_x('Require new users to solve a reCAPTCHA to create an account', 'e11Recaptcha')
      . '</p>
    ';
  }


  /**
   * Callback to add plugin options page under "Settings" in admin menu.
   *
   * [TODO] Use 'manage_e11_recommended_links' as the test for capability?
   */
  public static function admin_menu_options_page() {

    // Add menus to admin sidebar.  The first submenu is specified with the
    // same menu slug as parent menu, which allows it to be given a different
    // name (WordPress otherwise creates the first submenu with the same name
    // as the parent.)

    add_menu_page(
      'e11 Recommended Links',
      'e11 Recommended Links',
      'manage_options',
      'e11_recommended_links',
      array('e11RecommendedLinksAdmin', 'all_links_page_html')
    );

    add_submenu_page(
      'e11_recommended_links',
      'Recommended Links',
      'All Links',
      'manage_options',
      'e11_recommended_links',
      array('e11RecommendedLinksAdmin', 'all_links_page_html')
    );

    add_submenu_page(
      'e11_recommended_links',
      'Add New Link',
      'Add New',
      'manage_options',
      'e11_recommended_links_add',
      array('e11RecommendedLinksAdmin', 'modify_link_page_html')
    );

    // Add "Edit Link" page under a menu that doesn't exist.  This way
    // it won't be displayed in the admin sidebar while allowing link
    // editing to be done from this class.
    //
    // Also calls the "modify_link_page_html" function like the
    // "Add Link" page does, for code reuse.

    add_submenu_page(
      '__deliberately_nonexistent_menu_slug',
      'Edit Link',
      'Edit Link',
      'manage_options',
      'e11_recommended_links_edit',
      array('e11RecommendedLinksAdmin', 'modify_link_page_html')
    );
  }

  /**
   * Callback to build and display "all links" page for plugin.
   */
  public static function all_links_page_html() {

    // Block access unless user has adequate permissions.
    // [TODO] Make this capability 'manage_e11_recommended_links'?

    if (!current_user_can('manage_options')) {
      return;
    }

    // Display status messages to user.
    // [TODO] Move this to a "config" page.
    // settings_errors('e11_recommended_links_messages');

    // Output page HTML.

    echo '
      <div class="wrap">
        <h1>' . esc_html(get_admin_page_title());

    // [TODO] Set link to page to add recommended link

    if (current_user_can('manage_e11_recommended_links')) {
      echo '
        <a href="' . admin_url('admin.php?page=e11_recommended_links_add') . '" class="page-title-action">'
          . esc_html_x('Add New', 'e11RecommendedLinks')
          . '</a>
      ';
    }

    echo '</h1>';

    $linksTable = new RecommendedLinksListTable();

    $linksTable->prepare_items();

    $linksTable->display();


    // [TODO] Move these to a "config" page.

    // Write WordPress hidden fields for form input.
    // settings_fields('e11_recommended_links');

    // Write settings HTML for plugin.
    // do_settings_sections('e11_recommended_links');

//    // Write submit button.
//    submit_button('Save changes');

    echo '
        </form>
      </div>
    ';
  }

  /**
   * Callback to build and display "add link" and "edit link" pages for
   * plugin.
   */
  public static function modify_link_page_html() {

    // Block access unless user has adequate permissions.
    // [TODO] Make this capability 'manage_e11_recommended_links'?

    if (!current_user_can('manage_options')) {
      return;
    }

    // If calling this section with "edit link" functionality and not
    // posting anything, load the link record from database using the
    // supplied ID.

    if ($_GET['page'] == 'e11_recommended_links_edit') {
      if (!isset($_GET['id'])) {
        // [TODO] Display error and exit here.
      }

      // [TODO] Verify record with 'id' exists in database, exiting
      //        here if not.

      // Load record into form variables if not posting the form.

      if (!isset($_POST['modify-link'])) {
        // [TODO] Load record.
      }
    }

    $errors = array();

    // Read and validate post variables if submitted or loading from
    // database.  Otherwise, initialize them.

    if (isset($_POST['modify-link']) ||
                $_GET['page'] == 'e11_recommended_links_edit') {

      // Block the post if the nonce isn't verified.

      if (isset($_POST['modify-link']) &&
            !wp_verify_nonce($_POST['_wpnonce_e11-modify-recommended-link'],
                                      'e11-modify-recommended-link')) {
        $errors[] = 'Invalid nonce';
      }

      $link_title = trim(wp_unslash($_POST['link-title']));
      $link_url = trim(wp_unslash($_POST['link-url']));
      $link_description = trim(wp_unslash($_POST['link-description']));
      $link_display_mode = wp_unslash($_POST['link-display-mode']);
      $link_created = trim(wp_unslash($_POST['link-created']));

      // Validate "Title" -- required field.

      if (empty($link_title)) {
        $errors[] = '"Title" cannot be empty';
      }

      // Validate "URL" -- required field and must be in valid form.

      if (empty($link_url)) {
        $errors[] = '"URL" cannot be empty';
      }

      $link_url = filter_var($link_url, FILTER_SANITIZE_URL);

      if (filter_var($link_url, FILTER_VALIDATE_URL) === false) {
        $errors[] = '"URL" is invalid';
      }

      // Validate "Display Mode" -- must contain 1, 2, or 3.

      if (!preg_match('/^[123]$/', $link_display_mode)) {
        $errors[] = '"Display Mode" is invalid';

        $link_display_mode = 1;
      }

      // Validate "Created" -- if specified, must be in valid form.  If
      // blank, fill with current date/time.

      if (!empty($link_created)) {
        $link_created_formatted = DateTime::createFromFormat('Y-m-d H:i', $link_created);

        if ($link_created_formatted === false ||
                $link_created_formatted->format('Y-m-d H:i')
                                                        !== $link_created) {

          $errors[] = '"Created" field is invalid';
        }
      } else {
        $link_created = date('Y-m-d H:i', current_time('timestamp'));
      }

      // Save link if posted and no errors found.  Otherwise, return to
      // form and display error messages to user.

      if (empty($errors)) {

        // Save link.  (If not loading from database for the "edit link"
        // page.)

        if (isset($_POST['modify-link'])) {
          // [TODO] Save link.
        }
      } else {
        // Return to form and display error messages.
?>
        <div class="error">
          <ul>
            <?php
              foreach ($errors as $error) {
                echo '<li>' . $error . '</li>' . "\n";
              }
            ?>
          </ul>
        </div>
<?php
      }
    } else {
      $link_title = '';
      $link_url = '';
      $link_description = '';
      $link_display_mode = 1;
      $link_created = date('Y-m-d H:i', current_time('timestamp'));
    }


    // Output page HTML.

?>
    <div class="wrap">
      <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

      <form id="e11-modify-recommended-link" class="validate" method="post"
            name="e11-modify-recommended-link" novalidate="novalidate">

        <?php
          wp_nonce_field('e11-modify-recommended-link',
                                '_wpnonce_e11-modify-recommended-link');

          if ($_GET['page'] == 'e11_recommended_links_edit') {
            echo '<input type="hidden" name="link-id" value="'
                                        . $_GET['id'] . '" />' . "\n";
          }
        ?>

        <table class="form-table">
          <tr class="form-field form-required">
            <th scope="row">
              <label for="link-title"><?php _e('Title'); ?>
                <span class="description"><?php _e('(required)'); ?></span>
              </label>
            </th>
            <td>
              <input name="link-title" type="text" id="link-title"
                     value="<?php echo esc_attr($link_title); ?>"
                     aria-required="true" autocapitalize="none"
                     autocorrect="off" maxlength="512" />
            </td>
          </tr>
          <tr class="form-field form-required">
            <th scope="row">
              <label for="link-url"><?php _e('URL'); ?>
                <span class="description"><?php _e('(required)'); ?></span>
              </label>
            </th>
            <td>
              <input name="link-url" type="text" id="link-url"
                     value="<?php echo esc_attr($link_url); ?>"
                     aria-required="true" autocapitalize="none"
                     autocorrect="off" maxlength="1024" />
            </td>
          </tr>
          <tr class="form-field">
            <th scope="row">
              <label for="link-description"><?php _e('Description'); ?>
              </label>
            </th>
            <td>
              <textarea name="link-description" type="text"
                        id="link-description"
              ><?php echo esc_attr($link_description); ?></textarea>
            </td>
          </tr>
          <tr class="form-field">
            <th scope="row">
              <label for="link-display-mode"><?php _e('Display Mode'); ?>
              </label>
            </th>
            <td>
              <select id="link-display-mode" name="link-display-mode">
                <option value="1"
                  <?php echo ($link_display_mode == 1 ? ' selected' : ''); ?>
                  >Post index</option>
                <option value="2"
                  <?php echo ($link_display_mode == 2 ? ' selected' : ''); ?>
                  >Sidebar widget</option>
                <option value="3"
                  <?php echo ($link_display_mode == 3 ? ' selected' : ''); ?>
                  >Index and widget</option>
              </select>
            </td>
          </tr>
          <tr class="form-field form-required">
            <th scope="row">
              <label for="link-created"><?php _e('Created'); ?><br />
                <span class="description"><?php _e('(YYYY-MM-DD hh:mm)'); ?></span>
              </label>
            </th>
            <td>
              <input name="link-created" type="text" id="link-created"
                     value="<?php echo esc_attr($link_created); ?>"
                     aria-required="true" autocapitalize="none"
                     autocorrect="off" maxlength="16" />
            </td>
          </tr>
        </table>
        <?php submit_button(__('Save Link'), 'primary', 'modify-link', true); ?>
      </form>
    </div>

<?php

  }

}

add_action('admin_init', array('e11RecommendedLinksAdmin', 'settings_init'));
add_action('admin_menu', array('e11RecommendedLinksAdmin', 'admin_menu_options_page'));
