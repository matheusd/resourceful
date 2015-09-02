<?php

namespace Resourceful;

trait DecodesAcceptArray {

    protected function decodeAcceptArray($acceptString)
    {
        $accept = $acceptArray = array();
        foreach (explode(',', strtolower($acceptString)) as $part) {
            $parts = preg_split('/\s*;\s*q=/', $part);
            if (isset($parts) && isset($parts[1]) && $parts[1]) {
                $num = $parts[1] * 10;
            } else {
                $num = 10;
            }
            if ($parts[0]) {
                $accept[$num][] = $parts[0];
            }
        }
        krsort($accept);
        foreach ($accept as $parts) {
            foreach ($parts as $part) {
                $acceptArray[] = trim($part);
            }
        }        

        return $acceptArray;
    }        

}