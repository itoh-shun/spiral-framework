<?php

namespace framework\SpiralConnecter;

use Collection\Collection;
use framework\Http\Request;
use stdClass;

class Paginator extends stdClass
{
    private Collection $data ;
    private int $currentPage = 1;
    private int $from = 1;
    private int $lastPage = 0;
    private int $limit = 0;
    private int $total = 0;
    private OrderBy $orderBy;

    public static array $sortSymbol = ['asc' => '▲', 'desc' => '▼'];

    public function __construct(
        Collection $data,
        int $currentPage,
        int $from,
        int $lastPage,
        int $limit,
        int $total,
        OrderBy $orderBy = null
    ) {
        $this->data = $data;
        foreach ($data as $key => $val) {
            $this->{$key} = $val;
        }
        $this->currentPage = $currentPage;
        $this->from = $from;
        $this->lastPage = $lastPage;
        $this->limit = $limit;
        $this->total = $total;
        $this->orderBy = $orderBy;
    }

    public function getData()
    {
        return $this->data->all();
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getLastPage()
    {
        return $this->lastPage;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function sortSymbol($key)
    {
        if ($this->orderBy->field === $key) {
            return self::$sortSymbol[$this->orderBy->ascOrDesc];
        }
        return '';
    }

    public function sortLink($key)
    {
        $ascOrDesc = 'asc';
        if (
            $this->orderBy->field === $key &&
            $this->orderBy->ascOrDesc === 'asc'
        ) {
            $ascOrDesc = 'desc';
        }
        return '?' .
            Request::queryBuilder(['sortkey' => $key, 'sort' => $ascOrDesc]);
    }

    public function limits($limit = [10, 50, 100, 200, 500, 1000])
    {
        $html = '<div class="limit-wrapper">';
        $html .= '<select id="limit">';
        foreach ($limit as $l) {
            $selected = $this->getLimit() === $l ? 'selected' : '';
            $html .=
                '<option value="' .
                $l .
                '" ' .
                $selected .
                '>' .
                $l .
                '件</option>' .
                PHP_EOL;
        }
        $html .= '</select>' . PHP_EOL;
        $html .=
            '<button onClick="location.href=\'?limit=\'+document.querySelector(\'select#limit\').value + \'&' .
            Request::queryBuilder([], ['limit', 'page']) .
            '\'">表示</button>' .
            PHP_EOL;
        $html .= '</div>' . PHP_EOL;
        return $html;
    }

    public function links()
    {
        $html =
            '<nav aria-label="Page navigation" class="pagination-nav">' .
            PHP_EOL;
        $html .= '<ul class="pagination">' . PHP_EOL;
        foreach ($this->rangeWithDots() as $text) {
            if (is_int($text)) {
                $html .=
                    '<li class="page-item ' .
                    ($text === $this->currentPage ? 'current' : '') .
                    '"><a href="?' .
                    Request::queryBuilder(['page' => $text]) .
                    '" class="page-link"><span>' .
                    $text .
                    '</span></a></li>' .
                    PHP_EOL;
            } else {
                $html .=
                    '<li class="page-item"><span class="page-text">' .
                    $text .
                    '</span></li>' .
                    PHP_EOL;
            }
        }
        $html .= '</ul>' . PHP_EOL;
        $html .= '</nav>' . PHP_EOL;
        return $html;
    }

    private function rangeWithDots()
    {
        $current = $this->currentPage;
        $last = $this->lastPage;
        $delta = 2;
        $left = $current - $delta;
        $right = $current + $delta + 1;
        $range = [];
        $rangeWithDots = [];
        $l = 0;

        for ($i = 1; $i <= $last; $i++) {
            if ($i == 1 || $i == $last || ($i >= $left && $i < $right)) {
                $range[] = $i;
            }
        }

        foreach ($range as $i) {
            if ($l) {
                if ($i - $l === 2) {
                    $rangeWithDots[] = $l + 1;
                } elseif ($i - $l !== 1) {
                    $rangeWithDots[] = '...';
                }
            }
            $rangeWithDots[] = $i;
            $l = $i;
        }

        return $rangeWithDots;
    }
}
