<?php
class PbSpiralApiCommunicator{

    public function __construct(){
    }

    public function request(string $_function, string $_method, \SpiralApiRequest $_reqCtx){
        return $_reqCtx;
    }
    
}