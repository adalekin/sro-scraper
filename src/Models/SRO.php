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

  /**
   * Sets a specified attribute if it wasn't changed by user.
   */
  public function setAttribute($property, $value) {
    $editedFields = json_decode($this->edited_fields);

    if (!$editedFields || !array_key_exists($property, $editedFields)) {
      $this->attributes[$property] = $value;
    }
  }
};
