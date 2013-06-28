<?php
/**
 *	Data model of sitemap indices.
 *	@category		cmModules
 *	@package		SGT.Sitemap
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@since			26.06.2013
 *	@version		$Id$
 */
/**
 *	Data model of sitemap indices.
 *	@category		cmModules
 *	@package		SGT.Sitemap
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@since			26.06.2013
 *	@version		$Id$
 */
class CMM_SGT_Sitemap_Index{

	/**	@var		array		$sitemaps		List of sitemaps within index */
	protected $sitemaps			= array();
	
	/**
	 *	Add sitemap to index.
	 *	@access		public
	 *	@param		CMM_SGT_Sitemap	$sitemap
	 *	@return		void
	 *	@throws		Exception					if sitemap has no URL
	 *	@throws		OutOfBoundsException		if sitemap has more than 50000 entries
	 */
	public function addSitemap( CMM_SGT_Sitemap $sitemap ){
		if( !$sitemap->getUrl() )
			throw new Exception( 'Sitemaps needs to have an URL to be indexable' );
		$this->sitemaps[]	= $sitemap;
	}

	/**
	 *	Returns list of indexed sitemaps.
	 *	@acess		public
	 *	@return		array
	 */
	public function getSitemaps(){
		return $this->sitemaps;
	}

	/**
	 *	Returns XML of sitemap index.
	 *	@access		public
	 *	@param		integer		$compression	Compression method (see CMM_SGT_Compressor)
	 *	@return		string		XML of sitemap index
	 */
	public function render( $compression = NULL ){
		return CMM_SGT_Generator::renderSitemapIndex( $this, $compression );
	}

	/**
	 *	Saves sitemap index to file.
	 *	@access		public
	 *	@param		string		$fileName			Name of sitemap index file
	 *	@param		integer		$compression		Compression method (see CMM_SGT_Compressor)
	 *	@return		integer		Number of written bytes
	 */
	public function save( $fileName, $compression = NULL ){
		$number	= File_Writer::save( $fileName, $this->render() );
		if( $compression )
			$number	= CMM_SGT_Compressor::compressFile( $fileName, $compression );
		return $number;
	}
}
?>
