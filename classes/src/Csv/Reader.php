<?php

namespace CsvDiff\Csv;

use SplFileObject;

class Reader
{
    const CSV_READER_FLAGS =
        SplFileObject::READ_CSV |
        SplFileObject::SKIP_EMPTY |
        SplFileObject::DROP_NEW_LINE |
        SplFileObject::READ_AHEAD;

    /**
     * @var string $filename
     */
    protected $filename;

    /**
     * @var SplFileObject $file
     */
    protected $file;

    public function __construct($filename)
    {
        $filename = realpath($filename);
        $this->filename = $filename;
        if (!file_exists($filename) || !is_file($filename) || !is_readable($filename)) {
            throw new \Exception("Cannot read file {$filename}");
        }

        $this->file = new SplFileObject($this->filename, 'r');
        $this->file->setFlags(self::CSV_READER_FLAGS);
    }

    public function filename()
    {
        return $this->filename;
    }

    public function load()
    {
        $this->file->rewind();

        foreach ($this->file as $line) {
            yield $line;
        }
    }

    public function __destruct()
    {
        unset($this->file);
    }
}
