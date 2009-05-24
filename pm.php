<?php
/**
 * A PHP Class interface for Sierra Bravo's X-Box voting SOAP server.
 *
 * @author Ali Karbassi
 * @version $Id$
 * @copyright Ali Karbassi, May 17, 2009
 * @package Page Management
 **/

/**
 * A PHP Class interface for Sierra Bravo's X-Box voting SOAP server.
 *
 * @package Page Management
 * @author Ali Karbassi
 */
class PageManagement
{
	private $userKey;
	private $SOAPClient;
	private $structure;
	private $debug;
	private $haveList;
	private $wantList;

	/**
	 * Constructor class
	 *
	 * @param string $userKey Secret key used with SOAP server.
	 * @param string $url URL to server
	 * @param string $debug Display debug strings; Default: false
	 * @return void
	 * @author Ali Karbassi
	 */
	function __construct($userKey, $url, $debug = false)
	{
		$this->userKey = $userKey;
		$this->debug = $debug;
		$this->SOAPClient = new SoapClient($url);
		$this->haveList = array();
		$this->wantList = array();
	}

	/**
	 * Retrieves all votes from SOAP server.
	 *
	 * @return object Structure returned.
	 * @author Ali Karbassi
	 */
	public function getAllVotes()
	{
		$this->structure = $this->SOAPClient->getVotes($this->userKey);
		$this->updateLists();
		return $this->structure;
	}

	/**
	 * Adds a vote to the gameID after validating it exists and is on the want
	 * list
	 *
	 * @param string $gameId Unique Game Id
	 * @return bool True if successful; False otherwise.
	 * @author Ali Karbassi
	 */
	public function addVote($gameId)
	{
		if ( empty($gameId) || $gameId == '' || !ctype_digit($gameId) ||
			!$this->gameExist($gameId) || 
			$this->findGame($gameId)->status === 'gotit' )
		{
			return false;
		}
		return $this->SOAPClient->addVote($this->userKey,
			 $this->findGame($gameId)->id);
	}

	/**
	 * Adds a game to the SOAP server after validation.
	 *
	 * @param string $gameId Unique Game Id
	 * @return bool True if successful; False otherwise.
	 * @author Ali Karbassi
	 */
	public function addGame($gameId)
	{
		if (empty($gameId) || $gameId == '' || !ctype_digit($gameId) ||
			$this->gameExist($gameId))
		{
			return false;
		}
		
		return $this->SOAPClient->addTitle($this->userKey, $gameId);
	}
	
	/**
	 * Moves the game from want list to have list on SOAP server.
	 *
	 * @param string $gameId Unique Game Id
	 * @return bool True if successful; False otherwise.
	 * @author Ali Karbassi
	 */
	public function addToOwned($gameId)
	{
		if ( empty($gameId) || $gameId == '' || !ctype_digit($gameId) )
		{
			return false;
		}
		
		$this->getAllVotes();
		
		if ($this->findGame($gameId)->status === 'gotit')
		{
			return false;
		}
		
		return $this->SOAPClient->addGotIt($this->userKey,
			$this->findGame($gameId)->id);
	}
	
	/**
	 * Clears all votes from SOAP server
	 *
	 * @return bool True if successful; False otherwise.
	 * @author Ali Karbassi
	 */
	public function clearVotes()
	{
		return $this->SOAPClient->clearVotes($this->userKey);
	}

	/**
	 * Finds the server id for the game id
	 *
	 * @param string $gameId Unique Game Id 
	 * @return int Server Id
	 * @author Ali Karbassi
	 */
	public function findGame($gameId)
	{
		if ( empty($gameId) || $gameId == '' || !ctype_digit($gameId) )
		{
			return -1;
		}

		$this->getAllVotes();

		foreach ($this->structure as $key => $value)
		{
			if( $value->title == $gameId )
			{
				return $value;
			}
		}

		return -1;
	}
	
	/**
	 * Returns vote for specific game id.
	 *
	 * @param string $gameId Unique Game Id 
	 * @return int Number of votes; -1 if not found or error.
	 * @author Ali Karbassi
	 */
	public function getVotes($gameId)
	{
		if ( empty($gameId) || $gameId == '' || !ctype_digit($gameId) )
		{
			return -1;
		}

		$this->getAllVotes();

		foreach ($this->structure as $key => $value)
		{
			if( $value->title == $gameId )
			{
				return (int) $value->votes;
			}
		}

		return -1;
	}
	
	/**
	 * Returns truw or false if game id exists in any list (want or have).
	 *
	 * @param string $gameId Unique Game Id 
	 * @return bool True if found; False otherwise.
	 * @author Ali Karbassi
	 */
	public function gameExist($gameId)
	{
		if (empty($gameId) || $gameId == '' || !ctype_digit($gameId) )
		{
			return false;
		}
		
		$this->getAllVotes();
		
		if( in_array($gameId, $this->wantList) || 
			in_array($gameId, $this->haveList) )
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns want list.
	 *
	 * @return array Want List
	 * @author Ali Karbassi
	 */
	public function wantList()
	{
		$this->getAllVotes();
		return $this->wantList;
	}
	
	/**
	 * Returns have list.
	 *
	 * @return array Have List
	 * @author Ali Karbassi
	 */
	public function haveList()
	{
		$this->getAllVotes();
		return $this->haveList;
	}

	/**
	 * Updates the want and have list.
	 *
	 * @return void
	 * @author Ali Karbassi
	 */
	private function updateLists()
	{
		if ( count( $this->structure ) > 0)
		{
			foreach ($this->structure as $item)
			{
				if ($item->status === "gotit")
				{
					array_push($this->haveList, $item->title);
					$this->haveList = array_unique($this->haveList);
					sort($this->haveList);
				}
				elseif ($item->status === "wantit")
				{
					array_push($this->wantList, $item->title);
					$this->wantList = array_unique($this->wantList);
					sort($this->wantList);
				}
			}
			$this->debug($this->haveList);
			$this->debug($this->wantList);
		}
	}
	
	/**
	 * Displays the debug string sent if if and only if debug is on.
	 *
	 * @param string $display Debug Strings
	 * @return void
	 * @author Ali Karbassi
	 */
	private function debug($display)
	{
		if ( $this->debug )
		{
			echo '<pre>';
			var_dump($display);
			echo '</pre>';
			echo '<hr />';
		}
	}
}
?>