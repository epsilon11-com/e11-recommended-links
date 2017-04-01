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
  private static $actionErrors = array();

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

    // Add "Delete Link" page under a menu that doesn't exist.

    add_submenu_page(
      '__deliberately_nonexistent_menu_slug',
      'Delete Link',
      'Delete Link',
      'manage_options',
      'e11_recommended_links_delete',
      array('e11RecommendedLinksAdmin', 'delete_link_page_html')
    );
  }

  /**
   * Callback to build and display "all links" page for plugin.
   */
  public static function all_links_page_html() {

    // Intercept bulk actions and redirect them to a different function.

    if (isset($_REQUEST['action'])) {
      if ($_REQUEST['action'] == 'delete' || $_REQUEST['action'] == 'dodelete') {
        self::delete_link_page_html();
        return;
      }
    }

    // Block access unless user has adequate permissions.

    if (!current_user_can('manage_e11_recommended_links')) {
      wp_die(__('Your account is not able to modify recommended links.'));
    }

    // Display status messages to user.
    // [TODO] Move this to a "config" page.
    // settings_errors('e11_recommended_links_messages');

    // Output page HTML.

    echo '
      <div class="wrap">
        <h1>' . esc_html(get_admin_page_title());

    // Create "Add New" button if user has capability to manage links.

    if (current_user_can('manage_e11_recommended_links')) {
      echo '
        <a href="' . admin_url('admin.php?page=e11_recommended_links_add') . '" class="page-title-action">'
          . esc_html_x('Add New', 'e11RecommendedLinks')
          . '</a>
      ';
    }

    echo '</h1>';

    // If returning from a bulk action, create a status message for the result
    // using the GET parameters added to the redirect URL from the action.

    $messages = array();

    if (isset($_GET['update'])) {
      switch ($_GET['update']) {
        case 'del':
          // Display number of links deleted.

          $delete_count =
            isset($_GET['delete_count']) ? (int)$_GET['delete_count'] : 0;

          if (1 == $delete_count) {
            $message = __('Link deleted.');
          } else {
            $message = _n('%s link deleted.', '%s links deleted.',
                                                            $delete_count);
          }

          $messages[] = sprintf($message, number_format_i18n($delete_count));

          break;
      }
    }

    // Output status messages to HTML.

    if (!empty($messages)) {
      foreach ($messages as $message) {
        echo '<div id="message" class="updated notice is-dismissible"><p>'
                                                    . $message . '</p></div>';
      }
    }

    // Output list table to HTML.  The page is added as a hidden input to
    // cause the bulk action button to reload this page if clicked.

    echo '<form method="get">';
    echo '<input type="hidden" name="page" value="e11_recommended_links" />';

    $linksTable = new RecommendedLinksListTable();

    $linksTable->prepare_items();

    $linksTable->display();

    echo '</form>';

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
    global $wpdb;

    // Block access unless user has adequate permissions.

    if (!current_user_can('manage_e11_recommended_links')) {
      wp_die(__('Your account is not able to modify recommended links.'));
    }

    // Output page header HTML.
?>
      <div class="wrap">
          <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
<?php

    // Set defaults for form variables.

    $link_id = -1;
    $link_title = '';
    $link_url = '';
    $link_description = '';
    $link_display_mode = 1;
    $link_created = date('Y-m-d H:i', current_time('timestamp'));

    // If calling this section with "edit link" functionality and not
    // posting anything, load the link record from database using the
    // supplied ID.

    if ($_GET['page'] == 'e11_recommended_links_edit') {

      // Verify 'id' parameter is present.

      if (!isset($_GET['id'])) {
        wp_die(__('"id" parameter required in URL but not found.'));
      }

      // Read record from database.

      $query = $wpdb->prepare('
          SELECT id, created, display_mode, name, url, description 
          FROM ' . self::$linksTableName . ' 
          WHERE id = %d
      ', array($_GET['id']));

      $link = $wpdb->get_row($query);

      // Verify record exists for 'id'.

      if ($link === null) {
        wp_die(__('Link record not found.'));
      }

      // Load record into form variables if not posting the form.

      if (!isset($_POST['modify-link'])) {
        $link_id = $link->id;
        $link_title = $link->name;
        $link_url = $link->url;
        $link_description = $link->description;
        $link_display_mode = $link->display_mode;

        // Convert 'created' field to date/time string without seconds.

        $link_created = DateTime::createFromFormat(
                                'Y-m-d H:i:s', $link->created);

        if ($link_created === false) {
          $link_created = '';
        } else {
          $link_created = $link_created->format('Y-m-d H:i');
        }
      }
    }

    $errors = array();

    // Read and validate post variables if submitted or loading from
    // database.  Otherwise, initialize them.

    if (isset($_POST['modify-link']) ||
                $_GET['page'] == 'e11_recommended_links_edit') {

      if (isset($_POST['modify-link'])) {

        // Block the post if the nonce isn't verified.

        if (!wp_verify_nonce($_POST['_wpnonce_e11-modify-recommended-link'],
          'e11-modify-recommended-link')) {
          $errors[] = 'Invalid nonce';
        }

        // Read post variables.

        if (isset($_POST['link-id'])) {
          $link_id = wp_unslash($_POST['link-id']);
        }

        $link_title = trim(wp_unslash($_POST['link-title']));
        $link_url = trim(wp_unslash($_POST['link-url']));
        $link_description = trim(wp_unslash($_POST['link-description']));
        $link_display_mode = wp_unslash($_POST['link-display-mode']);
        $link_created = trim(wp_unslash($_POST['link-created']));
      }

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
        $link_created_formatted =
                DateTime::createFromFormat('Y-m-d H:i', $link_created);

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

        // Save link, if posting.

        if (isset($_POST['modify-link'])) {
          if ($_GET['page'] == 'e11_recommended_links_add') {

            // Insert new link if calling from "add" page.

            $result = $wpdb->insert(self::$linksTableName, array(
              'created' => $link_created . ':00',
              'display_mode' => $link_display_mode,
              'name' => $link_title,
              'url' => $link_url,
              'description' => $link_description
            ));

            if (1 != $result) {

              // Save unsuccessful.  Bounce user back to form with error message.

              $errors[] = 'An error occurred while saving the link: ' . $wpdb->last_error . '<br>Query: ' . $wpdb->last_query;
            } else {

              // Save successful.  Clear form and display success message.

              $messages[] = 'Link "' . $link_title . '" saved successfully.';

              $link_title = '';
              $link_url = '';
              $link_description = '';
              $link_display_mode = 1;
              $link_created = date('Y-m-d H:i', current_time('timestamp'));
            }
          } else {

            // Update existing link if calling from "edit" page.

            $result = $wpdb->update(self::$linksTableName, array(
              'created' => $link_created . ':00',
              'display_mode' => $link_display_mode,
              'name' => $link_title,
              'url' => $link_url,
              'description' => $link_description
            ), array('id' => $link_id));

            if (1 != $result) {

              // Save unsuccessful.  Bounce user back to form with error message.

              $errors[] = 'An error occurred while saving the link: ' . $wpdb->last_error . '<br>Query: ' . $wpdb->last_query;
            } else {

              // Save successful.  Display success message.

              $messages[] = 'Link "' . $link_title . '" updated successfully.';
            }
          }
        }
      }

      if (!empty($messages)) {
        foreach ($messages as $message) {
          echo '<div id="message" class="updated notice is-dismissible"><p>' . $message . '</p></div>';
        }
      }
      if (!empty($errors)) {
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
    }

    // Output remaining page HTML.

?>
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
        <?php
          if ($_GET['page'] == 'e11_recommended_links_add') {
        ?>
            <input id="modify-link" class="button button-primary"
                   name="modify-link" value="Save changes" type="submit" />
        <?php
          } else {
        ?>
            <input id="modify-link" class="button button-primary"
                   name="modify-link" value="Save changes" type="submit" />
        <?php
          }
        ?>
        <a href="<?php echo admin_url('admin.php?page=e11_recommended_links'); ?>"
           class="button button-cancel">Return to list</a>
      </form>
    </div>

<?php

  }

  /**
   * Callback to build and display "delete link" page for plugin.
   */
  public static function delete_link_page_html()
  {
    global $wpdb;

    // Verify capability.

    if (!current_user_can('manage_e11_recommended_links')) {
      wp_die(__('Your account is not able to modify recommended links.'));
    }

    // Verify nonce.

    check_admin_referer('bulk-e11-recommended-links');

    // Read link id(s) from request into array $ids.

    if (empty($_REQUEST['links'])) {
      if (!isset($_REQUEST['link'])) {
        wp_die(__('"link" parameter required but not found.'));
      }

      $ids = array(intval($_REQUEST['link']));
    } else {
      $ids = array_map('intval', (array)$_REQUEST['links']);
    }

    // Output page header HTML.
    ?>
      <div class="wrap">
      <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
      <form method="get">
      <input type="hidden" name="page" value="e11_recommended_links" />
    <?php

    // Display error messages if present.

    if (!empty(self::$actionErrors)) {
?>
      <div class="error">
        <ul>
          <?php
            foreach (self::$actionErrors as $error) {
              echo '<li>' . $error . '</li>' . "\n";
            }
          ?>
        </ul>
      </div>
<?php
    }

    // Build portion of query containing a set of placeholders equal to
    // the number of IDs being looked up.

    $idSet = '(%d' . str_repeat(',%d', count($ids) - 1) . ')';

    // Read records from database.

    $query = $wpdb->prepare('
          SELECT id, created, display_mode, name, url, description
          FROM ' . self::$linksTableName . '
          WHERE id IN ' . $idSet,
      $ids);

    $links = $wpdb->get_results($query);

    // Output rest of HTML for page.

    foreach ($links as $link) {
      echo '<input type="hidden" name="links[]" value="' . $link->id . '" />';
    }

    if (1 == count($ids)) {
      echo '<p>' . _e('You have specified this link for deletion:') . '</p>';
    } else {
      echo '<p>' . _e('You have specified these links for deletion:') . '</p>';
    }

    echo '<ul>';

    foreach ($links as $link) {
      echo '<li>ID #' . $link->id . ': ' . $link->name . ' (' . $link->url . ')</li>';
    }

    echo '</ul>';

    // [TODO] Different nonce field?
    wp_nonce_field('bulk-e11-recommended-links');

    if (isset($_REQUEST['wp_http_referer'])) {
      $redirect = remove_query_arg(array('wp_http_referer', 'updated', 'delete_count'), wp_unslash($_REQUEST['wp_http_referer']));
      $referer = '<input type="hidden" name="wp_http_referer" value="' . esc_attr($redirect) . '" />';
    } else {
      $referer = '';
    }
    echo $referer;

    echo '<input type="hidden" name="action" value="dodelete" />';

    submit_button(__('Confirm deletion'), 'primary');

    echo '</form>';
  }

  public static function delete_links_action() {
    global $wpdb;

    // Verify capability.

    if (!current_user_can('manage_e11_recommended_links')) {
      wp_die(__('Your account is not able to modify recommended links.'));
    }

    // Verify nonce.

    check_admin_referer('bulk-e11-recommended-links');

    // Read link id(s) from request into array $ids.

    if (empty($_REQUEST['links'])) {
      if (!isset($_REQUEST['link'])) {
        wp_die(__('"link" parameter required but not found.'));
      }

      $ids = array(intval($_REQUEST['link']));
    } else {
      $ids = array_map('intval', (array)$_REQUEST['links']);
    }

    // Build portion of query containing a set of placeholders equal to
    // the number of IDs being looked up.

    $idSet = '(%d' . str_repeat(',%d', count($ids) - 1) . ')';

    // Delete specified link records.

    $query = $wpdb->prepare('
        DELETE FROM ' . self::$linksTableName . '
        WHERE id IN ' . $idSet,
        $ids);

    $result = $wpdb->query($query);

    if ($result === false) {
      // Trigger return to the "delete links" page, and display error to
      // user.

      self::$actionErrors[] = 'An error occurred while deleting link(s): '
                      . $wpdb->last_error . '<br>Query: '
                      . $wpdb->last_query;
    } else {
      // Trigger return to the recommended links table with a status message
      // indicating success.

      wp_redirect(admin_url(
              'admin.php?page=e11_recommended_links&update=del&delete_count='
              . $result));

      exit;
    }
  }

  /**
   * Handle bulk actions that may require redirects.  Redirects can't be
   * issued from inside an admin page, so this is called as an 'admin_init'
   * action.
   */
  public static function process_bulk_action() {
    if (isset($_GET['page']) && $_GET['page'] == 'e11_recommended_links') {
      if (isset($_REQUEST['action'])) {
        switch($_REQUEST['action']) {
          case 'dodelete':
            self::delete_links_action();
            break;
        }
      }
    }
  }

}

add_action('admin_init', array('e11RecommendedLinksAdmin', 'settings_init'));
add_action('admin_init', array('e11RecommendedLinksAdmin', 'process_bulk_action'));
add_action('admin_menu', array('e11RecommendedLinksAdmin', 'admin_menu_options_page'));
