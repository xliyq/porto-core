<?php


namespace Porto\Core\Tests\PhpUnit;

use Porto\Core\Tests\PhpUnit\Traits\TestCaseTrait;
use Porto\Core\Tests\PhpUnit\Traits\TestsAuthHelperTrait;
use Porto\Core\Tests\PhpUnit\Traits\TestsMockHelperTrait;
use Porto\Core\Tests\PhpUnit\Traits\TestsRequestHelperTrait;
use Porto\Core\Tests\PhpUnit\Traits\TestsResponseHelperTrait;
use Porto\Core\Tests\PhpUnit\Traits\TestsUploadHelperTrait;
use Faker\Generator;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as LaravelTestCase;

abstract class CoreTestCase extends LaravelTestCase
{
    use TestCaseTrait,
        TestsAuthHelperTrait,
        TestsMockHelperTrait,
        TestsRequestHelperTrait,
        TestsResponseHelperTrait,
        TestsUploadHelperTrait,
        RefreshDatabase;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var Generator
     */
    protected $faker;

    /**
     * 启动测试之前，初始化环境
     */
    protected function setUp(): void {
        parent::setUp();
    }

    /**
     * 结束测试之后，重置环境
     */
    public function tearDown(): void {
        parent::tearDown();
    }

    /**
     * 刷新内存中的数据库
     */
    protected function refreshInMemoryDatabase() {
        // 迁移脚本
        $this->migrateDatabase();

        // 初始化脚本
        $this->seed();

        // 安装passport
        $this->setupPassportOAuth2();

        $this->app[Kernel::class]->setArtisan(null);
    }

    /**
     * 刷新测试数据库
     */
    protected function refreshTestDatabase() {
        if (!RefreshDatabaseState::$migrated) {
            $this->artisan('migrate:fresh');
            $this->seed();
            $this->setupPassportOAuth2();

            $this->app[Kernel::class]->setArtisan(null);
            RefreshDatabaseState::$migrated = true;
        }
        $this->beginDatabaseTransaction();
    }

}