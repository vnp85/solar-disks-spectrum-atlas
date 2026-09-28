<?php

class HyperCube {                

    public function getHeaderSize(){
        $expected_file_count     = 2000;        
        $expected_bytes_per_file = 1000;
        return $expected_file_count*$expected_bytes_per_file;
    }

    public function __construct(){
    }

    public function isCandidate($filename){
        $filename = basename($filename);
        if (strpos($filename, '_img_C')!==false){
            return true;
        }
        return false;
    }

    private $to_pack = array();

    public function addToPackIfCandidateFromGlob($gm){
        $list = glob($gm);
        return $this->addToPackIfCandidate($list);
    }

    public function addToPackIfCandidate($f){
        if (is_array($f)){
            foreach ($f as $fi){
                $this->addToPackIfCandidate($fi);
            }            
        }else{
            if ($this->isCandidate($f)){
                $this->to_pack[] = array("filename" => $f, "filesize" => filesize($f));
            }
        }        
        return $this;
    }


    private $outFilename = '';
    public function createPack($output_filename){
        $this->outFilename = $output_filename;
        return $this;
    }

    public function getWrittenFiles(){
        return $this->to_pack;
    }

    public function writePack(){
        if (!$this->outFilename){
            return $this;
        }
        $blob = '';
        $header_size = $this->getHeaderSize();
        $blob_offset = $header_size;
        $j = array();
        foreach ($this->to_pack as $item){
            $j[] = array(
                "basename" => basename($item["filename"]),
                "blob-offset" => $blob_offset,
                "blob-size" => $item["filesize"],
            );
            $blob_offset += $item["filesize"];
        }
        $blob .= json_encode($j)."\r\n";

        $delta = $header_size - strlen($blob);
        while ($delta > 0){
            if ($delta > 2){                
                $blob .= "\r\n";
            };
            $blob .= ' ';
            $delta = $header_size - strlen($blob);
        }
     
        file_put_contents($this->outFilename, $blob);

        foreach ($this->to_pack as $item){
            file_put_contents($this->outFilename, file_get_contents($item["filename"]), FILE_APPEND | LOCK_EX);
        };    
        return $this;
    }



    private $pack = array();
    private $packname = '';
    function openPack($filename){
        $header_size = $this->getHeaderSize();
        $this->packname = $filename;
        $h = trim(file_get_contents($this->packname, false, null, 0, $header_size));
        $this->pack = json_decode($h, true);
        return $this;        
    }
    function listContents(){
        return $this->pack;
    }
    function getBlob($filename){
        if (is_array($filename)){
            if (isset($filename["filename"])){
                $filename = $filename["filename"];
            }
        }
        $b = basename($filename);
        foreach ($this->pack as $i){
            if ($i['basename'] == $b){
                return file_get_contents($this->packname, false, null, $i['blob-offset'], $i['blob-size']);
            }
        }
        return '';
    }

    function selfTestOnFile($file_list_item){
        $blob_in_pack = $this->getBlob($file_list_item);
        $blob_on_filesystem = file_get_contents($file_list_item["filename"]);
        return sha1($blob_in_pack) == sha1($blob_on_filesystem);
    }

    function packCubeAt($folder){        
        $has_candidates = false;
        $extensions = array('*.jpg', '*.png');
        foreach ($extensions as $ext){
            foreach (glob($folder.'/'.$ext) as $filename){
                if ($this->isCandidate($filename)){
                    $has_candidates = true;
                }
            }
        }
        if ($has_candidates){
            $this->createPack($folder.'/hyper.cube');
            foreach ($extensions as $ext){
                $this->addToPackIfCandidateFromGlob($folder.'/'.$ext);
            };    
            $this->writePack();
        }
        return $this;
    }

    function unpackCubeAt($folder){
        $cubeName = $folder.'/hyper.cube';
        if (file_exists($cubeName)){
            $this->openPack($cubeName);
            foreach ($this->pack as $i){
                $out_filename = dirname($cubeName).'/'.$i['basename'];
                if (!file_exists($out_filename)){
                    file_put_contents($out_filename, $this->getBlob($i['basename']));
                }
            }
        }
        return $this;
    }
}
