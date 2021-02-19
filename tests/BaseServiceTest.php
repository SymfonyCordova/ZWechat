<?php


namespace Tests;


use PHPUnit\Framework\TestCase;
use Zler\Wechat\Service\Impl\BaseServiceImpl;

class BaseServiceTest extends TestCase
{
    protected $baseService;

    public function setUp(): void
    {
        $this->baseService = new BaseServiceImpl('wxc3b80a3aaf117f5a', '6b6b51f538fcd42d74b9a28013c4efa7');
    }

    public function testGetAccessToken()
    {
        $accessToken = $this->baseService->getAccessToken('abcd');
        $this->assertArrayHasKey('', $accessToken);
    }
}