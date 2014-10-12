<?php

class News extends \Eloquent {
  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'news';

  public function getId() {
    return $this->id;
  }

  public function getTitle() {
    return $this->title;
  }

  public function getBody() {
    return $this->body;
  }

  public function getPrev() {
    $prevNews = News::find($this->id - 1);

    if(!isset($prevNews->id)) {
      return '';
    }

    return '<a href="/news/'.$prevNews->id.'">&#8606; '.$prevNews->getTitle().'</a>';
  }

  public function getNext() {
    $nextNews = News::find($this->id + 1);

    if(!isset($nextNews->id)) {
      return '';
    }

    return '<a href="/news/'.$nextNews->id.'">'.$nextNews->getTitle().' &#8608;</a>';
  }
}
