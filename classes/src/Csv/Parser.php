<?php

namespace CsvDiff\Csv;

class Parser
{
    /**
     * @var Reader $reader
     */
    protected $reader;

    /**
     * @var array
     */
    protected $keys;

    /**
     * @var array
     */
    protected $ignore;

    protected $lines = [];

    public function __construct(Reader $reader, array $keys, array $ignore = [])
    {
        $this->reader = $reader;
        $this->keys = array_flip($keys);
        $this->ignore = array_flip($ignore);
    }

    public function hasKey($key)
    {
        return array_key_exists($key, $this->lines);
    }

    public function getHash($key)
    {
        return $this->hasKey($key) ? $this->lines[$key] : null;
    }

    public function missingIn(Parser $parser)
    {
        $list = [];
        foreach ($this->lines as $key => $hash) {
            if (!$parser->hasKey($key)) {
                $list[] = $key;
            }
        }

        return $list;
    }

    public function differences(Parser $parser)
    {
        $list = [];
        foreach ($this->lines as $key => $hash) {
            $diffCheck = $parser->getHash($key);
            if ($diffCheck !== null && $diffCheck !== $hash) {
                $list[] = $key;
            }
        }

        return $list;
    }

    public function parseData()
    {
        foreach ($this->reader->load() as $line) {
            $pk = $this->hashKey($line);
            if (array_key_exists($pk, $this->lines)) {
                throw new \Exception(
                    sprintf('Duplicate key value %s in %s', $this->getKey($line), $this->reader->filename())
                );
            }
            $this->lines[$pk] = $this->hashData($line);
        }
    }

    public function showDifferences($discrepancies)
    {
        $lineNumber = 1;
        foreach ($this->reader->load() as $line) {
            $pk = $this->hashKey($line);
            if (in_array($pk, $discrepancies)) {
                echo 'Line ', $lineNumber, ': ', $this->getKey($line), ' => ', implode(',', $line), PHP_EOL;
            }
            ++$lineNumber;
        }
    }

    public function hashKey($csv)
    {
        return hash('md5', $this->getKey($csv), true);
    }

    public function getKey($csv)
    {
        return implode(',', array_intersect_key($csv, $this->keys));
    }

    public function hashData($csv)
    {
        $csv = array_diff_key($csv, $this->ignore);
        $csv = array_diff_key($csv, $this->keys);

        return hash('md5', implode($csv), true);
    }
}
