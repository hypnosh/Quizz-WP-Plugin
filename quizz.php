<?php
/**
 * Plugin Name: Quizz
 * Plugin URI: http://www.13llama.com/quizz
 * Description: A plugin that helps you create a simple sequential quiz on WordPress.
 * Version: 1.02
 * Author: 13 Llama Studio
 * Author URI: http://www.13llama.com/
 * License: GNU GPL
 */


function create_quiz_post() {

	$labels = array(
		'name'                => __( 'Questions', 'text-domain-plural' ),
		'singular_name'       => __( 'Question', 'text-domain' ),
		'add_new'             => _x( 'Add New Question', 'text-domain-plural', 'text-domain-plural' ),
		'add_new_item'        => __( 'Add New Question', 'text-domain' ),
		'edit_item'           => __( 'Edit Question', 'text-domain' ),
		'new_item'            => __( 'New Question', 'text-domain' ),
		'view_item'           => __( 'View Question', 'text-domain' ),
		'search_items'        => __( 'Search Questions', 'text-domain-plural' ),
		'not_found'           => __( 'No Questions found', 'text-domain-plural' ),
		'not_found_in_trash'  => __( 'No Questions found in Trash', 'text-domain-plural' ),
		'parent_item_colon'   => __( 'Parent Question:', 'text-domain' ),
		'menu_name'           => __( 'Questions', 'text-domain-plural' ),
	);

	$args = array(
		'labels'              => $labels,
		'hierarchical'        => false,
		'description'         => 'description',
		'taxonomies'          => array( 'category', 'post_tag' ),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => '',
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => true,
		'capability_type'     => 'post',
		'supports'            => array(
								'title', 
								'editor', 'thumbnail', 'revisions', 
								)
	);

	register_post_type( 'Question', $args );
}

add_action( 'init', 'create_quiz_post' );

function quiz_show_form( $content ) {

	if (get_post_type()=='question'):
		$answer = $_POST['answer'];	// user submitted answer

		// get meta values for this question

		$answers = get_post_custom_values('quizz_answer');
		$nextlevel = get_post_custom_values('quizz_nextlevel');
		$exact = get_post_custom_values('quizz_exact');
		$last_bool = get_post_custom_values('quizz_last');
		$lastpage = get_post_custom_values('quizz_lastpage');

		$error = "<p class='quiz_error quiz_message'>ERROR</p>";

		
		$lsubmittedanswer = strtolower($answer);
		$lactualanswer = strtolower($answers[0]);


		if ($exact[0]=="exact") {
			//exact, strict match
			if ($answer == $answers[0]) {
				$correct = "yes";
			} else {
				$correct = "no";
			}
		} else {
			$needlehaystack = strrpos($lsubmittedanswer, $lactualanswer);
			if ( $needlehaystack > -1 ) {
				$correct = "yes";
			} else {
				$correct = "no";
			}
		}
		if ( $correct == "yes" ) {

			if ($last_bool != "last") {
				// raise a hook for updating record
				do_action( 'quizz_level_updated', $nextlevel[0] );
				$goto = $nextlevel[0];
			} else {
				// raise a hook for end of quiz
				do_action( 'quizz_ended', $lastpage[0] );
				$goto = $lastpage[0];
			}

				// redirect to next question - or last page
				echo "<meta http_equiv='refresh' content='0, " . get_post_permalink($goto) . "' />";
				echo "<script>window.location='" . get_post_permalink($goto) . "';</script>";
		} else {

			if (empty($answer)) {
				$error = "";
			} else {
				$error = str_replace("ERROR", __("Wrong answer. Try again.", 'quiz_text_domain'), $error);
			}
		}

		$theForm = '<form action="" method="POST" class="quiz_form form">
						<input type="text" name="answer" id="answer" class="quiz_answer answers" />
						<input type="submit" value="Check" class="quiz_button" />
					</form>';

		return $content . $error . $theForm;
	else :
		return $content;
	endif;
}

add_filter( 'the_content', 'quiz_show_form' );


function quizz_add_custom_box() {

    $screens = array( 'question' );

    foreach ( $screens as $screen ) {

        add_meta_box(
            'answers-more',
            __( 'Answers &amp; more', 'quizz_textdomain' ),
            'quizz_inner_custom_box',
            $screen, 'normal'
        );
    }
}
add_action( 'add_meta_boxes', 'quizz_add_custom_box' );


