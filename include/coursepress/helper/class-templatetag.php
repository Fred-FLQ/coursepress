<?php

class CoursePress_Helper_TemplateTag {
	public static function init() {
	}
}

if ( ! function_exists( 'cp_is_chat_plugin_active' ) ) {
	function cp_is_chat_plugin_active() {
		$plugins = get_option( 'active_plugins' );

		if ( is_multisite() ) {
			$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins' );
		} else {
			$active_sitewide_plugins = array();
		}

		$required_plugin = 'wordpress-chat/wordpress-chat.php';

		if ( in_array( $required_plugin, $plugins ) || cp_is_plugin_network_active( $required_plugin ) || preg_grep( '/^wordpress-chat.*/', $plugins ) || cp_preg_array_key_exists( '/^wordpress-chat.*/', $active_sitewide_plugins ) ) {
			return true;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'cp_is_plugin_network_active' ) ) {
	function cp_is_plugin_network_active( $plugin_file ) {
		if ( is_multisite() ) {
			return ( array_key_exists( $plugin_file, maybe_unserialize( get_site_option( 'active_sitewide_plugins' ) ) ) );
		}
	}
}

if ( ! function_exists( 'cp_is_course_visited' ) ) {
	function cp_is_course_visited( $course_id, $student_id = false ) {
		if ( ! $student_id ) {
			$student_id = get_current_user_ID();
		}

		$visited_courses = get_user_option( 'visited_course_units_' . $course_id, $student_id );

		if ( $visited_courses ) {
			$visited_courses = ( explode( ',', $visited_courses ) );
			if ( is_array( $visited_courses ) ) {
				if ( in_array( $course_id, $visited_courses ) ) {
					return true;
				} else {
					return false;
				}
			} else {
				if ( $visited_courses == $course_id ) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'cp_url_origin' ) ) {
	function cp_url_origin( $s, $use_forwarded_host = false ) {
		$ssl = ( ! empty( $s['HTTPS'] ) && 'on' == $s['HTTPS'] ) ? true : false;
		$sp = strtolower( $s['SERVER_PROTOCOL'] );
		$protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
		$port = $s['SERVER_PORT'];
		$port = ( ( ! $ssl && '80' == $port ) || ( $ssl && '443' == $port ) ) ? '' : ':' . $port;
		$host = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
		$host = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;

		return $protocol . '://' . $host;
	}
}

if ( ! function_exists( 'cp_full_url' ) ) {
	function cp_full_url( $s, $use_forwarded_host = false ) {
		return cp_url_origin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
	}
}

if ( ! function_exists( 'cp_preg_array_key_exists' ) ) {
	function cp_preg_array_key_exists( $pattern, $array ) {
		$keys = array_keys( $array );

		return (int) preg_grep( $pattern, $keys );
	}
}

if ( ! function_exists( 'cp_get_fragment' ) ) {
	function cp_get_fragment() {
	}
}

if ( ! function_exists( 'cp_is_chat_plugin_active' ) ) {
	function cp_is_chat_plugin_active() {
		$plugins = get_option( 'active_plugins' );

		if ( is_multisite() ) {
			$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins' );
		} else {
			$active_sitewide_plugins = array();
		}

		$required_plugin = 'wordpress-chat/wordpress-chat.php';

		if ( in_array( $required_plugin, $plugins ) || cp_is_plugin_network_active( $required_plugin ) || preg_grep( '/^wordpress-chat.*/', $plugins ) || cp_preg_array_key_exists( '/^wordpress-chat.*/', $active_sitewide_plugins ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Unit unit module pagination
	 */
}

if ( ! function_exists( 'coursepress_unit_module_pagination' ) ) {
	function coursepress_unit_module_pagination( $unit_id, $pages_num, $check_is_last_page = false ) {
		global $wp, $wp_query, $paged, $coursepress_modules, $coursepress;

		if ( ! isset( $unit_id ) ) {// || !is_singular()
			//<br clear="all">
			echo '<div class="navigation module-pagination" id="navigation-pagination"></div>';

			return;
		}

		$paged = isset( $wp->query_vars['paged'] ) ? absint( $wp->query_vars['paged'] ) : 1;

		$max = intval( $pages_num ); //number of page-break modules + 1

		$wp_query->max_num_pages = $max;

		if ( $check_is_last_page ) {
			if ( $max <= 1 || ( $max == $paged ) ) {
				return true;
			} else {
				return false;
			}
		}
		//<br clear="all">
		if ( $wp_query->max_num_pages <= 1 ) {
			echo '<div class="navigation module-pagination" id="navigation-pagination"></div>';

			return;
		}
		//<br clear="all">
		echo '<div class="navigation module-pagination" id="navigation-pagination"><ul>' . "\n";

		for ( $link_num = 1; $link_num <= $max; $link_num ++ ) {
			$enabled = '';
			if ( $coursepress->is_preview( $unit_id, $link_num ) ) {
				$enabled = 'enabled-link';
			} else {
				if ( isset( $_GET['try'] ) ) {
					$enabled = 'disabled-link';
				}
			}

			$class = ( $paged == $link_num ? ' class="active ' . $enabled . '"' : ' class="' . $enabled . '"' );

			printf( '<li%1$s><a href="%2$s">%3$s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link_num ) ), $link_num );
		}

		echo '</ul></div>' . "\n";
	}
}

if ( ! function_exists( 'coursepress_unit_module_pagination_ellipsis' ) ) {
	function coursepress_unit_module_pagination_ellipsis( $unit_id, $pages_num ) {
		global $wp, $wp_query, $paged, $coursepress_modules;

		if ( ! isset( $unit_id ) || ! is_singular() ) {
			return;
		}

		$paged = $wp->query_vars['paged'] ? absint( $wp->query_vars['paged'] ) : 1;

		$max = intval( $pages_num ); //number of page-break modules + 1

		$wp_query->max_num_pages = $max;

		if ( $wp_query->max_num_pages <= 1 ) {
			return;
		}

		//	Add current page to the array
		if ( $paged >= 1 ) {
			$links[] = $paged;
		}

		//	Add the pages around the current page to the array
		if ( $paged >= 3 ) {
			$links[] = $paged - 1;
			$links[] = $paged - 2;
		}

		if ( ( $paged + 2 ) <= $max ) {
			$links[] = $paged + 2;
			$links[] = $paged + 1;
		}

		echo '<br clear="all"><div class="navigation"><ul>' . "\n";

		//	Previous Post Link
		if ( get_previous_posts_link() ) {
			printf( '<li>%s</li>' . "\n", get_previous_posts_link( '<span class="meta-nav">&larr;</span>' ) );
		}

		//	Link to first page, plus ellipses if necessary
		if ( ! in_array( 1, $links ) ) {
			$class = 1 == $paged ? ' class="active"' : '';

			printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

			if ( ! in_array( 2, $links ) ) {
				echo '<li>…</li>';
			}
		}

		//	Link to current page, plus 2 pages in either direction if necessary
		sort( $links );

		foreach ( (array) $links as $link ) {
			$class = $paged == $link ? ' class="active"' : '';
			printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
		}

		//	Link to last page, plus ellipses if necessary
		if ( ! in_array( $max, $links ) ) {
			if ( ! in_array( $max - 1, $links ) ) {
				echo '<li>…</li>' . "\n";
			}

			$class = $paged == $max ? ' class="active"' : '';
			printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
		}

		$nextpage = intval( $paged ) + 1;
		//	Next Post Link
		if ( $nextpage <= $pages_num ) {
			$attr = apply_filters( 'next_posts_link_attributes', '' );

			printf( '<li>%s</li>' . "\n", get_next_posts_link( '<span class="meta-nav">&rarr;</span>' ) );
		}

		echo '</ul></div>' . "\n";
	}
}

if ( ! function_exists( 'coursepress_unit_pages' ) ) {
	function coursepress_unit_pages( $unit_id, $unit_pagination = false ) {
		if ( $unit_pagination ) {

			$args = array(
				'post_type' => 'module',
				'post_status' => 'publish',
				'posts_per_page' => 1,
				'post_parent' => $unit_id,
				'meta_key' => 'module_page',
				'orderby' => 'meta_value_num',
				'order' => 'DESC',
			);

			$modules = get_posts( $args );
			$module_id = isset( $modules[0] ) ? $modules[0]->ID : 0;

			if ( $module_id > 0 ) {
				$pages_num = count( get_post_meta( $unit_id, 'page_title', true ) );
				//$pages_num = get_post_meta( $module_id, 'module_page', true );
			} else {
				$pages_num = 1;
			}
		} else {
			$pages_num = 1;

			$modules = Unit_Module::get_modules( $unit_id );

			foreach ( $modules as $mod ) {
				if ( Unit_Module::get_module_type( $mod->ID ) == 'page_break_module' ) {
					$pages_num ++;
				}
			}
		}

		return $pages_num;
	}

	//get_site_option instead of get_option

}

if ( ! function_exists( 'coursepress_send_email' ) ) {
	function coursepress_send_email( $email_args = array() ) {

		if ( 'student_registration' == $email_args['email_type'] ) {
			global $course_slug;
			$email_address = $email_args['student_email'];
			$subject = coursepress_get_registration_email_subject();
			$courses_address = trailingslashit( home_url() ) . trailingslashit( $course_slug );

			$tags = array(
				'STUDENT_FIRST_NAME',
				'STUDENT_LAST_NAME',
				'STUDENT_USERNAME',
				'STUDENT_PASSWORD',
				'BLOG_NAME',
				'LOGIN_ADDRESS',
				'COURSES_ADDRESS',
				'WEBSITE_ADDRESS',
			);
			$tags_replaces = array(
				$email_args['student_first_name'],
				$email_args['student_last_name'],
				$email_args['student_username'],
				$email_args['student_password'],
				get_bloginfo(),
				cp_student_login_address(),
				$courses_address,
				home_url(),
			);

			$message = coursepress_get_registration_content_email();

			$message = str_replace( $tags, $tags_replaces, $message );

			add_filter( 'wp_mail_from', 'my_registration_from_function' );

			if ( ! function_exists( 'my_registration_from_function' ) ) {
				function my_registration_from_function( $email ) {
					return coursepress_get_registration_from_email();
				}
			}

			add_filter( 'wp_mail_from_name', 'my_registration_from_name_function' );

			if ( ! function_exists( 'my_registration_from_name_function' ) ) {
				function my_registration_from_name_function( $name ) {
					return coursepress_get_registration_from_name();
				}
			}
		}

		if ( 'enrollment_confirmation' == $email_args['email_type'] ) {
			global $course_slug;
			$email_address = $email_args['student_email'];
			$dashboard_address = $email_args['dashboard_address'];
			$subject = coursepress_get_enrollment_email_subject();
			$courses_address = trailingslashit( home_url() ) . trailingslashit( $course_slug );
			$course = new Course( $email_args['course_id'] );

			$tags = array(
				'STUDENT_FIRST_NAME',
				'STUDENT_LAST_NAME',
				'BLOG_NAME',
				'LOGIN_ADDRESS',
				'COURSES_ADDRESS',
				'WEBSITE_ADDRESS',
				'COURSE_ADDRESS',
				'COURSE_TITLE',
				'STUDENT_DASHBOARD',
			);
			$tags_replaces = array(
				$email_args['student_first_name'],
				$email_args['student_last_name'],
				get_bloginfo(),
				cp_student_login_address(),
				$courses_address,
				home_url(),
				$course->get_permalink(),
				$course->details->post_title,
				$email_args['dashboard_address'],
			);

			$message = coursepress_get_enrollment_content_email();

			$message = str_replace( $tags, $tags_replaces, $message );

			add_filter( 'wp_mail_from', 'my_enrollment_from_function' );

			if ( ! function_exists( 'my_enrollment_from_function' ) ) {
				function my_enrollment_from_function( $email ) {
					return coursepress_get_enrollment_from_email();
				}
			}

			add_filter( 'wp_mail_from_name', 'my_enrollment_from_name_function' );

			if ( ! function_exists( 'my_enrollment_from_name_function' ) ) {
				function my_enrollment_from_name_function( $name ) {
					return coursepress_get_enrollment_from_name();
				}
			}
		}

		if ( 'student_invitation' == $email_args['email_type'] ) {
			global $course_slug;

			$email_address = $email_args['student_email'];

			if ( isset( $email_args['course_id'] ) ) {
				$course = new Course( $email_args['course_id'] );
			}

			$tags = array(
				'STUDENT_FIRST_NAME',
				'STUDENT_LAST_NAME',
				'COURSE_NAME',
				'COURSE_EXCERPT',
				'COURSE_ADDRESS',
				'WEBSITE_ADDRESS',
				'PASSCODE',
			);
			$tags_replaces = array(
				$email_args['student_first_name'],
				$email_args['student_last_name'],
				$course->details->post_title,
				$course->details->post_excerpt,
				$course->get_permalink(),
				home_url(),
				$course->details->passcode,
			);

			if ( 'passcode' == $email_args['enroll_type'] ) {
				$message = coursepress_get_invitation_content_passcode_email();
				$subject = coursepress_get_invitation_passcode_email_subject();
			} else {
				$message = coursepress_get_invitation_content_email();
				$subject = coursepress_get_invitation_email_subject();
			}

			$message = str_replace( $tags, $tags_replaces, $message );

			add_filter( 'wp_mail_from', 'my_passcode_from_function' );

			if ( ! function_exists( 'my_passcode_from_function' ) ) {
				function my_passcode_from_function( $email ) {
					return coursepress_get_invitation_passcode_from_email();
				}
			}

			add_filter( 'wp_mail_from_name', 'my_passcode_from_name_function' );

			if ( ! function_exists( 'my_passcode_from_name_function' ) ) {
				function my_passcode_from_name_function( $name ) {
					return coursepress_get_invitation_passcode_from_name();
				}
			}
		}

		if ( 'instructor_invitation' == $email_args['email_type'] ) {
			global $course_slug;

			$course = '';
			$course_summary = '';
			$course_name = '';
			$courses_address = trailingslashit( home_url() ) . trailingslashit( $course_slug );
			$bugfix = false;

			if ( isset( $email_args['course_id'] ) ) {
				$course = new Course( $email_args['course_id'] );

				$course_name = $course->details->post_title;
				$course_summary = $course->details->post_excerpt;

				// For unpublished courses.
				$permalink = '';
				if ( in_array( $course->details->post_status, array( 'draft', 'pending', 'auto-draft' ) ) ) {
					$permalink = CoursePress::instance()->get_course_slug( true ) . '/' . $course->details->post_name . '/';
				} else {
					$permalink = get_permalink( $email_args['course_id'] );
				}

				$course_address = $permalink;
			}

			$confirm_link = $course_address . '?action=course_invite&course_id=' . $email_args['course_id'] . '&c=' . $email_args['invite_code'] . '&h=' . $email_args['invite_hash'];

			$email_address = $email_args['instructor_email'];
			$subject = cp_get_instructor_invitation_email_subject();

			$tags = array(
				'INSTRUCTOR_FIRST_NAME',
				'INSTRUCTOR_LAST_NAME',
				'INSTRUCTOR_EMAIL',
				'CONFIRMATION_LINK',
				'COURSE_NAME',
				'COURSE_EXCERPT',
				'COURSE_ADDRESS',
				'WEBSITE_ADDRESS',
				'WEBSITE_NAME',
			);

			$tags_replaces = array(
				$email_args['first_name'],
				$email_args['last_name'],
				$email_address,
				$confirm_link,
				$course_name,
				$course_summary,
				$course_address,
				home_url(),
				get_bloginfo(),
			);

			$message = cp_get_instructor_invitation_email();

			$message = str_replace( $tags, $tags_replaces, $message );

			add_filter( 'wp_mail_from', 'my_instructor_invitation_from_function' );

			if ( ! function_exists( 'my_instructor_invitation_from_function' ) ) {
				function my_instructor_invitation_from_function( $email ) {
					return coursepress_get_instructor_invitation_from_email();
				}
			}

			add_filter( 'wp_mail_from_name', 'my_instructor_invitation_from_name_function' );

			if ( ! function_exists( 'my_instructor_invitation_from_name_function' ) ) {
				function my_instructor_invitation_from_name_function( $name ) {
					return coursepress_get_instructor_invitation_from_name();
				}
			}
		}

		add_filter( 'wp_mail_content_type', 'cp_email_set_content_type' );

		if ( ! function_exists( 'cp_email_set_content_type' ) ) {
			function cp_email_set_content_type( $content_type ) {
				return 'text/html';
			}
		}

		add_filter( 'wp_mail_charset', 'cp_set_charset' );

		if ( ! function_exists( 'cp_set_charset' ) ) {
			function cp_set_charset( $charset ) {
				return get_option( 'blog_charset' );
			}
		}

		return wp_mail( $email_address, stripslashes( $subject ), stripslashes( nl2br( $message ) ) );
	}

	/* Get Student Invitation with Passcode to a Course E-mail data */
}

if ( ! function_exists( 'coursepress_get_invitation_passcode_from_name' ) ) {
	function coursepress_get_invitation_passcode_from_name() {
		return get_option( 'invitation_passcode_from_name', get_option( 'blogname' ) );
	}
}

if ( ! function_exists( 'coursepress_get_invitation_passcode_from_email' ) ) {
	function coursepress_get_invitation_passcode_from_email() {
		return get_option( 'invitation_passcode_from_email', get_option( 'admin_email' ) );
	}
}

if ( ! function_exists( 'coursepress_get_invitation_passcode_email_subject' ) ) {
	function coursepress_get_invitation_passcode_email_subject() {
		return get_option(
			'invitation_passcode_email_subject',
			__( 'Invitation to a Course ( Psss...for selected ones only )', 'CP_TD' )
		);
	}
}

if ( ! function_exists( 'coursepress_get_invitation_content_passcode_email' ) ) {
	function coursepress_get_invitation_content_passcode_email() {
		$default_invitation_content_passcode_email = sprintf( __( 'Hi %1$s,

			we would like to invite you to participate in the course: "%2$s"

			Since the course is only for selected ones, it is passcode protected. Here is the passcode for you: %6$s

			What is all about:
			%3$s

			Check this page for more info on the course: %4$s

			If you have any question feel free to contact us.

				Yours sincerely,
				%5$s Team', 'CP_TD' ), 'STUDENT_FIRST_NAME', 'COURSE_NAME', 'COURSE_EXCERPT', '<a href="COURSE_ADDRESS">COURSE_ADDRESS</a>', '<a href="WEBSITE_ADDRESS">WEBSITE_ADDRESS</a>', 'PASSCODE' );

		return get_option(
			'invitation_content_passcode_email',
			$default_invitation_content_passcode_email
		);
	}

	/* Get Student Invitation to a Course E-mail data */
}

if ( ! function_exists( 'coursepress_get_invitation_from_name' ) ) {
	function coursepress_get_invitation_from_name() {
		return get_option( 'invitation_from_name', get_option( 'blogname' ) );
	}
}

if ( ! function_exists( 'coursepress_get_invitation_from_email' ) ) {
	function coursepress_get_invitation_from_email() {
		return get_option( 'invitation_from_email', get_option( 'admin_email' ) );
	}
}

if ( ! function_exists( 'coursepress_get_invitation_email_subject' ) ) {
	function coursepress_get_invitation_email_subject() {
		return get_option( 'invitation_email_subject', __( 'Invitation to a Course', 'CP_TD' ) );
	}
}

if ( ! function_exists( 'coursepress_get_invitation_content_email' ) ) {
	function coursepress_get_invitation_content_email() {
		$default_invitation_content_email = sprintf( __( 'Hi %1$s,

			we would like to invite you to participate in the course: "%2$s"

			What is all about:
			%3$s

			Check this page for more info on the course: %4$s

			If you have any question feel free to contact us.

				Yours sincerely,
				%5$s Team', 'CP_TD' ), 'STUDENT_FIRST_NAME', 'COURSE_NAME', 'COURSE_EXCERPT', '<a href="COURSE_ADDRESS">COURSE_ADDRESS</a>', '<a href="WEBSITE_ADDRESS">WEBSITE_ADDRESS</a>' );

		return get_option( 'invitation_content_email', $default_invitation_content_email );
	}

	/* Get registration email data */

}

if ( ! function_exists( 'coursepress_get_registration_from_name' ) ) {
	function coursepress_get_registration_from_name() {
		return get_option( 'registration_from_name', get_option( 'blogname' ) );
	}
}

if ( ! function_exists( 'coursepress_get_registration_from_email' ) ) {
	function coursepress_get_registration_from_email() {
		return get_option( 'registration_from_email', get_option( 'admin_email' ) );
	}
}

if ( ! function_exists( 'coursepress_get_registration_email_subject' ) ) {
	function coursepress_get_registration_email_subject() {
		return get_option( 'registration_email_subject', __( 'Registration Status', 'CP_TD' ) );
	}
}

if ( ! function_exists( 'coursepress_get_registration_content_email' ) ) {
	function coursepress_get_registration_content_email() {
		$default_registration_content_email = sprintf( __( 'Hi %1$s,

			Congratulations! You have registered account with %2$s successfully! You may log into your account here: %3$s.

			Get started by exploring our courses here: %4$s

			Yours sincerely,
			%5$s Team', 'CP_TD' ), 'STUDENT_FIRST_NAME', 'BLOG_NAME', '<a href="LOGIN_ADDRESS">LOGIN_ADDRESS</a>', '<a href="COURSES_ADDRESS">COURSES_ADDRESS</a>', '<a href="WEBSITE_ADDRESS">WEBSITE_ADDRESS</a>' );

		return get_option( 'registration_content_email', $default_registration_content_email );
	}

	/* Get MarketPress order email data */

}

if ( ! function_exists( 'coursepress_get_mp_order_from_name' ) ) {
	function coursepress_get_mp_order_from_name() {
		return get_option( 'mp_order_from_name', get_option( 'blogname' ) );
	}
}

if ( ! function_exists( 'coursepress_get_mp_order_from_email' ) ) {
	function coursepress_get_mp_order_from_email() {
		return get_option( 'mp_order_from_email', get_option( 'admin_email' ) );
	}
}

if ( ! function_exists( 'coursepress_get_mp_order_email_subject' ) ) {
	function coursepress_get_mp_order_email_subject() {
		return get_option( 'mp_order_email_subject', __( 'Order Confirmation', 'CP_TD' ) );
	}
}

if ( ! function_exists( 'coursepress_get_mp_order_content_email' ) ) {
	function coursepress_get_mp_order_content_email() {
		$default_mp_order_content_email = sprintf( __( 'Thank you for your order %1$s,

			Your order for course "%2$s" has been received!

			Please refer to your Order ID (ORDER_ID) whenever contacting us.

			You can track the latest status of your order here: ORDER_STATUS_URL

			Yours sincerely,
			%5$s Team', 'CP_TD' ), 'CUSTOMER_NAME', '<a href="COURSE_ADDRESS">COURSE_TITLE</a>', '<a href="STUDENT_DASHBOARD">' . __( 'Dashboard', 'CP_TD' ) . '</a>', '<a href="COURSES_ADDRESS">COURSES_ADDRESS</a>', 'BLOG_NAME' );

		return get_option( 'mp_order_content_email', $default_mp_order_content_email );
	}

	/* Get enrollment email data */

}

if ( ! function_exists( 'coursepress_get_enrollment_from_name' ) ) {
	function coursepress_get_enrollment_from_name() {
		return get_option( 'enrollment_from_name', get_option( 'blogname' ) );
	}
}

if ( ! function_exists( 'coursepress_get_enrollment_from_email' ) ) {
	function coursepress_get_enrollment_from_email() {
		return get_option( 'enrollment_from_email', get_option( 'admin_email' ) );
	}
}

if ( ! function_exists( 'coursepress_get_enrollment_email_subject' ) ) {
	function coursepress_get_enrollment_email_subject() {
		return get_option( 'enrollment_email_subject', __( 'Enrollment Confirmation', 'CP_TD' ) );
	}
}

if ( ! function_exists( 'coursepress_get_enrollment_content_email' ) ) {
	function coursepress_get_enrollment_content_email() {
		$default_enrollment_content_email = sprintf( __( 'Hi %1$s,

			Congratulations! You have enrolled in course "%2$s" successfully!

			You may check all courses you are enrolled in here: %3$s.

			Or you can explore other courses in your %4$s

			Yours sincerely,
			%5$s Team', 'CP_TD' ), 'STUDENT_FIRST_NAME', '<a href="COURSE_ADDRESS">COURSE_TITLE</a>', '<a href="STUDENT_DASHBOARD">' . __( 'Dashboard', 'CP_TD' ) . '</a>', '<a href="COURSES_ADDRESS">COURSES_ADDRESS</a>', 'BLOG_NAME' );

		return get_option( 'enrollment_content_email', $default_enrollment_content_email );
	}

	/* Get instructor invite email data */
}

if ( ! function_exists( 'coursepress_get_instructor_invitation_from_name' ) ) {
	function coursepress_get_instructor_invitation_from_name() {
		return get_option( 'instructor_invitation_from_name', get_option( 'blogname' ) );
	}
}

if ( ! function_exists( 'coursepress_get_instructor_invitation_from_email' ) ) {
	function coursepress_get_instructor_invitation_from_email() {
		return get_option( 'instructor_invitation_from_email', get_option( 'admin_email' ) );
	}
}

if ( ! function_exists( 'cp_get_instructor_invitation_email_subject' ) ) {
	function cp_get_instructor_invitation_email_subject() {
		return get_option( 'instructor_invitation_email_subject', sprintf( __( 'Invitation to be an instructor at %s', 'CP_TD' ), get_option( 'blogname' ) ) );
	}
}

if ( ! function_exists( 'cp_get_instructor_invitation_email' ) ) {
	function cp_get_instructor_invitation_email() {
		$msg = __( 'Hi %1$s,

			Congratulations! You have been invited to become an instructor for the course: %2$s

			Click on the link below to confirm:

			%3$s

			If you haven\'t yet got a username you will need to create one.

				%4$s
				'
		, 'CP_TD' );

		$default_instructor_invitation_email = sprintf(
			$msg,
			'INSTRUCTOR_FIRST_NAME',
			'COURSE_NAME',
			'<a href="CONFIRMATION_LINK">CONFIRMATION_LINK</a>',
			'<a href="WEBSITE_ADDRESS">WEBSITE_ADDRESS</a>'
		);

		return get_option( 'instructor_invitation_email', $default_instructor_invitation_email );
	}
}

if ( ! function_exists( 'cp_admin_notice' ) ) {
	function cp_admin_notice( $notice, $type = 'updated' ) {
		if ( $notice ) {
			echo '<div class="' . esc_attr( $type ) . '"><p>' . esc_html( $notice ) . '</p></div>';
		}
	}
}

if ( ! function_exists( 'cp_get_number_of_instructors' ) ) {
	function cp_get_number_of_instructors() {
		$args = array(
			//'role' => 'instructor',
			'count_total' => false,
			'fields' => array( 'display_name', 'ID' ),
			'who' => '',
		);

		if ( is_multisite() ) {
			$args['blog_id'] = get_current_blog_id();
		}

		$instructors = get_users( $args );

		return count( $instructors );
	}
}

if ( ! function_exists( 'cp_instructors_avatars' ) ) {
	function cp_instructors_avatars( $course_id, $remove_buttons = true, $just_count = false ) {
		global $post_id, $wpdb;

		$content = '';

		$args = array(
			//'role' => 'instructor',
			'meta_key' => 'course_' . $course_id,
			'meta_value' => $course_id,
			'meta_compare' => '',
			'meta_query' => array(),
			'include' => array(),
			'exclude' => array(),
			'orderby' => 'display_name',
			'order' => 'ASC',
			'offset' => '',
			'search' => '',
			'number' => '',
			'count_total' => false,
			'fields' => array( 'display_name', 'ID' ),
			'who' => '',
		);

		if ( is_multisite() ) {
			$args['blog_id'] = get_current_blog_id();
			$args['meta_key'] = $wpdb->prefix . 'course_' . $course_id;
		}

		$instructors = get_users( $args );

		if ( $just_count ) {
			return count( $instructors );
		} else {
			foreach ( $instructors as $instructor ) {
				if ( $remove_buttons ) {
					$content .= '<div class="instructor-avatar-holder" id="instructor_holder_' . $instructor->ID . '"><div class="instructor-status"></div><div class="instructor-remove"><a href="javascript:removeInstructor( ' . $instructor->ID . ' );"><i class="fa fa-times-circle cp-move-icon remove-btn"></i></a></div>' . get_avatar( $instructor->ID, 80 ) . '<span class="instructor-name">' . $instructor->display_name . '</span></div><input type="hidden" id="instructor_' . $instructor->ID . '" name="instructor[]" value="' . $instructor->ID . '" />';
				} else {
					$content .= '<div class="instructor-avatar-holder" id="instructor_holder_' . $instructor->ID . '"><div class="instructor-status"></div><div class="instructor-remove"></div>' . get_avatar( $instructor->ID, 80 ) . '<span class="instructor-name">' . $instructor->display_name . '</span></div><input type="hidden" id="instructor_' . $instructor->ID . '" name="instructor[]" value="' . $instructor->ID . '" />';
				}
			}

			echo $content;
		}
	}
}

if ( ! function_exists( 'cp_instructors_avatars_array' ) ) {
	function cp_instructors_avatars_array( $args = array() ) {
		$content = '<script type="text/javascript" language="JavaScript">
			var instructor_avatars = new Array();';

		$args = array(
			//'role' => 'instructor',
			'meta_key' => ( isset( $args['meta_key'] ) ? $args['meta_key'] : '' ),
			'meta_value' => ( isset( $args['meta_value'] ) ? $args['meta_value'] : '' ),
			'meta_compare' => '',
			'meta_query' => array(),
			'include' => array(),
			'exclude' => array(),
			'orderby' => 'display_name',
			'order' => 'ASC',
			'offset' => '',
			'search' => '',
			'number' => '',
			'count_total' => false,
			'fields' => array( 'display_name', 'ID' ),
			'who' => '',
		);

		if ( is_multisite() ) {
			$args['blog_id'] = get_current_blog_id();
		}

		$instructors = get_users( $args );

		foreach ( $instructors as $instructor ) {
			$content .= 'instructor_avatars[' . $instructor->ID . '] = \'' . str_replace( "'", '"', get_avatar( $instructor->ID, 80, '', $instructor->display_name ) ) . '\';';
		}

		$content .= '</script>';
		echo $content;
	}
}

if ( ! function_exists( 'cp_instructors_pending' ) ) {
	function cp_instructors_pending( $course_id, $has_capability ) {
		$content = '';
		$instructor_invites = get_post_meta( $course_id, 'instructor_invites', true );

		if ( empty( $instructor_invites ) ) {
			return;
		}

		foreach ( $instructor_invites as $instructor ) {

			$remove_button = $has_capability ? '<div class="instructor-remove"><a href="javascript:removePendingInstructor(\'' . $instructor['code'] . '\', ' . $course_id . ' );"><i class="fa fa-times-circle cp-move-icon remove-btn"></i></a></div>' : '';

			$content .=
				'<div class="instructor-avatar-holder pending" id="' . $instructor['code'] . '">' .
				'<div class="instructor-status">PENDING</div>' .
				$remove_button .
				get_avatar( $instructor['email'], 80 ) .
				'<span class="instructor-name">' . $instructor['first_name'] . ' ' . $instructor['last_name'] . '</span>' .
				'</div>';
		}

		echo $content;
	}
}

if ( ! function_exists( 'cp_students_drop_down' ) ) {
	function cp_students_drop_down() {
		$content = '';
		$content .= '<select name="students" data-placeholder="' . __( 'Choose a Student...', 'CP_TD' ) . '" class="chosen-select">';

		$args = array(
			'role' => '',
			'meta_key' => '',
			'meta_value' => '',
			'meta_compare' => '',
			'meta_query' => array(),
			'include' => array(),
			'exclude' => array(),
			'orderby' => 'display_name',
			'order' => 'ASC',
			'offset' => '',
			'search' => '',
			'number' => '',
			'count_total' => false,
			'fields' => array( 'display_name', 'ID' ),
			'who' => '',
		);

		if ( is_multisite() ) {
			$args['blog_id'] = get_current_blog_id();
		}

		$students = get_users( $args );

		$number = 0;
		foreach ( $students as $student ) {
			$number ++;
			$content .= '<option value="' . $student->ID . '">' . $student->display_name . '</option>';
		}
		$content .= '</select>';

		if ( ! $number ) {
			$content = '';
		}

		echo $content;
	}
}

if ( ! function_exists( 'cp_instructors_drop_down' ) ) {
	function cp_instructors_drop_down( $class = '' ) {
		$content = '';
		$content .= '<select name="instructors" id="instructors" data-placeholder="' . __( 'Choose a Course Instructor...', 'CP_TD' ) . '" class="' . $class . '">';

		$args = array(
			//'role' => 'instructor',
			'meta_key' => '',
			'meta_value' => '',
			'meta_compare' => '',
			'meta_query' => array(),
			'include' => array(),
			'exclude' => array(),
			'orderby' => 'display_name',
			'order' => 'ASC',
			'offset' => '',
			'search' => '',
			'class' => $class,
			'number' => '',
			'count_total' => false,
			'fields' => array( 'display_name', 'ID' ),
			'who' => '',
		);

		if ( is_multisite() ) {
			$args['blog_id'] = get_current_blog_id();
		}

		$instructors = get_users( $args );

		$number = 0;
		foreach ( $instructors as $instructor ) {
			$number ++;
			$content .= '<option value="' . $instructor->ID . '">' . $instructor->display_name . '</option>';
		}
		$content .= '</select>';

		if ( ! $number ) {
			$content = '';
		}

		echo $content;
	}

	if ( ! function_exists( 'cp_delete_user_meta_by_key' ) ) {

		function cp_delete_user_meta_by_key( $meta_key ) {
			global $wpdb;

			// if ( $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE meta_key = %s", $meta_key)) ) {
			$legacy = delete_metadata( 'user', 0, $meta_key, '', true );

			$meta_key = $wpdb->prefix . $meta_key;

			if ( $legacy || delete_metadata( 'user', 0, $meta_key, '', true ) ) {
				return true;
			} else {
				return false;
			}
		}
	}
}

if ( ! function_exists( 'cp_cp_get_the_course_excerpt' ) ) {
	function cp_cp_get_the_course_excerpt( $id = false, $length = 55 ) {
		global $post;

		if ( $id != $post->ID ) {
			$post = get_page( $id );
		}

		if ( ! $excerpt = trim( $post->post_excerpt ) ) {
			$excerpt = $post->post_content;
			$excerpt = strip_shortcodes( $excerpt );
			$excerpt = apply_filters( 'the_content', $excerpt );
			$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
			$excerpt = strip_tags( $excerpt );
			$excerpt_length = apply_filters( 'excerpt_length', $length );
			$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[...]' );

			$words = preg_split( "/[\n\r\t ]+/", $excerpt, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );
			if ( count( $words ) > $excerpt_length ) {
				array_pop( $words );
				$excerpt = implode( ' ', $words );
				$excerpt = $excerpt . $excerpt_more;
			} else {
				$excerpt = implode( ' ', $words );
			}
		}

		return $excerpt;
	}
}

if ( ! function_exists( 'cp_get_the_course_excerpt' ) ) {
	function cp_get_the_course_excerpt( $id = false, $length = 55 ) {
		global $post;

		if ( empty( $post ) ) {
			$post = new StdClass;
			$post->ID = 0;
			$post->post_excerpt = '';
			$post->post_content = '';
		}

		$old_post = $post;

		if ( $id != $post->ID ) {
			$post = get_page( $id );
		}

		$excerpt = trim( $post->post_excerpt );

		if ( ! $excerpt ) {
			$excerpt = $post->post_content;
		}

		$excerpt = strip_shortcodes( $excerpt );
		//$excerpt = apply_filters( 'the_content', $excerpt );
		$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
		$excerpt = strip_tags( $excerpt );
		$excerpt_length = apply_filters( 'excerpt_length', $length );
		$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[...]' );

		$words = preg_split( "/[\n\r\t ]+/", $excerpt, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );
		if ( count( $words ) > $excerpt_length ) {
			array_pop( $words );
			$excerpt = implode( ' ', $words );
			$excerpt = $excerpt . $excerpt_more;
		} else {
			$excerpt = implode( ' ', $words );
		}

		$post = $old_post;

		return $excerpt;
	}
}

if ( ! function_exists( 'cp_get_number_of_days_between_dates' ) ) {
	function cp_get_number_of_days_between_dates( $start_date, $end_date ) {

		$time_start = strtotime( $start_date );
		$time_end = strtotime( $end_date );

		$time_diff = abs( $time_end - $time_start );

		$day_num = $time_diff / 86400;  // 86400 seconds in one day
		$day_num = intval( $day_num );

		return $day_num;
	}

	if ( ! function_exists( 'cp_register_module' ) ) {
		// To do.
	}
}

if ( ! function_exists( 'cp_object_encode' ) ) {
	function cp_object_encode( $object ) {
		$encoded = json_encode( $object, JSON_FORCE_OBJECT | JSON_HEX_QUOT | JSON_HEX_APOS );
		$encoded = str_replace( '"', '&quot;', $encoded );
		$encoded = str_replace( "'", '&apos;', $encoded );

		return $encoded;
	}
}

if ( ! function_exists( 'cp_object_decode' ) ) {
	function cp_object_decode( $string, $class = 'stdClass' ) {
		$object = str_replace( '&quot;', '"', $string );
		$object = str_replace( '&apos;', "'", $object );
		$object = json_decode( $object );

		// Convert to correct Class.
		return unserialize( sprintf(
			'O:%d:"%s"%s', strlen( $class ), $class, strstr( strstr( serialize( $object ), '"' ), ':' )
		) );
	}
}

if ( ! function_exists( 'cp_sp2nbsp' ) ) {
	function cp_sp2nbsp( $string ) {
		return str_replace( ' ', '&nbsp;', $string );
	}

	if ( ! function_exists( 'cp_get_userdatabynicename' ) ) :
		function cp_get_userdatabynicename( $user_nicename ) {
			global $wpdb;
			$user_nicename = sanitize_title( $user_nicename );

			if ( empty( $user_nicename ) ) {
				return false;
			}

			$args = array(
				'search' => $user_nicename,
				'search_columns' => array( 'user_nicename' ),
				'number' => '1',
				'fields' => array( 'id' ),
			);

			$users = new WP_User_Query( $args );
			$user_id = ! empty( $users->results ) ? array_pop( $users->results ) : false;

			$user = ! empty( $user_id ) ? new WP_User( $user_id->id ) : false;

			if ( empty( $user ) ) {
				return false;
			}

			$metavalues = get_user_meta( $user->ID );

			if ( $metavalues ) {
				foreach ( $metavalues as $key => $meta ) {

					$value = array_pop( $meta );
					$value = maybe_unserialize( $value );
					$user->{$key} = $value;

					// We need to set user_level from meta, not row
					if ( $wpdb->prefix . 'user_level' == $key ) {
						$user->user_level = $value;
					}
				}
			}

			// For backwards compat.
			if ( isset( $user->first_name ) ) {
				$user->user_firstname = $user->first_name;
			}
			if ( isset( $user->last_name ) ) {
				$user->user_lastname = $user->last_name;
			}
			if ( isset( $user->description ) ) {
				$user->user_description = $user->description;
			}

			return $user;
		}

endif;

}

if ( ! function_exists( 'cp_get_count_of_users' ) ) {
	function cp_get_count_of_users( $role = '' ) {
		$result = count_users();
		if ( ! $role ) {
			return $result['total_users'];
		} else {
			foreach ( $result['avail_roles'] as $roles => $count ) {
				if ( $roles == $role ) {
					return $count;
				}
			}
		}

		return 0;
	}
}

if ( ! function_exists( 'cp_curPageURL' ) ) {
	// @codingStandardsIgnoreStart Ignore CamelCase here...
	function cp_curPageURL() {
	// @codingStandardsIgnoreEnd
		$page_url = 'http';
		if ( isset( $_SERVER['HTTPS'] ) && 'on' == $_SERVER['HTTPS'] ) {
			$page_url .= 's';
		}
		$page_url .= '://';
		if ( isset( $_SERVER['SERVER_PORT'] ) && '80' != $_SERVER['SERVER_PORT'] ) {
			$page_url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		} else {
			$page_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}

		return $page_url;
	}
}

if ( ! function_exists( 'cp_natkrsort' ) ) {
	function cp_natkrsort( $array ) {
		$keys = array_keys( $array );
		natsort( $keys );

		foreach ( $keys as $k ) {
			$new_array[ $k ] = $array[ $k ];
		}

		$new_array = array_reverse( $new_array, true );

		return $new_array;
	}

	if ( ! function_exists( 'cp_register_module' ) ) {
		function cp_register_module( $module_name, $class_name, $section ) {
			global $coursepress_modules, $coursepress_modules_labels, $coursepress_modules_descriptions, $coursepress_modules_ordered;

			if ( ! is_array( $coursepress_modules ) ) {
				$coursepress_modules = array();
			}

			if ( class_exists( $class_name ) ) {
				$class = new $class_name();
				$coursepress_modules_labels[ $module_name ] = $class->label;
				$coursepress_modules_descriptions[ $module_name ] = $class->description;
				$coursepress_modules[ $section ][ $module_name ] = $class_name;
				$coursepress_modules_ordered[ $section ][ $class->order ] = $class_name;
				ksort( $coursepress_modules_ordered[ $section ] );
			} else {
				return false;
			}
		}
	}

	if ( ! function_exists( 'cp_register_front_page_module' ) ) {
		function cp_register_front_page_module( $module_name, $class_name, $section ) {
			global $coursepress_front_page_modules, $coursepress_front_page_modules_labels, $coursepress_front_page_modules_descriptions, $coursepress_front_page_modules_ordered;

			if ( ! is_array( $coursepress_front_page_modules ) ) {
				$coursepress_front_page_modules = array();
			}

			if ( class_exists( $class_name ) ) {
				$class = new $class_name();
				$coursepress_front_page_modules_labels[ $module_name ] = $class->label;
				$coursepress_front_page_modules_descriptions[ $module_name ] = $class->description;
				$coursepress_front_page_modules[ $section ][ $module_name ] = $class_name;
				$coursepress_front_page_modules_ordered[ $section ][ $class->order ] = $class_name;
			} else {
				return false;
			}
		}
	}


	if ( ! function_exists( 'cp_write_log' ) ) {
		function cp_write_log( $message, $echo_file = false ) {
			$trace = defined( 'DEBUG_BACKTRACE_IGNORE_ARGS' ) ? debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ) : debug_backtrace( false );
			$exception = new Exception();
			$debug = array_shift( $trace );
			$caller = array_shift( $trace );
			$exception = $exception->getTrace();
			$callee = array_shift( $exception );

			if ( true === WP_DEBUG ) {
				if ( is_array( $message ) || is_object( $message ) ) {
					$class = isset( $caller['class'] ) ? $caller['class'] . '[' . $callee['line'] . '] ' : '';
					if ( $echo_file ) {
						error_log( $class . print_r( $message, true ) . 'In ' . $callee['file'] . ' on line ' . $callee['line'] );
					} else {
						error_log( $class . print_r( $message, true ) );
					}
				} else {
					$class = isset( $caller['class'] ) ? $caller['class'] . '[' . $callee['line'] . ']: ' : '';
					if ( $echo_file ) {
						error_log( $class . $message . ' In ' . $callee['file'] . ' on line ' . $callee['line'] );
					} else {
						error_log( $class . $message );
					}
				}
			}
		}
	}

	if ( ! function_exists( 'cp_wp_get_image_extensions' ) ) {
		function cp_wp_get_image_extensions() {
			return array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp', 'tif', 'tiff', 'ico' );
		}
	}

	if ( ! function_exists( 'cp_is_plugin_network_active' ) ) {
		function cp_is_plugin_network_active( $plugin_file ) {
			if ( is_multisite() ) {
				return ( array_key_exists( $plugin_file, maybe_unserialize( get_site_option( 'active_sitewide_plugins' ) ) ) );
			}
		}
	}
}

if ( ! function_exists( 'cp_get_terms_dropdown' ) ) {
	function cp_get_terms_dropdown( $taxonomies, $args ) {
		$myterms = get_terms( $taxonomies, $args );
		$output = '<select>';
		foreach ( $myterms as $term ) {
			$root_url = get_bloginfo( 'url' );
			$term_taxonomy = $term->taxonomy;
			$term_slug = $term->slug;
			$term_name = $term->name;
			$link = $root_url . '/' . $term_taxonomy . '/' . $term_slug;
			$output .= '<option value="' . $link . '">' . $term_name . '</option>';
		}
		$output .= '</select>';

		return $output;
	}
}

if ( ! function_exists( 'cp_in_array_r' ) ) {
	function cp_in_array_r( $needle, $haystack, $strict = false ) {
		foreach ( $haystack as $item ) {
			if ( ( $strict ? $item === $needle : $item == $needle ) || ( is_array( $item ) && cp_in_array_r( $needle, $item, $strict ) ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'cp_replace_img_src' ) ) {
	function cp_replace_img_src( $original_img_tag, $new_src_url ) {
		$doc = new DOMDocument();
		$doc->loadHTML( $original_img_tag );

		$tags = $doc->getElementsByTagName( 'img' );
		if ( count( $tags ) > 0 ) {
			$tag = $tags->item( 0 );
			$tag->setAttribute( 'src', $new_src_url );

			return $doc->saveXML( $tag );
		}

		return false;
	}
}

if ( ! function_exists( 'cp_callback_img' ) ) {
	function cp_callback_img( $match ) {
		list(, $img, $src ) = $match;
		$new_src = str_replace( '../wp-content', WP_CONTENT_URL, $src );

		return "$img=\"$new_src\" ";
	}
}

if ( ! function_exists( 'cp_callback_link' ) ) {
	function cp_callback_link( $match ) {
		$new_url = str_replace( '../wp-content', WP_CONTENT_URL, $match[0] );

		return $new_url;
	}
}

if ( ! function_exists( 'cp_user_has_role' ) ) {
	function cp_user_has_role( $check_role, $user_id = null ) {
		// Get user by ID, else get current user
		if ( $user_id ) {
			$user = get_userdata( $user_id );
		} else {
			$user = wp_get_current_user();
		}

		// No user found, return
		if ( empty( $user ) ) {
			return false;
		}

		// Append administrator to roles, if necessary
		/* if ( ! in_array( 'administrator',$roles ) ) */
		$roles[] = '';

		// Loop through user roles
		foreach ( $user->roles as $role ) {
			// Does user have role
			if ( $role == $check_role ) {
				return true;
			}
		}

		// User not in roles
		return false;
	}

	/**
	 * Numeric pagination
	 */
	if ( ! function_exists( 'cp_numeric_posts_nav' ) ) {

		function cp_numeric_posts_nav( $navigation_id = '' ) {

			if ( is_singular() ) {
				return;
			}

			global $wp_query, $paged;
			/** Stop execution if there's only 1 page */
			if ( $wp_query->max_num_pages <= 1 ) {
				return;
			}

			$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;

			$max = intval( $wp_query->max_num_pages );

			/**	Add current page to the array */
			if ( $paged >= 1 ) {
				$links[] = $paged;
			}

			/**	Add the pages around the current page to the array */
			if ( $paged >= 3 ) {
				$links[] = $paged - 1;
				$links[] = $paged - 2;
			}

			if ( ( $paged + 2 ) <= $max ) {
				$links[] = $paged + 2;
				$links[] = $paged + 1;
			}

			if ( $navigation_id ) {
				$id = 'id="' . $navigation_id . '"';
			} else {
				$id = '';
			}

			echo '<div class="navigation" ' . $id . '><ul>' . "\n";

			/**	Previous Post Link */
			if ( get_previous_posts_link() ) {
				printf( '<li>%s</li>' . "\n", get_previous_posts_link( '<span class="meta-nav">&larr;</span>' ) );
			}

			/**	Link to first page, plus ellipses if necessary */
			if ( ! in_array( 1, $links ) ) {
				$class = 1 == $paged ? ' class="active"' : '';

				printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

				if ( ! in_array( 2, $links ) ) {
					echo '<li>…</li>';
				}
			}

			/**	Link to current page, plus 2 pages in either direction if necessary */
			sort( $links );
			foreach ( (array) $links as $link ) {
				$class = $paged == $link ? ' class="active"' : '';
				printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
			}

			/**	Link to last page, plus ellipses if necessary */
			if ( ! in_array( $max, $links ) ) {
				if ( ! in_array( $max - 1, $links ) ) {
					echo '<li>…</li>' . "\n";
				}

				$class = $paged == $max ? ' class="active"' : '';
				printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
			}

			/**	Next Post Link */
			if ( get_next_posts_link() ) {
				printf( '<li>%s</li>' . "\n", get_next_posts_link( '<span class="meta-nav">&rarr;</span>' ) );
			}

			echo '</ul></div>' . "\n";
		}
	}
}

if ( ! function_exists( 'cp_default_args' ) ) {
	function cp_default_args( $pairs, $atts, $shortcode = '' ) {
		$atts = (array) $atts;
		$out = array();
		foreach ( $pairs as $name => $default ) {
			if ( array_key_exists( $name, $atts ) ) {
				$out[ $name ] = $atts[ $name ];
			} else {
				$out[ $name ] = $default;
			}
		}

		return $out;
	}
}

if ( ! function_exists( 'cp_length' ) ) {
	function cp_length( $text, $excerpt_length ) {
		/*
		$text = strip_shortcodes( $text );
		//$text = apply_filters( 'the_content', $text );
		$excerpt_more = '...';
		$text = str_replace( ']]>', ']]&gt;', $text );
		$text = strip_tags( $text );
		$words = preg_split( "/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );
		if ( count( $words ) > $excerpt_length ) {
		array_pop( $words );
		$text = implode( ' ', $words );
		$text = $text . $excerpt_more;
		} else {
		$text = implode( ' ', $words );
		}
		*/
		$text = truncateHtml( $text, $excerpt_length );

		return $text;
	}
}

if ( ! function_exists( 'truncateHtml' ) ) {
	// @codingStandardsIgnoreStart Ignore CamelCase here...
	function truncateHtml( $text, $length = 100, $ending = '...', $exact = false, $consider_html = true ) {
	// @codingStandardsIgnoreEnd
		if ( $consider_html ) {
			// if the plain text is shorter than the maximum length, return the whole text
			if ( strlen( preg_replace( '/<.*?>/', '', $text ) ) <= $length ) {
				return $text;
			}

			// Splits all html-tags to scanable lines.
			preg_match_all( '/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER );
			$total_length = strlen( $ending );
			$open_tags = array();
			$truncate = '';

			foreach ( $lines as $line_matchings ) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if ( ! empty( $line_matchings[1] ) ) {
					// if it's an "empty element" with or without xhtml-conform closing slash
					if ( preg_match( '/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1] ) ) {
						// do nothing
						// if tag is a closing tag
					} else if ( preg_match( '/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings ) ) {
						// delete tag from $open_tags list
						$pos = array_search( $tag_matchings[1], $open_tags );
						if ( false !== $pos ) {
							unset( $open_tags[ $pos ] );
						}
						// if tag is an opening tag
					} else if ( preg_match( '/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings ) ) {
						// add tag to the beginning of $open_tags list
						array_unshift( $open_tags, strtolower( $tag_matchings[1] ) );
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen( preg_replace( '/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2] ) );
				if ( $total_length + $content_length > $length ) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if ( preg_match_all( '/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE ) ) {
						// Calculate the real length of all entities in the legal range.
						foreach ( $entities[0] as $entity ) {
							if ( $entity[1] + 1 - $entities_length <= $left ) {
								$left --;
								$entities_length += strlen( $entity[0] );
							} else {
								// No more characters left.
								break;
							}
						}
					}
					$truncate .= substr( $line_matchings[2], 0, $left + $entities_length );
					// Maximum lenght is reached, so get off the loop.
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				// If the maximum length is reached, get off the loop.
				if ( $total_length >= $length ) {
					break;
				}
			}
		} else {
			if ( strlen( $text ) <= $length ) {
				return $text;
			} else {
				$truncate = substr( $text, 0, $length - strlen( $ending ) );
			}
		}

		// if the words shouldn't be cut in the middle...
		if ( ! $exact ) {
			// ...search the last occurance of a space...
			$spacepos = strrpos( $truncate, ' ' );
			if ( isset( $spacepos ) ) {
				// ...and cut the text in this position
				$truncate = substr( $truncate, 0, $spacepos );
			}
		}
		// add the defined ending to the text
		$truncate .= $ending;
		if ( $consider_html ) {
			// close all unclosed html-tags
			foreach ( $open_tags as $tag ) {
				$truncate .= '</' . $tag . '>';
			}
		}

		return $truncate;
	}
}

if ( ! function_exists( 'cp_do_attachment_caption' ) ) {
	function cp_do_attachment_caption( $data ) {

		if ( empty( $data->image_url ) && empty( $data->video_url ) ) {
			return '';
		}

		$media_data = array();
		$caption_source = ( isset( $data->caption_field ) ? $data->caption_field : 'media' );

		if ( ! empty( $data->image_url ) ) {
			$media_data['id'] = $data->attachment_id;
		}
		if ( ! empty( $data->video_url ) ) {
			$media_data['id'] = $data->attachment_id;
		}

		if ( $media_data['id'] ) {
			// Alt - always add alt!
			$meta = get_post_meta( $media_data['id'] ); // Get post meta by ID
			if ( ! empty( $meta['_wp_attachment_image_alt'] ) ) {
				$media_data['alt'] = $meta['_wp_attachment_image_alt'][0];
			} else {
				$media_data['alt'] = '';
			}

			// Width - used for caption shortcode
			$attachment = get_post( $media_data['id'] );
			$meta = wp_get_attachment_metadata( $media_data['id'] );
			$media_data['width'] = $meta['width'];

			if ( 'media' == $caption_source ) {
				$media_data['caption'] = $attachment->post_excerpt;
			} else {
				$media_data['caption'] = ! empty( $data->caption_custom_text ) ? $data->caption_custom_text : '';
			}
		} else {

			// If the user did happen to put something in the custom caption box,
			// use this for alt. Worst case scenario is an empty alt tag.
			if ( ! empty( $data->caption_custom_text ) ) {
				$media_data['alt'] = $data->caption_custom_text;
			} else {
				$media_data['alt'] = '';
			}

			global $content_width;
			if ( ! empty( $content_width ) ) {
				$media_data['width'] = $content_width;
			} else {
				// Default to media setting for large images if its not an attachment
				$media_data['width'] = get_option( 'large_size_w' );
			}

			// Get the custom caption text
			$media_data['caption'] = ! empty( $data->caption_custom_text ) ? $data->caption_custom_text : '';
		}

		$html = '';

		// Called from Image module.
		if ( ! empty( $data->image_url ) ) {

			if ( 'yes' == $data->show_media_caption ) {
				$attachment_id = '';
				if ( $media_data['id'] ) {
					$attachment_id = ' id="attachment_' . $media_data['id'] . '"';
				}

				$html .= '<div class="image_holder">';
				$img = '<img src="' . $data->image_url . '" alt="' . $media_data['alt'] . '" />';
				$html .= do_shortcode( '[caption width="' . $media_data['width'] . '"' . $attachment_id . ']' . $img . ' ' . $media_data['caption'] . '[/caption]' );
				$html .= '</div>';
			} else {
				$html .= '<div class="image_holder">';
				$html .= '<img src="' . $data->image_url . '" alt="' . $media_data['alt'] . '" />';
				$html .= '</div>';
			}
		}

		// Called from Video module.
		if ( ! empty( $data->video_url ) ) {
			$video_extension = pathinfo( $data->video_url, PATHINFO_EXTENSION );

			if ( isset( $data->hide_related_media ) && 'yes' == $data->hide_related_media ) {
				add_filter( 'oembed_result', 'cp_remove_related_videos', 10, 3 );
			}

			$video = '';
			if ( ! empty( $video_extension ) ) {
				// It's file, most likely on the server.
				$attr = array(
					'src' => $data->video_url,
					//'width' => $data->player_width,
					//'height' => 550//$data->player_height,
				);
				$video = wp_video_shortcode( $attr );
			} else {
				$embed_args = array(
					//'width' => $data->player_width,
					//'height' => 550
				);

				$video = wp_oembed_get( $data->video_url, $embed_args );
				if ( ! $video ) {
					$video = apply_filters( 'the_content', '[embed]' . $data->video_url . '[/embed]' );
				}
			}

			if ( 'yes' == $data->show_media_caption ) {
				$attachment_id = '';
				if ( $media_data['id'] ) {
					$attachment_id = ' id="attachment_' . $media_data['id'] . '"';
				}

				$html .= '<div class="video_holder">';
				$html .= '<figure ' . $attachment_id . ' class="wp-caption" style="width: ' . $media_data['width'] . 'px;">';
				$html .= '<div class="video_player">';
				$html .= $video;
				$html .= '</div>';
				if ( ! empty( $media_data['caption'] ) ) {
					$html .= '<figcaption class="wp-caption-text">' . $media_data['caption'] . '</figcaption>';
				}
				$html .= '</figure>';
				$html .= '</div>';
			} else {
				$html .= '<div class="video_player">';
				$html .= $video;
				$html .= '</div>';
			}
		}

		return $html;
	}
}

if ( ! function_exists( 'cp_remove_related_videos' ) ) {
	function cp_remove_related_videos( $html, $url, $args ) {
		$newargs = $args;
		$newargs['rel'] = 0;

		// build the query url
		$parameters = http_build_query( $newargs );

		// YouTube
		$html = str_replace( 'feature=oembed', 'feature=oembed&' . $parameters, $html );

		return $html;
	}
}

if ( ! function_exists( 'cp_minify_output' ) ) {
	function cp_minify_output( $buffer ) {
		$search = array(
			'/\>[^\S ]+/s', // Strip whitespaces after tags, except space.
			'/[^\S ]+\</s', // Strip whitespaces before tags, except space.
			'/(\s)+/s',  // Shorten multiple whitespace sequences.
		);
		$replace = array(
			'>',
			'<',
			'\\1',
		);
		$buffer = preg_replace( $search, $replace, $buffer );

		return $buffer;
	}
}

if ( ! function_exists( 'cp_get_file_size' ) ) {
	function cp_get_file_size( $url, $human = true ) {
		$bytes = 0;

		// If its not a path... its probably a URL.
		if ( ! preg_match( '/^\//', $url ) ) {
			$header = wp_remote_head( $url );
			if ( ! is_wp_error( $header ) ) {
				$bytes = $header['headers']['content-length'];
			} else {
				$bytes = 0;
			}
		} else {
			try {
				$bytes = filesize( $url );
				$bytes = ! empty( $bytes ) ? $bytes : 0;
			} catch ( Exception $e ) {
				$bytes = 0;
			}
		}

		if ( $bytes = 0 ) {
			$human = false;
		}

		return $human ? cp_format_file_size( $bytes ) : $bytes;
	}
}

if ( ! function_exists( 'cp_format_file_size' ) ) {
	function cp_format_file_size( $bytes ) {
		if ( $bytes >= 1073741824 ) {
			$bytes = number_format( $bytes / 1073741824, 2 ) . ' GB';
		} elseif ( $bytes >= 1048576 ) {
			$bytes = number_format( $bytes / 1048576, 2 ) . ' MB';
		} elseif ( $bytes >= 1024 ) {
			$bytes = number_format( $bytes / 1024, 2 ) . ' KB';
		} elseif ( $bytes > 1 ) {
			$bytes = $bytes . ' bytes';
		} elseif ( 1 == $bytes ) {
			$bytes = $bytes . ' byte';
		} else {
			$bytes = '0 bytes';
		}

		return $bytes;
	}

	/**
	 * flush_rewrite_rules() wrapper for CoursePress.
	 *
	 * Used to wrap flush_rewrite_rules() so that rewrite flushes can
	 * be prevented in given environments.
	 *
	 * E.g. If we've got CampusPress/Edublogs then this method will have
	 * an early exit.
	 *
	 * @since 1.2.1
	 */
}

if ( ! function_exists( 'cp_flush_rewrite_rules' ) ) {
	function cp_flush_rewrite_rules() {

		if ( CoursePress_Capabilities::is_campus() ) {
			return;
		}

		flush_rewrite_rules();
	}
}

if ( ! function_exists( 'cp_search_array' ) ) {
	function cp_search_array( $array, $key, $value ) {
		$results = array();

		if ( is_array( $array ) ) {
			if ( isset( $array[ $key ] ) && $array[ $key ] == $value ) {
				$results[] = $array;
			}

			foreach ( $array as $subarray ) {
				$results = array_merge( $results, cp_search_array( $subarray, $key, $value ) );
			}
		}

		return $results;
	}
	// fix for recursive serialized objects.
}

if ( ! function_exists( 'cp_deep_unserialize' ) ) {
	function cp_deep_unserialize( $serialized_object ) {

		$new_array = maybe_unserialize( $serialized_object );

		if ( is_serialized( $new_array ) ) {
			$new_array = cp_deep_unserialize( $new_array );
		}

		return $new_array;
	}
}

if ( ! function_exists( 'cp_fix_module_metas' ) ) {
	function cp_fix_module_metas( $module_id, $update = false ) {
		$post_metas = get_post_meta( $module_id );

		// Clear indication that its broken
		if ( isset( $post_metas['module_type'] ) && is_array( $post_metas['module_type'] ) ) {

			// Clean up
			foreach ( $post_metas as $meta_key => $meta_value ) {
				$post_metas[ $meta_key ] = $meta_value[0];
			}

			// Update
			foreach ( $post_metas as $meta_key => $meta_value ) {
				delete_post_meta( $module_id, $meta_key );
				update_post_meta( $module_id, $meta_key, $meta_value );
			}
		}

		return $post_metas;
	}
}

if ( ! function_exists( 'cp_is_true' ) ) {

	/**
	 * Evaluate if the specified value translates to boolean TRUE.
	 *
	 * True:
	 * - Boolean true
	 * - Number other than 0
	 * - Strings 'yes', 'on', 'true'
	 *
	 * @since  2.0.0
	 * @param  mixed $value Value to evaluate.
	 * @return bool
	 */
	function cp_is_true( $value ) {
		if ( ! $value ) {
			// Handles: null, 0, '0', false, ''.
			return false;
		}

		if ( true === $value ) {
			return true;
		}

		if ( ! is_scalar( $value ) ) {
			// Arrays, objects, etc. always evaluate to false.
			return false;
		}

		if ( is_numeric( $value ) ) {
			// A number other than 0 is true.
			return true;
		}

		$value = strtolower( (string) $value );
		if ( 'on' == $value || 'yes' == $value || 'true' == $value ) {
			return true;
		}

		return false;
	}
}
