<?php


namespace Porto\Core\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait ValidationTrait
{

    public function extendValidationRules() {

        // 验证字符串包含空格
        Validator::extend('no_spaces', function (
            $attributes, $value, $parameters, $validator
        ) {
            return preg_match('/^\S*$/u', $value);
        }, 'String should not contain space.');

        // 检查唯一约束
        // Usage: unique_composite:table,this-attribute-column,the-other-attribute-column
        // Example:    'values'               => 'required|unique_composite:item_variant_values,value,item_variant_name_id',
        //             'item_variant_name_id' => 'required',
        Validator::extend('unique_composite', function (
            $attributes, $value, $parameters, $validator
        ) {
            $queryBuilder = DB::table($parameters[0]);
            $queryBuilder = is_array($value) ? $queryBuilder->whereIn($parameters[1], $value)
                : $queryBuilder->where($parameters[1], $value);
            $queryBuilder->where($parameters[2], $validator->getData()[$parameters[2]]);
            $queryResult = $queryBuilder->get();

            return $queryResult->isEmpty();
        }, 'Duplicated record. This record has composite ID and it must be unique.');
    }
}