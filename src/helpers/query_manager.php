<?php namespace Lti\Sitemap\Helpers;

class Query_Manager {

	private $columns = array();
	private $tables = array();
	private $where = array();
	private $join = array();
	private $groupBy = array();
	private $orderBy = array();
	public static $RESULTSET_LIMIT = 5000;

	public function select( Array $columns ) {
		$this->columns = array_unique( array_merge( $this->columns, $columns ) );
	}

	public function from( Array $tables ) {
		$this->tables = array_unique( array_merge( $this->tables, $tables ) );
	}

	public function where( $column, $comparator, $value ) {
		if ( is_string( $value ) ) {
			$value = sprintf( "'%s'", esc_sql( $value ) );
		}
		$this->where[] = array( " AND", $column, $comparator, $value );
	}

	public function whereIn( $column, $comparator, Array $values ) {
		$this->where[] = array(
			" AND",
			$column,
			"IN",
			"('" . implode( "','", array_map( 'esc_sql', $values ) ) . "')"
		);
	}

	public function groupBy( Array $columns ) {
		$this->groupBy = array_unique( array_merge( $this->groupBy, $columns ) );
	}

	public function orderBy( Array $columns ) {
		$this->orderBy = array_unique( array_merge( $this->orderBy, $columns ) );
	}

	public function join( $table, $column, $comparator, $value ) {
		$this->join[] = array( $table, $column, $comparator, $value );
	}


	public function build() {
		if ( empty( $this->columns ) ) {
			$this->columns = array( "*" );
		}
		$query = "SELECT " . implode( ',', $this->columns );
		$query .= " FROM " . implode( ',', $this->tables );
		if ( ! empty( $this->join ) ) {
			foreach ( $this->join as $join ) {
				$query .= " JOIN " . $join[0] . " ON (" . $join[1] . $join[2] . $join[3] . ')';
			}
			if ( ! empty( $this->where ) ) {
				foreach ( $this->where as $where ) {
					$query .= implode( ' ', $where );
				}
			}
		} else if ( ! empty( $this->where ) ) {
			$first = array_shift( $this->where );
			$query .= " WHERE " . $first[1] . $first[2] . $first[3];
			if ( ! empty( $this->where ) ) {
				foreach ( $this->where as $where ) {
					$query .= implode( ' ', $where );
				}
			}
		}

		if ( ! empty( $this->groupBy ) ) {
			$query .= " GROUP BY " . implode( ',', $this->groupBy );
		}

		if ( ! empty( $this->orderBy ) ) {
			$query .= " ORDER BY " . implode( ',', $this->orderBy );
		}

		$query .= " LIMIT " . self::$RESULTSET_LIMIT;

		return $query;

	}

	public function printQuery( $sql_query ) {
		$sql_query = preg_replace_callback( "#( FROM | WHERE | AND | OR | SET | VALUES\s?| (LEFT|RIGHT|OUTER|INNER|FULL) JOIN | JOIN | HAVING | ORDER BY | GROUP BY )#i",
			create_function( '$matches', "return \"<br/>\".\$matches[0];" ), $sql_query );

		return str_repeat( "=", 40 ) . "<br/>" . $sql_query . "<br/>" . str_repeat( "=", 40 ) . "<br/>";
	}
}

class Plugin_Queries {

	/**
	 * @var \wpdb
	 */
	private $wpdb;

	/**
	 * @var Query_Manager The query;
	 */
	private $q;

