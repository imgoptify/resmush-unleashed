<?php

/*
*    ENABLE RECURRENT ZIP COMPRESSION
*/
    
class ZIP extends ZipArchive
{
    private $excludeDir = array();
    
    /* Class Constructor */
    public function __construct($name, $comment = '')
    {
        if ($this->open($name, ZIPARCHIVE::OVERWRITE) !== TRUE) {
            throw new Exception('failed to create zip');
        }

        if ($comment) {
            $this->setArchiveComment($comment);
        }
    }

    /* Destruct Class*/
    public function __destruct()
    {
        $this->close();
    }
    
    /* Exclude a directory */
    public function excludeDir($directory)
    {
        $this->excludeDir[] = $directory;
    }
    
    
    /*Add files to the archive*/
    public function addFiles(Array $files)
    {
        foreach ($files as $k => $f) {
            if (!$this->addFile($f)) {
                throw new Exception('failed to add file');
            }

            if (is_string($k)) {
                $this->setCommentName($f, $k);
            }
        }
    }

    /* Add a folder recursively to the archive */
    public function addRecursive($path)
    {
        
        if (!file_exists($path)) {
            throw new Exception('path does not exists : ' . $path);
        }

        if (!is_dir($path)) {
            throw new Exception('unknown folder' . $path);
        }

        $iter = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iter as $fname => $finfo) {
            
            if ($finfo->isDir()) {
                $exclude = false;
                
                /* We check if this folder was excluded of Archive */
                foreach($this->excludeDir as $_dir):
                    if (strpos($fname,$_dir))
                        $exclude = true;
                endforeach;
                
                    /* If this folder was excluded,*/
                if($exclude) break;
                
                $this->addEmptyDir($fname);                  
            } else {
                if (!$this->addFile($fname)) {
                    throw new Exception( " Failed to add file : " . $fname);
                }
            }
        }
    }
}

?>
