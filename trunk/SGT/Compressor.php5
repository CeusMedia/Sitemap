<?php
/**
 *	Compressor for sitemap as XML or file.
 *	@category		cmModules
 *	@package		SGT
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@since			26.06.2013
 *	@version		$Id$
 */
/**
 *	Compressor for sitemap as XML or file.
 *	@category		cmModules
 *	@package		SGT
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@since			26.06.2013
 *	@version		$Id$
 */
class CMM_SGT_Compressor{
	
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
				throw new OutOfRangeException( 'Invalid compression method' );						//  quit with exception
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
			throw new OutOfRangeException( 'Invalid compression method' );							//  quit with exception
		$fileNameNew	= $fileName.( $method == self::METHOD_BZIP ? ".bz" : ".gz" );				//  calculate new file name
		$xml			= File_Reader::load( $fileName );											//  load original file
		$xml			= self::compressString( $xml, $method );									//  compress xml string
		$size			= File_Writer::save( $fileNameNew, $xml );									//  save compressed file
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
				throw new OutOfRangeException( 'Invalid compression method' );						//  quit with exception
		}
	}
}
?>