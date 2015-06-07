<?php
/**
 *	Compressor for sitemap as XML or file.
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
 *	Compressor for sitemap as XML or file.
 *	@category		Library
 *	@package		CeusMedia_Sitemap
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright		2013-2015 {@link http://ceusmedia.de/ Ceus Media}
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			https://github.com/CeusMedia/Sitemap
 */
class Compressor{

	const METHOD_NONE	= 0;
	const METHOD_GZIP	= 1;
	const METHOD_BZIP	= 2;

	/**
	 *	Returns compressed string.
	 *	@access		public
	 *	@param		string		$xml
	 *	@param		integer		$method		Compression method
	 *	@return		string		Compressed string
	 *	@throws		OutOfRangeException		if compression method is invalid
	 */
	static public function compressString( $xml, $method ){
		switch( $method ){
			case self::METHOD_NONE:																	//  no compression
				return $xml;																		//  return original string
			case self::METHOD_BZIP:																	//  bzip compression
				return bzcompress( $xml, 9 );														//  compress with bzip
			case self::METHOD_GZIP:																	//  gzip compression
				return gzencode( $xml, 9 );															//  compress with gzip
			default:																				//  invalid method
				throw new \OutOfRangeException( 'Invalid compression method' );						//  quit with exception
		}
	}

	/**
	 *	Stores compressed version of file and removed original file.
	 *	@access		public
	 *	@param		string		$fileName	Name of file
	 *	@param		integer		$method		Compression method
	 *	@return		integer		Number of written bytes
	 *	@throws		OutOfRangeException		if compression method is invalid
	 */
	static public function compressFile( $fileName, $method ){
		if( $method == self::METHOD_NONE )															//  no compression needed
			return;																					//  quit
		if( !in_array( $method, array( self::METHOD_BZIP, self::METHOD_GZIP ) ) )					//  invalid method
			throw new \OutOfRangeException( 'Invalid compression method' );							//  quit with exception
		$fileNameNew	= $fileName.( $method == self::METHOD_BZIP ? ".bz" : ".gz" );				//  calculate new file name
		$xml			= \File_Reader::load( $fileName );											//  load original file
		$xml			= self::compressString( $xml, $method );									//  compress xml string
		$size			= \File_Writer::save( $fileNameNew, $xml );									//  save compressed file
		unlink( $fileName );																		//  remove original file
		return $size;																				//  return number of written bytes
	}

	static public function getContentType( $method ){
		switch( $method ){
			case self::METHOD_NONE:																	//  no compression
				return "application/xml";															//  return original string
			case self::METHOD_BZIP:																	//  bzip compression
				return "application/x-bzip";														//  compress with bzip
			case self::METHOD_GZIP:																	//  gzip compression
				return "application/x-gzip";														//  compress with gzip
			default:																				//  invalid method
				throw new \OutOfRangeException( 'Invalid compression method' );						//  quit with exception
		}
	}
}
?>
