<?php

include_once XOOPS_ROOT_PATH . '/class/class.tar.php';

/**
 * class TarExtractor.
 *
 * this provides function of extracting tar archive.
 */
class TarExtractor extends Tar
{
    /**
     * public.
     */
    public $file_limit;

    // for debug
    public $show_fname;

    /**
     * private.
     */
    public $archive;
    public $extract_path;
    public $filename;
    public $error;
    public $directory;
    public $dir_path;

    /**
     * Class Constructor.
     */
    public function __construct()
    {
        $this->file_limit   = 288000000;
        $this->show_fname   = 0;
        $this->archive      = '';
        $this->extract_path = '';
        $this->filename     = '';
        $this->error        = '';
        $this->directory    = array();
        $this->dir_path     = '';
    }

    /**
     * setArchive.
     *
     * @param string $archive      (tar archive name)
     * @param string $extract_path (base directory path)
     *
     * @return bool
     */
    public function setArchive($archive, $extract_path)
    {
        $this->archive      = $archive;
        $this->extract_path = $extract_path;

        if (substr($extract_path, -1) === '/') {
            $this->extract_path = substr($extract_path, 0, -1);
        }
        if (!file_exists($this->archive)) {
            $this->error = $this->archive . ' does not exists. (tarextractor.php line ' . __LINE__ . ')<br>';

            return false;
        } elseif (filesize($this->archive) > $this->file_limit) {
            $this->error = $this->archive . ' size is too large. (tarextractor.php line ' . __LINE__ . ')<br>';

            return false;
        }

        return true;
    }

    /**
     * doRegExtract.
     *
     * @param        $label_id
     * @param string $suffix
     * @return bool this extract tar archive into extract directory and classify its
     *
     * this extract tar archive into extract directory and classify its
     * contents (files, directories) into special directories
     */
    public function doRegExtract($label_id, $suffix = '')
    {

        //$dir = explode('.', $dataname);
        //$dir = $dir[0];
        $dir = $label_id;

        $suf = array();
        if (!empty($suffix)) {
            $suf = explode('|', $suffix);
        }

        if ($this->openTAR($this->archive)) {
            foreach ($this->files as $file) {

                //	make directories
                $this->directory = array();
                $dammy           = explode('/', $file['name']);
                $file_path       = '';
                for ($i = 0, $iMax = count($dammy); $i < $iMax; ++$i) {
                    if (!$i) {
                        $file_path .= $dir . '/';
                    } else {
                        $file_path .= $dammy[$i] . '/';
                    }
                }
                $file_path = substr($file_path, 0, -1);
                $dammy     = explode('/', $file_path);

                // extract/dataname/thumbnail/...
                if (isset($dammy[1]) && $dammy[1] === 'thumbnail') {
                    $this->directory = explode('/', $file_path);

                    // extract/dataname/caption/...
                } elseif (isset($dammy[1]) && $dammy[1] === 'caption') {
                    $this->directory = explode('/', $file_path);

                    // insert 'data' directory
                    // extract/dataname/data/...
                } else {
                    $this->directory[] = $dammy[0];
                    $this->directory[] = 'data';
                    for ($i = 1, $iMax = count($dammy); $i < $iMax; ++$i) {
                        $this->directory[] = $dammy[$i];
                    }
                }
                $num = count($this->directory) - 1;

                for ($i = 0; $i < $num; ++$i) {
                    $this->dir_path = $this->extract_path . '/';
                    for ($j = 0; $j < $i; ++$j) {
                        $this->dir_path .= $this->directory[$j] . '/';
                    }
                    $this->dir_path .= $this->directory[$i];

                    if (!is_dir($this->dir_path)) {
                        if (!mkdir($this->dir_path, 0777)) {
                            $this->error = 'mkdir (' . $this->dir_path . ') false. (tarextractor.php line ' . __LINE__ . ')<br>';

                            return false;
                        }
                    }
                }

                // make files;
                $this->filename = $this->dir_path . '/' . $this->directory[$num];
                if ($this->show_fname) {
                    echo str_replace($this->extract_path . '/', '', $this->filename) . '<br>';
                }

                if (!file_exists($this->filename)) {
                    // suffix check
                    if (!empty($suffix)) {
                        $tmp     = explode('.', $this->filename);
                        $tmp_suf = $tmp[count($tmp) - 1];
                        if (in_array($tmp_suf, $suf)) {
                            $fp = fopen($this->filename, 'x');
                            fwrite($fp, $file['file']);
                            fclose($fp);
                        }
                    } else {
                        $fp = fopen($this->filename, 'x');
                        fwrite($fp, $file['file']);
                        fclose($fp);
                    }
                }
            }

            return true;
        } else {
            $this->error = 'openTAR error. (tarextractor.php line ' . __LINE__ . ')<br>';

            return false;
        }
    }

    /**
     * doExtract.
     *
     * @param        $archive
     * @param        $extract_path
     * @param string $suffix
     * @return bool general function
     * general function
     */
    public function doExtract($archive, $extract_path, $suffix = '')
    {
        if (!$this->setArchive($archive, $extract_path)) {
            return false;
        }

        $suf = array();
        if (!empty($suffix)) {
            $suf = explode('|', $suffix);
        }

        if ($this->openTAR($this->archive)) {
            foreach ($this->files as $file) {
                $this->filename = $this->extract_path . '/' . $file['name'];
                if ($this->show_fname) {
                    echo $file['name'] . '<br>';
                }

                //	make directories
                $this->directory = explode('/', $file['name']);
                $num             = count($this->directory) - 1;

                for ($i = 0; $i < $num; ++$i) {
                    $this->dir_path = $this->extract_path . '/';
                    for ($j = 0; $j < $i; ++$j) {
                        $this->dir_path .= $this->directory[$j] . '/';
                    }
                    $this->dir_path .= $this->directory[$i];

                    if (!is_dir($this->dir_path)) {
                        if (!mkdir($this->dir_path, 0777)) {
                            $this->error = 'mkdir (' . $this->dir_path . ') false. (tarextractor.php line ' . __LINE__ . ')<br>';

                            return false;
                        }
                    }
                }

                // make files
                if (!file_exists($this->filename)) {
                    // suffix check
                    if (!empty($suffix)) {
                        $tmp     = explode('.', $this->filename);
                        $tmp_suf = $tmp[count($tmp) - 1];
                        if (in_array($tmp_suf, $suf)) {
                            $fp = fopen($this->filename, 'x');
                            fwrite($fp, $file['file']);
                            fclose($fp);
                        }
                    } else {
                        $fp = fopen($this->filename, 'x');
                        fwrite($fp, $file['file']);
                        fclose($fp);
                    }
                }
            }

            return true;
        } else {
            $this->error = 'openTAR error. (tarextractor.php line ' . __LINE__ . ')<br>';

            return false;
        }
    }

    /**
     * error.
     */
    public function error()
    {
        return $this->error;
    }
}
