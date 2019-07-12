<?php


namespace Porto\Core\Dto;

class DataDto extends Dto
{
    protected $schema = [
        'type'       => 'object',
        'properties' => [
            'additionalProperties' => true,
        ],
        'required'   => [],
        'default'    => [],
    ];
}