<?php
// ビューの生成
namespace Command\Basis\Core;

use framework\Http\Request;
use stdClass;
class View {

    protected $file = null;
    public $data = [];

    public function __construct(string $file = null , array $data = array() , bool $filter = true )
    {
        $this->file = $file;
        foreach($data as $key => $d){
            if($d instanceof View)
            {
                $d = $d->render();
            }
            if($filter){
                $d = $this->filter($d);
            }
            $this->data[$key] = $d;
        }
    }

    public static function forge(string $file = null , array $data = array() , bool $filter = true ): View
    {
        return new View($file,$data,$filter);
    }

    public function set_filename(string $file = null): void
    {
        $this->file = $file;
    }

    public function get(string $key = null , string $default = null): mixed
    {
        if($key == null){
            return $this->data;
        }

        return ($this->data->$key)? $this->data[$key] : $default;
    }

    public function set(string $key , string $value = null , bool $filter = true): void
    {
        if($filter){
            $value = $this->filter($value);
        }
        $this->data[$key] = $value;
    }

    public function add_values(array $data , bool $filter = true): void
    {
        foreach($data as $key => $d){
            if($d instanceof View)
            {
                $d = $d->render();
            }
            if($filter){
                $d = $this->filter($d);
            }
            $this->data[$key] = $d;
        }
    }

    public function filter($value)
    {
        if ( ! is_object($value) && ! is_array($value)  ) return htmlspecialchars($value, ENT_QUOTES, "UTF-8");//PHPサーバーはUTF-8
        
        if( $value instanceof Request){
            return $value;
        }

        if( is_object($value)){
            unset($value->spiralDataBase);
            unset($value->spiralSendMail);
            unset($value->spiralDBFilter);
            $tmp = new stdClass;
            foreach((array)$value as $k => $t)
            {
                $t = $this->filter($t);
                $tmp->{$k} = $t;
            }
            
            return $tmp;
        }

        
        if( is_array($value)){
            $tmp = [];
            foreach($value as $k => $t)
            {
                $t = $this->filter($t);
                $tmp[$k] = $t;
            }
            return $tmp;
        }

        return $value;
    }

    public function __toString()
    {
        return $this->render();
    }

    public function render( string $file = null ): string
    {
        if($file != null){
            $this->file = $file ;
        }
        if(is_array($this->data)){
            extract($this->data, EXTR_PREFIX_SAME, "t_");
        }
        ob_start(); //バッファ制御スタート
        require( $this->file.'.php');
        $html = ob_get_clean(); //バッファ制御終了＆変数を取得

        return $html;
    }
    
}