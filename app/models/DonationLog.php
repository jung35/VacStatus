<?php

class DonationLog extends \Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'donation_log';

  public function isValid(){
    return $this->status == 'Completed';
  }

  public function getOriginalAmount() {
    return $this->original_amount;
  }

  public function getAmount() {
    return $this->amount;
  }
}
