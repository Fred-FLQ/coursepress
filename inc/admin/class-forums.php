<?php
/**
 * Class CoursePress_Admin_Forums
 *
 * @since 2.0
 * @package CoursePress
 */
class CoursePress_Admin_Forums extends CoursePress_Admin_Page {
	protected $slug = 'coursepress_forum';
	private $items;
	private $post_type = 'discussions';
	private $id_name = 'notification_id';

	public function __construct() {
        parent::__construct();
	}

	function columns() {
		$columns = array(
			'topic' => __( 'Topic', 'cp' ),
			'course' => __( 'Course', 'cp' ),
			'comments' => __( 'Comments', 'cp' ),
			'status' => __( 'Status', 'cp' ),
		);
		return $columns;
	}

	private function get_page_list() {
		$search = isset( $_GET['s'] ) ? $_GET['s'] : '';
		$args = array(
			'columns' => $this->columns(),
			'courses' => coursepress_get_accessible_courses( false ),
			'hidden_columns' => array(),
			'forums' => $this->get_list(),
			'page' => $this->slug,
			'search' => $search,
			'edit_link' => add_query_arg(
				array(
					'page' => $this->slug,
					$this->id_name => 0,
				),
				admin_url( 'admin.php' )
			),
		);
		coursepress_render( 'views/admin/forums', $args );
	}

    private function get_page_edit( $forum_id ) {
        $args = array(
            'page' => $this->slug,
            'post_title' => '',
            'post_content' => '',
            'course_id' => 0,
            $this->id_name => 0,
            'unit_id' => 'course',
            'email_notification' => 'yes',
            'thread_comments_depth' => 5,
            'comments_per_page' => 50,
            'comments_order' => 'newer',
            'courses' => array(),
            'units' => array(
                'course' => __( 'All units', 'cp' ),
            ),
        );
        if ( isset( $forum_id ) ) {
            if ( ! empty( $forum_id ) || 0 === $forum_id ) {
                $forum_id = $this->update( $forum_id );
            }
            $post = get_post( $forum_id );
            if ( is_a( $post, 'WP_Post' ) ) {
                if ( $this->post_type == $post->post_type ) {
                    $args[ $this->id_name ] = $post->ID;
                    $args['post_title'] = $post->post_title;
                    $args['post_content'] = stripslashes( $post->post_content );
                    $meta_keys = array( 'course_id', 'email_notification', 'unit_id', 'email_notification', 'thread_comments_depth', 'comments_per_page', 'comments_order' );
                    foreach( $meta_keys as $meta_key ) {
                        $meta_value = get_post_meta( $post->ID, $meta_key, true );
                        if ( ! empty( $meta_value ) ) {
                            $args[$meta_key] = $meta_value;
                        }
                    }
                    $course = get_post( $args['course_id'] );
                    if ( is_a( $course, 'WP_Post' ) ) {
                        $args['courses'][$course->ID] = $course->post_title;
                        $course = new CoursePress_Course( $course );
                        $units = $course->get_units();
                        foreach( $units as $unit ) {
                            $args['units'][$unit->ID] = $unit->post_title;
                        }
                    }
                }
            }
        }
        coursepress_render( 'views/admin/forum-edit', $args );
    }

	public function get_page() {
		$forum_id = filter_input( INPUT_GET, $this->id_name, FILTER_VALIDATE_INT );
		if ( $forum_id || 0 === $forum_id ) {
			$this->get_page_edit( $forum_id );
		} else {
			$this->get_page_list();
		}
		coursepress_render( 'views/admin/footer-text' );
	}

	public function get_list() {
		/**
		 * search
		 */
		$s = isset( $_POST['s'] )? mb_strtolower( trim( $_POST['s'] ) ):false;
		/**
		 * Per Page
		 */
		$per_page = $this->get_per_page();
		$per_page = $this->get_items_per_page( 'coursepress_forums_per_page', $per_page );
		/**
		 * Pagination
		 */
		$current_page = $this->get_pagenum();
		$offset = ( $current_page - 1 ) * $per_page;
		$post_args = array(
			'post_type' => $this->post_type,
			'posts_per_page' => $per_page,
			'paged' => $current_page,
			's' => $s,
			'post_status' => 'any',
		);
		/**
		 * Course ID
		 */
		$course_id = isset( $_GET['course_id'] ) ? sanitize_text_field( $_GET['course_id'] ) : '';
		if ( ! empty( $course_id ) ) {
			$post_args['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key' => 'course_id',
					'value' => (int) $course_id,
				)
			);
		}
		$wp_query = new WP_Query( $post_args );
		$this->items = array();
		$base_url = add_query_arg( 'page', $this->slug, admin_url( 'admin.php' ) );
		foreach ( $wp_query->posts as $one ) {
			$one->course_id = get_post_meta( $one->ID, 'course_id', true );
			$one->unit_id = get_post_meta( $one->ID, 'unit_id', true );
			$one->comments_number = get_comments_number( $one->ID );
			$one->edit_link = wp_nonce_url(
				add_query_arg( $this->id_name, $one->ID, $base_url ),
				$this->get_nonce_action( $one->ID )
			);
			$this->items[] = $one;
		}
		return $this->items;
	}

	/**
	 * Get nonce action.
	 *
	 * @since 3.0.0
	 *
	 * @param integer $id Forum ID.
	 * @returns strinng nonce action.
	 */
	private function get_nonce_action( $id ) {
		return sprintf( '%s_%d', $this->slug, $id );
	}

	private function update( $forum_id ) {
		/**
		 * check input
		 */
		if ( ! isset( $_POST['_wpnonce'] ) || ! isset( $_POST[ $this->id_name ] ) ) {
			return $forum_id;
		}
		/**
		 * check nonce
		 */
		$nonce_action = 'coursepress-update-notifiction-'.$_POST[ $this->id_name ];
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], $nonce_action ) ) {
			return $forum_id;
        }
		$postarr = array(
			'ID' => $_POST[ $this->id_name ],
			'post_title' => isset( $_POST['post_title'] )? $_POST['post_title']:'',
			'post_content' => isset( $_POST['post_content'] )? $_POST['post_content']:'',
			'post_type' => $this->post_type,
			'post_status' => 'publish',
			'meta_input' => array(
				'course_id' => isset( $_POST['course_id'] )? $_POST['course_id']:0,
				'unit_id' => isset( $_POST['unit_id'] )? $_POST['unit_id']:'course',
				'email_notification' => isset( $_POST['email_notification'] )? 'yes':'no',
				'thread_comments_depth' => isset( $_POST['thread_comments_depth'] )? $_POST['thread_comments_depth']:5,
				'comments_per_page' => isset( $_POST['comments_per_page'] )? $_POST['comments_per_page']:50,
			),
		);
		$postarr = sanitize_post( $postarr, 'db' );
		$post_id = wp_insert_post( $postarr );
		if ( 0 == $postarr['ID'] ) {
			return $post_id;
		}
		return $forum_id;
	}
}
