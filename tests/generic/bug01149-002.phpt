--TEST--
Test for PHP-1149: Collection name validation
--SKIPIF--
<?php require_once "tests/utils/standalone.inc" ?>
--FILE--
<?php
require_once "tests/utils/server.inc";

$host = MongoShellServer::getStandaloneInfo();

$m = new MongoClient( $host );

$dbNamesToTry = array(
	'', '"', '/', '\\', ' ', "\0",
	'foo', 'bar',
	'this is a really long collection name which is allowed, because, well, it is very long',
	"name\0with\0",
	"system.foo",
	"foo.system.foo",
);

foreach ( $dbNamesToTry as $name )
{
	echo $name, ': ';
	try {
		$d = $m->selectCollection( dbname(), $name );
		echo "OKAY\n";
	} catch ( MongoException $e ) {
		echo 'EXCEPTION: ', $e->getMessage(), "\n";
	}
}
?>
--EXPECT--
: EXCEPTION: Collection name cannot be empty
": OKAY
/: OKAY
\: OKAY
 : OKAY
 : EXCEPTION: Collection name cannot contain null bytes: \0...
foo: OKAY
bar: OKAY
this is a really long collection name which is allowed, because, well, it is very long: OKAY
name with : EXCEPTION: Collection name cannot contain null bytes: name\0...
system.foo: OKAY
foo.system.foo: OKAY
