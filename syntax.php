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
                     'date'   => '2014-03-04',
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
            $renderer->doc .= $this->tuxquote_main();
            return true;
        }
        return false;
    }
    
    /**
     * Callback used to determine if the passed file is an image.
     */
    function tuxquote_is_image( $file_name ){
        return strpos( $file_name, ".jpg" ) 
        ||     strpos( $file_name, ".png" ) 
        ||     strpos( $file_name, ".gif" );
    }

   /**
    * Return a random quote.
    */
    public function tuxquote_choose_quote(){
        $quotes = file( DOKU_PLUGIN.$this->getPluginName()."/quotes.txt" );
        return $quotes[ array_rand( $quotes,1 ) ];
    }

    /**
     * Chose and format a random image.
     */
    function tuxquote_choose_image() {
        $image_url   = dirname( $_SERVER['PHP_SELF'] )."/lib/plugins/{$this->getPluginName()}/images/";
        $image_dir   = DOKU_PLUGIN.$this->getPluginName()."/images/";
        $image_array = array_filter( scandir( $image_dir ), array( $this, 'tuxquote_is_image' ) );
        return $image_url.$image_array[ array_rand( $image_array,1 ) ];
    }

    /*
     * Return HTML encoded random image and quote.
     */
    function tuxquote_main() {
        return  "\n<div style='float: right; width:256px; '>\n"
                ."  <img src='".$this->tuxquote_choose_image()."'><br />\n"
                ."  <p align='middle'>".$this->tuxquote_choose_quote()."</p>\n"
                ."</div>\n";
    }
}

