<?php


namespace Usf\Components;


use Usf\Base\Exceptions\FileManagerException;

class FileManager
{

    protected $directory = DIR_APP;

    protected $reflector;

    public function __construct(string $directory = null)
    {
        if (!is_null($directory)) {
            if (strpos($directory, DIR_ROOT)===false) {
                throw new FileManagerException('Wrong directory name!');
            } else {
                if (!is_dir($directory)) {
                    throw new FileManagerException('Wrong directory name!');
                } else {
                    $this->directory = $directory;
                }
            }
        }
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function dir()
    {
        return scandir($this->directory);
    }

    public function mapDir()
    {
        foreach ($this->dir() as $path) {
            yield $path;
        }
    }

    public function cd(string $directory)
    {
        $directory = $this->directory . DS . $directory;
        if (!is_dir($directory)) {
            throw new FileManagerException('Wrong directory name!');
        } else {
            $this->directory = $directory;
        }
        return $this;
    }

}