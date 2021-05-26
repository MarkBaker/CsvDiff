<?php

namespace CsvDiff\Csv;

class Differ
{
    protected $filename1;
    protected $filename2;
    protected $keys;
    protected $ignore = [];

    public function __construct($filename1, $filename2, array $keyColumns, array $ignoreColumns = [])
    {
        $this->filename1 = $filename1;
        $this->filename2 = $filename2;
        $this->keys = $keyColumns;
        $this->ignore = $ignoreColumns;
    }

    public function ignoreColumns(array $ignoreColumns = [])
    {
        $this->ignore = $ignoreColumns;
    }

    public function compare()
    {
        $parser1 = $this->getParser($this->filename1);
        $parser2 = $this->getParser($this->filename2);

        $this->listAdditions($parser1, $parser2);
        $this->listDifferences($parser1, $parser2);
        $this->listDeletions($parser1, $parser2);
    }

    protected function listAdditions(Parser $parser1, Parser $parser2)
    {
        $additions = $parser2->missingIn($parser1);
        echo 'ADDITIONS IN FILE ', $this->filename2, PHP_EOL;
        $parser2->showDifferences($additions);
    }

    protected function listDifferences(Parser $parser1, Parser $parser2)
    {
        $differences = $parser1->differences($parser2);

        echo 'DIFFERENCES BETWEEN FILES ', $this->filename1, ' AND ', $this->filename2, PHP_EOL;
        $parser1->showDifferences($differences);
    }

    protected function listDeletions(Parser $parser1, Parser $parser2)
    {
        $deletions = $parser1->missingIn($parser2);

        echo 'DELETIONS FROM FILE ', $this->filename1, PHP_EOL;
        $parser1->showDifferences($deletions);
    }

    protected function getParser($file)
    {
        $parser = new Parser($this->getReader($file), $this->keys, $this->ignore);
        $parser->parseData();

        return $parser;
    }

    protected function getReader($file)
    {
        return new Reader($file);
    }
}
