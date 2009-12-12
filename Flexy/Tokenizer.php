<?php
// This Package is based upon PEAR::HTML_Template_Flexy (ver 1.3.9 (stable) released on 2009-03-24)
//  Please visit http://pear.php.net/package/Html_Template_Flexy
//  
// +----------------------------------------------------------------------+
// | PHP Version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author:  Tomoaki Kosugi <kosugi@kips.gr.jp>                          |
// | Authors:  Alan Knowles <alan@akbkhome.com>                           |
// +----------------------------------------------------------------------+
//
// $Id: $
//
//  The Source Lex file. (Tokenizer.lex) and the Generated one (Tokenizer.php)
// You should always work with the .lex file and generate by
//
// #mono phpLex/phpLex.exe Tokenizer.lex
// The lexer is available at http://sourceforge.net/projects/php-sharp/
// 
// or the equivialant .NET runtime on windows...
//
//  Note need to change a few of these defines, and work out
// how to modifiy the lexer to handle the changes..
//
define('FLY_FLEXY_TOKEN_NONE',1);
define('FLY_FLEXY_TOKEN_OK',2);
define('FLY_FLEXY_TOKEN_ERROR',3);
define("YYINITIAL"     ,0);
define("IN_SINGLEQUOTE"     ,   1) ;
define("IN_TAG"     ,           2)  ;
define("IN_ATTR"     ,          3);
define("IN_ATTRVAL"     ,       4) ;
define("IN_NETDATA"     ,       5);
define("IN_ENDTAG"     ,        6);
define("IN_DOUBLEQUOTE"     ,   7);
define("IN_MD"     ,            8);
define("IN_COM"     ,           9);
define("IN_DS",                 10);
define("IN_FLEXYMETHOD"     ,   11);
define("IN_FLEXYMETHODQUOTED"  ,12);
define("IN_FLEXYMETHODQUOTED_END" ,13);
define("IN_SCRIPT",             14);
define("IN_CDATA"     ,         15);
define("IN_DSCOM",              16);
define("IN_PHP",                17);
define("IN_COMSTYLE"     ,      18);
define("IN_METHODCHAIN", 19);
define('YY_E_INTERNAL', 0);
define('YY_E_MATCH',  1);
define('YY_BUFFER_SIZE', 4096);
define('YY_F' , -1);
define('YY_NO_STATE', -1);
define('YY_NOT_ACCEPT' ,  0);
define('YY_START' , 1);
define('YY_END' , 2);
define('YY_NO_ANCHOR' , 4);
define('YY_BOL' , 257);
define('YY_EOF' , 258);


class Fly_Flexy_Tokenizer
{

    /**
    * options array : meanings:
    *    ignore_html - return all tags as  text tokens
    *
    *
    * @var      boolean  public
    * @access   public
    */
    var $options = array(
        'ignore_html' => false,
        'token_factory'  => array('Fly_Flexy_Token','factory'),
    );
    /**
    * flag if inside a style tag. (so comments are ignored.. )
    *
    * @var boolean
    * @access private
    */
    var $inStyle = false;
    /**
    * the start position of a cdata block
    *
    * @var int
    * @access private
    */
    var $yyCdataBegin = 0;
     /**
    * the start position of a comment block
    *
    * @var int
    * @access private
    */
    var $yyCommentBegin = 0;
    /**
    * the name of the file being parsed (used by error messages)
    *
    * @var string
    * @access public
    */
    var $fileName;
    /**
    * the string containing an error if it occurs..
    *
    * @var string
    * @access public
    */
    var $error;
    /**
    * Flexible constructor
    *
    * @param   string       string to tokenize
    * @param   array        options array (see options above)       
    * 
    *
    * @return   Fly_Flexy_Tokenizer
    * @access   public
    */
    function &construct($data,$options= array()) 
    {
        $t = new Fly_Flexy_Tokenizer($data);
        foreach($options as $k=>$v) {
            if (is_object($v) || is_array($v)) {
                $t->options[$k] = &$v;
                continue;
            }
            $t->options[$k] = $v;
        }
        return $t;
    }
    /**
    * raise an error: = return an error token and set the error variable.
    *
    * 
    * @param   string           Error type
    * @param   string           Full Error message
    * @param   boolean          is it fatal..
    *
    * @return   int the error token.
    * @access   public
    */
    function raiseError($s,$n='',$isFatal=false) 
    {
        $this->error = "ERROR $n in File {$this->fileName} on Line {$this->yyline} Position:{$this->yy_buffer_end}: $s\n";
        return FLY_FLEXY_TOKEN_ERROR;
    }
    /**
    * return text
    *
    * Used mostly by the ignore HTML code. - really a macro :)
    *
    * @return   int   token ok.
    * @access   public
    */
    function returnSimple() 
    {
        $this->value = $this->createToken('TextSimple');
        return FLY_FLEXY_TOKEN_OK;
    }
    /**
    * Create a token based on the value of $this->options['token_call']
    *
    *
    * @return   Object   some kind of token..
    * @access   public
    */
    function createToken($token, $value = false, $line = false, $charPos = false) 
    {
        if ($value === false) {
            $value = $this->yytext();
        }
        if ($line === false) {
            $line = $this->yyline;
        }
        if ($charPos === false) {
            $charPos = $this->yy_buffer_start;
        }
        return call_user_func_array($this->options['token_factory'],array($token,$value,$line,$charPos));
    }


    var $yy_reader;
    var $yy_buffer_index;
    var $yy_buffer_read;
    var $yy_buffer_start;
    var $_fatal = false;
    var $yy_buffer_end;
    var $yy_buffer;
    var $yychar;
    var $yyline;
    var $yyEndOfLine;
    var $yy_at_bol;
    var $yy_lexical_state;

    function Fly_Flexy_Tokenizer($data) 
    {
        $this->yy_buffer = $data;
        $this->yy_buffer_read = strlen($data);
        $this->yy_buffer_index = 0;
        $this->yy_buffer_start = 0;
        $this->yy_buffer_end = 0;
        $this->yychar = 0;
        $this->yyline = 0;
        $this->yy_at_bol = true;
        $this->yy_lexical_state = YYINITIAL;
    }

    var $yy_state_dtrans = array  ( 
        0,
        293,
        38,
        140,
        318,
        319,
        320,
        321,
        58,
        326,
        329,
        331,
        353,
        367,
        368,
        376,
        88,
        90,
        92,
        414
    );


    function yybegin ($state)
    {
        $this->yy_lexical_state = $state;
    }



    function yy_advance ()
    {
        if ($this->yy_buffer_index < $this->yy_buffer_read) {
            return ord($this->yy_buffer{$this->yy_buffer_index++});
        }
        return YY_EOF;
    }


    function yy_move_end ()
    {
        if ($this->yy_buffer_end > $this->yy_buffer_start && 
            '\n' == $this->yy_buffer{$this->yy_buffer_end-1})
        {
            $this->yy_buffer_end--;
        }
        if ($this->yy_buffer_end > $this->yy_buffer_start &&
            '\r' == $this->yy_buffer{$this->yy_buffer_end-1})
        {
            $this->yy_buffer_end--;
        }
    }


    var $yy_last_was_cr=false;


    function yy_mark_start ()
    {
        for ($i = $this->yy_buffer_start; $i < $this->yy_buffer_index; $i++) {
            if ($this->yy_buffer{$i} == "\n" && !$this->yy_last_was_cr) {
                $this->yyline++; $this->yyEndOfLine = $this->yychar;
            }
            if ($this->yy_buffer{$i} == "\r") {
                $this->yyline++; $this->yyEndOfLine = $this->yychar;
                $this->yy_last_was_cr=true;
            } else {
                $this->yy_last_was_cr=false;
            }
        }
        $this->yychar = $this->yychar + $this->yy_buffer_index - $this->yy_buffer_start;
        $this->yy_buffer_start = $this->yy_buffer_index;
    }


    function yy_mark_end ()
    {
        $this->yy_buffer_end = $this->yy_buffer_index;
    }


    function  yy_to_mark ()
    {
        $this->yy_buffer_index = $this->yy_buffer_end;
        $this->yy_at_bol = ($this->yy_buffer_end > $this->yy_buffer_start) &&
            ($this->yy_buffer{$this->yy_buffer_end-1} == '\r' ||
            $this->yy_buffer{$this->yy_buffer_end-1} == '\n');
    }


    function yytext()
    {
        return substr($this->yy_buffer,$this->yy_buffer_start,$this->yy_buffer_end - $this->yy_buffer_start);
    }


    function yylength ()
    {
        return $this->yy_buffer_end - $this->yy_buffer_start;
    }


    var $yy_error_string = array(
        "Error: Internal error.\n",
        "Error: Unmatched input - \""
        );


    function yy_error ($code,$fatal)
    {
        if (method_exists($this,'raiseError')) { 
	        $this->_fatal = $fatal;
            $msg = $this->yy_error_string[$code];
            if ($code == 1) {
                $msg .= $this->yy_buffer[$this->yy_buffer_start] . "\"";
            }
 		    return $this->raiseError($msg, $code, $fatal); 
 		}
        echo $this->yy_error_string[$code];
        if ($fatal) {
            exit;
        }
    }


