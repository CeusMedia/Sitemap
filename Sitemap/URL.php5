<?php
/**
 *	Data model of URL for sitemaps.
 *	@category		cmModules
 *	@package		SGT.Sitemap
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@since			26.06.2013
 *	@version		$Id$
 */
/**
 *	Data model of URL for sitemaps.
 *	@category		cmModules
 *	@package		SGT.Sitemap
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@since			26.06.2013
 *	@version		$Id$
 */
class CMM_SGT_Sitemap_URL{
	
	/**	@var	string		$location */
	protected $location		= "";
	
	/**	@var	string		$datetime */
	protected $datetime		= NULL;
	
	/**	@var	string		$frequency */
	protected $frequency	= NULL;
	
	/**	@var	float		$priority */
	protected $priority		= NULL;

	/**	@var	array		$frequencies */
	protected $frequencies	= array(
		'always',
		'hourly',
		'daily',
		'weekly',
		'monthly',
		'yearly',
		'never'
	);

	/**
	 *	Constructor.
	 *	@access		public
	 *	@param		string		$location		Location (absolute URL)
	 *	@param		string		$datetime		Timestamp (@see 
	 *	@param		string		$frequency
	 *	@param		float		$priority
	 *	@return		void
	 */
	public function __construct( $location, $datetime = NULL, $frequency = NULL, $priority = NULL ){
		$this->setLocation( $location );
		if( !is_null( $datetime ) )
			$this->setDatetime( $datetime );
		if( !is_null( $frequency ) )
			$this->setFreqency( $frequency );
		if( !is_null( $priority ) )
			$this->setPriority( $priority );
	}

	/**
	 *	Returns location of sitemap URL.
	 *	@access		public
	 *	@return		string		Location of sitemap resource
	 */
	public function getLocation(){
		return $this->location;
	}

	/**
	 *	Returns timestamp of sitemap resource.
	 *	@access		public
	 *	@return		string		Datetime timestamp of sitemap resource
	 */
	public function getDatetime(){
		return $this->datetime;
	}

	/**
	 *	Sets ...
	 *	@access		public
	 *	@return		string		...
	 */
	public function getFrequency(){
		return $this->frequency;
	}

	/**
	 *	Returns priority of sitemap resource.
	 *	@access		public
	 *	@return		float		...
	 */
	public function getPriority(){
		return $this->priority;
	}

	/**
	 *	Set location (absolute URL) of sitemap resource.
	 *	@access		public
	 *	@param		string		$url		Location (absolute URL) of sitemap resource
	 *	@return		void
	 */
	public function setLocation( $url ){
		$this->location	= $url;
	}

	/**
	 *	Set 
	 *	@param		string		$frequency		...
	 *	@throws		InvalidArgumentException
	 *	@return		void
	 */
	public function setFreqency( $frequency ){
		$frequency	= trim( strolower( $frequency ) );
		if( !in_array( $frequency, $this->frequencies ) )
			throw new InvalidArgumentException( 'Frequency must with one of '.join( ', ', $this->frequencies ) );
		$this->frequency	= $frequency;
	}

	/**
	 *	Set timestamp of sitemap resource.
	 *	@access		public
	 *	@param		string		$datetime		Datetime timestamp of sitemap resource
	 *	@see		http://www.w3.org/TR/NOTE-datetime
	 *	@return		void
	 */
	public function setDatetime( $datetime ){
		$this->datetime	= $datetime;
	}

	/**
	 *	Set priority of sitemap resource.
	 *	@access		public
	 *	@param		float		$priority		...
	 *	@throws		OutOfBoundsException
	 *	@return		void
	 */
	public function setPriority( $priority = 0.5 ){
		if( $priority < 0 )
			throw new OutOfBoundsException( 'Priority cannot be lower than 0' );
		else if( $priority > 1 )
			throw new OutOfBoundsException( 'Priority cannot be greater than 1' );
		$this->priority	= priority;
	}
}
?>
