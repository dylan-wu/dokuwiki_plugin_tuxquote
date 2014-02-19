<?php
/**
 * Plugin Randompics: Inserts a random image and quote.
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Doug Burner
 */
 
// must be run within DokuWiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';

// Callback used to determine if the passed file is an image.
function isapic($item){
    return strpos($item, ".jpg") 
    || strpos($item, ".png") 
    || strpos($item, ".gif");
}

// Return a random quote.
function choosequote(){
    $quotes = file(DOKU_PLUGIN."randompics/quotes.txt");
    return $quotes[array_rand($quotes,1)];
}

// Chose and format a random image.
function chooseimage() {
    $IMAGEBASEURL = dirname($_SERVER['PHP_SELF'])."/lib/plugins/randompics/pics/";
    $IMAGEBASEURL = str_replace("//", "/", $IMAGEBASEURL);
    $IMAGEDIR = DOKU_PLUGIN."randompics/pics/";
    $images = array_filter(scandir($IMAGEDIR), 'isapic');
    return $IMAGEBASEURL.$images[array_rand($images,1)];
}

// Return HTML encoded random image and quote.
function image_and_quote(){
    return  "<div style=\"float: right; width:256px; \"><img src=\""
            .chooseimage()."\" ></a><br><p align=\"middle\">"
            .choosequote()."</p> </div>";
}

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_randompics extends DokuWiki_Syntax_Plugin {
 
    function getInfo() {
        return array('author' => 'Doug Burner',
                     'email'  => 'doug869@users.noreply.github.com',
                     'date'   => '2014-02-19',
                     'name'   => 'Randompics Plugin',
                     'desc'   => 'Show a random image and quote',
                     'url'    => 'https://github.com/foug869/dokuwiki_plugin_randompics');
    }
 
    function getType() { return 'substition'; }
    function getSort() { return 32; }
 
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\[RANDOMPICS\]',$mode,'plugin_randompics');
    }
 
    function handle($match, $state, $pos, &$handler) {
        return array($match, $state, $pos);
    }
 
    function render($mode, &$renderer, $data) {
        if($mode == 'xhtml'){
            $renderer->doc .= image_and_quote();
            return true;
        }
        return false;
    }
}

