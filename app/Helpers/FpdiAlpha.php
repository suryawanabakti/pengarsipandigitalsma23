<?php

namespace App\Helpers;

use setasign\Fpdi\Fpdi;

/**
 * Custom FPDI class to support alpha transparency (opacity).
 */
class FpdiAlpha extends Fpdi
{
    protected $extgstates = [];

    /**
     * Set alpha for next drawing operations.
     * @param float $alpha 0 to 1
     * @param string $bm Blend mode
     */
    public function SetAlpha($alpha, $bm = 'Normal')
    {
        $gs = $this->AddExtGState(['ca' => $alpha, 'CA' => $alpha, 'BM' => '/' . $bm]);
        $this->SetExtGState($gs);
    }

    protected function AddExtGState($parms)
    {
        $n = count($this->extgstates) + 1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    protected function SetExtGState($gs)
    {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    protected function _putextgstates()
    {
        for ($i = 1; $i <= count($this->extgstates); $i++) {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_put('<</Type /ExtGState');
            foreach ($this->extgstates[$i]['parms'] as $k => $v) {
                $this->_put("/$k $v");
            }
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    protected function _putresourcedict()
    {
        parent::_putresourcedict();
        $this->_put('/ExtGState <<');
        foreach ($this->extgstates as $k => $v) {
            $this->_put('/GS' . $k . ' ' . $v['n'] . ' 0 R');
        }
        $this->_put('>>');
    }

    protected function _putresources()
    {
        $this->_putextgstates();
        parent::_putresources();
    }
}