    var  $yy_acpt = array (
        /* 0 */   YY_NOT_ACCEPT,
        /* 1 */   YY_NO_ANCHOR,
        /* 2 */   YY_NO_ANCHOR,
        /* 3 */   YY_NO_ANCHOR,
        /* 4 */   YY_NO_ANCHOR,
        /* 5 */   YY_NO_ANCHOR,
        /* 6 */   YY_NO_ANCHOR,
        /* 7 */   YY_NO_ANCHOR,
        /* 8 */   YY_NO_ANCHOR,
        /* 9 */   YY_NO_ANCHOR,
        /* 10 */   YY_NO_ANCHOR,
        /* 11 */   YY_NO_ANCHOR,
        /* 12 */   YY_NO_ANCHOR,
        /* 13 */   YY_NO_ANCHOR,
        /* 14 */   YY_NO_ANCHOR,
        /* 15 */   YY_NO_ANCHOR,
        /* 16 */   YY_NO_ANCHOR,
        /* 17 */   YY_NO_ANCHOR,
        /* 18 */   YY_NO_ANCHOR,
        /* 19 */   YY_NO_ANCHOR,
        /* 20 */   YY_NO_ANCHOR,
        /* 21 */   YY_NO_ANCHOR,
        /* 22 */   YY_NO_ANCHOR,
        /* 23 */   YY_NO_ANCHOR,
        /* 24 */   YY_NO_ANCHOR,
        /* 25 */   YY_NO_ANCHOR,
        /* 26 */   YY_NO_ANCHOR,
        /* 27 */   YY_NO_ANCHOR,
        /* 28 */   YY_NO_ANCHOR,
        /* 29 */   YY_NO_ANCHOR,
        /* 30 */   YY_NO_ANCHOR,
        /* 31 */   YY_NO_ANCHOR,
        /* 32 */   YY_NO_ANCHOR,
        /* 33 */   YY_NO_ANCHOR,
        /* 34 */   YY_NO_ANCHOR,
        /* 35 */   YY_NO_ANCHOR,
        /* 36 */   YY_NO_ANCHOR,
        /* 37 */   YY_NO_ANCHOR,
        /* 38 */   YY_NO_ANCHOR,
        /* 39 */   YY_NO_ANCHOR,
        /* 40 */   YY_NO_ANCHOR,
        /* 41 */   YY_NO_ANCHOR,
        /* 42 */   YY_NO_ANCHOR,
        /* 43 */   YY_NO_ANCHOR,
        /* 44 */   YY_NO_ANCHOR,
        /* 45 */   YY_NO_ANCHOR,
        /* 46 */   YY_NO_ANCHOR,
        /* 47 */   YY_NO_ANCHOR,
        /* 48 */   YY_NO_ANCHOR,
        /* 49 */   YY_NO_ANCHOR,
        /* 50 */   YY_NO_ANCHOR,
        /* 51 */   YY_NO_ANCHOR,
        /* 52 */   YY_NO_ANCHOR,
        /* 53 */   YY_NO_ANCHOR,
        /* 54 */   YY_NO_ANCHOR,
        /* 55 */   YY_NO_ANCHOR,
        /* 56 */   YY_NO_ANCHOR,
        /* 57 */   YY_NO_ANCHOR,
        /* 58 */   YY_NO_ANCHOR,
        /* 59 */   YY_NO_ANCHOR,
        /* 60 */   YY_NO_ANCHOR,
        /* 61 */   YY_NO_ANCHOR,
        /* 62 */   YY_NO_ANCHOR,
        /* 63 */   YY_NO_ANCHOR,
        /* 64 */   YY_NO_ANCHOR,
        /* 65 */   YY_NO_ANCHOR,
        /* 66 */   YY_NO_ANCHOR,
        /* 67 */   YY_NO_ANCHOR,
        /* 68 */   YY_NO_ANCHOR,
        /* 69 */   YY_NO_ANCHOR,
        /* 70 */   YY_NO_ANCHOR,
        /* 71 */   YY_NO_ANCHOR,
        /* 72 */   YY_NO_ANCHOR,
        /* 73 */   YY_NO_ANCHOR,
        /* 74 */   YY_NO_ANCHOR,
        /* 75 */   YY_NO_ANCHOR,
        /* 76 */   YY_NO_ANCHOR,
        /* 77 */   YY_NO_ANCHOR,
        /* 78 */   YY_NO_ANCHOR,
        /* 79 */   YY_NO_ANCHOR,
        /* 80 */   YY_NO_ANCHOR,
        /* 81 */   YY_NO_ANCHOR,
        /* 82 */   YY_NO_ANCHOR,
        /* 83 */   YY_NO_ANCHOR,
        /* 84 */   YY_NO_ANCHOR,
        /* 85 */   YY_NO_ANCHOR,
        /* 86 */   YY_NO_ANCHOR,
        /* 87 */   YY_NO_ANCHOR,
        /* 88 */   YY_NO_ANCHOR,
        /* 89 */   YY_NO_ANCHOR,
        /* 90 */   YY_NO_ANCHOR,
        /* 91 */   YY_NO_ANCHOR,
        /* 92 */   YY_NO_ANCHOR,
        /* 93 */   YY_NO_ANCHOR,
        /* 94 */   YY_NO_ANCHOR,
        /* 95 */   YY_NO_ANCHOR,
        /* 96 */   YY_NO_ANCHOR,
        /* 97 */   YY_NO_ANCHOR,
        /* 98 */   YY_NO_ANCHOR,
        /* 99 */   YY_NOT_ACCEPT,
        /* 100 */   YY_NO_ANCHOR,
        /* 101 */   YY_NO_ANCHOR,
        /* 102 */   YY_NO_ANCHOR,
        /* 103 */   YY_NO_ANCHOR,
        /* 104 */   YY_NO_ANCHOR,
        /* 105 */   YY_NO_ANCHOR,
        /* 106 */   YY_NO_ANCHOR,
        /* 107 */   YY_NO_ANCHOR,
        /* 108 */   YY_NO_ANCHOR,
        /* 109 */   YY_NO_ANCHOR,
        /* 110 */   YY_NO_ANCHOR,
        /* 111 */   YY_NO_ANCHOR,
        /* 112 */   YY_NO_ANCHOR,
        /* 113 */   YY_NO_ANCHOR,
        /* 114 */   YY_NO_ANCHOR,
        /* 115 */   YY_NO_ANCHOR,
        /* 116 */   YY_NO_ANCHOR,
        /* 117 */   YY_NO_ANCHOR,
        /* 118 */   YY_NO_ANCHOR,
        /* 119 */   YY_NO_ANCHOR,
        /* 120 */   YY_NO_ANCHOR,
        /* 121 */   YY_NO_ANCHOR,
        /* 122 */   YY_NO_ANCHOR,
        /* 123 */   YY_NO_ANCHOR,
        /* 124 */   YY_NO_ANCHOR,
        /* 125 */   YY_NO_ANCHOR,
        /* 126 */   YY_NO_ANCHOR,
        /* 127 */   YY_NO_ANCHOR,
        /* 128 */   YY_NO_ANCHOR,
        /* 129 */   YY_NO_ANCHOR,
        /* 130 */   YY_NO_ANCHOR,
        /* 131 */   YY_NO_ANCHOR,
        /* 132 */   YY_NO_ANCHOR,
        /* 133 */   YY_NO_ANCHOR,
        /* 134 */   YY_NO_ANCHOR,
        /* 135 */   YY_NO_ANCHOR,
        /* 136 */   YY_NOT_ACCEPT,
        /* 137 */   YY_NO_ANCHOR,
        /* 138 */   YY_NO_ANCHOR,
        /* 139 */   YY_NO_ANCHOR,
        /* 140 */   YY_NO_ANCHOR,
        /* 141 */   YY_NO_ANCHOR,
        /* 142 */   YY_NO_ANCHOR,
        /* 143 */   YY_NO_ANCHOR,
        /* 144 */   YY_NO_ANCHOR,
        /* 145 */   YY_NO_ANCHOR,
        /* 146 */   YY_NO_ANCHOR,
        /* 147 */   YY_NO_ANCHOR,
        /* 148 */   YY_NOT_ACCEPT,
        /* 149 */   YY_NO_ANCHOR,
        /* 150 */   YY_NO_ANCHOR,
        /* 151 */   YY_NO_ANCHOR,
        /* 152 */   YY_NO_ANCHOR,
        /* 153 */   YY_NO_ANCHOR,
        /* 154 */   YY_NO_ANCHOR,
        /* 155 */   YY_NOT_ACCEPT,
        /* 156 */   YY_NO_ANCHOR,
        /* 157 */   YY_NO_ANCHOR,
        /* 158 */   YY_NOT_ACCEPT,
        /* 159 */   YY_NO_ANCHOR,
        /* 160 */   YY_NOT_ACCEPT,
        /* 161 */   YY_NO_ANCHOR,
        /* 162 */   YY_NOT_ACCEPT,
        /* 163 */   YY_NO_ANCHOR,
        /* 164 */   YY_NOT_ACCEPT,
        /* 165 */   YY_NO_ANCHOR,
        /* 166 */   YY_NOT_ACCEPT,
        /* 167 */   YY_NO_ANCHOR,
        /* 168 */   YY_NOT_ACCEPT,
        /* 169 */   YY_NO_ANCHOR,
        /* 170 */   YY_NOT_ACCEPT,
        /* 171 */   YY_NO_ANCHOR,
        /* 172 */   YY_NOT_ACCEPT,
        /* 173 */   YY_NO_ANCHOR,
        /* 174 */   YY_NOT_ACCEPT,
        /* 175 */   YY_NO_ANCHOR,
        /* 176 */   YY_NOT_ACCEPT,
        /* 177 */   YY_NOT_ACCEPT,
        /* 178 */   YY_NOT_ACCEPT,
        /* 179 */   YY_NOT_ACCEPT,
        /* 180 */   YY_NOT_ACCEPT,
        /* 181 */   YY_NOT_ACCEPT,
        /* 182 */   YY_NOT_ACCEPT,
        /* 183 */   YY_NOT_ACCEPT,
        /* 184 */   YY_NOT_ACCEPT,
        /* 185 */   YY_NOT_ACCEPT,
        /* 186 */   YY_NOT_ACCEPT,
        /* 187 */   YY_NOT_ACCEPT,
        /* 188 */   YY_NOT_ACCEPT,
        /* 189 */   YY_NOT_ACCEPT,
        /* 190 */   YY_NOT_ACCEPT,
        /* 191 */   YY_NOT_ACCEPT,
        /* 192 */   YY_NOT_ACCEPT,
        /* 193 */   YY_NOT_ACCEPT,
        /* 194 */   YY_NOT_ACCEPT,
        /* 195 */   YY_NOT_ACCEPT,
        /* 196 */   YY_NOT_ACCEPT,
        /* 197 */   YY_NOT_ACCEPT,
        /* 198 */   YY_NOT_ACCEPT,
        /* 199 */   YY_NOT_ACCEPT,
        /* 200 */   YY_NOT_ACCEPT,
        /* 201 */   YY_NOT_ACCEPT,
        /* 202 */   YY_NOT_ACCEPT,
        /* 203 */   YY_NOT_ACCEPT,
        /* 204 */   YY_NOT_ACCEPT,
        /* 205 */   YY_NOT_ACCEPT,
        /* 206 */   YY_NOT_ACCEPT,
        /* 207 */   YY_NOT_ACCEPT,
        /* 208 */   YY_NOT_ACCEPT,
        /* 209 */   YY_NOT_ACCEPT,
        /* 210 */   YY_NOT_ACCEPT,
        /* 211 */   YY_NOT_ACCEPT,
        /* 212 */   YY_NOT_ACCEPT,
        /* 213 */   YY_NOT_ACCEPT,
        /* 214 */   YY_NOT_ACCEPT,
        /* 215 */   YY_NOT_ACCEPT,
        /* 216 */   YY_NOT_ACCEPT,
        /* 217 */   YY_NOT_ACCEPT,
        /* 218 */   YY_NOT_ACCEPT,
        /* 219 */   YY_NOT_ACCEPT,
        /* 220 */   YY_NOT_ACCEPT,
        /* 221 */   YY_NOT_ACCEPT,
        /* 222 */   YY_NOT_ACCEPT,
        /* 223 */   YY_NOT_ACCEPT,
        /* 224 */   YY_NOT_ACCEPT,
        /* 225 */   YY_NOT_ACCEPT,
        /* 226 */   YY_NOT_ACCEPT,
        /* 227 */   YY_NOT_ACCEPT,
        /* 228 */   YY_NOT_ACCEPT,
        /* 229 */   YY_NOT_ACCEPT,
        /* 230 */   YY_NOT_ACCEPT,
        /* 231 */   YY_NOT_ACCEPT,
        /* 232 */   YY_NOT_ACCEPT,
        /* 233 */   YY_NOT_ACCEPT,
        /* 234 */   YY_NOT_ACCEPT,
        /* 235 */   YY_NOT_ACCEPT,
        /* 236 */   YY_NOT_ACCEPT,
        /* 237 */   YY_NOT_ACCEPT,
        /* 238 */   YY_NOT_ACCEPT,
        /* 239 */   YY_NOT_ACCEPT,
        /* 240 */   YY_NOT_ACCEPT,
        /* 241 */   YY_NOT_ACCEPT,
        /* 242 */   YY_NOT_ACCEPT,
        /* 243 */   YY_NOT_ACCEPT,
        /* 244 */   YY_NOT_ACCEPT,
        /* 245 */   YY_NOT_ACCEPT,
        /* 246 */   YY_NOT_ACCEPT,
        /* 247 */   YY_NOT_ACCEPT,
        /* 248 */   YY_NOT_ACCEPT,
        /* 249 */   YY_NOT_ACCEPT,
        /* 250 */   YY_NOT_ACCEPT,
        /* 251 */   YY_NOT_ACCEPT,
        /* 252 */   YY_NOT_ACCEPT,
        /* 253 */   YY_NOT_ACCEPT,
        /* 254 */   YY_NOT_ACCEPT,
        /* 255 */   YY_NOT_ACCEPT,
        /* 256 */   YY_NOT_ACCEPT,
        /* 257 */   YY_NOT_ACCEPT,
        /* 258 */   YY_NOT_ACCEPT,
        /* 259 */   YY_NOT_ACCEPT,
        /* 260 */   YY_NOT_ACCEPT,
        /* 261 */   YY_NOT_ACCEPT,
        /* 262 */   YY_NOT_ACCEPT,
        /* 263 */   YY_NOT_ACCEPT,
        /* 264 */   YY_NOT_ACCEPT,
        /* 265 */   YY_NOT_ACCEPT,
        /* 266 */   YY_NOT_ACCEPT,
        /* 267 */   YY_NOT_ACCEPT,
        /* 268 */   YY_NOT_ACCEPT,
        /* 269 */   YY_NOT_ACCEPT,
        /* 270 */   YY_NOT_ACCEPT,
        /* 271 */   YY_NOT_ACCEPT,
        /* 272 */   YY_NOT_ACCEPT,
        /* 273 */   YY_NOT_ACCEPT,
        /* 274 */   YY_NOT_ACCEPT,
        /* 275 */   YY_NOT_ACCEPT,
        /* 276 */   YY_NOT_ACCEPT,
        /* 277 */   YY_NOT_ACCEPT,
        /* 278 */   YY_NOT_ACCEPT,
        /* 279 */   YY_NOT_ACCEPT,
        /* 280 */   YY_NOT_ACCEPT,
        /* 281 */   YY_NOT_ACCEPT,
        /* 282 */   YY_NOT_ACCEPT,
        /* 283 */   YY_NOT_ACCEPT,
        /* 284 */   YY_NOT_ACCEPT,
        /* 285 */   YY_NOT_ACCEPT,
        /* 286 */   YY_NOT_ACCEPT,
        /* 287 */   YY_NOT_ACCEPT,
        /* 288 */   YY_NOT_ACCEPT,
        /* 289 */   YY_NOT_ACCEPT,
        /* 290 */   YY_NOT_ACCEPT,
        /* 291 */   YY_NOT_ACCEPT,
        /* 292 */   YY_NOT_ACCEPT,
        /* 293 */   YY_NOT_ACCEPT,
        /* 294 */   YY_NOT_ACCEPT,
        /* 295 */   YY_NOT_ACCEPT,
        /* 296 */   YY_NOT_ACCEPT,
        /* 297 */   YY_NOT_ACCEPT,
        /* 298 */   YY_NOT_ACCEPT,
        /* 299 */   YY_NOT_ACCEPT,
        /* 300 */   YY_NOT_ACCEPT,
        /* 301 */   YY_NOT_ACCEPT,
        /* 302 */   YY_NOT_ACCEPT,
        /* 303 */   YY_NOT_ACCEPT,
        /* 304 */   YY_NOT_ACCEPT,
        /* 305 */   YY_NOT_ACCEPT,
        /* 306 */   YY_NOT_ACCEPT,
        /* 307 */   YY_NOT_ACCEPT,
        /* 308 */   YY_NOT_ACCEPT,
        /* 309 */   YY_NOT_ACCEPT,
        /* 310 */   YY_NOT_ACCEPT,
        /* 311 */   YY_NOT_ACCEPT,
        /* 312 */   YY_NOT_ACCEPT,
        /* 313 */   YY_NOT_ACCEPT,
        /* 314 */   YY_NOT_ACCEPT,
        /* 315 */   YY_NOT_ACCEPT,
        /* 316 */   YY_NOT_ACCEPT,
        /* 317 */   YY_NOT_ACCEPT,
        /* 318 */   YY_NOT_ACCEPT,
        /* 319 */   YY_NOT_ACCEPT,
        /* 320 */   YY_NOT_ACCEPT,
        /* 321 */   YY_NOT_ACCEPT,
        /* 322 */   YY_NOT_ACCEPT,
        /* 323 */   YY_NOT_ACCEPT,
        /* 324 */   YY_NOT_ACCEPT,
        /* 325 */   YY_NOT_ACCEPT,
        /* 326 */   YY_NOT_ACCEPT,
        /* 327 */   YY_NOT_ACCEPT,
        /* 328 */   YY_NOT_ACCEPT,
        /* 329 */   YY_NOT_ACCEPT,
        /* 330 */   YY_NOT_ACCEPT,
        /* 331 */   YY_NOT_ACCEPT,
        /* 332 */   YY_NOT_ACCEPT,
        /* 333 */   YY_NOT_ACCEPT,
        /* 334 */   YY_NOT_ACCEPT,
        /* 335 */   YY_NOT_ACCEPT,
        /* 336 */   YY_NOT_ACCEPT,
        /* 337 */   YY_NOT_ACCEPT,
        /* 338 */   YY_NOT_ACCEPT,
        /* 339 */   YY_NOT_ACCEPT,
        /* 340 */   YY_NOT_ACCEPT,
        /* 341 */   YY_NOT_ACCEPT,
        /* 342 */   YY_NOT_ACCEPT,
        /* 343 */   YY_NOT_ACCEPT,
        /* 344 */   YY_NOT_ACCEPT,
        /* 345 */   YY_NOT_ACCEPT,
        /* 346 */   YY_NOT_ACCEPT,
        /* 347 */   YY_NOT_ACCEPT,
        /* 348 */   YY_NOT_ACCEPT,
        /* 349 */   YY_NOT_ACCEPT,
        /* 350 */   YY_NOT_ACCEPT,
        /* 351 */   YY_NOT_ACCEPT,
        /* 352 */   YY_NOT_ACCEPT,
        /* 353 */   YY_NOT_ACCEPT,
        /* 354 */   YY_NOT_ACCEPT,
        /* 355 */   YY_NOT_ACCEPT,
        /* 356 */   YY_NOT_ACCEPT,
        /* 357 */   YY_NOT_ACCEPT,
        /* 358 */   YY_NOT_ACCEPT,
        /* 359 */   YY_NOT_ACCEPT,
        /* 360 */   YY_NOT_ACCEPT,
        /* 361 */   YY_NOT_ACCEPT,
        /* 362 */   YY_NOT_ACCEPT,
        /* 363 */   YY_NOT_ACCEPT,
        /* 364 */   YY_NOT_ACCEPT,
        /* 365 */   YY_NOT_ACCEPT,
        /* 366 */   YY_NOT_ACCEPT,
        /* 367 */   YY_NOT_ACCEPT,
        /* 368 */   YY_NOT_ACCEPT,
        /* 369 */   YY_NOT_ACCEPT,
        /* 370 */   YY_NOT_ACCEPT,
        /* 371 */   YY_NOT_ACCEPT,
        /* 372 */   YY_NOT_ACCEPT,
        /* 373 */   YY_NOT_ACCEPT,
        /* 374 */   YY_NOT_ACCEPT,
        /* 375 */   YY_NOT_ACCEPT,
        /* 376 */   YY_NOT_ACCEPT,
        /* 377 */   YY_NOT_ACCEPT,
        /* 378 */   YY_NOT_ACCEPT,
        /* 379 */   YY_NOT_ACCEPT,
        /* 380 */   YY_NOT_ACCEPT,
        /* 381 */   YY_NOT_ACCEPT,
        /* 382 */   YY_NOT_ACCEPT,
        /* 383 */   YY_NOT_ACCEPT,
        /* 384 */   YY_NOT_ACCEPT,
        /* 385 */   YY_NOT_ACCEPT,
        /* 386 */   YY_NOT_ACCEPT,
        /* 387 */   YY_NOT_ACCEPT,
        /* 388 */   YY_NOT_ACCEPT,
        /* 389 */   YY_NOT_ACCEPT,
        /* 390 */   YY_NOT_ACCEPT,
        /* 391 */   YY_NOT_ACCEPT,
        /* 392 */   YY_NOT_ACCEPT,
        /* 393 */   YY_NOT_ACCEPT,
        /* 394 */   YY_NOT_ACCEPT,
        /* 395 */   YY_NOT_ACCEPT,
        /* 396 */   YY_NOT_ACCEPT,
        /* 397 */   YY_NOT_ACCEPT,
        /* 398 */   YY_NOT_ACCEPT,
        /* 399 */   YY_NOT_ACCEPT,
        /* 400 */   YY_NOT_ACCEPT,
        /* 401 */   YY_NOT_ACCEPT,
        /* 402 */   YY_NOT_ACCEPT,
        /* 403 */   YY_NOT_ACCEPT,
        /* 404 */   YY_NOT_ACCEPT,
        /* 405 */   YY_NOT_ACCEPT,
        /* 406 */   YY_NOT_ACCEPT,
        /* 407 */   YY_NOT_ACCEPT,
        /* 408 */   YY_NOT_ACCEPT,
        /* 409 */   YY_NOT_ACCEPT,
        /* 410 */   YY_NOT_ACCEPT,
        /* 411 */   YY_NOT_ACCEPT,
        /* 412 */   YY_NOT_ACCEPT,
        /* 413 */   YY_NOT_ACCEPT,
        /* 414 */   YY_NOT_ACCEPT,
        /* 415 */   YY_NOT_ACCEPT,
        /* 416 */   YY_NOT_ACCEPT,
        /* 417 */   YY_NOT_ACCEPT,
        /* 418 */   YY_NOT_ACCEPT,
        /* 419 */   YY_NOT_ACCEPT,
        /* 420 */   YY_NOT_ACCEPT,
        /* 421 */   YY_NOT_ACCEPT,
        /* 422 */   YY_NOT_ACCEPT,
        /* 423 */   YY_NOT_ACCEPT,
        /* 424 */   YY_NOT_ACCEPT,
        /* 425 */   YY_NOT_ACCEPT,
        /* 426 */   YY_NOT_ACCEPT,
        /* 427 */   YY_NOT_ACCEPT,
        /* 428 */   YY_NOT_ACCEPT,
        /* 429 */   YY_NOT_ACCEPT,
        /* 430 */   YY_NO_ANCHOR,
        /* 431 */   YY_NO_ANCHOR,
        /* 432 */   YY_NO_ANCHOR,
        /* 433 */   YY_NO_ANCHOR,
        /* 434 */   YY_NOT_ACCEPT,
        /* 435 */   YY_NOT_ACCEPT,
        /* 436 */   YY_NOT_ACCEPT,
        /* 437 */   YY_NOT_ACCEPT,
        /* 438 */   YY_NOT_ACCEPT,
        /* 439 */   YY_NOT_ACCEPT,
        /* 440 */   YY_NOT_ACCEPT,
        /* 441 */   YY_NOT_ACCEPT,
        /* 442 */   YY_NOT_ACCEPT,
        /* 443 */   YY_NOT_ACCEPT,
        /* 444 */   YY_NOT_ACCEPT,
        /* 445 */   YY_NOT_ACCEPT,
        /* 446 */   YY_NOT_ACCEPT,
        /* 447 */   YY_NOT_ACCEPT,
        /* 448 */   YY_NOT_ACCEPT,
        /* 449 */   YY_NOT_ACCEPT,
        /* 450 */   YY_NOT_ACCEPT,
        /* 451 */   YY_NOT_ACCEPT,
        /* 452 */   YY_NOT_ACCEPT,
        /* 453 */   YY_NOT_ACCEPT,
        /* 454 */   YY_NOT_ACCEPT,
        /* 455 */   YY_NOT_ACCEPT,
        /* 456 */   YY_NOT_ACCEPT,
        /* 457 */   YY_NOT_ACCEPT,
        /* 458 */   YY_NOT_ACCEPT,
        /* 459 */   YY_NOT_ACCEPT,
        /* 460 */   YY_NOT_ACCEPT,
        /* 461 */   YY_NOT_ACCEPT,
        /* 462 */   YY_NOT_ACCEPT,
        /* 463 */   YY_NOT_ACCEPT,
        /* 464 */   YY_NOT_ACCEPT,
        /* 465 */   YY_NOT_ACCEPT,
        /* 466 */   YY_NOT_ACCEPT,
        /* 467 */   YY_NOT_ACCEPT,
        /* 468 */   YY_NOT_ACCEPT,
        /* 469 */   YY_NOT_ACCEPT,
        /* 470 */   YY_NOT_ACCEPT,
        /* 471 */   YY_NOT_ACCEPT,
        /* 472 */   YY_NOT_ACCEPT,
        /* 473 */   YY_NOT_ACCEPT,
        /* 474 */   YY_NOT_ACCEPT,
        /* 475 */   YY_NOT_ACCEPT,
        /* 476 */   YY_NOT_ACCEPT,
        /* 477 */   YY_NOT_ACCEPT,
        /* 478 */   YY_NOT_ACCEPT,
        /* 479 */   YY_NOT_ACCEPT,
        /* 480 */   YY_NOT_ACCEPT,
        /* 481 */   YY_NOT_ACCEPT,
        /* 482 */   YY_NOT_ACCEPT,
        /* 483 */   YY_NOT_ACCEPT,
        /* 484 */   YY_NOT_ACCEPT,
        /* 485 */   YY_NOT_ACCEPT,
        /* 486 */   YY_NOT_ACCEPT,
        /* 487 */   YY_NOT_ACCEPT,
        /* 488 */   YY_NOT_ACCEPT,
        /* 489 */   YY_NOT_ACCEPT,
        /* 490 */   YY_NOT_ACCEPT,
        /* 491 */   YY_NOT_ACCEPT,
        /* 492 */   YY_NOT_ACCEPT,
        /* 493 */   YY_NOT_ACCEPT,
        /* 494 */   YY_NOT_ACCEPT,
        /* 495 */   YY_NOT_ACCEPT,
        /* 496 */   YY_NOT_ACCEPT,
        /* 497 */   YY_NOT_ACCEPT,
        /* 498 */   YY_NOT_ACCEPT,
        /* 499 */   YY_NOT_ACCEPT,
        /* 500 */   YY_NOT_ACCEPT,
        /* 501 */   YY_NOT_ACCEPT,
        /* 502 */   YY_NOT_ACCEPT,
        /* 503 */   YY_NOT_ACCEPT,
        /* 504 */   YY_NOT_ACCEPT,
        /* 505 */   YY_NOT_ACCEPT,
        /* 506 */   YY_NOT_ACCEPT,
        /* 507 */   YY_NOT_ACCEPT,
        /* 508 */   YY_NOT_ACCEPT,
        /* 509 */   YY_NOT_ACCEPT,
        /* 510 */   YY_NOT_ACCEPT,
        /* 511 */   YY_NOT_ACCEPT,
        /* 512 */   YY_NOT_ACCEPT,
        /* 513 */   YY_NOT_ACCEPT,
        /* 514 */   YY_NOT_ACCEPT,
        /* 515 */   YY_NOT_ACCEPT,
        /* 516 */   YY_NOT_ACCEPT,
        /* 517 */   YY_NOT_ACCEPT,
        /* 518 */   YY_NOT_ACCEPT,
        /* 519 */   YY_NOT_ACCEPT,
        /* 520 */   YY_NOT_ACCEPT,
        /* 521 */   YY_NOT_ACCEPT,
        /* 522 */   YY_NOT_ACCEPT,
        /* 523 */   YY_NOT_ACCEPT,
        /* 524 */   YY_NOT_ACCEPT,
        /* 525 */   YY_NOT_ACCEPT,
        /* 526 */   YY_NOT_ACCEPT,
        /* 527 */   YY_NOT_ACCEPT,
        /* 528 */   YY_NOT_ACCEPT,
        /* 529 */   YY_NOT_ACCEPT,
        /* 530 */   YY_NOT_ACCEPT,
        /* 531 */   YY_NOT_ACCEPT,
        /* 532 */   YY_NOT_ACCEPT,
        /* 533 */   YY_NOT_ACCEPT,
        /* 534 */   YY_NOT_ACCEPT,
        /* 535 */   YY_NOT_ACCEPT
        );


