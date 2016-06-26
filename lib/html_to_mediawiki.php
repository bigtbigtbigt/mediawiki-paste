<?php
#  https://en.wikibooks.org/wiki/MediaWiki_User_Guide/Text_Formatting
#  https://regex101.com/

#  Globals
$my_nl = '=N=';

#  Parse GET/POST
$html_input = (isset($_GET['html'])) ? $_GET['html'] : ((isset($_POST['html'])) ? $_POST['html'] : '');
$html_input = str_replace("\n",'',$html_input); # We like our HTML all on one line
$options['date_translate'] = (isset($_GET['date_translate'])) ? $_GET['date_translate'] : ((isset($_POST['date_translate'])) ? $_POST['date_translate'] : '');
$options['date_translate'] = ($options['date_translate'] == 'true') ? true : false;

#  The response (as JSON)
$json_response = array ( 'html' => $html_input, 'mediawiki' => html2wiki($html_input) );

#  Serve it up
header('Content-type: application/json');
echo json_encode($json_response);

function html2wiki($str){

  detect_options($str);

  $result = $str;

  $result = html2wiki_clean($result);
  $result = html2wiki_tables($result);
  $result = html2wiki_meta($result);
  $result = html2wiki_tags($result);
  $result = html2wiki_headers($result);
  $result = html2wiki_styles($result);
  $result = html2wiki_special($result);
  $result = html2wiki_clean($result);

  #  Options
  global $options;
  if ($options['date_translate']) $result = translate_dates($result);

  return trim($result);

}

#  Look at the input HTML string and try to determine helpful information for parsing
function detect_options($str) {

  global $options;

  if (strpos($str, 'class="mw-editsection"') !== false) $options['source'] = 'mediawiki';

}

#  Find anything matching MM-DD-YYYY, M/D/YYYY, MM/DD/YYYY, or M/D/YYYY and replace it with YYYY-MM-DD
function translate_dates($str) {

  $str = preg_replace_callback('/(\d{1,2})(-|\/)(\d{1,2})(-|\/)(\d{4})/', function ($matches) { return sprintf('%04d-%02d-%02d', $matches[5], $matches[1], $matches[3]); }, $str);
  //$str = preg_replace('/(\d{1,2})(-|\/)(\d{1,2})(-|\/)(\d{4})/', sprintf('%04d', '$5').'-'.sprintf('%02d', '$1').'-'.sprintf('%02d', '$3'), $str);
  //$str = preg_replace('/(\d{1,2})(-|\/)(\d{1,2})(-|\/)(\d{4})/', '$5-$1-$3', $str);

  return $str;

}

function attributes2wiki($str, $tag) {

        $dq = "_dq_";

        $str = str_replace('\"', $dq, $str);

        $html_tags = array(
            "/aria-.*\=$dq.*$dq/Ui",      # aria 
            "/border\=$dq.*$dq/Ui",       # border
            "/cellpadding\=$dq.*$dq/Ui",  # cellpadding 
            "/cellspacing\=$dq.*$dq/Ui",  # cellspacing
            "/class\=$dq.*$dq/Ui",        # class 
            "/data-.*\=$dq.*$dq/Ui",      # data
            "/dir\=$dq.*$dq/Ui",          # dir
            "/height\=$dq.*$dq/Ui",       # height
            "/id\=$dq.*$dq/Ui",           # id 
            "/nowrap\=$dq.*$dq/Ui",       # nowrap 
            "/role\=$dq.*$dq/Ui",         # role
            "/style\=$dq.*$dq/Ui",        # style 
            "/width\=$dq.*$dq/Ui",        # width
            "/$dq/",                 # double quote
        );

        $wiki_tags = array(
            "",              # aria
            "",              # border
            "",              # cellpadding
            "",              # cellspacing
            //(($tag == 'table') ? 'class="wikitable"' : ""),              # class 
            "",              # class 
            "",              # data
            "",              # dir
            "",              # height
            "",              # id 
            "",              # nowrap 
            "",              # role 
            "",              # style 
            "",              # width
            '"',        # double quote
        );

        # replace html tags with wiki equivalents
        $str = preg_replace($html_tags, $wiki_tags, $str);

        return $str;

}

