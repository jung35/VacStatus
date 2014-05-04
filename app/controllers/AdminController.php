<?php
class AdminController extends BaseController {

  public function getIndex()
  {
    if(Session::get('user.admin') <= 0) return View::make('noAdmin');
    // var_dump(Session::get('user.admin'));
  }

}
