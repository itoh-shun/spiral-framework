<?php

namespace framework\Library\BladeLikeEngine;

use framework\Library\BladeLikeEngine\BladeOneCustom;
use framework\Http\View;

class BladeLikeView extends View
{
    public function render($isFullPath = false): string
    {
        $file = $isFullPath ? VIEW_FILE_ROOT . $this->file : $this->file;
        try {
            $parser = static::parser();
            $result = $parser->run($file, $this->data);
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
        return $result;
    }

    public static function parser()
    {
        $path = ( VIEW_FILE_ROOT == '' ) ? 'framework/resources' : VIEW_FILE_ROOT ;
        return new BladeOneCustom(
            $path,
            null,
            BladeOneCustom::MODE_DEBUG
        );
    }
}
