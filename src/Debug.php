<?php

/*
  @file
 */

namespace ControlAltKaboom\Debug;

use ControlAltKaboom\Debug\DebugException;

/**
 * A simple php-debugging class.
 * When used with the 'instance()' method, it allows for it be called as within a static context. 
 *
 * @author Brian Snopek <brian.snopek@gmail.com>
 */

class Debug {
  
  /**
   * Enable/Disable debug-output
   * @var boolean
   */
  protected $debugMode;

  /**
   * A mixed callback/callable reference - used to conditionally control debugging output
   * @var Callable
   */
  protected $condition;

  /**
   *  Multi-Dimensional array storing style-references for the debug-output
   * @var array
   */
  protected $style;
  
  /**
   * Multi-Dimensional array storing any recored debug-entries
   * @var array
   */
  protected $stack;
  
  /**
   * Constructor - Initializes debug settings
   */
  public function __construct() 
  {
    $this->setDebugMode(TRUE);
    $this->condition = NULL;
    $this->setDebugStyle("background-color", "white")
         ->setDebugStyle("text-align", "left");
  }
  
  /**
   * Singleton method - Used to call within a static context while maintaining testablility
   * @staticvar \ControlAltKaboom\Debug\className $instance
   * @return \ControlAltKaboom\Debug\className
   */
  public static function instance() 
  {
    static $instance;
    $className = __CLASS__;
    if( !($instance instanceof $className) ):
      $instance = new $className;
    endif;   
    return $instance;
  }

  /**
   * Sets the debugMode setting
   * @param boolean $set
   * @return \ControlAltKaboom\Debug\Debug
   */
  public function setDebugMode( $set )
  {
    if( is_bool($set) ):
      $this->debugMode = $set;  
      return $this;
    endif;
    throw new DebugException("Attempt to set the debug-mode failed: Debug mode must be set to a boolean.");
  }
 
  /**
    * Gets the debugMode setting
    * @return boolean - the current debugMode value
  */
  public function getDebugMode()
  {
    return $this->debugMode;
  }
  
  /**
   * Set a callable-condition - When set, debug output will be restricted to 
   * if the supplied condition evaluates to TRUE
   * @param Callable $condition
   * @return \ControlAltKaboom\Debug\Debug
   * @throws DebugException
   */
  public function setCondition($condition)
  {
    if( is_callable($condition) || is_null($condition) ):
      $this->condition = $condition;
      return $this;
    endif;
    throw new DebugException("Attempt to set a debug-condition failed: Supplied condition is not a a valid callback.");
  }

  /**
   * Evaluates the defined condition, otherwise just returns TRUE;
   * @return boolean
   */
  public function getCondition()
  {
    return ( !is_null($this->condition) && is_callable($this->condition) )
      ? call_user_func($this->condition)
      : TRUE;
  }

  /**
   * Checks that both the debugMode and condition are set/TRUE
   * @return boolean
   */
  public function debugEnabled()
  {
    return ($this->getDebugMode() == TRUE && $this->getCondition() == TRUE) 
      ? TRUE
      : FALSE;
  }
  
  /**
   * When debugging is enabled, it dumps the data passed to it - Otherwise it exists quietly.
   * @param mixed $d 
   * @param string $strMode - when TRUE, returns as a string - otherwise direct output.
   * @return string - print_r output of the input data
  */
  public function debug( $d, $strMode=false ) 
  {
    if( $this->debugEnabled() == TRUE ):
      if($strMode == TRUE):
        return $this->dump($d, TRUE);
      else:
        $this->dump($d);
      endif;
    endif;
  } 
  
  /**
    * Performs a dump and terminates
    * @param mixed $d 
    * @return string - print_r output of the input data - terminates execution
  */
  public function _die( $d )
  { 
    if( $this->debugEnabled() ):
      $this->dump($d);die();
    endif;
  } 
  
  /**
    * Performs a dump of the input data
    * @param mixed $var - the data to be dumped
    * @param mixed $strMode - if enabled, it wraps the output in a styled container.
    * @return string - print_r output of the input data in the strMode provided
  */
  public function dump($var, $strMode=false)
  { 
    if($this->debugEnabled() !== TRUE)  return;
    
    $style = $this->getDebugStyle();  
    $str = "<div style='{$style}'><pre>" .print_r($var, TRUE) . "</pre></div>";
    if($strMode == TRUE):
      return $str;
    endif;
    print $str;
  }
  
  /**
    * Sets or Unsets a debug style setting
    * @param string $key - the valid css key to be set
    * @param string $style - the value being set. if null, it will clear the key.
    * @return object- $this - allows for chaining
  */
  public function setDebugStyle( $key, $style=NULL )
  {
    // if the style is empty/null
    if ( empty($style) ): 
      unset($this->style[$key]);      // clear the setting
    else: 
      $this->style[$key] = rtrim($style, ";");  // otherwise set/overwrite it
    endif;
    // return self for chaining
    return $this;
  }

  /**
    * Gets the/a debug style setting
    * @param string $key - if passed, select only this key, otherwise return everything.
    * @param string $mode - determins the output type, either value, or raw ( array )
    * @return mixed $ret - when raw, it returns the array, otherwise its the inline-css string.
  */
  public function getDebugStyle( $key=FALSE, $mode=FALSE)
  {
    $ret = NULL;
    switch($mode):
      case "value": 
        $ret = ($key !== FALSE && array_key_exists($key, $this->style))
          ? rtrim($this->style[$key], ";") . ";" 
          : "";     
        break;
      case "raw":
        $ret = ($key !== FALSE && array_key_exists($key, $this->style))
          ? $this->style[$key]
          : $this->style;
        break;     
      case "string":
      default:
        if ( $key != FALSE && array_key_exists($key, $this->style)):
          $ret = "{$key}:" . rtrim($this->style[$key], ";") . ";";
        else:
          foreach( $this->style AS $key => $v):
            $ret .= "{$key}:" . rtrim($this->style[$key], ";") . ";";
          endforeach;          
        endif;
        break;
    endswitch;
    return $ret;
  }
  
  /**
   * Appends the debug-data to the log-stack for future access
   * @param string $name - a name for the log-entry 
   * @param mixed  $a - the data to be logged
   */
  public function log( $name, $a ) {
    $time = microtime();
    $trace = debug_backtrace();
    $this->stack[] = 
        [ 'name'  => $name,
          'time'  => microtime(),
          'trace' => debug_backtrace(),
          'data'  => print_r($a,TRUE)
        ];
  }

  /**
   * Renders the logged-entries in a readable format
   * @return string - the debug-log
  */
  public function showLog()
  {
    if($this->debugEnabled() !== FALSE) return;
    
    $ret =<<<END
        <table>
        <tr><th>Item</th>
            <th>TimeStamp</th>
            <th>Data</th>
            <th>Backtrace</th>
        </tr>
END;

    foreach($this->stack AS $i => $d):
      $ret .=<<<END
        <tr>
        <td style="vertical-align:top;text-align:left;">{$d['name']}</td>
        <td style="vertical-align:top;text-align:left;">{$d['time']}</td>
        <td style="vertical-align:top;text-align:left;">{$d['data']}</td>
        <td style="vertical-align:top;text-align:left;">
        {$d['trace']}</td>
        </tr>
END;
        
    endforeach;
    return "<pre>" . print_r($this->stack,TRUE) . "</pre>";
  }
  
  
}