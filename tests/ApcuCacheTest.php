<?php

use voku\cache\AdapterApcu;
use voku\cache\Cache;
use voku\cache\iAdapter;
use voku\cache\iSerializer;
use voku\cache\SerializerDefault;

/**
 * ApcuCacheTest
 */
class ApcuCacheTest extends \PHPUnit\Framework\TestCase
{

  /**
   * @var iSerializer
   */
  public $serializer;

  /**
   * @var iAdapter
   */
  public $adapter;

  /**
   * @var Cache
   */
  public $cache;

  protected $backupGlobalsBlacklist = [
      '_SESSION',
  ];

  public function testSetItem()
  {
    $return = $this->cache->setItem('foo', [1, 2, 3, 4]);

    self::assertSame(true, $return);
  }

  public function testGetItem()
  {
    for ($i = 0; $i <= 1000; $i++) {
      $return = $this->cache->existsItem('foo');
      self::assertSame(true, $return);
      $return = $this->cache->getItem('foo', 2);
      self::assertSame([1, 2, 3, 4], $return);
    }
  }

  public function testExistsItem()
  {
    $return = $this->cache->existsItem('foo');

    self::assertSame(true, $return);
  }

  public function testGetCacheIsReady()
  {
    $return = $this->cache->getCacheIsReady();

    self::assertSame(true, $return);
  }

  public function testSetGetItemWithPrefix()
  {
    $this->cache->setPrefix('bar');
    $prefix = $this->cache->getPrefix();
    self::assertSame('bar', $prefix);

    $return = $this->cache->setItem('foo', [3, 2, 1]);
    self::assertSame(true, $return);

    $return = $this->cache->getItem('foo');
    self::assertSame([3, 2, 1], $return);
  }

  public function testSetGetCacheWithEndDateTime()
  {
    $expireDate = new DateTime();
    $interval = DateInterval::createFromDateString('+3 seconds');
    $expireDate->add($interval);

    $return = $this->cache->setItemToDate('testSetGetCacheWithEndDateTime', [3, 2, 1], $expireDate);
    self::assertSame(true, $return);

    $return = $this->cache->getItem('testSetGetCacheWithEndDateTime');
    self::assertSame([3, 2, 1], $return);
  }

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp()
  {
    $this->adapter = new AdapterApcu();
    $this->serializer = new SerializerDefault();

    if ($this->adapter->installed() === false) {
      self::markTestSkipped(
          'The APCu extension is not available.'
      );
    }

    $this->cache = new Cache($this->adapter, $this->serializer, false, true);

    // reset default prefix
    $this->cache->setPrefix('');

  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown()
  {

  }

}
