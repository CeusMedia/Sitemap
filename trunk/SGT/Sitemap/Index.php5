<?php
/**
 *	Data model of sitemap indices.
 *
 *	Copyright (c) 2010-2013 Christian Würker / {@link http://ceusmedia.de/ Ceus Media}
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *	@category		cmModules
 *	@package		SGT.Sitemap
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2010-2013 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmmodules/
 *	@since			0.3.0
 *	@version		$Id$
 */
/**
 *	Data model of sitemap indices.
 *	@category		cmModules
 *	@package		SGT.Sitemap
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2010-2013 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmmodules/
 *	@since			0.3.0
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
