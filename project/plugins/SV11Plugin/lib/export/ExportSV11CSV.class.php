<?php

class ExportSV11CSV implements InterfaceDeclarationExportCsv 
{
    protected $doc = null;
    protected $header = false;

    public static function getHeaderCsv() 
    {
        return DouaneCsvFile::CSV_ENTETES;
    }

    public function __construct($doc, $header = true) 
    {
        $this->doc = $doc;
        $this->header = $header;
    }

    public function getFileName() 
    {
        return $this->doc->_id . '_' . $this->doc->_rev . '.csv';
    }

    public function export() {
        $csv = "";
        if($this->header) {
            $csv .= self::getHeaderCsv();
        }
        if ($file = $this->doc->getFichier('csv')) {
        	$c = new SV11DouaneCsvFile($file, $this->doc);
        	$csv .= $c->convert();
        }
        return $csv;
    }

    protected function formatFloat($value) {

        return str_replace(".", ",", $value);
    }
}