	private static $instance;

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->q    = self::getQueryManager();
	}

	public static function getQueryManager() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Query_Manager();
		}

		return self::$instance;
	}

	private function flush() {
		self::$instance = $this->q = new Query_Manager();
	}

	public function get_results( $debug = false ) {

		$query = $this->q->build();
		$this->flush();

		if ( $debug === true ) {
			print_r( $this->q->printQuery( $query ) );

			return false;
		}

		return $this->wpdb->get_results( $query );
	}

	public function get_posts_info_month() {
		$this->q->select( array(
			'YEAR(p.post_date_gmt)    AS `year`',
			'MONTH(p.post_date_gmt)   AS `month`',
			'MAX(p.post_modified_gmt) as `lastmod`'
		) );
		$this->q->from( array( $this->wpdb->posts . ' p' ) );
		$this->q->where( 'p.post_password', '=', '' );
		$this->q->where( 'p.post_type', '=', 'post' );
		$this->q->where( 'p.post_status', '=', 'publish' );
		$this->q->groupBy( array( 'YEAR(p.post_date_gmt)', 'MONTH(p.post_date_gmt)' ) );
		$this->q->orderBy( array( 'p.post_date_gmt DESC' ) );

		return $this->get_results();
	}

	public function get_posts_info_year() {
		$this->q->select( array(
			'YEAR(p.post_date_gmt)    AS `year`',
			'MAX(p.post_modified_gmt) as `lastmod`'
		) );
		$this->q->from( array( $this->wpdb->posts . ' p' ) );
		$this->q->where( 'p.post_password', '=', '' );
		$this->q->where( 'p.post_type', '=', 'post' );
		$this->q->where( 'p.post_status', '=', 'publish' );
		$this->q->groupBy( array( 'YEAR(p.post_date_gmt)' ) );
		$this->q->orderBy( array( 'p.post_date_gmt DESC' ) );

		return $this->get_results();
	}

	public function get_posts_info() {
		$this->q->select( array(
			'MAX(p.post_modified_gmt) as `lastmod`'
		) );
		$this->q->from( array( $this->wpdb->posts . ' p' ) );
		$this->q->where( 'p.post_password', '=', '' );
		$this->q->where( 'p.post_type', '=', 'post' );
		$this->q->where( 'p.post_status', '=', 'publish' );
		$this->q->groupBy( array( 'p.post_date_gmt' ) );

		return $this->get_results();
	}

	public function get_posts( $month = null, $year = null ) {
		$this->q->select( array( 'p.ID', 'p.post_modified_gmt as `lastmod`' ) );
		$this->q->from( array( $this->wpdb->posts . ' p' ) );
		$this->q->where( 'p.post_password', '=', '' );
		$this->q->where( 'p.post_type', '=', 'post' );
		$this->q->where( 'p.post_status', '=', 'publish' );
		if ( ! is_null( $year ) ) {
			$this->q->where( 'YEAR(p.post_date_gmt)', '=', $year );
		}
		if ( ! is_null( $month ) ) {
			$this->q->where( 'MONTH(p.post_date_gmt)', '=', $month );

		}
		$this->q->orderBy( array( 'p.post_date_gmt DESC' ) );

		return $this->get_results();
	}

	public function get_posts_attachment_images() {
		$this->q->select( array(
			'p2.id as post_id',
			'p1.post_content as license',
			'p1.post_title as title',
			'p1.post_excerpt as caption',
			'p1.guid as url'
		) );
		$this->q->from( array( $this->wpdb->posts . ' p1' ) );
		$this->q->join( $this->wpdb->posts . ' p2', 'p1.post_parent', '=', 'p2.ID' );
		$this->q->where( 'p2.post_type', '=', 'post' );
		$this->q->where( 'p2.post_status', '=', 'publish' );
		$this->q->where( 'p1.post_type', '=', 'attachment' );
		$this->q->orderBy( array( 'p2.post_date_gmt DESC' ) );

		return $this->get_results();
	}

	public function get_posts_thumbnail_images() {
		$this->q->select( array(
			'post_id',
			'p.guid as url',
			'p.post_content as license',
			'p.post_title as title',
			'p.post_excerpt as caption'
		) );
		$this->q->from( array( $this->wpdb->postmeta . ' pm' ) );
		$this->q->join( $this->wpdb->posts . ' p', 'pm.meta_value', '=', 'p.ID' );
		$this->q->where( 'pm.meta_key', '=', '_thumbnail_id' );
		$this->q->where( 'meta_value', '>', 0 );
		$this->q->where( 'post_parent', '>', 0 );
		$this->q->orderBy( array( 'p.post_date_gmt DESC' ) );

		return $this->get_results();
	}


	public function get_pages() {
		$this->q->select( array( 'p.ID', 'p.post_modified_gmt as `lastmod`' ) );
		$this->q->from( array( $this->wpdb->posts . ' p' ) );
		$this->q->where( 'p.post_password', '=', '' );
		$this->q->where( 'p.post_type', '=', 'page' );
		$this->q->where( 'p.post_status', '=', 'publish' );
		$this->q->orderBy( array( 'p.post_date_gmt DESC' ) );

		return $this->get_results();
	}

	public function get_pages_info() {
		$this->q->select( array( 'COUNT(p.ID)', 'MAX(p.post_modified_gmt) as `lastmod`' ) );
		$this->q->from( array( $this->wpdb->posts . ' p' ) );
		$this->q->where( 'p.post_password', '=', '' );
		$this->q->where( 'p.post_type', '=', 'page' );
		$this->q->where( 'p.post_status', '=', 'publish' );
		$this->q->groupBy( array( 'p.ID' ) );

		return $this->get_results();
	}

	public function get_authors_info( $supported_post_types ) {

		$this->q->select( array( 'COUNT(u.ID)', 'MAX(p.post_modified_gmt) as `lastmod`' ) );
		$this->q->from( array( $this->wpdb->posts . ' p' ) );
		$this->q->join( $this->wpdb->users . ' u', 'u.ID', '=', 'post_author' );
		$this->q->where( 'p.post_password', '=', '' );
		$this->q->whereIn( 'p.post_type', 'IN', $supported_post_types );
		$this->q->where( 'p.post_status', '=', 'publish' );
		$this->q->groupBy( array( 'u.ID' ) );

		return $this->get_results();
	}

	public function get_authors( $supported_post_types ) {

		$this->q->select( array( 'u.ID', 'MAX(p.post_modified_gmt) as `lastmod`' ) );
		$this->q->from( array( $this->wpdb->posts . ' p' ) );
		$this->q->join( $this->wpdb->users . ' u', 'u.ID', '=', 'post_author' );
		$this->q->where( 'p.post_password', '=', '' );
		$this->q->whereIn( 'p.post_type', 'IN', $supported_post_types );
		$this->q->where( 'p.post_status', '=', 'publish' );
		$this->q->groupBy( array( 'u.ID' ) );

		return $this->get_results();
	}
}