<?php


namespace Porto\Core\Tests\PhpUnit\Traits;


use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\PersonalAccessClient;

/**
 * Trait TestCaseTrait
 *
 * @package Porto\Core\Tests\PhpUnit\Traits
 *
 * author liyq <2847895875@qq.com>
 */
trait TestCaseTrait
{
    /**
     * 执行迁移命令
     */
    public function migrateDatabase() {
        Artisan::call('migrate');
    }

    public function overrideSubDomain($url = null) {
        if (!property_exists($this, 'subDomain')) {
            return;
        }
        $url = ($url) ?: $this->baseUrl;
        $info = parse_url($url);
        $array = explode('.', $info['host']);
        $withoutDomain = (array_key_exists(count($array) - 2, $array) ? $array[count($array) - 2] : '') . "." . $array[count($array) - 1];

        $newSubDomain = $info['scheme'] . "//" . $this->subDomain . '.' . $withoutDomain;

        return $this->baseUrl = $newSubDomain;
    }

    /**
     * 安装passport
     */
    public function setupPassportOAuth2() {
        $client = (new ClientRepository())->createPersonalAccessClient(
            null,
            'Testing Personal Access Client',
            'http://localhost'
        );
        $accessClient = new PersonalAccessClient();
        $accessClient->client_id = $client->id;
        $accessClient->save();
    }

}