function html2wiki_clean($str) {

        global $my_nl;
    
        $str = str_replace("\r", '', $str);
        $str = str_replace('  ', ' ', $str); # two spaces in a row
        $str = str_replace(' &nbsp;', ' ', $str); # two spaces in a row

        $html_tags = array(
                # since PHP 5 str_ireplace() can be used for the end tags
                "/\n/",
                '/>\s+</',         # spaces between tags
                "/\n$my_nl/",
                "/$my_nl/",
                "/\n */",             # spaces at beginning of a line
        );

        $wiki_tags = array(
                "\n",        # was " \n"
                '><',        # remove spaces between tags
                "\n",
                "\n",
                "\n",
        );

        # replace html tags with wiki equivalents
        $str = preg_replace($html_tags, $wiki_tags, $str);

        return $str;
}

function html2wiki_meta($str) {

  #TODO: Actually process the meta, read the name and content and store it in an object

  $str = str_replace("<meta charset=\"utf-8\">", '', $str);
  $str = preg_replace("/\<meta.*>/Ui", '', $str);

  return trim($str);

}

function html2wiki_tags($str) {

  # Catch all for html tags

  # Obliterate these tags entirely

  $str = preg_replace("/\<!--.*-->/Ui", '', $str); # comments 
  $str = preg_replace("/\<link.*>/Ui", '', $str); # link

  return trim($str);

} 

function html2wiki_headers($str) {

        global $my_nl;

        $str = str_replace("\r", '', $str);

        $html_tags = array(
                # since PHP 5 str_ireplace() can be used for the end tags
                "/\n/",
                '/>\s+</',         # spaces between tags
                '/<\/h1>/i',    # header end
                '/<\/h2>/i',    # header end
                '/<\/h3>/i',    # header end
                '/<\/h4>/i',    # header end
                '/<\/h5>/i',    # header end
                '/<\/h6>/i',    # header end
                '/<\/p>/i',     # paragraph end
                # e - replacement string gets evaluated before the replacement
                '/<h1([^>]*)>/i', # table start
                '/<h2([^>]*)>/i', # table start
                '/<h3([^>]*)>/i', # table start
                '/<h4([^>]*)>/i', # table start
                '/<h5([^>]*)>/i', # table start
                '/<h6([^>]*)>/i', # table start
                '/<p([^>]*)>/i',  # paragraph start
        );

        $wiki_tags = array(
                "\n",        # was " \n"
                '><',        # remove spaces between tags
                " =\n\n",            # header end
                " ==\n\n",           # header end
                " ===\n\n",          # header end
                " ====\n\n",         # header end
                " =====\n\n",        # header end
                " ======\n\n",       # header end
                "\n",              # paragraph end
                "= ",            # header start 
                "== ",           # header start 
                "=== ",          # header start 
                "==== ",         # header start 
                "===== ",        # header start 
                "====== ",       # header start 
                "\n\n",            # paragraph start
        );

        # replace html tags with wiki equivalents
        $str = preg_replace($html_tags, $wiki_tags, $str);

        return $str;

} 

function html2wiki_styles( $str ) {

        global $my_nl;
        
        # Fix non-bullets before doing bullets later
        $str = preg_replace("/^\* /", "<nowiki>* </nowiki>", $str);        # Asterisk at the start of the input
        $str = preg_replace("/\n\* /", "$my_nl<nowiki>* </nowiki>", $str); # Asterisk at the start of a new line
        
        $html_tags = array(
                '/\<style.*\/style\>/Uis', # css style blocks 
                '/<\/span>/i',     # span end
                '/<\/u>/i',     # underline end
                # e - replacement string gets evaluated before the replacement
                '/<span([^>]*)>/i',  # span start
                '/<u([^>]*)>/i',  # underline start
        );

        $wiki_tags = array(
                '',          # css style blocks 
                "",                 # span end
                "</u>",                 # underline end
                "",                 # span start 
                "<u>",                 # underline start 
        );

        # replace html tags with wiki equivalents
        $str = preg_replace($html_tags, $wiki_tags, $str);

        return $str;

}

function html2wiki_special( $str ) {

        global $options;

        if ($options['source'] == 'mediawiki') {
            # Remove [edit] links
            $str = preg_replace("/\[<a.*>edit<\/a>\]/Uis", "", $str);
        }

        return $str;

}

#  The script below is based on the script found at https://es.wikipedia.org/wiki/Usuario:Sanbec/html2wiki_tables.php
#
#############################################################################

