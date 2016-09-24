# ControlAltKaboom/Debug

This package provides a simple means of generating debug-output from within your application when external tools (such as xDebug) are not available.

## Includes
* Enable/Disable flags
* Conditional Output restrictions ( by passing in a closure/callable )
* Optional stylization/formatting
* In-Memory logging for delayed rendering
* Called within a Singleton/Static-Context

> This package is part of the ControlAltKaboom/BoomStick Framework:

***

## Installation

**Toggle Debug Mode**
```php
Debug::instance()->setDebugMode(TRUE); // Enables debug-mode
Debug::instance()->setDebugMode(FALSE); // Disables debug-mode
```

**Set a conditional output function**
```php
// Using a function as a callback
function UserIsEnabled() {
  // your code
  return TRUE;
}
Debug::instance()->setCondition("UserIsEnabled"); // Evaluates the condition a run-time and outputs based on its return-value

// Using a class/method as a callback
Debug::instance()->setCondition(["\\YourNameSpace\\\YourClass", "methodName"]);

// Using an anonymous function (with optional variable assignment)
Debug::instance()->setCondition( 
  function() use($yourvar) { return TRUE; }
);

```

**Outputting debug-data**
```php
$testVar = [
  "test" => "something",
  "foo"  -> "bar"
  "walrus" => [
    "status" => "awesome",
    "plural" => "walri",
    "asPets" => AskProfessionals->areGoodPets("walrus")
    ]
  ];

Debug::instance()->debug($testVar); // Directly outputs the debug-vars

$debugData = Debug::instance()->debug($testVar, TRUE); // Returns the output as a string
```

**Deferred Output**
```php
$testVar = ["foo" => "bar"];
Debug::instance()->log("testvar", $testVar);

$testVar2 = ["walrus" => "are awesome!"];
Debug::instance()->log("another reference", $testVar2);

// .. later on ..
Debug::instance()->showLog();
```
