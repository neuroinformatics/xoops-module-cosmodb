<?php

class RegCopy
{
    /**
     * public.
     */
    // for debug
    public $show_fname;

    /**
     * private.
     */
    public $from_path;
    public $from_base_path;
    public $extract_path;
    public $dir_path;
    public $flist;
    public $fname;
    public $directory;
    public $error;

    /**
     * Class Constructor.
     */
    public function __construct()
    {
        $this->show_fname     = 0;
        $this->from_path      = '';
        $this->from_base_path = '';
        $this->extract_path   = '';
        $this->dir_path       = '';
        $this->flist          = array();
        $this->fname          = '';
        $this->directory      = array();
        $this->error          = '';
    }

    /**
     * setPath.
     *
     * @param string $from_path
     * @param string $extract_path
     *
     * @return bool
     */
    public function setPath($from_path, $extract_path)
    {
        $this->from_path    = $from_path;
        $this->extract_path = $extract_path;

        if (substr($from_path, -1) === '/') {
            $this->from_path = substr($from_path, 0, -1);
        }
        if (substr($extract_path, -1) === '/') {
            $this->base_path = substr($extract_path, 0, -1);
        }

        if (!is_dir($this->from_path)) {
            $this->error = $this->from_path . ' does not exist. (regcopy.php line ' . __LINE__ . ')<br>';

            return false;
        }

        $dammy = explode('/', $this->from_path);
        for ($i = 0; $i < count($dammy) - 1; ++$i) {
            $this->from_base_path .= $dammy[$i] . '/';
        }
        $this->from_base_path = substr($this->from_base_path, 0, -1);

        return true;
    }

    /**
     * doRegCopy.
     *
     * @param $suffix
     * @param $label_id
     * @return bool
     */
    public function doRegCopy($suffix, $label_id)
    {
        $this->__getFlist($this->from_path);
        $suf = array();
        if (!empty($suffix)) {
            $suf = explode('|', $suffix);
        }

        foreach ($this->flist as $file) {

            //	make directories
            $this->directory = array();
            $dammy           = explode('/', str_replace($this->from_base_path . '/', '', $file));
            $dammy[0]        = $label_id;

            // extract/dataname/thumbnail/...
            if (isset($dammy[1]) && $dammy[1] === 'thumbnail') {
                $this->directory    = explode('/', str_replace($this->from_base_path . '/', '', $file));
                $this->directory[0] = $dammy[0];

                // extract/dataname/caption/...
            } elseif (isset($dammy[1]) && $dammy[1] === 'caption') {
                $this->directory    = explode('/', str_replace($this->from_base_path . '/', '', $file));
                $this->directory[0] = $dammy[0];

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
                        $this->error = 'mkdir (' . $this->dir_path . ') false. (regcopy.php line ' . __LINE__ . ')<br>';

                        return false;
                    }
                }
            }

            // copy files;
            $this->fname = $this->dir_path . '/' . $this->directory[$num];
            if ($this->show_fname) {
                echo str_replace($this->extract_path . '/', '', $this->fname) . '<br>';
            }

            // suffix check
            if (!empty($suffix)) {
                $tmp     = explode('.', $file);
                $tmp_suf = $tmp[count($tmp) - 1];
                if (in_array($tmp_suf, $suf)) {
                    copy($file, $this->fname);
                }
            } else {
                copy($file, $this->fname);
            }
        }

        return true;
    }

    /**
     * __getFlist.
     *
     * @param string $dir
     */
    public function __getFlist($dir)
    {
        if ($handle = opendir($dir)) {
            while (false !== $file = readdir($handle)) {
                if ($file !== '.' && $file !== '..') {
                    if (is_dir($dir . '/' . $file)) {
                        $this->__getFlist($dir . '/' . $file);
                    } else {
                        $this->flist[] = $dir . '/' . $file;
                    }
                }
            }
            closedir($handle);
        }

        return;
    }

    public function doCopy($from_path, $extract_path, $suffix = '')
    {
        if (substr($from_path, -1) === '/') {
            $from_path = substr($from_path, 0, -1);
        }
        if (substr($extract_path, -1) === '/') {
            $extract_path = substr($extract_path, 0, -1);
        }

        if (!is_dir($from_path)) {
            $this->error = $from_path . ' does not exist. (regcopy.php line ' . __LINE__ . ')<br>';

            return false;
        }
        if (!is_dir($extract_path) && !mkdir($extract_path, 0777)) {
            $this->error = 'mkdir (' . $extract_path . ') false. (regcopy.php line ' . __LINE__ . ')<br>';

            return false;
        }

        $this->__doCopy($from_path, $extract_path, $suffix);

        return true;
    }

    public function __doCopy($from_path, $extract_path, $suffix)
    {
        $suf = array();
        if (!empty($suffix)) {
            $suf = explode('|', $suffix);
        }

        if ($handle = opendir($from_path)) {
            while (false !== $file = readdir($handle)) {
                if ($file !== '.' && $file !== '..') {
                    $fname   = $from_path . '/' . $file;
                    $tofname = $extract_path . '/' . $file;

                    if (is_dir($fname)) {
                        if (!is_dir($tofname) && mkdir($tofname, 0777)) {
                            $this->__doCopy($fname, $tofname, $suffix);
                        } elseif (is_dir($tofname)) {
                            $this->__doCopy($fname, $tofname, $suffix);
                        }
                    } elseif (file_exists($fname) && !file_exists($tofname)) {

                        // suffix check
                        if (!empty($suffix)) {
                            $tmp     = explode('.', $fname);
                            $tmp_suf = $tmp[count($tmp) - 1];
                            if (in_array($tmp_suf, $suf)) {
                                copy($fname, $tofname);
                            }
                        } else {
                            copy($fname, $tofname);
                        }
                    }
                }
            }
            closedir($handle);
        }

        return;
    }

    /**
     * delDirectory.
     *
     * @param string $dir
     */
    public function delDirectory($dir)
    {
        $this->__doDelDir($dir, $dir);
    }

    /**
     * __doDelDir.
     *
     * @param string $dir
     * @param        $dir_pass
     */
    public function __doDelDir($dir, $dir_pass)
    {
        if ($handle = opendir($dir)) {
            while (false !== $file = readdir($handle)) {
                if ($file !== '.' && $file !== '..') {
                    $this->fname = $dir . '/' . $file;

                    if (is_dir($this->fname)) {
                        $this->__doDelDir($this->fname, $this->fname);
                    } else {
                        unlink($this->fname);
                    }
                }
            }
            closedir($handle);
        }
        rmdir($dir_pass);

        return;
    }

    /**
     * error.
     */
    public function error()
    {
        return $this->error;
    }
}
