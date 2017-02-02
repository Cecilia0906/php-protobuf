# PHP Protobuf - Google's Protocol Buffers for PHP

[![Packagist](https://img.shields.io/packagist/v/basho/protobuf.svg?maxAge=2592000)](https://packagist.org/packages/basho/protobuf) [![Build Status](https://secure.travis-ci.org/basho/php-protobuf.png?branch=master)](http://travis-ci.org/basho/php-protobuf)

[Protocol Buffers][1] are a way of encoding structured data in an efficient yet extensible format. It might be used in file formats and RPC protocols.

PHP Protobuf is Google's Protocol Buffers implementation for PHP with a goal to provide high performance, including a `protoc` plugin to generate PHP classes from .proto files. The heavy-lifting (a parsing and a serialization) is done by a PHP extension.

1. [Installation](#installation)
1. [Documentation](#documentation)
1. [Contributing](#contributing)
1. [Roadmap](#roadmap)
1. [License and Authors](#license-and-authors)
1. [References](#references)

## Installation

### Dependencies

* PHP 5.4 or above
* Protobuf `protoc` compiler 2.6 or above
* Protobuf message version `proto2`

### Composer Install

### Install From Source

1. Clone the source code
    ```
    git clone https://github.com/allegro/php-protobuf
    ```
1. Go to the source code directory
    ```
    cd php-protobuf
    ```
1. Build and install the PHP extension (follow instructions at [php.net][2])
1. Install protoc plugin dependencies
    ```
    composer install
    ```

## Documentation

1. Assume you have a file `foo.proto`
    ```
    message Foo
    {
        required int32 bar = 1;
        optional string baz = 2;
        repeated float spam = 3;
    }
    ```

1. Compile `foo.proto`
    ```
    php protoc-gen-php.php foo.proto
    ```

1. Create `Foo` message and populate it with some data
    ```php
    require_once 'Foo.php';

    $foo = new Foo();
    $foo->setBar(1);
    $foo->setBaz('two');
    $foo->appendSpam(3.0);
    $foo->appendSpam(4.0);
    ```

1. Serialize a message to a string
    ```php
    $packed = $foo->serializeToString();
    ```

1. Parse a message from a string
    ```php
    $parsedFoo = new Foo();
    try {
        $parsedFoo->parseFromString($packed);
    } catch (Exception $ex) {
        die('Oops.. there is a bug in this example, ' . $ex->getMessage());
    }
    ```

1. Let's see what we parsed out
    ```php
    $parsedFoo->dump();
    ```

    It should produce output similar to the following:
    ```
    Foo {
      1: bar => 1
      2: baz => 'two'
      3: spam(2) =>
        [0] => 3
        [1] => 4
    }
    ```

1. If you would like you can reset an object to its initial state
    ```php
    $parsedFoo->reset();
    ```

### Compilation

Use *protoc-php.php* script to compile your *proto* files. It requires extension to be installed.

    php protoc-php.php foo.proto

Specify *--use-namespaces* or *-n* option to generate classes using native PHP namespaces.

    php protoc-php.php -n foo.proto

### Package

If a proto file is compiled with a -n / --use-namespaces option a package is represented as an namespace. Otherwise message and enum name is prefixed with it separated by underscore. The package name is composed of a respective first-upper-case parts separated by underscore.

### Message and enum name

* underscore separated name is converted to CamelCased
* embedded name is composed of parent message name separated by underscore

### Message interface

PHP Protobuf module implements *ProtobufMessage* class which encapsulates protocol logic. Message compiled from *proto* file extends this class providing message field descriptors. Based on these descriptors *ProtobufMessage* knows how to parse and serialize messages of the given type.

For each field a set of accessors is generated. Methods actually accessible are different for single value fields (*required* / *optional*) and multi-value fields (*repeated*).

* *required* / *optional*

        get{FIELD}()        // return field value
        set{FIELD}($value)  // set field value to $value

* repeated

        append{FIELD}($value)       // append $value value to field
        clear{FIELD}()              // empty field
        get{FIELD}()                // return array of field values
        getAt{FIELD}($index)        // return field value at $index index
        getCount{FIELD}()           // return number of field values
        getIterator{FIELD}($index)  // return ArrayIterator for field values

{FIELD} is camel cased field name.

### Enums

PHP does not natively support enum type. Hence enum is compiled to a class with set of constants.

Enum field is simple PHP integer type.

### Type mapping

Range of available build-in PHP types poses some limitations. PHP does not support 64-bit positive integer type. Note that parsing big integer values might result in getting unexpected results.

Protocol Buffers types map to PHP types as follows:

    | Protocol Buffers | PHP    |
    | ---------------- | ------ |
    | double           | float  |
    | float            |        |
    | ---------------- | ------ |
    | int32            | int    |
    | int64            |        |
    | uint32           |        |
    | uint64           |        |
    | sint32           |        |
    | sint64           |        |
    | fixed32          |        |
    | fixed64          |        |
    | sfixed32         |        |
    | sfixed64         |        |
    | ---------------- | ------ |
    | bool             | bool   |
    | ---------------- | ------ |
    | string           | string |
    | bytes            |        |

Not set value is represented by *null* type. To unset value just set its value to *null*.

### Parsing

To parse message create message class instance and call its *parseFromString* method passing it prior to the serialized message. Errors encountered are signaled by throwing *Exception*. Exception message provides detailed explanation. Required fields not set are silently ignored.

    $packed = /* serialized FooMessage */;
    $foo = new FooMessage();

    try {
        $foo->parseFromString($packed);
    } catch (Exception $ex) {
        die('Parse error: ' . $e->getMessage());
    }

    $foo->dump(); // see what you got

### Serialization

To serialize message call *serializeToString* method. It returns a string containing protobuf-encoded message. Errors encountered are signaled by throwing *Exception*. Exception message provides detailed explanation. Required field not set triggers an error.

    $foo = new FooMessage()
    $foo->setBar(1);

    try {
        $packed = $foo->serializeToString();
    } catch (Exception $ex) {
        die 'Serialize error: ' . $e->getMessage();
    }

    /* do some cool stuff with protobuf-encoded $packed */

### Dumping

There might be situations you need to investigate what actual content of the given message is. What *var_dump* gives on message instance is somewhat obscure.

*ProtobufMessage* class comes with *dump* method which prints out a message content to the standard output. It takes one optional argument specifying whether you want to dump only set fields. By default it dumps only set fields. Pass *false* as argument to dump all fields. Format it produces is similar to *var_dump*.

### Example

* *foo.proto*

        message Foo
        {
            required int32 bar = 1;
            optional string baz = 2;
            repeated float spam = 3;
        }

* *pb_proto_foo.php*

        php protoc-php.php foo.proto

* *foo.php*

        <?php
            require_once 'pb_proto_foo.php';

            $foo = new Foo();
            $foo->setBar(1);
            $foo->setBaz('two');
            $foo->appendSpam(3.0);
            $foo->appendSpam(4.0);

            $packed = $foo->serializeToString();

            $foo->clear();

            try {
                $foo->parseFromString($packed);
            } catch (Exception $ex) {
                die('Oops.. there is a bug in this example');
            }

            $foo->dump();
        ?>

`php foo.php` should produce following output:

    Foo {
      1: bar => 1
      2: baz => 'two'
      3: spam(2) =>
        [0] => 3
        [1] => 4
    }

## License and Authors

* Author: Hubert Jagodziński (https://github.com/hjagodzinski)
* Author: Mateusz Gajewski (https://github.com/wendigo)
* Author: Sergey P (https://github.com/serggp)
* Author: Christopher Mancini (https://github.com/christophermancini)

Copyright (c) 2017 Allegro Group (Original Authors)
Copyright (c) 2017 Basho Technologies, Inc.

Licensed under the Apache License, Version 2.0 (the "License"). For more details, see [License](License).

## References

[1]: http://code.google.com/p/protobuf/ "Protocol Buffers"
[2]: http://php.net/manual/en/install.pecl.phpize.php "phpize"
