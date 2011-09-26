<?php

namespace Xbox;

class Gamertag 
{
  
  /** @var SimpleXMLElement */
  protected $_xml = null;
  
  /** @var string contains the gamertag */
  public $gamertag = null;
  
  /** @var string contains gamer's score */
  public $score = null;
  
  /** @var string contains gamer's location */
  public $location = null;
  
  /** @var string contains gamer's bio */
  public $bio = null;
  
  /** @var string contains gamer's motto */
  public $motto = null;
  
  /** @var string contains gamer's name */
  public $name = null;
  
  /**
   * @var bool is true when it is a valid gamertag
   */
  public $valid = false;
  
  /**
   * converts HTML code into valid XML code
   * @param string HTML code
   * @return string XML code
   */
  private function htmlToXml($res)
  {
    // replace numeric entities
    $res = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $res);
    $res = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $res);
    
    // replace literal entities
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    $res = strtr($res, $trans_tbl);
    
    // let's convert some utf8 signs into html entities
    $res = str_replace('®','(R)',$res);
    
    // let's convert back all &-signs
    $res = str_replace('&','&amp;',$res);
    
    /** @todo: cleanup non existing ascii codes */
    
    // SimpleXMLElement expects UTF-8 code
    $res = utf8_decode($res);
    
    return $res;
  }
  
  /**
   * @example $gamertag = new Gamertag(@file_get_contents('http://gamercard.xbox.com/en-US/XoverBit.card'));
   * @param string $res includes the whole gamertag
   */
  public function __construct($res)
  {
    if (is_string($res))
    {
      try {
        $xml = $this->htmlToXml($res);

        $this->_xml = new \SimpleXMLElement($xml);
        
        // read gamertag
        $this->gamertag = (string)$this->_xml->head->title;
        
        // read gamer's score
        $score = $this->_xml->xpath("//*[@id='Gamerscore']/text()");
        $this->score = (string)current($score);
        
        // read gamer's location
        $location = $this->_xml->xpath("//*[@id='Location']/text()");
        $this->location = (string)current($location);
        
        // read gamer's bio
        $bio = $this->_xml->xpath("//*[@id='Bio']/text()");
        $this->bio = (string)current($bio);
        
        // read gamer's motto
        $motto = $this->_xml->xpath("//*[@id='Motto']/text()");
        $this->motto = (string)current($motto);
        
        // read gamer's name
        $name = $this->_xml->xpath("//*[@id='Name']/text()");
        $this->name = (string)current($name);
        
        // check if gamer is valid
        $this->valid = (trim($this->score) !== '--');
      } catch(\Exception $e) {
        $this->valid = false;
      }
      
    } else {
      throw new \Exception("Resource must be a string.");
    }
  }
  
  /**
   * build a Gamertag class using
   * @param string gamertag
   * @return Gamertag
   */
  static public function create($gamertag)
  {
    $c = get_called_class();
    $gamertag = rawurlencode($gamertag);
    $gamertagObject = new $c(@file_get_contents("http://gamercard.xbox.com/en-US/{$gamertag}.card"));
    return $gamertagObject;
  }
  
}
