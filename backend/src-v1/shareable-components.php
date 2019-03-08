<?php

class ShareableComponents
{
    public function getMessage() {
        // Return all messages and strip <br /> at the end of the line
        return rtrim($this->message, '<br />');
    }

    public function getFields() {
        // Return unique fields
        if (!$this->fields) {
            return false;
        }

        return array_unique($this->fields);
    }
}