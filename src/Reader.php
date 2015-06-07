<?php
/**
 *	Submits sitemap URL to Google and Bing webmaster tools.
 *
 *	Copyright (c) 2013 Christian Würker / {@link http://ceusmedia.de/ Ceus Media}
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
 *	@package		CeusMedia_Sitemap
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2013-2015 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Sitemap
 */
namespace CeusMedia\Sitemap;
/**
 *	Submits sitemap URL to Google and Bing webmaster tools.
 *	@category		Library
 *	@package		CeusMedia_Sitemap
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2013-2015 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Sitemap
 */
class Reader{

	static protected function convertTreeToSitemapIndex( XML_DOM_Node $tree ){
		$index		= new \CeusMedia\Sitemap\Model\Index();
		$attributes	= array( 'loc', 'lastmod' );
		if( $tree->getNodeName() !== "sitemapindex" )
			throw new \Exception( 'Root node of sitemap index must be "sitemapindex"' );
		foreach( $tree->getChildren() as $child ){
			if( $child->getNodeName() !== "sitemap" )
				throw new \Exception( 'Missing node of type "sitemap"' );
			$loc = $lastmod = NULL;
			foreach( $child->getChildren() as $node )
				if( in_array( $node->getNodeName(), $attributes ) )
					${$node->getNodeName()}	= $node->getContent();
			if( strlen( trim( $loc ) ) ){
				$sitemap	= self::readSitemapUrl( $loc );
				$sitemap->setUrl( $loc );
				if( strlen( trim( $lastmod ) ) )
					$sitemap->setDatetime ( $lastmod );
				$index->addSitemap( $sitemap );
			}
		}
		return $index;
	}

	static public function detectCompression( $fileName ){
		switch( pathinfo( $fileName, PATHINFO_EXTENSION ) ){
			case 'bz':	return \CeusMedia\Sitemap\Compressor::METHOD_BZIP;
			case 'gz':	return \CeusMedia\Sitemap\Compressor::METHOD_GZIP;
		}
		return \CeusMedia\Sitemap\Compressor::METHOD_NONE;
	}

	static public function readSitemap( $xml ){
		$parser		= new \XML_DOM_Parser();
		$tree		= $parser->parse( $xml );
		$sitemap	= new \CeusMedia\Sitemap\Model\Map();
		$attributes	= array( 'loc', 'lastmod', 'frequency', 'priority' );
		if( $tree->getNodeName() !== "urlset" )
			throw new \Exception( 'Root node of sitemap must be "urlset"' );
		foreach( $tree->getChildren() as $child ){
			if( $child->getNodeName() !== "url" )
				throw new \Exception( 'Missing node of type "url"' );
			$loc = $lastmod = $frequency = $priority = NULL;
			foreach( $child->getChildren() as $node )
				if( in_array( $node->getNodeName(), $attributes ) )
					${$node->getNodeName()}	= $node->getContent();
			if( strlen( trim( $loc ) ) ){
				$url	= new \CeusMedia\Sitemap\Model\Url( $loc );
				if( strlen( trim( $lastmod ) ) )
					$url->setDatetime ( $lastmod );
				if( strlen( trim( $frequency ) ) )
					$url->setFreqency ( $frequency );
				if( strlen( trim( $priority ) ) )
					$url->setDatetime ( $priority );
				$sitemap->addUrl( $url );
			}
		}
		return $sitemap;
	}

	static public function readSitemapFile( $fileName ){
		$xml	= \File_Reader::load( $fileName );
		if( ( $method = self::detectCompression( $fileName ) ) )
			$xml	= self::uncompressXml ( $xml, $method );
		return self::readSitemap( $xml );
	}

	static public function readSitemapUrl( $url ){
		$xml		= \Net_Reader::readUrl( $url );
		if( ( $method = self::detectCompression( $url ) ) )
			$xml	= self::uncompressXml ( $xml, $method );
		$sitemap	= self::readSitemap( $xml );
		$sitemap->setUrl( $url );
		return $sitemap;
	}

	static public function readIndex( $xml ){
		$parser	= new \XML_DOM_Parser();
		$tree	= $parser->parse( $xml );
		return self::convertTreeToSitemapIndex( $tree );
	}

	static public function readIndexFile( $fileName ){
		$tree	= \XML_DOM_FileReader::load( $fileName);
		return self::convertTreeToSitemapIndex( $tree );
	}

	static public function readIndexUrl( $url ){
		$tree	= \XML_DOM_UrlReader::load( $url );
		return self::convertTreeToSitemapIndex( $tree );
	}

	static public function uncompressXml( $xml, $method = 0 ){
		switch( $method ){
			case \CeusMedia\Sitemap\Compressor::METHOD_BZIP:
				return bzdecompress( $xml );
			case \CeusMedia\Sitemap\Compressor::METHOD_GZIP:
				return gzdecode( $xml );
			case \CeusMedia\Sitemap\Compressor::METHOD_NONE:
				return $xml;
		}
	}
}
?>
