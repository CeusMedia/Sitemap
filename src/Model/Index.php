<?php
/**
 *	Data model of sitemap indices.
 *
 *	Copyright (c) 2013-2015 Christian Würker / {@link http://ceusmedia.de/ Ceus Media}
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
 *	@category		Library
 *	@package		CeusMedia_Sitemap_Model
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2013-2015 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Sitemap
 */
namespace CeusMedia\Sitemap\Model;
/**
 *	Data model of sitemap indices.
 *	@category		Library
 *	@package		CeusMedia_Sitemap_Model
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2013-2015 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Sitemap
 */
class CMM_SGT_Sitemap_Index{

	/**	@var		array		$sitemaps		List of sitemaps within index */
	protected $sitemaps			= array();

	/**
	 *	Add sitemap to index.
	 *	@access		public
	 *	@param		\CeusMedia\Sitemap\Model\Map	$sitemap
	 *	@return		void
	 *	@throws		\Exception					if sitemap has no URL
	 *	@throws		OutOfBoundsException		if sitemap has more than 50000 entries
	 */
	public function addSitemap( \CeusMedia\Sitemap\Model\Map $sitemap ){
		if( !$sitemap->getUrl() )
			throw new \Exception( 'Sitemaps needs to have an URL to be indexable' );
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
		return \CeusMedia\Sitemap\Generator::renderSitemapIndex( $this, $compression );
	}

	/**
	 *	Saves sitemap index to file.
	 *	@access		public
	 *	@param		string		$fileName			Name of sitemap index file
	 *	@param		integer		$compression		Compression method (see \CeusMedia\Sitemap\Compressor)
	 *	@return		integer		Number of written bytes
	 */
	public function save( $fileName, $compression = NULL ){
		$number	= File_Writer::save( $fileName, $this->render() );
		if( $compression )
			$number	= \CeusMedia\Sitemap\Compressor::compressFile( $fileName, $compression );
		return $number;
	}
}
?>