#
#  HTML to Wiki Converter - tables
#  converts the HTML table tags into their wiki equivalents,
#  which were developed by Magnus Manske and are used in MediaWiki
#
#  Copyright (C) 2004 Borislav Manolov
#
#  This program is free software; you can redistribute it and/or
#  modify it under the terms of the GNU General Public License
#  as published by the Free Software Foundation; either version 2
#  of the License, or (at your option) any later version.
#
#  Author: Borislav Manolov <b.manolov at web.de>
#          http://purl.oclc.org/NET/manolov/
#############################################################################


# $str - the HTML markup
# $row_delim - number of dashes used for a row
# $oneline - use one-line markup for cells - ||
function html2wiki_tables($str, $row_delim = 1, $oneline = false) {

        global $my_nl;

        $html_tags = array(
                '/<\/table>/i',    # table end
                '/<\/caption>/i',  # caption end
                '/<\/tr>/i',       # rows end
                '/<\/tbody>/i',    # tbody end
                '/<\/thead>/i',    # thead headers end
                '/<\/th>/i',       # th headers end
                '/<\/td>/i',       # cells end
                '/<colgroup.*\/colgroup>/Uis', #colgroup
                # e - replacement string gets evaluated before the replacement
                '/<table([^>]*)>/ie', # table start
                '/<caption>/i',       # caption start
                '/<tr(.*)>/Uie',      # row start
                '/<tbody(.*)>/Uie',   # body start
                '/<thead(.*)>/Uie',   # thead header start
                '/<th(.*)>/Uie',      # th header start
                '/<td(.*)>/Uie',      # cell start
                "/\n/",               # new line
                "/$my_nl */",         # spaces at beginning of a line
                "/$my_nl$my_nl/",     # double new line
                "/$my_nl/",           # new line
        );

        $wiki_tags = array(
                "$my_nl|}",      # table end
                '',              # caption end
                '',              # rows end
                '',              # tbody end
                '',              # thead headers end
                '',              # th headers end
                '',              # cells end
                '',              # colgroup
                "'$my_nl{| class=\"wikitable\" '.trim(strip_newlines(attributes2wiki('$1', 'table')))",     # table start
                "$my_nl|+",      # caption
                "'$my_nl|'.str_repeat('-', $row_delim).' '.trim(strip_newlines(attributes2wiki('$1', 'tr')))", # rows
                "trim(strip_newlines(attributes2wiki('$1', 'tbody')))", # tbody 
                "trim(strip_newlines('$1'))", # thead headers
                "'$my_nl! '.trim(strip_newlines(attributes2wiki('$1', 'th'))).' | '", # th header start
                "'$my_nl| '.trim(strip_newlines(attributes2wiki('$1', 'td'))).' | '", # cell start
                "$my_nl",       # new line
                "$my_nl",       # spaces at beginning of a line
                "$my_nl",       # double new line
                "\n",           # new line
        );

        # replace html tags with wiki equivalents
        $str = preg_replace($html_tags, $wiki_tags, $str);

        # remove table row after table start
        $str = preg_replace("/\{\|(.*)\n\|-+ *\n/", "{|$1\n", $str);

        # clear phase
        $s = array('!  |', '|  |', '\\"');
        $r = array('!'   , '|'   ,   '"');
        $str = str_replace($s, $r, $str);

        # use one-line markup for cells
        if ($oneline) {
                $prevcell = false; # the previous row is a table cell
                $prevhead = false; # the previous row is a table header
                $pos = -1;
                while ( ($pos = strpos($str, "\n", $pos+1)) !== false ) { #echo "\n$str\n";
                switch ($str{$pos+1}) {
                        case '|': # cell start
                                if ($prevcell && $str{$pos+2} == ' ') {
                                        $str = substr_replace($str, ' |', $pos, 1); # s/\n/ |/
                                } else if ($str{$pos+2} == ' ') {
                                        $prevcell = true;
                                } else {
                                        $prevcell = false;
                                }
                                $prevhead = false;
                                break;
                        case '!': # header cell start
                                if ($prevhead) {
                                        $str = substr_replace($str, ' !', $pos, 1); # s/\n/ !/
                                } else {
                                        $prevhead = true;
                                }
                                $prevcell = false;
                                break;
                        case '{': # possible table start
                                if ($str{$pos+2} == '|') { # table start
                                        $prevcell = $prevhead = false;
                                } else {
                                        $str{$pos} = ' ';
                                }
                                break;
                        default: $str{$pos} = ' ';
                }
                }
        }
        return $str;
}

function strip_newlines($str) {
        return str_replace("\n", '', $str);
}
?>
