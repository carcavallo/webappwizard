<?php

namespace PR24\Controller;

use PR24\Model\TherapyOptionsModel;

class TherapyOptionsController {
    private $therapyOptionsModel;

    public function __construct(TherapyOptionsModel $therapyOptionsModel) {
        $this->therapyOptionsModel = $therapyOptionsModel;
    }

    public function getLokaleTherapyOptions() {
        return $this->therapyOptionsModel->getLokaleTherapyOptions();
    }

    public function getSystemtherapieOptions() {
        return $this->therapyOptionsModel->getSystemtherapieOptions();
    }
}