    var  $yy_cmap = array(
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 11, 5, 50, 50, 12, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        11, 14, 49, 2, 51, 44, 1, 48,
        52, 40, 51, 51, 61, 15, 7, 9,
        3, 3, 3, 3, 3, 57, 3, 62,
        3, 3, 10, 4, 8, 47, 13, 43,
        50, 39, 23, 37, 38, 27, 6, 6,
        6, 30, 6, 6, 33, 6, 6, 6,
        56, 6, 55, 54, 16, 6, 6, 6,
        6, 6, 6, 36, 45, 41, 50, 46,
        50, 21, 29, 32, 28, 17, 58, 24,
        60, 25, 6, 6, 20, 18, 26, 59,
        19, 6, 34, 31, 22, 6, 6, 6,
        6, 35, 6, 42, 50, 53, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 50, 50, 50, 50, 50, 50, 50,
        50, 0, 0 
         );


    var $yy_rmap = array(
        0, 1, 2, 3, 4, 5, 1, 6,
        7, 8, 9, 1, 10, 1, 11, 12,
        1, 3, 1, 1, 1, 1, 13, 1,
        1, 1, 1, 1, 1, 1, 1, 1,
        1, 1, 14, 1, 1, 1, 15, 1,
        1, 1, 16, 17, 18, 1, 1, 19,
        20, 19, 1, 1, 1, 21, 1, 1,
        22, 1, 23, 1, 24, 25, 26, 1,
        1, 27, 28, 29, 30, 31, 1, 1,
        32, 33, 1, 34, 1, 1, 1, 1,
        35, 1, 1, 1, 36, 1, 37, 1,
        38, 1, 39, 1, 40, 41, 1, 1,
        1, 1, 1, 42, 43, 44, 1, 45,
        46, 1, 1, 47, 48, 49, 50, 51,
        52, 19, 53, 54, 55, 56, 57, 58,
        59, 60, 61, 62, 63, 1, 64, 1,
        65, 66, 67, 68, 69, 41, 70, 71,
        72, 73, 74, 75, 76, 77, 75, 78,
        79, 1, 80, 81, 82, 1, 83, 1,
        1, 84, 85, 86, 87, 88, 89, 90,
        91, 92, 93, 94, 95, 96, 97, 98,
        99, 100, 101, 102, 103, 104, 105, 106,
        107, 108, 109, 110, 111, 112, 113, 114,
        115, 116, 13, 117, 118, 119, 120, 121,
        122, 123, 124, 125, 126, 127, 128, 129,
        130, 131, 132, 133, 134, 135, 136, 137,
        138, 139, 140, 141, 142, 143, 144, 145,
        146, 147, 148, 149, 150, 151, 152, 153,
        154, 155, 156, 157, 158, 159, 160, 161,
        162, 163, 164, 165, 166, 167, 168, 169,
        170, 171, 172, 173, 174, 175, 176, 177,
        178, 179, 180, 181, 182, 183, 184, 185,
        186, 187, 188, 189, 190, 191, 192, 193,
        194, 195, 196, 197, 198, 199, 200, 201,
        202, 203, 204, 205, 206, 207, 208, 209,
        210, 205, 211, 212, 213, 214, 215, 216,
        217, 218, 219, 216, 220, 221, 73, 222,
        223, 224, 225, 226, 227, 228, 229, 230,
        231, 232, 233, 234, 235, 236, 237, 238,
        239, 240, 241, 242, 243, 17, 244, 245,
        246, 247, 92, 248, 78, 84, 249, 250,
        64, 251, 252, 253, 94, 96, 254, 98,
        255, 256, 257, 258, 259, 260, 261, 262,
        263, 264, 265, 266, 267, 268, 269, 270,
        271, 272, 102, 273, 274, 275, 276, 277,
        278, 279, 280, 281, 282, 283, 284, 285,
        286, 287, 288, 289, 290, 291, 292, 293,
        294, 295, 296, 297, 298, 299, 300, 301,
        302, 303, 304, 305, 306, 307, 308, 309,
        310, 311, 312, 313, 314, 315, 41, 316,
        317, 318, 71, 319, 320, 321, 322, 323,
        324, 325, 326, 327, 328, 329, 330, 331,
        332, 333, 334, 335, 336, 337, 338, 339,
        340, 341, 342, 343, 344, 345, 346, 77,
        347, 348, 349, 120, 350, 351, 352, 353,
        354, 355, 356, 357, 358, 220, 359, 360,
        361, 362, 363, 135, 364, 365, 366, 367,
        151, 368, 369, 370, 172, 371, 182, 372,
        227, 373, 234, 374, 256, 375, 263, 376,
        274, 377, 280, 378, 297, 379, 301, 380,
        318, 381, 322, 382, 334, 383, 338, 384,
        385, 386, 387, 388, 389, 390, 391, 392,
        393, 394, 395, 396, 397, 398, 399, 400,
        401, 402, 403, 404, 405, 406, 407, 408,
        409, 410, 411, 412, 413, 414, 415, 416,
        417, 418, 419, 420, 421, 422, 423, 424,
        425, 426, 427, 428, 429, 430, 431, 432
        );


