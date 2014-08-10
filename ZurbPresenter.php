<?php

class ZurbPresenter extends Illuminate\Pagination\Presenter {

    public function getActivePageWrapper($text)
    {
        return '<li class="current"><a href="">'.$text.'</a></li>';
    }

    public function getDisabledTextWrapper($text)
    {
        return '<li class="unavailable">'.$text.'</li>';
    }

    public function getPageLinkWrapper($url, $page)
    {
        return '<li><a href="'.$url.'">'.$page.'</a></li>';
    }

}
