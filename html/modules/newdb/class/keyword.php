<?php

class keyword
{
    public $db;
    public $lid;

    public function __construct()
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->lid = '';
    }

    public function setLabel($lid)
    {
        $sql = 'SELECT label_id FROM '.$this->db->prefix('newdb_master')." WHERE label_id='".$lid."'";
        $rs = $this->db->query($sql);
        if ($this->db->getRowsNum($rs) > 0) {
            $row = $this->db->fetchArray($rs);
            $this->lid = $row['label_id'];
        }
    }

    public function getCateTB()
    {
        $cate = array();
        $cate = $this->__getCatelist();

        $return_value = "<table class='list_table' style='width:150px;'><tr>";
        $return_value .= '<th>'._ND_CLASS_CATEGORY.'</th></tr><tr>';

        for ($i = 0, $iMax = count($cate); $i < $iMax; ++$i) {
            $return_value .= '<tr><td>';
            $return_value .= "<a href=\"javascript:seltab('kwbox', 'head', 10, ".($i + 1).')">'.$cate[$i]['kw'].'</a>';
            $return_value .= '</td></tr>';
        }
        $return_value .= '</table>';

        return $return_value;
    }

    public function getKeyTB()
    {
        $cate = array();
        $cate = $this->__getCatelist();
        $return_value = '';

        if ($this->lid > 0) {
            $sql = 'SELECT keyword FROM '.$this->db->prefix('newdb_master')." WHERE label_id='".$this->lid."'";
            $rs = $this->db->query($sql);
            $row = $this->db->fetchArray($rs);
            $kw = $row['keyword'];
        }

        for ($i = 0, $iMax = count($cate); $i < $iMax; ++$i) {
            (!$i) ? $div = '' : $div = 'display:none';
            $return_value .= "<div id='kwbox".($i + 1)."' style='margin-bottom:1em; ".$div."'>";
            $return_value .= "<table class='list_table' style='width:350px; text-align:left;'>";
            $return_value .= '<tr><th>'.$cate[$i]['kw'].'</th></tr>';
            if ($this->lid > 0) {
                $return_value .= $this->__makeTree('/'.$cate[$i]['id'].'/', $cate[$i]['kw'], $kw);
            } else {
                $return_value .= $this->__makeTree('/'.$cate[$i]['id'].'/', $cate[$i]['kw']);
            }
            $return_value .= '</table></div>';
        }

        return $return_value;
    }

    public function __getCatelist()
    {
        $cate = array();
        $sql = 'SELECT * FROM '.$this->db->prefix('newdb_keyword')." WHERE path='/' ORDER BY sort";
        $rs = $this->db->query($sql);
        if ($this->db->getRowsNum($rs) > 0) {
            $i = 0;
            while ($row = $this->db->fetchArray($rs)) {
                $cate[$i]['kw'] = $row['keyword'];
                $cate[$i]['id'] = $row['kw_id'];
                ++$i;
            }
        }

        return $cate;
    }

    public function __makeTree($path, $category, $kw = '')
    {
        $return_value = '';

        $sql = 'SELECT * FROM '.$this->db->prefix('newdb_keyword')." WHERE path ='".$path."' ORDER BY sort";
        $rs = $this->db->query($sql);
        if ($this->db->getRowsNum($rs) > 0) {
            while ($row = $this->db->fetchArray($rs)) {
                $kw_id = $row['kw_id'];
                $path = explode('/', $row['path']);
                $n = count($path) - 2;
                $path4show = '';

                for ($i = 0, $iMax = count($path); $i < $iMax; ++$i) {
                    if ($path[$i]) {
                        $sql = 'SELECT * FROM '.$this->db->prefix('newdb_keyword')." WHERE kw_id='".$path[$i]."'";
                        $rs2 = $this->db->query($sql);
                        $row2 = $this->db->fetchArray($rs2);
                        $path4show .= $row2['keyword'].'/';
                    }
                }
                $path4show = str_replace($category.'/', '', $path4show.$row['keyword']);
                $path = $row['path'].$kw_id.'/';

                if ($n == 1) {
                    $return_value .= "<tr><td class='even'>";
                } else {
                    $return_value .= '<tr><td>';
                }

                for ($i = 0; $i < $n; ++$i) {
                    $return_value .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                }

                $check = '';
                if (!empty($kw)) {
                    if (strstr($kw, '['.$kw_id.']')) {
                        $check = ' checked';
                    }
                }

                $onClick_check = ' onClick="javascript:notCheck('.$kw_id.')" ';
                $onClick_str = ' onClick="javascript:notStr('.$kw_id.')" ';

                $return_value .= "<input type='checkbox' name='kw[]' id='c".$kw_id."' ".$onClick_check." value='".$kw_id."'".$check.'>';
                $return_value .= "<span id='".$kw_id."'".$onClick_str.'>'.$row['keyword'].'</span>';
                $return_value .= '</td></tr>';

                $sql = 'SELECT * FROM '.$this->db->prefix('newdb_keyword')." WHERE path like'".$path."%' ORDER BY sort";
                $rs2 = $this->db->query($sql);
                if ($this->db->getRowsNum($rs2) > 0) {
                    $return_value .= $this->__makeTree($path, $category, $kw);
                }
            }
        }

        return $return_value;
    }
}