    var $yy_nxt = array(
        array( 1, 2, 3, 3, 3, 3, 3, 3,
            100, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            101, 430, 138, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, 99, 3, 3, 3, 4, 3,
            -1, 3, 3, 3, 3, 3, 3, 3,
            4, 4, 4, 4, 4, 4, 4, 4,
            4, 4, 4, 4, 4, 4, 4, 4,
            4, 4, 4, 4, 3, 4, 4, 4,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 4, 4,
            4, 3, 4, 4, 4, 3, 3 ),
        array( -1, 136, 3, 3, 3, 3, 3, 3,
            148, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            -1, 3, -1, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3 ),
        array( -1, -1, -1, 4, 102, 102, 4, 4,
            -1, -1, -1, -1, -1, -1, -1, 4,
            4, 4, 4, 4, 4, 4, 4, 4,
            4, 4, 4, 4, 4, 4, 4, 4,
            4, 4, 4, 4, -1, 4, 4, 4,
            -1, -1, -1, -1, -1, -1, 4, -1,
            -1, -1, -1, -1, -1, -1, 4, 4,
            4, 4, 4, 4, 4, -1, 4 ),
        array( -1, -1, -1, 5, -1, 103, 5, 5,
            -1, -1, 5, 103, 103, -1, -1, 5,
            5, 5, 5, 5, 5, 5, 5, 5,
            5, 5, 5, 5, 5, 5, 5, 5,
            5, 5, 5, 5, -1, 5, 5, 5,
            -1, -1, -1, -1, -1, -1, 5, -1,
            -1, -1, -1, -1, -1, -1, 5, 5,
            5, 5, 5, 5, 5, -1, 5 ),
        array( -1, -1, -1, -1, -1, 104, 15, -1,
            -1, -1, -1, 104, 104, -1, -1, -1,
            15, 15, 15, 15, 15, 15, 15, 15,
            15, 15, 15, 15, 15, 15, 15, 15,
            15, 15, 15, 15, -1, 15, 15, 15,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 15, 15,
            15, -1, 15, 15, 15, -1, -1 ),
        array( -1, -1, -1, 8, 105, 105, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 8, -1, -1, -1, -1, 8 ),
        array( -1, -1, -1, 9, 106, 106, 9, 9,
            -1, -1, -1, -1, -1, -1, -1, 9,
            9, 9, 9, 9, 9, 9, 9, 9,
            9, 9, 9, 9, 9, 9, 9, 9,
            9, 9, 9, 9, -1, 9, 9, 9,
            -1, -1, -1, -1, -1, -1, 9, -1,
            -1, -1, -1, -1, -1, -1, 9, 9,
            9, 9, 9, 9, 9, -1, 9 ),
        array( -1, -1, -1, 10, -1, 107, 10, 10,
            -1, 168, 10, 107, 107, -1, -1, 10,
            10, 10, 10, 10, 10, 10, 10, 10,
            10, 10, 10, 10, 10, 10, 10, 10,
            10, 10, 10, 10, -1, 10, 10, 10,
            -1, -1, -1, -1, -1, -1, 10, -1,
            -1, -1, -1, -1, -1, -1, 10, 10,
            10, 10, 10, 10, 10, -1, 10 ),
        array( -1, -1, -1, 12, -1, 108, 12, 12,
            -1, -1, -1, 108, 108, -1, -1, 12,
            12, 12, 12, 12, 12, 12, 12, 12,
            12, 12, 12, 12, 12, 12, 12, 12,
            12, 12, 12, 12, -1, 12, 12, 12,
            -1, -1, -1, -1, -1, -1, 12, -1,
            -1, -1, -1, -1, -1, -1, 12, 12,
            12, 12, 12, 12, 12, -1, 12 ),
        array( -1, -1, -1, -1, -1, 109, -1, -1,
            -1, -1, -1, 109, 109, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 179, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 15, -1, 110, 15, 15,
            -1, -1, -1, 110, 110, -1, -1, 15,
            15, 15, 15, 15, 15, 15, 15, 15,
            15, 15, 15, 15, 15, 15, 15, 15,
            15, 15, 15, 15, -1, 15, 15, 15,
            -1, -1, -1, -1, -1, -1, 15, -1,
            -1, -1, -1, -1, -1, -1, 15, 15,
            15, 15, 15, 15, 15, -1, 15 ),
        array( -1, -1, 185, -1, -1, 186, -1, -1,
            -1, -1, -1, 186, 186, -1, -1, -1,
            187, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 188, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            34, 34, -1, 34, -1, 294, 34, 34,
            -1, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34 ),
        array( 1, 149, 149, 149, 149, 112, 149, 149,
            39, 149, 149, 112, 112, 40, 149, 156,
            149, 149, 149, 149, 149, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 149 ),
        array( -1, -1, -1, 42, -1, 114, 42, 42,
            -1, -1, 42, 114, 114, -1, -1, 42,
            42, 42, 42, 42, 42, 42, 42, 42,
            42, 42, 42, 42, 42, 42, 42, 42,
            42, 42, 42, 42, -1, 42, 42, 42,
            -1, -1, -1, -1, -1, -1, 42, 44,
            -1, -1, -1, -1, -1, -1, 42, 42,
            42, 42, 42, 42, 42, -1, 42 ),
        array( -1, -1, -1, -1, -1, 317, -1, -1,
            -1, -1, -1, 317, 317, 45, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 44, -1, -1,
            -1, -1, -1, 44, 44, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 47, 47, 47, 47, 115, 47, 47,
            47, 47, 47, 115, 115, -1, 47, 47,
            47, 47, 47, 47, 47, 47, 47, 47,
            47, 47, 47, 47, 47, 47, 47, 47,
            47, 47, 47, 47, 47, 47, 47, 47,
            47, 47, 47, 47, 47, 47, 47, 47,
            -1, -1, 47, 47, 47, 47, 47, 47,
            47, 47, 47, 47, 47, 47, 47 ),
        array( -1, 47, 47, 48, 47, 116, 48, 48,
            47, 47, 47, 116, 116, -1, 47, 48,
            48, 48, 48, 48, 48, 48, 48, 48,
            48, 48, 48, 48, 48, 48, 48, 48,
            48, 48, 48, 48, 47, 48, 48, 48,
            47, 47, 47, 47, 47, 47, 48, 47,
            -1, -1, 47, 47, 47, 47, 48, 48,
            48, 48, 48, 48, 48, 47, 48 ),
        array( -1, -1, -1, -1, -1, 53, -1, -1,
            -1, -1, -1, 53, 53, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 56, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 56, 56, 56,
            56, 56, -1, 56, -1, 322, 56, 56,
            56, -1, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 56, 56 ),
        array( 1, 59, 59, 60, 59, 118, 61, 62,
            59, 59, 59, 118, 118, 63, 59, 62,
            61, 61, 61, 61, 61, 61, 61, 61,
            61, 61, 61, 61, 61, 61, 61, 61,
            61, 61, 61, 61, 64, 61, 61, 61,
            59, 59, 59, 59, 119, 59, 62, 59,
            143, 153, 59, 59, 59, 59, 61, 61,
            61, 60, 61, 61, 61, 59, 60 ),
        array( -1, -1, -1, 60, -1, 120, 65, 65,
            -1, -1, -1, 120, 120, -1, -1, 65,
            65, 65, 65, 65, 65, 65, 65, 65,
            65, 65, 65, 65, 65, 65, 65, 65,
            65, 65, 65, 65, -1, 65, 65, 65,
            -1, -1, -1, -1, -1, -1, 65, -1,
            -1, -1, -1, -1, -1, -1, 65, 65,
            65, 60, 65, 65, 65, -1, 60 ),
        array( -1, -1, -1, 61, -1, 121, 61, 61,
            -1, -1, -1, 121, 121, -1, -1, 61,
            61, 61, 61, 61, 61, 61, 61, 61,
            61, 61, 61, 61, 61, 61, 61, 61,
            61, 61, 61, 61, -1, 61, 61, 61,
            -1, -1, -1, -1, -1, -1, 61, -1,
            -1, -1, -1, -1, -1, -1, 61, 61,
            61, 61, 61, 61, 61, -1, 61 ),
        array( -1, -1, -1, 62, -1, 122, 62, 62,
            -1, -1, -1, 122, 122, -1, -1, 62,
            62, 62, 62, 62, 62, 62, 62, 62,
            62, 62, 62, 62, 62, 62, 62, 62,
            62, 62, 62, 62, -1, 62, 62, 62,
            -1, -1, -1, -1, -1, -1, 62, -1,
            -1, -1, -1, -1, -1, -1, 62, 62,
            62, 62, 62, 62, 62, -1, 62 ),
        array( -1, -1, -1, 65, -1, 123, 65, 65,
            -1, -1, -1, 123, 123, -1, -1, 65,
            65, 65, 65, 65, 65, 65, 65, 65,
            65, 65, 65, 65, 65, 65, 65, 65,
            65, 65, 65, 65, -1, 65, 65, 65,
            -1, -1, -1, -1, -1, -1, 65, -1,
            -1, -1, -1, -1, -1, -1, 65, 65,
            65, 65, 65, 65, 65, -1, 65 ),
        array( -1, -1, -1, -1, -1, 66, -1, -1,
            -1, -1, -1, 66, 66, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 67, 124, 124, 67, 67,
            -1, -1, -1, 124, 124, -1, -1, 67,
            67, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67, 67, -1, 67, 67, 67,
            -1, -1, -1, -1, -1, -1, 67, -1,
            -1, -1, -1, -1, -1, -1, 67, 67,
            67, 67, 67, 67, 67, -1, 67 ),
        array( -1, -1, -1, -1, -1, 68, -1, -1,
            -1, -1, -1, 68, 68, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 69, -1, -1,
            -1, -1, -1, 69, 69, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 72, 72, 72, 72, 72, 72, 72,
            72, 72, 72, 72, 72, 72, 72, 72,
            72, 72, 72, 72, 72, 72, 72, 72,
            72, 72, 72, 72, 72, 72, 72, 72,
            72, 72, 72, 72, 72, 72, 72, 72,
            72, -1, 72, 72, 72, 72, 72, 72,
            72, 72, 72, 72, 72, 72, 72, 72,
            72, 72, 72, 72, 72, 72, 72 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 330, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 127, -1 ),
        array( -1, -1, -1, 80, -1, -1, 80, 355,
            -1, -1, -1, -1, -1, -1, -1, -1,
            80, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80, 80, 356, 80, 80, 80,
            -1, -1, -1, -1, 503, -1, 80, -1,
            -1, -1, -1, -1, -1, -1, 80, 80,
            80, 80, 80, 80, 80, -1, 80 ),
        array( -1, 84, 84, 84, 84, 84, 84, 84,
            -1, 84, 84, 84, 84, 84, 84, 84,
            84, 84, 84, 84, 84, 84, 84, 84,
            84, 84, 84, 84, 84, 84, 84, 84,
            84, 84, 84, 84, 84, 84, 84, 84,
            84, 84, 84, 84, 84, 84, 84, 84,
            84, 84, 84, 84, 84, 84, 84, 84,
            84, 84, 84, 84, 84, 84, 84 ),
        array( -1, 86, 86, 86, 86, 86, 86, 86,
            86, 86, 86, 86, 86, 86, 86, 86,
            86, 86, 86, 86, 86, 86, 86, 86,
            86, 86, 86, 86, 86, 86, 86, 86,
            86, 86, 86, 86, 86, 86, 86, 86,
            86, -1, -1, 86, 86, 86, 86, 86,
            86, 86, 86, 86, 86, 86, 86, 86,
            86, 86, 86, 86, 86, 86, 86 ),
        array( 1, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 175,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131 ),
        array( 1, 132, 132, 132, 132, 132, 132, 132,
            132, 132, 132, 132, 132, 132, 132, 132,
            132, 132, 132, 132, 132, 132, 132, 132,
            132, 132, 132, 132, 132, 132, 132, 132,
            132, 132, 132, 132, 132, 132, 132, 132,
            132, 132, 132, 395, 132, 132, 132, 132,
            132, 132, 132, 132, 132, 132, 132, 132,
            132, 132, 132, 132, 132, 132, 132 ),
        array( 1, 93, 93, 93, 93, 133, 93, 93,
            93, 93, 93, 133, 133, 93, 93, 134,
            93, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 147, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93, 93 ),
        array( -1, 146, 146, 146, 146, 146, 146, 146,
            146, 146, 146, 146, 146, 146, 146, -1,
            146, 146, 146, 146, 146, 146, 146, 146,
            146, 146, 146, 146, 146, 146, 146, 146,
            146, 146, 146, 146, 146, 146, 146, 146,
            146, 146, 146, 146, 146, 146, 146, 146,
            146, 146, 146, 146, 146, 146, 146, 146,
            146, 146, 146, 146, 146, 146, 146 ),
        array( -1, -1, -1, 8, -1, -1, 9, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            9, 9, 9, 9, 9, 9, 9, 9,
            9, 9, 9, 9, 9, 9, 9, 9,
            9, 9, 9, 9, -1, 9, 9, 9,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 9, 9,
            9, 8, 9, 9, 9, -1, 8 ),
        array( -1, -1, -1, -1, -1, 3, 5, -1,
            -1, 155, -1, 3, 3, 6, 158, -1,
            5, 5, 5, 5, 5, 5, 5, 5,
            5, 5, 5, 5, 5, 5, 5, 5,
            5, 5, 5, 5, 3, 5, 5, 5,
            -1, 3, 3, 7, -1, 3, 3, -1,
            -1, -1, 3, -1, -1, 3, 5, 5,
            5, -1, 5, 5, 5, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 160, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 103, -1, -1,
            -1, -1, -1, 103, 103, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 104, -1, -1,
            -1, -1, -1, 104, 104, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 107, -1, -1,
            -1, 168, -1, 107, 107, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 108, -1, -1,
            -1, -1, -1, 108, 108, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 109, -1, -1,
            -1, -1, -1, 109, 109, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 110, -1, -1,
            -1, -1, -1, 110, 110, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 295, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            295, 295, 295, 295, 295, 295, 295, 295,
            295, 295, 295, 295, 295, 295, 295, 295,
            295, 295, 295, 295, -1, 295, 295, 295,
            -1, -1, -1, -1, -1, -1, 296, -1,
            -1, -1, -1, -1, -1, -1, 295, 295,
            295, -1, 295, 295, 295, -1, -1 ),
        array( -1, -1, -1, -1, -1, 112, -1, -1,
            -1, -1, -1, 112, 112, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 114, -1, -1,
            -1, -1, -1, 114, 114, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 44,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 115, -1, -1,
            -1, -1, -1, 115, 115, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 116, -1, -1,
            -1, -1, -1, 116, 116, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 295, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            295, 295, 295, 295, 295, 295, 295, 295,
            295, 295, 295, 295, 295, 295, 295, 295,
            295, 295, 295, 295, -1, 295, 295, 295,
            -1, -1, -1, -1, -1, -1, 323, -1,
            -1, -1, -1, -1, -1, -1, 295, 295,
            295, -1, 295, 295, 295, -1, -1 ),
        array( -1, -1, -1, -1, -1, 118, -1, -1,
            -1, -1, -1, 118, 118, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 66, 67, -1,
            -1, -1, -1, 66, 66, -1, -1, -1,
            67, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67, 67, -1, 67, 67, 67,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 67, 67,
            67, -1, 67, 67, 67, -1, -1 ),
        array( -1, -1, -1, -1, -1, 120, -1, -1,
            -1, -1, -1, 120, 120, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 121, -1, -1,
            -1, -1, -1, 121, 121, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 122, -1, -1,
            -1, -1, -1, 122, 122, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 123, -1, -1,
            -1, -1, -1, 123, 123, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 124, -1, -1,
            -1, -1, -1, 124, 124, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 71, -1, 328,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 355,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 356, -1, -1, -1,
            -1, -1, -1, -1, 503, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 369, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 154, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 393,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131 ),
        array( -1, 132, 132, 132, 132, 132, 132, 132,
            132, 132, 132, 132, 132, 132, 132, 132,
            132, 132, 132, 132, 132, 132, 132, 132,
            132, 132, 132, 132, 132, 132, 132, 132,
            132, 132, 132, 132, 132, 132, 132, 132,
            132, 132, 132, -1, 132, 132, 132, 132,
            132, 132, 132, 132, 132, 132, 132, 132,
            132, 132, 132, 132, 132, 132, 132 ),
        array( -1, 146, 146, 146, 146, 146, 146, 146,
            146, 146, 146, 146, 146, 146, 146, 396,
            146, 146, 146, 146, 146, 146, 146, 146,
            146, 146, 146, 146, 146, 146, 146, 146,
            146, 146, 146, 146, 146, 146, 146, 146,
            146, 146, 146, 146, 146, 146, 146, 146,
            146, 146, 146, 146, 146, 146, 146, 146,
            146, 146, 146, 146, 146, 146, 146 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 95, -1, 402,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 3, 3, 3, -1, 3,
            -1, 3, 3, 3, 3, 3, 3, 3,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 3, -1, -1, -1,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, -1, -1,
            -1, 3, -1, -1, -1, 3, 3 ),
        array( -1, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            -1, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34 ),
        array( -1, -1, -1, -1, -1, -1, 162, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            162, 488, 162, 162, 162, 162, 162, 162,
            162, 434, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, -1, 162, 162, 162,
            -1, -1, -1, -1, -1, -1, 164, -1,
            -1, -1, -1, -1, -1, -1, 162, 162,
            162, -1, 524, 162, 162, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 297 ),
        array( 1, 149, 149, 149, 149, 112, 42, 149,
            39, 43, 149, 112, 112, 40, 149, 156,
            42, 42, 42, 42, 42, 42, 42, 42,
            42, 42, 42, 42, 42, 42, 42, 42,
            42, 42, 42, 42, 149, 42, 42, 42,
            149, 149, 149, 159, 149, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 42, 42,
            42, 149, 42, 42, 42, 149, 149 ),
        array( -1, 47, 47, 141, 47, 116, 141, 141,
            47, 47, 47, 116, 116, -1, 47, 141,
            141, 141, 141, 141, 141, 141, 141, 141,
            141, 141, 141, 141, 141, 141, 141, 141,
            141, 141, 141, 141, 47, 141, 141, 141,
            47, 47, 47, 47, 47, 47, 141, 47,
            -1, -1, 47, 47, 47, 47, 141, 141,
            141, 141, 141, 141, 141, 47, 141 ),
        array( -1, 324, 324, 324, 324, 324, 324, 324,
            324, 324, 324, 324, 324, 324, 324, 324,
            324, 324, 324, 324, 324, 324, 324, 324,
            324, 324, 324, 324, 324, 324, 324, 324,
            324, 324, 324, 324, 324, 324, 324, 324,
            324, 324, 324, 324, 324, 324, 324, 324,
            68, 324, 324, 324, 324, 324, 324, 324,
            324, 324, 324, 324, 324, 324, 324 ),
        array( -1, -1, -1, -1, -1, -1, 377, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            377, 493, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, -1, 377, 377, 377,
            -1, -1, -1, -1, -1, -1, 377, -1,
            -1, -1, -1, -1, -1, -1, 377, 377,
            377, -1, 527, 377, 377, -1, -1 ),
        array( -1, 398, 398, 398, 398, 133, 398, 398,
            398, 398, 398, 133, 133, 398, 398, 398,
            398, 398, 398, 398, 398, 398, 398, 398,
            398, 398, 398, 398, 398, 398, 398, 398,
            398, 398, 398, 398, 398, 398, 398, 398,
            398, 398, -1, 398, 398, 398, 398, 398,
            398, 398, 398, 398, 398, 398, 398, 398,
            398, 398, 398, 398, 398, 398, 398 ),
        array( -1, -1, -1, -1, -1, -1, 397, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            397, 397, 397, 397, 397, 397, 397, 397,
            397, 397, 397, 397, 397, 397, 397, 397,
            397, 397, 397, 397, -1, 397, 397, 397,
            -1, -1, -1, -1, -1, -1, 397, -1,
            -1, -1, -1, -1, -1, -1, 397, 397,
            397, -1, 397, 397, 397, -1, -1 ),
        array( -1, -1, -1, -1, -1, 3, -1, -1,
            -1, -1, -1, 3, 3, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 3, -1, -1, -1,
            -1, 3, 3, -1, -1, 3, 3, -1,
            -1, -1, 3, -1, -1, 3, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 136, 3, 3, 3, 3, 3, 3,
            148, 3, 3, 3, 3, 17, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            -1, 3, -1, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3 ),
        array( -1, 325, 325, 325, 325, 325, 325, 325,
            325, 325, 325, 325, 325, 325, 325, 325,
            325, 325, 325, 325, 325, 325, 325, 325,
            325, 325, 325, 325, 325, 325, 325, 325,
            325, 325, 325, 325, 325, 325, 325, 325,
            325, 325, 325, 325, 325, 325, 325, 325,
            325, 125, 325, 325, 325, 325, 325, 325,
            325, 325, 325, 325, 325, 325, 325 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 87, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 166, 10, -1,
            -1, 168, -1, 166, 166, 11, -1, -1,
            10, 10, 10, 10, 10, 10, 10, 10,
            10, 10, 10, 10, 10, 10, 10, 10,
            10, 10, 10, 10, -1, 10, 10, 10,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 10, 10,
            10, -1, 10, 10, 10, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 316,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 69, 69, 69, 69, 69, 69, 69,
            69, 69, 69, 69, 69, 69, 69, 327,
            69, 69, 69, 69, 69, 69, 69, 69,
            69, 69, 69, 69, 69, 69, 69, 69,
            69, 69, 69, 69, 69, 69, 69, 69,
            69, 69, 69, 69, 69, 69, 69, 69,
            69, 69, 69, 69, 69, 69, 69, 69,
            69, 69, 69, 69, 69, 69, 69 ),
        array( -1, -1, -1, -1, -1, -1, 12, -1,
            -1, -1, -1, -1, -1, 13, -1, 170,
            12, 12, 12, 12, 12, 12, 12, 12,
            12, 12, 12, 12, 12, 12, 12, 12,
            12, 12, 12, 12, 14, 12, 12, 12,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 12, 12,
            12, -1, 12, 12, 12, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 46, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 16, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 56, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, -1, 56, 56,
            56, -1, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 56, 56 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 162, 162, 162, -1, 162 ),
        array( -1, 332, 75, 332, 332, 332, 332, 332,
            332, 332, 332, 332, 332, 332, 332, 332,
            332, 332, 332, 332, 332, 332, 332, 332,
            332, 332, 332, 332, 332, 332, 332, 332,
            332, 332, 332, 332, 332, 332, 332, 332,
            332, 332, 332, 332, 332, 332, 332, 332,
            332, 332, 332, 332, 332, 332, 332, 332,
            332, 332, 332, 332, 332, 332, 332 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 20, 19, 162, 162,
            162, 162, 162, 162, 162, -1, 162 ),
        array( -1, -1, -1, 333, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            334, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 333, -1, -1, -1, 76, 333 ),
        array( -1, -1, -1, -1, -1, 166, -1, -1,
            -1, 168, -1, 166, 166, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 335, -1, -1, 335, 336,
            -1, -1, -1, -1, -1, -1, -1, -1,
            335, 335, 335, 335, 335, 335, 335, 335,
            335, 335, 335, 335, 335, 335, 335, 335,
            335, 335, 335, 335, 337, 335, 335, 335,
            338, -1, -1, -1, 501, -1, 335, -1,
            -1, -1, -1, -1, -1, -1, 335, 335,
            335, 335, 335, 335, 335, 77, 335 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            21, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 78,
            -1, -1, 339, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 79, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 22,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 354, 81, 354, 354, 354, 354, 354,
            354, 354, 354, 354, 354, 354, 354, 354,
            354, 354, 354, 354, 354, 354, 354, 354,
            354, 354, 354, 354, 354, 354, 354, 354,
            354, 354, 354, 354, 354, 354, 354, 354,
            354, 354, 354, 354, 354, 354, 354, 354,
            354, 354, 354, 354, 354, 354, 354, 354,
            354, 354, 354, 354, 354, 354, 354 ),
        array( -1, -1, -1, -1, -1, -1, 180, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            180, 180, 180, 180, 180, 180, 180, 180,
            180, 180, 180, 180, 180, 180, 180, 180,
            180, 180, 180, 180, -1, 180, 180, 180,
            -1, -1, -1, -1, -1, -1, 180, -1,
            -1, -1, -1, -1, -1, -1, 180, 180,
            180, -1, 180, 180, 180, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 357, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 358, -1, -1, -1,
            -1, -1, -1, -1, -1, 82, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 181, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            181, 181, 181, 181, 181, 181, 181, 181,
            181, 181, 181, 181, 181, 181, 181, 181,
            181, 181, 181, 181, -1, 181, 181, 181,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 181, 181,
            181, -1, 181, 181, 181, -1, -1 ),
        array( -1, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 394,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131 ),
        array( -1, -1, -1, 182, -1, -1, 182, -1,
            -1, -1, -1, -1, -1, -1, -1, 182,
            182, 182, 182, 182, 182, 182, 182, 182,
            182, 182, 182, 182, 182, 182, 182, 182,
            182, 182, 182, 182, -1, 182, 182, 182,
            -1, -1, -1, -1, -1, -1, 182, -1,
            -1, -1, -1, -1, -1, -1, 182, 182,
            182, 182, 182, 182, 182, -1, 182 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 183, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 184, -1, -1, -1, -1, -1,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 162, 162, 162, -1, 162 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 189, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 180, -1, -1, 180, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            180, 180, 180, 180, 180, 180, 180, 180,
            180, 180, 180, 180, 180, 180, 180, 180,
            180, 180, 180, 180, 190, 180, 180, 180,
            -1, -1, -1, -1, 191, -1, 180, -1,
            -1, -1, -1, -1, 18, 19, 180, 180,
            180, 180, 180, 180, 180, -1, 180 ),
        array( -1, -1, -1, -1, -1, -1, 181, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            181, 181, 181, 181, 181, 181, 181, 181,
            181, 181, 181, 181, 181, 181, 181, 181,
            181, 181, 181, 181, -1, 181, 181, 181,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 19, 181, 181,
            181, -1, 181, 181, 181, -1, -1 ),
        array( -1, -1, -1, 182, -1, -1, 182, -1,
            -1, -1, -1, -1, -1, -1, -1, 182,
            182, 182, 182, 182, 182, 182, 182, 182,
            182, 182, 182, 182, 182, 182, 182, 182,
            182, 182, 182, 182, -1, 182, 182, 182,
            -1, 192, -1, -1, 193, -1, 182, -1,
            -1, -1, -1, -1, -1, -1, 182, 182,
            182, 182, 182, 182, 182, -1, 182 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 176,
            -1, -1, -1, -1, -1, 176, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 196, -1,
            -1, -1, -1, -1, -1, -1, 197, -1,
            196, 196, 196, 196, 196, 196, 196, 196,
            196, 196, 196, 196, 196, 196, 196, 196,
            196, 196, 196, 196, -1, 196, 196, 196,
            -1, -1, -1, -1, -1, -1, 196, -1,
            -1, -1, -1, -1, -1, -1, 196, 196,
            196, -1, 196, 196, 196, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 438,
            -1, -1, -1, 439, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 198, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 199, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 200,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 201, -1, -1, 201, -1,
            -1, -1, -1, -1, -1, -1, -1, 201,
            201, 201, 201, 201, 201, 201, 201, 201,
            201, 201, 201, 201, 201, 201, 201, 201,
            201, 201, 201, 201, -1, 201, 201, 201,
            -1, -1, -1, -1, -1, -1, 201, -1,
            -1, -1, -1, -1, -1, -1, 201, 201,
            201, 201, 201, 201, 201, -1, 201 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 437, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 176, -1, -1, -1,
            -1, -1, -1, -1, 177, -1, -1, -1,
            -1, -1, -1, -1, -1, 19, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 202, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 203, -1, -1, -1, -1, -1,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 162, 162, 162, -1, 162 ),
        array( -1, -1, -1, -1, -1, -1, 181, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            181, 181, 181, 181, 181, 181, 181, 181,
            181, 181, 181, 181, 181, 181, 181, 181,
            181, 181, 181, 181, -1, 181, 181, 181,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 23, 181, 181,
            181, -1, 181, 181, 181, -1, -1 ),
        array( -1, -1, -1, 196, -1, -1, 196, 204,
            -1, -1, -1, -1, -1, -1, -1, -1,
            196, 196, 196, 196, 196, 196, 196, 196,
            196, 196, 196, 196, 196, 196, 196, 196,
            196, 196, 196, 196, 205, 196, 196, 196,
            -1, -1, -1, -1, 489, -1, 196, -1,
            -1, -1, -1, -1, 24, 25, 196, 196,
            196, 196, 196, 196, 196, -1, 196 ),
        array( -1, -1, -1, -1, -1, -1, 196, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            196, 196, 196, 196, 196, 196, 196, 196,
            196, 196, 196, 196, 196, 196, 196, 196,
            196, 196, 196, 196, -1, 196, 196, 196,
            -1, -1, -1, -1, -1, -1, 196, -1,
            -1, -1, -1, -1, -1, -1, 196, 196,
            196, -1, 196, 196, 196, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 208, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 209,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            210, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 201, -1, -1, 201, -1,
            -1, -1, -1, -1, -1, -1, -1, 201,
            201, 201, 201, 201, 201, 201, 201, 201,
            201, 201, 201, 201, 201, 201, 201, 201,
            201, 201, 201, 201, -1, 201, 201, 201,
            -1, 211, -1, -1, 212, -1, 201, -1,
            -1, -1, -1, -1, -1, -1, 201, 201,
            201, 201, 201, 201, 201, -1, 201 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 192, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 192, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 181, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            181, 181, 181, 181, 181, 181, 181, 181,
            181, 181, 181, 181, 181, 181, 181, 181,
            181, 181, 181, 181, -1, 181, 181, 181,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 26, 181, 181,
            181, -1, 181, 181, 181, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 213, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            213, 213, 213, 213, 213, 213, 213, 213,
            213, 213, 213, 213, 213, 213, 213, 213,
            213, 213, 213, 213, -1, 213, 213, 213,
            -1, -1, -1, -1, -1, -1, 213, -1,
            -1, -1, -1, -1, -1, -1, 213, 213,
            213, -1, 213, 213, 213, -1, -1 ),
        array( -1, -1, -1, 214, -1, -1, 214, -1,
            -1, -1, -1, -1, -1, -1, -1, 214,
            214, 214, 214, 214, 214, 214, 214, 214,
            214, 214, 214, 214, 214, 214, 214, 214,
            214, 214, 214, 214, -1, 214, 214, 214,
            -1, -1, -1, -1, -1, -1, 214, -1,
            -1, -1, -1, -1, -1, -1, 214, 214,
            214, 214, 214, 214, 214, -1, 214 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            215, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 216, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 217, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 218, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 219,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 190, -1, -1, -1,
            -1, -1, -1, -1, 191, -1, -1, -1,
            -1, -1, -1, -1, -1, 19, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 220, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 213, -1, -1, 213, 204,
            -1, -1, -1, -1, -1, -1, -1, -1,
            213, 213, 213, 213, 213, 213, 213, 213,
            213, 213, 213, 213, 213, 213, 213, 213,
            213, 213, 213, 213, 221, 213, 213, 213,
            -1, -1, -1, -1, 495, -1, 213, -1,
            -1, -1, -1, -1, 24, 25, 213, 213,
            213, 213, 213, 213, 213, -1, 213 ),
        array( -1, -1, -1, 214, -1, -1, 214, -1,
            -1, -1, -1, -1, -1, -1, -1, 214,
            214, 214, 214, 214, 214, 214, 214, 214,
            214, 214, 214, 214, 214, 214, 214, 214,
            214, 214, 214, 214, -1, 214, 214, 214,
            -1, 222, -1, -1, 223, -1, 214, -1,
            -1, -1, -1, -1, -1, -1, 214, 214,
            214, 214, 214, 214, 214, -1, 214 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 225, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 226, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 440, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 441, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 27, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 211, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 211, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 227, -1, -1, 227, -1,
            -1, -1, -1, -1, -1, -1, -1, 227,
            227, 227, 227, 227, 227, 227, 227, 227,
            227, 227, 227, 227, 227, 227, 227, 227,
            227, 227, 227, 227, -1, 227, 227, 227,
            -1, -1, -1, -1, -1, -1, 227, -1,
            -1, -1, -1, -1, -1, -1, 227, 227,
            227, 227, 227, 227, 227, -1, 227 ),
        array( -1, -1, -1, -1, -1, -1, -1, 204,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 205, -1, -1, -1,
            -1, -1, -1, -1, 489, -1, -1, -1,
            -1, -1, -1, -1, 24, 25, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 228, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 229, -1, -1, -1, -1, -1,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 162, 162, 162, -1, 162 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 230, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 231, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 227, -1, -1, 227, -1,
            -1, -1, -1, -1, -1, -1, -1, 227,
            227, 227, 227, 227, 227, 227, 227, 227,
            227, 227, 227, 227, 227, 227, 227, 227,
            227, 227, 227, 227, -1, 227, 227, 227,
            -1, 234, -1, -1, 235, -1, 227, -1,
            -1, -1, -1, -1, -1, -1, 227, 227,
            227, 227, 227, 227, 227, -1, 227 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 222, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 222, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 236, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            236, 236, 236, 236, 236, 236, 236, 236,
            236, 236, 236, 236, 236, 236, 236, 236,
            236, 236, 236, 236, -1, 236, 236, 236,
            -1, -1, -1, -1, -1, -1, 236, -1,
            -1, -1, -1, -1, -1, -1, 236, 236,
            236, -1, 236, 236, 236, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 237, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 238, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 239, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            534, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 204,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 221, -1, -1, -1,
            -1, -1, -1, -1, 495, -1, -1, -1,
            -1, -1, -1, -1, 24, 25, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 240, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 236, -1, -1, 236, 241,
            -1, -1, -1, -1, -1, -1, -1, -1,
            236, 236, 236, 236, 236, 236, 236, 236,
            236, 236, 236, 236, 236, 236, 236, 236,
            236, 236, 236, 236, 242, 236, 236, 236,
            -1, -1, -1, -1, 498, -1, 236, -1,
            -1, -1, -1, -1, -1, 28, 236, 236,
            236, 236, 236, 236, 236, 442, 236 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 243, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 244, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 245, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 234, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 234, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 246, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            246, 246, 246, 246, 246, 246, 246, 246,
            246, 246, 246, 246, 246, 246, 246, 246,
            246, 246, 246, 246, -1, 246, 246, 246,
            -1, -1, -1, -1, -1, -1, 246, -1,
            -1, -1, -1, -1, -1, -1, 246, 246,
            246, -1, 246, 246, 246, -1, -1 ),
        array( -1, -1, -1, 247, -1, -1, 247, -1,
            -1, -1, -1, -1, -1, -1, -1, 247,
            247, 247, 247, 247, 247, 247, 247, 247,
            247, 247, 247, 247, 247, 247, 247, 247,
            247, 247, 247, 247, -1, 247, 247, 247,
            -1, -1, -1, -1, -1, -1, 247, -1,
            -1, -1, -1, -1, -1, -1, 247, 247,
            247, 247, 247, 247, 247, -1, 247 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 249, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 443, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 250,
            -1, -1, -1, 251, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 246, -1, -1, 246, 241,
            -1, -1, -1, -1, -1, -1, -1, -1,
            246, 246, 246, 246, 246, 246, 246, 246,
            246, 246, 246, 246, 246, 246, 246, 246,
            246, 246, 246, 246, 252, 246, 246, 246,
            -1, -1, -1, -1, 500, -1, 246, -1,
            -1, -1, -1, -1, -1, 28, 246, 246,
            246, 246, 246, 246, 246, 442, 246 ),
        array( -1, -1, -1, 247, -1, -1, 247, -1,
            -1, -1, -1, -1, -1, -1, -1, 247,
            247, 247, 247, 247, 247, 247, 247, 247,
            247, 247, 247, 247, 247, 247, 247, 247,
            247, 247, 247, 247, -1, 247, 247, 247,
            -1, 253, -1, -1, 254, -1, 247, -1,
            -1, -1, -1, -1, -1, -1, 247, 247,
            247, 247, 247, 247, 247, -1, 247 ),
        array( -1, -1, -1, 248, -1, -1, 248, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            248, 248, 248, 248, 248, 248, 248, 248,
            248, 248, 248, 248, 248, 248, 248, 248,
            248, 248, 248, 248, -1, 248, 248, 248,
            -1, -1, -1, -1, -1, -1, 248, -1,
            -1, -1, -1, -1, -1, 29, 248, 248,
            248, 248, 248, 248, 248, 255, 248 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 256, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 258, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 259, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 260, -1, -1, 260, -1,
            -1, -1, -1, -1, -1, -1, -1, 260,
            260, 260, 260, 260, 260, 260, 260, 260,
            260, 260, 260, 260, 260, 260, 260, 260,
            260, 260, 260, 260, -1, 260, 260, 260,
            -1, -1, -1, -1, -1, -1, 260, -1,
            -1, -1, -1, -1, -1, -1, 260, 260,
            260, 260, 260, 260, 260, -1, 260 ),
        array( -1, -1, -1, -1, -1, -1, -1, 241,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 242, -1, -1, -1,
            -1, -1, -1, -1, 498, -1, -1, -1,
            -1, -1, -1, -1, -1, 28, -1, -1,
            -1, -1, -1, -1, -1, 442, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 261, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 262, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            262, 262, 262, 262, 262, 262, 262, 262,
            262, 262, 262, 262, 262, 262, 262, 262,
            262, 262, 262, 262, -1, 262, 262, 262,
            -1, -1, -1, -1, -1, -1, 262, -1,
            -1, -1, -1, -1, -1, -1, 262, 262,
            262, -1, 262, 262, 262, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 263, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 264, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            265, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 266, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 260, -1, -1, 260, -1,
            -1, -1, -1, -1, -1, -1, -1, 260,
            260, 260, 260, 260, 260, 260, 260, 260,
            260, 260, 260, 260, 260, 260, 260, 260,
            260, 260, 260, 260, -1, 260, 260, 260,
            -1, 267, -1, -1, 268, -1, 260, -1,
            -1, -1, -1, -1, -1, -1, 260, 260,
            260, 260, 260, 260, 260, -1, 260 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 253, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 253, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 262, -1, -1, 262, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            262, 262, 262, 262, 262, 262, 262, 262,
            262, 262, 262, 262, 262, 262, 262, 262,
            262, 262, 262, 262, -1, 262, 262, 262,
            -1, -1, -1, -1, -1, -1, 262, -1,
            -1, -1, -1, -1, -1, 30, 262, 262,
            262, 262, 262, 262, 262, -1, 262 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 269, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 270, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 271, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 272, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 241,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 252, -1, -1, -1,
            -1, -1, -1, -1, 500, -1, -1, -1,
            -1, -1, -1, -1, -1, 28, -1, -1,
            -1, -1, -1, -1, -1, 442, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 273, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 274, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 275, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 276, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 277, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 267, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 267, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 278, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 279, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 280, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 444, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 281, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 31, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 282, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 284, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 285, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 286, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 287, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 288, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 289, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 291, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 32, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 33, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 290, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 111, 34, 139, 137, 34, 34,
            35, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34 ),
        array( -1, -1, -1, 295, -1, -1, 295, 298,
            -1, -1, 299, -1, -1, -1, -1, -1,
            295, 295, 295, 295, 295, 295, 295, 295,
            295, 295, 295, 295, 295, 295, 295, 295,
            295, 295, 295, 295, 300, 295, 295, 295,
            -1, -1, -1, -1, 301, -1, 295, -1,
            -1, -1, -1, -1, 36, 37, 295, 295,
            295, 295, 295, 295, 295, -1, 295 ),
        array( -1, -1, -1, 295, -1, -1, 295, 298,
            -1, -1, 299, -1, -1, -1, -1, -1,
            295, 295, 295, 295, 295, 295, 295, 295,
            295, 295, 295, 295, 295, 295, 295, 295,
            295, 295, 295, 295, 300, 295, 295, 295,
            -1, -1, -1, -1, 301, -1, 295, -1,
            -1, -1, -1, -1, 151, 37, 295, 295,
            295, 295, 295, 295, 295, -1, 295 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 302,
            -1, -1, -1, -1, -1, 302, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 303, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            303, 303, 303, 303, 303, 303, 303, 303,
            303, 303, 303, 303, 303, 303, 303, 303,
            303, 303, 303, 303, -1, 303, 303, 303,
            -1, -1, -1, -1, -1, -1, 303, -1,
            -1, -1, -1, -1, -1, -1, 303, 303,
            303, -1, 303, 303, 303, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 304, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            304, 304, 304, 304, 304, 304, 304, 304,
            304, 304, 304, 304, 304, 304, 304, 304,
            304, 304, 304, 304, -1, 304, 304, 304,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 304, 304,
            304, -1, 304, 304, 304, -1, -1 ),
        array( -1, -1, -1, 305, -1, -1, 305, -1,
            -1, -1, -1, -1, -1, -1, -1, 305,
            305, 305, 305, 305, 305, 305, 305, 305,
            305, 305, 305, 305, 305, 305, 305, 305,
            305, 305, 305, 305, -1, 305, 305, 305,
            -1, -1, -1, -1, -1, -1, 305, -1,
            -1, -1, -1, -1, -1, -1, 305, 305,
            305, 305, 305, 305, 305, -1, 305 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 465, -1, -1, -1, -1, 306 ),
        array( -1, -1, -1, -1, -1, -1, 295, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            295, 295, 295, 295, 295, 295, 295, 295,
            295, 295, 295, 295, 295, 295, 295, 295,
            295, 295, 295, 295, -1, 295, 295, 295,
            -1, -1, -1, -1, -1, -1, 295, -1,
            -1, -1, -1, -1, -1, -1, 295, 295,
            295, -1, 295, 295, 295, -1, -1 ),
        array( -1, -1, -1, 303, -1, -1, 303, 298,
            -1, -1, 299, -1, -1, -1, -1, -1,
            303, 303, 303, 303, 303, 303, 303, 303,
            303, 303, 303, 303, 303, 303, 303, 303,
            303, 303, 303, 303, 307, 303, 303, 303,
            -1, -1, -1, -1, 492, -1, 303, -1,
            -1, -1, -1, -1, 36, 37, 303, 303,
            303, 303, 303, 303, 303, -1, 303 ),
        array( -1, -1, -1, -1, -1, -1, 304, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            304, 304, 304, 304, 304, 304, 304, 304,
            304, 304, 304, 304, 304, 304, 304, 304,
            304, 304, 304, 304, -1, 304, 304, 304,
            -1, -1, -1, -1, 308, -1, -1, -1,
            -1, -1, -1, -1, -1, 37, 304, 304,
            304, -1, 304, 304, 304, -1, -1 ),
        array( -1, -1, -1, 305, -1, -1, 305, -1,
            -1, -1, -1, -1, -1, -1, -1, 305,
            305, 305, 305, 305, 305, 305, 305, 305,
            305, 305, 305, 305, 305, 305, 305, 305,
            305, 305, 305, 305, -1, 305, 305, 305,
            -1, 309, -1, -1, 310, -1, 305, -1,
            -1, -1, -1, -1, -1, -1, 305, 305,
            305, 305, 305, 305, 305, -1, 305 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 37, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 37, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 311, -1, -1, 311, -1,
            -1, -1, -1, -1, -1, -1, -1, 311,
            311, 311, 311, 311, 311, 311, 311, 311,
            311, 311, 311, 311, 311, 311, 311, 311,
            311, 311, 311, 311, -1, 311, 311, 311,
            -1, -1, -1, -1, -1, -1, 311, -1,
            -1, -1, -1, -1, -1, -1, 311, 311,
            311, 311, 311, 311, 311, -1, 311 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 306 ),
        array( -1, -1, -1, -1, -1, -1, -1, 298,
            -1, -1, 299, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 300, -1, -1, -1,
            -1, -1, -1, -1, 301, -1, -1, -1,
            -1, -1, -1, -1, -1, 37, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 312, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 311, -1, -1, 311, -1,
            -1, -1, -1, -1, -1, -1, -1, 311,
            311, 311, 311, 311, 311, 311, 311, 311,
            311, 311, 311, 311, 311, 311, 311, 311,
            311, 311, 311, 311, -1, 311, 311, 311,
            -1, 313, -1, -1, 314, -1, 311, -1,
            -1, -1, -1, -1, -1, -1, 311, 311,
            311, 311, 311, 311, 311, -1, 311 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 309, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 309, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 298,
            -1, -1, 299, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 307, -1, -1, -1,
            -1, -1, -1, -1, 492, -1, -1, -1,
            -1, -1, -1, -1, -1, 37, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 315, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 313, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 313, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 41, -1, 316,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 47, 47, 48, 47, -1, 431, 431,
            113, 49, 47, 149, -1, 50, 47, 431,
            431, 431, 431, 431, 431, 431, 431, 431,
            431, 431, 431, 431, 431, 431, 431, 431,
            431, 431, 431, 431, 47, 431, 431, 431,
            47, 47, 47, 47, 47, 47, 431, 47,
            51, 52, 47, 47, 47, 47, 431, 431,
            431, 48, 431, 431, 431, 47, 48 ),
        array( 1, 149, 149, 149, 149, 53, 149, 149,
            149, 149, 149, 53, 53, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 149 ),
        array( 1, 54, 54, 54, 54, -1, 54, 54,
            54, 54, 54, 54, -1, 55, 54, 54,
            54, 54, 54, 54, 54, 54, 54, 54,
            54, 54, 54, 54, 54, 54, 54, 54,
            54, 54, 54, 54, 54, 54, 54, 54,
            54, 54, 54, 54, 54, 54, 54, 54,
            54, 54, 54, 54, 54, 54, 54, 54,
            54, 54, 54, 54, 54, 54, 54 ),
        array( 1, 56, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 56, 56, 56,
            56, 56, 117, 56, 142, 161, 56, 56,
            56, 57, 56, 56, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 56, 56 ),
        array( -1, -1, -1, 295, -1, -1, 295, 298,
            -1, -1, 299, -1, -1, -1, -1, -1,
            295, 295, 295, 295, 295, 295, 295, 295,
            295, 295, 295, 295, 295, 295, 295, 295,
            295, 295, 295, 295, 300, 295, 295, 295,
            -1, -1, -1, -1, 301, -1, 295, -1,
            -1, -1, -1, -1, 152, 37, 295, 295,
            295, 295, 295, 295, 295, -1, 295 ),
        array( 1, 69, 69, 69, 69, 69, 69, 69,
            69, 69, 69, 69, 69, 69, 69, 157,
            69, 69, 69, 69, 69, 69, 69, 69,
            69, 69, 69, 69, 69, 69, 69, 69,
            69, 69, 69, 69, 69, 69, 69, 69,
            69, 69, 69, 69, 69, 69, 69, 69,
            69, 69, 69, 69, 69, 69, 69, 69,
            69, 69, 69, 69, 69, 69, 69 ),
        array( -1, 70, 70, 70, 70, 70, 70, 70,
            70, 70, 70, 70, 70, 71, 70, 126,
            70, 70, 70, 70, 70, 70, 70, 70,
            70, 70, 70, 70, 70, 70, 70, 70,
            70, 70, 70, 70, 70, 70, 70, 70,
            70, 70, 70, 70, 70, 70, 70, 70,
            70, 70, 70, 70, 70, 70, 70, 70,
            70, 70, 70, 70, 70, 70, 70 ),
        array( 1, 72, 72, 72, 72, 72, 72, 72,
            72, 72, 72, 72, 72, 72, 72, 72,
            72, 72, 72, 72, 72, 72, 72, 72,
            72, 72, 72, 72, 72, 72, 72, 72,
            72, 72, 72, 72, 72, 72, 72, 72,
            72, 73, 72, 72, 72, 72, 72, 72,
            72, 72, 72, 72, 72, 72, 72, 72,
            72, 72, 72, 72, 72, 72, 72 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 74, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 149, 163, 165, 149, -1, 167, 149,
            149, 149, 149, 149, -1, 149, 149, 149,
            167, 167, 167, 167, 167, 167, 167, 167,
            167, 167, 167, 167, 167, 167, 167, 167,
            167, 167, 167, 167, 149, 167, 167, 167,
            169, 149, 149, 149, 149, 149, 167, 149,
            149, 149, 149, 149, 149, 149, 167, 167,
            167, 165, 167, 167, 167, 149, 165 ),
        array( -1, -1, -1, -1, -1, -1, -1, 76,
            -1, -1, 447, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 76, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 340, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            340, 340, 340, 340, 340, 340, 340, 340,
            340, 340, 340, 340, 340, 340, 340, 340,
            340, 340, 340, 340, -1, 340, 340, 340,
            -1, -1, -1, -1, -1, -1, 340, -1,
            -1, -1, -1, -1, -1, -1, 340, 340,
            340, -1, 340, 340, 340, -1, -1 ),
        array( -1, -1, -1, 341, -1, -1, 341, -1,
            -1, -1, -1, -1, -1, -1, -1, 341,
            341, 341, 341, 341, 341, 341, 341, 341,
            341, 341, 341, 341, 341, 341, 341, 341,
            341, 341, 341, 341, -1, 341, 341, 341,
            -1, -1, -1, -1, -1, -1, 341, -1,
            -1, -1, -1, -1, -1, -1, 341, 341,
            341, 341, 341, 341, 341, -1, 341 ),
        array( -1, -1, -1, -1, -1, -1, -1, 77,
            -1, -1, 453, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 77, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 342, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            342, 342, 342, 342, 342, 342, 342, 342,
            342, 342, 342, 342, 342, 342, 342, 342,
            342, 342, 342, 342, -1, 342, 342, 342,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 342, 342,
            342, -1, 342, 342, 342, -1, -1 ),
        array( -1, -1, -1, 340, -1, -1, 340, 336,
            -1, -1, -1, -1, -1, -1, -1, -1,
            340, 340, 340, 340, 340, 340, 340, 340,
            340, 340, 340, 340, 340, 340, 340, 340,
            340, 340, 340, 340, 344, 340, 340, 340,
            338, -1, -1, -1, 502, -1, 340, -1,
            -1, -1, -1, -1, -1, -1, 340, 340,
            340, 340, 340, 340, 340, 77, 340 ),
        array( -1, -1, -1, 341, -1, -1, 341, -1,
            -1, -1, -1, -1, -1, -1, -1, 341,
            341, 341, 341, 341, 341, 341, 341, 341,
            341, 341, 341, 341, 341, 341, 341, 341,
            341, 341, 341, 341, -1, 341, 341, 341,
            -1, 345, -1, -1, 346, -1, 341, -1,
            -1, -1, -1, -1, -1, -1, 341, 341,
            341, 341, 341, 341, 341, -1, 341 ),
        array( -1, -1, -1, -1, -1, -1, 342, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            342, 342, 342, 342, 342, 342, 342, 342,
            342, 342, 342, 342, 342, 342, 342, 342,
            342, 342, 342, 342, -1, 342, 342, 342,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 79, 342, 342,
            342, -1, 342, 342, 342, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 343, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            343, 343, 343, 343, 343, 343, 343, 343,
            343, 343, 343, 343, 343, 343, 343, 343,
            343, 343, 343, 343, -1, 343, 343, 343,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 76, 343, 343,
            343, -1, 343, 343, 343, -1, -1 ),
        array( -1, -1, -1, 348, -1, -1, 348, -1,
            -1, -1, -1, -1, -1, -1, -1, 348,
            348, 348, 348, 348, 348, 348, 348, 348,
            348, 348, 348, 348, 348, 348, 348, 348,
            348, 348, 348, 348, -1, 348, 348, 348,
            -1, -1, -1, -1, -1, -1, 348, -1,
            -1, -1, -1, -1, -1, -1, 348, 348,
            348, 348, 348, 348, 348, -1, 348 ),
        array( -1, -1, -1, -1, -1, -1, -1, 336,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 337, -1, -1, -1,
            338, -1, -1, -1, 501, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 77, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 349, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 347, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            347, 347, 347, 347, 347, 347, 347, 347,
            347, 347, 347, 347, 347, 347, 347, 347,
            347, 347, 347, 347, -1, 347, 347, 347,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 77, 347, 347,
            347, -1, 347, 347, 347, -1, -1 ),
        array( -1, -1, -1, 348, -1, -1, 348, -1,
            -1, -1, -1, -1, -1, -1, -1, 348,
            348, 348, 348, 348, 348, 348, 348, 348,
            348, 348, 348, 348, 348, 348, 348, 348,
            348, 348, 348, 348, -1, 348, 348, 348,
            -1, 350, -1, -1, 351, -1, 348, -1,
            -1, -1, -1, -1, -1, -1, 348, 348,
            348, 348, 348, 348, 348, -1, 348 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 345, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 345, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 336,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 344, -1, -1, -1,
            338, -1, -1, -1, 502, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 77, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 352, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 350, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 350, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 149, 171, 149, 149, -1, 80, 149,
            149, 149, 149, 149, -1, 149, 149, 149,
            80, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80, 80, 149, 80, 80, 80,
            173, 149, 149, 149, 149, 149, 80, 149,
            149, 149, 149, 149, 149, 149, 80, 80,
            80, 149, 80, 80, 80, 149, 149 ),
        array( -1, -1, -1, -1, -1, -1, 432, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            432, 432, 432, 432, 432, 432, 432, 432,
            432, 432, 432, 432, 432, 432, 432, 432,
            432, 432, 432, 432, -1, 432, 432, 432,
            -1, -1, -1, -1, -1, -1, 432, -1,
            -1, -1, -1, -1, -1, -1, 432, 432,
            432, -1, 432, 432, 432, -1, -1 ),
        array( -1, -1, -1, 359, -1, -1, 359, -1,
            -1, -1, -1, -1, -1, -1, -1, 359,
            359, 359, 359, 359, 359, 359, 359, 359,
            359, 359, 359, 359, 359, 359, 359, 359,
            359, 359, 359, 359, -1, 359, 359, 359,
            -1, -1, -1, -1, -1, -1, 359, -1,
            -1, -1, -1, -1, -1, -1, 359, 359,
            359, 359, 359, 359, 359, -1, 359 ),
        array( -1, -1, -1, -1, -1, -1, 360, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            360, 360, 360, 360, 360, 360, 360, 360,
            360, 360, 360, 360, 360, 360, 360, 360,
            360, 360, 360, 360, -1, 360, 360, 360,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 360, 360,
            360, -1, 360, 360, 360, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 361 ),
        array( -1, -1, -1, 359, -1, -1, 359, -1,
            -1, -1, -1, -1, -1, -1, -1, 359,
            359, 359, 359, 359, 359, 359, 359, 359,
            359, 359, 359, 359, 359, 359, 359, 359,
            359, 359, 359, 359, -1, 359, 359, 359,
            -1, 128, -1, -1, 363, -1, 359, -1,
            -1, -1, -1, -1, -1, -1, 359, 359,
            359, 359, 359, 359, 359, -1, 359 ),
        array( -1, -1, -1, -1, -1, -1, 360, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            360, 360, 360, 360, 360, 360, 360, 360,
            360, 360, 360, 360, 360, 360, 360, 360,
            360, 360, 360, 360, -1, 360, 360, 360,
            -1, -1, -1, -1, 358, -1, -1, -1,
            -1, -1, -1, -1, -1, 82, 360, 360,
            360, -1, 360, 360, 360, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 82, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 82, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 364, -1, -1, 364, -1,
            -1, -1, -1, -1, -1, -1, -1, 364,
            364, 364, 364, 364, 364, 364, 364, 364,
            364, 364, 364, 364, 364, 364, 364, 364,
            364, 364, 364, 364, -1, 364, 364, 364,
            -1, -1, -1, -1, -1, -1, 364, -1,
            -1, -1, -1, -1, -1, -1, 364, 364,
            364, 364, 364, 364, 364, -1, 364 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 365, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 364, -1, -1, 364, -1,
            -1, -1, -1, -1, -1, -1, -1, 364,
            364, 364, 364, 364, 364, 364, 364, 364,
            364, 364, 364, 364, 364, 364, 364, 364,
            364, 364, 364, 364, -1, 364, 364, 364,
            -1, 433, -1, -1, 366, -1, 364, -1,
            -1, -1, -1, -1, -1, -1, 364, 364,
            364, 364, 364, 364, 364, -1, 364 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 128, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 128, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 448, -1, -1, -1, -1, -1 ),
        array( 1, 149, 149, 149, 149, -1, 149, 149,
            149, 149, 149, 149, -1, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 149, 149,
            173, 149, 149, 149, 149, 149, 149, 149,
            149, 149, 149, 149, 149, 149, 149, 149,
            149, 149, 149, 149, 149, 83, 149 ),
        array( 1, 84, 84, 84, 84, 84, 84, 84,
            129, 84, 84, 84, 84, 84, 84, 84,
            84, 84, 84, 84, 84, 84, 84, 84,
            84, 84, 84, 84, 84, 84, 84, 84,
            84, 84, 84, 84, 84, 84, 84, 84,
            84, 84, 84, 84, 84, 84, 84, 84,
            84, 84, 84, 84, 84, 84, 84, 84,
            84, 84, 84, 84, 84, 84, 84 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 370,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 370, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            371, -1, -1, -1, -1, 371, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 372, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 372,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 373, -1, -1, -1, -1, 373, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 374, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            374, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            375, -1, -1, -1, -1, -1, 375, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 85, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 86, 86, 86, 86, 86, 86, 86,
            86, 86, 86, 86, 86, 86, 86, 86,
            86, 86, 86, 86, 86, 86, 86, 86,
            86, 86, 86, 86, 86, 86, 86, 86,
            86, 86, 86, 86, 86, 86, 86, 86,
            86, 130, 144, 86, 86, 86, 86, 86,
            86, 86, 86, 86, 86, 86, 86, 86,
            86, 86, 86, 86, 86, 86, 86 ),
        array( -1, -1, -1, 377, -1, -1, 377, 378,
            -1, -1, 174, -1, -1, -1, -1, -1,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 379, 377, 377, 377,
            -1, -1, -1, -1, 505, -1, 377, -1,
            -1, -1, -1, -1, -1, 19, 377, 377,
            377, 377, 377, 377, 377, -1, 377 ),
        array( -1, -1, -1, -1, -1, -1, 380, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            380, 380, 380, 380, 380, 380, 380, 380,
            380, 380, 380, 380, 380, 380, 380, 380,
            380, 380, 380, 380, -1, 380, 380, 380,
            -1, -1, -1, -1, -1, -1, 380, -1,
            -1, -1, -1, -1, -1, -1, 380, 380,
            380, -1, 380, 380, 380, -1, -1 ),
        array( -1, -1, -1, 381, -1, -1, 381, -1,
            -1, -1, -1, -1, -1, -1, -1, 381,
            381, 381, 381, 381, 381, 381, 381, 381,
            381, 381, 381, 381, 381, 381, 381, 381,
            381, 381, 381, 381, -1, 381, 381, 381,
            -1, -1, -1, -1, -1, -1, 381, -1,
            -1, -1, -1, -1, -1, -1, 381, 381,
            381, 381, 381, 381, 381, -1, 381 ),
        array( -1, -1, -1, 380, -1, -1, 380, 378,
            -1, -1, 174, -1, -1, -1, -1, -1,
            380, 380, 380, 380, 380, 380, 380, 380,
            380, 380, 380, 380, 380, 380, 380, 380,
            380, 380, 380, 380, 383, 380, 380, 380,
            -1, -1, -1, -1, 506, -1, 380, -1,
            -1, -1, -1, -1, -1, 19, 380, 380,
            380, 380, 380, 380, 380, -1, 380 ),
        array( -1, -1, -1, 381, -1, -1, 381, -1,
            -1, -1, -1, -1, -1, -1, -1, 381,
            381, 381, 381, 381, 381, 381, 381, 381,
            381, 381, 381, 381, 381, 381, 381, 381,
            381, 381, 381, 381, -1, 381, 381, 381,
            -1, 384, -1, -1, 385, -1, 381, -1,
            -1, -1, -1, -1, -1, -1, 381, 381,
            381, 381, 381, 381, 381, -1, 381 ),
        array( -1, -1, -1, 377, -1, -1, 377, 378,
            -1, -1, 195, -1, -1, -1, -1, -1,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 379, 377, 377, 377,
            -1, -1, -1, -1, 505, -1, 377, -1,
            -1, -1, -1, -1, -1, 19, 377, 377,
            377, 377, 377, 377, 377, -1, 377 ),
        array( -1, -1, -1, 387, -1, -1, 387, -1,
            -1, -1, -1, -1, -1, -1, -1, 387,
            387, 387, 387, 387, 387, 387, 387, 387,
            387, 387, 387, 387, 387, 387, 387, 387,
            387, 387, 387, 387, -1, 387, 387, 387,
            -1, -1, -1, -1, -1, -1, 387, -1,
            -1, -1, -1, -1, -1, -1, 387, 387,
            387, 387, 387, 387, 387, -1, 387 ),
        array( -1, -1, -1, -1, -1, -1, -1, 378,
            -1, -1, 174, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 379, -1, -1, -1,
            -1, -1, -1, -1, 505, -1, -1, -1,
            -1, -1, -1, -1, -1, 19, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 388, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 377, -1, -1, 377, 378,
            -1, -1, 203, -1, -1, -1, -1, -1,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 379, 377, 377, 377,
            -1, -1, -1, -1, 505, -1, 377, -1,
            -1, -1, -1, -1, -1, 19, 377, 377,
            377, 377, 377, 377, 377, -1, 377 ),
        array( -1, -1, -1, 387, -1, -1, 387, -1,
            -1, -1, -1, -1, -1, -1, -1, 387,
            387, 387, 387, 387, 387, 387, 387, 387,
            387, 387, 387, 387, 387, 387, 387, 387,
            387, 387, 387, 387, -1, 387, 387, 387,
            -1, 389, -1, -1, 390, -1, 387, -1,
            -1, -1, -1, -1, -1, -1, 387, 387,
            387, 387, 387, 387, 387, -1, 387 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 384, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 384, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 378,
            -1, -1, 174, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 383, -1, -1, -1,
            -1, -1, -1, -1, 506, -1, -1, -1,
            -1, -1, -1, -1, -1, 19, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 391, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 389, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 389, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 377, -1, -1, 377, 378,
            -1, -1, 229, -1, -1, -1, -1, -1,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 379, 377, 377, 377,
            -1, -1, -1, -1, 505, -1, 377, -1,
            -1, -1, -1, -1, -1, 19, 377, 377,
            377, 377, 377, 377, 377, -1, 377 ),
        array( -1, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, -1,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131, 131,
            131, 131, 131, 131, 131, 131, 131 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 89, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 145, 145, 145, 145, 145, 145, 145,
            145, 145, 145, 145, 145, 91, 145, 145,
            145, 145, 145, 145, 145, 145, 145, 145,
            145, 145, 145, 145, 145, 145, 145, 145,
            145, 145, 145, 145, 145, 145, 145, 145,
            145, 145, 145, 145, 145, 145, 145, 145,
            145, 145, 145, 145, 145, 145, 145, 145,
            145, 145, 145, 145, 145, 145, 145 ),
        array( -1, 94, 94, 94, 94, 94, 94, 94,
            94, 94, 94, 94, 94, 95, 94, 135,
            94, 94, 94, 94, 94, 94, 94, 94,
            94, 94, 94, 94, 94, 94, 94, 94,
            94, 94, 94, 94, 94, 94, 94, 94,
            94, 94, 94, 94, 94, 94, 94, 94,
            94, 94, 94, 94, 94, 94, 94, 94,
            94, 94, 94, 94, 94, 94, 94 ),
        array( -1, -1, -1, 397, -1, -1, 397, 399,
            -1, -1, 400, -1, -1, -1, -1, -1,
            397, 397, 397, 397, 397, 397, 397, 397,
            397, 397, 397, 397, 397, 397, 397, 397,
            397, 397, 397, 397, 401, 397, 397, 397,
            -1, -1, -1, -1, 507, -1, 397, -1,
            -1, -1, -1, -1, -1, 96, 397, 397,
            397, 397, 397, 397, 397, -1, 397 ),
        array( -1, -1, -1, -1, -1, -1, 403, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            403, 403, 403, 403, 403, 403, 403, 403,
            403, 403, 403, 403, 403, 403, 403, 403,
            403, 403, 403, 403, -1, 403, 403, 403,
            -1, -1, -1, -1, -1, -1, 403, -1,
            -1, -1, -1, -1, -1, -1, 403, 403,
            403, -1, 403, 403, 403, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 404, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            404, 404, 404, 404, 404, 404, 404, 404,
            404, 404, 404, 404, 404, 404, 404, 404,
            404, 404, 404, 404, -1, 404, 404, 404,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 404, 404,
            404, -1, 404, 404, 404, -1, -1 ),
        array( -1, -1, -1, 405, -1, -1, 405, -1,
            -1, -1, -1, -1, -1, -1, -1, 405,
            405, 405, 405, 405, 405, 405, 405, 405,
            405, 405, 405, 405, 405, 405, 405, 405,
            405, 405, 405, 405, -1, 405, 405, 405,
            -1, -1, -1, -1, -1, -1, 405, -1,
            -1, -1, -1, -1, -1, -1, 405, 405,
            405, 405, 405, 405, 405, -1, 405 ),
        array( -1, -1, -1, 403, -1, -1, 403, 399,
            -1, -1, 400, -1, -1, -1, -1, -1,
            403, 403, 403, 403, 403, 403, 403, 403,
            403, 403, 403, 403, 403, 403, 403, 403,
            403, 403, 403, 403, 406, 403, 403, 403,
            -1, -1, -1, -1, 508, -1, 403, -1,
            -1, -1, -1, -1, -1, 96, 403, 403,
            403, 403, 403, 403, 403, -1, 403 ),
        array( -1, -1, -1, -1, -1, -1, 404, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            404, 404, 404, 404, 404, 404, 404, 404,
            404, 404, 404, 404, 404, 404, 404, 404,
            404, 404, 404, 404, -1, 404, 404, 404,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 96, 404, 404,
            404, -1, 404, 404, 404, -1, -1 ),
        array( -1, -1, -1, 405, -1, -1, 405, -1,
            -1, -1, -1, -1, -1, -1, -1, 405,
            405, 405, 405, 405, 405, 405, 405, 405,
            405, 405, 405, 405, 405, 405, 405, 405,
            405, 405, 405, 405, -1, 405, 405, 405,
            -1, 407, -1, -1, 408, -1, 405, -1,
            -1, -1, -1, -1, -1, -1, 405, 405,
            405, 405, 405, 405, 405, -1, 405 ),
        array( -1, -1, -1, 409, -1, -1, 409, -1,
            -1, -1, -1, -1, -1, -1, -1, 409,
            409, 409, 409, 409, 409, 409, 409, 409,
            409, 409, 409, 409, 409, 409, 409, 409,
            409, 409, 409, 409, -1, 409, 409, 409,
            -1, -1, -1, -1, -1, -1, 409, -1,
            -1, -1, -1, -1, -1, -1, 409, 409,
            409, 409, 409, 409, 409, -1, 409 ),
        array( -1, -1, -1, -1, -1, -1, -1, 399,
            -1, -1, 400, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 401, -1, -1, -1,
            -1, -1, -1, -1, 507, -1, -1, -1,
            -1, -1, -1, -1, -1, 96, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 410, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 409, -1, -1, 409, -1,
            -1, -1, -1, -1, -1, -1, -1, 409,
            409, 409, 409, 409, 409, 409, 409, 409,
            409, 409, 409, 409, 409, 409, 409, 409,
            409, 409, 409, 409, -1, 409, 409, 409,
            -1, 411, -1, -1, 412, -1, 409, -1,
            -1, -1, -1, -1, -1, -1, 409, 409,
            409, 409, 409, 409, 409, -1, 409 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 407, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 407, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 399,
            -1, -1, 400, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 406, -1, -1, -1,
            -1, -1, -1, -1, 508, -1, -1, -1,
            -1, -1, -1, -1, -1, 96, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 413, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 411, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 411, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, -1, -1, -1, -1, -1, 415, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            415, 415, 415, 415, 415, 415, 415, 415,
            415, 415, 415, 415, 415, 415, 415, 415,
            415, 415, 415, 415, -1, 415, 415, 415,
            -1, -1, -1, -1, -1, -1, 415, -1,
            -1, -1, -1, -1, -1, -1, 415, 415,
            415, -1, 415, 415, 415, -1, -1 ),
        array( -1, -1, -1, 415, -1, -1, 415, 416,
            -1, -1, 417, -1, -1, -1, -1, -1,
            415, 415, 415, 415, 415, 415, 415, 415,
            415, 415, 415, 415, 415, 415, 415, 415,
            415, 415, 415, 415, 418, 415, 415, 415,
            -1, -1, -1, -1, 509, -1, 415, -1,
            -1, -1, -1, -1, 97, 98, 415, 415,
            415, 415, 415, 415, 415, -1, 415 ),
        array( -1, -1, -1, -1, -1, -1, 419, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            419, 419, 419, 419, 419, 419, 419, 419,
            419, 419, 419, 419, 419, 419, 419, 419,
            419, 419, 419, 419, -1, 419, 419, 419,
            -1, -1, -1, -1, -1, -1, 419, -1,
            -1, -1, -1, -1, -1, -1, 419, 419,
            419, -1, 419, 419, 419, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 420, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            420, 420, 420, 420, 420, 420, 420, 420,
            420, 420, 420, 420, 420, 420, 420, 420,
            420, 420, 420, 420, -1, 420, 420, 420,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 420, 420,
            420, -1, 420, 420, 420, -1, -1 ),
        array( -1, -1, -1, 421, -1, -1, 421, -1,
            -1, -1, -1, -1, -1, -1, -1, 421,
            421, 421, 421, 421, 421, 421, 421, 421,
            421, 421, 421, 421, 421, 421, 421, 421,
            421, 421, 421, 421, -1, 421, 421, 421,
            -1, -1, -1, -1, -1, -1, 421, -1,
            -1, -1, -1, -1, -1, -1, 421, 421,
            421, 421, 421, 421, 421, -1, 421 ),
        array( -1, -1, -1, 419, -1, -1, 419, 416,
            -1, -1, 417, -1, -1, -1, -1, -1,
            419, 419, 419, 419, 419, 419, 419, 419,
            419, 419, 419, 419, 419, 419, 419, 419,
            419, 419, 419, 419, 422, 419, 419, 419,
            -1, -1, -1, -1, 510, -1, 419, -1,
            -1, -1, -1, -1, 97, 98, 419, 419,
            419, 419, 419, 419, 419, -1, 419 ),
        array( -1, -1, -1, -1, -1, -1, 420, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            420, 420, 420, 420, 420, 420, 420, 420,
            420, 420, 420, 420, 420, 420, 420, 420,
            420, 420, 420, 420, -1, 420, 420, 420,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 98, 420, 420,
            420, -1, 420, 420, 420, -1, -1 ),
        array( -1, -1, -1, 421, -1, -1, 421, -1,
            -1, -1, -1, -1, -1, -1, -1, 421,
            421, 421, 421, 421, 421, 421, 421, 421,
            421, 421, 421, 421, 421, 421, 421, 421,
            421, 421, 421, 421, -1, 421, 421, 421,
            -1, 423, -1, -1, 424, -1, 421, -1,
            -1, -1, -1, -1, -1, -1, 421, 421,
            421, 421, 421, 421, 421, -1, 421 ),
        array( -1, -1, -1, 425, -1, -1, 425, -1,
            -1, -1, -1, -1, -1, -1, -1, 425,
            425, 425, 425, 425, 425, 425, 425, 425,
            425, 425, 425, 425, 425, 425, 425, 425,
            425, 425, 425, 425, -1, 425, 425, 425,
            -1, -1, -1, -1, -1, -1, 425, -1,
            -1, -1, -1, -1, -1, -1, 425, 425,
            425, 425, 425, 425, 425, -1, 425 ),
        array( -1, -1, -1, -1, -1, -1, -1, 416,
            -1, -1, 417, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 418, -1, -1, -1,
            -1, -1, -1, -1, 509, -1, -1, -1,
            -1, -1, -1, -1, -1, 98, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 426, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 425, -1, -1, 425, -1,
            -1, -1, -1, -1, -1, -1, -1, 425,
            425, 425, 425, 425, 425, 425, 425, 425,
            425, 425, 425, 425, 425, 425, 425, 425,
            425, 425, 425, 425, -1, 425, 425, 425,
            -1, 427, -1, -1, 428, -1, 425, -1,
            -1, -1, -1, -1, -1, -1, 425, 425,
            425, 425, 425, 425, 425, -1, 425 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 423, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 423, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 416,
            -1, -1, 417, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 422, -1, -1, -1,
            -1, -1, -1, -1, 510, -1, -1, -1,
            -1, -1, -1, -1, -1, 98, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 429, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 427, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 427, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 136, 3, 3, 3, 3, 3, 3,
            148, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            -1, 150, -1, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3 ),
        array( -1, -1, -1, 432, -1, -1, 432, 355,
            -1, -1, -1, -1, -1, -1, -1, -1,
            432, 432, 432, 432, 432, 432, 432, 432,
            432, 432, 432, 432, 432, 432, 432, 432,
            432, 432, 432, 432, 362, 432, 432, 432,
            -1, -1, -1, -1, 504, -1, 432, -1,
            -1, -1, -1, -1, -1, -1, 432, 432,
            432, 432, 432, 432, 432, -1, 432 ),
        array( -1, -1, -1, -1, -1, -1, -1, 355,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 362, -1, -1, -1,
            -1, -1, -1, -1, 504, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 178, 162, 162, -1, 162 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 195, -1, -1, -1, -1, -1,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 162, 162, 162, -1, 162 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 435,
            -1, -1, -1, -1, -1, 435, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 206, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 207, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 232, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 233, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 248, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            248, 248, 248, 248, 248, 248, 248, 248,
            248, 248, 248, 248, 248, 248, 248, 248,
            248, 248, 248, 248, -1, 248, 248, 248,
            -1, -1, -1, -1, -1, -1, 248, -1,
            -1, -1, -1, -1, -1, -1, 248, 248,
            248, -1, 248, 248, 248, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 257, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 283, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 292, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 343, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            343, 343, 343, 343, 343, 343, 343, 343,
            343, 343, 343, 343, 343, 343, 343, 343,
            343, 343, 343, 343, -1, 343, 343, 343,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 343, 343,
            343, -1, 343, 343, 343, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 433, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 433, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 377, -1, -1, 377, 378,
            -1, -1, 174, -1, -1, -1, -1, -1,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 382, 377, 377, 377,
            377, 377, 377, 377, 379, 377, 377, 377,
            -1, -1, -1, -1, 505, -1, 377, -1,
            -1, -1, -1, -1, -1, 19, 377, 377,
            377, 377, 377, 377, 377, -1, 377 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 436, 162, 162, 162,
            162, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 162, 162, 162, -1, 162 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 451,
            -1, -1, -1, -1, -1, 451, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 347, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            347, 347, 347, 347, 347, 347, 347, 347,
            347, 347, 347, 347, 347, 347, 347, 347,
            347, 347, 347, 347, -1, 347, 347, 347,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 347, 347,
            347, -1, 347, 347, 347, -1, -1 ),
        array( -1, -1, -1, 377, -1, -1, 377, 378,
            -1, -1, 174, -1, -1, -1, -1, -1,
            377, 386, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 379, 377, 377, 377,
            -1, -1, -1, -1, 505, -1, 377, -1,
            -1, -1, -1, -1, -1, 19, 377, 377,
            377, 377, 377, 377, 377, -1, 377 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            162, 194, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 162, 162, 162, -1, 162 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 456,
            -1, -1, -1, -1, -1, 456, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 377, -1, -1, 377, 378,
            -1, -1, 174, -1, -1, -1, -1, -1,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 379, 377, 377, 377,
            -1, -1, -1, -1, 505, -1, 377, -1,
            -1, -1, -1, -1, -1, 19, 377, 377,
            377, 377, 377, 377, 392, -1, 377 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 162, 162, 224, -1, 162 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 460,
            -1, -1, -1, -1, -1, 460, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 462,
            -1, -1, -1, -1, -1, 462, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 464,
            -1, -1, -1, -1, -1, 464, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 466,
            -1, -1, -1, -1, -1, 466, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 468,
            -1, -1, -1, -1, -1, 468, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 470,
            -1, -1, -1, -1, -1, 470, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 472,
            -1, -1, -1, -1, -1, 472, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 474,
            -1, -1, -1, -1, -1, 474, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 476,
            -1, -1, -1, -1, -1, 476, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 478,
            -1, -1, -1, -1, -1, 478, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 480,
            -1, -1, -1, -1, -1, 480, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 482,
            -1, -1, -1, -1, -1, 482, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 484,
            -1, -1, -1, -1, -1, 484, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 486,
            -1, -1, -1, -1, -1, 486, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            162, 162, 162, 162, 494, 162, 162, 162,
            162, 162, 450, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 162, 162, 162, -1, 162 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 452, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 445, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 446, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 467, -1, -1, -1, -1, 306 ),
        array( -1, -1, -1, 377, -1, -1, 377, 378,
            -1, -1, 174, -1, -1, -1, -1, -1,
            377, 377, 377, 377, 496, 377, 377, 377,
            377, 377, 449, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 379, 377, 377, 377,
            -1, -1, -1, -1, 505, -1, 377, -1,
            -1, -1, -1, -1, -1, 19, 377, 377,
            377, 377, 377, 377, 377, -1, 377 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 455,
            162, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 162, 162, 162, -1, 162 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 457, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 377, -1, -1, 377, 378,
            -1, -1, 174, -1, -1, -1, -1, -1,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 377, 377, 377, 454,
            377, 377, 377, 377, 379, 377, 377, 377,
            -1, -1, -1, -1, 505, -1, 377, -1,
            -1, -1, -1, -1, -1, 19, 377, 377,
            377, 377, 377, 377, 377, -1, 377 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            459, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 162, 162, 162, -1, 162 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 461, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 377, -1, -1, 377, 378,
            -1, -1, 174, -1, -1, -1, -1, -1,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 377, 377, 377, 377,
            458, 377, 377, 377, 379, 377, 377, 377,
            -1, -1, -1, -1, 505, -1, 377, -1,
            -1, -1, -1, -1, -1, 19, 377, 377,
            377, 377, 377, 377, 377, -1, 377 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 463, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 469, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 471, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 473, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 475, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 477, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 479, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 481, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 483, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 485, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 487, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            162, 162, 162, 162, 162, 497, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 162, 162, 162, -1, 162 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 490, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 491, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 377, -1, -1, 377, 378,
            -1, -1, 174, -1, -1, -1, -1, -1,
            377, 377, 377, 377, 377, 499, 377, 377,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 379, 377, 377, 377,
            -1, -1, -1, -1, 505, -1, 377, -1,
            -1, -1, -1, -1, -1, 19, 377, 377,
            377, 377, 377, 377, 377, -1, 377 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 512, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            162, 511, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 162, 162, 162, -1, 162 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 515, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 513, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 377, -1, -1, 377, 378,
            -1, -1, 174, -1, -1, -1, -1, -1,
            377, 514, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 379, 377, 377, 377,
            -1, -1, -1, -1, 505, -1, 377, -1,
            -1, -1, -1, -1, -1, 19, 377, 377,
            377, 377, 377, 377, 377, -1, 377 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 516, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 162, 162, 162, -1, 162 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 517, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 518, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 377, -1, -1, 377, 378,
            -1, -1, 174, -1, -1, -1, -1, -1,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 519, 377, 379, 377, 377, 377,
            -1, -1, -1, -1, 505, -1, 377, -1,
            -1, -1, -1, -1, -1, 19, 377, 377,
            377, 377, 377, 377, 377, -1, 377 ),
        array( -1, -1, -1, 162, -1, -1, 162, 172,
            -1, -1, 174, -1, -1, -1, -1, -1,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 176, 162, 162, 162,
            -1, -1, -1, -1, 177, -1, 162, -1,
            -1, -1, -1, -1, 18, 19, 162, 162,
            162, 162, 162, 520, 162, -1, 162 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 521, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 522, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 377, -1, -1, 377, 378,
            -1, -1, 174, -1, -1, -1, -1, -1,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 377, 377, 377, 377,
            377, 377, 377, 377, 379, 377, 377, 377,
            -1, -1, -1, -1, 505, -1, 377, -1,
            -1, -1, -1, -1, -1, 19, 377, 377,
            377, 377, 377, 523, 377, -1, 377 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 525, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 526, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 528, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 529, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 535,
            -1, -1, -1, 530, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            531, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 532, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 533, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1 )
        );


