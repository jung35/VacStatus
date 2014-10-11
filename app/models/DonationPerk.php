<?php

class DonationPerk extends \Eloquent {
  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'donation_perk';

  public function getDesc() {
    return $this->desc;
  }

  public function getAmount() {
    return number_format($this->amount, 2, '.', '');
  }

  static public function getPerkAmount($perk) {
    $perk = self::wherePerk($perk)->first();

    return $perk->amount;
  }
}
