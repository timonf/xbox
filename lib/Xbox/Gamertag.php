<?php

namespace Xbox;

class Gamertag
{

    /** @var \DOMXPath */
    protected $xpath = null;

    /** @var string contains the gamertag */
    public $gamertag = null;

    /** @var int contains gamer's score */
    public $score = null;

    /**
     * @var string contains gamer's location
     * @deprecated information is not longer provided by Microsoft
     */
    public $location = null;

    /**
     * @var string contains gamer's bio
     * @deprecated information is not longer provided by Microsoft
     */
    public $bio = null;

    /**
     * @var string contains gamer's motto
     * @deprecated information is not longer provided by Microsoft
     */
    public $motto = null;

    /**
     * @var string contains gamer's name
     * @deprecated information is not longer provided by Microsoft
     */
    public $name = null;

    /**
     * @var bool is true when it is a valid gamertag
     */
    public $valid = false;

    /**
     * @see http://stackoverflow.com/questions/1365756/how-to-view-domnodelist-objects-data-in-php
     * @param \DOMNodeList $xpathResult
     * @return string
     */
    protected function getDomText(\DOMNodeList $xpathResult)
    {
        $tempDom = new \DOMDocument();

        foreach($xpathResult as $n) {
            $tempDom->appendChild($tempDom->importNode($n,true));
        }

        return trim($tempDom->saveHTML());
    }

    /**
     * @example $gamertag = new Gamertag(@file_get_contents('http://gamercard.xbox.com/en-US/XoverBit.card'));
     * @throws \Exception
     * @param string $res includes the whole gamertag
     */
    public function __construct($res)
    {
        if (is_string($res))
        {
            try {
                $dom = new \DOMDocument();
                $dom->loadHTML($res);

                $this->xpath = new \DOMXPath($dom);

                // read gamertag
                $this->gamertag = $this->getDomText($this->xpath->evaluate("/html/head/title/text()"));

                // read gamer's score
                $score = $this->getDomText($this->xpath->evaluate("//*[@id='Gamerscore']/text()"));
                if ($score == '--') {
                    throw new \Exception('Invalid gamertag');
                } else {
                    $this->score = (int)$score;
                }

                // read gamer's location
                $this->location = $this->getDomText($this->xpath->evaluate("//*[@id='Location']/text()"));

                // read gamer's bio
                $this->bio = $this->getDomText($this->xpath->evaluate("//*[@id='Bio']/text()"));

                // read gamer's motto
                $this->motto = $this->getDomText($this->xpath->evaluate("//*[@id='Motto']/text()"));

                // read gamer's name
                $this->name = $this->getDomText($this->xpath->evaluate("//*[@id='Name']/text()"));

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