    function  yylex()
    {
        $yy_lookahead = '';
        $yy_anchor = YY_NO_ANCHOR;
        $yy_state = $this->yy_state_dtrans[$this->yy_lexical_state];
        $yy_next_state = YY_NO_STATE;
         $yy_last_accept_state = YY_NO_STATE;
        $yy_initial = true;
        $yy_this_accept = 0;
        
        $this->yy_mark_start();
        $yy_this_accept = $this->yy_acpt[$yy_state];
        if (YY_NOT_ACCEPT != $yy_this_accept) {
            $yy_last_accept_state = $yy_state;
            $this->yy_buffer_end = $this->yy_buffer_index;
        }
        while (true) {
            if ($yy_initial && $this->yy_at_bol) {
                $yy_lookahead =  YY_BOL;
            } else {
                $yy_lookahead = $this->yy_advance();
            }
            $yy_next_state = $this->yy_nxt[$this->yy_rmap[$yy_state]][$this->yy_cmap[$yy_lookahead]];
            if (YY_EOF == $yy_lookahead && $yy_initial) {
                return false;            }
            if (YY_F != $yy_next_state) {
                $yy_state = $yy_next_state;
                $yy_initial = false;
                $yy_this_accept = $this->yy_acpt[$yy_state];
                if (YY_NOT_ACCEPT != $yy_this_accept) {
                    $yy_last_accept_state = $yy_state;
                    $this->yy_buffer_end = $this->yy_buffer_index;
                }
            } else {
                if (YY_NO_STATE == $yy_last_accept_state) {
                    $this->yy_error(1,1);
                    if ($this->_fatal) {
                        return;
                    }
                } else {
                    $yy_anchor = $this->yy_acpt[$yy_last_accept_state];
                    if (0 != (YY_END & $yy_anchor)) {
                        $this->yy_move_end();
                    }
                    $this->yy_to_mark();
                    if ($yy_last_accept_state < 0) {
                        if ($yy_last_accept_state < 536) {
                            $this->yy_error(YY_E_INTERNAL, false);
                            if ($this->_fatal) {
                                return;
                            }
                        }
                    } else {

                        switch ($yy_last_accept_state) {
case 2:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 3:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return FLY_FLEXY_TOKEN_OK;
}
case 4:
{
    // &abc;
    $this->value = $this->createToken('Text');
    return FLY_FLEXY_TOKEN_OK;
}
case 5:
{
    //<name -- start tag */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    $this->tagName = trim(substr($this->yytext(),1));
    $this->tokenName = 'Tag';
    $this->value = '';
    $this->attributes = array();
    $this->yybegin(IN_ATTR);
    return FLY_FLEXY_TOKEN_NONE;
}
case 6:
{  
    // <> -- empty start tag */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    return $this->raiseError("empty tag"); 
}
case 7:
{ 
    /* <? php start.. */
    //echo "STARTING PHP?\n";
    $this->yyPhpBegin = $this->yy_buffer_start;
    $this->yybegin(IN_PHP);
    return FLY_FLEXY_TOKEN_NONE;
}
case 8:
{
    // &#123;
    $this->value = $this->createToken('Text');
    return FLY_FLEXY_TOKEN_OK;
}
case 9:
{
    // &#abc;
    $this->value = $this->createToken('Text');
    return FLY_FLEXY_TOKEN_OK;
}
case 10:
{
    /* </title> -- end tag */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    if ($this->inStyle) {
        $this->inStyle = false;
    }
    $this->tagName = trim(substr($this->yytext(),1));
    $this->tokenName = 'EndTag';
    $this->yybegin(IN_ENDTAG);
    $this->value = '';
    return FLY_FLEXY_TOKEN_NONE;
}
case 11:
{
    /* </> -- empty end tag */  
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    return $this->raiseError("empty end tag not handled");
}
case 12:
{
    /* <!DOCTYPE -- markup declaration */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    $this->value = $this->createToken('Doctype');
    $this->yybegin(IN_MD);
    return FLY_FLEXY_TOKEN_OK;
}
case 13:
{
    /* <!> */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    return $this->raiseError("empty markup tag not handled"); 
}
case 14:
{
    /* <![ -- marked section */
    return $this->returnSimple();
}
case 15:
{ 
    /* eg. <?xml-stylesheet, <?php ... */
    $t = $this->yytext();
    $tagname = trim(strtoupper(substr($t,2)));
   // echo "STARTING XML? $t:$tagname\n";
    if ($tagname == 'PHP') {
        $this->yyPhpBegin = $this->yy_buffer_start;
        $this->yybegin(IN_PHP);
        return FLY_FLEXY_TOKEN_NONE;
    }
    // not php - it's xlm or something...
    // we treat this like a tag???
    // we are going to have to escape it eventually...!!!
    $this->tagName = trim(substr($t,1));
    $this->tokenName = 'Tag';
    $this->value = '';
    $this->attributes = array();
    $this->yybegin(IN_ATTR);
    return FLY_FLEXY_TOKEN_NONE;
}
case 16:
{
    $this->value = $this->createToken('GetTextEnd','');
    return FLY_FLEXY_TOKEN_OK;
}
case 17:
{ 
    /* ]]> -- marked section end */
    return $this->returnSimple();
}
case 18:
{
    $this->value =  '';
    $this->flexyMethod = substr($this->yytext(),1,-1);
    $this->flexyArgs = array();
    $this->yybegin(IN_FLEXYMETHOD);
    return FLY_FLEXY_TOKEN_NONE;
}
case 19:
{
    $t =  $this->yytext();
    $t = substr($t,1,-1);
    $this->value = $this->createToken('Var'  , $t);
    return FLY_FLEXY_TOKEN_OK;
}
case 20:
{
    $this->value = $this->createToken('GetTextStart','');
    return FLY_FLEXY_TOKEN_OK;
}
case 21:
{
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    /* </name <  -- unclosed end tag */
    return $this->raiseError("Unclosed  end tag");
}
case 22:
{
    /* <!--  -- comment declaration */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    if ($this->inStyle) {
        $this->value = $this->createToken('Comment');
        $this->yybegin(IN_COMSTYLE);
        return FLY_FLEXY_TOKEN_OK;
    }
    $this->yyCommentBegin = $this->yy_buffer_end;
    //$this->value = $this->createToken('Comment',$this->yytext(),$this->yyline);
    $this->yybegin(IN_COM);
    return FLY_FLEXY_TOKEN_NONE;
}
case 23:
{
    $this->value = $this->createToken('End', '');
    return FLY_FLEXY_TOKEN_OK;
}
case 24:
{
    $this->value =  '';
    $this->flexyMethod = substr($this->yytext(),1,-1);
    $this->flexyArgs = array();
    $this->yybegin(IN_FLEXYMETHOD);
    return FLY_FLEXY_TOKEN_NONE;
}
case 25:
{
    $this->value = $this->createToken('If',substr($this->yytext(),4,-1));
    return FLY_FLEXY_TOKEN_OK;
}
case 26:
{
    $this->value = $this->createToken('Else', '');
    return FLY_FLEXY_TOKEN_OK;
}
case 27:
{
    /* <![ -- marked section */
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    $this->yybegin(IN_CDATA);
    return FLY_FLEXY_TOKEN_OK;
}
case 28:
{
    return $this->raiseError('invalid syntax for Foreach','',true);
}
case 29:
{
    $this->value = $this->createToken('Foreach', explode(',',substr($this->yytext(),9,-1)));
    return FLY_FLEXY_TOKEN_OK;
}
case 30:
{
    $this->value = $this->createToken('Foreach',  explode(',',substr($this->yytext(),9,-1)));
    return FLY_FLEXY_TOKEN_OK;
}
case 31:
{
}
case 32:
{
    /* <!--  -- comment declaration */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    if ($this->inStyle) {
        return FLY_FLEXY_TOKEN_ERROR;
    }
    $this->tagName = trim(substr($this->yytext(),5));
    $this->tokenName = 'DWTag';
    $this->value = '';
    $this->attributes = array();
    $this->yybegin(IN_ATTR);
    return FLY_FLEXY_TOKEN_NONE;
}
case 33:
{
}
case 34:
{
    $this->attrVal[] = $this->yytext();
    return FLY_FLEXY_TOKEN_NONE;
}
case 35:
{
    $this->attrVal[] = "'";
     //var_dump($this->attrVal);
    $s = "";
    foreach($this->attrVal as $v) {
        if (!is_string($v)) {
            $this->attributes[$this->attrKey] = $this->attrVal;
            $this->yybegin(IN_ATTR);
            return FLY_FLEXY_TOKEN_NONE;
        }
        $s .= $v;
    }
    $this->attributes[$this->attrKey] = $s;
    $this->yybegin(IN_ATTR);
    return FLY_FLEXY_TOKEN_NONE;
}
case 36:
{
    $this->value =  '';
    $n = $this->yytext();
    if ($n{0} != "{") {
        $n = substr($n,2);
    }
    $this->flexyMethod = substr($n,1,-1);
    $this->flexyArgs = array();
    $this->flexyMethodState = $this->yy_lexical_state;
    $this->yybegin(IN_FLEXYMETHODQUOTED);
    return FLY_FLEXY_TOKEN_NONE;
}
case 37:
{
    $n = $this->yytext();
    if ($n{0} != '{') {
        $n = substr($n,3);
    } else {
        $n = substr($n,1);
    }
    if ($n{strlen($n)-1} != '}') {
        $n = substr($n,0,-3);
    } else {
        $n = substr($n,0,-1);
    }
    $this->attrVal[] = $this->createToken('Var'  , $n);
    return FLY_FLEXY_TOKEN_NONE;
}
case 38:
{
    $this->value = '';
    return FLY_FLEXY_TOKEN_NONE;
}
case 39:
{
    // <foo^<bar> -- unclosed start tag */
    return $this->raiseError("Unclosed tags not supported"); 
}
case 40:
{
    $this->value = $this->createToken($this->tokenName, array($this->tagName,$this->attributes));
    if (strtoupper($this->tagName) == 'SCRIPT') {
        $this->yybegin(IN_SCRIPT);
        return FLY_FLEXY_TOKEN_OK;
    }
    if (strtoupper($this->tagName) == 'STYLE') {
        $this->inStyle = true;
    } else {
        $this->inStyle = false;
    }
    $this->yybegin(YYINITIAL);
    return FLY_FLEXY_TOKEN_OK;
}
case 41:
{
    $this->value = $this->createToken($this->tokenName, array($this->tagName,$this->attributes));
    $this->yybegin(YYINITIAL);
    return FLY_FLEXY_TOKEN_OK;
}
case 42:
{
    // <img src="xxx" ...ismap...> the ismap */
    $this->attributes[trim($this->yytext())] = true;
    $this->value = '';
    return FLY_FLEXY_TOKEN_NONE;
}
case 43:
{
    // <em^/ -- NET tag */
    $this->yybegin(IN_NETDATA);
    $this->attributes["/"] = true;
    $this->value = '';
    return FLY_FLEXY_TOKEN_NONE;
}
case 44:
{
   // <a ^href = "xxx"> -- attribute name 
    $this->attrKey = substr(trim($this->yytext()),0,-1);
    $this->yybegin(IN_ATTRVAL);
    $this->value = '';
    return FLY_FLEXY_TOKEN_NONE;
}
case 45:
{
    // <em^/ -- NET tag */
    $this->attributes["/"] = true;
    $this->value = $this->createToken($this->tokenName, array($this->tagName,$this->attributes));
    $this->yybegin(YYINITIAL);
    return FLY_FLEXY_TOKEN_OK;
}
case 46:
{
    // <em^/ -- NET tag */
    $this->attributes["?"] = true;
    $this->value = $this->createToken($this->tokenName, array($this->tagName,$this->attributes));
    $this->yybegin(YYINITIAL);
    return FLY_FLEXY_TOKEN_OK;
}
case 47:
{
    // <a href = ^http://foo/> -- unquoted literal HACK */                          
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    //   $this->raiseError("attribute value needs quotes");
    $this->value = '';
    return FLY_FLEXY_TOKEN_NONE;
}
case 48:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return FLY_FLEXY_TOKEN_NONE;
}
case 49:
{
    // <em^/ -- NET tag */
    return $this->raiseError("attribute value missing"); 
}
case 50:
{ 
    return $this->raiseError("Tag close found where attribute value expected"); 
}
case 51:
{
	//echo "STARTING SINGLEQUOTE";
    $this->attrVal = array( "'");
    $this->yybegin(IN_SINGLEQUOTE);
    return FLY_FLEXY_TOKEN_NONE;
}
case 52:
{
    //echo "START QUOTE";
    $this->attrVal =array("\"");
    $this->yybegin(IN_DOUBLEQUOTE);
    return FLY_FLEXY_TOKEN_NONE;
}
case 53:
{ 
    // whitespace switch back to IN_ATTR MODE.
    $this->value = '';
    $this->yybegin(IN_ATTR);
    return FLY_FLEXY_TOKEN_NONE;
}
case 54:
{ 
    return $this->raiseError("extraneous character in end tag"); 
}
case 55:
{ 
    $this->value = $this->createToken($this->tokenName, array($this->tagName));
        array($this->tagName);
    $this->yybegin(YYINITIAL);
    return FLY_FLEXY_TOKEN_OK;
}
case 56:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return FLY_FLEXY_TOKEN_NONE;
}
case 57:
{
    //echo "GOT END DATA:".$this->yytext();
    $this->attrVal[] = "\"";
    $s = "";
    foreach($this->attrVal as $v) {
        if (!is_string($v)) {
            $this->attributes[$this->attrKey] = $this->attrVal;
            $this->yybegin(IN_ATTR);
            return FLY_FLEXY_TOKEN_NONE;
        }
        $s .= $v;
    }
    $this->attributes[$this->attrKey] = $s;
    $this->yybegin(IN_ATTR);
    return FLY_FLEXY_TOKEN_NONE;
}
case 58:
{ 
    $this->value = $this->createToken('WhiteSpace');
    return FLY_FLEXY_TOKEN_OK; 
}
case 59:
{
    return $this->raiseError("illegal character in markup declaration (0x".dechex(ord($this->yytext())).')');
}
case 60:
{   
    $this->value = $this->createToken('Number');
    return FLY_FLEXY_TOKEN_OK; 
}
case 61:
{ 
    $this->value = $this->createToken('Name');
    return FLY_FLEXY_TOKEN_OK; 
}
case 62:
{ 
    $this->value = $this->createToken('NameT');
    return FLY_FLEXY_TOKEN_OK; 
}
case 63:
{   
    $this->value = $this->createToken('CloseTag');
    $this->yybegin(YYINITIAL); 
    return FLY_FLEXY_TOKEN_OK; 
}
case 64:
{
    // <!doctype foo ^[  -- declaration subset */
    $this->value = $this->createToken('BeginDS');
    $this->yybegin(IN_DS);
    return FLY_FLEXY_TOKEN_OK;
}
case 65:
{ 
    $this->value = $this->createToken('NumberT');
    return FLY_FLEXY_TOKEN_OK; 
}
case 66:
{
    // <!entity ^% foo system "..." ...> -- parameter entity definition */
    $this->value = $this->createToken('EntityPar');
    return FLY_FLEXY_TOKEN_OK;
}
case 67:
{
    // <!doctype ^%foo;> -- parameter entity reference */
    $this->value = $this->createToken('EntityRef');
    return FLY_FLEXY_TOKEN_OK;
}
case 68:
{ 
    $this->value = $this->createToken('Literal');
    return FLY_FLEXY_TOKEN_OK; 
}
case 69:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    return FLY_FLEXY_TOKEN_NONE;
}
case 70:
{
	// inside comment -- without a >
	return FLY_FLEXY_TOKEN_NONE;
}
case 71:
{   
    $this->value = $this->createToken('Comment',
        '<!--'. substr($this->yy_buffer,$this->yyCommentBegin ,$this->yy_buffer_end - $this->yyCommentBegin),
        $this->yyline,$this->yyCommentBegin
    );
    $this->yybegin(YYINITIAL); 
    return FLY_FLEXY_TOKEN_OK; 
}
case 72:
{ 
    $this->value = $this->createToken('Declaration');
    return FLY_FLEXY_TOKEN_OK;
}
case 73:
{ 
    // ] -- declaration subset close */
    $this->value = $this->createToken('DSEndSubset');
    $this->yybegin(IN_DSCOM); 
    return FLY_FLEXY_TOKEN_OK;
}
case 74:
{
    // ]]> -- marked section end */
     $this->value = $this->createToken('DSEnd');
    $this->yybegin(YYINITIAL);
    return FLY_FLEXY_TOKEN_OK;
}
case 75:
{
    $t = $this->yytext();
    if ($t{strlen($t)-1} == ",") {
        // add argument
        $this->flexyArgs[] = substr($t,0,-1);
        return FLY_FLEXY_TOKEN_NONE;
    }
    $this->flexyArgs[] = $t;
    return FLY_FLEXY_TOKEN_NONE;
}
case 76:
{
    $t = $this->yytext();
    if ($t{strlen($t)-1} == ",") {
        // add argument
        $this->flexyArgs[] = '#' . substr($t,0,-1) . '#';
        return FLY_FLEXY_TOKEN_NONE;
    }
	if ($t{strlen($t)-1} == ".") {
		$this->flexyArgs[] = substr($t,0,-2);
		$this->flexyArgs = array($this->createToken('MethodChain'  , array($this->flexyMethod, $this->flexyArgs)));
		$this->flexyMethod = '';
		$this->yybegin(IN_METHODCHAIN);
		return FLY_FLEXY_TOKEN_NONE;
	}
    if ($c = strpos($t,':')) {
        $this->flexyMethod .= substr($t,$c,-1);
        $t = '#' . substr($t,0,$c-1) . '#';
    } else {
        $t = '#' . substr($t,0,-2) . '#';
    }
    $this->flexyArgs[] = $t;
    $this->value = $this->createToken('Method', array($this->flexyMethod,$this->flexyArgs));
    $this->yybegin(YYINITIAL);
    return FLY_FLEXY_TOKEN_OK;
}
case 77:
{
    $t = $this->yytext();
    if ($t{strlen($t)-1} == ",") {
        // add argument
        $this->flexyArgs[] = substr($t,0,-1);
        return FLY_FLEXY_TOKEN_NONE;
    }
	if ($t{strlen($t)-1} == ".") {
		$this->flexyArgs[] = substr($t,0,-2);
		$this->flexyArgs = array($this->createToken('MethodChain'  , array($this->flexyMethod, $this->flexyArgs)));
		$this->flexyMethod = '';
		$this->yybegin(IN_METHODCHAIN);
		return FLY_FLEXY_TOKEN_NONE;
	}
    if ($c = strpos($t,':')) {
        $this->flexyMethod .= substr($t,$c,-1);
        $t = substr($t,0,$c-1);
    } else {
        $t = substr($t,0,-2);
    }
    $this->flexyArgs[] = $t;
    $this->value = $this->createToken('Method'  , array($this->flexyMethod,$this->flexyArgs));
    $this->yybegin(YYINITIAL);
    return FLY_FLEXY_TOKEN_OK;
}
case 78:
{
    $t = $this->yytext();
	$this->flexyArgs = array($this->createToken('MethodChain'  , array($this->flexyMethod,$this->flexyArgs)));
	$this->flexyMethod = '';
	$this->yybegin(IN_METHODCHAIN);
	return FLY_FLEXY_TOKEN_NONE;
}
case 79:
{
    $t = $this->yytext();
    if ($t{1} == ':') {
        $this->flexyMethod .= substr($t,1,-1);
    }
    $this->value = $this->createToken('Method'  , array($this->flexyMethod,$this->flexyArgs));
    $this->yybegin(YYINITIAL);
    return FLY_FLEXY_TOKEN_OK;
}
case 80:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return FLY_FLEXY_TOKEN_NONE;
}
case 81:
{
    $t = $this->yytext();
    $this->flexyArgs[] =$t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return FLY_FLEXY_TOKEN_NONE;
}
case 82:
{
    $t = $this->yytext();
    if ($p = strpos($t,':')) {
        $this->flexyMethod .= substr($t,$p,-1);
    }
    $this->attrVal[] = $this->createToken('Method'  , array($this->flexyMethod,$this->flexyArgs));
    $this->yybegin($this->flexyMethodState);
    return FLY_FLEXY_TOKEN_NONE;
}
case 83:
{
    $this->yybegin(IN_FLEXYMETHODQUOTED);
    return FLY_FLEXY_TOKEN_NONE;
}
case 84:
{
    // general text in script..
    $this->value = $this->createToken('Text');
    return FLY_FLEXY_TOKEN_OK;
}
case 85:
{
    // </script>
    $this->value = $this->createToken('EndTag', array('/script'));
    $this->yybegin(YYINITIAL);
    return FLY_FLEXY_TOKEN_OK;
}
case 86:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return FLY_FLEXY_TOKEN_OK;
}
case 87:
{ 
    /* ]]> -- marked section end */
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    $this->yybegin(YYINITIAL);
    return FLY_FLEXY_TOKEN_OK; 
}
case 88:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('DSComment');
    return FLY_FLEXY_TOKEN_OK;
}
case 89:
{   
    $this->value = $this->createToken('DSEnd');
    $this->yybegin(YYINITIAL); 
    return FLY_FLEXY_TOKEN_OK; 
}
case 90:
{     
    /* anything inside of php tags */
    return FLY_FLEXY_TOKEN_NONE;
}
case 91:
{ 
    /* php end */
    $this->value = $this->createToken('Php',
        substr($this->yy_buffer,$this->yyPhpBegin ,$this->yy_buffer_end - $this->yyPhpBegin ),
        $this->yyline,$this->yyPhpBegin);
    $this->yybegin(YYINITIAL);
    return FLY_FLEXY_TOKEN_OK;
}
case 92:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return FLY_FLEXY_TOKEN_OK;
}
case 93:
{
    // we allow anything inside of comstyle!!!
    $this->value = $this->createToken('Comment');
	return FLY_FLEXY_TOKEN_OK;
}
case 94:
{
	// inside style comment -- without a >
    $this->value = $this->createToken('Comment');
	return FLY_FLEXY_TOKEN_OK;
}
case 95:
{   
    // --> inside a style tag.
    $this->value = $this->createToken('Comment');
    $this->yybegin(YYINITIAL); 
    return FLY_FLEXY_TOKEN_OK; 
}
case 96:
{
    // var in commented out style bit..
    $t =  $this->yytext();
    $t = substr($t,1,-1);
    $this->value = $this->createToken('Var', $t);
    return FLY_FLEXY_TOKEN_OK;
}
case 97:
{
    $this->flexyMethod = substr($this->yytext(),0,-1);
    $this->yybegin(IN_FLEXYMETHOD);
	return FLY_FLEXY_TOKEN_NONE;
}
case 98:
{
    $t =  $this->yytext();
    $t = substr($t,1,-1);
    $this->value = $this->createToken('Var'  , array($t, $this->flexyArgs));
    return FLY_FLEXY_TOKEN_OK;
}
case 100:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 101:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return FLY_FLEXY_TOKEN_OK;
}
case 102:
{
    // &abc;
    $this->value = $this->createToken('Text');
    return FLY_FLEXY_TOKEN_OK;
}
case 103:
{
    //<name -- start tag */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    $this->tagName = trim(substr($this->yytext(),1));
    $this->tokenName = 'Tag';
    $this->value = '';
    $this->attributes = array();
    $this->yybegin(IN_ATTR);
    return FLY_FLEXY_TOKEN_NONE;
}
case 104:
{ 
    /* <? php start.. */
    //echo "STARTING PHP?\n";
    $this->yyPhpBegin = $this->yy_buffer_start;
    $this->yybegin(IN_PHP);
    return FLY_FLEXY_TOKEN_NONE;
}
case 105:
{
    // &#123;
    $this->value = $this->createToken('Text');
    return FLY_FLEXY_TOKEN_OK;
}
case 106:
{
    // &#abc;
    $this->value = $this->createToken('Text');
    return FLY_FLEXY_TOKEN_OK;
}
case 107:
{
    /* </title> -- end tag */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    if ($this->inStyle) {
        $this->inStyle = false;
    }
    $this->tagName = trim(substr($this->yytext(),1));
    $this->tokenName = 'EndTag';
    $this->yybegin(IN_ENDTAG);
    $this->value = '';
    return FLY_FLEXY_TOKEN_NONE;
}
case 108:
{
    /* <!DOCTYPE -- markup declaration */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    $this->value = $this->createToken('Doctype');
    $this->yybegin(IN_MD);
    return FLY_FLEXY_TOKEN_OK;
}
case 109:
{
    /* <![ -- marked section */
    return $this->returnSimple();
}
case 110:
{ 
    /* eg. <?xml-stylesheet, <?php ... */
    $t = $this->yytext();
    $tagname = trim(strtoupper(substr($t,2)));
   // echo "STARTING XML? $t:$tagname\n";
    if ($tagname == 'PHP') {
        $this->yyPhpBegin = $this->yy_buffer_start;
        $this->yybegin(IN_PHP);
        return FLY_FLEXY_TOKEN_NONE;
    }
    // not php - it's xlm or something...
    // we treat this like a tag???
    // we are going to have to escape it eventually...!!!
    $this->tagName = trim(substr($t,1));
    $this->tokenName = 'Tag';
    $this->value = '';
    $this->attributes = array();
    $this->yybegin(IN_ATTR);
    return FLY_FLEXY_TOKEN_NONE;
}
case 111:
{
    $this->attrVal[] = $this->yytext();
    return FLY_FLEXY_TOKEN_NONE;
}
case 112:
{
    $this->value = '';
    return FLY_FLEXY_TOKEN_NONE;
}
case 113:
{
    // <foo^<bar> -- unclosed start tag */
    return $this->raiseError("Unclosed tags not supported"); 
}
case 114:
{
    // <img src="xxx" ...ismap...> the ismap */
    $this->attributes[trim($this->yytext())] = true;
    $this->value = '';
    return FLY_FLEXY_TOKEN_NONE;
}
case 115:
{
    // <a href = ^http://foo/> -- unquoted literal HACK */                          
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    //   $this->raiseError("attribute value needs quotes");
    $this->value = '';
    return FLY_FLEXY_TOKEN_NONE;
}
case 116:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return FLY_FLEXY_TOKEN_NONE;
}
case 117:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return FLY_FLEXY_TOKEN_NONE;
}
case 118:
{ 
    $this->value = $this->createToken('WhiteSpace');
    return FLY_FLEXY_TOKEN_OK; 
}
case 119:
{
    return $this->raiseError("illegal character in markup declaration (0x".dechex(ord($this->yytext())).')');
}
case 120:
{   
    $this->value = $this->createToken('Number');
    return FLY_FLEXY_TOKEN_OK; 
}
case 121:
{ 
    $this->value = $this->createToken('Name');
    return FLY_FLEXY_TOKEN_OK; 
}
case 122:
{ 
    $this->value = $this->createToken('NameT');
    return FLY_FLEXY_TOKEN_OK; 
}
case 123:
{ 
    $this->value = $this->createToken('NumberT');
    return FLY_FLEXY_TOKEN_OK; 
}
case 124:
{
    // <!doctype ^%foo;> -- parameter entity reference */
    $this->value = $this->createToken('EntityRef');
    return FLY_FLEXY_TOKEN_OK;
}
case 125:
{ 
    $this->value = $this->createToken('Literal');
    return FLY_FLEXY_TOKEN_OK; 
}
case 126:
{
	// inside comment -- without a >
	return FLY_FLEXY_TOKEN_NONE;
}
case 127:
{
    $t = $this->yytext();
    if ($t{strlen($t)-1} == ",") {
        // add argument
        $this->flexyArgs[] = substr($t,0,-1);
        return FLY_FLEXY_TOKEN_NONE;
    }
    $this->flexyArgs[] = $t;
    return FLY_FLEXY_TOKEN_NONE;
}
case 128:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return FLY_FLEXY_TOKEN_NONE;
}
case 129:
{
    // general text in script..
    $this->value = $this->createToken('Text');
    return FLY_FLEXY_TOKEN_OK;
}
case 130:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return FLY_FLEXY_TOKEN_OK;
}
case 131:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('DSComment');
    return FLY_FLEXY_TOKEN_OK;
}
case 132:
{     
    /* anything inside of php tags */
    return FLY_FLEXY_TOKEN_NONE;
}
case 133:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return FLY_FLEXY_TOKEN_OK;
}
case 134:
{
    // we allow anything inside of comstyle!!!
    $this->value = $this->createToken('Comment');
	return FLY_FLEXY_TOKEN_OK;
}
case 135:
{
	// inside style comment -- without a >
    $this->value = $this->createToken('Comment');
	return FLY_FLEXY_TOKEN_OK;
}
case 137:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 138:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return FLY_FLEXY_TOKEN_OK;
}
case 139:
{
    $this->attrVal[] = $this->yytext();
    return FLY_FLEXY_TOKEN_NONE;
}
case 140:
{
    $this->value = '';
    return FLY_FLEXY_TOKEN_NONE;
}
case 141:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return FLY_FLEXY_TOKEN_NONE;
}
case 142:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return FLY_FLEXY_TOKEN_NONE;
}
case 143:
{
    return $this->raiseError("illegal character in markup declaration (0x".dechex(ord($this->yytext())).')');
}
case 144:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return FLY_FLEXY_TOKEN_OK;
}
case 145:
{     
    /* anything inside of php tags */
    return FLY_FLEXY_TOKEN_NONE;
}
case 146:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return FLY_FLEXY_TOKEN_OK;
}
case 147:
{
    // we allow anything inside of comstyle!!!
    $this->value = $this->createToken('Comment');
	return FLY_FLEXY_TOKEN_OK;
}
case 149:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 150:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return FLY_FLEXY_TOKEN_OK;
}
case 151:
{
    $this->attrVal[] = $this->yytext();
    return FLY_FLEXY_TOKEN_NONE;
}
case 152:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return FLY_FLEXY_TOKEN_NONE;
}
case 153:
{
    return $this->raiseError("illegal character in markup declaration (0x".dechex(ord($this->yytext())).')');
}
case 154:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return FLY_FLEXY_TOKEN_OK;
}
case 156:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 157:
{
    return $this->raiseError("illegal character in markup declaration (0x".dechex(ord($this->yytext())).')');
}
case 159:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 161:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 163:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 165:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 167:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 169:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 171:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 173:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 175:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 430:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return FLY_FLEXY_TOKEN_OK;
}
case 431:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return FLY_FLEXY_TOKEN_NONE;
}
case 432:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return FLY_FLEXY_TOKEN_NONE;
}
case 433:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return FLY_FLEXY_TOKEN_NONE;
}

                        }
                    }
                    if ($this->_fatal) {
                        return;
                    }
                    $yy_initial = true;
                    $yy_state = $this->yy_state_dtrans[$this->yy_lexical_state];
                    $yy_next_state = YY_NO_STATE;
                    $yy_last_accept_state = YY_NO_STATE;
                    $this->yy_mark_start();
                    $yy_this_accept = $this->yy_acpt[$yy_state];
                    if (YY_NOT_ACCEPT != $yy_this_accept) {
                        $yy_last_accept_state = $yy_state;
                        $this->yy_buffer_end = $this->yy_buffer_index;
                    }
                }
            }
        }
        return FLY_FLEXY_TOKEN_NONE;
    }
}
