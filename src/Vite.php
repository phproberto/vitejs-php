<?php

namespace Phproberto\Vite;

use Phproberto\Vite\ViteEntryConfiguration;

final class Vite {
    /**
     * @var ViteEntryConfiguration
     */
    protected $config;

    public function __construct(ViteEntryConfiguration $config = null) {
        $this->config = $config ?: new ViteEntryConfiguration();
    }

    public function getEntriesFromText(string $text): array
    {
        $entries = [];
        $regex = '/@vite\((.+?)\)/s';
        preg_match_all($regex, $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $entries[] = ViteEntry::fromRegexMatch($match, $this->config->getRawData());
        }

        return $entries;
    }

    public function replaceTagsInText(string $text): string
    {
        $entries = $this->getEntriesFromText($text);

        foreach ($entries as $entry) {
            $text = str_replace($entry->getTag(), $entry->getOutput(), $text);
        }

        return $text;
    }
}