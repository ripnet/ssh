<?php

namespace ripnet\ssh\Adapters;

class Adapters {
    private static $adapters = [
        'cisco-ios'  => [
            'prompt'         => '/(?:#|>)$/m',
            'disable_paging' => 'terminal length 0',
            'eol'            => "\n",
        ],
        'alcatel-sr' => [
            'prompt'          => '/\*?[AB]:.*?[#\$] /m',
            'disable_paging'  => 'environment no more',
            'possible_banner' => ['Press any key to continue', 'Q'],
            'eol'             => "\n",
        ],
        'adva-825'   => [
            'prompt'          => '/^.*?:.*?--> /m',
            'possible_banner' => ['Do you wish to continue', "Y\r\n"],
            'eol'             => "\r\n",
        ],
        'adva-new'   => [
            'prompt'          => '/(?:^.*?--> |^--More--)/m',
            'possible_banner' => ['Do you wish to continue', "Y\r\n"],
            'eol'             => "\r\n",
            'bad'             => '/assword: /',
            'paging'          => ['/^--More--/', ' '],
        ],
    ];

    public static function getAdapter(string $adapter) {
        if (array_key_exists($adapter, self::$adapters)) {
            return self::$adapters[$adapter];
        } else {
            return false;
        }
    }
}