<?php

namespace PR24\Controller;

use PR24\Model\TherapyOptionsModel;

/**
 * TherapyOptionsController manages the therapy-related actions.
 */
class TherapyOptionsController {
    private $therapyOptionsModel;

    /**
     * Constructor to initialize the TherapyOptionsModel.
     *
     * @param TherapyOptionsModel $therapyOptionsModel The model handling therapy data.
     */
    public function __construct(TherapyOptionsModel $therapyOptionsModel) {
        $this->therapyOptionsModel = $therapyOptionsModel;
    }

    /**
     * Retrieves local therapy options.
     *
     * @return array An array of local therapy options.
     */
    public function getLokaleTherapyOptions() {
        return $this->therapyOptionsModel->getLokaleTherapyOptions();
    }

    /**
     * Retrieves systemic therapy options.
     *
     * @return array An array of systemic therapy options.
     */
    public function getSystemtherapieOptions() {
        return $this->therapyOptionsModel->getSystemtherapieOptions();
    }
}
