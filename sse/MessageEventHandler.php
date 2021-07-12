<?php

namespace app\sse;

use odannyc\Yii2SSE\SSEBase;

class MessageEventHandler extends SSEBase
{

  /**
   * Check for continue to send event.
   *
   * @return bool
   */
  public function check()
  {
    return time()%2==1; // condition
  }

  /**
   * Get Updated Data.
   *
   * @return string
   */
  public function update()
  {
    $now = date('Y-M-m h:i:s');
    return "Now it's: $now";
  }
}
