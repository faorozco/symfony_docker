<?php

namespace App\Utils;

use Port\Writer\AbstractStreamWriter;

class MyStreamWriter extends AbstractStreamWriter
{
    public function writeItem(array $row)
    {
        foreach ($row as $key => $value) {
            $row[$key] = self::ValidarTipoDato($value);
        }
        if (null !== $row) {
            fputcsv($this->getStream(), $row, ",");
        }
    }

    public function ValidarTipoDato($value)
    {
        if ($value instanceof \DateTime) {
            if (null !== $value) {
                return $value->format("Y-m-d H:i:s");
            } else {
                return "";
            }
        } else {

            return $value;
        }
    }
}
