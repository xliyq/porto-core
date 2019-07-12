<?php


namespace Porto\Core\Loaders;

use Illuminate\Foundation\AliasLoader;

/**
 * Trait AliasesLoaderTrait
 *
 * @package Porto\Core\Loaders
 *
 * author liyq <2847895875@qq.com>
 */
trait AliasesLoaderTrait
{

    public function loadAliases() {
        $aliases = isset($this->aliases) ? $this->aliases : [];
        foreach ($aliases as $aliasKey => $aliasValue) {
            if (class_exists($aliasValue)) {
                $this->loadAlias($aliasKey, $aliasValue);
            }
        }
    }

    public function loadAlias($aliasKey, $aliasVale) {
        AliasLoader::getInstance()->alias($aliasKey, $aliasVale);
    }


}