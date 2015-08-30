<?php
  /* vim: set ts=4 sw=4: */
  /**
   * Class for working with BibTex data
   *
   * A class which provides common methods to access and
   * create Strings in BibTex format
   *
   * PHP version 5
   *
   * LICENSE: This source file is subject to version 3.0 of the PHP license
   * that is available through the world-wide-web at the following URI:
   * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
   * the PHP License and are unable to obtain it through the web, please
   * send a note to license@php.net so we can mail you a copy immediately.
   *
   * @category   Structures
   * @package    Structures_BibTex
   * @author     Till Steinbach <till.stienbach@informatik.haw-hamburg.de>
   * @copyright  1997-2005 The PHP Group
   * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
   * @version    CVS: $Id: BibTex.php 322412 2012-01-17 14:25:28Z clockwerx $
   * @link       http://pear.php.net/package/Structures_BibTex
   */


require_once 'Structures/BibTex/Exception.php';
require_once 'Structures/BibTex.php';

class Structures_BibTex_Formater
{
    var $bibstyles;   
    
    var $entrytypes;
    
    var $types;
    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    function Structures_BibTex_Formater($options = array())
    {

	$this->bibstyles = array(
		'mystyle' => array(
			        'article'	    => '<:post:>(<:sc:>(<:authors:>([:author:])),". ").<:post:>(<:b:>([:title:]),". ").<:post:>(<:pre:>("In: ",<:i:>([:journal:])),". ").<:post:>(<:pages:>([:pages:]),", ").<:post:>([:address:],", ").<:post:>(<:smonth:>([:month:])," ").<:post:>([:year:],", ").<:post:>([:publisher:],", ")',
            		'book'		    => '',
            		'booklet'	    => '',
            		'conference'	=> '',
            		'inbook'	    => '<:post:>(<:sc:>(<:authors:>([:author:])),". ").<:post:>(<:b:>([:title:]),". ").<:post:>(<:pre:>("In: ",<:i:>([:booktitle:])),". ").<:post:>(<:pages:>([:pages:]),", ").<:post:>([:address:],", ").<:post:>(<:smonth:>([:month:])," ").<:post:>([:year:],", ").<:post:>([:publisher:],", ")',
            		'incollection'	=> '<:post:>(<:sc:>(<:authors:>([:author:])),". ").<:post:>(<:b:>([:title:]),". ").<:post:>(<:pre:>("In: ",<:i:>([:journal:])),". ").<:post:>(<:pages:>([:pages:]),", ").<:post:>([:address:],", ").<:post:>(<:smonth:>([:month:])," ").<:post:>([:year:],", ").<:post:>([:publisher:],", ")',
            		'inproceedings'	=> '<:post:>(<:sc:>(<:authors:>([:author:])),". ").<:post:>(<:b:>([:title:]),". ").<:post:>(<:pre:>("In: ",<:i:>([:booktitle:])),". ").<:post:>(<:pages:>([:pages:]),", ").<:post:>([:address:],", ").<:post:>(<:smonth:>([:month:])," ").<:post:>([:year:],", ").<:post:>([:publisher:],", ")',
            		'manual'	    => '',
            		'mastersthesis'	=> '<:post:>(<:sc:>(<:authors:>([:author:])),". ").<:post:>(<:b:>([:title:]),". ").<:post:>(<:smonth:>([:month:])," ").<:post:>([:year:],", ").<:post:>(<:type:>([:type:]),". ").<:post:>([:school:],". ")',
            		'misc'		    => '<:post:>(<:sc:>(<:authors:>([:author:])),". ").<:post:>(<:b:>([:title:]),". ").<:post:>(<:smonth:>([:month:])," ").<:post:>([:year:],", ").<:post:>([:note:],". ")',
            		'phdthesis'	    => '<:post:>(<:sc:>(<:authors:>([:author:])),". ").<:post:>(<:b:>([:title:]),". ").<:post:>(<:smonth:>([:month:])," ").<:post:>([:year:],", ")',
            		'proceedings'	=> '<:post:>(<:sc:>(<:authors:>([:editor:])),". ").<:post:>(<:b:>([:title:]),". ").<:post:>([:address:],", ").<:post:>(<:smonth:>([:month:])," ").<:post:>([:year:],", ").<:post:>([:publisher:],", ")',
            		'techreport'	=> '<:post:>(<:sc:>(<:authors:>([:author:])),". ").<:post:>(<:b:>([:title:]),". ").<:post:>(<:smonth:>([:month:])," ").<:post:>([:year:],", ")',
            		'unpublished'	=> '<:post:>(<:sc:>(<:authors:>([:author:])),". ").<:post:>(<:b:>([:title:]),". ").<:post:>(<:smonth:>([:month:])," ").<:post:>([:year:],", ")',
			),
	);
	
	$this->types = array(
                'bachelorsthesis' => 'Bachelorthesis',
                'mastersthesis' => 'Masterthesis',
                );
                
    $this->entrytypes = array( 'all' => 'All',
                'article' => 'Article',
                'inproceedings' => 'Conference Proceedings',
                'mastersthesis' => 'Bachelor & Master Theses',
                'misc' => 'Miscellaneous',
                'proceedings' => 'Edited Conference Proceedings',
                'unpublished' => 'Unpublished');
                
    $this->languages = array(   'english' => 'en_US.utf8',
                                'ngerman' => 'de_DE.utf8',
                            );
                            
            bindtextdomain("messages", "/usr/share/php/data/Structures_BibTex/Structures/locale");
            textdomain("messages");
    }

