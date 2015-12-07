<?php

class Bill_ExportFile
{
    //recommend
    public static function exportCsv($filename, $csv_content)
    {
        ob_clean();
        self::_setHeaderInfo();
        header("Content-Type: text/csv; charset=utf-8;");
        header("Content-Disposition: attachment; filename=" . $filename);

        echo self::_getBomHeader() . $csv_content;
        exit;
    }

    //char only with alphabetic will save success. chinese or other failed.
    public static function exportExcel($filename, $xls_content)
    {
        ob_clean();
        self::_setHeaderInfo();
        header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition: attachment; filename=" . $filename);

        //add (chr(0xEF) . chr(0xBB) . chr(0xBF)) will failed, each row data in one cell.
        echo $xls_content;
        exit;
    }

    //work with chinese character, but \t failed.
    public static function saveExcel($filename, $xls_content)
    {
        $fp = fopen($filename, 'w+');
        fwrite($fp, self::_getBomHeader());
        fwrite($fp, $xls_content);
        fclose($fp);
    }

    private static function _exprotPdf($filename, $pdf_content)
    {
        ob_clean();
        self::_setHeaderInfo();
        header("Content-Type: application/pdf;");
        header("Content-Disposition: attachment; filename=" . $filename);
    }

    private static function _setHeaderInfo()
    {
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/download");
        header("Content-Transfer-Encoding: binary");
        header("Pragma: no-cache");
        header("Expires: 0");
    }

    private static function _getBomHeader()
    {
        return (chr(0xEF) . chr(0xBB) . chr(0xBF));
    }
}