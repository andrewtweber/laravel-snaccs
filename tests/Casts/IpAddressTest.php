<?php

namespace Snaccs\Tests\Casts;

use Snaccs\Casts\IpAddress;
use Snaccs\Tests\TestCase;
use Snaccs\Tests\TestModel;

/**
 * Class IpAddressTest
 *
 * @package Snaccs\Tests\Casts
 */
class IpAddressTest extends TestCase
{
    /**
     * @test
     */
    public function cast_null()
    {
        $cast = new IpAddress();

        $this->assertNull($cast->get(new TestModel(), "", null, []));
        $this->assertNull($cast->set(new TestModel(), "", null, []));
    }

    /**
     * @test
     */
    public function cast_ipv4_addresses()
    {
        $cast = new IpAddress();

        $ips = [
            '0.0.0.0',
            '127.0.0.1',
            '255.255.255.255',
        ];

        foreach ($ips as $ip) {
            $result = $cast->set(new TestModel(), "", $ip, []);
            $this->assertSame($ip, inet_ntop($result));

            $length = strlen(bin2hex($result)) / 2;
            $this->assertTrue($length <= 16);

            $result = $cast->get(new TestModel(), "", $result, []);
            $this->assertSame($ip, $result);
        }
    }

    /**
     * @test
     */
    public function cast_ipv6_addresses()
    {
        $cast = new IpAddress();

        $ips = [
            '0000:0000:0000:0000:0000:0000:0000:0000',
            '200d:31c4:1905:9eb2:3c7f:c45c:de78:42cd',
            '59b0:c4d6:48b4:3717:f031:d05b:705d:6c65',
            '788e:3f48:e62b:c3bb:da10:6a03:f987:7a16',
            'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff',
        ];

        foreach ($ips as $ip) {
            $ip = inet_ntop(inet_pton($ip));

            $result = $cast->set(new TestModel(), "", $ip, []);
            $this->assertSame($ip, inet_ntop($result));

            $length = strlen(bin2hex($result)) / 2;
            $this->assertSame(16, $length); // IPv6 will always be exactly 16 binary characters

            $result = $cast->get(new TestModel(), "", $result, []);
            $this->assertSame($ip, $result);
        }
    }

    /**
     * @test
     */
    public function cast_condensed_ipv6_addresses()
    {
        $cast = new IpAddress();

        $pairs = [
            [
                '0000:0000:0000:0000:0000:0000:0000:0001',
                '::1',
            ],
            [
                '1050:0000:0000:0000:0005:0600:300c:326b',
                '1050:0:0:0:5:600:300c:326b',
            ],
            [
                'ff06:0:0:0:0:0:0:c3',
                'ff06::c3',
            ],
            [
                '2041:0000:140f:0000:0000:0000:875b:131b',
                '2041:0000:140f::875b:131b',
            ],
            [
                '2041:0000:140f::875b:131b',
                '2041:0:140f::875b:131b',
            ],
        ];

        foreach ($pairs as $pair) {
            $result1 = $cast->set(new TestModel(), "", $pair[0], []);
            $result2 = $cast->set(new TestModel(), "", $pair[1], []);
            $this->assertSame($result1, $result2);

            $result1 = $cast->get(new TestModel(), "", $result1, []);
            $result2 = $cast->get(new TestModel(), "", $result2, []);
            $this->assertSame($result1, $result2);
        }
    }
}
