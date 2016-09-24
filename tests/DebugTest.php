<?php

/*
  @file
 */

use ControlAltKaboom\Debug\Debug;
use ControlAltKaboom\Debug\DebugException;

/**
 * PHPUnit Tests for the ControlAltKaboom\Debug\Debug class
 *
 * @author Brian Snopek <brian.snopek@gmail.com>
 */
 
class DebugTest extends PHPUnit_Framework_TestCase {
 
  /**
   * Test that the debugMode property can be set using the method setDebugMode()
   */
  public function testSetDebugMode()
  {
    Debug::instance()->setDebugMode(FALSE);
    $this->assertFalse(Debug::instance()->getDebugMode());
    Debug::instance()->setDebugMode(TRUE);
    $this->assertTrue(Debug::instance()->getDebugMode());
  }

  /**
   * Test that the correct exceptin is thrown when passing a non-boolean to setDebugMode()
   * @expectedException ControlAltKaboom\Debug\DebugException
  */
  public function testDebugModeException()
  {
    Debug::instance()->setDebugMode("foobar");
  }

  /**
   * Test the setCondition method
   */
  public function testSetCondition()
  {
    Debug::instance()->setCondition( function() {return true;} );
    $this->assertTrue( Debug::instance()->getCondition() );
  }

  /**
   * Tests that the setCondition method throws the DebugException when passed a non-callable string
   * @expectedException ControlAltKaboom\Debug\DebugException
  */
  public function testSetConditionException()
  {
    Debug::instance()->setCondition( "foobar" );
  }

  /**
   * Tests the getCondition method
   */
  public function testGetCondition()
  {
    Debug::instance()->setCondition( function() {return TRUE;} );
    $this->assertTrue( Debug::instance()->getCondition() );
    Debug::instance()->setCondition( function() {return FALSE;} );
    $this->assertFalse( Debug::instance()->getCondition() );
  }

  /**
   * Tests the debugEnable() method with all combinations of debugMode and conditions
   */
  public function testDebugEnabled()
  {
    Debug::instance()->setDebugMode(TRUE);
    Debug::instance()->setCondition(NULL);
    $this->assertTrue( Debug::instance()->debugEnabled() );
    
    Debug::instance()->setDebugMode(FALSE);
    Debug::instance()->setCondition(NULL);
    $this->assertFalse( Debug::instance()->debugEnabled() );
 
    Debug::instance()->setDebugMode(TRUE);
    Debug::instance()->setCondition( function() { return TRUE;} );
    $this->assertTrue( Debug::instance()->debugEnabled() );
    
    Debug::instance()->setDebugMode(FALSE);
    Debug::instance()->setCondition( function() { return FALSE;} );
    $this->assertFalse( Debug::instance()->debugEnabled() );
  }
  
}