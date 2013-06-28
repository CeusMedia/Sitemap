<?php
/**
 *	Data model of sitemaps.
 *	@category		cmModules
 *	@package		SGT
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@since			26.06.2013
 *	@version		$Id$
 */
/**
 *	Data model of sitemaps.
 *	@category		cmModules
 *	@package		SGT
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@since			26.06.2013
 *	@version		$Id$
 */
class CMM_SGT_Sitemap implements Countable{

	/**	@var		string		$url		URL of sitemap for sitemap index */
	protected $url				= NULL;
	/**	@var		array		$urls		List of mapped URLs */
	protected $urls				= array();
	/**	@var		string		$datetime	Timestamp of sitemap for sitemap index */
	protected $datetime			= NULL;

	/**
	 *	Constructor.
	 *	@access		public
	 *	@param		string		$url		URL of sitemap for sitemap index
	 *	@param		string		$datetime	Timestamp of sitemap for sitemap index
	 *	@return		void
	 */
	public function __construct( $url = NULL, $datetime = NULL ){
		if( $url )
			$this->setUrl( $url );
		if( $datetime )
			$this->setDatetime( $datetime );
	}

	/**
	 *	Add URL to sitemap.
	 *	@access		public
	 *	@param		string		$location		Location of sitemap URL
	 *	@param		string		$datetime		Timestamp
	 *	@param		string		$frequency		...
	 *	@param		string		$priority		...
	 *	@return		void
	 */
	public function add( $location, $datetime = NULL, $frequency = NULL, $priority = NULL ){
		$this->addUrl( new CMM_SGT_Sitemap_URL( $location, $datetime, $frequency, $priority ) );	//  
	}

	/**
	 *	Add URL to sitemap.
	 *	@access		public
	 *	@param		CMM_SGT_Sitemap	$url		URL to add to sitemap
	 *	@return		boolean
	 */
	public function addUrl( CMM_SGT_Sitemap_URL $url ){
		foreach( $this->urls as $entry )
			if( $entry->getLocation() === $url->getLocation() )
				return FALSE;
		$this->urls[]	= $url;
		if( $url->getDatetime() ){
			if( !$this->datetime )
				$this->datetime	= $url->getDatetime();
			else{
				$timestamp	= strtotime( $url->getDatetime() );
				if( $timestamp > strtotime( $this->datetime ) )
					$this->datetime	= $url->getDatetime();
			}
		}
		return TRUE;
	}

	/**
	 *	Returns number of mapped URLs.
	 *	@return integer
	 */
	public function count(){
		return count( $this->urls );
	}

	/**
	 *	Return timestamp of sitemap.
	 *	@return		string
	 */
	public function getDatetime(){
		return $this->datetime;
	}

	/**
	 *	Returns URL of sitemap, if set.
	 *	@access		public
	 *	@return		string|NULL		URL of sitemap
	 */
	public function getUrl(){
		return $this->url;
	}

	/**
	 *	Returns list of mapped URLs.
	 *	@access		public
	 *	@return		array		List of mapped URLs
	 */
	public function getUrls(){
		return $this->urls;
	}

	/**
	 *	Returns XML of sitemap. Compression is available.
	 *	@access		public
	 *	@param		integer		$compression		Compression method (see CMM_SGT_Compressor)
	 *	@return		string
	 */
	public function render( $compression = NULL ){
		return CMM_SGT_Generator::renderSitemap( $this, $compression );
	}

	/**
	 *	Renders and saves sitemap as file. Compression is available.
	 *	Enabling compression automatically adds extension to file name.
	 *	@access		public
	 *	@param		string		$fileName			Name of sitemap file
	 *	@param		integer		$compression		Compression method (see CMM_SGT_Compressor)
	 *	@return		integer		Number of written bytes
	 */
	public function save( $fileName, $compression = NULL ){
		$number	= File_Writer::save( $fileName, $this->render() );
		if( $compression )
			$number	= CMM_SGT_Compressor::compressFile( $fileName, $compression );
		return $number;
	}

	/**
	 *	Sets timestamp of sitemap for sitemap index.
	 *	@access		public
	 *	@param		string		$datetime			Timestamp of sitemap (W3C Datetime)
	 *	@see		http://www.w3.org/TR/NOTE-datetime
	 *	@return		void
	 */
	public function setDatetime( $datetime ){
		$this->datetime	= $datetime;
	}

	/**
	 *	Sets URL of sitemap for sitemap index.
	 *	@access		public
	 *	@param		string		$url		URL of sitemap
	 *	@return		void
	 */
	public function setUrl( $url ){
		$this->url	= $url;
	}
}
?>