function quizz_inner_custom_box( $post ) {

  // Add an nonce field so we can check for it later.
  wp_nonce_field( 'quizz_inner_custom_box', 'quizz_inner_custom_box_nonce' );

  /*
   * Use get_post_meta() to retrieve an existing value
   * from the database and use the value for the form.
   */
  $value = get_post_meta( $post->ID, 'quizz_answer', true );

  echo '<label for="quizz_answer">';
       _e( "Answer", 'quizz_textdomain' );
  echo '</label> ';
  echo '<input type="text" id="quizz_answer" name="quizz_answer" value="' . esc_attr( $value ) . '" size="35" />';

  $value1 = get_post_meta( $post->ID, 'quizz_exact', true);
  echo '<input type="checkbox" name="quizz_exact" id="quizz_exact" value="exact" ' . (($value1=="exact") ? " checked" : "") . '>Exact match (also enforces case)';

  echo '<br />';


  global $wpdb;
   $query = "SELECT `post_id` FROM $wpdb->postmeta WHERE `meta_value`='%s'";

   $prev = $wpdb->get_var( $wpdb->prepare($query, $post->ID) );



  echo '<label for="quizz_prevlevel">';
       _e( "Previous Level", 'quizz_textdomain' );
  echo '</label>';
	  $args = array(
	  		'post_type' => 'question',
	  		'exclude' => $post->ID,
	  		'post_status' => 'publish'
	  	);

	  $questions = get_posts( $args );

  echo '<select id="quizz_prevlevel" name="quizz_prevlevel">';

	  	echo "<option value='0'>None</option>";

	  foreach ($questions as $question) {

	  	echo "<option value='" . $question->ID . "'". (( $prev == $question->ID ) ? ' selected' : '') . ">" . $question->post_title . "-" . $question->post_content ."</option>";
	  }
  echo '</select> <br/>';

  $last = get_post_meta( $post->ID, 'quizz_last_bool', true);

  echo '<input type="checkbox" name="quizz_last" id="quizz_last" value="last"' . (($last=="last") ? " checked" : "" ) . '> Last level?';

  $lastlevel = get_post_meta( $post->ID, 'quizz_last', true);

  	$args = array(
  		'post_type' => 'page',
  		'post_status' => 'publish'
  	);

  	$lastpages = get_posts($args);

  echo '<select id="quizz_lastpage" name="quizz_lastpage">';

  	echo '<option value="0">None</option>';

  	foreach ($lastpages as $lastpage) {
	  	echo "<option value='" . $lastpage->ID . "'". (( $lastlevel == $lastpage->ID ) ? ' selected' : '') . ">" . $lastpage->post_title ."</option>";
  	}
  echo '</select>';
}

function quizz_save_postdata( $post_id ) {

  /*
   * We need to verify this came from the our screen and with proper authorization,
   * because save_post can be triggered at other times.
   */

  // Check if our nonce is set.
  if ( ! isset( $_POST['quizz_inner_custom_box_nonce'] ) )
    return $post_id;

  $nonce = $_POST['quizz_inner_custom_box_nonce'];

  // Verify that the nonce is valid.
  if ( ! wp_verify_nonce( $nonce, 'quizz_inner_custom_box' ) )
      return $post_id;

  // If this is an autosave, our form has not been submitted, so we don't want to do anything.
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return $post_id;

  // Check the user's permissions.
  if ( 'page' == $_POST['post_type'] ) {

    if ( ! current_user_can( 'edit_page', $post_id ) )
        return $post_id;
  
  } else {

    if ( ! current_user_can( 'edit_post', $post_id ) )
        return $post_id;
  }

  /* OK, its safe for us to save the data now. */

  // Sanitize user input.
  $myanswer = sanitize_text_field( $_POST['quizz_answer'] );
  $fromlevel = $_POST['quizz_prevlevel'];
  $exact = $_POST['quizz_exact'];
  $lastlevel_bool = $_POST['quizz_last'];
  $lastpage = $_POST['quizz_lastpage'];


  // Update the meta field in the database.
  update_post_meta( $post_id, 'quizz_answer', $myanswer );
  update_post_meta( $fromlevel, 'quizz_nextlevel', $post_id );
  update_post_meta( $post_id, 'quizz_exact', $exact );
  update_post_meta( $post_id, 'quizz_last', $lastlevel_bool );
  update_post_meta( $post_id, 'quizz_lastpage', $lastpage );

}

add_action( 'save_post', 'quizz_save_postdata' );