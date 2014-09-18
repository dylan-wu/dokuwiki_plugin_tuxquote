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
                     'email'  => 'contact22@eldougo.net',
                     'date'   => '2014-09-18',
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
        $quotes = file( DOKU_PLUGIN . $this->getPluginName() . "/quotes.txt" );
        return $quotes[ array_rand( $quotes, 1 ) ];
    }

    /**
     * Chose and format a random image.
     */
    function tuxquote_choose_image() {
        $image_url   = getBaseURL() . "lib/plugins/{$this->getPluginName()}/images/";
        $image_dir   = DOKU_PLUGIN . $this->getPluginName() . "/images/";
        $image_array = array_filter( scandir( $image_dir ), array( $this, 'tuxquote_is_image' ) );
        return $image_url . $image_array[ array_rand( $image_array,1 ) ];
    }

    /**
     * Build and format HTML output.
     *
     * @param   string  $div_width   Div width in pixels or percentage [NN%|NNpx].
     * @param   string  $div_align       Div float alignment [none|left|right].
     * @param   string  $title       Div title, optional.
     */
    function tuxquote_build_format( $div_width, $div_align, $title = '' ) {
        if ( empty( $div_width ) ) {
            $div_width = TUXQUOTE_DEFAULT_WIDTH;
        }
        if ( is_numeric( $div_width ) ) {
            $div_width = trim( $div_width ) . "%";
        }
        if ( empty( $div_align ) ) {
        $div_align = TUXQUOTE_DEFAULT_ALIGN;
        }
        if ( ! empty( $title ) ) {
             $title_line = "  <p style='text-align: center; font-weight: 900'>" . $title . "</p>\n";
        } else {
            $title_line = '';
        }
        return  "\n<div style='float: " . $div_align . "; width: " . $div_width . "; '>\n"
                .$title_line
                ."  <img style='width:100%' src='" . $this->tuxquote_choose_image()  . "'><br />\n"
                ."  <p style='text-align: center'>" . $this->tuxquote_choose_quote() . "</p>\n"
                ."</div>\n";
     }

    /**
     * Return HTML encoded random image and quote.
     */
    function tuxquote_main() {
        return  $this->tuxquote_build_format( $this->getConf('tuxquote_width'), $this->getConf('tuxquote_align'), $this->getConf('tuxquote_title') );
    }
} // class syntax_plugin_tuxquote
