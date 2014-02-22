<?php
/**
 * Plugin Tuxquote: Inserts a random image and quote.
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Craig Douglas
 */
 
// must be run within DokuWiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_tuxquote extends DokuWiki_Syntax_Plugin {
 
    function getInfo() {
        return array('author' => 'Craig Douglas',
                     'email'  => 'eldougo@missionrisk.com',
                     'date'   => '2014-02-21',
                     'name'   => 'Tuxquote Plugin',
                     'desc'   => 'Show a random image and quote',
                     'url'    => 'https://github.com/eldougo/dokuwiki_plugin_tuxquote');
    }
 
    function getType() { return 'substition'; }
    function getSort() { return 32; }
 
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\[TUXQUOTE\]',$mode,'plugin_tuxquote');
    }
 
    function handle($match, $state, $pos, &$handler) {
        return array($match, $state, $pos);
    }
 
    function render($mode, &$renderer, $data) {
        if($mode == 'xhtml'){
            $renderer->doc .= $this->image_and_quote();
            return true;
        }
        return false;
    }
    
    /**
     * Callback used to determine if the passed file is an image.
     */
    function isapic($item){
        return strpos($item, ".jpg") 
        || strpos($item, ".png") 
        || strpos($item, ".gif");
    }

   /**
    * Return a random quote.
    */
    public function choosequote(){
        $quotes = file(DOKU_PLUGIN.$this->getPluginName()."/quotes.txt");
        return $quotes[array_rand($quotes,1)];
    }

    /**
     * Chose and format a random image.
     */
    function chooseimage() {
        $IMAGEBASEURL = dirname($_SERVER['PHP_SELF'])."/lib/plugins/{$this->getPluginName()}/pics/";
        $IMAGEDIR = DOKU_PLUGIN.$this->getPluginName()."/pics/";
        $images = array_filter(scandir($IMAGEDIR), array($this, 'isapic'));
        return $IMAGEBASEURL.$images[array_rand($images,1)];
    }

    /*
     * Return HTML encoded random image and quote.
     */
    function image_and_quote(){
        return  "<div style=\"float: right; width:256px; \"><img src=\""
                .$this->chooseimage()."\" ></a><br><p align=\"middle\">"
                .$this->choosequote()."</p></div>\n";
    }
}

