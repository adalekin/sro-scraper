<?php

namespace SroScraper\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;


class SRO extends Eloquent {
  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'sro';

  /**
   * Indicates if the model should be timestamped.
   *
   * @var bool
   */
  public $timestamps = true;
};
