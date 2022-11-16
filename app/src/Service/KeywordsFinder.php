<?php

namespace App\Service;

class KeywordsFinder
{
    private string $content = '';
    private string $keywords = '';
    private array $keywordsFound;

    public function setContent(string $content): self
    {
        $this->content = strtolower($content);

        return $this;
    }

    public function setKeywordsString(string $keywords): self
    {
        $this->keywords = strtolower($keywords);

        return $this;
    }

    public function getFoundKeywordsString(): string
    {
        if (count($this->keywordsFound) > 0) {
            return implode(', ', $this->keywordsFound);
        }

        return '';
    }

    public function findKeywordsOnContent(): self
    {
        $this->keywordsFound = [];

        if ($this->keywords) {
            $keywordsArray = explode(',', $this->keywords);
            foreach ($keywordsArray as $keyword) {
                $keywordTrimmed = trim($keyword);
                $found = strpos($this->content, $keywordTrimmed);

                if ($found !== false) {
                    $this->keywordsFound[] = $keywordTrimmed;
                }
            }
        }

        return $this;
    }
}