    function formatByStyle($entry, $style){
        $old_locale = setlocale(LC_ALL, "0");
        //Set locale according to entry
        if(isset($entry['langid'])){
            setlocale(LC_ALL, $this->languages[$entry['langid']]);
        }
        // now use the geman locale
        
        $template = $this->bibstyles[$style][$entry['entryType']];
        //String concatination of two following functions
        $pattern = '/\)<:/';
        $replacement = ').<:';
        $template = preg_replace($pattern, $replacement, $template);
        //replace entrys syntax
        $pattern = '/\[:([^:]+):\]/';
        $replacement = '(isset($entry[\'${1}\']))?$entry[\'${1}\']:null';
        $template = preg_replace($pattern, $replacement, $template);
        //replace functions syntax
        $pattern = '/<:([^:]+):>/';
        $replacement = '$this->${1}';
        $template = preg_replace($pattern, $replacement, $template);
        $return_str = eval('return('.$template.');');
        setlocale(LC_ALL, $old_locale);
        // now use the original locale
    	return $return_str;
    }

	function authors($array){
		$ret = '';
		foreach( $array as $author){
			if(end($array) === $author && sizeof($array)>1){
				$ret .= 'and ';
		    }
			$authorret = '';
		    if(array_key_exists('first',$author)){
		        $authorret .= ' '.$author['first'];
		    }
		    if(array_key_exists('von',$author)){
		        $authorret .= ' '.$author['von'];
		   	 }
		    if(array_key_exists('last',$author)){
		        $authorret .= ' '.$author['last'];
		    }
		    if(array_key_exists('jr',$author)){
		        $authorret .= ' '.$author['jr'];
		    }
			$ret .= trim($authorret);
			if(end($array) !== $author){
		        $ret .= ', ';
		    }
		}
		return($ret);
	}
	
	function pages($array){
	    $ret = ucfirst(_('pages')).' ';
	    if($array == null){
	        return null;
	    }
	    else if (is_array($array)){
	        if(array_key_exists('from',$array)){
		        $ret .= $array['from'];
		        if(array_key_exists('to',$array)){
		            $ret .= '—';
		            $ret .= $array['to'];
		        }
		    }
	        return $ret;
	    }
	    else{
	        $ret .= $array;
	        return $ret;
	    }
	}
	
	function pre($prefix, $string){
	    if(strlen($string)>0){
	        return $prefix.$string;
	    }
	}
	
	function post($string, $postfix){
	    if(strlen($string)>0){
	        return $string.$postfix;
	    }
	}

	function b($string){
		return "<b>".$string."</b>";
	}
	
	function i($string){
		return "<i>".$string."</i>";
	}
	
	function sc($string){
		return "<span style=\"font-variant:small-caps\">".$string."</span>";
	}
	
	function smonth($month){
	    if(strlen($month)>0){
		    return  strftime('%b', strtotime('1970-'.$month . '-01')).'.';
		}
		else{
		    return "";
		}
	}
	
	function type($type){
		return $this->types[$type];
	}

}



?>
