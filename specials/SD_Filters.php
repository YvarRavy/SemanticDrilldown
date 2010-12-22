<?php
/**
 * Shows list of all filters on the site.
 *
 * @author Yaron Koren
 */

if ( !defined( 'MEDIAWIKI' ) ) die();

class SDFilters extends SpecialPage {

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct( 'Filters' );
		wfLoadExtensionMessages( 'SemanticDrilldown' );
	}

	function execute( $par ) {
		$this->setHeaders();
		list( $limit, $offset ) = wfCheckLimits();
		$rep = new FiltersPage();
		return $rep->doQuery( $offset, $limit );
	}
}

class FiltersPage extends QueryPage {
	function getName() {
		return "Filters";
	}

	function isExpensive() { return false; }

	function isSyndicated() { return false; }

	function getPageHeader() {
		$header = '<p>' . wfMsg( 'sd_filters_docu' ) . "</p><br />\n";
		return $header;
	}

	function getPageFooter() {
	}

	function getSQL() {
		$filter_ns = SD_NS_FILTER;
		$dbr = wfGetDB( DB_SLAVE );
		$page = $dbr->tableName( 'page' );
		// QueryPage uses the value from this SQL in an ORDER clause,
		// so return page_title as title.
		return "SELECT 'Filters' as type,
			page_title as title,
			page_title as value
			FROM $page
			WHERE page_namespace = $filter_ns";
	}

	function getQueryInfo() {
		return array(
			'tables' => array( 'page' ),
			'fields' => array( 'page_title AS title', 'page_title AS value' ),
			'conds' => array( 'page_namespace' => SD_NS_FILTER )
		);
	}

	function sortDescending() {
		return false;
	}

	function formatResult( $skin, $result ) {
		$title = Title::makeTitle( SD_NS_FILTER, $result->value );
		$text = $skin->makeLinkObj( $title, $title->getText() );
		return $text;
	}
}
