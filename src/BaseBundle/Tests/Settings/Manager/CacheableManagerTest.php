<?php

namespace Perform\BaseBundle\Tests\Settings;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Settings\Manager\CacheableManager;
use Perform\BaseBundle\Settings\Manager\SettingsManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Perform\BaseBundle\Settings\Manager\WriteableSettingsManagerInterface;
use Perform\BaseBundle\Exception\ReadOnlySettingsException;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CacheableManagerTest extends TestCase
{
    private $cache;
    private $innerManager;
    private $manager;
    private $user;

    public function setUp()
    {
        $this->cache = $this->createMock(CacheItemPoolInterface::class);
        $this->innerManager = $this->createMock([SettingsManagerInterface::class, WriteableSettingsManagerInterface::class]);
        $this->manager = new CacheableManager($this->innerManager, $this->cache);
        $this->user = $this->createMock(UserInterface::class);
        $this->user->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('testuser@example.com'));
    }

    public function testImplementsInterfaces()
    {
        $this->assertInstanceOf(SettingsManagerInterface::class, $this->manager);
    }

    public function testGetInnerManager()
    {
        $this->assertSame($this->innerManager, $this->manager->getInnerManager());
    }

    private function expectItem($key, $isHit, $value = null)
    {
        $item = $this->createMock(CacheItemInterface::class);
        $item->expects($this->any())
            ->method('isHit')
            ->will($this->returnValue($isHit));
        $item->expects($this->any())
            ->method('get')
            ->will($this->returnValue($value));

        $this->cache->expects($this->any())
            ->method('getItem')
            ->with($key)
            ->will($this->returnValue($item));

        return $item;
    }

    public function testGetValueCacheMiss()
    {
        $item = $this->expectItem('some_setting', false);
        $this->innerManager->expects($this->once())
            ->method('getRequiredValue')
            ->with('some_setting')
            ->will($this->returnValue('some_value'));
        $item->expects($this->once())
            ->method('set')
            ->with('some_value');
        $this->cache->expects($this->once())
            ->method('save')
            ->with($item);

        $this->assertSame('some_value', $this->manager->getValue('some_setting'));
    }

    public function testGetValueCacheMissWithExpiryTime()
    {
        $this->manager = new CacheableManager($this->innerManager, $this->cache, 30);

        $item = $this->expectItem('some_setting', false);
        $this->innerManager->expects($this->once())
            ->method('getRequiredValue')
            ->with('some_setting')
            ->will($this->returnValue('some_value'));
        $item->expects($this->once())
            ->method('set')
            ->with('some_value');
        $item->expects($this->once())
            ->method('expiresAfter')
            ->with(30);
        $this->cache->expects($this->once())
            ->method('save')
            ->with($item);

        $this->assertSame('some_value', $this->manager->getValue('some_setting'));
    }

    public function testGetValueCacheHit()
    {
        $this->expectItem('some_setting', true, 'cached_value');
        $this->innerManager->expects($this->never())
            ->method('getRequiredValue');

        $this->assertSame('cached_value', $this->manager->getValue('some_setting', 'some_default'));
    }

    public function testGetValueWithDodgyKey()
    {
        $this->expectItem(urlencode(':/?{}weird key""'), true, 'foo');
        $this->manager->getValue(':/?{}weird key""');
    }

    public function testSetValue()
    {
        $this->innerManager->expects($this->once())
            ->method('setValue')
            ->with('some_setting', 'new_value');
        $this->cache->expects($this->once())
            ->method('deleteItem')
            ->with('some_setting');

        $this->manager->setValue('some_setting', 'new_value');
    }

    public function testGetUserValueCacheMiss()
    {
        $item = $this->expectItem('some_setting_'.urlencode('testuser@example.com'), false);
        $this->innerManager->expects($this->once())
            ->method('getRequiredUserValue')
            ->with($this->user, 'some_setting')
            ->will($this->returnValue('some_value'));
        $item->expects($this->once())
            ->method('set')
            ->with('some_value');
        $this->cache->expects($this->once())
            ->method('save')
            ->with($item);

        $this->assertSame('some_value', $this->manager->getUserValue($this->user, 'some_setting'));
    }

    public function testGetUserValueCacheMissWithExpiryTime()
    {
        $this->manager = new CacheableManager($this->innerManager, $this->cache, 30);

        $item = $this->expectItem('some_setting_'.urlencode('testuser@example.com'), false);
        $this->innerManager->expects($this->once())
            ->method('getRequiredUserValue')
            ->with($this->user, 'some_setting')
            ->will($this->returnValue('some_value'));
        $item->expects($this->once())
            ->method('set')
            ->with('some_value');
        $item->expects($this->once())
            ->method('expiresAfter')
            ->with(30);
        $this->cache->expects($this->once())
            ->method('save')
            ->with($item);

        $this->assertSame('some_value', $this->manager->getUserValue($this->user, 'some_setting'));
    }

    public function testGetUserValueCacheHit()
    {
        $this->expectItem('some_setting_'.urlencode('testuser@example.com'), true, 'cached_value');
        $this->innerManager->expects($this->never())
            ->method('getRequiredUserValue');

        $this->assertSame('cached_value', $this->manager->getUserValue($this->user, 'some_setting', 'some_default'));
    }

    public function testSetUserValue()
    {
        $this->innerManager->expects($this->once())
            ->method('setUserValue')
            ->with($this->user, 'some_setting', 'new_value');
        $this->cache->expects($this->once())
            ->method('deleteItem')
            ->with('some_setting_'.urlencode('testuser@example.com'));

        $this->manager->setUserValue($this->user, 'some_setting', 'new_value');
    }

    public function testSetValueThrowsExceptionForNonWriteable()
    {
        $manager = new CacheableManager($this->createMock(SettingsManagerInterface::class), $this->cache);
        $this->expectException(ReadOnlySettingsException::class);
        $manager->setValue('some_setting', 'new_value');
    }

    public function testSetUserValueThrowsForNonWriteable()
    {
        $manager = new CacheableManager($this->createMock(SettingsManagerInterface::class), $this->cache);
        $this->expectException(ReadOnlySettingsException::class);
        $manager->setUserValue($this->user, 'some_setting', 'new_value');
    }
}
