<?php

namespace App\Libraries\GroupEmail;

/**
 * A group email action that can't go ahead. The message is safe to show to
 * the admin as it stands.
 */
class GroupEmailException extends \RuntimeException
{
}
