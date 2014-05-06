<?php
class AdminController extends BaseController {

  public function __construct() {
    parent::__construct();
    if(Session::get('user.admin') <= 0) return View::make('noAdmin');
  }

  public function getIndex()
  {
    return View::make('admin.index');
  }

}
