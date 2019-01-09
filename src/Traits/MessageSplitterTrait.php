<?php

namespace VkBirthdayReminder\Traits;

trait MessageSplitterTrait
{
    /**
     * Splits messages into array of batches of $batchLength.
     *
     * @param string $msg
     * @param int $batchLength
     *
     * @return array
     */
    public function splitMessage(string $msg, int $batchLength): array
    {
        if ($batchLength > 0) {
            $ret = array();
            $msgLength = mb_strlen($msg, "UTF-8");

            for ($i = 0; $i < $msgLength; $i += $batchLength) {
                $ret[] = mb_substr($msg, $i, $batchLength, "UTF-8");
            }

            return $ret;
        }

        return preg_split("//u", $msg, -1, PREG_SPLIT_NO_EMPTY);
    }
}